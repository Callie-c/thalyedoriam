<?php

define('APP_ROOT', dirname(__DIR__));
define('PUBLIC_ROOT', APP_ROOT . '/public');
define('UPLOAD_DIR', PUBLIC_ROOT . '/uploads');
define('DATA_DIR', APP_ROOT . '/data');
define('TEMPLATE_DIR', APP_ROOT . '/templates');

define('SITE_URL', getenv('SITE_URL') ?: 'http://localhost:8000');

// Timezone
date_default_timezone_set('Europe/Paris');

// Détection prod/dev
$isProd = strpos($_SERVER['HTTP_HOST'] ?? '', 'thalyedoriam.fr') !== false;

if ($isProd) {
    ini_set('display_errors', 0);
    error_reporting(0);
} else {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
