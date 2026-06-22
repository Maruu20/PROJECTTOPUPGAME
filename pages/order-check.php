<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Cek Pesanan';
$result = null;
$error  = '';

// Data order dummy untuk demo
$demoOrders = [
    'ORD-ABC123-456' => [
        'order_id'   => 'ORD-ABC123-456',
        'game_name'  => 'Mobile Legends',
        'game_icon'  => '🗡️',
        'product'    => '257 Diamonds',
        'user_id'    => '123456789',
        'payment'    => 'GoPay',
        'total'      => 57000,
        'status'     => 'success',
        'created_at' => '15 Jun 2025, 14:32',
    ],
    'ORD-DEF456-789' => [
        'order_id'   => 'ORD-DEF456-789',
        'game_name'  => 'Free Fire',
        'game_icon'  => '🔥',
        'product'    => '355 Diamonds',
        'user_id'    => '987654321',
        'payment'    => 'Transfer BCA',
        'total'      => 69000,
        'status'     => 'pending',
        'created_at' => '15 Jun 2025, 13:10',
    ],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = strtoupper(sanitize($_POST['order_id'] ?? ''));
    if (empty($orderId)) {
        $error = 'Masukkan ID pesanan terlebih dahulu.';
    } else {
        // Cek database terlebih dahulu
        $db = getDB();
        $dbFound = false;
        if ($db) {
            $orderIdEscaped = $db->real_escape_string($orderId);
            $query = "SELECT o.*, g.name AS game_name, g.icon AS game_icon, p.name AS product_name 
                      FROM orders o
                      LEFT JOIN games g ON o.game_id = g.id
                      LEFT JOIN products p ON o.product_id = p.id
                      WHERE o.order_id = '$orderIdEscaped' LIMIT 1";
            $res = $db->query($query);
            if ($res && $res->num_rows > 0) {
                $row = $res->fetch_assoc();
                $result = [
                    'order_id'   => $row['order_id'],
                    'game_name'  => $row['game_name'] ?? 'Game Tidak Diketahui',
                    'game_icon'  => $row['game_icon'] ?? '🎮',
                    'product'    => $row['product_name'] ?? 'Produk Tidak Diketahui',
                    'user_id'    => $row['user_game_id'] . ($row['server_id'] ? ' (' . $row['server_id'] . ')' : ''),
                    'payment'    => strtoupper(str_replace('_', ' ', $row['payment_method'])),
                    'total'      => (int)$row['total_price'],
                    'status'     => $row['status'],
                    'created_at' => date('d M Y, H:i', strtotime($row['created_at'])),
                ];
                $dbFound = true;
            }
        }
        
        if (!$dbFound) {
            if (isset($demoOrders[$orderId])) {
                $result = $demoOrders[$orderId];
            } elseif (isset($_SESSION['order']) && $_SESSION['order']['order_id'] === $orderId) {
                $o = $_SESSION['order'];
                $result = [
                    'order_id'   => $o['order_id'],
                    'game_name'  => $o['game']['name'],
                    'game_icon'  => $o['game']['icon'],
                    'product'    => $o['product']['name'],
                    'user_id'    => $o['user_id'] . (!empty($o['server_id']) ? ' (' . $o['server_id'] . ')' : ''),
                    'payment'    => strtoupper(str_replace('_', ' ', $o['payment'])),
                    'total'      => $o['total'],
                    'status'     => $o['status'],
                    'created_at' => $o['created_at'],
                ];
            } else {
                $error = 'ID pesanan tidak ditemukan. Pastikan ID sudah benar.';
            }
        }
    }
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="page-header">
    <div class="container">
        <h1>🔍 Cek Status Pesanan</h1>
        <p>Masukkan ID pesanan untuk melihat status top-up kamu.</p>
    </div>
</div>

<section class="section" style="padding-top: 0;">
    <div class="container">
        <div class="order-check-card">
            <form method="POST">
                <div class="form-group">
                    <label class="form-label" for="order_id">ID Pesanan</label>
                    <input type="text" id="order_id" name="order_id"
                           class="form-control"
                           placeholder="Contoh: ORD-ABC123-456"
                           value="<?= isset($_POST['order_id']) ? htmlspecialchars($_POST['order_id']) : '' ?>"
                           style="text-transform: uppercase; font-family: monospace;">
                </div>

                <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <button type="submit" class="btn btn-primary btn-full">
                    Cek Pesanan →
                </button>
            </form>

            <div class="alert alert-info" style="margin-top: 20px; margin-bottom: 0; font-size: 0.8rem;">
                💡 <strong>Demo:</strong> Coba gunakan ID <code>ORD-ABC123-456</code> atau <code>ORD-DEF456-789</code>
            </div>
        </div>

        <!-- Hasil -->
        <?php if ($result): ?>
        <div class="result-card" style="max-width: 560px; margin: 0 auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="font-size: 1.1rem;">Detail Pesanan</h2>
                <?php
                $statusMap = [
                    'success' => ['label' => '✅ Berhasil', 'class' => 'status-success'],
                    'pending' => ['label' => '⏳ Menunggu', 'class' => 'status-pending'],
                    'failed'  => ['label' => '❌ Gagal',   'class' => 'status-failed'],
                ];
                $statusInfo = $statusMap[$result['status']] ?? $statusMap['pending'];
                ?>
                <span class="status-badge <?= $statusInfo['class'] ?>"><?= $statusInfo['label'] ?></span>
            </div>

            <div class="summary-rows">
                <div class="summary-row">
                    <span class="key">ID Pesanan</span>
                    <span class="val" style="font-family: monospace; font-size: 0.8rem; color: var(--cyan);">
                        <?= htmlspecialchars($result['order_id']) ?>
                    </span>
                </div>
                <div class="summary-row">
                    <span class="key">Game</span>
                    <span class="val"><?= $result['game_icon'] ?> <?= htmlspecialchars($result['game_name']) ?></span>
                </div>
                <div class="summary-row">
                    <span class="key">Nominal</span>
                    <span class="val"><?= htmlspecialchars($result['product']) ?></span>
                </div>
                <div class="summary-row">
                    <span class="key">User ID</span>
                    <span class="val"><?= htmlspecialchars($result['user_id']) ?></span>
                </div>
                <div class="summary-row">
                    <span class="key">Pembayaran</span>
                    <span class="val"><?= htmlspecialchars($result['payment']) ?></span>
                </div>
                <div class="summary-row">
                    <span class="key">Waktu</span>
                    <span class="val"><?= htmlspecialchars($result['created_at']) ?></span>
                </div>
                <div class="summary-row total">
                    <span class="key">Total Dibayar</span>
                    <span class="val"><?= formatRupiah($result['total']) ?></span>
                </div>
            </div>

            <?php if ($result['status'] === 'pending'): ?>
            <div class="alert alert-info" style="margin-top: 16px; margin-bottom: 0;">
                Pesanan kamu sedang dalam proses verifikasi pembayaran. Biasanya membutuhkan 5–15 menit.
            </div>
            <?php elseif ($result['status'] === 'success'): ?>
            <div class="alert alert-success" style="margin-top: 16px; margin-bottom: 0;">
                Top-up berhasil! Item sudah masuk ke akun game kamu.
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
