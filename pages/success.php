<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isset($_SESSION['order']) || $_SESSION['order']['status'] !== 'success') {
    redirect(APP_URL . '/index.php');
}

$order = $_SESSION['order'];
$pageTitle = 'Pembayaran Berhasil';

// Hapus session setelah ditampilkan
// unset($_SESSION['order']); // Komentari agar bisa refresh untuk testing
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<section class="section success-page">
    <div class="container">
        <div class="success-card">
            <div class="success-icon">🎉</div>
            <h1>Top-Up Berhasil!</h1>
            <p>
                <strong><?= htmlspecialchars($order['product']['name']) ?></strong> untuk game
                <strong><?= htmlspecialchars($order['game']['name']) ?></strong> telah berhasil
                dikirim ke akun <strong><?= htmlspecialchars($order['user_id']) ?></strong>.
            </p>

            <div class="order-id-box" id="orderIdText">
                <?= htmlspecialchars($order['order_id']) ?>
            </div>

            <div class="summary-rows" style="text-align: left; margin-bottom: 28px;">
                <div class="summary-row">
                    <span class="key">Waktu</span>
                    <span class="val"><?= $order['created_at'] ?></span>
                </div>
                <div class="summary-row">
                    <span class="key">Game</span>
                    <span class="val"><?= $order['game']['icon'] ?> <?= htmlspecialchars($order['game']['name']) ?></span>
                </div>
                <div class="summary-row">
                    <span class="key">Nominal</span>
                    <span class="val"><?= htmlspecialchars($order['product']['name']) ?></span>
                </div>
                <div class="summary-row">
                    <span class="key">Status</span>
                    <span class="val"><span class="status-badge status-success">✅ Berhasil</span></span>
                </div>
                <div class="summary-row total">
                    <span class="key">Total Dibayar</span>
                    <span class="val"><?= formatRupiah($order['total']) ?></span>
                </div>
            </div>

            <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: center;">
                <button class="btn btn-secondary" id="copyOrderId" style="font-size: 0.85rem;">
                    📋 Salin ID Pesanan
                </button>
                <a href="<?= APP_URL ?>/pages/games.php" class="btn btn-primary">
                    Top-Up Lagi →
                </a>
            </div>

            <p style="font-size: 0.78rem; color: var(--text-muted); margin-top: 20px;">
                Simpan ID pesanan untuk keperluan jika ada pertanyaan.<br>
                Masalah? <a href="#" style="color: var(--cyan);">Hubungi dukungan kami</a>.
            </p>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
