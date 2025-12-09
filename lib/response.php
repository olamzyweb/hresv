<?php
declare(strict_types=1);

function json_response($data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function require_method(string $method): void
{
    if ($_SERVER['REQUEST_METHOD'] !== strtoupper($method)) {
        json_response(['error' => 'Method not allowed'], 405);
    }
}

