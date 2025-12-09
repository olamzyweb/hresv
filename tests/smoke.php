<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

function assert_true($cond, $msg) {
    if (!$cond) {
        throw new RuntimeException("Assertion failed: {$msg}");
    }
}

// CSRF token generation
$t1 = csrf_token();
$t2 = csrf_token();
assert_true($t1 === $t2 && strlen($t1) === 64, 'CSRF stable in session');

// Validation helpers
assert_true(validate_email('user@example.com'), 'valid email');
assert_true(!validate_email('bad-email'), 'invalid email');
assert_true(validate_password('longenough'), 'password length');

// Availability calculation baseline
$rooms = find_available_rooms(date('Y-m-d'), date('Y-m-d', strtotime('+1 day')), 1);
assert_true(is_array($rooms), 'rooms returns array');

echo "Smoke tests passed\n";

