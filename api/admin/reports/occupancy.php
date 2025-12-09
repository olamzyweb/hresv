<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../config/bootstrap.php';
require_method('GET');
require_admin();

$from = sanitize_string($_GET['from'] ?? '');
$to = sanitize_string($_GET['to'] ?? '');
if (!$from || !$to) {
    json_response(['error' => 'from/to required'], 422);
}

$pdo = db();
$stmt = $pdo->prepare("
    SELECT
      COUNT(*) AS bookings,
      SUM(total_amount) AS revenue,
      SUM(nights) AS room_nights
    FROM bookings
    WHERE checkin_date >= :from AND checkout_date <= :to AND status IN ('confirmed','checked_in','checked_out')
");
$stmt->execute([':from' => $from, ':to' => $to]);
$row = $stmt->fetch();

$roomsCount = (int)$pdo->query("SELECT COUNT(*) FROM rooms")->fetchColumn();
$days = (new DateTime($from))->diff(new DateTime($to))->days ?: 1;
$occupancyRate = $roomsCount ? (($row['room_nights'] ?? 0) / ($roomsCount * $days)) * 100 : 0;
$adr = ($row['revenue'] ?? 0) / max($row['room_nights'] ?? 1, 1);
$revpar = ($row['revenue'] ?? 0) / max($roomsCount * $days, 1);

json_response([
    'data' => [
        'bookings' => (int)$row['bookings'],
        'revenue' => (float)$row['revenue'],
        'room_nights' => (int)$row['room_nights'],
        'occupancy_rate' => round($occupancyRate, 2),
        'adr' => round($adr, 2),
        'revpar' => round($revpar, 2),
    ]
]);

