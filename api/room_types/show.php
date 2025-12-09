<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';
require_method('GET');

$id = (int)($_GET['id'] ?? 0);
$pdo = db();
$stmt = $pdo->prepare("SELECT * FROM room_types WHERE id=:id");
$stmt->execute([':id' => $id]);
$type = $stmt->fetch();
if (!$type) {
    json_response(['error' => 'Not found'], 404);
}
$imgs = $pdo->prepare("SELECT filename FROM room_images WHERE room_type_id=:id ORDER BY ordinal");
$imgs->execute([':id' => $id]);
$type['images'] = $imgs->fetchAll(PDO::FETCH_COLUMN);

json_response(['data' => $type]);

