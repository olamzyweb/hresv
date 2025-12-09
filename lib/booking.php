<?php
declare(strict_types=1);

function create_booking(array $payload, ?array $user): array
{
    $pdo = db();
    $pdo->beginTransaction();
    try {
        $checkin = $payload['checkin_date'];
        $checkout = $payload['checkout_date'];
        $nights = (new DateTime($checkin))->diff(new DateTime($checkout))->days;
        if ($nights <= 0) {
            throw new RuntimeException('Invalid dates');
        }

        $available = find_available_rooms($checkin, $checkout, (int)$payload['guests'] ?? 1);
        $room = null;
        foreach ($available as $candidate) {
            if ((int)$candidate['room_id'] === (int)$payload['room_id']) {
                $room = $candidate;
                break;
            }
        }
        if (!$room) {
            throw new RuntimeException('Room not available');
        }

        $rate = (float)$room['base_price'];
        $subtotal = $rate * $nights;
        $discount = 0.0;
        if (!empty($payload['coupon'])) {
            $discount = apply_coupon($payload['coupon'], $subtotal);
        }
        $total = max($subtotal - $discount, 0);

        $bookingRef = strtoupper(bin2hex(random_bytes(4)));
        $stmt = $pdo->prepare("
            INSERT INTO bookings (booking_ref, user_id, guest_name, guest_email, guest_phone, room_id, room_type_id, checkin_date, checkout_date, nights, total_amount, status, payment_status)
            VALUES (:ref, :user_id, :gname, :gemail, :gphone, :room_id, :room_type_id, :checkin, :checkout, :nights, :total, 'pending', 'unpaid')
        ");
        $stmt->execute([
            ':ref' => $bookingRef,
            ':user_id' => $user['id'] ?? null,
            ':gname' => $payload['guest_name'],
            ':gemail' => $payload['guest_email'],
            ':gphone' => $payload['guest_phone'] ?? null,
            ':room_id' => $payload['room_id'],
            ':room_type_id' => $room['room_type_id'],
            ':checkin' => $checkin,
            ':checkout' => $checkout,
            ':nights' => $nights,
            ':total' => $total,
        ]);
        $bookingId = (int)$pdo->lastInsertId();

        $pdo->commit();
        return [
            'id' => $bookingId,
            'booking_ref' => $bookingRef,
            'total' => $total,
            'nights' => $nights,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ];
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function apply_coupon(string $code, float $amount): float
{
    $pdo = db();
    $stmt = $pdo->prepare("SELECT * FROM coupons WHERE code = :code AND (start_date IS NULL OR start_date <= CURDATE()) AND (end_date IS NULL OR end_date >= CURDATE())");
    $stmt->execute([':code' => $code]);
    $coupon = $stmt->fetch();
    if (!$coupon) {
        return 0.0;
    }
    if ($coupon['usage_limit'] !== null && (int)$coupon['usage_limit'] <= 0) {
        return 0.0;
    }
    if ($amount < (float)$coupon['min_amount']) {
        return 0.0;
    }
    if ($coupon['type'] === 'percentage') {
        return $amount * ((float)$coupon['value'] / 100);
    }
    return min((float)$coupon['value'], $amount);
}

