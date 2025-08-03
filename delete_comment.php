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
$stmt = $pdo->prepare('SELECT user_id FROM comments WHERE id = ?');
$stmt->execute([$id]);
$comment = $stmt->fetch();

if ($comment && $comment['user_id'] == current_user_id()) {
    $pdo->prepare('DELETE FROM comments WHERE id = ?')->execute([$id]);
}

header('Location: index.php');
exit;
?>