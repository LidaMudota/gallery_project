<?php
require_once 'config.php';
require_once 'database.php';
require_once 'functions.php';

if (is_logged_in()) {
    header("Location: index.php");
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['token'] ?? '')) {
        $errors[] = 'Неверный CSRF токен';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $errors[] = 'Заполните все поля';
        } else {
            $pdo = get_db();
            $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header("Location: index.php");
                exit;
            } else {
                $errors[] = 'Неверный логин или пароль';
            }
        }
    }
}
?>
<?php include 'templates/header.php'; ?>
<div class="container mt-4">
    <h2>Вход</h2>
    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= escape($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form action="login.php" method="post">
        <div class="mb-3">
            <label for="username">Логин:</label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password">Пароль:</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <input type="hidden" name="token" value="<?= csrf_token() ?>">
        <button type="submit" class="btn btn-primary">Войти</button>
    </form>
    <p class="mt-3">Нет аккаунта? <a href="register.php">Зарегистрируйтесь</a></p>
</div>
<?php include 'templates/footer.php'; ?>