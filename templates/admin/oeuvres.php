<?php
$db = getDb();
$action = $action ?? null;
$id = $id ?? null;

// ---- Suppression ----
if ($action === 'delete' && $id) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf()) {
        $stmt = $db->prepare('SELECT image FROM oeuvres WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        if ($row && $row['image']) deleteImage($row['image']);
        $db->prepare('DELETE FROM oeuvres WHERE id = :id')->execute(['id' => $id]);
        flashMessage('success', 'Œuvre supprimée.');
    }
    header('Location: ' . SITE_URL . '/artistspace/oeuvres');
    exit;
}

// ---- Édition / Création ----
if ($action === 'edit' || $action === 'new') {
    $oeuvre = null;
    if ($action === 'edit' && $id) {
        $stmt = $db->prepare('SELECT * FROM oeuvres WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $oeuvre = $stmt->fetch();
        if (!$oeuvre) {
            flashMessage('error', 'Œuvre introuvable.');
            header('Location: ' . SITE_URL . '/artistspace/oeuvres');
            exit;
        }
    }

    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verifyCsrf()) {
            $errors[] = 'Erreur de sécurité.';
        } else {
            $titre = trim($_POST['titre'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $technique = trim($_POST['technique'] ?? '');
            $dimensions = trim($_POST['dimensions'] ?? '');
            $annee = (int)($_POST['annee'] ?? 0);
            $ordre = (int)($_POST['ordre'] ?? 0);
            $visible = isset($_POST['visible']) ? 1 : 0;

            if (!$titre) $errors[] = 'Le titre est obligatoire.';

            // Upload image
            $imagePath = $oeuvre['image'] ?? '';
            if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                try {
                    if ($imagePath) deleteImage($imagePath);
                    $imagePath = uploadImage($_FILES['image'], 'oeuvres');
                } catch (RuntimeException $e) {
                    $errors[] = $e->getMessage();
                }
            }

            if (empty($errors)) {
                if ($oeuvre) {
                    $stmt = $db->prepare('UPDATE oeuvres SET titre=:titre, description=:description, technique=:technique, dimensions=:dimensions, annee=:annee, image=:image, ordre=:ordre, visible=:visible WHERE id=:id');
                    $stmt->execute([
                        'titre' => $titre, 'description' => $description, 'technique' => $technique,
                        'dimensions' => $dimensions, 'annee' => $annee ?: null, 'image' => $imagePath,
                        'ordre' => $ordre, 'visible' => $visible, 'id' => $id
                    ]);
                    flashMessage('success', 'Œuvre modifiée.');
                } else {
                    $stmt = $db->prepare('INSERT INTO oeuvres (titre, description, technique, dimensions, annee, image, ordre, visible, created_at) VALUES (:titre, :description, :technique, :dimensions, :annee, :image, :ordre, :visible, :created_at)');
                    $stmt->execute([
                        'titre' => $titre, 'description' => $description, 'technique' => $technique,
                        'dimensions' => $dimensions, 'annee' => $annee ?: null, 'image' => $imagePath,
                        'ordre' => $ordre, 'visible' => $visible, 'created_at' => date('Y-m-d H:i:s')
                    ]);
                    flashMessage('success', 'Œuvre ajoutée.');
                }
                header('Location: ' . SITE_URL . '/artistspace/oeuvres');
                exit;
            }
        }
    }

    ob_start();
    ?>
    <h1 class="admin-title"><?= $oeuvre ? 'Modifier l\'œuvre' : 'Nouvelle œuvre' ?></h1>

    <?php if (!empty($errors)): ?>
        <div class="flash flash-error"><?= implode('<br>', array_map('e', $errors)) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="admin-form">
        <?= csrfField() ?>
        <div class="form-group">
            <label for="titre">Titre *</label>
            <input type="text" id="titre" name="titre" value="<?= e($oeuvre['titre'] ?? ($titre ?? '')) ?>" required>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="technique">Technique</label>
                <input type="text" id="technique" name="technique" value="<?= e($oeuvre['technique'] ?? ($technique ?? '')) ?>" placeholder="Ex: Technique mixte, résine, verre">
            </div>
            <div class="form-group">
                <label for="dimensions">Dimensions</label>
                <input type="text" id="dimensions" name="dimensions" value="<?= e($oeuvre['dimensions'] ?? ($dimensions ?? '')) ?>" placeholder="Ex: 65 x 56 cm">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="annee">Année</label>
                <input type="number" id="annee" name="annee" value="<?= e($oeuvre['annee'] ?? ($annee ?? '')) ?>" min="1900" max="2100">
            </div>
            <div class="form-group">
                <label for="ordre">Ordre d'affichage</label>
                <input type="number" id="ordre" name="ordre" value="<?= e($oeuvre['ordre'] ?? ($ordre ?? '0')) ?>" min="0">
            </div>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4"><?= e($oeuvre['description'] ?? ($description ?? '')) ?></textarea>
        </div>
        <div class="form-group">
            <label for="image">Image</label>
            <input type="file" id="image" name="image" accept="image/*">
            <?php if (!empty($oeuvre['image'])): ?>
                <div class="image-preview"><img src="<?= imageUrl($oeuvre['image']) ?>" alt=""></div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label class="toggle-label">
                <span class="toggle-switch">
                    <input type="checkbox" name="visible" <?= ($oeuvre['visible'] ?? 1) ? 'checked' : '' ?>>
                    <span class="toggle-slider"></span>
                </span>
                <span class="toggle-text">Visible sur le site</span>
            </label>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= $oeuvre ? 'Enregistrer' : 'Ajouter' ?></button>
            <a href="/artistspace/oeuvres" class="btn">Annuler</a>
        </div>
    </form>
    <?php
    $adminContent = ob_get_clean();
    include TEMPLATE_DIR . '/admin/layout.php';
    return;
}

// ---- Liste ----
$oeuvres = $db->query('SELECT * FROM oeuvres ORDER BY ordre ASC, created_at DESC')->fetchAll();

ob_start();
?>

<div class="admin-actions">
    <h1 class="admin-title" style="margin-bottom:0">Œuvres</h1>
    <a href="/artistspace/oeuvres/new" class="btn btn-primary btn-sm">+ Ajouter</a>
</div>

<?php if (empty($oeuvres)): ?>
    <p>Aucune œuvre pour le moment.</p>
<?php else: ?>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Titre</th>
                <th>Technique</th>
                <th>Année</th>
                <th>Visible</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($oeuvres as $o): ?>
                <tr>
                    <td><img src="<?= imageUrl($o['image']) ?>" alt=""></td>
                    <td><?= e($o['titre']) ?></td>
                    <td><?= e($o['technique']) ?></td>
                    <td><?= $o['annee'] ?: '—' ?></td>
                    <td><?= $o['visible'] ? '✓' : '✗' ?></td>
                    <td class="actions">
                        <a href="/artistspace/oeuvres/<?= $o['id'] ?>/edit" class="btn btn-sm">Modifier</a>
                        <form method="POST" action="/artistspace/oeuvres/<?= $o['id'] ?>/delete" style="display:inline" onsubmit="return confirm('Supprimer cette œuvre ?')">
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
include TEMPLATE_DIR . '/admin/layout.php';
