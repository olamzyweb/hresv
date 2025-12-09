<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
require_method('GET');

$checkin = sanitize_string($_GET['checkin'] ?? '');
$checkout = sanitize_string($_GET['checkout'] ?? '');
$guests = (int)($_GET['guests'] ?? 1);

if (!$checkin || !$checkout) {
    json_response(['error' => 'checkin and checkout required'], 422);
}

$rooms = find_available_rooms($checkin, $checkout, $guests);
json_response(['data' => $rooms]);

