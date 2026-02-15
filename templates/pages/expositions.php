<?php
$db = getDb();
$expos = $db->query('SELECT * FROM expositions WHERE visible = 1 ORDER BY date_debut DESC')->fetchAll();
?>

<section class="section" style="padding-top: calc(var(--header-height) + 3rem);">
    <div class="section-inner" style="max-width: 800px;">
        <h1 class="section-title">Expositions</h1>
        <div class="section-divider"></div>

        <?php if (empty($expos)): ?>
            <p style="text-align: center; color: var(--color-text-light);">Aucune exposition pour le moment.</p>
        <?php else: ?>
            <div class="expo-timeline">
                <?php foreach ($expos as $expo):
                    $status = expoStatus($expo['date_debut'], $expo['date_fin']);
                ?>
                    <div class="expo-item">
                        <span class="expo-badge <?= $status ?>"><?= expoStatusLabel($status) ?></span>
                        <h3 class="expo-title"><?= e($expo['titre']) ?></h3>
                        <p class="expo-meta">
                            <?php if ($expo['lieu']): ?><?= e($expo['lieu']) ?> — <?php endif; ?>
                            <?= formatDate($expo['date_debut']) ?>
                            <?php if ($expo['date_fin']): ?> au <?= formatDate($expo['date_fin']) ?><?php endif; ?>
                        </p>
                        <?php if ($expo['description']): ?>
                            <p class="expo-desc"><?= nl2br(e($expo['description'])) ?></p>
                        <?php endif; ?>
                        <?php if ($expo['image']): ?>
                            <img src="<?= imageUrl($expo['image']) ?>" alt="<?= e($expo['titre']) ?>" style="margin-top: 1rem; max-width: 100%; border-radius: 2px;">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
