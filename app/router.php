<?php

function route(string $uri): void
{
    // Nettoyer l'URI
    $uri = parse_url($uri, PHP_URL_PATH);
    $uri = rtrim($uri, '/') ?: '/';

    $settings = getAllSettings();

    // Routes admin
    if (str_starts_with($uri, '/admin')) {
        routeAdmin($uri, $settings);
        return;
    }

    // Routes publiques
    $routes = [
        '/' => 'accueil',
        '/galerie' => 'galerie',
        '/biographie' => 'biographie',
        '/contact' => 'contact',
        '/mentions-legales' => 'mentions',
    ];

    // Sections activables
    if (!empty($settings['expos_enabled']) && $settings['expos_enabled'] === '1') {
        $routes['/expositions'] = 'expositions';
    }
    if (!empty($settings['boutique_enabled']) && $settings['boutique_enabled'] === '1') {
        $routes['/boutique'] = 'boutique';
    }

    // Route œuvre individuelle
    if (preg_match('#^/galerie/(\d+)#', $uri, $m)) {
        $oeuvreId = (int)$m[1];
        renderPage('oeuvre', $settings, ['oeuvre_id' => $oeuvreId]);
        return;
    }

    if (isset($routes[$uri])) {
        renderPage($routes[$uri], $settings);
        return;
    }

    // 404
    http_response_code(404);
    renderPage('accueil', $settings);
}

function routeAdmin(string $uri, array $settings): void
{
    $adminRoutes = [
        '/admin' => 'dashboard',
        '/admin/login' => 'login',
        '/admin/logout' => 'logout',
        '/admin/oeuvres' => 'oeuvres',
        '/admin/expositions' => 'expositions',
        '/admin/boutique' => 'boutique',
        '/admin/pages' => 'pages',
        '/admin/settings' => 'settings',
    ];

    // Logout
    if ($uri === '/admin/logout') {
        logoutAdmin();
        header('Location: ' . SITE_URL . '/admin/login');
        exit;
    }

    // Login
    if ($uri === '/admin/login') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (loginAdmin($_POST['password'] ?? '')) {
                header('Location: ' . SITE_URL . '/admin');
                exit;
            }
            $loginError = true;
        }
        include TEMPLATE_DIR . '/admin/login.php';
        return;
    }

    // Toutes les autres pages admin nécessitent d'être connecté
    requireAdmin();

    // Routes CRUD avec ID (edit/delete)
    if (preg_match('#^/admin/(oeuvres|expositions|boutique)/(\d+)/(edit|delete)$#', $uri, $m)) {
        $section = $m[1];
        $id = (int)$m[2];
        $action = $m[3];
        include TEMPLATE_DIR . '/admin/' . $section . '.php';
        return;
    }

    // Route ajout
    if (preg_match('#^/admin/(oeuvres|expositions|boutique)/new$#', $uri, $m)) {
        $section = $m[1];
        $action = 'new';
        $id = null;
        include TEMPLATE_DIR . '/admin/' . $section . '.php';
        return;
    }

    if (isset($adminRoutes[$uri])) {
        $template = $adminRoutes[$uri];
        include TEMPLATE_DIR . '/admin/' . $template . '.php';
        return;
    }

    http_response_code(404);
    echo '404 — Page admin introuvable.';
}

function renderPage(string $page, array $settings, array $extra = []): void
{
    extract($extra);
    $pageFile = TEMPLATE_DIR . '/pages/' . $page . '.php';
    if (!file_exists($pageFile)) {
        http_response_code(404);
        return;
    }

    // Charger le contenu de la page dans un buffer
    ob_start();
    include $pageFile;
    $content = ob_get_clean();

    // Injecter dans le layout
    include TEMPLATE_DIR . '/layout.php';
}

function navItems(array $settings): array
{
    $items = [
        ['url' => '/', 'label' => 'Accueil'],
        ['url' => '/galerie', 'label' => 'Galerie'],
        ['url' => '/biographie', 'label' => 'Biographie'],
    ];

    if (!empty($settings['expos_enabled']) && $settings['expos_enabled'] === '1') {
        $items[] = ['url' => '/expositions', 'label' => 'Expositions'];
    }

    if (!empty($settings['boutique_enabled']) && $settings['boutique_enabled'] === '1') {
        $items[] = ['url' => '/boutique', 'label' => 'Boutique'];
    }

    $items[] = ['url' => '/contact', 'label' => 'Contact'];

    return $items;
}
