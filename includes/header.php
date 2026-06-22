<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' — ' . APP_NAME : APP_NAME . ' | Top-Up Game Terpercaya' ?></title>
    <meta name="description" content="Top-up game terpercaya, cepat, dan murah. Mobile Legends, Free Fire, PUBG, Genshin Impact dan masih banyak lagi.">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .nav-auth-desktop { display: flex; align-items: center; gap: 16px; margin-left: 12px; }
        @media (max-width: 640px) {
            .nav-auth-desktop { display: none !important; }
        }
    </style>
</head>
<body>

<!-- ===== NAVBAR ===== -->
<nav class="navbar">
    <div class="container nav-inner">
        <a href="<?= APP_URL ?>/index.php" class="logo">
            <span class="logo-icon">⚡</span>
            <span class="logo-text"><?= APP_NAME ?></span>
        </a>
        <ul class="nav-links">
            <li><a href="<?= APP_URL ?>/index.php" class="<?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">Beranda</a></li>
            <li><a href="<?= APP_URL ?>/pages/games.php" class="<?= basename($_SERVER['PHP_SELF']) === 'games.php' ? 'active' : '' ?>">Semua Game</a></li>
            <li><a href="<?= APP_URL ?>/pages/order-check.php" class="<?= basename($_SERVER['PHP_SELF']) === 'order-check.php' ? 'active' : '' ?>">Cek Pesanan</a></li>
            <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
                <li><a href="<?= APP_URL ?>/pages/history.php" class="<?= basename($_SERVER['PHP_SELF']) === 'history.php' ? 'active' : '' ?>">Riwayat</a></li>
            <?php endif; ?>
        </ul>
        
        <div class="nav-auth-desktop">
            <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
                <button class="btn-nav-cta" style="background: var(--surface2); color: var(--text); border: 1px solid var(--border);" onclick="location.href='<?= APP_URL ?>/pages/logout.php'">Keluar (<?= htmlspecialchars($_SESSION['username']) ?>)</button>
            <?php else: ?>
                <a href="<?= APP_URL ?>/pages/login.php" style="font-size: 0.85rem; font-weight: 600; color: var(--text-dim); transition: var(--transition);" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-dim)'">Masuk</a>
                <button class="btn-nav-cta" onclick="location.href='<?= APP_URL ?>/pages/register.php'">Daftar</button>
            <?php endif; ?>
        </div>

        <button class="hamburger" id="hamburger" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>
<!-- Mobile Menu -->
<div class="mobile-menu" id="mobileMenu">
    <a href="<?= APP_URL ?>/index.php">Beranda</a>
    <a href="<?= APP_URL ?>/pages/games.php">Semua Game</a>
    <a href="<?= APP_URL ?>/pages/order-check.php">Cek Pesanan</a>
    <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
        <a href="<?= APP_URL ?>/pages/history.php">Riwayat Transaksi</a>
        <a href="<?= APP_URL ?>/pages/logout.php" style="color: var(--red);">Keluar (<?= htmlspecialchars($_SESSION['username']) ?>)</a>
    <?php else: ?>
        <a href="<?= APP_URL ?>/pages/login.php">Masuk</a>
        <a href="<?= APP_URL ?>/pages/register.php" style="color: var(--cyan);">Daftar Akun</a>
    <?php endif; ?>
</div>

