<?php
// admin/categories.php
// SMK Bangun Nusa Bangsa - Category Management

$adminTitle = 'Manajemen Kategori';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$pdo = getDBConnection();
$error = '';

// Add / Edit Category Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $name = sanitize($_POST['name'] ?? '');
    $slug = sanitize($_POST['slug'] ?? '');
    $icon = sanitize($_POST['icon'] ?? 'bi-bookmark');
    $description = sanitize($_POST['description'] ?? '');

    if (empty($slug)) {
        $slug = slugify($name);
    } else {
        $slug = slugify($slug);
    }

    if (empty($name)) {
        $error = 'Nama kategori tidak boleh kosong.';
    } else {
        if ($action === 'create') {
            // Check duplicate
            $check = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE slug = ?");
            $check->execute([$slug]);
            if ($check->fetchColumn() > 0) {
                $slug .= '-' . time();
            }

            $stmt = $pdo->prepare("INSERT INTO categories (name, slug, icon, description) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $slug, $icon, $description]);
            setFlashMessage('success', 'Kategori "' . htmlspecialchars($name) . '" berhasil ditambahkan.');
            echo "<script>window.location.href = '" . SITE_URL . "/admin/categories.php';</script>";
            exit;
        } elseif ($action === 'update') {
            $catId = (int)$_POST['category_id'];
            $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, icon = ?, description = ? WHERE id = ?");
            $stmt->execute([$name, $slug, $icon, $description, $catId]);
            setFlashMessage('success', 'Kategori berhasil diperbarui.');
            echo "<script>window.location.href = '" . SITE_URL . "/admin/categories.php';</script>";
            exit;
        }
    }
}

// Delete Category Handler
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    
    // Check if category has articles
    $artCount = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE category_id = ?");
    $artCount->execute([$delId]);
    if ($artCount->fetchColumn() > 0) {
        setFlashMessage('danger', 'Kategori ini tidak dapat dihapus karena masih memiliki artikel terkait.');
    } else {
        $stmtDel = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmtDel->execute([$delId]);
        setFlashMessage('success', 'Kategori berhasil dihapus.');
    }
    echo "<script>window.location.href = '" . SITE_URL . "/admin/categories.php';</script>";
    exit;
}

// Fetch all categories with article counts
$categories = $pdo->query("
    SELECT c.*, COUNT(a.id) AS article_count 
    FROM categories c 
    LEFT JOIN articles a ON a.category_id = c.id 
    GROUP BY c.id 
    ORDER BY c.name ASC
")->fetchAll();

$editCategory = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmtEdit = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmtEdit->execute([$editId]);
    $editCategory = $stmtEdit->fetch();
}
?>

<!-- PAGE HEADER -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Kategori Artikel</h2>
        <p class="text-muted mb-0">Kelola pengelompokan artikel, berita, dan kegiatan sekolah.</p>
    </div>
</div>

<?php if (!empty($error)): ?>
<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
    <?= htmlspecialchars($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row g-4">
    <!-- FORM CREATE/EDIT (LEFT) -->
    <div class="col-lg-4">
        <div class="admin-card">
            <div class="admin-card-header">
                <h6 class="fw-bold mb-0"><?= $editCategory ? 'Edit Kategori' : 'Tambah Kategori Baru' ?></h6>
            </div>
            <div class="admin-card-body">
                <form action="<?= SITE_URL ?>/admin/categories.php" method="POST">
                    <input type="hidden" name="action" value="<?= $editCategory ? 'update' : 'create' ?>">
                    <?php if ($editCategory): ?>
                        <input type="hidden" name="category_id" value="<?= $editCategory['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Info Akademik" required value="<?= htmlspecialchars($editCategory['name'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Slug URL (Opsional)</label>
                        <input type="text" name="slug" class="form-control" placeholder="info-akademik" value="<?= htmlspecialchars($editCategory['slug'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Icon Bootstrap</label>
                        <input type="text" name="icon" class="form-control" placeholder="bi-bookmark" value="<?= htmlspecialchars($editCategory['icon'] ?? 'bi-bookmark') ?>">
                        <div class="form-text small">Contoh: bi-trophy, bi-newspaper, bi-briefcase</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi Singkat</label>
                        <textarea name="description" rows="3" class="form-control" placeholder="Keterangan kategori..."><?= htmlspecialchars($editCategory['description'] ?? '') ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-custom-primary flex-grow-1">
                            <?= $editCategory ? 'Simpan Perubahan' : 'Tambah Kategori' ?>
                        </button>
                        <?php if ($editCategory): ?>
                            <a href="<?= SITE_URL ?>/admin/categories.php" class="btn btn-light border">Batal</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- CATEGORIES TABLE (RIGHT) -->
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="admin-card-header">
                <h6 class="fw-bold mb-0">Daftar Kategori Aktif (<?= count($categories) ?>)</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th>Slug</th>
                            <th>Icon</th>
                            <th>Jumlah Artikel</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td>
                                <strong class="text-dark"><?= htmlspecialchars($cat['name']) ?></strong>
                                <?php if (!empty($cat['description'])): ?>
                                    <div class="small text-muted"><?= htmlspecialchars($cat['description']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td><code><?= htmlspecialchars($cat['slug']) ?></code></td>
                            <td><i class="bi <?= htmlspecialchars($cat['icon']) ?> fs-5 text-primary"></i></td>
                            <td><span class="badge bg-primary bg-opacity-10 text-primary border px-3"><?= $cat['article_count'] ?> artikel</span></td>
                            <td class="text-end">
                                <a href="<?= SITE_URL ?>/admin/categories.php?edit=<?= $cat['id'] ?>" class="btn btn-sm btn-light border text-primary" title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <a href="<?= SITE_URL ?>/admin/categories.php?delete=<?= $cat['id'] ?>" class="btn btn-sm btn-light border text-danger btn-confirm-delete" data-name="kategori <?= htmlspecialchars($cat['name']) ?>" title="Hapus">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
