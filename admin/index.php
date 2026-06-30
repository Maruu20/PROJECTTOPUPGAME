<?php
$adminTitle = 'Dashboard';
include __DIR__ . '/admin-header.php';

$stats = getAdminStats();
$recentOrders = getOrders(5);
?>

<!-- ===== STATISTIK ===== -->
<div class="stats-grid">
    <div class="stat-card">
        <div>
            <div class="stat-label">Total Game</div>
            <div class="stat-value"><?= $stats['total_games'] ?></div>
        </div>
        <div class="stat-icon-wrapper stat-icon-cyan">🎮</div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-label">Total Produk</div>
            <div class="stat-value"><?= $stats['total_products'] ?></div>
        </div>
        <div class="stat-icon-wrapper stat-icon-purple">💎</div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-label">Total Pesanan</div>
            <div class="stat-value"><?= $stats['total_orders'] ?></div>
        </div>
        <div class="stat-icon-wrapper stat-icon-amber">📋</div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-label">Total Pendapatan</div>
            <div class="stat-value" style="font-size: 1.5rem;"><?= formatRupiah($stats['total_revenue']) ?></div>
        </div>
        <div class="stat-icon-wrapper stat-icon-green">💰</div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-label">Total Banner</div>
            <div class="stat-value"><?= $stats['total_banners'] ?></div>
        </div>
        <div class="stat-icon-wrapper stat-icon-purple" style="background: rgba(189, 0, 255, 0.1); color: var(--neon-purple);">🖼️</div>
    </div>
</div>

<!-- ===== PESANAN TERBARU ===== -->
<div class="admin-card">
    <div class="admin-card-header">
        <h2>📋 Pesanan Terbaru</h2>
        <a href="<?= APP_URL ?>/admin/orders.php" class="btn-admin btn-admin-secondary btn-admin-sm">
            Lihat Semua Pesanan →
        </a>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID Pesanan</th>
                    <th>Game & Produk</th>
                    <th>ID Player</th>
                    <th>Metode</th>
                    <th>Total</th>
                    <th>Waktu</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentOrders)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center;" class="text-muted">Belum ada data pesanan masuk.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td style="font-family: monospace; font-weight: 700; color: var(--neon-cyan);">
                                <?= htmlspecialchars($order['order_id']) ?>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span><?= $order['game_icon'] ?></span>
                                    <div>
                                        <div style="font-weight: 600;"><?= htmlspecialchars($order['game_name']) ?></div>
                                        <div class="text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars($order['product_name']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div><?= htmlspecialchars($order['user_game_id']) ?></div>
                                <?php if (!empty($order['server_id'])): ?>
                                    <div class="text-muted" style="font-size: 0.75rem;">Server: <?= htmlspecialchars($order['server_id']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars(strtoupper(str_replace('_', ' ', $order['payment_method']))) ?></td>
                            <td style="font-weight: 600; color: var(--green);"><?= formatRupiah($order['total_price']) ?></td>
                            <td style="font-size: 0.78rem;" class="text-muted"><?= date('d M Y, H:i', strtotime($order['created_at'])) ?></td>
                            <td>
                                <?php
                                $statusClass = 'badge-pending';
                                $statusLabel = 'Pending';
                                if ($order['status'] === 'success') {
                                    $statusClass = 'badge-success';
                                    $statusLabel = 'Sukses';
                                } elseif ($order['status'] === 'processing') {
                                    $statusClass = 'badge-processing';
                                    $statusLabel = 'Diproses';
                                } elseif ($order['status'] === 'failed') {
                                    $statusClass = 'badge-failed';
                                    $statusLabel = 'Gagal';
                                }
                                ?>
                                <span class="badge-admin <?= $statusClass ?>"><?= $statusLabel ?></span>
                            </td>
                            <td>
                                <a href="<?= APP_URL ?>/admin/orders.php?edit=<?= htmlspecialchars($order['order_id']) ?>" class="btn-admin btn-admin-primary btn-admin-sm">
                                    Kelola
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/admin-footer.php'; ?>
