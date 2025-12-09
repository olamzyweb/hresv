<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';
require_method('GET');
require_admin();

$pdo = db();
$stmt = $pdo->query("SELECT r.*, rt.title AS room_type FROM rooms r JOIN room_types rt ON r.room_type_id=rt.id ORDER BY r.id DESC");
$rooms = $stmt->fetchAll();

json_response(['data' => $rooms]);

