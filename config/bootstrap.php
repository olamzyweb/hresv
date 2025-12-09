<?php
declare(strict_types=1);

use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment
if (file_exists(__DIR__ . '/../.env')) {
    Dotenv::createImmutable(dirname(__DIR__))->load();
} elseif (file_exists(__DIR__ . '/../env.example')) {
    // Allow running without .env by copying defaults from env.example
    Dotenv::createImmutable(dirname(__DIR__), 'env.example')->load();
}

// Session configuration
$sessionName = getenv('SESSION_NAME') ?: 'hrest_sid';
session_name($sessionName);
session_set_cookie_params([
    'httponly' => true,
    'secure' => isset($_SERVER['HTTPS']),
    'samesite' => 'Lax',
    'path' => '/',
]);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Create storage paths
$logPath = getenv('LOG_PATH') ?: __DIR__ . '/../storage/logs/app.log';
@mkdir(dirname($logPath), 0775, true);
@mkdir(__DIR__ . '/../storage/uploads', 0775, true);

require_once __DIR__ . '/../lib/response.php';
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/csrf.php';
require_once __DIR__ . '/../lib/validation.php';
require_once __DIR__ . '/../lib/availability.php';
require_once __DIR__ . '/../lib/booking.php';
require_once __DIR__ . '/../lib/payment_stub.php';
require_once __DIR__ . '/../lib/mailer.php';
require_once __DIR__ . '/../lib/logger.php';

