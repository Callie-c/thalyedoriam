<?php
$db = getDb();
$action = $action ?? null;
$id = $id ?? null;

// ---- Suppression ----
if ($action === 'delete' && $id) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf()) {
        $stmt = $db->prepare('SELECT image FROM expositions WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        if ($row && $row['image']) deleteImage($row['image']);
        $db->prepare('DELETE FROM expositions WHERE id = :id')->execute(['id' => $id]);
        flashMessage('success', 'Exposition supprimée.');
    }
    header('Location: ' . SITE_URL . '/artistspace/expositions');
    exit;
}

// ---- Édition / Création ----
if ($action === 'edit' || $action === 'new') {
    $expo = null;
    if ($action === 'edit' && $id) {
        $stmt = $db->prepare('SELECT * FROM expositions WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $expo = $stmt->fetch();
        if (!$expo) {
            flashMessage('error', 'Exposition introuvable.');
            header('Location: ' . SITE_URL . '/artistspace/expositions');
            exit;
        }
    }

    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verifyCsrf()) {
            $errors[] = 'Erreur de sécurité.';
        } else {
            $titre = trim($_POST['titre'] ?? '');
            $lieu = trim($_POST['lieu'] ?? '');
            $date_debut = trim($_POST['date_debut'] ?? '');
            $date_fin = trim($_POST['date_fin'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $visible = isset($_POST['visible']) ? 1 : 0;

            if (!$titre) $errors[] = 'Le titre est obligatoire.';

            $imagePath = $expo['image'] ?? '';
            if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                try {
                    if ($imagePath) deleteImage($imagePath);
                    $imagePath = uploadImage($_FILES['image'], 'expos');
                } catch (RuntimeException $e) {
                    $errors[] = $e->getMessage();
                }
            }

            if (empty($errors)) {
                if ($expo) {
                    $stmt = $db->prepare('UPDATE expositions SET titre=:titre, lieu=:lieu, date_debut=:date_debut, date_fin=:date_fin, description=:description, image=:image, visible=:visible WHERE id=:id');
                    $stmt->execute([
                        'titre' => $titre, 'lieu' => $lieu, 'date_debut' => $date_debut ?: null,
                        'date_fin' => $date_fin ?: null, 'description' => $description,
                        'image' => $imagePath, 'visible' => $visible, 'id' => $id
                    ]);
                    flashMessage('success', 'Exposition modifiée.');
                } else {
                    $stmt = $db->prepare('INSERT INTO expositions (titre, lieu, date_debut, date_fin, description, image, visible, created_at) VALUES (:titre, :lieu, :date_debut, :date_fin, :description, :image, :visible, :created_at)');
                    $stmt->execute([
                        'titre' => $titre, 'lieu' => $lieu, 'date_debut' => $date_debut ?: null,
                        'date_fin' => $date_fin ?: null, 'description' => $description,
                        'image' => $imagePath, 'visible' => $visible, 'created_at' => date('Y-m-d H:i:s')
                    ]);
                    flashMessage('success', 'Exposition ajoutée.');
                }
                header('Location: ' . SITE_URL . '/artistspace/expositions');
                exit;
            }
        }
    }

    ob_start();
    ?>
    <h1 class="admin-title"><?= $expo ? 'Modifier l\'exposition' : 'Nouvelle exposition' ?></h1>

    <?php if (!empty($errors)): ?>
        <div class="flash flash-error"><?= implode('<br>', array_map('e', $errors)) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="admin-form">
        <?= csrfField() ?>
        <div class="form-group">
            <label for="titre">Titre *</label>
            <input type="text" id="titre" name="titre" value="<?= e($expo['titre'] ?? ($titre ?? '')) ?>" required>
        </div>
        <div class="form-group">
            <label for="lieu">Lieu</label>
            <input type="text" id="lieu" name="lieu" value="<?= e($expo['lieu'] ?? ($lieu ?? '')) ?>" placeholder="Ex: Galerie XYZ, Paris">
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="date_debut">Date de début</label>
                <input type="date" id="date_debut" name="date_debut" value="<?= e($expo['date_debut'] ?? ($date_debut ?? '')) ?>">
            </div>
            <div class="form-group">
                <label for="date_fin">Date de fin</label>
                <input type="date" id="date_fin" name="date_fin" value="<?= e($expo['date_fin'] ?? ($date_fin ?? '')) ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4"><?= e($expo['description'] ?? ($description ?? '')) ?></textarea>
        </div>
        <div class="form-group">
            <label for="image">Image / Affiche</label>
            <input type="file" id="image" name="image" accept="image/*">
            <?php if (!empty($expo['image'])): ?>
                <div class="image-preview"><img src="<?= imageUrl($expo['image']) ?>" alt=""></div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label class="toggle-label">
                <span class="toggle-switch">
                    <input type="checkbox" name="visible" <?= ($expo['visible'] ?? 1) ? 'checked' : '' ?>>
                    <span class="toggle-slider"></span>
                </span>
                <span class="toggle-text">Visible sur le site</span>
            </label>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= $expo ? 'Enregistrer' : 'Ajouter' ?></button>
            <a href="/artistspace/expositions" class="btn">Annuler</a>
        </div>
    </form>
    <?php
    $adminContent = ob_get_clean();
    include TEMPLATE_DIR . '/artistspace/layout.php';
    return;
}

// ---- Liste ----
$expos = $db->query('SELECT * FROM expositions ORDER BY date_debut DESC, created_at DESC')->fetchAll();

ob_start();
?>

<div class="admin-actions">
    <h1 class="admin-title" style="margin-bottom:0">Expositions</h1>
    <a href="/artistspace/expositions/new" class="btn btn-primary btn-sm">+ Ajouter</a>
</div>

<?php if (empty($expos)): ?>
    <p>Aucune exposition pour le moment.</p>
<?php else: ?>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Titre</th>
                <th>Lieu</th>
                <th>Dates</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($expos as $expo):
                $status = expoStatus($expo['date_debut'], $expo['date_fin']);
            ?>
                <tr>
                    <td><img src="<?= imageUrl($expo['image']) ?>" alt=""></td>
                    <td><?= e($expo['titre']) ?></td>
                    <td><?= e($expo['lieu']) ?></td>
                    <td><?= formatDate($expo['date_debut']) ?></td>
                    <td><span class="expo-badge <?= $status ?>"><?= expoStatusLabel($status) ?></span></td>
                    <td class="actions">
                        <a href="/artistspace/expositions/<?= $expo['id'] ?>/edit" class="btn btn-sm">Modifier</a>
                        <form method="POST" action="/artistspace/expositions/<?= $expo['id'] ?>/delete" style="display:inline" onsubmit="return confirm('Supprimer cette exposition ?')">
                            <?= csrfField() ?>
                            <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php
$adminContent = ob_get_clean();
include TEMPLATE_DIR . '/artistspace/layout.php';
