<?php
// admin/index.php
// SMK Bangun Nusa Bangsa - Admin Dashboard Overview

$adminTitle = 'Ringkasan Dashboard';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$pdo = getDBConnection();

// Statistics Counters
$totalArticles = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
$totalViews = $pdo->query("SELECT SUM(views) FROM articles")->fetchColumn() ?: 0;
$totalComments = $pdo->query("SELECT COUNT(*) FROM comments")->fetchColumn();
$pendingComments = $pdo->query("SELECT COUNT(*) FROM comments WHERE status = 'pending'")->fetchColumn();
$totalCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalMessages = $pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();

// Latest Articles
$stmtRecentArt = $pdo->query("
    SELECT a.*, c.name AS category_name,
           (SELECT COUNT(*) FROM comments cm WHERE cm.article_id = a.id) AS comment_count
    FROM articles a
    JOIN categories c ON a.category_id = c.id
    ORDER BY a.created_at DESC
    LIMIT 5
");
$recentArticles = $stmtRecentArt->fetchAll();

// Recent Comments for Moderation
$stmtRecentCom = $pdo->query("
    SELECT c.*, a.title AS article_title, a.slug AS article_slug
    FROM comments c
    JOIN articles a ON c.article_id = a.id
    ORDER BY c.created_at DESC
    LIMIT 5
");
$recentComments = $stmtRecentCom->fetchAll();
?>

<!-- PAGE HEADER -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Dashboard Ikhtisar</h2>
        <p class="text-muted mb-0">Selamat datang di pusat pengelolaan konten SMK Bangun Nusa Bangsa.</p>
    </div>
    <div class="mt-3 mt-md-0">
        <a href="<?= SITE_URL ?>/admin/article-add.php" class="btn btn-custom-primary">
            <i class="bi bi-plus-circle-fill me-1"></i> Buat Artikel Baru
        </a>
    </div>
</div>

<!-- STATS CARDS -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="admin-stat-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Total Artikel</span>
                    <h3 class="fw-bold mb-0 mt-1"><?= number_format($totalArticles) ?></h3>
                </div>
                <div class="stat-card-icon-box bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-journal-richtext"></i>
                </div>
            </div>
            <div class="small text-muted"><a href="<?= SITE_URL ?>/admin/articles.php" class="text-decoration-none">Kelola artikel &rarr;</a></div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="admin-stat-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Total Pembaca</span>
                    <h3 class="fw-bold mb-0 mt-1"><?= number_format($totalViews) ?></h3>
                </div>
                <div class="stat-card-icon-box bg-info bg-opacity-10 text-info">
                    <i class="bi bi-eye-fill"></i>
                </div>
            </div>
            <div class="small text-muted">Akumulasi seluruh tayangan artikel</div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="admin-stat-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Total Komentar</span>
                    <h3 class="fw-bold mb-0 mt-1"><?= number_format($totalComments) ?></h3>
                </div>
                <div class="stat-card-icon-box bg-success bg-opacity-10 text-success">
                    <i class="bi bi-chat-dots-fill"></i>
                </div>
            </div>
            <div class="small text-muted">
                <?php if ($pendingComments > 0): ?>
                    <span class="text-danger fw-bold"><?= $pendingComments ?> komentar menunggu moderasi</span>
                <?php else: ?>
                    <span class="text-success">Seluruh komentar termoderasi</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="admin-stat-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Pesan PPDB & Kontak</span>
                    <h3 class="fw-bold mb-0 mt-1"><?= number_format($totalMessages) ?></h3>
                </div>
                <div class="stat-card-icon-box bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-envelope-open-fill"></i>
                </div>
            </div>
            <div class="small text-muted"><a href="<?= SITE_URL ?>/admin/messages.php" class="text-decoration-none">Buka kotak masuk &rarr;</a></div>
        </div>
    </div>
</div>

<!-- RECENT ARTICLES & RECENT COMMENTS -->
<div class="row g-4">
    <!-- RECENT ARTICLES TABLE (LEFT) -->
    <div class="col-lg-7">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="fw-bold mb-0"><i class="bi bi-newspaper text-primary me-2"></i>Artikel Terbaru</h5>
                <a href="<?= SITE_URL ?>/admin/articles.php" class="btn btn-sm btn-outline-primary rounded-pill">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th>Artikel</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Views</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentArticles)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada artikel yang ditambahkan.</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($recentArticles as $art): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?= SITE_URL . '/' . htmlspecialchars($art['thumbnail'] ?: 'assets/images/hero-3d.jpg') ?>" alt="Thumb" class="rounded-3 object-fit-cover" style="width: 48px; height: 40px;">
                                        <div>
                                            <a href="<?= SITE_URL ?>/admin/article-edit.php?id=<?= $art['id'] ?>" class="text-dark fw-semibold text-decoration-none d-block">
                                                <?= htmlspecialchars(mb_substr($art['title'], 0, 35)) ?>...
                                            </a>
                                            <small class="text-muted"><?= formatDateIndo($art['created_at']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-primary border"><?= htmlspecialchars($art['category_name']) ?></span></td>
                                <td>
                                    <span class="badge bg-<?= $art['status'] === 'published' ? 'success' : 'secondary' ?> bg-opacity-10 text-<?= $art['status'] === 'published' ? 'success' : 'secondary' ?> border border-<?= $art['status'] === 'published' ? 'success' : 'secondary' ?>">
                                        <?= ucfirst($art['status']) ?>
                                    </span>
                                </td>
                                <td><i class="bi bi-eye text-muted me-1"></i><?= $art['views'] ?></td>
                                <td class="text-end">
                                    <a href="<?= SITE_URL ?>/admin/article-edit.php?id=<?= $art['id'] ?>" class="btn btn-sm btn-light border text-primary" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                                    <a href="<?= SITE_URL ?>/artikel-detail.php?slug=<?= urlencode($art['slug']) ?>" target="_blank" class="btn btn-sm btn-light border text-info" title="Lihat"><i class="bi bi-box-arrow-up-right"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- RECENT COMMENTS MODERATION (RIGHT) -->
    <div class="col-lg-5">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="fw-bold mb-0"><i class="bi bi-chat-left-dots-fill text-info me-2"></i>Komentar Masuk</h5>
                <a href="<?= SITE_URL ?>/admin/comments.php" class="btn btn-sm btn-outline-info rounded-pill">Kelola Komentar</a>
            </div>
            <div class="admin-card-body p-0">
                <?php if (empty($recentComments)): ?>
                    <div class="p-4 text-center text-muted">Belum ada komentar pengunjung.</div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentComments as $com): ?>
                        <div class="list-group-item p-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <div class="d-flex align-items-center gap-2">
                                    <strong class="text-dark small"><?= htmlspecialchars($com['name']) ?></strong>
                                    <?php if ($com['is_anonymous']): ?>
                                        <span class="comment-badge-anonymous"><i class="bi bi-incognito me-1"></i>Anonim</span>
                                    <?php else: ?>
                                        <span class="comment-badge-verified"><i class="bi bi-check-circle me-1"></i><?= htmlspecialchars($com['email'] ?: 'Terverifikasi') ?></span>
                                    <?php endif; ?>
                                </div>
                                <small class="text-muted" style="font-size: 0.75rem;"><?= timeAgo($com['created_at']) ?></small>
                            </div>
                            <p class="small text-secondary mb-2" style="line-height: 1.4;">
                                "<?= htmlspecialchars(mb_substr($com['comment'], 0, 90)) ?>..."
                            </p>
                            <div class="d-flex align-items-center justify-content-between">
                                <small class="text-muted text-truncate" style="max-width: 200px;">
                                    Pada: <a href="<?= SITE_URL ?>/artikel-detail.php?slug=<?= urlencode($com['article_slug']) ?>" target="_blank" class="text-decoration-none text-info"><?= htmlspecialchars($com['article_title']) ?></a>
                                </small>
                                <span class="badge bg-<?= $com['status'] === 'approved' ? 'success' : ($com['status'] === 'pending' ? 'warning text-dark' : 'danger') ?>">
                                    <?= ucfirst($com['status']) ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
