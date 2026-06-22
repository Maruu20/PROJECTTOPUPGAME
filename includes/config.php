<?php
// =============================================
// KONFIGURASI DATABASE
// =============================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'topup_game_db');

// =============================================
// KONFIGURASI APLIKASI
// =============================================
define('APP_NAME', 'GameTop');
define('APP_URL', 'http://localhost/topup-game');
define('APP_VERSION', '1.0.0');

// =============================================
// TIMEZONE
// =============================================
date_default_timezone_set('Asia/Jakarta');

// =============================================
// SESSION
// =============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =============================================
// KREDENSIAL ADMIN
// =============================================
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123');

