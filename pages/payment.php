<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Cek session order
if (!isset($_SESSION['order'])) {
    redirect(APP_URL . '/pages/games.php');
}

$order = $_SESSION['order'];
$pageTitle = 'Konfirmasi Pembayaran';

// Handle konfirmasi pembayaran
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    // Di sini normalnya ada integrasi payment gateway
    // Untuk demo: simulasikan pembayaran berhasil
    $_SESSION['order']['status'] = 'success';
    redirect(APP_URL . '/pages/success.php');
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<section class="section">
    <div class="container" style="max-width: 640px;">
        <div class="breadcrumb" style="margin-bottom: 24px;">
            <a href="<?= APP_URL ?>/index.php">Beranda</a>
            <span>›</span>
            <span>Konfirmasi Pembayaran</span>
        </div>

        <div class="topup-card" style="margin-bottom: 20px;">
            <div style="text-align: center; padding: 12px 0 24px;">
                <div style="font-size: 2.5rem; margin-bottom: 12px;">⏳</div>
                <h1 style="font-size: 1.4rem; margin-bottom: 8px;">Menunggu Pembayaran</h1>
                <p style="color: var(--text-muted); font-size: 0.875rem;">
                    Selesaikan pembayaran sebelum waktu habis
                </p>
                <div style="background: var(--amber-dim); border: 1px solid rgba(245,158,11,0.3);
                            border-radius: var(--radius-sm); padding: 12px 20px; margin-top: 16px;
                            font-family: 'Syne', sans-serif; font-size: 1.4rem; font-weight: 800;
                            color: var(--amber);" id="countdown">15:00</div>
            </div>

            <!-- Detail Pesanan -->
            <div class="summary-rows">
                <div class="summary-row">
                    <span class="key">ID Pesanan</span>
                    <span class="val" style="font-family: monospace; font-size: 0.8rem; color: var(--cyan);">
                        <?= htmlspecialchars($order['order_id']) ?>
                    </span>
                </div>
                <div class="summary-row">
                    <span class="key">Game</span>
                    <span class="val"><?= $order['game']['icon'] ?> <?= htmlspecialchars($order['game']['name']) ?></span>
                </div>
                <div class="summary-row">
                    <span class="key">User ID</span>
                    <span class="val"><?= htmlspecialchars($order['user_id']) ?></span>
                </div>
                <div class="summary-row">
                    <span class="key">Nominal</span>
                    <span class="val"><?= htmlspecialchars($order['product']['name']) ?></span>
                </div>
                <div class="summary-row">
                    <span class="key">Metode Bayar</span>
                    <span class="val"><?= htmlspecialchars(strtoupper(str_replace('_', ' ', $order['payment']))) ?></span>
                </div>
                <div class="summary-row total">
                    <span class="key">Total Bayar</span>
                    <span class="val"><?= formatRupiah($order['total']) ?></span>
                </div>
            </div>
        </div>

        <!-- Instruksi Pembayaran -->
        <div class="topup-card" style="margin-bottom: 20px;">
            <h2 style="font-size: 1rem; margin-bottom: 16px;">📋 Instruksi Pembayaran</h2>
            <?php
            $paymentInstructions = [
                'transfer_bank' => [
                    'Buka aplikasi mobile banking kamu.',
                    'Pilih menu Transfer → ke Rekening Bank.',
                    'Masukkan nomor rekening: <strong>1234-5678-9012</strong> (BCA a/n GameTop Indonesia).',
                    'Nominal transfer: <strong>' . formatRupiah($order['total']) . '</strong>.',
                    'Simpan bukti transfer dan klik tombol "Konfirmasi" di bawah.',
                ],
                'ewallet' => [
                    'Buka aplikasi e-wallet kamu (GoPay / OVO / DANA / ShopeePay).',
                    'Pilih menu Transfer atau Kirim Uang.',
                    'Nomor tujuan: <strong>0812-3456-7890</strong> (a/n GameTop).',
                    'Nominal: <strong>' . formatRupiah($order['total']) . '</strong>.',
                    'Screenshot bukti transfer dan klik "Konfirmasi" di bawah.',
                ],
                'pulsa' => [
                    'Kirim pulsa senilai <strong>' . formatRupiah($order['total']) . '</strong>.',
                    'Nomor tujuan: <strong>0812-3456-7890</strong> (Telkomsel/Indosat/XL).',
                    'Pulsa akan dikonfirmasi otomatis dalam 5-10 menit.',
                    'Klik tombol "Konfirmasi" setelah mengirim pulsa.',
                ],
                'qris' => [
                    'Buka kamera atau fitur scan di aplikasi bank / e-wallet kamu.',
                    'Scan kode QRIS di bawah ini.',
                    'Nominal akan terisi otomatis: <strong>' . formatRupiah($order['total']) . '</strong>.',
                    'Konfirmasi pembayaran dan klik "Konfirmasi" di bawah.',
                ],
            ];
            $instructions = $paymentInstructions[$order['payment']] ?? $paymentInstructions['transfer_bank'];
            ?>
            <ol style="padding-left: 20px; display: flex; flex-direction: column; gap: 10px;">
                <?php foreach ($instructions as $step): ?>
                <li style="font-size: 0.875rem; color: var(--text-dim); line-height: 1.6;">
                    <?= $step ?>
                </li>
                <?php endforeach; ?>
            </ol>

            <?php if ($order['payment'] === 'qris'): ?>
            <div style="text-align: center; margin-top: 20px;">
                <div style="background: white; display: inline-block; padding: 12px; border-radius: var(--radius-sm);">
                    <div style="width: 160px; height: 160px; background: linear-gradient(135deg, #000 25%, transparent 25%) -10px 0,
                         linear-gradient(225deg, #000 25%, transparent 25%) -10px 0,
                         linear-gradient(315deg, #000 25%, transparent 25%),
                         linear-gradient(45deg, #000 25%, transparent 25%);
                         background-size: 20px 20px; background-color: #eee;
                         display: flex; align-items: center; justify-content: center;
                         font-size: 3rem;">
                        📲
                    </div>
                </div>
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 8px;">QRIS — Berlaku 15 menit</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Tombol Aksi -->
        <form method="POST">
            <button type="submit" name="confirm" class="btn btn-primary btn-full btn-lg" style="margin-bottom: 12px;">
                ✅ Sudah Bayar — Konfirmasi
            </button>
        </form>
        <a href="<?= APP_URL ?>/pages/topup.php?game=<?= $order['game']['slug'] ?>"
           class="btn btn-outline btn-full">← Batalkan & Kembali</a>

        <p style="text-align: center; font-size: 0.75rem; color: var(--text-muted); margin-top: 16px;">
            Butuh bantuan? <a href="#" style="color: var(--cyan);">Hubungi CS kami</a>
        </p>
    </div>
</section>

<script>
// Countdown timer 15 menit
let totalSeconds = 15 * 60;
const el = document.getElementById('countdown');
const timer = setInterval(() => {
    if (totalSeconds <= 0) { clearInterval(timer); el.textContent = 'WAKTU HABIS'; return; }
    totalSeconds--;
    const m = Math.floor(totalSeconds / 60).toString().padStart(2, '0');
    const s = (totalSeconds % 60).toString().padStart(2, '0');
    el.textContent = m + ':' + s;
}, 1000);
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
