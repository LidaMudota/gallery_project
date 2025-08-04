<?php
require_once 'config.php';
require_once 'database.php';
require_once 'functions.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

if (!verify_csrf($_GET['token'] ?? '')) {
    header('Location: index.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);
$pdo = get_db();
$stmt = $pdo->prepare('SELECT filename, uploaded_by FROM images WHERE id = ?');
$stmt->execute([$id]);
$image = $stmt->fetch();

if ($image && $image['uploaded_by'] == current_user_id()) {
    $pdo->prepare('DELETE FROM comments WHERE image_id = ?')->execute([$id]);
    $pdo->prepare('DELETE FROM images WHERE id = ?')->execute([$id]);
    $path = UPLOAD_DIR . $image['filename'];
    if (is_file($path)) {
        unlink($path);
    }
}

header('Location: index.php');
exit;
?>