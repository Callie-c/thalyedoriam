<?php
$db = getDb();
$nbOeuvres = $db->query('SELECT COUNT(*) FROM oeuvres')->fetchColumn();
$nbExpos = $db->query('SELECT COUNT(*) FROM expositions')->fetchColumn();
$nbBoutique = $db->query('SELECT COUNT(*) FROM boutique')->fetchColumn();

ob_start();
?>

<h1 class="admin-title">Tableau de bord</h1>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-number"><?= $nbOeuvres ?></div>
        <div class="stat-label">Œuvres</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= $nbExpos ?></div>
        <div class="stat-label">Expositions</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= $nbBoutique ?></div>
        <div class="stat-label">Articles boutique</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem;">
    <div>
        <h3 style="font-family: var(--font-heading); margin-bottom: 1rem;">Actions rapides</h3>
        <p><a href="/artistspace/oeuvres/new" class="btn btn-sm">+ Ajouter une œuvre</a></p>
        <p style="margin-top: 0.5rem;"><a href="/artistspace/expositions/new" class="btn btn-sm">+ Ajouter une exposition</a></p>
        <p style="margin-top: 0.5rem;"><a href="/artistspace/boutique/new" class="btn btn-sm">+ Ajouter un article</a></p>
    </div>
    <div>
        <h3 style="font-family: var(--font-heading); margin-bottom: 1rem;">État des sections</h3>
        <p>Expositions : <strong><?= getSetting('expos_enabled') === '1' ? '✓ Activées' : '✗ Désactivées' ?></strong></p>
        <p>Boutique : <strong><?= getSetting('boutique_enabled') === '1' ? '✓ Activée' : '✗ Désactivée' ?></strong></p>
        <p style="margin-top: 1rem;"><a href="/artistspace/settings">Modifier les réglages →</a></p>
    </div>
</div>

<p style="margin-top: 3rem;"><a href="/" target="_blank">Voir le site →</a></p>

<?php
$adminContent = ob_get_clean();
include TEMPLATE_DIR . '/artistspace/layout.php';
