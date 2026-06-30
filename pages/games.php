<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Semua Game';
$games = getGames();
$banners = getBanners();
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="page-header">
    <div class="container">
        <div class="breadcrumb">
            <a href="<?= APP_URL ?>/index.php">Beranda</a>
            <span>›</span>
            <span>Semua Game</span>
        </div>
        <h1>🎮 Semua Game</h1>
        <p>Pilih game favoritmu dan lakukan top-up dengan mudah dan cepat.</p>
    </div>
</div>

<section class="section" style="padding-top: 0;">
    <div class="container">
        
        <!-- ===== PROMO BANNER ===== -->
        <?php if (!empty($banners)): ?>
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
        <?php endif; ?>

        <!-- ===== FILTER KATEGORI ===== -->
        <div class="games-filter">
            <button class="filter-btn active" data-cat="all">Semua Produk</button>
            <button class="filter-btn" data-cat="flash-topup">Flash Top-Up</button>
            <button class="filter-btn" data-cat="voucher">Voucher</button>

        </div>

        <!-- ===== TOOLBAR: SEARCH & SORT ===== -->
        <div class="catalog-toolbar">
            <div class="search-wrapper">
                <input type="text" id="gameSearch" placeholder="Cari di <?= APP_NAME ?>...">
                <span class="search-icon">🔍</span>
                <button class="clear-btn" id="clearSearch" aria-label="Clear Search">✖</button>
            </div>
            <div class="sort-wrapper">
                <select id="gameSort" aria-label="Urutkan Berdasarkan">
                    <option value="az">Urutan Abjad (A sampai Z)</option>
                    <option value="za">Urutan Abjad (Z sampai A)</option>
                    <option value="populer">Terpopuler</option>
                </select>
            </div>
        </div>

        <!-- ===== HORIZONTAL ALPHABET BAR (A-Z) ===== -->
        <div class="alphabet-filter-container">
            <span class="alphabet-filter-label">A-Z:</span>
            <ul class="alphabet-list" id="alphabetList">
                <li><button class="alphabet-btn active" data-letter="all">ALL</button></li>
                <?php 
                foreach (range('A', 'Z') as $char) {
                    echo '<li><button class="alphabet-btn" data-letter="' . $char . '">' . $char . '</button></li>';
                }
                ?>
            </ul>
        </div>

        <!-- ===== GAMES GRID ===== -->
        <div class="games-grid" id="gamesGrid">
            <?php foreach ($games as $game): 
                // Map category fields
                $tag = 'all';
                if ($game['category'] === 'MOBA' || $game['category'] === 'Battle Royale' || $game['category'] === 'FPS') {
                    $tag = 'flash-topup';
                } elseif ($game['category'] === 'RPG') {
                    $tag = 'voucher';
                }
                
                // Extra metadata for live filtering
                $nameUpper = strtoupper($game['name']);
                $firstLetter = substr($nameUpper, 0, 1);
                $popularVal = $game['popular'] ? 1 : 0;
            ?>
            <a href="<?= APP_URL ?>/pages/topup.php?game=<?= $game['slug'] ?>"
               class="game-card game-card-item"
               data-cat="<?= $tag ?>"
               data-name="<?= htmlspecialchars($game['name']) ?>"
               data-letter="<?= $firstLetter ?>"
               data-popular="<?= $popularVal ?>">
                <?php if ($game['popular']): ?>
                <span class="badge-popular">HOT</span>
                <?php endif; ?>
                
                <div class="game-card-img-container">
                    <?php 
                    if (preg_match('/\.(jpg|jpeg|png|gif|webp|svg)$/i', $game['icon']) || strpos($game['icon'], '/') === 0) {
                        echo '<img src="' . APP_URL . $game['icon'] . '" alt="' . htmlspecialchars($game['name']) . '">';
                    } else {
                        echo '<span class="game-icon-text">' . $game['icon'] . '</span>';
                    }
                    ?>
                </div>
                
                <div class="game-card-info">
                    <div class="game-card-category"><?= htmlspecialchars($game['category']) ?></div>
                    <h3 class="game-card-title"><?= htmlspecialchars($game['name']) ?></h3>
                </div>
                
                <button class="game-card-btn">TOP UP</button>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
