<?php
$db = getDb();
$stmt = $db->prepare('SELECT * FROM oeuvres WHERE id = :id AND visible = 1');
$stmt->execute(['id' => $oeuvre_id]);
$oeuvre = $stmt->fetch();

if (!$oeuvre) {
    http_response_code(404);
    echo '<section class="section" style="padding-top: calc(var(--header-height) + 3rem); text-align: center;"><p>Œuvre introuvable.</p></section>';
    return;
}

// Récupérer l'oeuvre précédente et suivante
$allOeuvres = $db->query('SELECT id FROM oeuvres WHERE visible = 1 ORDER BY ordre ASC, created_at DESC')->fetchAll();
$prevId = null;
$nextId = null;
foreach ($allOeuvres as $i => $o) {
    if ((int)$o['id'] === (int)$oeuvre_id) {
        if ($i > 0) $prevId = $allOeuvres[$i - 1]['id'];
        if ($i < count($allOeuvres) - 1) $nextId = $allOeuvres[$i + 1]['id'];
        break;
    }
}
?>

<section class="section" style="padding-top: calc(var(--header-height) + 3rem); position: relative;">
    <?php if ($prevId): ?>
        <a href="/galerie/<?= $prevId ?>" class="oeuvre-nav oeuvre-nav-prev" title="Œuvre précédente">&#10094;</a>
    <?php endif; ?>
    <?php if ($nextId): ?>
        <a href="/galerie/<?= $nextId ?>" class="oeuvre-nav oeuvre-nav-next" title="Œuvre suivante">&#10095;</a>
    <?php endif; ?>

    <div class="section-inner">
        <div class="bio-layout">
            <div>
                <img src="<?= imageUrl($oeuvre['image']) ?>" alt="<?= e($oeuvre['titre']) ?>" style="width: 100%; border-radius: 2px;">
            </div>
            <div>
                <h1 style="font-family: var(--font-heading); font-size: 2rem; margin-bottom: 1rem;"><?= e($oeuvre['titre']) ?></h1>
                <?php if ($oeuvre['dimensions']): ?>
                    <p><strong>Dimensions :</strong> <?= e($oeuvre['dimensions']) ?></p>
                <?php endif; ?>
                <?php if ($oeuvre['annee']): ?>
                    <p><strong>Année :</strong> <?= $oeuvre['annee'] ?></p>
                <?php endif; ?>
                <?php if ($oeuvre['description']): ?>
                    <div style="margin-top: 1.5rem; font-size: 1.15rem; line-height: 1.9;">
                        <?= nl2br(e($oeuvre['description'])) ?>
                    </div>
                <?php endif; ?>
                <div style="margin-top: 2rem;">
                    <a href="/galerie" class="btn">← Retour à la galerie</a>
                </div>
            </div>
        </div>
    </div>
</section>
