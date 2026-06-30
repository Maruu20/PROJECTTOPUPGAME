<?php
$adminTitle = 'Kelola Banner';
include __DIR__ . '/admin-header.php';

$db = getDB();
$dbActive = ($db !== null);
$error = '';
$success = '';

// Mode form: default adalah tambah, jika ada GET edit maka ganti mode edit
$editMode = false;
$editBanner = null;
if ($dbActive && isset($_GET['edit'])) {
    $bannerId = (int)$_GET['edit'];
    $editBanner = getBannerById($bannerId);
    if ($editBanner) {
        $editMode = true;
    }
}

// Handle CRUD Actions
if ($dbActive && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = sanitize($_POST['action'] ?? '');

    if ($action === 'add' || $action === 'edit') {
        $title        = sanitize($_POST['title'] ?? '');
        $description  = sanitize($_POST['description'] ?? '');
        $badge_text   = sanitize($_POST['badge_text'] ?? 'Promo');
        $graphic_icon = sanitize($_POST['graphic_icon'] ?? '🏆');
        $button_link  = sanitize($_POST['button_link'] ?? '#');
        $button_text  = sanitize($_POST['button_text'] ?? 'Info Selengkapnya');
        $sort_order   = (int)($_POST['sort_order'] ?? 0);
        $is_active    = isset($_POST['is_active']) ? 1 : 0;
        
        $image_path = null;
        if ($editMode) {
            $image_path = $editBanner['image_path'];
        }
        
        // Handle file upload if any
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['image_file']['tmp_name'];
            $fileName = $_FILES['image_file']['name'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($fileExtension, $allowedExtensions)) {
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $uploadFileDir = __DIR__ . '/../assets/images/banners/';
                if (!file_exists($uploadFileDir)) {
                    mkdir($uploadFileDir, 0777, true);
                }
                $dest_path = $uploadFileDir . $newFileName;
                
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    // Hapus gambar lama jika mengedit
                    if ($editMode && !empty($editBanner['image_path'])) {
                        $oldPath = __DIR__ . '/../' . $editBanner['image_path'];
                        if (file_exists($oldPath)) {
                            @unlink($oldPath);
                        }
                    }
                    $image_path = 'assets/images/banners/' . $newFileName;
                    // Jika upload gambar berhasil, kosongkan graphic_icon atau gunakan default
                } else {
                    $error = 'Gagal memindahkan file ke direktori tujuan.';
                }
            } else {
                $error = 'Format file gambar tidak didukung. Gunakan JPG, PNG, GIF, atau WEBP.';
            }
        }
        
        // Opsi hapus gambar saat edit
        if ($editMode && isset($_POST['delete_image']) && $_POST['delete_image'] == 1) {
            if (!empty($editBanner['image_path'])) {
                $oldPath = __DIR__ . '/../' . $editBanner['image_path'];
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
                $image_path = null;
            }
        }

        if (empty($title) || empty($description)) {
            $error = 'Judul dan Deskripsi wajib diisi.';
        } elseif (empty($error)) {
            if ($action === 'add') {
                $res = createBanner($title, $description, $image_path, $graphic_icon, $badge_text, $button_link, $button_text, $sort_order, $is_active);
                if ($res) {
                    $success = "Banner <strong>" . htmlspecialchars($title) . "</strong> berhasil ditambahkan.";
                } else {
                    $error = 'Gagal menyimpan banner ke database.';
                }
            } else {
                $id = (int)$_POST['id'];
                $res = updateBanner($id, $title, $description, $image_path, $graphic_icon, $badge_text, $button_link, $button_text, $sort_order, $is_active);
                if ($res) {
                    $success = "Perubahan banner <strong>" . htmlspecialchars($title) . "</strong> berhasil disimpan.";
                    $editMode = false;
                    $editBanner = null;
                } else {
                    $error = 'Gagal memperbarui banner di database.';
                }
            }
        }
    }
}

// Handle Delete Action
if ($dbActive && isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $bannerToDelete = getBannerById($id);
    if ($bannerToDelete) {
        deleteBanner($id);
        $success = "Banner <strong>" . htmlspecialchars($bannerToDelete['title']) . "</strong> berhasil dihapus.";
    }
}

// Fetch banners list
$banners = getBanners(false); // Ambil semua banner (aktif maupun nonaktif)
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
    <!-- ===== KIRI: LIST BANNER ===== -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h2>🖼️ Daftar Banner</h2>
            <span class="text-muted" style="font-size: 0.8rem;"><?= count($banners) ?> banner terdaftar</span>
        </div>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">Urutan</th>
                        <th>Banner</th>
                        <th>Badge & Tombol</th>
                        <th>Status</th>
                        <th style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($banners)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center;" class="text-muted">Tidak ada banner. Tambahkan banner promo pertama Anda!</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($banners as $b): ?>
                            <tr>
                                <td style="text-align: center; font-weight: 700;"><?= $b['sort_order'] ?></td>
                                <td>
                                    <div style="display: flex; gap: 12px; align-items: center;">
                                        <div style="width: 70px; height: 45px; border-radius: 6px; background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0;">
                                            <?php if (!empty($b['image_path'])): ?>
                                                <img src="<?= APP_URL . '/' . htmlspecialchars($b['image_path']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                            <?php else: ?>
                                                <span style="font-size: 1.4rem;"><?= htmlspecialchars($b['graphic_icon']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; font-size: 0.9rem; color: #fff; margin-bottom: 2px;"><?= htmlspecialchars($b['title']) ?></div>
                                            <div class="text-muted" style="font-size: 0.78rem; line-height: 1.3; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                <?= htmlspecialchars($b['description']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-admin badge-pending" style="font-size: 0.7rem; margin-bottom: 4px; display: inline-block;"><?= htmlspecialchars($b['badge_text']) ?></span>
                                    <div style="font-size: 0.75rem;" class="text-muted">
                                        Tombol: <a href="<?= htmlspecialchars($b['button_link']) ?>" target="_blank" style="color: var(--neon-cyan); text-decoration: underline;"><?= htmlspecialchars($b['button_text']) ?></a>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-admin <?= $b['is_active'] ? 'badge-success' : 'badge-failed' ?>" style="padding: 2px 8px; font-size: 0.7rem;">
                                        <?= $b['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($dbActive): ?>
                                        <a href="?edit=<?= $b['id'] ?>" class="btn-admin btn-admin-primary btn-admin-sm">Edit</a>
                                        <a href="?delete=<?= $b['id'] ?>" class="btn-admin btn-admin-danger btn-admin-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus banner ini?');">Hapus</a>
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
        <h2><?= $editMode ? '⚙️ Edit Banner' : '➕ Tambah Banner' ?></h2>
        <p class="text-muted" style="font-size: 0.8rem; margin-bottom: 20px;">
            <?= $editMode ? 'Modifikasi konten promo dan berita pilihan.' : 'Buat banner promo geser baru untuk halaman depan.' ?>
        </p>

        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="action" value="<?= $editMode ? 'edit' : 'add' ?>">
            <?php if ($editMode): ?>
                <input type="hidden" name="id" value="<?= $editBanner['id'] ?>">
            <?php endif; ?>

            <div class="admin-form-group">
                <label class="admin-form-label" for="title">Judul Banner *</label>
                <input type="text" id="title" name="title" class="admin-form-control" placeholder="Contoh: Football Fever 2026"
                       value="<?= $editMode ? htmlspecialchars($editBanner['title']) : '' ?>" required <?= !$dbActive ? 'disabled' : '' ?>>
            </div>

            <div class="admin-form-group">
                <label class="admin-form-label" for="description">Deskripsi Berita/Promo *</label>
                <textarea id="description" name="description" class="admin-form-control" rows="3" placeholder="Masukkan konten promo secara singkat..." required <?= !$dbActive ? 'disabled' : '' ?>><?= $editMode ? htmlspecialchars($editBanner['description']) : '' ?></textarea>
            </div>

            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label" for="badge_text">Teks Label (Badge)</label>
                    <input type="text" id="badge_text" name="badge_text" class="admin-form-control" placeholder="Contoh: Event Terbatas"
                           value="<?= $editMode ? htmlspecialchars($editBanner['badge_text']) : 'Promo' ?>" <?= !$dbActive ? 'disabled' : '' ?>>
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label" for="graphic_icon">Icon Emoji (jika tanpa gambar)</label>
                    <input type="text" id="graphic_icon" name="graphic_icon" class="admin-form-control" placeholder="Contoh: 🏆"
                           value="<?= $editMode ? htmlspecialchars($editBanner['graphic_icon']) : '🏆' ?>" <?= !$dbActive ? 'disabled' : '' ?>>
                </div>
            </div>

            <div class="admin-form-group">
                <label class="admin-form-label" for="image_file">Unggah Gambar Banner (Opsional)</label>
                <input type="file" id="image_file" name="image_file" class="admin-form-control" style="padding: 7px 16px;" <?= !$dbActive ? 'disabled' : '' ?>>
                <p class="text-muted" style="font-size: 0.72rem; margin-top: 4px;">Rekomendasi rasio horizontal lebar (misal: 1200x400px atau sejenisnya). Format JPG, PNG, WEBP.</p>
                
                <?php if ($editMode && !empty($editBanner['image_path'])): ?>
                    <div style="margin-top: 10px; display: flex; align-items: center; gap: 10px;">
                        <img src="<?= APP_URL . '/' . htmlspecialchars($editBanner['image_path']) ?>" style="height: 50px; border-radius: 4px; border: 1px solid var(--border-color);">
                        <label class="admin-toggle-container" style="margin-top: 0;">
                            <input type="checkbox" name="delete_image" value="1" class="admin-toggle-input">
                            <div class="admin-toggle-switch"></div>
                            <span style="font-size: 0.78rem; font-weight: 500; color: var(--red);">Hapus Gambar Saat Ini</span>
                        </label>
                    </div>
                <?php endif; ?>
            </div>

            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label" for="button_text">Teks Tombol</label>
                    <input type="text" id="button_text" name="button_text" class="admin-form-control" placeholder="Contoh: Info Selengkapnya"
                           value="<?= $editMode ? htmlspecialchars($editBanner['button_text']) : 'Info Selengkapnya' ?>" <?= !$dbActive ? 'disabled' : '' ?>>
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label" for="button_link">Link Tombol (Link Tujuan)</label>
                    <input type="text" id="button_link" name="button_link" class="admin-form-control" placeholder="Contoh: # atau URL spesifik"
                           value="<?= $editMode ? htmlspecialchars($editBanner['button_link']) : '#' ?>" <?= !$dbActive ? 'disabled' : '' ?>>
                </div>
            </div>

            <div class="admin-form-group">
                <label class="admin-form-label" for="sort_order">Urutan Tampilan</label>
                <input type="number" id="sort_order" name="sort_order" class="admin-form-control" placeholder="0"
                       value="<?= $editMode ? (int)$editBanner['sort_order'] : '0' ?>" <?= !$dbActive ? 'disabled' : '' ?>>
            </div>

            <div class="admin-form-group">
                <label class="admin-toggle-container">
                    <input type="checkbox" name="is_active" class="admin-toggle-input" <?= (!$editMode || $editBanner['is_active']) ? 'checked' : '' ?> <?= !$dbActive ? 'disabled' : '' ?>>
                    <div class="admin-toggle-switch"></div>
                    <span style="font-size: 0.85rem; font-weight: 500;">Aktif (Ditampilkan di Slider)</span>
                </label>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 24px;">
                <button type="submit" class="btn-admin btn-admin-primary" style="flex: 1;" <?= !$dbActive ? 'disabled' : '' ?>>
                    <?= $editMode ? 'Simpan Perubahan' : 'Tambah Banner' ?>
                </button>
                <?php if ($editMode): ?>
                    <a href="banners.php" class="btn-admin btn-admin-secondary" style="text-decoration: none;">Batal</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/admin-footer.php'; ?>
