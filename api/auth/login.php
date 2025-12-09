<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';
require_method('POST');
rate_limit_auth();
verify_csrf();

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$email = sanitize_string($input['email'] ?? '');
$password = $input['password'] ?? '';

$pdo = db();
$stmt = $pdo->prepare("SELECT * FROM users WHERE email=:email");
$stmt->execute([':email' => $email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password_hash'])) {
    json_response(['error' => 'Invalid credentials'], 401);
}

if ((int)$user['is_email_verified'] === 0) {
    json_response(['error' => 'Email not verified'], 401);
}

login_user($user);

json_response(['message' => 'Logged in', 'user' => ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email'], 'role' => $user['role']]]);

