<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
json_response(['token' => csrf_token()]);

