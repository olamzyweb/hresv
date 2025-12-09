<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../config/bootstrap.php';
require_method('POST');
verify_csrf();
$admin = require_admin();

$id = (int)($_GET['id'] ?? 0);
$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$status = sanitize_string($input['status'] ?? '');

$allowed = ['pending','confirmed','checked_in','checked_out','cancelled'];
if (!in_array($status, $allowed, true)) {
    json_response(['error' => 'Invalid status'], 422);
}

$pdo = db();
$pdo->prepare("UPDATE bookings SET status=:status, updated_at=NOW() WHERE id=:id")->execute([':status' => $status, ':id' => $id]);
$pdo->prepare("INSERT INTO audit_logs (admin_user_id, action, meta) VALUES (:uid, 'booking_status_change', JSON_OBJECT('booking_id', :bid, 'status', :status))")->execute([
    ':uid' => $admin['id'],
    ':bid' => $id,
    ':status' => $status,
]);

json_response(['message' => 'updated']);

