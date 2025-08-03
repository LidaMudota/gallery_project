<?php
require_once 'config.php';
require_once 'database.php';
require_once 'functions.php';

if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];
    $errors = validate_image($file);

    if (!$errors) {
        $filename = uniqid() . '_' . basename($file['name']);
        $targetPath = UPLOAD_DIR . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $pdo = get_db();
            $stmt = $pdo->prepare("INSERT INTO images (filename, uploaded_by, created_at) VALUES (?, ?, NOW())");
            $stmt->execute([$filename, current_user_id()]);
            header("Location: index.php?upload=success");
            exit;
        } else {
            $errors[] = 'Ошибка при загрузке файла.';
        }
    }
}
?>
<?php include 'templates/header.php'; ?>
<div class="container mt-4">
    <h2>Загрузка изображения</h2>
    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= escape($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="image">Выберите изображение:</label>
            <input type="file" name="image" id="image" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Загрузить</button>
    </form>
</div>
<?php include 'templates/footer.php'; ?>