<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    redirect(APP_URL . '/admin/login.php');
}

$activePage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($adminTitle) ? $adminTitle . ' | ' . APP_NAME . ' Admin' : APP_NAME . ' Panel Admin' ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/admin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="admin-body">

    <!-- ===== SIDEBAR ===== -->
    <aside class="admin-sidebar">
        <div class="admin-logo">
            <span class="logo-icon">⚡</span>
            <span class="logo-text"><?= APP_NAME ?> Admin</span>
        </div>
        <ul class="admin-nav">
            <li class="admin-nav-item <?= $activePage === 'index.php' ? 'active' : '' ?>">
                <a href="<?= APP_URL ?>/admin/index.php">
                    <span>📊</span>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="admin-nav-item <?= $activePage === 'games.php' ? 'active' : '' ?>">
                <a href="<?= APP_URL ?>/admin/games.php">
                    <span>🎮</span>
                    <span>Kelola Game</span>
                </a>
            </li>
            <li class="admin-nav-item <?= $activePage === 'products.php' ? 'active' : '' ?>">
                <a href="<?= APP_URL ?>/admin/products.php">
                    <span>💎</span>
                    <span>Kelola Produk</span>
                </a>
            </li>
            <li class="admin-nav-item <?= $activePage === 'orders.php' ? 'active' : '' ?>">
                <a href="<?= APP_URL ?>/admin/orders.php">
                    <span>📋</span>
                    <span>Kelola Pesanan</span>
                </a>
            </li>
            <li class="admin-nav-item">
                <a href="<?= APP_URL ?>/index.php" target="_blank">
                    <span>🌐</span>
                    <span>Lihat Website</span>
                </a>
            </li>
        </ul>
        <div class="admin-sidebar-footer">
            <ul class="admin-nav" style="padding: 0;">
                <li class="admin-nav-item" style="border-top: 1px solid var(--border-color); padding-top: 12px;">
                    <a href="<?= APP_URL ?>/admin/logout.php" style="color: var(--red);">
                        <span>🚪</span>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <!-- ===== MAIN CONTENT AREA ===== -->
    <main class="admin-content">
        <!-- Top bar -->
        <header class="admin-header">
            <div>
                <span class="label">Panel Kontrol</span>
                <h1><?= isset($adminTitle) ? htmlspecialchars($adminTitle) : 'Selamat Datang' ?></h1>
            </div>
            <div class="admin-profile">
                <div class="admin-profile-name">Administrator</div>
                <div class="admin-profile-avatar">🛡️</div>
            </div>
        </header>
