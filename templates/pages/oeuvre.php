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
?>

<section class="section" style="padding-top: calc(var(--header-height) + 3rem);">
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
