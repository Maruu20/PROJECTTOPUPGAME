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

    // --- Game Catalog Filter, Search, Sort & Alphabet A-Z ---
    const gameSearch = document.getElementById('gameSearch');
    const clearSearch = document.getElementById('clearSearch');
    const gameSort = document.getElementById('gameSort');
    const filterBtns = document.querySelectorAll('.filter-btn');
    const alphabetList = document.getElementById('alphabetList');
    const alphabetBtns = document.querySelectorAll('.alphabet-btn');
    const gameCards = document.querySelectorAll('.game-card-item');
    const gamesGrid = document.getElementById('gamesGrid');

    let activeCategory = 'all';
    let activeLetter = 'all';
    let searchQuery = '';

    function filterAndSortGames() {
        if (!gameCards.length || !gamesGrid) return;

        // 1. FILTERING
        let visibleCards = [];
        
        gameCards.forEach(card => {
            const cat = card.dataset.cat;
            const name = card.dataset.name.toLowerCase();
            const letter = card.dataset.letter;

            const matchesCategory = (activeCategory === 'all' || cat === activeCategory);
            const matchesLetter = (activeLetter === 'all' || letter === activeLetter);
            const matchesSearch = (searchQuery === '' || name.includes(searchQuery));

            if (matchesCategory && matchesLetter && matchesSearch) {
                card.style.display = '';
                visibleCards.push(card);
            } else {
                card.style.display = 'none';
            }
        });

        // Update Alphabet Buttons Disabled State based on activeCategory
        if (alphabetBtns.length > 0) {
            alphabetBtns.forEach(btn => {
                const letter = btn.dataset.letter;
                if (letter === 'all') return;
                
                // Check if there are any games with this letter in the current category
                const hasGamesInCat = Array.from(gameCards).some(card => {
                    const cardCat = card.dataset.cat;
                    const cardLetter = card.dataset.letter;
                    const matchesCat = (activeCategory === 'all' || cardCat === activeCategory);
                    return matchesCat && (cardLetter === letter);
                });

                if (hasGamesInCat) {
                    btn.classList.remove('disabled');
                } else {
                    btn.classList.add('disabled');
                    // If the active letter is now disabled, reset active letter filter
                    if (activeLetter === letter) {
                        btn.classList.remove('active');
                        const allBtn = alphabetList.querySelector('[data-letter="all"]');
                        if (allBtn) allBtn.classList.add('active');
                        activeLetter = 'all';
                    }
                }
            });
        }

        // 2. SORTING
        const sortVal = gameSort ? gameSort.value : 'az';
        const cardsArray = Array.from(gameCards);
        
        cardsArray.sort((a, b) => {
            if (sortVal === 'az') {
                return a.dataset.name.localeCompare(b.dataset.name);
            } else if (sortVal === 'za') {
                return b.dataset.name.localeCompare(a.dataset.name);
            } else if (sortVal === 'populer') {
                return parseInt(b.dataset.popular) - parseInt(a.dataset.popular);
            }
            return 0;
        });

        // Append sorted cards back to grid
        cardsArray.forEach(card => {
            gamesGrid.appendChild(card);
        });

        // Show/hide empty state if no games visible
        let emptyState = document.getElementById('emptyState');
        if (visibleCards.length === 0) {
            if (!emptyState) {
                emptyState = document.createElement('div');
                emptyState.id = 'emptyState';
                emptyState.style.gridColumn = '1 / -1';
                emptyState.style.padding = '64px 0';
                emptyState.style.textAlign = 'center';
                emptyState.style.color = 'var(--text-muted)';
                emptyState.innerHTML = `
                    <div style="font-size: 3rem; margin-bottom: 12px;">🔍</div>
                    <h3 style="font-family: 'Montserrat', sans-serif; font-weight: 700; color: #fff; margin-bottom: 8px;">Game Tidak Ditemukan</h3>
                    <p>Coba gunakan kata kunci lain atau ubah filter kategori Anda.</p>
                `;
                gamesGrid.appendChild(emptyState);
            } else {
                emptyState.style.display = '';
            }
        } else if (emptyState) {
            emptyState.style.display = 'none';
        }
    }

    // Category Buttons
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            activeCategory = this.dataset.cat;
            filterAndSortGames();
        });
    });

    // Alphabet Buttons
    alphabetBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            if (this.classList.contains('disabled')) return;
            alphabetBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            activeLetter = this.dataset.letter;
            filterAndSortGames();
        });
    });

    // Search Input
    if (gameSearch) {
        gameSearch.addEventListener('input', function () {
            searchQuery = this.value.trim().toLowerCase();
            if (clearSearch) {
                clearSearch.style.display = searchQuery ? 'flex' : 'none';
            }
            filterAndSortGames();
        });
    }

    // Clear Search Button
    if (clearSearch) {
        clearSearch.addEventListener('click', function () {
            if (gameSearch) {
                gameSearch.value = '';
                searchQuery = '';
                this.style.display = 'none';
                filterAndSortGames();
                gameSearch.focus();
            }
        });
    }

    // Sort Dropdown
    if (gameSort) {
        gameSort.addEventListener('change', filterAndSortGames);
    }

    // --- Cookie Consent Banner Logic ---
    const cookieBanner = document.getElementById('cookieBanner');
    const cookieAllowBtn = document.getElementById('cookieAllowBtn');
    const cookieDeclineBtn = document.getElementById('cookieDeclineBtn');
    const cookieSettingsBtn = document.getElementById('cookieSettingsBtn');

    if (cookieBanner) {
        const cookieStatus = localStorage.getItem('cookieConsent');
        if (!cookieStatus) {
            setTimeout(() => {
                cookieBanner.style.display = 'flex';
            }, 800);
        }

        const hideBanner = (status) => {
            localStorage.setItem('cookieConsent', status);
            cookieBanner.style.animation = 'slideOutDown 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards';
            setTimeout(() => {
                cookieBanner.style.display = 'none';
            }, 400);
        };

        if (cookieAllowBtn) {
            cookieAllowBtn.addEventListener('click', () => hideBanner('allow'));
        }
        if (cookieDeclineBtn) {
            cookieDeclineBtn.addEventListener('click', () => hideBanner('decline'));
        }
        if (cookieSettingsBtn) {
            cookieSettingsBtn.addEventListener('click', () => {
                alert('Pengaturan Cookie: Cookie fungsional dasar diaktifkan untuk menyimpan preferensi Anda.');
                hideBanner('settings');
            });
        }
    }

    // Initial Filter and Sort
    filterAndSortGames();

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

    // --- Premium Banner Slider ---
    const sliderContainer = document.querySelector('.banner-slider-container');
    if (sliderContainer) {
        const sliderWrapper = sliderContainer.querySelector('.banner-slider-wrapper');
        const slides = sliderContainer.querySelectorAll('.banner-slide');
        const dots = sliderContainer.querySelectorAll('.banner-slider-dot');
        const prevBtn = sliderContainer.querySelector('.banner-slider-arrow.prev');
        const nextBtn = sliderContainer.querySelector('.banner-slider-arrow.next');
        
        let activeIdx = 0;
        let slideInterval = null;
        const intervalTime = 5000; // 5 seconds
        
        function updateSlider(index) {
            if (slides.length === 0) return;
            
            // Wrap index
            if (index < 0) {
                activeIdx = slides.length - 1;
            } else if (index >= slides.length) {
                activeIdx = 0;
            } else {
                activeIdx = index;
            }
            
            // Translate wrapper
            if (sliderWrapper) {
                sliderWrapper.style.transform = `translateX(-${activeIdx * 100}%)`;
            }
            
            // Update dots
            dots.forEach((dot, idx) => {
                if (idx === activeIdx) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });
        }
        
        function startAutoPlay() {
            stopAutoPlay();
            slideInterval = setInterval(() => {
                updateSlider(activeIdx + 1);
            }, intervalTime);
        }
        
        function stopAutoPlay() {
            if (slideInterval) {
                clearInterval(slideInterval);
                slideInterval = null;
            }
        }
        
        // Event Listeners
        if (prevBtn) {
            prevBtn.addEventListener('click', function() {
                updateSlider(activeIdx - 1);
                startAutoPlay();
            });
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', function() {
                updateSlider(activeIdx + 1);
                startAutoPlay();
            });
        }
        
        dots.forEach((dot, idx) => {
            dot.addEventListener('click', function() {
                updateSlider(idx);
                startAutoPlay();
            });
        });
        
        sliderContainer.addEventListener('mouseenter', stopAutoPlay);
        sliderContainer.addEventListener('mouseleave', startAutoPlay);
        
        // Initialize
        updateSlider(0);
        startAutoPlay();
    }
});
