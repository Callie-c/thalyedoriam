<?php
$db = getDb();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf()) {
    $fields = [
        'site_title', 'site_description',
        'contact_email', 'contact_phone', 'contact_address',
        'instagram', 'facebook',
    ];
    foreach ($fields as $field) {
        setSetting($field, trim($_POST[$field] ?? ''));
    }

    // Toggles
    setSetting('expos_enabled', isset($_POST['expos_enabled']) ? '1' : '0');
    setSetting('boutique_enabled', isset($_POST['boutique_enabled']) ? '1' : '0');

    // Changement de mot de passe
    $newPassword = trim($_POST['new_password'] ?? '');
    if ($newPassword) {
        if (strlen($newPassword) < 6) {
            flashMessage('error', 'Le mot de passe doit faire au moins 6 caractères.');
            header('Location: ' . SITE_URL . '/artistspace/settings');
            exit;
        }
        setSetting('admin_password', password_hash($newPassword, PASSWORD_DEFAULT));
    }

    flashMessage('success', 'Réglages enregistrés.');
    header('Location: ' . SITE_URL . '/artistspace/settings');
    exit;
}

$s = getAllSettings();

ob_start();
?>

<h1 class="admin-title">Réglages</h1>

<form method="POST" class="admin-form" style="max-width: 800px;">
    <?= csrfField() ?>

    <h3 style="font-family: var(--font-heading); margin-bottom: 1rem;">Informations du site</h3>
    <div class="form-row">
        <div class="form-group">
            <label for="site_title">Titre du site</label>
            <input type="text" id="site_title" name="site_title" value="<?= e($s['site_title'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="site_description">Description</label>
            <input type="text" id="site_description" name="site_description" value="<?= e($s['site_description'] ?? '') ?>">
        </div>
    </div>

    <h3 style="font-family: var(--font-heading); margin: 2rem 0 1rem;">Coordonnées</h3>
    <div class="form-row">
        <div class="form-group">
            <label for="contact_email">Email de contact</label>
            <input type="email" id="contact_email" name="contact_email" value="<?= e($s['contact_email'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="contact_phone">Téléphone</label>
            <input type="text" id="contact_phone" name="contact_phone" value="<?= e($s['contact_phone'] ?? '') ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="contact_address">Adresse</label>
        <textarea id="contact_address" name="contact_address" rows="2"><?= e($s['contact_address'] ?? '') ?></textarea>
    </div>

    <h3 style="font-family: var(--font-heading); margin: 2rem 0 1rem;">Réseaux sociaux</h3>
    <div class="form-row">
        <div class="form-group">
            <label for="instagram">Instagram (URL complète)</label>
            <input type="url" id="instagram" name="instagram" value="<?= e($s['instagram'] ?? '') ?>" placeholder="https://instagram.com/...">
        </div>
        <div class="form-group">
            <label for="facebook">Facebook (URL complète)</label>
            <input type="url" id="facebook" name="facebook" value="<?= e($s['facebook'] ?? '') ?>" placeholder="https://facebook.com/...">
        </div>
    </div>

    <h3 style="font-family: var(--font-heading); margin: 2rem 0 1rem;">Sections activables</h3>
    <div class="form-group">
        <label class="toggle-label">
            <span class="toggle-switch">
                <input type="checkbox" name="expos_enabled" <?= ($s['expos_enabled'] ?? '0') === '1' ? 'checked' : '' ?>>
                <span class="toggle-slider"></span>
            </span>
            <span class="toggle-text">Activer la section Expositions</span>
        </label>
    </div>
    <div class="form-group">
        <label class="toggle-label">
            <span class="toggle-switch">
                <input type="checkbox" name="boutique_enabled" <?= ($s['boutique_enabled'] ?? '0') === '1' ? 'checked' : '' ?>>
                <span class="toggle-slider"></span>
            </span>
            <span class="toggle-text">Activer la section Boutique</span>
        </label>
    </div>

    <h3 style="font-family: var(--font-heading); margin: 2rem 0 1rem;">Mot de passe admin</h3>
    <div class="form-group">
        <label for="new_password">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
        <input type="password" id="new_password" name="new_password" placeholder="Minimum 6 caractères">
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </div>
</form>

<?php
$adminContent = ob_get_clean();
include TEMPLATE_DIR . '/admin/layout.php';
