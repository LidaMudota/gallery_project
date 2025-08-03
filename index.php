<?php
require_once 'config.php';
require_once 'database.php';
require_once 'functions.php';
require_once 'templates/header.php';

$pdo = get_db();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_logged_in()) {
    $content = trim($_POST['content'] ?? '');
    $image_id = (int)($_POST['image_id'] ?? 0);
    if ($content !== '' && $image_id > 0) {
        $stmt = $pdo->prepare("INSERT INTO comments (image_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$image_id, current_user_id(), $content]);
    }
    header('Location: index.php');
    exit;
}

$images = $pdo->query("SELECT images.*, users.username FROM images JOIN users ON images.uploaded_by = users.id ORDER BY images.created_at DESC")->fetchAll();
?>
<h1>Галерея изображений</h1>
<div class="row">
<?php foreach ($images as $img): ?>
    <div class="col-md-4 mb-4">
        <div class="card">
            <img src="uploads/<?= escape($img['filename']) ?>" class="card-img-top">
            <div class="card-body">
                <p class="card-text">Автор: <?= escape($img['username']) ?></p>
                <?php if (is_logged_in() && $img['uploaded_by'] == current_user_id()): ?>
                    <a href="delete_image.php?id=<?= $img['id'] ?>" class="btn btn-sm btn-danger delete-confirm">Удалить</a>
                <?php endif; ?>
            </div>
            <div class="card-body border-top">
                <?php
                    $cstmt = $pdo->prepare("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE image_id = ? ORDER BY comments.created_at ASC");
                    $cstmt->execute([$img['id']]);
                    $comments = $cstmt->fetchAll();
                ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="mb-2">
                        <strong><?= escape($comment['username']) ?>:</strong>
                        <?= escape($comment['content']) ?>
                        <?php if (is_logged_in() && $comment['user_id'] == current_user_id()): ?>
                            <a href="delete_comment.php?id=<?= $comment['id'] ?>" class="text-danger ms-2 delete-confirm">×</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <?php if (is_logged_in()): ?>
                    <form action="index.php" method="post">
                        <div class="mb-2">
                            <textarea name="content" class="form-control" rows="2" required></textarea>
                            <input type="hidden" name="image_id" value="<?= $img['id'] ?>">
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">Комментировать</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>
<?php require 'templates/footer.php'; ?>