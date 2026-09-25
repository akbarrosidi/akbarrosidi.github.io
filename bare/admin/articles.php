<?php
// admin/articles.php
// SMK Bangun Nusa Bangsa - Articles List Management

$adminTitle = 'Manajemen Artikel';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$pdo = getDBConnection();

$categoryFilter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$statusFilter = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

$where = ["1=1"];
$params = [];

if ($categoryFilter > 0) {
    $where[] = "a.category_id = :cat_id";
    $params[':cat_id'] = $categoryFilter;
}

if (!empty($statusFilter)) {
    $where[] = "a.status = :status";
    $params[':status'] = $statusFilter;
}

if (!empty($search)) {
    $where[] = "(a.title LIKE :search OR a.content LIKE :search)";
    $params[':search'] = "%$search%";
}

$whereSql = implode(' AND ', $where);

$sql = "
    SELECT a.*, c.name AS category_name, u.fullname AS author_name,
           (SELECT COUNT(*) FROM comments cm WHERE cm.article_id = a.id) AS total_comments
    FROM articles a
    JOIN categories c ON a.category_id = c.id
    LEFT JOIN users u ON a.author_id = u.id
    WHERE $whereSql
    ORDER BY a.created_at DESC
";
$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->execute();
$articles = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>

<!-- PAGE HEADER -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Manajemen Artikel</h2>
        <p class="text-muted mb-0">Kelola publikasi, penulisan, serta pembaruan seluruh konten berita dan artikel.</p>
    </div>
    <div class="mt-3 mt-md-0">
        <a href="<?= SITE_URL ?>/admin/article-add.php" class="btn btn-custom-primary">
            <i class="bi bi-plus-circle-fill me-1"></i> Buat Artikel Baru
        </a>
    </div>
</div>

<!-- FILTERS CARD -->
<div class="admin-card mb-4">
    <div class="admin-card-body p-3">
        <form action="<?= SITE_URL ?>/admin/articles.php" method="GET" class="row g-3 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari judul artikel..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $categoryFilter == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="published" <?= $statusFilter === 'published' ? 'selected' : '' ?>>Published (Tayang)</option>
                    <option value="draft" <?= $statusFilter === 'draft' ? 'selected' : '' ?>>Draft (Konsep)</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
                <?php if ($categoryFilter || $statusFilter || !empty($search)): ?>
                    <a href="<?= SITE_URL ?>/admin/articles.php" class="btn btn-light border"><i class="bi bi-arrow-counterclockwise"></i></a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- ARTICLES TABLE CARD -->
<div class="admin-card">
    <div class="admin-card-header">
        <h5 class="fw-bold mb-0">Daftar Artikel (<?= count($articles) ?>)</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th style="width: 70px;">Thumbnail</th>
                    <th>Judul & Tanggal</th>
                    <th>Kategori</th>
                    <th>Penulis</th>
                    <th>Statistik</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($articles)): ?>
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary"></i>
                        Tidak ada artikel yang sesuai dengan kriteria filter.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($articles as $art): ?>
                    <tr>
                        <td>
                            <img src="<?= SITE_URL . '/' . htmlspecialchars($art['thumbnail'] ?: 'assets/images/hero-3d.jpg') ?>" alt="Thumb" class="rounded-3 object-fit-cover" style="width: 60px; height: 45px;">
                        </td>
                        <td>
                            <a href="<?= SITE_URL ?>/admin/article-edit.php?id=<?= $art['id'] ?>" class="text-dark fw-bold text-decoration-none d-block mb-1">
                                <?= htmlspecialchars($art['title']) ?>
                            </a>
                            <small class="text-muted"><i class="bi bi-clock me-1"></i><?= formatDateIndo($art['created_at'], true) ?></small>
                        </td>
                        <td>
                            <span class="badge bg-light text-primary border"><?= htmlspecialchars($art['category_name']) ?></span>
                        </td>
                        <td>
                            <small class="text-secondary"><?= htmlspecialchars($art['author_name'] ?: 'Admin') ?></small>
                        </td>
                        <td>
                            <div class="small text-muted">
                                <div><i class="bi bi-eye me-1 text-info"></i><?= $art['views'] ?> views</div>
                                <div><i class="bi bi-chat-dots me-1 text-success"></i><?= $art['total_comments'] ?> komentar</div>
                            </div>
                        </td>
                        <td>
                            <?php if ($art['status'] === 'published'): ?>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-1 rounded-pill">Published</span>
                            <?php else: ?>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-3 py-1 rounded-pill">Draft</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <a href="<?= SITE_URL ?>/artikel-detail.php?slug=<?= urlencode($art['slug']) ?>" target="_blank" class="btn btn-sm btn-light border text-info" title="Lihat di Web">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                                <a href="<?= SITE_URL ?>/admin/article-edit.php?id=<?= $art['id'] ?>" class="btn btn-sm btn-light border text-primary" title="Edit Artikel">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <a href="<?= SITE_URL ?>/admin/article-delete.php?id=<?= $art['id'] ?>" class="btn btn-sm btn-light border text-danger btn-confirm-delete" data-name="<?= htmlspecialchars($art['title']) ?>" title="Hapus Artikel">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
