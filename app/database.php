<?php

function getDb(): PDO
{
    static $db = null;
    if ($db !== null) {
        return $db;
    }

    $dbPath = DATA_DIR . '/site.db';
    $isNew = !file_exists($dbPath);

    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $db->exec('PRAGMA journal_mode=WAL');
    $db->exec('PRAGMA foreign_keys=ON');

    if ($isNew) {
        initDatabase($db);
    }

    return $db;
}

function initDatabase(PDO $db): void
{
    $db->exec('
        CREATE TABLE IF NOT EXISTS settings (
            key TEXT PRIMARY KEY,
            value TEXT
        )
    ');
    $db->exec('
        CREATE TABLE IF NOT EXISTS oeuvres (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            titre TEXT NOT NULL,
            description TEXT DEFAULT "",
            technique TEXT DEFAULT "",
            dimensions TEXT DEFAULT "",
            annee INTEGER,
            image TEXT DEFAULT "",
            ordre INTEGER DEFAULT 0,
            visible INTEGER DEFAULT 1,
            created_at TEXT DEFAULT ""
        )
    ');
    $db->exec('
        CREATE TABLE IF NOT EXISTS expositions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            titre TEXT NOT NULL,
            lieu TEXT DEFAULT "",
            date_debut TEXT,
            date_fin TEXT,
            description TEXT DEFAULT "",
            image TEXT DEFAULT "",
            visible INTEGER DEFAULT 1,
            created_at TEXT DEFAULT ""
        )
    ');
    $db->exec('
        CREATE TABLE IF NOT EXISTS boutique (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            titre TEXT NOT NULL,
            description TEXT DEFAULT "",
            prix REAL DEFAULT 0,
            image TEXT DEFAULT "",
            disponible INTEGER DEFAULT 1,
            visible INTEGER DEFAULT 1,
            ordre INTEGER DEFAULT 0,
            created_at TEXT DEFAULT ""
        )
    ');

    // Réglages par défaut
    $defaults = [
        'site_title' => "Thalye d'Oriam",
        'site_description' => 'Artiste peintre contemporaine',
        'contact_email' => '',
        'contact_phone' => '',
        'contact_address' => '',
        'instagram' => '',
        'facebook' => '',
        'expos_enabled' => '0',
        'boutique_enabled' => '0',
        'bio_text' => '',
        'accueil_text' => '',
        'admin_password' => password_hash('admin', PASSWORD_DEFAULT),
    ];

    $stmt = $db->prepare('INSERT OR IGNORE INTO settings (key, value) VALUES (:key, :value)');
    foreach ($defaults as $key => $value) {
        $stmt->execute(['key' => $key, 'value' => $value]);
    }
}

function getSetting(string $key, string $default = ''): string
{
    $db = getDb();
    $stmt = $db->prepare('SELECT value FROM settings WHERE key = :key');
    $stmt->execute(['key' => $key]);
    $row = $stmt->fetch();
    return $row ? $row['value'] : $default;
}

function setSetting(string $key, string $value): void
{
    $db = getDb();
    $stmt = $db->prepare('INSERT OR REPLACE INTO settings (key, value) VALUES (:key, :value)');
    $stmt->execute(['key' => $key, 'value' => $value]);
}

function getAllSettings(): array
{
    $db = getDb();
    $rows = $db->query('SELECT key, value FROM settings')->fetchAll();
    $settings = [];
    foreach ($rows as $row) {
        $settings[$row['key']] = $row['value'];
    }
    return $settings;
}
