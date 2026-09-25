<?php
// admin/article-edit.php
// SMK Bangun Nusa Bangsa - Edit Existing Article

$adminTitle = 'Edit Artikel';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$pdo = getDBConnection();
$articleId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$articleId]);
$article = $stmt->fetch();

if (!$article) {
    setFlashMessage('danger', 'Artikel tidak ditemukan.');
    echo "<script>window.location.href = '" . SITE_URL . "/admin/articles.php';</script>";
    exit;
}

$error = '';
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $slug = sanitize($_POST['slug'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $excerpt = sanitize($_POST['excerpt'] ?? '');
    $content = $_POST['content'] ?? '';
    $status = in_array($_POST['status'] ?? '', ['published', 'draft']) ? $_POST['status'] : 'published';

    if (empty($slug)) {
        $slug = slugify($title);
    } else {
        $slug = slugify($slug);
    }

    if (empty($title) || empty($content) || $categoryId <= 0) {
        $error = 'Judul, kategori, dan isi artikel wajib diisi.';
    } else {
        // Check duplicate slug excluding current article
        $stmtSlugCheck = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE slug = ? AND id != ?");
        $stmtSlugCheck->execute([$slug, $articleId]);
        if ($stmtSlugCheck->fetchColumn() > 0) {
            $slug = $slug . '-' . time();
        }

        // Handle thumbnail upload if replacement provided
        $thumbnailPath = $article['thumbnail'];
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
                $stmtUpdate = $pdo->prepare("
                    UPDATE articles 
                    SET title = ?, slug = ?, category_id = ?, thumbnail = ?, excerpt = ?, content = ?, status = ?
                    WHERE id = ?
                ");
                $stmtUpdate->execute([$title, $slug, $categoryId, $thumbnailPath, $excerpt, $content, $status, $articleId]);

                setFlashMessage('success', 'Artikel "' . htmlspecialchars($title) . '" berhasil diperbarui!');
                echo "<script>window.location.href = '" . SITE_URL . "/admin/articles.php';</script>";
                exit;
            } catch (Exception $e) {
                $error = 'Gagal memperbarui artikel: ' . $e->getMessage();
            }
        }
    }
}
?>

<!-- PAGE HEADER -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Edit Artikel</h2>
        <p class="text-muted mb-0">Ubah konten, kategori, thumbnail, atau status artikel ini.</p>
    </div>
    <div class="mt-3 mt-md-0 d-flex gap-2">
        <a href="<?= SITE_URL ?>/artikel-detail.php?slug=<?= urlencode($article['slug']) ?>" target="_blank" class="btn btn-outline-info rounded-pill">
            <i class="bi bi-box-arrow-up-right me-1"></i> Lihat di Web
        </a>
        <a href="<?= SITE_URL ?>/admin/articles.php" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Kembali
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

<form action="<?= SITE_URL ?>/admin/article-edit.php?id=<?= $articleId ?>" method="POST" enctype="multipart/form-data">
    <div class="row g-4">
        <!-- LEFT COLUMN: CONTENT -->
        <div class="col-lg-8">
            <div class="admin-card mb-4">
                <div class="admin-card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Judul Artikel <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="article_title" class="form-control form-control-lg" placeholder="Judul artikel" required value="<?= htmlspecialchars($article['title']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">URL Slug</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted small"><?= SITE_URL ?>/artikel-detail.php?slug=</span>
                            <input type="text" name="slug" id="article_slug" class="form-control" value="<?= htmlspecialchars($article['slug']) ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ringkasan Singkat (Excerpt)</label>
                        <textarea name="excerpt" rows="2" class="form-control"><?= htmlspecialchars($article['excerpt']) ?></textarea>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold">Isi Konten Lengkap <span class="text-danger">*</span></label>
                        <textarea name="content" id="summernote_editor"><?= htmlspecialchars($article['content']) ?></textarea>
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
                            <option value="published" <?= $article['status'] === 'published' ? 'selected' : '' ?>>Published (Tayang)</option>
                            <option value="draft" <?= $article['status'] === 'draft' ? 'selected' : '' ?>>Draft (Konsep)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $article['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3 small text-muted">
                        <div><i class="bi bi-eye me-1"></i><strong><?= $article['views'] ?></strong> kali dilihat</div>
                        <div><i class="bi bi-clock me-1"></i>Dibuat: <?= formatDateIndo($article['created_at'], true) ?></div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-custom-primary py-2">
                            <i class="bi bi-check-circle-fill me-1"></i> Simpan Perubahan
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
                    <img id="thumbnail_preview" src="<?= SITE_URL . '/' . htmlspecialchars($article['thumbnail'] ?: 'assets/images/hero-3d.jpg') ?>" alt="Preview" class="img-fluid rounded-3 mb-3 border shadow-sm" style="max-height: 200px; width: 100%; object-fit: cover;">
                    <input type="file" name="thumbnail" id="thumbnail_input" class="form-control" accept="image/jpeg,image/png,image/webp">
                    <div class="form-text text-muted small mt-2">Biarkan kosong jika tidak ingin mengganti thumbnail yang ada.</div>
                </div>
            </div>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
