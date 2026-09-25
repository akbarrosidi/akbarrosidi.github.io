<?php
// admin/article-add.php
// SMK Bangun Nusa Bangsa - Add New Article

$adminTitle = 'Tulis Artikel Baru';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$pdo = getDBConnection();
$error = '';
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $slug = sanitize($_POST['slug'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $excerpt = sanitize($_POST['excerpt'] ?? '');
    $content = $_POST['content'] ?? ''; // rich html
    $status = in_array($_POST['status'] ?? '', ['published', 'draft']) ? $_POST['status'] : 'published';
    $authorId = $_SESSION['admin_id'] ?? 1;

    if (empty($slug)) {
        $slug = slugify($title);
    } else {
        $slug = slugify($slug);
    }

    if (empty($title) || empty($content) || $categoryId <= 0) {
        $error = 'Judul, kategori, dan isi artikel wajib diisi.';
    } else {
        // Check duplicate slug
        $stmtSlugCheck = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE slug = ?");
        $stmtSlugCheck->execute([$slug]);
        if ($stmtSlugCheck->fetchColumn() > 0) {
            $slug = $slug . '-' . time();
        }

        // Handle thumbnail upload
        $thumbnailPath = 'assets/images/hero-3d.jpg'; // default fallback
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
            $uploadRes = uploadArticleThumbnail($_FILES['thumbnail'], __DIR__ . '/../../uploads/');
            if ($uploadRes['status']) {
                $thumbnailPath = $uploadRes['filepath'];
            } else {
                $error = $uploadRes['message'];
            }
        }

        if (empty($error)) {
            try {
                $stmtInsert = $pdo->prepare("
                    INSERT INTO articles (title, slug, category_id, author_id, thumbnail, excerpt, content, status, views)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)
                ");
                $stmtInsert->execute([$title, $slug, $categoryId, $authorId, $thumbnailPath, $excerpt, $content, $status]);

                setFlashMessage('success', 'Artikel "' . htmlspecialchars($title) . '" berhasil diterbitkan!');
                echo "<script>window.location.href = '" . SITE_URL . "/admin/articles.php';</script>";
                exit;
            } catch (Exception $e) {
                $error = 'Gagal menyimpan artikel: ' . $e->getMessage();
            }
        }
    }
}
?>

<!-- PAGE HEADER -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Tulis Artikel Baru</h2>
        <p class="text-muted mb-0">Publikasikan karya tulis, pengumuman, atau dokumentasi prestasi terkini.</p>
    </div>
    <div class="mt-3 mt-md-0">
        <a href="<?= SITE_URL ?>/admin/articles.php" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>
</div>

<?php if (!empty($error)): ?>
<div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <?= htmlspecialchars($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<form action="<?= SITE_URL ?>/admin/article-add.php" method="POST" enctype="multipart/form-data">
    <div class="row g-4">
        <!-- LEFT COLUMN: CONTENT -->
        <div class="col-lg-8">
            <div class="admin-card mb-4">
                <div class="admin-card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Judul Artikel <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="article_title" class="form-control form-control-lg" placeholder="Contoh: Siswa SMK Bangun Nusa Bangsa Ciptakan Inovasi Robotika Cerdas 2026" required value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">URL Slug (Otomatis dari Judul)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted small"><?= SITE_URL ?>/artikel-detail.php?slug=</span>
                            <input type="text" name="slug" id="article_slug" class="form-control" placeholder="slug-artikel-otomatis" value="<?= htmlspecialchars($_POST['slug'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ringkasan Singkat (Excerpt)</label>
                        <textarea name="excerpt" rows="2" class="form-control" placeholder="Tuliskan 1-2 kalimat ringkasan yang akan tampil di halaman depan..."><?= htmlspecialchars($_POST['excerpt'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold">Isi Konten Lengkap <span class="text-danger">*</span></label>
                        <textarea name="content" id="summernote_editor"><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: METADATA & THUMBNAIL -->
        <div class="col-lg-4">
            <!-- PUBLISH CARD -->
            <div class="admin-card mb-4">
                <div class="admin-card-header">
                    <h6 class="fw-bold mb-0">Publikasi</h6>
                </div>
                <div class="admin-card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="published">Published (Langsung Tayang)</option>
                            <option value="draft">Draft (Simpan sebagai Konsep)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-custom-primary py-2">
                            <i class="bi bi-cloud-upload-fill me-1"></i> Terbitkan Artikel
                        </button>
                    </div>
                </div>
            </div>

            <!-- THUMBNAIL CARD -->
            <div class="admin-card mb-4">
                <div class="admin-card-header">
                    <h6 class="fw-bold mb-0">Gambar Sampul (Thumbnail)</h6>
                </div>
                <div class="admin-card-body text-center">
                    <img id="thumbnail_preview" src="<?= SITE_URL ?>/assets/images/hero-3d.jpg" alt="Preview" class="img-fluid rounded-3 mb-3 border shadow-sm" style="max-height: 200px; width: 100%; object-fit: cover;">
                    <input type="file" name="thumbnail" id="thumbnail_input" class="form-control" accept="image/jpeg,image/png,image/webp">
                    <div class="form-text text-muted small mt-2">Maksimal ukuran 5MB (JPG, PNG, WEBP). Disarankan rasio 16:9.</div>
                </div>
            </div>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
