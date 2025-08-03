<?php
require_once 'config.php';
require_once 'database.php';
require_once 'functions.php';
require_once 'murmurs.php';
require_once 'templates/header.php';

$pdo = get_db();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_logged_in()) {
    if (!verify_csrf($_POST['token'] ?? '')) {
        http_response_code(403);
        exit('Неверный CSRF токен');
    }
    $content = trim($_POST['content'] ?? '');
    $image_id = (int)($_POST['image_id'] ?? 0);
    if ($content !== '' && $image_id > 0) {
        plant_murmur($pdo, $image_id, current_user_id(), $content);
    }
    header('Location: index.php');
    exit;
    }

$images = $pdo->query("SELECT images.*, users.username FROM images JOIN users ON images.uploaded_by = users.id ORDER BY images.created_at DESC")->fetchAll();
?>
<h1>Галерея изображений</h1>
<div class="cards-wrapper">
<?php foreach ($images as $img): ?>
    <div class="card">
        <img src="uploads/<?= escape($img['filename']) ?>" class="card-img-top">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <p class="card-text mb-0">Автор: <?= escape($img['username']) ?></p>
                <?php if (is_logged_in() && $img['uploaded_by'] == current_user_id()): ?>
                    <a href="delete_image.php?id=<?= $img['id'] ?>&token=<?= csrf_token() ?>" class="btn btn-sm btn-danger delete-confirm">Удалить</a>
                <?php endif; ?>
            </div>
            <?php
            $comments = pull_murmurs($pdo, $img['id']);
            ?>
            <div class="comments mb-2">
                <?php foreach ($comments as $comment): ?>
                    <div class="mb-2">
                          <div>
                              <strong><?= escape($comment['username']) ?></strong>
                                <span class="text-muted small ms-1"><?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?></span>
                              <?php if (is_logged_in() && $comment['user_id'] == current_user_id()): ?>
                                  <a href="delete_comment.php?id=<?= $comment['id'] ?>&token=<?= csrf_token() ?>" class="text-danger ms-2 delete-confirm">×</a>
                              <?php endif; ?>
                          </div>
                          <div><?= escape($comment['content']) ?></div>
                      </div>
                  <?php endforeach; ?>
              </div>
            <?php if (is_logged_in()): ?>
                <form action="index.php" method="post">
                    <div class="mb-2">
                        <textarea name="content" class="form-control" rows="2" required></textarea>
                        <input type="hidden" name="image_id" value="<?= $img['id'] ?>">
                        <input type="hidden" name="token" value="<?= csrf_token() ?>">
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary">Комментировать</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>
</div>
<?php require 'templates/footer.php'; ?>