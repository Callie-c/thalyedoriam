<?php

function e(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function uploadImage(array $file, string $subdir): string
{
    $targetDir = UPLOAD_DIR . '/' . $subdir;
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowed)) {
        throw new RuntimeException('Format d\'image non autorisé. Formats acceptés : ' . implode(', ', $allowed));
    }

    if ($file['size'] > 10 * 1024 * 1024) {
        throw new RuntimeException('Image trop lourde (max 10 Mo).');
    }

    $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
    $targetPath = $targetDir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new RuntimeException('Erreur lors de l\'upload de l\'image.');
    }

    return $subdir . '/' . $filename;
}

function deleteImage(string $path): void
{
    if ($path && file_exists(UPLOAD_DIR . '/' . $path)) {
        unlink(UPLOAD_DIR . '/' . $path);
    }
}

function imageUrl(string $path): string
{
    if (!$path) {
        return SITE_URL . '/img/placeholder.svg';
    }
    return SITE_URL . '/uploads/' . $path;
}

function formatDate(string $date): string
{
    if (!$date) return '';
    $months = [
        1 => 'janvier', 'février', 'mars', 'avril', 'mai', 'juin',
        'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'
    ];
    $ts = strtotime($date);
    if (!$ts) return $date;
    $day = date('j', $ts);
    $month = $months[(int)date('n', $ts)];
    $year = date('Y', $ts);
    return "$day $month $year";
}

function expoStatus(string $dateDebut, string $dateFin): string
{
    $now = date('Y-m-d');
    if ($dateFin && $dateFin < $now) return 'passee';
    if ($dateDebut && $dateDebut <= $now && (!$dateFin || $dateFin >= $now)) return 'en_cours';
    if ($dateDebut && $dateDebut > $now) return 'a_venir';
    return 'a_venir';
}

function expoStatusLabel(string $status): string
{
    return match ($status) {
        'en_cours' => 'En cours',
        'a_venir' => 'À venir',
        'passee' => 'Passée',
        default => '',
    };
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrfToken() . '">';
}

function verifyCsrf(): bool
{
    $token = $_POST['csrf_token'] ?? '';
    return hash_equals(csrfToken(), $token);
}

function flashMessage(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}
