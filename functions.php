<?php
session_start();

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

function current_user_id(): ?int
{
    return $_SESSION['user_id'] ?? null;
}

function escape(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function validate_image(array $file): array
{
    $errors = [];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Ошибка загрузки файла.';
        return $errors;
    }

    if ($file['size'] > MAX_FILE_SIZE) {
        $errors[] = 'Файл слишком большой.';
    }

    $type = mime_content_type($file['tmp_name']);
    if (!in_array($type, ALLOWED_TYPES, true)) {
        $errors[] = 'Недопустимый формат файла.';
    }

    return $errors;
}
?>