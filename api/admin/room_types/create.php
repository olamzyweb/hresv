<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../config/bootstrap.php';
require_method('POST');
verify_csrf();
require_admin();

$input = $_POST ?: json_decode(file_get_contents('php://input'), true);
$errors = require_fields($input, ['code', 'title', 'capacity', 'base_price']);
if ($errors) {
    json_response(['errors' => $errors], 422);
}

$pdo = db();
$stmt = $pdo->prepare("INSERT INTO room_types (code, title, description, capacity, base_price) VALUES (:code, :title, :desc, :capacity, :price)");
$stmt->execute([
    ':code' => sanitize_string($input['code']),
    ':title' => sanitize_string($input['title']),
    ':desc' => sanitize_string($input['description'] ?? ''),
    ':capacity' => (int)$input['capacity'],
    ':price' => (float)$input['base_price'],
]);

json_response(['message' => 'Room type created']);

