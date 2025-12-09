<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';
require_method('POST');
verify_csrf();

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$errors = require_fields($input, ['room_id', 'guest_name', 'guest_email', 'checkin_date', 'checkout_date']);
if (!validate_email($input['guest_email'] ?? '')) {
    $errors['guest_email'] = 'Invalid email';
}
if ($errors) {
    json_response(['errors' => $errors], 422);
}

try {
    $booking = create_booking($input, current_user());
    send_email($input['guest_email'], 'Booking confirmation', "<p>Your booking ref: {$booking['booking_ref']}</p>");
    json_response(['data' => $booking]);
} catch (Throwable $e) {
    json_response(['error' => $e->getMessage()], 400);
}

