<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    redirect(APP_URL . '/index.php');
}

$error = '';
$success = '';

if (isset($_SESSION['register_success'])) {
    $success = $_SESSION['register_success'];
    unset($_SESSION['register_success']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = sanitize($_POST['password'] ?? '');

    $result = loginUser($username, $password);
    if ($result['status']) {
        redirect(APP_URL . '/index.php');
    } else {
        $error = $result['message'];
    }
}
$pageTitle = 'Masuk Akun';
include __DIR__ . '/../includes/header.php';
?>

<section class="section" style="min-height: 70vh; display: flex; align-items: center;">
    <div class="container" style="max-width: 440px; width: 100%;">
        <div class="topup-card" style="padding: 40px 32px; border-radius: var(--radius); box-shadow: var(--shadow-lg);">
            <div style="text-align: center; margin-bottom: 28px;">
                <span style="font-size: 2.5rem;">⚡</span>
                <h1 style="font-family: 'Montserrat', sans-serif; font-size: 1.6rem; font-weight: 800; margin-top: 12px; margin-bottom: 8px;">Masuk Akun</h1>
                <p style="color: var(--text-muted); font-size: 0.85rem;">Akses riwayat transaksi dan kemudahan top-up</p>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success" style="font-size: 0.85rem; margin-bottom: 20px;"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error" style="font-size: 0.85rem; margin-bottom: 20px;"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label" for="username">Username atau Email</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username/email" required autofocus>
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary btn-full btn-lg" style="margin-top: 8px; font-size: 0.95rem;">
                    Masuk Akun →
                </button>
            </form>

            <div style="text-align: center; margin-top: 24px; font-size: 0.85rem; color: var(--text-muted);">
                Belum punya akun? <a href="<?= APP_URL ?>/pages/register.php" style="color: var(--cyan); font-weight: 600;">Daftar Sekarang</a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
