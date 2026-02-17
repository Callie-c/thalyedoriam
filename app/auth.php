<?php

function isAdmin(): bool
{
    return !empty($_SESSION['admin_logged_in']);
}

function requireAdmin(): void
{
    if (!isAdmin()) {
        header('Location: ' . SITE_URL . '/artistspace/login');
        exit;
    }
}

function loginAdmin(string $password): bool
{
    $hash = getSetting('admin_password');
    if (password_verify($password, $hash)) {
        $_SESSION['admin_logged_in'] = true;
        return true;
    }
    return false;
}

function logoutAdmin(): void
{
    unset($_SESSION['admin_logged_in']);
    session_destroy();
}
