<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';
require_method('POST');
verify_csrf();

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$email = sanitize_string($input['email'] ?? '');
$token = sanitize_string($input['token'] ?? '');
$password = $input['password'] ?? '';

if (!validate_password($password)) {
    json_response(['error' => 'Password too short'], 422);
}

$pdo = db();
$stmt = $pdo->prepare("SELECT id FROM users WHERE email=:email AND password_reset_token=:token AND token_expires_at >= NOW()");
$stmt->execute([':email' => $email, ':token' => $token]);
$user = $stmt->fetch();

if (!$user) {
    json_response(['error' => 'Invalid or expired token'], 400);
}

$pdo->prepare("UPDATE users SET password_hash=:hash, password_reset_token=NULL, token_expires_at=NULL WHERE id=:id")->execute([
    ':hash' => password_hash($password, PASSWORD_BCRYPT),
    ':id' => $user['id'],
]);

json_response(['message' => 'Password updated']);

