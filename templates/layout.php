<!DOCTYPE html>
<html lang="fr">
<head>
    <script>
        (function(){var t=localStorage.getItem('theme');if(t){document.documentElement.setAttribute('data-theme',t);}else{document.documentElement.setAttribute('data-theme','dark');localStorage.setItem('theme','dark');}})();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($settings['site_title'] ?? "Thalye d'Oriam") ?> — <?= e($settings['site_description'] ?? 'Artiste peintre') ?></title>
    <meta name="description" content="<?= e($settings['site_description'] ?? '') ?>">
    <meta property="og:title" content="<?= e($settings['site_title'] ?? "Thalye d'Oriam") ?>">
    <meta property="og:description" content="<?= e($settings['site_description'] ?? '') ?>">
    <meta property="og:type" content="website">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= SITE_URL ?>/css/style.css">
</head>
<body>
    <header class="site-header" id="site-header">
        <div class="header-inner">
            <a href="/" class="site-logo"><?= e($settings['site_title'] ?? "Thalye d'Oriam") ?></a>
            <button class="menu-toggle" id="menu-toggle" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
            <nav class="site-nav" id="site-nav">
                <?php foreach (navItems($settings) as $item): ?>
                    <a href="<?= $item['url'] ?>" class="nav-link <?= ($_SERVER['REQUEST_URI'] === $item['url']) ? 'active' : '' ?>"><?= e($item['label']) ?></a>
                <?php endforeach; ?>
            </nav>
            <button class="theme-toggle" id="theme-toggle" aria-label="Changer de thème">
                <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
                <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
            </button>
        </div>
    </header>

    <main class="site-main">
        <?= $content ?>
    </main>

    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-brand">
                <span class="footer-name"><?= e($settings['site_title'] ?? "Thalye d'Oriam") ?></span>
                <span class="footer-desc"><?= e($settings['site_description'] ?? '') ?></span>
            </div>
            <div class="footer-social">
                <?php if (!empty($settings['instagram'])): ?>
                    <a href="<?= e($settings['instagram']) ?>" target="_blank" rel="noopener" aria-label="Instagram">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="5"/><circle cx="17.5" cy="6.5" r="1.5"/></svg>
                    </a>
                <?php endif; ?>
                <?php if (!empty($settings['facebook'])): ?>
                    <a href="<?= e($settings['facebook']) ?>" target="_blank" rel="noopener" aria-label="Facebook">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                    </a>
                <?php endif; ?>
            </div>
            <div class="footer-links">
                <a href="/mentions-legales">Mentions légales</a>
                <?php if (isAdmin()): ?>
                    <a href="/artistspace">Administration</a>
                <?php endif; ?>
            </div>
            <p class="footer-copy">&copy; <?= date('Y') ?> <?= e($settings['site_title'] ?? "Thalye d'Oriam") ?>. Tous droits réservés.</p>
        </div>
    </footer>

    <div class="lightbox" id="lightbox">
        <button class="lightbox-close" id="lightbox-close">&times;</button>
        <button class="lightbox-prev" id="lightbox-prev">&#10094;</button>
        <button class="lightbox-next" id="lightbox-next">&#10095;</button>
        <div class="lightbox-content">
            <img id="lightbox-img" src="" alt="">
            <div class="lightbox-info" id="lightbox-info"></div>
        </div>
    </div>

    <script src="<?= SITE_URL ?>/js/main.js"></script>
</body>
</html>
