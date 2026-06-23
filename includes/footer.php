<!-- ===== FOOTER ===== -->
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="<?= APP_URL ?>/index.php" class="logo">
                    <span class="logo-icon">⚡</span>
                    <span class="logo-text"><?= APP_NAME ?></span>
                </a>
                <p>Platform top-up game terpercaya di Indonesia. Proses cepat, harga terjangkau, layanan 24 jam.</p>
                <div class="socials">
                    <a href="#" aria-label="Instagram">📸</a>
                    <a href="#" aria-label="WhatsApp">💬</a>
                    <a href="#" aria-label="Twitter">🐦</a>
                </div>
            </div>
            <div class="footer-col">
                <h4>Game Populer</h4>
                <ul>
                    <li><a href="<?= APP_URL ?>/pages/topup.php?game=mobile-legends">Mobile Legends</a></li>
                    <li><a href="<?= APP_URL ?>/pages/topup.php?game=free-fire">Free Fire</a></li>
                    <li><a href="<?= APP_URL ?>/pages/topup.php?game=pubg-mobile">PUBG Mobile</a></li>
                    <li><a href="<?= APP_URL ?>/pages/topup.php?game=genshin-impact">Genshin Impact</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Bantuan</h4>
                <ul>
                    <li><a href="<?= APP_URL ?>/pages/order-check.php">Cek Pesanan</a></li>
                    <li><a href="#">Cara Top-Up</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Hubungi Kami</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Pembayaran</h4>
                <div class="payment-badges">
                    <span>🏦 BCA</span>
                    <span>🏦 BNI</span>
                    <span>💳 GoPay</span>
                    <span>💳 OVO</span>
                    <span>💳 DANA</span>
                    <span>📲 QRIS</span>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> <?= APP_NAME ?>. Dibuat dengan ❤️ untuk para gamer Indonesia.</p>
        </div>
    </div>
</footer>

<!-- ===== FLOATING COOKIE CONSENT BANNER ===== -->
<div class="cookie-banner" id="cookieBanner" style="display: none;">
    <div class="cookie-header">
        <span class="cookie-header-icon">🍪</span>
        <h4 class="cookie-header-title">Persetujuan Cookie</h4>
    </div>
    <div class="cookie-body">
        Kami menggunakan cookie untuk meningkatkan pengalaman Anda di GameTop. Dengan melanjutkan, Anda menyetujui penggunaan cookie kami sesuai dengan <a href="#">Kebijakan Privasi</a> kami.
    </div>
    <div class="cookie-actions">
        <button class="cookie-btn cookie-btn-settings" id="cookieSettingsBtn">Setelan</button>
        <button class="cookie-btn cookie-btn-decline" id="cookieDeclineBtn">Tolak Semua</button>
        <button class="cookie-btn cookie-btn-allow" id="cookieAllowBtn">Setuju Semua</button>
    </div>
</div>

<script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body>
</html>
