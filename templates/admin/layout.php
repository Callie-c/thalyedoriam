<!DOCTYPE html>
<html lang="fr">
<head>
    <script>
        (function(){document.documentElement.removeAttribute('data-theme');})();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — <?= e(getSetting('site_title', "Thalye d'Oriam")) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= SITE_URL ?>/css/style.css?v=<?= filemtime(PUBLIC_ROOT . '/css/style.css') ?>">
</head>
<body class="admin-body">
    <header class="admin-header">
        <a href="/artistspace" class="site-logo"><?= e(getSetting('site_title', "Thalye d'Oriam")) ?></a>
        <nav class="admin-nav">
            <?php
            $adminUri = $_SERVER['REQUEST_URI'];
            $adminLinks = [
                '/artistspace' => 'Tableau de bord',
                '/artistspace/oeuvres' => 'Œuvres',
                '/artistspace/expositions' => 'Expositions',
                '/artistspace/boutique' => 'Boutique',
                '/artistspace/pages' => 'Pages',
                '/artistspace/settings' => 'Réglages',
            ];
            foreach ($adminLinks as $url => $label):
                $isActive = ($url === '/artistspace' && $adminUri === '/artistspace') || ($url !== '/artistspace' && str_starts_with($adminUri, $url));
            ?>
                <a href="<?= $url ?>" class="<?= $isActive ? 'active' : '' ?>"><?= $label ?></a>
            <?php endforeach; ?>
        </nav>
        <a href="/artistspace/logout" class="admin-logout">Déconnexion</a>
    </header>

    <div class="admin-main">
        <?php
        $flash = getFlash();
        if ($flash): ?>
            <div class="flash flash-<?= $flash['type'] ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>

        <?= $adminContent ?>
    </div>

    <script src="<?= SITE_URL ?>/js/main.js"></script>
</body>
</html>
