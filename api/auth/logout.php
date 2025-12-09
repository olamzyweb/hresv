<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';
require_method('POST');
verify_csrf();

logout_user();
json_response(['message' => 'Logged out']);

