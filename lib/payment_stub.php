<?php
declare(strict_types=1);

function start_payment(int $bookingId, float $amount, string $currency = 'USD'): array
{
    // Simulate Stripe-style client secret
    return [
        'provider' => getenv('PAYMENT_PROVIDER') ?: 'stripe_test',
        'client_secret' => 'pi_' . bin2hex(random_bytes(8)),
        'booking_id' => $bookingId,
        'amount' => $amount,
        'currency' => $currency,
        'status' => 'requires_confirmation',
    ];
}

function mark_payment_success(int $bookingId, string $providerPaymentId): void
{
    $pdo = db();
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("UPDATE bookings SET payment_status='paid', status='confirmed', updated_at=NOW() WHERE id=:id");
    $stmt->execute([':id' => $bookingId]);
    $log = $pdo->prepare("INSERT INTO payments (booking_id, provider, provider_payment_id, amount, status) SELECT :bid, 'stripe_test', :ppid, total_amount, 'success' FROM bookings WHERE id=:bid");
    $log->execute([':bid' => $bookingId, ':ppid' => $providerPaymentId]);
    $pdo->commit();
}

