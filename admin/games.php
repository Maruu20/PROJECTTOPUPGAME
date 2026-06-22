<?php
$adminTitle = 'Kelola Game';
include __DIR__ . '/admin-header.php';

$db = getDB();
$dbActive = ($db !== null);
$error = '';
$success = '';

// Mode form: default adalah tambah, jika ada GET edit maka ganti mode edit
$editMode = false;
$editGame = null;
if ($dbActive && isset($_GET['edit'])) {
    $gameId = (int)$_GET['edit'];
    $editGame = getGameById($gameId);
    if ($editGame) {
        $editMode = true;
    }
}

// Handle CRUD Actions
if ($dbActive && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = sanitize($_POST['action'] ?? '');

    if ($action === 'add' || $action === 'edit') {
        $name       = sanitize($_POST['name'] ?? '');
        $slug       = sanitize($_POST['slug'] ?? '');
        $category   = sanitize($_POST['category'] ?? '');
        $icon       = sanitize($_POST['icon'] ?? '🎮');
        $color      = sanitize($_POST['color'] ?? '#00e5ff');
        $is_popular = isset($_POST['is_popular']) ? 1 : 0;
        $is_active  = isset($_POST['is_active']) ? 1 : 0;
        $sort_order = (int)($_POST['sort_order'] ?? 0);

        if (empty($name) || empty($slug) || empty($category)) {
            $error = 'Nama, Slug, dan Kategori wajib diisi.';
        } else {
            if ($action === 'add') {
                // Periksa duplikasi slug
                $check = $db->query("SELECT id FROM games WHERE slug = '" . $db->real_escape_string($slug) . "'");
                if ($check && $check->num_rows > 0) {
                    $error = 'Slug game sudah digunakan, gunakan slug unik lain.';
                } else {
                    $stmt = $db->prepare("INSERT INTO games (slug, name, category, icon, color, is_popular, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssssiii", $slug, $name, $category, $icon, $color, $is_popular, $is_active, $sort_order);
                    if ($stmt->execute()) {
                        $success = "Game <strong>" . htmlspecialchars($name) . "</strong> berhasil ditambahkan.";
                    } else {
                        $error = 'Gagal menyimpan ke database: ' . $db->error;
                    }
                }
            } else {
                $id = (int)$_POST['id'];
                // Periksa duplikasi slug pada id lain
                $check = $db->query("SELECT id FROM games WHERE slug = '" . $db->real_escape_string($slug) . "' AND id != $id");
                if ($check && $check->num_rows > 0) {
                    $error = 'Slug game sudah digunakan oleh game lain.';
                } else {
                    $stmt = $db->prepare("UPDATE games SET slug = ?, name = ?, category = ?, icon = ?, color = ?, is_popular = ?, is_active = ?, sort_order = ? WHERE id = ?");
                    $stmt->bind_param("sssssiiii", $slug, $name, $category, $icon, $color, $is_popular, $is_active, $sort_order, $id);
                    if ($stmt->execute()) {
                        $success = "Perubahan game <strong>" . htmlspecialchars($name) . "</strong> berhasil disimpan.";
                        // Reset edit mode
                        $editMode = false;
                        $editGame = null;
                    } else {
                        $error = 'Gagal memperbarui database: ' . $db->error;
                    }
                }
            }
        }
    }
}

// Handle Delete Action
if ($dbActive && isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $gameToDelete = getGameById($id);
    if ($gameToDelete) {
        $db->query("DELETE FROM games WHERE id = $id");
        $success = "Game <strong>" . htmlspecialchars($gameToDelete['name']) . "</strong> berhasil dihapus.";
    }
}

// Fetch games list
$games = getGames(false); // Ambil semua game (aktif maupun nonaktif)
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

<div class="admin-grid-layout">
    <!-- ===== KIRI: LIST GAME ===== -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h2>🎮 Daftar Game</h2>
            <span class="text-muted" style="font-size: 0.8rem;"><?= count($games) ?> game terdaftar</span>
        </div>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">Urutan</th>
                        <th>Game</th>
                        <th>Kategori</th>
                        <th>Slug</th>
                        <th>Warna</th>
                        <th>Populer</th>
                        <th>Status</th>
                        <th style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($games)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center;" class="text-muted">Tidak ada game. Tambahkan game pertama Anda!</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($games as $g): ?>
                            <tr>
                                <td style="text-align: center; font-weight: 700;"><?= $g['sort_order'] ?></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span style="font-size: 1.6rem;"><?= htmlspecialchars($g['icon']) ?></span>
                                        <span style="font-weight: 600;"><?= htmlspecialchars($g['name']) ?></span>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($g['category']) ?></td>
                                <td style="font-family: monospace; font-size: 0.8rem;" class="text-muted"><?= htmlspecialchars($g['slug']) ?></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <span style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; background-color: <?= $g['color'] ?>;"></span>
                                        <span style="font-family: monospace; font-size: 0.78rem;"><?= htmlspecialchars($g['color']) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-admin <?= $g['popular'] ? 'badge-success' : 'badge-failed' ?>" style="padding: 2px 8px; font-size: 0.7rem;">
                                        <?= $g['popular'] ? 'YA' : 'TIDAK' ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-admin <?= $g['is_active'] ? 'badge-success' : 'badge-failed' ?>" style="padding: 2px 8px; font-size: 0.7rem;">
                                        <?= $g['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($dbActive): ?>
                                        <a href="?edit=<?= $g['id'] ?>" class="btn-admin btn-admin-primary btn-admin-sm">Edit</a>
                                        <a href="?delete=<?= $g['id'] ?>" class="btn-admin btn-admin-danger btn-admin-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus game ini? Semua produk dalam game ini juga akan terhapus.');">Hapus</a>
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
        <h2><?= $editMode ? '⚙️ Edit Game' : '➕ Tambah Game' ?></h2>
        <p class="text-muted" style="font-size: 0.8rem; margin-bottom: 20px;">
            <?= $editMode ? 'Modifikasi detail game terpilih.' : 'Masukkan game baru untuk ditawarkan kepada pelanggan.' ?>
        </p>

        <form method="POST" action="">
            <input type="hidden" name="action" value="<?= $editMode ? 'edit' : 'add' ?>">
            <?php if ($editMode): ?>
                <input type="hidden" name="id" value="<?= $editGame['id'] ?>">
            <?php endif; ?>

            <div class="admin-form-group">
                <label class="admin-form-label" for="name">Nama Game *</label>
                <input type="text" id="name" name="name" class="admin-form-control" placeholder="Contoh: Mobile Legends"
                       value="<?= $editMode ? htmlspecialchars($editGame['name']) : '' ?>" required <?= !$dbActive ? 'disabled' : '' ?>>
            </div>

            <div class="admin-form-group">
                <label class="admin-form-label" for="slug">Slug URL *</label>
                <input type="text" id="slug" name="slug" class="admin-form-control" placeholder="Contoh: mobile-legends (tanpa spasi)"
                       value="<?= $editMode ? htmlspecialchars($editGame['slug']) : '' ?>" required <?= !$dbActive ? 'disabled' : '' ?>>
            </div>

            <div class="admin-form-group">
                <label class="admin-form-label" for="category">Kategori *</label>
                <input type="text" id="category" name="category" class="admin-form-control" placeholder="Contoh: MOBA / RPG / FPS"
                       value="<?= $editMode ? htmlspecialchars($editGame['category']) : '' ?>" required <?= !$dbActive ? 'disabled' : '' ?>>
            </div>

            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label" for="icon">Icon Emoji</label>
                    <input type="text" id="icon" name="icon" class="admin-form-control" placeholder="Contoh: 🗡️"
                           value="<?= $editMode ? htmlspecialchars($editGame['icon']) : '🎮' ?>" <?= !$dbActive ? 'disabled' : '' ?>>
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label" for="color">Warna Tema (Hex)</label>
                    <input type="text" id="color" name="color" class="admin-form-control" placeholder="#00e5ff"
                           value="<?= $editMode ? htmlspecialchars($editGame['color']) : '#00e5ff' ?>" <?= !$dbActive ? 'disabled' : '' ?>>
                </div>
            </div>

            <div class="admin-form-group">
                <label class="admin-form-label" for="sort_order">Urutan Tampilan</label>
                <input type="number" id="sort_order" name="sort_order" class="admin-form-control" placeholder="0"
                       value="<?= $editMode ? (int)$editGame['sort_order'] : '0' ?>" <?= !$dbActive ? 'disabled' : '' ?>>
            </div>

            <div class="admin-form-group">
                <label class="admin-toggle-container">
                    <input type="checkbox" name="is_popular" class="admin-toggle-input" <?= ($editMode && $editGame['is_popular']) ? 'checked' : '' ?> <?= !$dbActive ? 'disabled' : '' ?>>
                    <div class="admin-toggle-switch"></div>
                    <span style="font-size: 0.85rem; font-weight: 500;">Tandai Terpopuler (HOT Badge)</span>
                </label>
            </div>

            <div class="admin-form-group">
                <label class="admin-toggle-container">
                    <input type="checkbox" name="is_active" class="admin-toggle-input" <?= (!$editMode || $editGame['is_active']) ? 'checked' : '' ?> <?= !$dbActive ? 'disabled' : '' ?>>
                    <div class="admin-toggle-switch"></div>
                    <span style="font-size: 0.85rem; font-weight: 500;">Aktif (Ditampilkan di Frontend)</span>
                </label>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 24px;">
                <button type="submit" class="btn-admin btn-admin-primary" style="flex: 1;" <?= !$dbActive ? 'disabled' : '' ?>>
                    <?= $editMode ? 'Simpan Perubahan' : 'Tambah Game' ?>
                </button>
                <?php if ($editMode): ?>
                    <a href="games.php" class="btn-admin btn-admin-secondary" style="text-decoration: none;">Batal</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<script>
// Auto generate slug dari nama game
const nameInput = document.getElementById('name');
const slugInput = document.getElementById('slug');
if (nameInput && slugInput && !slugInput.value) {
    nameInput.addEventListener('input', function() {
        slugInput.value = nameInput.value
            .toLowerCase()
            .replace(/[^\w ]+/g, '')
            .replace(/ +/g, '-');
    });
}
</script>

<?php include __DIR__ . '/admin-footer.php'; ?>
