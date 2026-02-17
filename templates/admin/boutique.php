<?php
$db = getDb();
$action = $action ?? null;
$id = $id ?? null;

// ---- Suppression ----
if ($action === 'delete' && $id) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf()) {
        $stmt = $db->prepare('SELECT image FROM boutique WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        if ($row && $row['image']) deleteImage($row['image']);
        $db->prepare('DELETE FROM boutique WHERE id = :id')->execute(['id' => $id]);
        flashMessage('success', 'Article supprimé.');
    }
    header('Location: ' . SITE_URL . '/artistspace/boutique');
    exit;
}

// ---- Édition / Création ----
if ($action === 'edit' || $action === 'new') {
    $article = null;
    if ($action === 'edit' && $id) {
        $stmt = $db->prepare('SELECT * FROM boutique WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $article = $stmt->fetch();
        if (!$article) {
            flashMessage('error', 'Article introuvable.');
            header('Location: ' . SITE_URL . '/artistspace/boutique');
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
            $prix = (float)($_POST['prix'] ?? 0);
            $ordre = (int)($_POST['ordre'] ?? 0);
            $disponible = isset($_POST['disponible']) ? 1 : 0;
            $visible = isset($_POST['visible']) ? 1 : 0;

            if (!$titre) $errors[] = 'Le titre est obligatoire.';

            $imagePath = $article['image'] ?? '';
            if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                try {
                    if ($imagePath) deleteImage($imagePath);
                    $imagePath = uploadImage($_FILES['image'], 'boutique');
                } catch (RuntimeException $e) {
                    $errors[] = $e->getMessage();
                }
            }

            if (empty($errors)) {
                if ($article) {
                    $stmt = $db->prepare('UPDATE boutique SET titre=:titre, description=:description, prix=:prix, image=:image, disponible=:disponible, visible=:visible, ordre=:ordre WHERE id=:id');
                    $stmt->execute([
                        'titre' => $titre, 'description' => $description, 'prix' => $prix,
                        'image' => $imagePath, 'disponible' => $disponible,
                        'visible' => $visible, 'ordre' => $ordre, 'id' => $id
                    ]);
                    flashMessage('success', 'Article modifié.');
                } else {
                    $stmt = $db->prepare('INSERT INTO boutique (titre, description, prix, image, disponible, visible, ordre, created_at) VALUES (:titre, :description, :prix, :image, :disponible, :visible, :ordre, :created_at)');
                    $stmt->execute([
                        'titre' => $titre, 'description' => $description, 'prix' => $prix,
                        'image' => $imagePath, 'disponible' => $disponible,
                        'visible' => $visible, 'ordre' => $ordre, 'created_at' => date('Y-m-d H:i:s')
                    ]);
                    flashMessage('success', 'Article ajouté.');
                }
                header('Location: ' . SITE_URL . '/artistspace/boutique');
                exit;
            }
        }
    }

    ob_start();
    ?>
    <h1 class="admin-title"><?= $article ? 'Modifier l\'article' : 'Nouvel article' ?></h1>

    <?php if (!empty($errors)): ?>
        <div class="flash flash-error"><?= implode('<br>', array_map('e', $errors)) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="admin-form">
        <?= csrfField() ?>
        <div class="form-group">
            <label for="titre">Titre *</label>
            <input type="text" id="titre" name="titre" value="<?= e($article['titre'] ?? ($titre ?? '')) ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"><?= e($article['description'] ?? ($description ?? '')) ?></textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="prix">Prix (€)</label>
                <input type="number" id="prix" name="prix" value="<?= e($article['prix'] ?? ($prix ?? '0')) ?>" min="0" step="0.01">
            </div>
            <div class="form-group">
                <label for="ordre">Ordre d'affichage</label>
                <input type="number" id="ordre" name="ordre" value="<?= e($article['ordre'] ?? ($ordre ?? '0')) ?>" min="0">
            </div>
        </div>
        <div class="form-group">
            <label for="image">Image</label>
            <input type="file" id="image" name="image" accept="image/*">
            <?php if (!empty($article['image'])): ?>
                <div class="image-preview"><img src="<?= imageUrl($article['image']) ?>" alt=""></div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label class="toggle-label">
                <span class="toggle-switch">
                    <input type="checkbox" name="disponible" <?= ($article['disponible'] ?? 1) ? 'checked' : '' ?>>
                    <span class="toggle-slider"></span>
                </span>
                <span class="toggle-text">Disponible à la vente</span>
            </label>
        </div>
        <div class="form-group">
            <label class="toggle-label">
                <span class="toggle-switch">
                    <input type="checkbox" name="visible" <?= ($article['visible'] ?? 1) ? 'checked' : '' ?>>
                    <span class="toggle-slider"></span>
                </span>
                <span class="toggle-text">Visible sur le site</span>
            </label>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= $article ? 'Enregistrer' : 'Ajouter' ?></button>
            <a href="/artistspace/boutique" class="btn">Annuler</a>
        </div>
    </form>
    <?php
    $adminContent = ob_get_clean();
    include TEMPLATE_DIR . '/artistspace/layout.php';
    return;
}

// ---- Liste ----
$articles = $db->query('SELECT * FROM boutique ORDER BY ordre ASC, created_at DESC')->fetchAll();

ob_start();
?>

<div class="admin-actions">
    <h1 class="admin-title" style="margin-bottom:0">Boutique</h1>
    <a href="/artistspace/boutique/new" class="btn btn-primary btn-sm">+ Ajouter</a>
</div>

<?php if (empty($articles)): ?>
    <p>Aucun article pour le moment.</p>
<?php else: ?>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Titre</th>
                <th>Prix</th>
                <th>Dispo</th>
                <th>Visible</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($articles as $a): ?>
                <tr>
                    <td><img src="<?= imageUrl($a['image']) ?>" alt=""></td>
                    <td><?= e($a['titre']) ?></td>
                    <td><?= $a['prix'] > 0 ? number_format($a['prix'], 0, ',', ' ') . ' €' : '—' ?></td>
                    <td><?= $a['disponible'] ? '✓' : 'Vendu' ?></td>
                    <td><?= $a['visible'] ? '✓' : '✗' ?></td>
                    <td class="actions">
                        <a href="/artistspace/boutique/<?= $a['id'] ?>/edit" class="btn btn-sm">Modifier</a>
                        <form method="POST" action="/artistspace/boutique/<?= $a['id'] ?>/delete" style="display:inline" onsubmit="return confirm('Supprimer cet article ?')">
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
