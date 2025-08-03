<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Галерея</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">Галерея</a>
        <div>
            <?php if (is_logged_in()): ?>
                <a href="upload.php" class="btn btn-outline-light me-2">Загрузить</a>
                <a href="logout.php" class="btn btn-outline-light">Выйти</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline-light me-2">Вход</a>
                <a href="register.php" class="btn btn-outline-light">Регистрация</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<div class="container">