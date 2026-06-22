-- ============================================
-- GAMETOP — DATABASE SCHEMA
-- Jalankan di MySQL/MariaDB
-- ============================================

CREATE DATABASE IF NOT EXISTS topup_game_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE topup_game_db;

-- ===== RESET TABLES =====
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS games;
DROP TABLE IF EXISTS users;

-- ===== TABEL GAMES =====
CREATE TABLE IF NOT EXISTS games (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    slug        VARCHAR(100) UNIQUE NOT NULL,
    name        VARCHAR(150) NOT NULL,
    category    VARCHAR(80)  NOT NULL,
    icon        VARCHAR(10)  DEFAULT '🎮',
    color       VARCHAR(20)  DEFAULT '#00e5ff',
    is_popular  TINYINT(1)   DEFAULT 0,
    is_active   TINYINT(1)   DEFAULT 1,
    sort_order  INT          DEFAULT 0,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ===== TABEL PRODUK =====
CREATE TABLE IF NOT EXISTS products (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    game_id     INT NOT NULL,
    name        VARCHAR(150) NOT NULL,
    amount      INT          NOT NULL,
    price       INT          NOT NULL,
    bonus       INT          DEFAULT 0,
    is_active   TINYINT(1)  DEFAULT 1,
    sort_order  INT          DEFAULT 0,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===== TABEL USERS =====
CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(50) UNIQUE NOT NULL,
    email       VARCHAR(100) UNIQUE NOT NULL,
    password    VARCHAR(255) NOT NULL,
    phone       VARCHAR(20)  DEFAULT NULL,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ===== TABEL PESANAN =====
CREATE TABLE IF NOT EXISTS orders (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    order_id        VARCHAR(50) UNIQUE NOT NULL,
    game_id         INT NOT NULL,
    product_id      INT NOT NULL,
    user_id         INT          DEFAULT NULL,
    user_game_id    VARCHAR(100) NOT NULL,
    server_id       VARCHAR(50)  DEFAULT NULL,
    payment_method  VARCHAR(50)  NOT NULL,
    payment_option  VARCHAR(50)  DEFAULT NULL,
    total_price     INT NOT NULL,
    status          ENUM('pending','processing','success','failed') DEFAULT 'pending',
    notes           TEXT         DEFAULT NULL,
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (game_id)    REFERENCES games(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (user_id)    REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ===== DATA AWAL: GAMES =====
INSERT INTO games (slug, name, category, icon, color, is_popular, sort_order) VALUES
('mobile-legends', 'Mobile Legends', 'MOBA',          '🗡️', '#1a73e8', 1, 1),
('free-fire',      'Free Fire',      'Battle Royale',  '🔥', '#f4a018', 1, 2),
('pubg-mobile',    'PUBG Mobile',    'Battle Royale',  '🎯', '#c9a227', 1, 3),
('genshin-impact', 'Genshin Impact', 'RPG',            '✨', '#6b5de4', 1, 4),
('valorant',       'Valorant',       'FPS',            '⚡', '#ff4655', 0, 5),
('honkai-star-rail','Honkai Star Rail','RPG',           '🌟', '#7c3aed', 0, 6);

-- ===== DATA AWAL: PRODUK MOBILE LEGENDS =====
INSERT INTO products (game_id, name, amount, price, bonus, sort_order) VALUES
((SELECT id FROM games WHERE slug = 'mobile-legends' LIMIT 1), '86 Diamonds',   86,   19000, 0, 1),
((SELECT id FROM games WHERE slug = 'mobile-legends' LIMIT 1), '172 Diamonds',  172,  38000, 0, 2),
((SELECT id FROM games WHERE slug = 'mobile-legends' LIMIT 1), '257 Diamonds',  257,  57000, 5, 3),
((SELECT id FROM games WHERE slug = 'mobile-legends' LIMIT 1), '514 Diamonds',  514, 114000, 10, 4),
((SELECT id FROM games WHERE slug = 'mobile-legends' LIMIT 1), '1412 Diamonds', 1412, 304000, 20, 5);

-- ===== INDEX =====
CREATE INDEX idx_orders_order_id ON orders(order_id);
CREATE INDEX idx_orders_status   ON orders(status);
CREATE INDEX idx_products_game   ON products(game_id);
