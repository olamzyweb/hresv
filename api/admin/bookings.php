<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';
require_method('GET');
require_admin();

$pdo = db();
$status = sanitize_string($_GET['status'] ?? '');
$where = '';
$params = [];
if ($status) {
    $where = 'WHERE b.status = :status';
    $params[':status'] = $status;
}
$stmt = $pdo->prepare("SELECT b.*, r.room_number, rt.title AS room_type FROM bookings b JOIN rooms r ON b.room_id=r.id JOIN room_types rt ON b.room_type_id=rt.id {$where} ORDER BY b.created_at DESC LIMIT 200");
$stmt->execute($params);
$bookings = $stmt->fetchAll();

json_response(['data' => $bookings]);

