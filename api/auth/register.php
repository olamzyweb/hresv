<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';
require_method('POST');
rate_limit_auth();
verify_csrf();

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$name = sanitize_string($input['name'] ?? '');
$email = sanitize_string($input['email'] ?? '');
$password = $input['password'] ?? '';
$phone = sanitize_string($input['phone'] ?? '');

$errors = require_fields($input, ['name', 'email', 'password']);
if (!validate_email($email)) {
    $errors['email'] = 'Invalid email';
}
if (!validate_password($password)) {
    $errors['password'] = 'Min 8 chars';
}
if ($errors) {
    json_response(['errors' => $errors], 422);
}

$pdo = db();
$stmt = $pdo->prepare("SELECT id FROM users WHERE email=:email");
$stmt->execute([':email' => $email]);
if ($stmt->fetch()) {
    json_response(['error' => 'Email already registered'], 409);
}

$token = bin2hex(random_bytes(16));
$stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, phone, role, email_verification_token, token_expires_at) VALUES (:name, :email, :hash, :phone, 'guest', :token, DATE_ADD(NOW(), INTERVAL 1 DAY))");
$stmt->execute([
    ':name' => $name,
    ':email' => $email,
    ':hash' => password_hash($password, PASSWORD_BCRYPT),
    ':phone' => $phone,
    ':token' => $token,
]);

$verifyLink = (getenv('APP_URL') ?: '') . "/api/auth/verify_email.php?token={$token}&email=" . urlencode($email);
send_email($email, 'Verify your account', "<p>Click to verify: <a href=\"{$verifyLink}\">Verify</a></p>", "Verify here: {$verifyLink}");

json_response(['message' => 'Registered. Check email to verify.']);

