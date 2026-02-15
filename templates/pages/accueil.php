<?php
$db = getDb();
$oeuvresRecentes = $db->query('SELECT * FROM oeuvres WHERE visible = 1 ORDER BY ordre ASC, created_at DESC LIMIT 6')->fetchAll();
$heroOeuvre = $oeuvresRecentes[0] ?? null;
?>

<section class="hero-section">
    <div class="hero-bg" style="background-image: url('<?= $heroOeuvre ? imageUrl($heroOeuvre['image']) : SITE_URL . '/img/placeholder.svg' ?>')"></div>
    <div class="hero-content">
        <h1 class="hero-title"><?= e($settings['site_title'] ?? "Thalye d'Oriam") ?></h1>
        <p class="hero-subtitle"><?= e($settings['site_description'] ?? 'Artiste peintre contemporaine') ?></p>
        <a href="/galerie" class="hero-cta">Découvrir les œuvres</a>
    </div>
</section>

<?php if (!empty($settings['accueil_text'])): ?>
<section class="section">
    <div class="section-inner" style="max-width: 800px;">
        <div class="section-divider"></div>
        <p style="text-align: center; font-size: 1.15rem; line-height: 1.9;">
            <?= nl2br(e($settings['accueil_text'])) ?>
        </p>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($oeuvresRecentes)): ?>
<section class="section" style="background: var(--color-card-bg);">
    <div class="section-inner">
        <h2 class="section-title">Œuvres récentes</h2>
        <div class="section-divider"></div>
        <div class="gallery-grid">
            <?php foreach ($oeuvresRecentes as $oeuvre): ?>
                <div class="gallery-item" data-lightbox
                     data-src="<?= imageUrl($oeuvre['image']) ?>"
                     data-title="<?= e($oeuvre['titre']) ?>"
                     data-info="<?= e($oeuvre['technique']) ?><?= $oeuvre['dimensions'] ? ' — ' . e($oeuvre['dimensions']) : '' ?><?= $oeuvre['annee'] ? ' (' . $oeuvre['annee'] . ')' : '' ?>">
                    <img src="<?= imageUrl($oeuvre['image']) ?>" alt="<?= e($oeuvre['titre']) ?>" loading="lazy">
                    <div class="gallery-overlay">
                        <h3><?= e($oeuvre['titre']) ?></h3>
                        <p><?= e($oeuvre['technique']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align: center; margin-top: 3rem;">
            <a href="/galerie" class="btn">Voir toute la galerie</a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($settings['instagram'])): ?>
<section class="section">
    <div class="section-inner" style="text-align: center;">
        <a href="<?= e($settings['instagram']) ?>" target="_blank" rel="noopener" class="instagram-link" aria-label="Suivez-moi sur Instagram">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="2" width="20" height="20" rx="5"/>
                <circle cx="12" cy="12" r="5"/>
                <circle cx="17.5" cy="6.5" r="1.5"/>
            </svg>
            <span>Suivez-moi sur Instagram</span>
        </a>
    </div>
</section>
<?php endif; ?>
