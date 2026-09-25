<?php
// artikel-detail.php
// SMK Bangun Nusa Bangsa - Article Detail & Interactive Comment System

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/functions.php';

$pdo = getDBConnection();
$slug = isset($_GET['slug']) ? sanitize($_GET['slug']) : '';

if (empty($slug)) {
    header('Location: ' . SITE_URL . '/artikel.php');
    exit;
}

// Fetch Article Details
$stmt = $pdo->prepare("
    SELECT a.*, c.name AS category_name, c.slug AS category_slug, u.fullname AS author_name
    FROM articles a
    JOIN categories c ON a.category_id = c.id
    LEFT JOIN users u ON a.author_id = u.id
    WHERE a.slug = ? AND a.status = 'published'
");
$stmt->execute([$slug]);
$article = $stmt->fetch();

if (!$article) {
    header('Location: ' . SITE_URL . '/artikel.php');
    exit;
}

// Increment View Counter
incrementArticleViews($article['id']);

// Fetch Comments for this Article
$stmtComments = $pdo->prepare("
    SELECT * FROM comments 
    WHERE article_id = ? AND status = 'approved' 
    ORDER BY created_at DESC
");
$stmtComments->execute([$article['id']]);
$comments = $stmtComments->fetchAll();
$totalComments = count($comments);

// Fetch Related Articles
$stmtRelated = $pdo->prepare("
    SELECT a.*, c.name AS category_name 
    FROM articles a 
    JOIN categories c ON a.category_id = c.id 
    WHERE a.category_id = ? AND a.id != ? AND a.status = 'published' 
    ORDER BY a.created_at DESC 
    LIMIT 3
");
$stmtRelated->execute([$article['category_id'], $article['id']]);
$relatedArticles = $stmtRelated->fetchAll();

// Generate Math Captcha numbers
$num1 = rand(2, 9);
$num2 = rand(1, 8);
$captchaSum = $num1 + $num2;

$pageTitle = $article['title'];
$pageDescription = mb_substr(strip_tags($article['excerpt'] ?: $article['content']), 0, 150);
require_once __DIR__ . '/includes/header.php';
?>

<!-- ARTICLE HEADER -->
<section class="article-detail-header">
    <div class="container position-relative" style="z-index: 5;" data-aos="fade-down">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php" class="text-info text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/artikel.php" class="text-info text-decoration-none">Artikel</a></li>
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/artikel.php?kategori=<?= urlencode($article['category_slug']) ?>" class="text-info text-decoration-none"><?= htmlspecialchars($article['category_name']) ?></a></li>
                <li class="breadcrumb-item active text-white opacity-75" aria-current="page"><?= htmlspecialchars(mb_substr($article['title'], 0, 30)) ?>...</li>
            </ol>
        </nav>

        <span class="badge bg-primary bg-opacity-25 text-info px-3 py-2 rounded-pill fw-bold mb-3">
            <?= htmlspecialchars($article['category_name']) ?>
        </span>

        <h1 class="display-5 fw-bold text-white mb-4" style="line-height: 1.25; max-width: 900px;">
            <?= htmlspecialchars($article['title']) ?>
        </h1>

        <div class="d-flex flex-wrap align-items-center gap-4 text-light opacity-90 small">
            <span><i class="bi bi-person-fill text-info me-1"></i> Ditulis oleh: <strong><?= htmlspecialchars($article['author_name'] ?: 'Humas SMK BNB') ?></strong></span>
            <span><i class="bi bi-calendar3 text-info me-1"></i> <?= formatDateIndo($article['created_at'], true) ?></span>
            <span><i class="bi bi-eye-fill text-info me-1"></i> <?= $article['views'] ?> Dilihat</span>
            <span><i class="bi bi-chat-dots-fill text-info me-1"></i> <?= $totalComments ?> Komentar</span>
        </div>
    </div>
</section>

<!-- ARTICLE CONTENT & COMMENT AREA -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row g-5">
            <!-- MAIN ARTICLE & COMMENTS (LEFT) -->
            <div class="col-lg-8">
                
                <!-- ARTICLE CONTENT CARD -->
                <article class="article-content-card mb-5" data-aos="fade-up">
                    <?php if (!empty($article['thumbnail'])): ?>
                    <div class="mb-4">
                        <img src="<?= SITE_URL . '/' . htmlspecialchars($article['thumbnail']) ?>" alt="<?= htmlspecialchars($article['title']) ?>" class="w-100 rounded-4 shadow-sm" style="max-height: 480px; object-fit: cover;">
                    </div>
                    <?php endif; ?>

                    <!-- CONTENT BODY -->
                    <div class="article-body-text">
                        <?= $article['content'] ?>
                    </div>

                    <!-- SOCIAL SHARE -->
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pt-4 mt-5 border-top">
                        <div class="fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-share-fill text-primary"></i> Bagikan Artikel Ini:
                        </div>
                        <div class="d-flex gap-2">
                            <a href="https://api.whatsapp.com/send?text=<?= urlencode($article['title'] . ' - ' . SITE_URL . '/artikel-detail.php?slug=' . $article['slug']) ?>" target="_blank" class="btn btn-sm btn-success rounded-pill px-3">
                                <i class="bi bi-whatsapp me-1"></i> WhatsApp
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(SITE_URL . '/artikel-detail.php?slug=' . $article['slug']) ?>" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3">
                                <i class="bi bi-facebook me-1"></i> Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?text=<?= urlencode($article['title']) ?>&url=<?= urlencode(SITE_URL . '/artikel-detail.php?slug=' . $article['slug']) ?>" target="_blank" class="btn btn-sm btn-dark rounded-pill px-3">
                                <i class="bi bi-twitter-x me-1"></i> Twitter
                            </a>
                        </div>
                    </div>
                </article>

                <!-- COMMENTS SECTION -->
                <div class="comment-box" id="komentar" data-aos="fade-up">
                    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                        <h4 class="fw-bold mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-chat-left-text-fill text-primary"></i> Komentar Pengunjung (<?= $totalComments ?>)
                        </h4>
                    </div>

                    <!-- ALERT MESSAGE BOX FOR AJAX RESPONSE -->
                    <div id="commentAlertBox"></div>

                    <!-- COMMENT SUBMISSION FORM -->
                    <div class="p-4 bg-light rounded-4 border mb-5">
                        <h5 class="fw-bold mb-3 text-dark">Tulis Komentar Anda</h5>
                        
                        <form id="commentForm">
                            <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                            <input type="hidden" name="captcha_expected" value="<?= $captchaSum ?>">

                            <!-- ANONYMOUS TOGGLE CHECKBOX -->
                            <div class="form-check form-switch p-3 bg-white rounded-3 border mb-3">
                                <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" id="is_anonymous" name="is_anonymous" value="1" style="cursor: pointer; transform: scale(1.3);">
                                <label class="form-check-label fw-bold text-dark" for="is_anonymous" style="cursor: pointer;">
                                    <i class="bi bi-incognito text-secondary me-1"></i> Kirim sebagai Anonim (Tanpa Menampilkan Identitas)
                                </label>
                                <div class="small text-muted mt-1">Jika dicentang, nama dan email Anda tidak akan dicatat atau ditampilkan di kolom komentar.</div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6" id="name_input_group">
                                    <label for="comment_name" class="form-label fw-semibold text-dark">Nama Lengkap <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="bi bi-person text-muted"></i></span>
                                        <input type="text" class="form-control" id="comment_name" name="name" placeholder="Masukkan nama Anda" required>
                                    </div>
                                </div>
                                <div class="col-md-6" id="email_input_group">
                                    <label for="comment_email" class="form-label fw-semibold text-dark">Alamat Email <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="bi bi-envelope text-muted"></i></span>
                                        <input type="email" class="form-control" id="comment_email" name="email" placeholder="nama@email.com" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="comment_text" class="form-label fw-semibold text-dark">Pesan Komentar <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="comment_text" name="comment" rows="4" placeholder="Tuliskan tanggapan, pertanyaan, atau apresiasi Anda terkait artikel ini..." required></textarea>
                                </div>

                                <!-- MATH CAPTCHA ANTI-SPAM -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">
                                        Verifikasi Keamanan: <strong>Berapa <?= $num1 ?> + <?= $num2 ?> = ?</strong> <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="bi bi-shield-check text-success"></i></span>
                                        <input type="number" class="form-control" name="captcha_answer" placeholder="Tulis hasil angka" required>
                                    </div>
                                </div>

                                <div class="col-12 text-end mt-4">
                                    <button type="submit" id="btnSubmitComment" class="btn btn-ppdb px-4 py-2">
                                        <i class="bi bi-send-fill"></i> Kirim Komentar Sekarang
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- COMMENTS LIST -->
                    <div id="commentsList">
                        <?php if (empty($comments)): ?>
                        <div id="noCommentAlert" class="text-center py-4 text-muted">
                            <i class="bi bi-chat-square-text fs-2 d-block mb-2 text-secondary"></i>
                            Jadilah orang pertama yang memberikan komentar pada artikel ini!
                        </div>
                        <?php else: ?>
                            <?php foreach ($comments as $com): ?>
                            <div class="comment-item" id="comment-<?= $com['id'] ?>">
                                <div class="comment-header">
                                    <div class="comment-user-info">
                                        <?php if ($com['is_anonymous']): ?>
                                            <div class="comment-avatar anonymous"><i class="bi bi-incognito"></i></div>
                                            <div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <strong class="text-dark">Anonim</strong>
                                                    <span class="badge-anonymous"><i class="bi bi-incognito me-1"></i>Anonim</span>
                                                </div>
                                                <small class="text-muted"><i class="bi bi-clock me-1"></i><?= timeAgo($com['created_at']) ?></small>
                                            </div>
                                        <?php else: ?>
                                            <div class="comment-avatar"><?= strtoupper(mb_substr($com['name'], 0, 1)) ?></div>
                                            <div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <strong class="text-dark"><?= htmlspecialchars($com['name']) ?></strong>
                                                    <span class="badge-verified"><i class="bi bi-shield-check me-1"></i>Terverifikasi</span>
                                                </div>
                                                <small class="text-muted"><i class="bi bi-clock me-1"></i><?= timeAgo($com['created_at']) ?></small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <p class="mb-0 text-secondary" style="font-size: 0.95rem; line-height: 1.6;">
                                    <?= nl2br(htmlspecialchars($com['comment'])) ?>
                                </p>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                </div>

            </div>

            <!-- SIDEBAR (RIGHT) -->
            <div class="col-lg-4">
                <!-- RELATED ARTICLES -->
                <?php if (!empty($relatedArticles)): ?>
                <div class="card border-0 rounded-4 shadow-sm p-4 mb-4 bg-white" data-aos="fade-left">
                    <h5 class="fw-bold mb-4 pb-2 border-bottom d-flex align-items-center gap-2">
                        <i class="bi bi-collection-fill text-primary"></i> Artikel Terkait
                    </h5>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($relatedArticles as $rel): ?>
                        <div class="d-flex align-items-center gap-3">
                            <img src="<?= SITE_URL . '/' . htmlspecialchars($rel['thumbnail'] ?: 'assets/images/hero-3d.jpg') ?>" alt="<?= htmlspecialchars($rel['title']) ?>" class="rounded-3 object-fit-cover flex-shrink-0" style="width: 80px; height: 65px;">
                            <div>
                                <small class="text-primary fw-bold" style="font-size: 0.72rem;"><?= htmlspecialchars($rel['category_name']) ?></small>
                                <h6 class="mb-1 fw-bold" style="font-size: 0.88rem; line-height: 1.3;">
                                    <a href="<?= SITE_URL ?>/artikel-detail.php?slug=<?= urlencode($rel['slug']) ?>" class="text-dark text-decoration-none hover-primary">
                                        <?= htmlspecialchars(mb_substr($rel['title'], 0, 50)) ?>...
                                    </a>
                                </h6>
                                <small class="text-muted"><?= formatDateIndo($rel['created_at']) ?></small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- CONTACT / PPDB PROMO -->
                <div class="card border-0 rounded-4 shadow-sm p-4 text-white text-center" style="background: linear-gradient(135deg, #0b1329, #1e3a8a);" data-aos="fade-left" data-aos-delay="100">
                    <i class="bi bi-mortarboard-fill fs-1 text-info mb-2"></i>
                    <h4 class="fw-bold mb-2">Tertarik Bergabung?</h4>
                    <p class="small text-light opacity-75 mb-4">Konsultasikan jurusan dan jalur beasiswa Anda langsung dengan tim konselor kami.</p>
                    <a href="<?= SITE_URL ?>/kontak.php#ppdb" class="btn btn-ppdb w-100 justify-content-center py-2">Info Pendaftaran</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
