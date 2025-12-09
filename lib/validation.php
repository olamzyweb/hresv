<?php
declare(strict_types=1);

function sanitize_string(?string $value): string
{
    return trim(filter_var($value ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
}

function validate_email(string $email): bool
{
    return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validate_password(string $password): bool
{
    return strlen($password) >= 8;
}

function require_fields(array $data, array $fields): array
{
    $errors = [];
    foreach ($fields as $field) {
        if (!isset($data[$field]) || $data[$field] === '') {
            $errors[$field] = 'Required';
        }
    }
    return $errors;
}

