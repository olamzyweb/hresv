<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../config/bootstrap.php';
require_method('POST');
verify_csrf();
require_admin();

$input = $_POST ?: json_decode(file_get_contents('php://input'), true);
$roomNumber = sanitize_string($input['room_number'] ?? '');
$roomTypeId = (int)($input['room_type_id'] ?? 0);

$errors = require_fields($input, ['room_number', 'room_type_id']);
if ($errors) {
    json_response(['errors' => $errors], 422);
}

$pdo = db();
$stmt = $pdo->prepare("INSERT INTO rooms (room_number, room_type_id, status, floor) VALUES (:num, :type, 'available', :floor)");
$stmt->execute([
    ':num' => $roomNumber,
    ':type' => $roomTypeId,
    ':floor' => sanitize_string($input['floor'] ?? ''),
]);

json_response(['message' => 'Room created']);

