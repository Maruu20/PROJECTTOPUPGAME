<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$gameSlug = isset($_GET['game']) ? sanitize($_GET['game']) : '';
$game = getGameBySlug($gameSlug);

if (!$game) {
    header('Location: ' . APP_URL . '/pages/games.php');
    exit();
}

$products = getProducts($gameSlug);
$paymentMethods = getPaymentMethods();
$pageTitle = 'Top-Up ' . $game['name'];

// Handle POST submission
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId    = sanitize($_POST['user_id'] ?? '');
    $serverId  = sanitize($_POST['server_id'] ?? '');
    $productId = (int)($_POST['product_id'] ?? 0);
    $payMethod = sanitize($_POST['payment_method'] ?? '');

    // Validasi
    if (empty($userId)) {
        $error = 'User ID tidak boleh kosong.';
    } elseif ($productId <= 0) {
        $error = 'Pilih nominal top-up terlebih dahulu.';
    } elseif (empty($payMethod)) {
        $error = 'Pilih metode pembayaran terlebih dahulu.';
    } else {
        // Cari produk yang dipilih
        $selectedProduct = null;
        foreach ($products as $p) {
            if ($p['id'] === $productId) {
                $selectedProduct = $p;
                break;
            }
        }

        if (!$selectedProduct) {
            $error = 'Produk tidak ditemukan.';
        } else {
            // Simpan ke session & redirect ke halaman success
            $orderId = generateOrderId();
            $_SESSION['order'] = [
                'order_id'   => $orderId,
                'game'       => $game,
                'product'    => $selectedProduct,
                'user_id'    => $userId,
                'server_id'  => $serverId,
                'payment'    => $payMethod,
                'total'      => $selectedProduct['price'],
                'status'     => 'pending',
                'created_at' => date('d M Y, H:i'),
            ];
            redirect(APP_URL . '/pages/payment.php');
        }
    }
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="page-header">
    <div class="container">
        <div class="breadcrumb">
            <a href="<?= APP_URL ?>/index.php">Beranda</a>
            <span>›</span>
            <a href="<?= APP_URL ?>/pages/games.php">Game</a>
            <span>›</span>
            <span><?= htmlspecialchars($game['name']) ?></span>
        </div>
        <h1>
            <?php 
            if (preg_match('/\.(jpg|jpeg|png|gif|webp|svg)$/i', $game['icon']) || strpos($game['icon'], '/') === 0) {
                echo '<img src="' . APP_URL . $game['icon'] . '" alt="' . htmlspecialchars($game['name']) . '" style="width:36px;height:36px;border-radius:8px;object-fit:cover;vertical-align:middle;margin-right:8px;">';
            } else {
                echo $game['icon'];
            }
            ?>
            Top-Up <?= htmlspecialchars($game['name']) ?>
        </h1>
        <p>Isi User ID dan pilih nominal. Proses instan setelah pembayaran terkonfirmasi.</p>
    </div>
</div>

<section class="section" style="padding-top: 0;">
    <div class="container">
        <form method="POST" id="topupForm">
            <input type="hidden" name="game" value="<?= $gameSlug ?>">

            <div class="topup-layout">
                <!-- ===== KIRI: FORM ===== -->
                <div>
                    <?php if ($error): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <!-- STEP 1: USER ID -->
                    <div class="topup-card" style="margin-bottom: 20px;">
                        <h2>① Masukkan Data Akun</h2>
                        <div class="form-group">
                            <label class="form-label" for="userId">User ID *</label>
                            <input type="text" id="userId" name="user_id"
                                   class="form-control" placeholder="Contoh: 12345678"
                                   value="<?= isset($_POST['user_id']) ? htmlspecialchars($_POST['user_id']) : '' ?>"
                                   required>
                        </div>
                        <?php if (in_array($gameSlug, ['mobile-legends', 'pubg-mobile'])): ?>
                        <div class="form-group">
                            <label class="form-label" for="serverId">Server ID <?= $gameSlug === 'mobile-legends' ? '(Zone ID)' : '' ?></label>
                            <input type="text" id="serverId" name="server_id"
                                   class="form-control" placeholder="Contoh: 1234"
                                   value="<?= isset($_POST['server_id']) ? htmlspecialchars($_POST['server_id']) : '' ?>">
                        </div>
                        <?php endif; ?>
                        <div class="alert alert-info" style="margin-bottom: 0; font-size: 0.8rem;">
                            💡 User ID bisa dilihat di profil dalam game. Pastikan data akun benar sebelum melanjutkan.
                        </div>
                    </div>

                    <!-- STEP 2: PILIH NOMINAL -->
                    <div class="topup-card" style="margin-bottom: 20px;">
                        <h2>② Pilih Nominal</h2>
                        <div class="product-grid">
                            <?php foreach ($products as $i => $product): ?>
                            <label class="product-item <?= $i === 0 ? 'selected' : '' ?>"
                                   data-name="<?= htmlspecialchars($product['name']) ?>"
                                   data-price="<?= $product['price'] ?>">
                                <input type="radio" name="product_id"
                                       value="<?= $product['id'] ?>"
                                       <?= $i === 0 ? 'checked' : '' ?>>
                                <?php if ($product['bonus'] > 0): ?>
                                <span class="prod-bonus">+<?= $product['bonus'] ?></span>
                                <?php endif; ?>
                                <div class="prod-name"><?= htmlspecialchars($product['name']) ?></div>
                                <div class="prod-price"><?= formatRupiah($product['price']) ?></div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- STEP 3: METODE BAYAR -->
                    <div class="topup-card">
                        <h2>③ Pilih Pembayaran</h2>
                        <div class="payment-grid">
                            <?php foreach ($paymentMethods as $i => $method): ?>
                            <label class="payment-item <?= $i === 0 ? 'selected' : '' ?>"
                                   data-options='<?= json_encode($method['options']) ?>'>
                                <input type="radio" name="payment_method"
                                       value="<?= $method['id'] ?>"
                                       <?= $i === 0 ? 'checked' : '' ?>>
                                <span class="pay-icon"><?= $method['icon'] ?></span>
                                <div>
                                    <div class="pay-name"><?= htmlspecialchars($method['name']) ?></div>
                                    <div class="pay-sub"><?= implode(', ', $method['options']) ?></div>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                        <div id="paymentSubOption" style="display:none; margin-top: 16px;"></div>
                    </div>
                </div>

                <!-- ===== KANAN: SUMMARY ===== -->
                <div class="order-summary">
                    <div class="summary-card">
                        <h3>📋 Ringkasan Pesanan</h3>
                        <div class="summary-game">
                            <?php 
                            if (preg_match('/\.(jpg|jpeg|png|gif|webp|svg)$/i', $game['icon']) || strpos($game['icon'], '/') === 0) {
                                echo '<img src="' . APP_URL . $game['icon'] . '" alt="' . htmlspecialchars($game['name']) . '" style="width:40px;height:40px;border-radius:10px;object-fit:cover;">';
                            } else {
                                echo '<span class="summary-game-icon">' . $game['icon'] . '</span>';
                            }
                            ?>
                            <div>
                                <div class="summary-game-name"><?= htmlspecialchars($game['name']) ?></div>
                                <div class="summary-game-cat"><?= htmlspecialchars($game['category']) ?></div>
                            </div>
                        </div>
                        <div class="summary-rows">
                            <div class="summary-row">
                                <span class="key">User ID</span>
                                <span class="val" id="summaryUserId">—</span>
                            </div>
                            <div class="summary-row">
                                <span class="key">Nominal</span>
                                <span class="val" id="summaryProduct"><?= htmlspecialchars($products[0]['name'] ?? '—') ?></span>
                            </div>
                            <div class="summary-row">
                                <span class="key">Harga</span>
                                <span class="val" id="summaryPrice"><?= formatRupiah($products[0]['price'] ?? 0) ?></span>
                            </div>
                            <div class="summary-row total">
                                <span class="key">Total Bayar</span>
                                <span class="val" id="summaryTotal"><?= formatRupiah($products[0]['price'] ?? 0) ?></span>
                            </div>
                        </div>
                        <div class="divider"></div>
                        <button type="submit" class="btn btn-primary btn-full btn-lg">
                            Bayar Sekarang →
                        </button>
                        <p style="text-align:center; font-size: 0.75rem; color: var(--text-muted); margin-top: 12px;">
                            🔒 Transaksi diproses dengan aman dan terenkripsi
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
