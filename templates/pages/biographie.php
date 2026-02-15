<?php
$bioText = $settings['bio_text'] ?? '';
?>

<section class="section" style="padding-top: calc(var(--header-height) + 3rem);">
    <div class="section-inner">
        <h1 class="section-title">Biographie</h1>
        <div class="section-divider"></div>

        <?php if ($bioText): ?>
            <div class="bio-layout">
                <div class="bio-photo">
                    <img src="<?= imageUrl(getSetting('bio_photo', '')) ?>" alt="<?= e($settings['site_title'] ?? "Thalye d'Oriam") ?>">
                </div>
                <div class="bio-text">
                    <?= nl2br(e($bioText)) ?>
                </div>
            </div>
        <?php else: ?>
            <p style="text-align: center; color: var(--color-text-light);">La biographie sera bientôt disponible.</p>
        <?php endif; ?>
    </div>
</section>
