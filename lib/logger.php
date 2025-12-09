<?php
declare(strict_types=1);

function log_message(string $label, string $message): void
{
    $path = getenv('LOG_PATH') ?: __DIR__ . '/../storage/logs/app.log';
    $line = sprintf("[%s] %s: %s%s", date('c'), $label, $message, PHP_EOL);
    @file_put_contents($path, $line, FILE_APPEND);
}

