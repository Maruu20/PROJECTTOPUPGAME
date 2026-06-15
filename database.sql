-- ============================================
-- GAMETOP — DATABASE SCHEMA
-- Jalankan di MySQL/MariaDB
-- ============================================

CREATE DATABASE IF NOT EXISTS topup_game_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE topup_game_db;

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

-- ===== TABEL PESANAN =====
CREATE TABLE IF NOT EXISTS orders (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    order_id        VARCHAR(50) UNIQUE NOT NULL,
    game_id         INT NOT NULL,
    product_id      INT NOT NULL,
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
    FOREIGN KEY (product_id) REFERENCES products(id)
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
INSERT INTO products (game_id, name, amount, price, bonus, sort_order)
SELECT id, name, amount, price, bonus, sort_order FROM games
JOIN (VALUES
    ('mobile-legends', '86 Diamonds',   86,   19000, 0, 1),
    ('mobile-legends', '172 Diamonds',  172,  38000, 0, 2),
    ('mobile-legends', '257 Diamonds',  257,  57000, 5, 3),
    ('mobile-legends', '514 Diamonds',  514, 114000, 10, 4),
    ('mobile-legends', '1412 Diamonds', 1412, 304000, 20, 5)
) AS p(slug, name, amount, price, bonus, sort_order)
ON games.slug = p.slug;

-- ===== INDEX =====
CREATE INDEX idx_orders_order_id ON orders(order_id);
CREATE INDEX idx_orders_status   ON orders(status);
CREATE INDEX idx_products_game   ON products(game_id);
