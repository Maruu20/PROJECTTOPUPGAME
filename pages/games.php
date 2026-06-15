<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Semua Game';
$games = getGames();
$categories = ['Semua' => 'all', 'MOBA' => 'MOBA', 'Battle Royale' => 'Battle Royale', 'RPG' => 'RPG', 'FPS' => 'FPS'];
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
        <!-- Filter -->
        <div class="games-filter">
            <?php foreach ($categories as $label => $cat): ?>
            <button class="filter-btn <?= $cat === 'all' ? 'active' : '' ?>" data-cat="<?= $cat ?>">
                <?= $label ?>
            </button>
            <?php endforeach; ?>
        </div>

        <!-- Grid -->
        <div class="games-grid">
            <?php foreach ($games as $game): ?>
            <a href="<?= APP_URL ?>/pages/topup.php?game=<?= $game['slug'] ?>"
               class="game-card game-card-item"
               data-cat="<?= $game['category'] ?>">
                <?php if ($game['popular']): ?>
                <span class="badge-popular">HOT</span>
                <?php endif; ?>
                <span class="game-icon"><?= $game['icon'] ?></span>
                <div class="game-name"><?= htmlspecialchars($game['name']) ?></div>
                <div class="game-cat"><?= htmlspecialchars($game['category']) ?></div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
