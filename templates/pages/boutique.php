<?php
$db = getDb();
$articles = $db->query('SELECT * FROM boutique WHERE visible = 1 ORDER BY ordre ASC, created_at DESC')->fetchAll();
?>

<section class="section" style="padding-top: calc(var(--header-height) + 3rem);">
    <div class="section-inner">
        <h1 class="section-title">Boutique</h1>
        <div class="section-divider"></div>

        <?php if (empty($articles)): ?>
            <p style="text-align: center; color: var(--color-text-light);">La boutique sera bientôt disponible.</p>
        <?php else: ?>
            <div class="shop-grid">
                <?php foreach ($articles as $article): ?>
                    <div class="shop-item">
                        <?php if (!$article['disponible']): ?>
                            <span class="shop-item-sold">Vendu</span>
                        <?php endif; ?>
                        <img src="<?= imageUrl($article['image']) ?>" alt="<?= e($article['titre']) ?>" loading="lazy">
                        <div class="shop-item-info">
                            <h3 class="shop-item-title"><?= e($article['titre']) ?></h3>
                            <?php if ($article['description']): ?>
                                <p style="font-size: 0.9rem; color: var(--color-text-light); margin: 0.3rem 0;"><?= e($article['description']) ?></p>
                            <?php endif; ?>
                            <?php if ($article['prix'] > 0): ?>
                                <p class="shop-item-price"><?= number_format($article['prix'], 0, ',', ' ') ?> €</p>
                            <?php endif; ?>
                            <?php if ($article['disponible']): ?>
                                <a href="/contact?sujet=<?= urlencode('Intéressé(e) par : ' . $article['titre']) ?>" class="btn btn-sm" style="margin-top: 0.8rem;">Me contacter</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
