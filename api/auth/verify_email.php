<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';
require_method('GET');

$email = sanitize_string($_GET['email'] ?? '');
$token = sanitize_string($_GET['token'] ?? '');

$pdo = db();
$stmt = $pdo->prepare("SELECT id FROM users WHERE email=:email AND email_verification_token=:token AND token_expires_at >= NOW()");
$stmt->execute([':email' => $email, ':token' => $token]);
$user = $stmt->fetch();

if (!$user) {
    json_response(['error' => 'Invalid or expired token'], 400);
}

$pdo->prepare("UPDATE users SET is_email_verified=1, email_verification_token=NULL, token_expires_at=NULL WHERE id=:id")->execute([':id' => $user['id']]);

json_response(['message' => 'Email verified']);

