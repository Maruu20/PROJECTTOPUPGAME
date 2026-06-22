<?php
$adminTitle = 'Kelola Pesanan';
include __DIR__ . '/admin-header.php';

$db = getDB();
$dbActive = ($db !== null);
$error = '';
$success = '';

// Tentukan filter status
$filterStatus = isset($_GET['status_filter']) && $_GET['status_filter'] !== '' ? sanitize($_GET['status_filter']) : null;

// Mode kelola status (edit)
$editMode = false;
$editOrder = null;
if ($dbActive && isset($_GET['edit'])) {
    $orderId = sanitize($_GET['edit']);
    $editOrder = getOrderDetails($orderId);
    if ($editOrder) {
        $editMode = true;
    }
}

// Handle Update Status
if ($dbActive && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderIdEscaped = $db->real_escape_string($_POST['order_id']);
    $newStatus      = sanitize($_POST['status'] ?? '');
    $notes          = sanitize($_POST['notes'] ?? '');

    // Validasi status
    if (in_array($newStatus, ['pending', 'processing', 'success', 'failed'])) {
        $stmt = $db->prepare("UPDATE orders SET status = ?, notes = ? WHERE order_id = ?");
        $stmt->bind_param("sss", $newStatus, $notes, $orderIdEscaped);
        if ($stmt->execute()) {
            $success = "Pesanan <strong>" . htmlspecialchars($_POST['order_id']) . "</strong> berhasil diperbarui ke status <strong>" . htmlspecialchars($newStatus) . "</strong>.";
            $editMode = false;
            $editOrder = null;
        } else {
            $error = 'Gagal memperbarui status pesanan: ' . $db->error;
        }
    } else {
        $error = 'Status pesanan tidak valid.';
    }
}

// Handle Delete Action
if ($dbActive && isset($_GET['delete'])) {
    $orderIdEscaped = $db->real_escape_string($_GET['delete']);
    $db->query("DELETE FROM orders WHERE order_id = '$orderIdEscaped'");
    $success = "Pesanan <strong>" . htmlspecialchars($_GET['delete']) . "</strong> berhasil dihapus.";
}

// Fetch orders list
if ($dbActive) {
    $orders = getOrders(null, $filterStatus);
} else {
    // Fallback static data
    $orders = [];
    $demoOrders = [
        [
            'order_id' => 'ORD-ABC123-456',
            'game_name' => 'Mobile Legends',
            'game_icon' => '🗡️',
            'product_name' => '257 Diamonds',
            'user_game_id' => '123456789',
            'server_id' => '',
            'payment_method' => 'ewallet',
            'total_price' => 57000,
            'status' => 'success',
            'created_at' => '2026-06-15 14:32:00',
            'notes' => 'Pembayaran lunas.'
        ],
        [
            'order_id' => 'ORD-DEF456-789',
            'game_name' => 'Free Fire',
            'game_icon' => '🔥',
            'product_name' => '355 Diamonds',
            'user_game_id' => '987654321',
            'server_id' => '',
            'payment_method' => 'transfer_bank',
            'total_price' => 69000,
            'status' => 'pending',
            'created_at' => '2026-06-15 13:10:00',
            'notes' => ''
        ]
    ];
    
    foreach ($demoOrders as $o) {
        if ($filterStatus === null || $o['status'] === $filterStatus) {
            $orders[] = $o;
        }
    }
}
?>

<?php if (!$dbActive): ?>
    <div class="admin-alert admin-alert-error">
        <span>⚠️</span> <strong>Koneksi Database tidak aktif!</strong> Fungsionalitas ubah status dan hapus pesanan dinonaktifkan. Menggunakan data fallback statis.
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="admin-alert admin-alert-error">
        <span>❌</span> <?= $error ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="admin-alert admin-alert-success">
        <span>✅</span> <?= $success ?>
    </div>
<?php endif; ?>

<!-- ===== FILTER BAR ===== -->
<div class="filter-card">
    <form method="GET" action="" style="display: flex; width: 100%; gap: 16px; align-items: flex-end;">
        <div class="admin-form-group">
            <label class="admin-form-label" for="status_filter">Filter Status</label>
            <select id="status_filter" name="status_filter" class="admin-form-control">
                <option value="">Tampilkan Semua Status</option>
                <option value="pending" <?= $filterStatus === 'pending' ? 'selected' : '' ?>>⏳ Pending (Menunggu Pembayaran)</option>
                <option value="processing" <?= $filterStatus === 'processing' ? 'selected' : '' ?>>⚙️ Processing (Diproses)</option>
                <option value="success" <?= $filterStatus === 'success' ? 'selected' : '' ?>>✅ Success (Berhasil)</option>
                <option value="failed" <?= $filterStatus === 'failed' ? 'selected' : '' ?>>❌ Failed (Gagal)</option>
            </select>
        </div>
        <button type="submit" class="btn-admin btn-admin-secondary" style="height: 44px; padding: 0 24px;">
            Filter
        </button>
        <?php if ($filterStatus !== null): ?>
            <a href="orders.php" class="btn-admin btn-admin-secondary" style="height: 44px; display: flex; align-items: center; text-decoration: none;">Reset</a>
        <?php endif; ?>
    </form>
</div>

<div class="admin-grid-layout">
    <!-- ===== KIRI: LIST PESANAN ===== -->
    <div class="admin-card" style="flex: 2;">
        <div class="admin-card-header">
            <h2>📋 Daftar Transaksi Masuk</h2>
            <span class="text-muted" style="font-size: 0.8rem;"><?= count($orders) ?> transaksi ditemukan</span>
        </div>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Game & Nominal</th>
                        <th>Player ID</th>
                        <th>Metode Bayar</th>
                        <th>Total Bayar</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center;" class="text-muted">Tidak ada transaksi ditemukan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $o): ?>
                            <tr>
                                <td style="font-family: monospace; font-weight: 700; color: var(--neon-cyan);">
                                    <?= htmlspecialchars($o['order_id']) ?>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span><?= $o['game_icon'] ?></span>
                                        <div>
                                            <div style="font-weight: 600;"><?= htmlspecialchars($o['game_name']) ?></div>
                                            <div class="text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars($o['product_name']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div><?= htmlspecialchars($o['user_game_id']) ?></div>
                                    <?php if (!empty($o['server_id'])): ?>
                                        <div class="text-muted" style="font-size: 0.75rem;">Server: <?= htmlspecialchars($o['server_id']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars(strtoupper(str_replace('_', ' ', $o['payment_method']))) ?></td>
                                <td style="font-weight: 600; color: var(--green);"><?= formatRupiah($o['total_price']) ?></td>
                                <td style="font-size: 0.75rem;" class="text-muted"><?= date('d M Y, H:i', strtotime($o['created_at'])) ?></td>
                                <td>
                                    <?php
                                    $statusClass = 'badge-pending';
                                    $statusLabel = 'Pending';
                                    if ($o['status'] === 'success') {
                                        $statusClass = 'badge-success';
                                        $statusLabel = 'Sukses';
                                    } elseif ($o['status'] === 'processing') {
                                        $statusClass = 'badge-processing';
                                        $statusLabel = 'Diproses';
                                    } elseif ($o['status'] === 'failed') {
                                        $statusClass = 'badge-failed';
                                        $statusLabel = 'Gagal';
                                    }
                                    ?>
                                    <span class="badge-admin <?= $statusClass ?>"><?= $statusLabel ?></span>
                                </td>
                                <td>
                                    <?php if ($dbActive): ?>
                                        <a href="?edit=<?= $o['order_id'] ?><?= $filterStatus !== null ? '&status_filter=' . $filterStatus : '' ?>" class="btn-admin btn-admin-primary btn-admin-sm">Kelola</a>
                                        <a href="?delete=<?= $o['order_id'] ?><?= $filterStatus !== null ? '&status_filter=' . $filterStatus : '' ?>" class="btn-admin btn-admin-danger btn-admin-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus catatan pesanan ini?');">Hapus</a>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ===== KANAN: FORM UPDATE STATUS ===== -->
    <?php if ($editMode): ?>
        <div class="admin-card">
            <h2>⚙️ Kelola Transaksi</h2>
            <p class="text-muted" style="font-size: 0.8rem; margin-bottom: 20px;">
                Perbarui status transaksi dan tambahkan catatan admin.
            </p>

            <div style="background: rgba(0,0,0,0.15); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 16px; margin-bottom: 20px;">
                <div style="font-size: 0.78rem; text-transform: uppercase; color: var(--text-gray); margin-bottom: 8px;">Detail Pesanan</div>
                <div style="display: flex; flex-direction: column; gap: 8px; font-size: 0.85rem;">
                    <div><strong>ID:</strong> <span style="font-family: monospace; color: var(--neon-cyan);"><?= htmlspecialchars($editOrder['order_id']) ?></span></div>
                    <div><strong>Game:</strong> <?= $editOrder['game_icon'] ?> <?= htmlspecialchars($editOrder['game_name']) ?></div>
                    <div><strong>Produk:</strong> <?= htmlspecialchars($editOrder['product_name']) ?></div>
                    <div><strong>User ID:</strong> <?= htmlspecialchars($editOrder['user_game_id']) ?><?= $editOrder['server_id'] ? ' (Zone ' . htmlspecialchars($editOrder['server_id']) . ')' : '' ?></div>
                    <div><strong>Metode Bayar:</strong> <?= htmlspecialchars(strtoupper(str_replace('_', ' ', $editOrder['payment_method']))) ?></div>
                    <div><strong>Total:</strong> <span style="color: var(--green); font-weight: 600;"><?= formatRupiah($editOrder['total_price']) ?></span></div>
                    <div><strong>Tanggal:</strong> <?= date('d M Y, H:i', strtotime($editOrder['created_at'])) ?></div>
                </div>
            </div>

            <form method="POST" action="">
                <input type="hidden" name="update_status" value="1">
                <input type="hidden" name="order_id" value="<?= htmlspecialchars($editOrder['order_id']) ?>">

                <div class="admin-form-group">
                    <label class="admin-form-label" for="status">Ubah Status Transaksi *</label>
                    <select id="status" name="status" class="admin-form-control" required>
                        <option value="pending" <?= $editOrder['status'] === 'pending' ? 'selected' : '' ?>>⏳ Pending (Menunggu Pembayaran)</option>
                        <option value="processing" <?= $editOrder['status'] === 'processing' ? 'selected' : '' ?>>⚙️ Processing (Sedang Diproses)</option>
                        <option value="success" <?= $editOrder['status'] === 'success' ? 'selected' : '' ?>>✅ Success (Berhasil Dikirim)</option>
                        <option value="failed" <?= $editOrder['status'] === 'failed' ? 'selected' : '' ?>>❌ Failed (Transaksi Gagal)</option>
                    </select>
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="notes">Catatan Admin (Notes)</label>
                    <textarea id="notes" name="notes" class="admin-form-control" rows="4" placeholder="Masukkan detail tambahan (contoh: bukti pengiriman, alasan gagal, dll.)"><?= htmlspecialchars($editOrder['notes']) ?></textarea>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 24px;">
                    <button type="submit" class="btn-admin btn-admin-primary" style="flex: 1;">
                        Simpan Perubahan
                    </button>
                    <a href="orders.php<?= $filterStatus !== null ? '?status_filter=' . $filterStatus : '' ?>" class="btn-admin btn-admin-secondary" style="text-decoration: none;">Batal</a>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div class="admin-card">
            <h2>💡 Petunjuk Kelola</h2>
            <div style="font-size: 0.88rem; line-height: 1.6; color: var(--text-gray); display: flex; flex-direction: column; gap: 12px;">
                <p>Klik tombol <strong>Kelola</strong> pada baris pesanan untuk memperbarui status.</p>
                <div style="border-top: 1px solid var(--border-color); padding-top: 12px;">
                    <strong style="color: var(--text-white); display: block; margin-bottom: 4px;">Alur Status:</strong>
                    <ul style="padding-left: 20px; display: flex; flex-direction: column; gap: 6px;">
                        <li><span style="color: var(--amber);">Pending</span>: Menunggu pembayaran atau verifikasi manual transfer bank.</li>
                        <li><span style="color: var(--blue);">Processing</span>: Pembayaran diterima, diamond sedang dalam proses pengiriman.</li>
                        <li><span style="color: var(--green);">Success</span>: Diamond sukses dikirim ke akun player.</li>
                        <li><span style="color: var(--red);">Failed</span>: Pembayaran tidak valid / User ID salah dan dana dikembalikan.</li>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/admin-footer.php'; ?>
