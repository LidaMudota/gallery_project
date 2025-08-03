<?php
require_once 'config.php';
require_once 'database.php';
require_once 'functions.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $errors[] = 'Заполните все поля';
    } else {
        $pdo = get_db();
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $errors[] = 'Логин уже занят';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
            $stmt->execute([$username, $hash]);
            header('Location: login.php?registered=1');
            exit;
        }
    }
}
?>
<?php include 'templates/header.php'; ?>
<div class="container mt-4">
    <h2>Регистрация</h2>
    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= escape($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form action="register.php" method="post">
        <div class="mb-3">
            <label for="username">Логин:</label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password">Пароль:</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
    </form>
    <p class="mt-3">Уже есть аккаунт? <a href="login.php">Войдите</a></p>
</div>
<?php include 'templates/footer.php'; ?>