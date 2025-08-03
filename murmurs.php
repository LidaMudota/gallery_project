<?php
// Простейшие струи комментариев

function pull_murmurs(PDO $pdo, int $image_id): array
{
    $stmt = $pdo->prepare("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE image_id = ? ORDER BY comments.created_at ASC");
    $stmt->execute([$image_id]);
    return $stmt->fetchAll();
}

function plant_murmur(PDO $pdo, int $image_id, int $user_id, string $content): void
{
    $stmt = $pdo->prepare("INSERT INTO comments (image_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$image_id, $user_id, $content]);
}
?>