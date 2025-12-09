<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';
require_method('GET');
$user = require_auth();

$id = (int)($_GET['id'] ?? 0);
$pdo = db();
$stmt = $pdo->prepare("SELECT * FROM bookings WHERE id=:id");
$stmt->execute([':id' => $id]);
$booking = $stmt->fetch();

if (!$booking) {
    json_response(['error' => 'Not found'], 404);
}
if ($booking['user_id'] && $booking['user_id'] !== $user['id'] && $user['role'] !== 'admin') {
    json_response(['error' => 'Forbidden'], 403);
}

json_response(['data' => $booking]);

