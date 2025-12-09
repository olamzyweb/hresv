<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';
require_method('POST');
verify_csrf();

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$bookingId = (int)($input['booking_id'] ?? 0);

$pdo = db();
$stmt = $pdo->prepare("SELECT id, total_amount, payment_status FROM bookings WHERE id=:id");
$stmt->execute([':id' => $bookingId]);
$booking = $stmt->fetch();
if (!$booking) {
    json_response(['error' => 'Booking not found'], 404);
}
if ($booking['payment_status'] === 'paid') {
    json_response(['message' => 'Already paid']);
}

$payment = start_payment($bookingId, (float)$booking['total_amount']);
json_response(['data' => $payment]);

