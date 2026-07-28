<?php
/**
 * Admin authentication helpers.
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

function auth_start(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function auth_attempt(string $username, string $password): bool
{
    auth_start();
    $stmt = db()->prepare('SELECT id, username, password_hash FROM admins WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if (!$admin || !password_verify($password, $admin['password_hash'])) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['admin_id'] = (int) $admin['id'];
    $_SESSION['admin_username'] = $admin['username'];
    return true;
}

function auth_check(): bool
{
    auth_start();
    return !empty($_SESSION['admin_id']);
}

function auth_require(): void
{
    if (!auth_check()) {
        redirect('login.php');
    }
}

function auth_logout(): void
{
    auth_start();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function auth_username(): string
{
    auth_start();
    return (string) ($_SESSION['admin_username'] ?? 'Admin');
}
