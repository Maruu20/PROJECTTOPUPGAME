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
        </ul>
        <button class="btn-nav-cta" onclick="location.href='<?= APP_URL ?>/pages/games.php'">Top-Up Sekarang</button>
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
</div>
