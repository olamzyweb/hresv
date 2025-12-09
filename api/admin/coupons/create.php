<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../config/bootstrap.php';
require_method('POST');
verify_csrf();
require_admin();

$input = $_POST ?: json_decode(file_get_contents('php://input'), true);
$errors = require_fields($input, ['code', 'type', 'value']);
if ($errors) {
    json_response(['errors' => $errors], 422);
}

$pdo = db();
$stmt = $pdo->prepare("INSERT INTO coupons (code, type, value, min_amount, start_date, end_date, usage_limit) VALUES (:code, :type, :val, :min, :start, :end, :limit)");
$stmt->execute([
    ':code' => sanitize_string($input['code']),
    ':type' => sanitize_string($input['type']),
    ':val' => (float)$input['value'],
    ':min' => (float)($input['min_amount'] ?? 0),
    ':start' => $input['start_date'] ?? null,
    ':end' => $input['end_date'] ?? null,
    ':limit' => $input['usage_limit'] ?? null,
]);

json_response(['message' => 'Coupon created']);

