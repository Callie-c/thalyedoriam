<?php
$db = getDb();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf()) {
    setSetting('accueil_text', trim($_POST['accueil_text'] ?? ''));
    setSetting('bio_text', trim($_POST['bio_text'] ?? ''));

    // Upload photo bio
    if (!empty($_FILES['bio_photo']['name']) && $_FILES['bio_photo']['error'] === UPLOAD_ERR_OK) {
        try {
            $oldPhoto = getSetting('bio_photo', '');
            if ($oldPhoto) deleteImage($oldPhoto);
            $photoPath = uploadImage($_FILES['bio_photo'], 'oeuvres');
            setSetting('bio_photo', $photoPath);
        } catch (RuntimeException $e) {
            flashMessage('error', $e->getMessage());
            header('Location: ' . SITE_URL . '/artistspace/pages');
            exit;
        }
    }

    flashMessage('success', 'Contenus enregistrés.');
    header('Location: ' . SITE_URL . '/artistspace/pages');
    exit;
}

$s = getAllSettings();

ob_start();
?>

<h1 class="admin-title">Édition des pages</h1>

<form method="POST" enctype="multipart/form-data" class="admin-form" style="max-width: 800px;">
    <?= csrfField() ?>

    <h3 style="font-family: var(--font-heading); margin-bottom: 1rem;">Page d'accueil</h3>
    <div class="form-group">
        <label for="accueil_text">Texte de présentation (affiché sous le hero)</label>
        <textarea id="accueil_text" name="accueil_text" rows="4"><?= e($s['accueil_text'] ?? '') ?></textarea>
    </div>

    <h3 style="font-family: var(--font-heading); margin: 2rem 0 1rem;">Page Biographie</h3>
    <div class="form-group">
        <label for="bio_photo">Photo de l'artiste</label>
        <input type="file" id="bio_photo" name="bio_photo" accept="image/*">
        <?php if (!empty($s['bio_photo'])): ?>
            <div class="image-preview"><img src="<?= imageUrl($s['bio_photo']) ?>" alt=""></div>
        <?php endif; ?>
    </div>
    <div class="form-group">
        <label for="bio_text">Texte biographique</label>
        <textarea id="bio_text" name="bio_text" rows="10"><?= e($s['bio_text'] ?? '') ?></textarea>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </div>
</form>

<?php
$adminContent = ob_get_clean();
include TEMPLATE_DIR . '/admin/layout.php';
