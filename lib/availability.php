<?php
declare(strict_types=1);

function find_available_rooms(string $checkin, string $checkout, int $guests): array
{
    $pdo = db();
    $sql = "
        SELECT r.id AS room_id, rt.id AS room_type_id, rt.title, rt.capacity, rt.base_price, rt.description
        FROM rooms r
        JOIN room_types rt ON r.room_type_id = rt.id
        WHERE rt.capacity >= :guests
          AND r.status = 'available'
          AND r.id NOT IN (
            SELECT b.room_id FROM bookings b
            WHERE NOT (b.checkout_date <= :checkin OR b.checkin_date >= :checkout)
            AND b.status IN ('pending','confirmed','checked_in')
          )
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':guests' => $guests,
        ':checkin' => $checkin,
        ':checkout' => $checkout,
    ]);
    return $stmt->fetchAll();
}

