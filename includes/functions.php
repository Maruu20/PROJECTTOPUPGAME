<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';

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
// DATA GAME (DENGAN INTEGRASI DB & FALLBACK)
// =============================================
function getGames($onlyActive = true) {
    $db = getDB();
    if ($db) {
        $whereClause = $onlyActive ? "WHERE is_active = 1" : "";
        $result = $db->query("SELECT * FROM games $whereClause ORDER BY sort_order ASC, name ASC");
        if ($result && $result->num_rows > 0) {
            $games = [];
            while ($row = $result->fetch_assoc()) {
                $games[] = [
                    'id'         => (int)$row['id'],
                    'slug'       => $row['slug'],
                    'name'       => $row['name'],
                    'category'   => $row['category'],
                    'icon'       => $row['icon'],
                    'color'      => $row['color'],
                    'popular'    => (bool)$row['is_popular'],
                    'is_active'  => (bool)$row['is_active'],
                    'sort_order' => (int)$row['sort_order'],
                ];
            }
            return $games;
        }
    }
    // Fallback static array jika DB tidak terhubung atau kosong
    $staticGames = [
        [
            'id'       => 1,
            'slug'     => 'mobile-legends',
            'name'     => 'Mobile Legends',
            'category' => 'MOBA',
            'icon'     => '🗡️',
            'color'    => '#1a73e8',
            'popular'  => true,
            'is_active'=> true,
            'sort_order'=> 1,
        ],
        [
            'id'       => 2,
            'slug'     => 'free-fire',
            'name'     => 'Free Fire',
            'category' => 'Battle Royale',
            'icon'     => '🔥',
            'color'    => '#f4a018',
            'popular'  => true,
            'is_active'=> true,
            'sort_order'=> 2,
        ],
        [
            'id'       => 3,
            'slug'     => 'pubg-mobile',
            'name'     => 'PUBG Mobile',
            'category' => 'Battle Royale',
            'icon'     => '🎯',
            'color'    => '#c9a227',
            'popular'  => true,
            'is_active'=> true,
            'sort_order'=> 3,
        ],
        [
            'id'       => 4,
            'slug'     => 'genshin-impact',
            'name'     => 'Genshin Impact',
            'category' => 'RPG',
            'icon'     => '✨',
            'color'    => '#6b5de4',
            'popular'  => true,
            'is_active'=> true,
            'sort_order'=> 4,
        ],
        [
            'id'       => 5,
            'slug'     => 'valorant',
            'name'     => 'Valorant',
            'category' => 'FPS',
            'icon'     => '⚡',
            'color'    => '#ff4655',
            'popular'  => false,
            'is_active'=> true,
            'sort_order'=> 5,
        ],
        [
            'id'       => 6,
            'slug'     => 'honkai-star-rail',
            'name'     => 'Honkai Star Rail',
            'category' => 'RPG',
            'icon'     => '🌟',
            'color'    => '#7c3aed',
            'popular'  => false,
            'is_active'=> true,
            'sort_order'=> 6,
        ],
        [
            'id'       => 7,
            'slug'     => 'lokapala',
            'name'     => 'Lokapala',
            'category' => 'Game Lokal',
            'icon'     => '🛡️',
            'color'    => '#10b981',
            'popular'  => false,
            'is_active'=> true,
            'sort_order'=> 7,
        ],
    ];
    if ($onlyActive) {
        return array_filter($staticGames, function($g) { return $g['is_active']; });
    }
    return $staticGames;
}

function getGameBySlug($slug) {
    $db = getDB();
    if ($db) {
        $slugEscaped = $db->real_escape_string($slug);
        $result = $db->query("SELECT * FROM games WHERE slug = '$slugEscaped' LIMIT 1");
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return [
                'id'         => (int)$row['id'],
                'slug'       => $row['slug'],
                'name'       => $row['name'],
                'category'   => $row['category'],
                'icon'       => $row['icon'],
                'color'      => $row['color'],
                'popular'    => (bool)$row['is_popular'],
                'is_active'  => (bool)$row['is_active'],
                'sort_order' => (int)$row['sort_order'],
            ];
        }
    }
    // Fallback
    foreach (getGames(false) as $game) {
        if ($game['slug'] === $slug) return $game;
    }
    return null;
}

// =============================================
// DATA PRODUK TOP-UP (DENGAN INTEGRASI DB & FALLBACK)
// =============================================
function getProducts($gameSlug, $onlyActive = true) {
    $db = getDB();
    if ($db) {
        $gameSlugEscaped = $db->real_escape_string($gameSlug);
        $activeClause = $onlyActive ? "AND p.is_active = 1" : "";
        $query = "SELECT p.* FROM products p 
                  JOIN games g ON p.game_id = g.id 
                  WHERE g.slug = '$gameSlugEscaped' $activeClause
                  ORDER BY p.sort_order ASC, p.price ASC";
        $result = $db->query($query);
        if ($result && $result->num_rows > 0) {
            $products = [];
            while ($row = $result->fetch_assoc()) {
                $products[] = [
                    'id'         => (int)$row['id'],
                    'game_id'    => (int)$row['game_id'],
                    'name'       => $row['name'],
                    'amount'     => (int)$row['amount'],
                    'price'      => (int)$row['price'],
                    'bonus'      => (int)$row['bonus'],
                    'is_active'  => (bool)$row['is_active'],
                    'sort_order' => (int)$row['sort_order'],
                ];
            }
            return $products;
        }
    }
    // Fallback
    $staticProducts = [
        'mobile-legends' => [
            ['id' => 1, 'name' => '86 Diamonds', 'amount' => 86, 'price' => 19000, 'bonus' => 0, 'is_active' => true, 'sort_order' => 1],
            ['id' => 2, 'name' => '172 Diamonds', 'amount' => 172, 'price' => 38000, 'bonus' => 0, 'is_active' => true, 'sort_order' => 2],
            ['id' => 3, 'name' => '257 Diamonds', 'amount' => 257, 'price' => 57000, 'bonus' => 5, 'is_active' => true, 'sort_order' => 3],
            ['id' => 4, 'name' => '344 Diamonds', 'amount' => 344, 'price' => 76000, 'bonus' => 0, 'is_active' => true, 'sort_order' => 4],
            ['id' => 5, 'name' => '514 Diamonds', 'amount' => 514, 'price' => 114000, 'bonus' => 10, 'is_active' => true, 'sort_order' => 5],
            ['id' => 6, 'name' => '706 Diamonds', 'amount' => 706, 'price' => 152000, 'bonus' => 0, 'is_active' => true, 'sort_order' => 6],
            ['id' => 7, 'name' => '1412 Diamonds', 'amount' => 1412, 'price' => 304000, 'bonus' => 20, 'is_active' => true, 'sort_order' => 7],
            ['id' => 8, 'name' => '2195 Diamonds', 'amount' => 2195, 'price' => 456000, 'bonus' => 50, 'is_active' => true, 'sort_order' => 8],
        ],
        'free-fire' => [
            ['id' => 1, 'name' => '70 Diamonds', 'amount' => 70, 'price' => 15000, 'bonus' => 0, 'is_active' => true, 'sort_order' => 1],
            ['id' => 2, 'name' => '140 Diamonds', 'amount' => 140, 'price' => 29000, 'bonus' => 0, 'is_active' => true, 'sort_order' => 2],
            ['id' => 3, 'name' => '355 Diamonds', 'amount' => 355, 'price' => 69000, 'bonus' => 5, 'is_active' => true, 'sort_order' => 3],
            ['id' => 4, 'name' => '720 Diamonds', 'amount' => 720, 'price' => 139000, 'bonus' => 10, 'is_active' => true, 'sort_order' => 4],
            ['id' => 5, 'name' => '1450 Diamonds', 'amount' => 1450, 'price' => 279000, 'bonus' => 20, 'is_active' => true, 'sort_order' => 5],
            ['id' => 6, 'name' => '2180 Diamonds', 'amount' => 2180, 'price' => 419000, 'bonus' => 30, 'is_active' => true, 'sort_order' => 6],
        ],
        'pubg-mobile' => [
            ['id' => 1, 'name' => '60 UC', 'amount' => 60, 'price' => 16000, 'bonus' => 0, 'is_active' => true, 'sort_order' => 1],
            ['id' => 2, 'name' => '325 UC', 'amount' => 325, 'price' => 80000, 'bonus' => 0, 'is_active' => true, 'sort_order' => 2],
            ['id' => 3, 'name' => '660 UC', 'amount' => 660, 'price' => 159000, 'bonus' => 0, 'is_active' => true, 'sort_order' => 3],
            ['id' => 4, 'name' => '1800 UC', 'amount' => 1800, 'price' => 399000, 'bonus' => 0, 'is_active' => true, 'sort_order' => 4],
            ['id' => 5, 'name' => '3850 UC', 'amount' => 3850, 'price' => 799000, 'bonus' => 50, 'is_active' => true, 'sort_order' => 5],
            ['id' => 6, 'name' => '8100 UC', 'amount' => 8100, 'price' => 1599000, 'bonus' => 100, 'is_active' => true, 'sort_order' => 6],
        ],
        'genshin-impact' => [
            ['id' => 1, 'name' => '60 Primogems', 'amount' => 60, 'price' => 15000, 'bonus' => 0, 'is_active' => true, 'sort_order' => 1],
            ['id' => 2, 'name' => '300 Primogems', 'amount' => 300, 'price' => 75000, 'bonus' => 30, 'is_active' => true, 'sort_order' => 2],
            ['id' => 3, 'name' => '980 Primogems', 'amount' => 980, 'price' => 229000, 'bonus' => 110, 'is_active' => true, 'sort_order' => 3],
            ['id' => 4, 'name' => '1980 Primogems', 'amount' => 1980, 'price' => 449000, 'bonus' => 260, 'is_active' => true, 'sort_order' => 4],
            ['id' => 5, 'name' => '3280 Primogems', 'amount' => 3280, 'price' => 699000, 'bonus' => 600, 'is_active' => true, 'sort_order' => 5],
            ['id' => 6, 'name' => '6480 Primogems', 'amount' => 6480, 'price' => 1349000, 'bonus' => 1600, 'is_active' => true, 'sort_order' => 6],
        ],
        'valorant' => [
            ['id' => 1, 'name' => '475 VP', 'amount' => 475, 'price' => 75000, 'bonus' => 0, 'is_active' => true, 'sort_order' => 1],
            ['id' => 2, 'name' => '1000 VP', 'amount' => 1000, 'price' => 149000, 'bonus' => 0, 'is_active' => true, 'sort_order' => 2],
            ['id' => 3, 'name' => '2050 VP', 'amount' => 2050, 'price' => 299000, 'bonus' => 0, 'is_active' => true, 'sort_order' => 3],
            ['id' => 4, 'name' => '3650 VP', 'amount' => 3650, 'price' => 499000, 'bonus' => 0, 'is_active' => true, 'sort_order' => 4],
            ['id' => 5, 'name' => '5350 VP', 'amount' => 5350, 'price' => 699000, 'bonus' => 0, 'is_active' => true, 'sort_order' => 5],
        ],
        'honkai-star-rail' => [
            ['id' => 1, 'name' => '60 Oneiric Shards', 'amount' => 60, 'price' => 15000, 'bonus' => 0, 'is_active' => true, 'sort_order' => 1],
            ['id' => 2, 'name' => '300 Oneiric Shards', 'amount' => 300, 'price' => 75000, 'bonus' => 30, 'is_active' => true, 'sort_order' => 2],
            ['id' => 3, 'name' => '980 Oneiric Shards', 'amount' => 980, 'price' => 229000, 'bonus' => 110, 'is_active' => true, 'sort_order' => 3],
            ['id' => 4, 'name' => '1980 Oneiric Shards', 'amount' => 1980, 'price' => 449000, 'bonus' => 260, 'is_active' => true, 'sort_order' => 4],
        ],
    ];
    $prods = $staticProducts[$gameSlug] ?? [];
    if ($onlyActive) {
        return array_filter($prods, function($p) { return $p['is_active']; });
    }
    return $prods;
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

// =============================================
// HELPER DATABASE GAME (ADMIN)
// =============================================
function getGameById($id) {
    $db = getDB();
    if ($db) {
        $id = (int)$id;
        $result = $db->query("SELECT * FROM games WHERE id = $id");
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
    }
    return null;
}

// =============================================
// HELPER DATABASE PRODUK (ADMIN)
// =============================================
function getProductById($id) {
    $db = getDB();
    if ($db) {
        $id = (int)$id;
        $result = $db->query("SELECT * FROM products WHERE id = $id");
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
    }
    return null;
}

function getAllProducts($gameId = null) {
    $db = getDB();
    if ($db) {
        $where = "";
        if ($gameId !== null) {
            $where = "WHERE p.game_id = " . (int)$gameId;
        }
        $query = "SELECT p.*, g.name AS game_name FROM products p 
                  JOIN games g ON p.game_id = g.id 
                  $where
                  ORDER BY g.name ASC, p.sort_order ASC, p.price ASC";
        $result = $db->query($query);
        $products = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }
        return $products;
    }
    return [];
}

// =============================================
// HELPER DATABASE PESANAN (ADMIN)
// =============================================
function getOrders($limit = null, $status = null) {
    $db = getDB();
    if ($db) {
        $where = [];
        if ($status !== null) {
            $where[] = "o.status = '" . $db->real_escape_string($status) . "'";
        }
        $whereSql = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";
        $limitSql = $limit !== null ? "LIMIT " . (int)$limit : "";
        
        $query = "SELECT o.*, g.name AS game_name, g.icon AS game_icon, p.name AS product_name 
                  FROM orders o
                  LEFT JOIN games g ON o.game_id = g.id
                  LEFT JOIN products p ON o.product_id = p.id
                  $whereSql
                  ORDER BY o.created_at DESC 
                  $limitSql";
        $result = $db->query($query);
        $orders = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $orders[] = $row;
            }
        }
        return $orders;
    }
    return [];
}

function getOrderDetails($orderId) {
    $db = getDB();
    if ($db) {
        $orderIdEscaped = $db->real_escape_string($orderId);
        $query = "SELECT o.*, g.name AS game_name, g.icon AS game_icon, p.name AS product_name 
                  FROM orders o
                  LEFT JOIN games g ON o.game_id = g.id
                  LEFT JOIN products p ON o.product_id = p.id
                  WHERE o.order_id = '$orderIdEscaped' LIMIT 1";
        $result = $db->query($query);
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
    }
    return null;
}

// =============================================
// HELPER ADMIN STATISTIK
// =============================================
function getAdminStats() {
    $db = getDB();
    $stats = [
        'total_games' => 0,
        'total_products' => 0,
        'total_orders' => 0,
        'total_revenue' => 0
    ];
    
    if ($db) {
        // Total Games
        $res = $db->query("SELECT COUNT(*) AS count FROM games");
        if ($res) $stats['total_games'] = (int)$res->fetch_assoc()['count'];
        
        // Total Products
        $res = $db->query("SELECT COUNT(*) AS count FROM products");
        if ($res) $stats['total_products'] = (int)$res->fetch_assoc()['count'];
        
        // Total Orders
        $res = $db->query("SELECT COUNT(*) AS count FROM orders");
        if ($res) $stats['total_orders'] = (int)$res->fetch_assoc()['count'];
        
        // Total Revenue (Status Success)
        $res = $db->query("SELECT SUM(total_price) AS sum FROM orders WHERE status = 'success'");
        if ($res) {
            $sum = $res->fetch_assoc()['sum'];
            $stats['total_revenue'] = $sum ? (int)$sum : 0;
        }
    } else {
        // Dummy stats fallback
        $stats['total_games'] = count(getGames(false));
        $stats['total_products'] = 34;
        $stats['total_orders'] = 2;
        $stats['total_revenue'] = 57000;
    }
    return $stats;
}

// =============================================
// USER AUTHENTICATION & HISTORY
// =============================================
function registerUser($username, $email, $password, $phone) {
    $db = getDB();
    if (!$db) {
        return ['status' => false, 'message' => 'Koneksi database terputus.'];
    }

    $username = sanitize($username);
    $email = sanitize($email);
    $phone = sanitize($phone);

    // Validasi input
    if (empty($username) || empty($email) || empty($password)) {
        return ['status' => false, 'message' => 'Username, Email, dan Password wajib diisi.'];
    }

    // Periksa keunikan username dan email
    $usernameEsc = $db->real_escape_string($username);
    $emailEsc = $db->real_escape_string($email);
    $check = $db->query("SELECT id FROM users WHERE username = '$usernameEsc' OR email = '$emailEsc' LIMIT 1");
    if ($check && $check->num_rows > 0) {
        return ['status' => false, 'message' => 'Username atau Email sudah terdaftar.'];
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Simpan ke DB
    $stmt = $db->prepare("INSERT INTO users (username, email, password, phone) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $hashedPassword, $phone);
    if ($stmt->execute()) {
        return ['status' => true, 'message' => 'Registrasi berhasil! Silakan masuk.'];
    } else {
        return ['status' => false, 'message' => 'Gagal mendaftarkan akun: ' . $db->error];
    }
}

function loginUser($usernameOrEmail, $password) {
    $db = getDB();
    if (!$db) {
        return ['status' => false, 'message' => 'Koneksi database terputus.'];
    }

    $usernameOrEmail = sanitize($usernameOrEmail);

    if (empty($usernameOrEmail) || empty($password)) {
        return ['status' => false, 'message' => 'Semua field wajib diisi.'];
    }

    $esc = $db->real_escape_string($usernameOrEmail);
    $query = "SELECT * FROM users WHERE username = '$esc' OR email = '$esc' LIMIT 1";
    $result = $db->query($query);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Set Session
            $_SESSION['user_id']   = (int)$user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['email']     = $user['email'];
            $_SESSION['user_logged_in'] = true;
            return ['status' => true, 'message' => 'Login berhasil!'];
        }
    }

    return ['status' => false, 'message' => 'Username/Email atau Password salah!'];
}

function getUserOrders($userId) {
    $db = getDB();
    $orders = [];
    if ($db) {
        $userId = (int)$userId;
        $query = "SELECT o.*, g.name AS game_name, g.icon AS game_icon, p.name AS product_name 
                  FROM orders o
                  LEFT JOIN games g ON o.game_id = g.id
                  LEFT JOIN products p ON o.product_id = p.id
                  WHERE o.user_id = $userId
                  ORDER BY o.created_at DESC";
        $result = $db->query($query);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $orders[] = $row;
            }
        }
    }
    return $orders;
}


