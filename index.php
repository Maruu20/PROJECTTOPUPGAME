<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Beranda';
$games = getGames();
$popularGames = array_filter($games, function($g) {
    return $g['popular'];
});
$banners = getBanners();
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<!-- ===== HERO ===== -->
<section class="hero">
    <div class="hero-bg-grid"></div>
    <div class="container hero-inner">
        <div class="hero-badge">⚡ #1 Platform Top-Up Game Indonesia</div>
        <h1>Top-Up Game Favorit<br>Kamu <span class="accent">Lebih Mudah</span></h1>
        <p>Proses instan, harga terjangkau, tersedia lebih dari 50+ game. Didukung ratusan metode pembayaran.</p>
        <div class="hero-ctas">
            <a href="pages/games.php" class="btn btn-primary btn-lg">Mulai Top-Up →</a>
            <a href="pages/order-check.php" class="btn btn-outline btn-lg">Cek Pesanan</a>
        </div>
        <div class="hero-stats">
            <div class="hero-stat">
                <strong data-count="250000" data-suffix="+">0</strong>
                <span>Transaksi Sukses</span>
            </div>
            <div class="hero-stat">
                <strong data-count="50" data-suffix="+">0</strong>
                <span>Judul Game</span>
            </div>
            <div class="hero-stat">
                <strong data-count="99" data-suffix="%">0</strong>
                <span>Proses Berhasil</span>
            </div>
        </div>
    </div>
</section>

<!-- ===== PROMO SLIDER SECTION ===== -->
<?php if (!empty($banners)): ?>
<section class="section" style="padding-bottom: 0;">
    <div class="container">
        <div class="banner-slider-container">
            <div class="banner-slider-wrapper">
                <?php foreach ($banners as $b): ?>
                    <div class="banner-slide <?= !empty($b['image_path']) ? 'has-image' : '' ?>">
                        <?php if (!empty($b['image_path'])): ?>
                            <img src="<?= APP_URL . '/' . htmlspecialchars($b['image_path']) ?>" class="banner-slide-bg" alt="<?= htmlspecialchars($b['title']) ?>">
                            <div class="banner-slide-overlay"></div>
                        <?php endif; ?>
                        
                        <div class="banner-slide-content">
                            <span class="promo-banner-badge"><?= htmlspecialchars($b['badge_text']) ?></span>
                            <h2 class="promo-banner-title"><?= htmlspecialchars($b['title']) ?></h2>
                            <p class="promo-banner-desc"><?= htmlspecialchars($b['description']) ?></p>
                            <a href="<?= htmlspecialchars($b['button_link']) ?>" class="banner-slide-btn">
                                <?= htmlspecialchars($b['button_text']) ?> →
                            </a>
                        </div>
                        
                        <?php if (empty($b['image_path'])): ?>
                            <div class="banner-slide-graphic"><?= htmlspecialchars($b['graphic_icon']) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (count($banners) > 1): ?>
                <!-- Navigation Arrows -->
                <button class="banner-slider-arrow prev" aria-label="Sebelumnya">‹</button>
                <button class="banner-slider-arrow next" aria-label="Berikutnya">›</button>
                
                <!-- Navigation Dots -->
                <div class="banner-slider-dots">
                    <?php foreach ($banners as $index => $b): ?>
                        <span class="banner-slider-dot <?= $index === 0 ? 'active' : '' ?>" data-index="<?= $index ?>"></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ===== GAME POPULER ===== -->
<section class="section" style="background: var(--bg2);">
    <div class="container">
        <div class="section-title">
            <span class="label">🔥 Terpopuler</span>
            <h2>Game Pilihan Gamer</h2>
            <p>Top-up diamond, UC, VP, primogem dan currency game lainnya dengan cepat.</p>
        </div>
        <div class="games-grid">
            <?php foreach ($popularGames as $game): ?>
            <a href="pages/topup.php?game=<?= $game['slug'] ?>" class="game-card">
                <?php if ($game['popular']): ?>
                <span class="badge-popular">HOT</span>
                <?php endif; ?>
                <?php 
                if (preg_match('/\.(jpg|jpeg|png|gif|webp|svg)$/i', $game['icon']) || strpos($game['icon'], '/') === 0) {
                    echo '<img src="' . APP_URL . $game['icon'] . '" alt="' . htmlspecialchars($game['name']) . '" class="game-icon-img">';
                } else {
                    echo '<span class="game-icon">' . $game['icon'] . '</span>';
                }
                ?>
                <div class="game-name"><?= htmlspecialchars($game['name']) ?></div>
                <div class="game-cat"><?= htmlspecialchars($game['category']) ?></div>
            </a>
            <?php endforeach; ?>
            <a href="pages/games.php" class="game-card" style="justify-content: center; display: flex; flex-direction: column; align-items: center; gap: 8px; color: var(--text-muted);">
                <span class="game-icon">🎮</span>
                <div class="game-name" style="color: var(--cyan);">Lihat Semua</div>
                <div class="game-cat">50+ Game Tersedia</div>
            </a>
        </div>
    </div>
</section>

<!-- ===== KEUNGGULAN ===== -->
<section class="section">
    <div class="container">
        <div class="section-title centered">
            <span class="label">Kenapa Pilih Kami</span>
            <h2>Cepat, Aman, Terpercaya</h2>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3>Proses Instan</h3>
                <p>Top-up kamu langsung masuk ke akun dalam hitungan detik, 24 jam 7 hari tanpa libur.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💰</div>
                <h3>Harga Terbaik</h3>
                <p>Kami menjamin harga termurah di Indonesia dengan kualitas layanan terbaik.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3>100% Aman</h3>
                <p>Sistem keamanan berlapis melindungi setiap transaksi kamu. Tidak perlu share password.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💳</div>
                <h3>Banyak Metode Bayar</h3>
                <p>Transfer bank, e-wallet (GoPay, OVO, DANA), pulsa, hingga QRIS semua tersedia.</p>
            </div>
        </div>
    </div>
</section>

<!-- ===== TESTIMONIAL ===== -->
<section class="section" style="background: var(--bg2);">
    <div class="container">
        <div class="section-title centered">
            <span class="label">Ulasan</span>
            <h2>Kata Para Gamer</h2>
        </div>
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="stars">★★★★★</div>
                <p>Mantap banget! Top-up ML ku langsung masuk kurang dari 1 menit. Udah sering pake sini dan gak pernah kecewa.</p>
                <div class="author">Reza M.</div>
                <div class="author-sub">Mobile Legends Player</div>
            </div>
            <div class="testimonial-card">
                <div class="stars">★★★★★</div>
                <p>Harganya paling murah dibanding toko lain, udah coba bandingin. Plus CS-nya ramah dan responsif!</p>
                <div class="author">Sinta D.</div>
                <div class="author-sub">Free Fire Player</div>
            </div>
            <div class="testimonial-card">
                <div class="stars">★★★★★</div>
                <p>Top-up Genshin pake QRIS, scan langsung bayar, 30 detik primogem udah masuk. Recommended!</p>
                <div class="author">Kevin A.</div>
                <div class="author-sub">Genshin Impact Player</div>
            </div>
        </div>
    </div>
</section>

<!-- ===== CTA ===== -->
<section class="section">
    <div class="container" style="text-align: center;">
        <span class="label">Siap Bermain?</span>
        <h2 style="font-size: clamp(1.8rem, 4vw, 2.6rem); margin-bottom: 16px;">Mulai Top-Up Sekarang</h2>
        <p style="color: var(--text-dim); margin-bottom: 32px;">Bergabunglah dengan 250.000+ gamer yang sudah mempercayai <?= APP_NAME ?>.</p>
        <a href="pages/games.php" class="btn btn-primary btn-lg">Pilih Game →</a>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
