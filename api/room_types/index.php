<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';
require_method('GET');

$pdo = db();
$stmt = $pdo->query("SELECT rt.*, (SELECT JSON_ARRAYAGG(filename) FROM room_images WHERE room_type_id=rt.id) AS images FROM room_types rt ORDER BY rt.id DESC");
$types = $stmt->fetchAll();

json_response(['data' => $types]);

