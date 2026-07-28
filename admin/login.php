<?php
require_once __DIR__ . '/../includes/auth.php';

auth_start();
if (auth_check()) {
    redirect('index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $username = trim($_POST['username'] ?? '');
    $password = (string) ($_POST['password'] ?? '');
    try {
        if (auth_attempt($username, $password)) {
            redirect('index.php');
        }
        $error = 'Invalid username or password.';
    } catch (Throwable $e) {
        $error = 'Database connection failed. Check config/database.php and run install.php.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Naseeb Consultant</title>
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body class="login-page">
    <div class="login-card">
        <h1>CMS Dashboard</h1>
        <p class="sub">Sign in to edit website text and images.</p>
        <?php if ($error): ?>
            <div class="flash err"><?= e($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <?= csrf_field() ?>
            <label for="username">Username</label>
            <input id="username" name="username" type="text" required autofocus autocomplete="username">

            <label for="password">Password</label>
            <input id="password" name="password" type="password" required autocomplete="current-password">

            <div class="actions">
                <button class="btn btn-gold" type="submit" style="width:100%;">Sign In</button>
            </div>
        </form>
    </div>
</body>
</html>
