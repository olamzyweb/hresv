<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';
require_method('POST');
verify_csrf();
rate_limit_auth();

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$email = sanitize_string($input['email'] ?? '');

$pdo = db();
$stmt = $pdo->prepare("SELECT id FROM users WHERE email=:email");
$stmt->execute([':email' => $email]);
$user = $stmt->fetch();

if ($user) {
    $token = bin2hex(random_bytes(16));
    $pdo->prepare("UPDATE users SET password_reset_token=:token, token_expires_at=DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id=:id")->execute([':token' => $token, ':id' => $user['id']]);
    $link = (getenv('APP_URL') ?: '') . "/reset.html?token={$token}&email=" . urlencode($email);
    send_email($email, 'Password reset', "<p>Reset your password: <a href=\"{$link}\">Reset</a></p>", "Reset: {$link}");
}

json_response(['message' => 'If the email exists, a reset link was sent.']);

