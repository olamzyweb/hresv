<?php
declare(strict_types=1);

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_auth(): array
{
    $user = current_user();
    if (!$user) {
        json_response(['error' => 'Unauthorized'], 401);
    }
    return $user;
}

function require_admin(): array
{
    $user = require_auth();
    if (($user['role'] ?? 'guest') !== 'admin') {
        json_response(['error' => 'Forbidden'], 403);
    }
    return $user;
}

function login_user(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'],
    ];
}

function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function rate_limit_auth(): void
{
    $key = 'auth_attempts';
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 0, 'last' => time()];
    }
    $window = 60;
    if (time() - $_SESSION[$key]['last'] > $window) {
        $_SESSION[$key] = ['count' => 0, 'last' => time()];
    }
    $_SESSION[$key]['count']++;
    if ($_SESSION[$key]['count'] > 10) {
        json_response(['error' => 'Too many attempts, slow down'], 429);
    }
}

