<?php
$db = getDb();
$oeuvres = $db->query('SELECT * FROM oeuvres WHERE visible = 1 ORDER BY ordre ASC, created_at DESC')->fetchAll();

// Récupérer les techniques pour le filtre
$techniques = [];
foreach ($oeuvres as $o) {
    if ($o['technique'] && !in_array($o['technique'], $techniques)) {
        $techniques[] = $o['technique'];
    }
}
sort($techniques);
?>

<section class="section" style="padding-top: calc(var(--header-height) + 3rem);">
    <div class="section-inner">
        <h1 class="section-title">Galerie</h1>
        <div class="section-divider"></div>

        <?php if (!empty($techniques)): ?>
        <div style="text-align: center; margin-bottom: 2rem;">
            <button class="btn btn-sm filter-btn active" data-filter="all">Toutes</button>
            <?php foreach ($techniques as $tech): ?>
                <button class="btn btn-sm filter-btn" data-filter="<?= e($tech) ?>"><?= e($tech) ?></button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (empty($oeuvres)): ?>
            <p style="text-align: center; color: var(--color-text-light);">La galerie sera bientôt enrichie de nouvelles œuvres.</p>
        <?php else: ?>
            <div class="gallery-grid" id="gallery-grid">
                <?php foreach ($oeuvres as $oeuvre): ?>
                    <a href="/galerie/<?= $oeuvre['id'] ?>" class="gallery-item" data-technique="<?= e($oeuvre['technique']) ?>" style="text-decoration: none;">
                        <img src="<?= imageUrl($oeuvre['image']) ?>" alt="<?= e($oeuvre['titre']) ?>" loading="lazy">
                        <div class="gallery-overlay">
                            <h3><?= e($oeuvre['titre']) ?></h3>
                            <p><?= e($oeuvre['technique']) ?><?= $oeuvre['annee'] ? ' — ' . $oeuvre['annee'] : '' ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
