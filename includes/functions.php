<?php
require_once __DIR__ . '/config.php';

// =============================================
// FORMAT RUPIAH
// =============================================
function formatRupiah($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

// =============================================
// GENERATE ORDER ID
// =============================================
function generateOrderId() {
    return 'ORD-' . strtoupper(uniqid()) . '-' . rand(100, 999);
}

// =============================================
// SANITIZE INPUT
// =============================================
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// =============================================
// REDIRECT
// =============================================
function redirect($url) {
    header("Location: $url");
    exit();
}

// =============================================
// DATA GAME (DEMO - Biasanya dari database)
// =============================================
function getGames() {
    return [
        [
            'id'       => 1,
            'slug'     => 'mobile-legends',
            'name'     => 'Mobile Legends',
            'category' => 'MOBA',
            'icon'     => '🗡️',
            'color'    => '#1a73e8',
            'popular'  => true,
        ],
        [
            'id'       => 2,
            'slug'     => 'free-fire',
            'name'     => 'Free Fire',
            'category' => 'Battle Royale',
            'icon'     => '🔥',
            'color'    => '#f4a018',
            'popular'  => true,
        ],
        [
            'id'       => 3,
            'slug'     => 'pubg-mobile',
            'name'     => 'PUBG Mobile',
            'category' => 'Battle Royale',
            'icon'     => '🎯',
            'color'    => '#c9a227',
            'popular'  => true,
        ],
        [
            'id'       => 4,
            'slug'     => 'genshin-impact',
            'name'     => 'Genshin Impact',
            'category' => 'RPG',
            'icon'     => '✨',
            'color'    => '#6b5de4',
            'popular'  => true,
        ],
        [
            'id'       => 5,
            'slug'     => 'valorant',
            'name'     => 'Valorant',
            'category' => 'FPS',
            'icon'     => '⚡',
            'color'    => '#ff4655',
            'popular'  => false,
        ],
        [
            'id'       => 6,
            'slug'     => 'honkai-star-rail',
            'name'     => 'Honkai Star Rail',
            'category' => 'RPG',
            'icon'     => '🌟',
            'color'    => '#7c3aed',
            'popular'  => false,
        ],
    ];
}

function getGameBySlug($slug) {
    foreach (getGames() as $game) {
        if ($game['slug'] === $slug) return $game;
    }
    return null;
}

// =============================================
// DATA PRODUK TOP-UP PER GAME
// =============================================
function getProducts($gameSlug) {
    $products = [
        'mobile-legends' => [
            ['id' => 1, 'name' => '86 Diamonds', 'amount' => 86, 'price' => 19000, 'bonus' => 0],
            ['id' => 2, 'name' => '172 Diamonds', 'amount' => 172, 'price' => 38000, 'bonus' => 0],
            ['id' => 3, 'name' => '257 Diamonds', 'amount' => 257, 'price' => 57000, 'bonus' => 5],
            ['id' => 4, 'name' => '344 Diamonds', 'amount' => 344, 'price' => 76000, 'bonus' => 0],
            ['id' => 5, 'name' => '514 Diamonds', 'amount' => 514, 'price' => 114000, 'bonus' => 10],
            ['id' => 6, 'name' => '706 Diamonds', 'amount' => 706, 'price' => 152000, 'bonus' => 0],
            ['id' => 7, 'name' => '1412 Diamonds', 'amount' => 1412, 'price' => 304000, 'bonus' => 20],
            ['id' => 8, 'name' => '2195 Diamonds', 'amount' => 2195, 'price' => 456000, 'bonus' => 50],
        ],
        'free-fire' => [
            ['id' => 1, 'name' => '70 Diamonds', 'amount' => 70, 'price' => 15000, 'bonus' => 0],
            ['id' => 2, 'name' => '140 Diamonds', 'amount' => 140, 'price' => 29000, 'bonus' => 0],
            ['id' => 3, 'name' => '355 Diamonds', 'amount' => 355, 'price' => 69000, 'bonus' => 5],
            ['id' => 4, 'name' => '720 Diamonds', 'amount' => 720, 'price' => 139000, 'bonus' => 10],
            ['id' => 5, 'name' => '1450 Diamonds', 'amount' => 1450, 'price' => 279000, 'bonus' => 20],
            ['id' => 6, 'name' => '2180 Diamonds', 'amount' => 2180, 'price' => 419000, 'bonus' => 30],
        ],
        'pubg-mobile' => [
            ['id' => 1, 'name' => '60 UC', 'amount' => 60, 'price' => 16000, 'bonus' => 0],
            ['id' => 2, 'name' => '325 UC', 'amount' => 325, 'price' => 80000, 'bonus' => 0],
            ['id' => 3, 'name' => '660 UC', 'amount' => 660, 'price' => 159000, 'bonus' => 0],
            ['id' => 4, 'name' => '1800 UC', 'amount' => 1800, 'price' => 399000, 'bonus' => 0],
            ['id' => 5, 'name' => '3850 UC', 'amount' => 3850, 'price' => 799000, 'bonus' => 50],
            ['id' => 6, 'name' => '8100 UC', 'amount' => 8100, 'price' => 1599000, 'bonus' => 100],
        ],
        'genshin-impact' => [
            ['id' => 1, 'name' => '60 Primogems', 'amount' => 60, 'price' => 15000, 'bonus' => 0],
            ['id' => 2, 'name' => '300 Primogems', 'amount' => 300, 'price' => 75000, 'bonus' => 30],
            ['id' => 3, 'name' => '980 Primogems', 'amount' => 980, 'price' => 229000, 'bonus' => 110],
            ['id' => 4, 'name' => '1980 Primogems', 'amount' => 1980, 'price' => 449000, 'bonus' => 260],
            ['id' => 5, 'name' => '3280 Primogems', 'amount' => 3280, 'price' => 699000, 'bonus' => 600],
            ['id' => 6, 'name' => '6480 Primogems', 'amount' => 6480, 'price' => 1349000, 'bonus' => 1600],
        ],
        'valorant' => [
            ['id' => 1, 'name' => '475 VP', 'amount' => 475, 'price' => 75000, 'bonus' => 0],
            ['id' => 2, 'name' => '1000 VP', 'amount' => 1000, 'price' => 149000, 'bonus' => 0],
            ['id' => 3, 'name' => '2050 VP', 'amount' => 2050, 'price' => 299000, 'bonus' => 0],
            ['id' => 4, 'name' => '3650 VP', 'amount' => 3650, 'price' => 499000, 'bonus' => 0],
            ['id' => 5, 'name' => '5350 VP', 'amount' => 5350, 'price' => 699000, 'bonus' => 0],
        ],
        'honkai-star-rail' => [
            ['id' => 1, 'name' => '60 Oneiric Shards', 'amount' => 60, 'price' => 15000, 'bonus' => 0],
            ['id' => 2, 'name' => '300 Oneiric Shards', 'amount' => 300, 'price' => 75000, 'bonus' => 30],
            ['id' => 3, 'name' => '980 Oneiric Shards', 'amount' => 980, 'price' => 229000, 'bonus' => 110],
            ['id' => 4, 'name' => '1980 Oneiric Shards', 'amount' => 1980, 'price' => 449000, 'bonus' => 260],
        ],
    ];
    return $products[$gameSlug] ?? [];
}

// =============================================
// METODE PEMBAYARAN
// =============================================
function getPaymentMethods() {
    return [
        ['id' => 'transfer_bank', 'name' => 'Transfer Bank', 'icon' => '🏦',
         'options' => ['BCA', 'BNI', 'BRI', 'Mandiri']],
        ['id' => 'ewallet', 'name' => 'E-Wallet', 'icon' => '💳',
         'options' => ['GoPay', 'OVO', 'DANA', 'ShopeePay']],
        ['id' => 'pulsa', 'name' => 'Pulsa', 'icon' => '📱',
         'options' => ['Telkomsel', 'Indosat', 'XL']],
        ['id' => 'qris', 'name' => 'QRIS', 'icon' => '📲',
         'options' => ['QRIS All Payment']],
    ];
}
