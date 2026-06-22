<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Jika sudah login, langsung ke dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    redirect(APP_URL . '/admin/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = sanitize($_POST['password'] ?? '');

    if ($username === ADMIN_USER && $password === ADMIN_PASS) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $username;
        redirect(APP_URL . '/admin/index.php');
    } else {
        $error = 'Username atau Password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/admin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body class="admin-body">
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <span style="font-size: 2.5rem; display: block; margin-bottom: 8px;">⚡</span>
                <h1><?= APP_NAME ?> Admin</h1>
                <p class="text-muted" style="font-size: 0.85rem;">Silakan masuk ke panel kontrol admin</p>
            </div>

            <?php if ($error): ?>
                <div class="admin-alert admin-alert-error">
                    <span>❌</span> <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="admin-form-group">
                    <label class="admin-form-label" for="username">Username</label>
                    <input type="text" id="username" name="username" class="admin-form-control" placeholder="admin" required autofocus>
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="admin-form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn-admin btn-admin-primary" style="width: 100%; margin-top: 10px; padding: 12px;">
                    Masuk Panel →
                </button>
            </form>
            <div style="text-align: center; margin-top: 20px;">
                <a href="<?= APP_URL ?>/index.php" style="font-size: 0.8rem; color: var(--neon-cyan); text-decoration: none;">
                    ← Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</body>
</html>
