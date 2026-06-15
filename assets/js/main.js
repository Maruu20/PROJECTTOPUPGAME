// ============================================
// GAMETOP — MAIN JAVASCRIPT
// ============================================

document.addEventListener('DOMContentLoaded', function () {

    // --- Mobile Menu Toggle ---
    const hamburger = document.getElementById('hamburger');
    const mobileMenu = document.getElementById('mobileMenu');
    if (hamburger && mobileMenu) {
        hamburger.addEventListener('click', () => {
            mobileMenu.classList.toggle('open');
        });
    }

    // --- Product Selection ---
    const productItems = document.querySelectorAll('.product-item');
    productItems.forEach(item => {
        item.addEventListener('click', function () {
            productItems.forEach(p => p.classList.remove('selected'));
            this.classList.add('selected');
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
                updateSummary();
            }
        });
    });

    // --- Payment Method Selection ---
    const paymentItems = document.querySelectorAll('.payment-item');
    paymentItems.forEach(item => {
        item.addEventListener('click', function () {
            paymentItems.forEach(p => p.classList.remove('selected'));
            this.classList.add('selected');
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
                updateSummary();
            }
            // Show sub-option if available
            const subOption = document.getElementById('paymentSubOption');
            if (subOption) {
                const options = this.dataset.options ? JSON.parse(this.dataset.options) : [];
                if (options.length > 0) {
                    subOption.innerHTML = buildSubOptions(options);
                    subOption.style.display = 'block';
                } else {
                    subOption.style.display = 'none';
                }
            }
        });
    });

    function buildSubOptions(options) {
        let html = '<div class="form-group"><label class="form-label">Pilih Provider</label><select class="form-control" name="payment_provider">';
        options.forEach(o => {
            html += `<option value="${o}">${o}</option>`;
        });
        html += '</select></div>';
        return html;
    }

    // --- Update Order Summary ---
    function updateSummary() {
        const selectedProduct = document.querySelector('.product-item.selected');
        const gameId   = document.getElementById('summaryGameId');
        const prodName = document.getElementById('summaryProduct');
        const prodPrice = document.getElementById('summaryPrice');
        const total    = document.getElementById('summaryTotal');
        const userId   = document.getElementById('userId');
        const userIdSummary = document.getElementById('summaryUserId');

        if (userIdSummary && userId) {
            userIdSummary.textContent = userId.value || '-';
        }
        if (selectedProduct && prodName && prodPrice && total) {
            const name  = selectedProduct.dataset.name;
            const price = parseInt(selectedProduct.dataset.price);
            prodName.textContent = name;
            const formatted = 'Rp ' + price.toLocaleString('id-ID');
            prodPrice.textContent = formatted;
            total.textContent = formatted;
        }
    }

    // Live update user id in summary
    const userIdInput = document.getElementById('userId');
    if (userIdInput) {
        userIdInput.addEventListener('input', updateSummary);
    }

    // --- Game Filter (Games Page) ---
    const filterBtns = document.querySelectorAll('.filter-btn');
    const gameCards  = document.querySelectorAll('.game-card-item');
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const cat = this.dataset.cat;
            gameCards.forEach(card => {
                if (cat === 'all' || card.dataset.cat === cat) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // --- Copy Order ID ---
    const copyBtn = document.getElementById('copyOrderId');
    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            const orderId = document.getElementById('orderIdText')?.textContent;
            if (orderId) {
                navigator.clipboard.writeText(orderId).then(() => {
                    copyBtn.textContent = '✅ Tersalin!';
                    setTimeout(() => { copyBtn.textContent = '📋 Salin'; }, 2000);
                });
            }
        });
    }

    // --- Form Validation (Topup) ---
    const topupForm = document.getElementById('topupForm');
    if (topupForm) {
        topupForm.addEventListener('submit', function (e) {
            const userId = document.getElementById('userId')?.value?.trim();
            const product = document.querySelector('.product-item.selected');
            const payment = document.querySelector('.payment-item.selected');

            if (!userId) {
                e.preventDefault();
                showAlert('Harap masukkan User ID kamu!', 'error');
                return;
            }
            if (!product) {
                e.preventDefault();
                showAlert('Harap pilih nominal top-up!', 'error');
                return;
            }
            if (!payment) {
                e.preventDefault();
                showAlert('Harap pilih metode pembayaran!', 'error');
                return;
            }
        });
    }

    function showAlert(msg, type) {
        const existing = document.querySelector('.js-alert');
        if (existing) existing.remove();
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} js-alert`;
        alert.textContent = msg;
        topupForm?.prepend(alert);
        setTimeout(() => alert.remove(), 4000);
    }

    // --- Animate numbers on hero ---
    const heroNums = document.querySelectorAll('.hero-stat strong[data-count]');
    heroNums.forEach(el => {
        const target = parseInt(el.dataset.count);
        const suffix = el.dataset.suffix || '';
        let current  = 0;
        const step   = Math.ceil(target / 40);
        const timer  = setInterval(() => {
            current += step;
            if (current >= target) { current = target; clearInterval(timer); }
            el.textContent = current.toLocaleString('id-ID') + suffix;
        }, 30);
    });
});
