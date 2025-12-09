<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';
require_method('POST');

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$bookingId = (int)($input['booking_id'] ?? 0);
$status = $input['status'] ?? '';
$providerId = $input['provider_payment_id'] ?? ('pi_' . bin2hex(random_bytes(4)));

if ($status === 'succeeded') {
    mark_payment_success($bookingId, $providerId);
    json_response(['message' => 'ok']);
}

json_response(['error' => 'unsupported status'], 400);

