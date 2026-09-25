<?php
// artikel.php
// SMK Bangun Nusa Bangsa - Articles & News Portal

$pageTitle = 'Portal Artikel & Berita';
require_once __DIR__ . '/includes/header.php';

$pdo = getDBConnection();

// Filters & Pagination
$categorySlug = isset($_GET['kategori']) ? sanitize($_GET['kategori']) : '';
$searchQuery = isset($_GET['cari']) ? sanitize($_GET['cari']) : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 6;
$offset = ($page - 1) * $perPage;

// Build SQL Query
$where = ["a.status = 'published'"];
$params = [];

if (!empty($categorySlug)) {
    $where[] = "c.slug = :category_slug";
    $params[':category_slug'] = $categorySlug;
}

if (!empty($searchQuery)) {
    $where[] = "(a.title LIKE :search OR a.content LIKE :search OR a.excerpt LIKE :search)";
    $params[':search'] = '%' . $searchQuery . '%';
}

$whereSql = implode(' AND ', $where);

// Total records count
$countStmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM articles a 
    JOIN categories c ON a.category_id = c.id 
    WHERE $whereSql
");
foreach ($params as $k => $v) {
    $countStmt->bindValue($k, $v);
}
$countStmt->execute();
$totalArticles = $countStmt->fetchColumn();
$totalPages = ceil($totalArticles / $perPage);

// Fetch Articles
$sql = "
    SELECT a.*, c.name AS category_name, c.slug AS category_slug,
           (SELECT COUNT(*) FROM comments cm WHERE cm.article_id = a.id AND cm.status = 'approved') AS total_comments
    FROM articles a
    JOIN categories c ON a.category_id = c.id
    WHERE $whereSql
    ORDER BY a.created_at DESC
    LIMIT :offset, :per_page
";
$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$stmt->bindValue(':per_page', (int)$perPage, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll();

// Fetch all categories for filter pills
$categories = $pdo->query("
    SELECT c.*, COUNT(a.id) AS article_count 
    FROM categories c 
    LEFT JOIN articles a ON a.category_id = c.id AND a.status = 'published'
    GROUP BY c.id 
    ORDER BY c.name ASC
")->fetchAll();

// Fetch Popular Articles (Trending)
$popularArticles = $pdo->query("
    SELECT a.*, c.name AS category_name
    FROM articles a
    JOIN categories c ON a.category_id = c.id
    WHERE a.status = 'published'
    ORDER BY a.views DESC
    LIMIT 4
")->fetchAll();
?>

<!-- ARTICLE PORTAL HEADER -->
<section class="article-detail-header text-center">
    <div class="container position-relative" style="z-index: 5;" data-aos="fade-down">
        <span class="badge bg-primary bg-opacity-25 text-info px-3 py-2 rounded-pill fw-bold mb-3">PUSAT INFORMASI & ARTIKEL</span>
        <h1 class="display-4 fw-bold text-white mb-3">Kabar, Prestasi, & Edukasi</h1>
        <p class="lead text-light opacity-75 mx-auto" style="max-width: 650px;">
            Temukan berita kegiatan terbaru, inovasi teknologi siswa, tips pembelajaran, dan pengumuman resmi sekolah.
        </p>

        <!-- SEARCH BAR -->
        <div class="row justify-content-center mt-4">
            <div class="col-md-8 col-lg-6">
                <form action="<?= SITE_URL ?>/artikel.php" method="GET" class="d-flex bg-white p-2 rounded-pill shadow-lg">
                    <?php if (!empty($categorySlug)): ?>
                        <input type="hidden" name="kategori" value="<?= htmlspecialchars($categorySlug) ?>">
                    <?php endif; ?>
                    <input type="text" name="cari" class="form-control border-0 bg-transparent px-4 shadow-none" placeholder="Cari artikel, topik, atau kata kunci..." value="<?= htmlspecialchars($searchQuery) ?>">
                    <button type="submit" class="btn btn-ppdb rounded-pill px-4">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- MAIN CONTENT SECTION -->
<section class="section-padding bg-light">
    <div class="container">
        
        <!-- CATEGORY PILLS -->
        <div class="d-flex flex-wrap gap-2 mb-5 justify-content-center" data-aos="fade-up">
            <a href="<?= SITE_URL ?>/artikel.php" class="btn btn-sm rounded-pill fw-semibold <?= empty($categorySlug) ? 'btn-primary shadow' : 'btn-white bg-white text-secondary border' ?> px-3 py-2">
                <i class="bi bi-grid-fill me-1"></i> Semua Kategori
            </a>
            <?php foreach ($categories as $cat): ?>
            <a href="<?= SITE_URL ?>/artikel.php?kategori=<?= urlencode($cat['slug']) ?><?= !empty($searchQuery) ? '&cari=' . urlencode($searchQuery) : '' ?>" class="btn btn-sm rounded-pill fw-semibold <?= $categorySlug === $cat['slug'] ? 'btn-primary shadow' : 'btn-white bg-white text-secondary border' ?> px-3 py-2">
                <i class="bi <?= htmlspecialchars($cat['icon'] ?: 'bi-bookmark') ?> me-1"></i> <?= htmlspecialchars($cat['name']) ?> (<?= $cat['article_count'] ?>)
            </a>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($searchQuery) || !empty($categorySlug)): ?>
        <div class="alert alert-info d-flex justify-content-between align-items-center mb-4 rounded-4 shadow-sm" role="alert">
            <div>
                <i class="bi bi-info-circle-fill me-2"></i>
                Menampilkan hasil untuk: 
                <?php if (!empty($categorySlug)): ?>
                    <strong>Kategori: <?= htmlspecialchars($categorySlug) ?></strong>
                <?php endif; ?>
                <?php if (!empty($searchQuery)): ?>
                    <strong>Kata Kunci: "<?= htmlspecialchars($searchQuery) ?>"</strong>
                <?php endif; ?>
                (<?= $totalArticles ?> artikel ditemukan)
            </div>
            <a href="<?= SITE_URL ?>/artikel.php" class="btn btn-sm btn-outline-dark rounded-pill">Reset Filter</a>
        </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- ARTICLES LIST (LEFT) -->
            <div class="col-lg-8">
                <?php if (empty($articles)): ?>
                <div class="bg-white p-5 rounded-4 text-center border shadow-sm my-4">
                    <div class="stat-icon bg-light text-muted mx-auto mb-3">
                        <i class="bi bi-journal-x"></i>
                    </div>
                    <h4 class="fw-bold text-dark">Tidak Ada Artikel Ditemukan</h4>
                    <p class="text-muted">Maaf, belum ada artikel yang sesuai dengan filter atau kata kunci pencarian Anda.</p>
                    <a href="<?= SITE_URL ?>/artikel.php" class="btn btn-primary rounded-pill px-4">Lihat Semua Artikel</a>
                </div>
                <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($articles as $art): ?>
                    <div class="col-md-6" data-aos="fade-up">
                        <div class="article-card">
                            <div class="article-thumbnail-box">
                                <img src="<?= SITE_URL . '/' . htmlspecialchars($art['thumbnail'] ?: 'assets/images/hero-3d.jpg') ?>" alt="<?= htmlspecialchars($art['title']) ?>">
                                <span class="article-category-badge">
                                    <?= htmlspecialchars($art['category_name']) ?>
                                </span>
                            </div>
                            <div class="article-body">
                                <div class="article-meta">
                                    <span><i class="bi bi-calendar3"></i> <?= formatDateIndo($art['created_at']) ?></span>
                                    <span><i class="bi bi-chat-left-text"></i> <?= $art['total_comments'] ?></span>
                                    <span><i class="bi bi-eye"></i> <?= $art['views'] ?></span>
                                </div>
                                <h3 class="article-card-title">
                                    <a href="<?= SITE_URL ?>/artikel-detail.php?slug=<?= urlencode($art['slug']) ?>" class="article-title-link">
                                        <?= htmlspecialchars($art['title']) ?>
                                    </a>
                                </h3>
                                <p class="article-excerpt">
                                    <?= htmlspecialchars(mb_substr(strip_tags($art['excerpt'] ?: $art['content']), 0, 110)) ?>...
                                </p>
                                <a href="<?= SITE_URL ?>/artikel-detail.php?slug=<?= urlencode($art['slug']) ?>" class="btn btn-link text-primary p-0 text-decoration-none fw-bold align-self-start mt-auto">
                                    Baca Selengkapnya <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- PAGINATION -->
                <?php if ($totalPages > 1): ?>
                <nav class="mt-5 d-flex justify-content-center" data-aos="fade-up">
                    <ul class="pagination pagination-md">
                        <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link rounded-circle mx-1" href="<?= SITE_URL ?>/artikel.php?page=<?= $page - 1 ?><?= !empty($categorySlug) ? '&kategori=' . urlencode($categorySlug) : '' ?><?= !empty($searchQuery) ? '&cari=' . urlencode($searchQuery) : '' ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link rounded-circle mx-1" href="<?= SITE_URL ?>/artikel.php?page=<?= $i ?><?= !empty($categorySlug) ? '&kategori=' . urlencode($categorySlug) : '' ?><?= !empty($searchQuery) ? '&cari=' . urlencode($searchQuery) : '' ?>">
                                <?= $i ?>
                            </a>
                        </li>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link rounded-circle mx-1" href="<?= SITE_URL ?>/artikel.php?page=<?= $page + 1 ?><?= !empty($categorySlug) ? '&kategori=' . urlencode($categorySlug) : '' ?><?= !empty($searchQuery) ? '&cari=' . urlencode($searchQuery) : '' ?>">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>

                <?php endif; ?>
            </div>

            <!-- SIDEBAR WIDGETS (RIGHT) -->
            <div class="col-lg-4">
                <!-- POPULAR ARTICLES -->
                <div class="card border-0 rounded-4 shadow-sm p-4 mb-4 bg-white" data-aos="fade-left">
                    <h5 class="fw-bold mb-4 pb-2 border-bottom d-flex align-items-center gap-2">
                        <i class="bi bi-fire text-danger"></i> Artikel Terpopuler
                    </h5>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($popularArticles as $pop): ?>
                        <div class="d-flex align-items-center gap-3">
                            <img src="<?= SITE_URL . '/' . htmlspecialchars($pop['thumbnail'] ?: 'assets/images/hero-3d.jpg') ?>" alt="<?= htmlspecialchars($pop['title']) ?>" class="rounded-3 object-fit-cover flex-shrink-0" style="width: 75px; height: 60px;">
                            <div>
                                <small class="text-primary fw-bold" style="font-size: 0.72rem;"><?= htmlspecialchars($pop['category_name']) ?></small>
                                <h6 class="mb-1 fw-bold" style="font-size: 0.88rem; line-height: 1.3;">
                                    <a href="<?= SITE_URL ?>/artikel-detail.php?slug=<?= urlencode($pop['slug']) ?>" class="text-dark text-decoration-none hover-primary">
                                        <?= htmlspecialchars(mb_substr($pop['title'], 0, 55)) ?>...
                                    </a>
                                </h6>
                                <small class="text-muted"><i class="bi bi-eye"></i> <?= $pop['views'] ?> views</small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- PPDB BANNER WIDGET -->
                <div class="card border-0 rounded-4 shadow-sm p-4 text-white text-center" style="background: linear-gradient(135deg, #2563eb, #0ea5e9);" data-aos="fade-left" data-aos-delay="100">
                    <i class="bi bi-rocket-takeoff-fill fs-1 text-warning mb-2"></i>
                    <h4 class="fw-bold mb-2">PPDB 2026 Dibuka!</h4>
                    <p class="small opacity-90 mb-4">Daftarkan diri Anda di SMK Bangun Nusa Bangsa dan nikmati beasiswa serta kurikulum modern 4.0.</p>
                    <a href="<?= SITE_URL ?>/kontak.php#ppdb" class="btn btn-light text-primary rounded-pill fw-bold w-100 py-2">Daftar Online</a>
                </div>
            </div>
        </div>

    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
