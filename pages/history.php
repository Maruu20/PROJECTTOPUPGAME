<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Proteksi halaman: harus login
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    redirect(APP_URL . '/pages/login.php');
}

$userId = $_SESSION['user_id'];
$orders = getUserOrders($userId);
$pageTitle = 'Riwayat Transaksi';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <div class="breadcrumb">
            <a href="<?= APP_URL ?>/index.php">Beranda</a>
            <span>›</span>
            <span>Riwayat Transaksi</span>
        </div>
        <h1>📋 Riwayat Transaksi Anda</h1>
        <p>Pantau seluruh transaksi top-up game Anda di <?= APP_NAME ?>.</p>
    </div>
</div>

<section class="section" style="padding-top: 0; min-height: 50vh;">
    <div class="container">
        <div class="topup-card" style="padding: 28px; box-shadow: var(--shadow);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
                <h2 style="font-size: 1.15rem; margin-bottom: 0;">Daftar Pemesanan</h2>
                <span style="font-size: 0.8rem; color: var(--text-muted);"><?= count($orders) ?> transaksi terdaftar</span>
            </div>

            <?php if (empty($orders)): ?>
                <div style="text-align: center; padding: 48px 24px;">
                    <div style="font-size: 3rem; margin-bottom: 16px;">🛒</div>
                    <h3 style="font-size: 1.1rem; margin-bottom: 8px;">Belum Ada Transaksi</h3>
                    <p style="color: var(--text-dim); font-size: 0.88rem; margin-bottom: 24px; max-width: 320px; margin-left: auto; margin-right: auto;">
                        Anda belum pernah melakukan pemesanan top-up game. Mulai top-up game favorit Anda sekarang!
                    </p>
                    <a href="<?= APP_URL ?>/pages/games.php" class="btn btn-primary">Top-Up Sekarang</a>
                </div>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                        <thead>
                            <tr style="border-bottom: 1px solid var(--border); color: var(--text-dim);">
                                <th style="padding: 12px 16px; font-family: 'Syne', sans-serif; font-weight: 700; font-size: 0.78rem; text-transform: uppercase;">ID Pesanan</th>
                                <th style="padding: 12px 16px; font-family: 'Syne', sans-serif; font-weight: 700; font-size: 0.78rem; text-transform: uppercase;">Game & Nominal</th>
                                <th style="padding: 12px 16px; font-family: 'Syne', sans-serif; font-weight: 700; font-size: 0.78rem; text-transform: uppercase;">User ID</th>
                                <th style="padding: 12px 16px; font-family: 'Syne', sans-serif; font-weight: 700; font-size: 0.78rem; text-transform: uppercase;">Metode</th>
                                <th style="padding: 12px 16px; font-family: 'Syne', sans-serif; font-weight: 700; font-size: 0.78rem; text-transform: uppercase;">Total</th>
                                <th style="padding: 12px 16px; font-family: 'Syne', sans-serif; font-weight: 700; font-size: 0.78rem; text-transform: uppercase;">Tanggal</th>
                                <th style="padding: 12px 16px; font-family: 'Syne', sans-serif; font-weight: 700; font-size: 0.78rem; text-transform: uppercase;">Status</th>
                                <th style="padding: 12px 16px; font-family: 'Syne', sans-serif; font-weight: 700; font-size: 0.78rem; text-transform: uppercase; text-align: center;">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $o): ?>
                                <tr style="border-bottom: 1px solid var(--border); transition: var(--transition);" onmouseover="this.style.background='rgba(255,255,255,0.01)'" onmouseout="this.style.background='transparent'">
                                    <td style="padding: 16px; font-family: monospace; font-weight: 700; color: var(--cyan);">
                                        <?= htmlspecialchars($o['order_id']) ?>
                                    </td>
                                    <td style="padding: 16px;">
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <span style="font-size: 1.4rem;"><?= $o['game_icon'] ?></span>
                                            <div>
                                                <div style="font-weight: 600; color: var(--text);"><?= htmlspecialchars($o['game_name']) ?></div>
                                                <div style="font-size: 0.75rem; color: var(--text-dim);"><?= htmlspecialchars($o['product_name']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding: 16px; color: var(--text);">
                                        <?= htmlspecialchars($o['user_game_id']) ?>
                                        <?php if (!empty($o['server_id'])): ?>
                                            <span style="color: var(--text-muted); font-size: 0.75rem;">(Zone <?= htmlspecialchars($o['server_id']) ?>)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 16px; color: var(--text-dim);">
                                        <?= htmlspecialchars(strtoupper(str_replace('_', ' ', $o['payment_method']))) ?>
                                    </td>
                                    <td style="padding: 16px; font-weight: 600; color: var(--green);">
                                        <?= formatRupiah($o['total_price']) ?>
                                    </td>
                                    <td style="padding: 16px; color: var(--text-muted); font-size: 0.78rem;">
                                        <?= date('d M Y, H:i', strtotime($o['created_at'])) ?>
                                    </td>
                                    <td style="padding: 16px;">
                                        <?php
                                        $statusClass = 'status-pending';
                                        $statusLabel = 'Pending';
                                        if ($o['status'] === 'success') {
                                            $statusClass = 'status-success';
                                            $statusLabel = '✅ Berhasil';
                                        } elseif ($o['status'] === 'processing') {
                                            $statusClass = 'status-pending'; // keep orange or customized
                                            $statusLabel = '⚙️ Diproses';
                                        } elseif ($o['status'] === 'failed') {
                                            $statusClass = 'status-failed';
                                            $statusLabel = '❌ Gagal';
                                        }
                                        ?>
                                        <span class="status-badge <?= $statusClass ?>" style="padding: 2px 10px; font-size: 0.72rem;"><?= $statusLabel ?></span>
                                    </td>
                                    <td style="padding: 16px; text-align: center;">
                                        <form method="POST" action="<?= APP_URL ?>/pages/order-check.php">
                                            <input type="hidden" name="order_id" value="<?= htmlspecialchars($o['order_id']) ?>">
                                            <button type="submit" class="btn btn-outline" style="padding: 6px 12px; font-size: 0.75rem; border-radius: var(--radius-sm);">
                                                Cek Detail
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
