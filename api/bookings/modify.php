<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';
require_method('POST');
verify_csrf();
$user = require_auth();

$id = (int)($_GET['id'] ?? 0);
$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$checkin = sanitize_string($input['checkin_date'] ?? '');
$checkout = sanitize_string($input['checkout_date'] ?? '');

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

$available = find_available_rooms($checkin, $checkout, 1);
$roomOk = array_filter($available, fn($r) => (int)$r['room_id'] === (int)$booking['room_id']);
if (!$roomOk) {
    json_response(['error' => 'Room unavailable for new dates'], 409);
}

$nights = (new DateTime($checkin))->diff(new DateTime($checkout))->days;
$newTotal = $nights * (float)$booking['total_amount'] / max($booking['nights'], 1);
$pdo->prepare("UPDATE bookings SET checkin_date=:ci, checkout_date=:co, nights=:n, total_amount=:t, updated_at=NOW() WHERE id=:id")->execute([
    ':ci' => $checkin,
    ':co' => $checkout,
    ':n' => $nights,
    ':t' => $newTotal,
    ':id' => $id,
]);

json_response(['message' => 'Booking updated']);

