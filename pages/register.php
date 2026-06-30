<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    redirect(APP_URL . '/index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $email    = sanitize($_POST['email'] ?? '');
    $password = sanitize($_POST['password'] ?? '');
    $phone    = sanitize($_POST['phone'] ?? '');

    $result = registerUser($username, $email, $password, $phone);
    if ($result['status']) {
        $_SESSION['register_success'] = $result['message'];
        redirect(APP_URL . '/pages/login.php');
    } else {
        $error = $result['message'];
    }
}
$pageTitle = 'Daftar Akun Baru';
include __DIR__ . '/../includes/header.php';
?>

<section class="section" style="min-height: 70vh; display: flex; align-items: center;">
    <div class="container" style="max-width: 460px; width: 100%;">
        <div class="topup-card" style="padding: 40px 32px; border-radius: var(--radius); box-shadow: var(--shadow-lg);">
            <div style="text-align: center; margin-bottom: 28px;">
                <span style="font-size: 2.5rem;">🎮</span>
                <h1 style="font-family: 'Montserrat', sans-serif; font-size: 1.6rem; font-weight: 800; margin-top: 12px; margin-bottom: 8px;">Daftar Akun</h1>
                <p style="color: var(--text-muted); font-size: 0.85rem;">Buat akun Anda untuk mulai melacak top-up</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error" style="font-size: 0.85rem; margin-bottom: 20px;"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label" for="username">Username *</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Contoh: gamerid" required autofocus>
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email *</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Contoh: gamer@email.com" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="phone">Nomor HP / WhatsApp</label>
                    <input type="tel" id="phone" name="phone" class="form-control" placeholder="Contoh: 081234567890">
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Password *</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary btn-full btn-lg" style="margin-top: 8px; font-size: 0.95rem;">
                    Daftar Sekarang →
                </button>
            </form>

            <div style="text-align: center; margin-top: 24px; font-size: 0.85rem; color: var(--text-muted);">
                Sudah memiliki akun? <a href="<?= APP_URL ?>/pages/login.php" style="color: var(--cyan); font-weight: 600;">Masuk di Sini</a>
            </div>
        </div>
    </div>
</section>

<script>
// Validasi sederhana agar username tidak mengandung spasi
const usernameInput = document.getElementById('username');
if (usernameInput) {
    usernameInput.addEventListener('input', function() {
        usernameInput.value = usernameInput.value.replace(/\s+/g, '').toLowerCase();
    });
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
