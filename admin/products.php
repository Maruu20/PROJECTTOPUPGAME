<?php
$adminTitle = 'Kelola Produk';
include __DIR__ . '/admin-header.php';

$db = getDB();
$dbActive = ($db !== null);
$error = '';
$success = '';

// Ambil list game untuk dropdown select form & filter
$games = getGames(false);

// Tentukan filter game teraktif
$filterGameId = isset($_GET['game_filter']) ? (int)$_GET['game_filter'] : 0;

// Mode form: default adalah tambah, jika ada GET edit maka ganti mode edit
$editMode = false;
$editProduct = null;
if ($dbActive && isset($_GET['edit'])) {
    $productId = (int)$_GET['edit'];
    $editProduct = getProductById($productId);
    if ($editProduct) {
        $editMode = true;
    }
}

// Handle CRUD Actions
if ($dbActive && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = sanitize($_POST['action'] ?? '');

    if ($action === 'add' || $action === 'edit') {
        $game_id    = (int)($_POST['game_id'] ?? 0);
        $name       = sanitize($_POST['name'] ?? '');
        $amount     = (int)($_POST['amount'] ?? 0);
        $price      = (int)($_POST['price'] ?? 0);
        $bonus      = (int)($_POST['bonus'] ?? 0);
        $is_active  = isset($_POST['is_active']) ? 1 : 0;
        $sort_order = (int)($_POST['sort_order'] ?? 0);

        if ($game_id <= 0 || empty($name) || $amount <= 0 || $price <= 0) {
            $error = 'Semua field wajib diisi, nominal dan harga harus lebih besar dari 0.';
        } else {
            if ($action === 'add') {
                $stmt = $db->prepare("INSERT INTO products (game_id, name, amount, price, bonus, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isiiiii", $game_id, $name, $amount, $price, $bonus, $is_active, $sort_order);
                if ($stmt->execute()) {
                    $success = "Produk <strong>" . htmlspecialchars($name) . "</strong> berhasil ditambahkan.";
                } else {
                    $error = 'Gagal menyimpan ke database: ' . $db->error;
                }
            } else {
                $id = (int)$_POST['id'];
                $stmt = $db->prepare("UPDATE products SET game_id = ?, name = ?, amount = ?, price = ?, bonus = ?, is_active = ?, sort_order = ? WHERE id = ?");
                $stmt->bind_param("isiiiiii", $game_id, $name, $amount, $price, $bonus, $is_active, $sort_order, $id);
                if ($stmt->execute()) {
                    $success = "Perubahan produk <strong>" . htmlspecialchars($name) . "</strong> berhasil disimpan.";
                    $editMode = false;
                    $editProduct = null;
                } else {
                    $error = 'Gagal memperbarui database: ' . $db->error;
                }
            }
        }
    }
}

// Handle Delete Action
if ($dbActive && isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $prodToDelete = getProductById($id);
    if ($prodToDelete) {
        $db->query("DELETE FROM products WHERE id = $id");
        $success = "Produk <strong>" . htmlspecialchars($prodToDelete['name']) . "</strong> berhasil dihapus.";
    }
}

// Fetch products list
if ($dbActive) {
    if ($filterGameId > 0) {
        $productsList = getAllProducts($filterGameId);
    } else {
        $productsList = getAllProducts();
    }
} else {
    // Fallback static data
    $productsList = [];
    foreach ($games as $g) {
        $prods = getProducts($g['slug'], false);
        foreach ($prods as $p) {
            $productsList[] = [
                'id' => $p['id'],
                'game_id' => $g['id'],
                'game_name' => $g['name'],
                'name' => $p['name'],
                'amount' => $p['amount'],
                'price' => $p['price'],
                'bonus' => $p['bonus'],
                'is_active' => $p['is_active'],
                'sort_order' => $p['sort_order']
            ];
        }
    }
}
?>

<?php if (!$dbActive): ?>
    <div class="admin-alert admin-alert-error">
        <span>⚠️</span> <strong>Koneksi Database tidak aktif!</strong> Fungsionalitas tambah, edit, dan hapus dinonaktifkan. Menggunakan data fallback statis.
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
            <label class="admin-form-label" for="game_filter">Saring berdasarkan Game</label>
            <select id="game_filter" name="game_filter" class="admin-form-control">
                <option value="0">Tampilkan Semua Game</option>
                <?php foreach ($games as $g): ?>
                    <option value="<?= $g['id'] ?>" <?= $filterGameId === $g['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g['icon'] . ' ' . $g['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn-admin btn-admin-secondary" style="height: 44px; padding: 0 24px;">
            Filter
        </button>
        <?php if ($filterGameId > 0): ?>
            <a href="products.php" class="btn-admin btn-admin-secondary" style="height: 44px; display: flex; align-items: center; text-decoration: none;">Reset</a>
        <?php endif; ?>
    </form>
</div>

<div class="admin-grid-layout">
    <!-- ===== KIRI: LIST PRODUK ===== -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h2>💎 Daftar Nominal Produk</h2>
            <span class="text-muted" style="font-size: 0.8rem;"><?= count($productsList) ?> item terdaftar</span>
        </div>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">Urutan</th>
                        <th>Game</th>
                        <th>Nominal Produk</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                        <th>Bonus</th>
                        <th>Status</th>
                        <th style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($productsList)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center;" class="text-muted">Tidak ada produk untuk saringan ini.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($productsList as $p): ?>
                            <tr>
                                <td style="text-align: center; font-weight: 700;"><?= $p['sort_order'] ?></td>
                                <td style="font-weight: 600;"><?= htmlspecialchars($p['game_name']) ?></td>
                                <td><?= htmlspecialchars($p['name']) ?></td>
                                <td><?= number_format($p['amount']) ?></td>
                                <td style="font-weight: 600; color: var(--neon-cyan);"><?= formatRupiah($p['price']) ?></td>
                                <td style="color: var(--green); font-weight: 600;">+<?= number_format($p['bonus']) ?></td>
                                <td>
                                    <span class="badge-admin <?= $p['is_active'] ? 'badge-success' : 'badge-failed' ?>" style="padding: 2px 8px; font-size: 0.7rem;">
                                        <?= $p['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($dbActive): ?>
                                        <a href="?edit=<?= $p['id'] ?><?= $filterGameId > 0 ? '&game_filter=' . $filterGameId : '' ?>" class="btn-admin btn-admin-primary btn-admin-sm">Edit</a>
                                        <a href="?delete=<?= $p['id'] ?><?= $filterGameId > 0 ? '&game_filter=' . $filterGameId : '' ?>" class="btn-admin btn-admin-danger btn-admin-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus nominal produk ini?');">Hapus</a>
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

    <!-- ===== KANAN: FORM DINAMIS ===== -->
    <div class="admin-card">
        <h2><?= $editMode ? '⚙️ Edit Nominal' : '💎 Tambah Nominal' ?></h2>
        <p class="text-muted" style="font-size: 0.8rem; margin-bottom: 20px;">
            <?= $editMode ? 'Modifikasi detail nominal produk terpilih.' : 'Definisikan pilihan top-up baru untuk dipublikasikan.' ?>
        </p>

        <form method="POST" action="">
            <input type="hidden" name="action" value="<?= $editMode ? 'edit' : 'add' ?>">
            <?php if ($editMode): ?>
                <input type="hidden" name="id" value="<?= $editProduct['id'] ?>">
            <?php endif; ?>

            <div class="admin-form-group">
                <label class="admin-form-label" for="game_id">Pilih Game *</label>
                <select id="game_id" name="game_id" class="admin-form-control" required <?= !$dbActive ? 'disabled' : '' ?>>
                    <option value="">-- Pilih Game Rujukan --</option>
                    <?php foreach ($games as $g): ?>
                        <option value="<?= $g['id'] ?>" <?= (($editMode && $editProduct['game_id'] == $g['id']) || (!$editMode && $filterGameId == $g['id'])) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($g['icon'] . ' ' . $g['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="admin-form-group">
                <label class="admin-form-label" for="name">Nama/Label Nominal *</label>
                <input type="text" id="name" name="name" class="admin-form-control" placeholder="Contoh: 86 Diamonds / 325 UC"
                       value="<?= $editMode ? htmlspecialchars($editProduct['name']) : '' ?>" required <?= !$dbActive ? 'disabled' : '' ?>>
            </div>

            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label" for="amount">Jumlah Item *</label>
                    <input type="number" id="amount" name="amount" class="admin-form-control" placeholder="86"
                           value="<?= $editMode ? (int)$editProduct['amount'] : '' ?>" required <?= !$dbActive ? 'disabled' : '' ?>>
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label" for="bonus">Bonus Item</label>
                    <input type="number" id="bonus" name="bonus" class="admin-form-control" placeholder="0"
                           value="<?= $editMode ? (int)$editProduct['bonus'] : '0' ?>" <?= !$dbActive ? 'disabled' : '' ?>>
                </div>
            </div>

            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label" for="price">Harga Jual (Rp) *</label>
                    <input type="number" id="price" name="price" class="admin-form-control" placeholder="19000"
                           value="<?= $editMode ? (int)$editProduct['price'] : '' ?>" required <?= !$dbActive ? 'disabled' : '' ?>>
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label" for="sort_order">Urutan Tampilan</label>
                    <input type="number" id="sort_order" name="sort_order" class="admin-form-control" placeholder="0"
                           value="<?= $editMode ? (int)$editProduct['sort_order'] : '0' ?>" <?= !$dbActive ? 'disabled' : '' ?>>
                </div>
            </div>

            <div class="admin-form-group">
                <label class="admin-toggle-container">
                    <input type="checkbox" name="is_active" class="admin-toggle-input" <?= (!$editMode || $editProduct['is_active']) ? 'checked' : '' ?> <?= !$dbActive ? 'disabled' : '' ?>>
                    <div class="admin-toggle-switch"></div>
                    <span style="font-size: 0.85rem; font-weight: 500;">Aktif (Ditawarkan di Form Top-Up)</span>
                </label>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 24px;">
                <button type="submit" class="btn-admin btn-admin-primary" style="flex: 1;" <?= !$dbActive ? 'disabled' : '' ?>>
                    <?= $editMode ? 'Simpan Perubahan' : 'Tambah Nominal' ?>
                </button>
                <?php if ($editMode): ?>
                    <a href="products.php<?= $filterGameId > 0 ? '?game_filter=' . $filterGameId : '' ?>" class="btn-admin btn-admin-secondary" style="text-decoration: none;">Batal</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<script>
// Auto fill nominal label dari input jumlah
const amountInput = document.getElementById('amount');
const bonusInput = document.getElementById('bonus');
const prodNameInput = document.getElementById('name');
const gameSelect = document.getElementById('game_id');

if (amountInput && prodNameInput) {
    const updateProdName = () => {
        if (editModeActive()) return; // Jangan override jika sedang edit
        const amount = amountInput.value;
        const bonus = bonusInput.value || 0;
        const selectedGameText = gameSelect.options[gameSelect.selectedIndex]?.text || '';
        
        let currency = 'Item';
        if (selectedGameText.includes('Legends')) currency = 'Diamonds';
        else if (selectedGameText.includes('Free')) currency = 'Diamonds';
        else if (selectedGameText.includes('PUBG')) currency = 'UC';
        else if (selectedGameText.includes('Genshin')) currency = 'Primogems';
        else if (selectedGameText.includes('Valorant')) currency = 'VP';
        else if (selectedGameText.includes('Star')) currency = 'Oneiric Shards';

        if (amount) {
            prodNameInput.value = amount + (parseInt(bonus) > 0 ? ' +' + bonus : '') + ' ' + currency;
        }
    };

    const editModeActive = () => {
        return <?= $editMode ? 'true' : 'false' ?>;
    };

    amountInput.addEventListener('input', updateProdName);
    bonusInput.addEventListener('input', updateProdName);
    gameSelect.addEventListener('change', updateProdName);
}
</script>

<?php include __DIR__ . '/admin-footer.php'; ?>
