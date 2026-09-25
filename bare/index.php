<?php
// index.php
// SMK Bangun Nusa Bangsa - Home Page

$pageTitle = 'Beranda - Pusat Keunggulan Vokasi 4.0';
require_once __DIR__ . '/includes/header.php';

$pdo = getDBConnection();

// Fetch 4 featured majors
$stmtMajors = $pdo->query("SELECT * FROM majors ORDER BY id ASC LIMIT 4");
$majors = $stmtMajors->fetchAll();

// Fetch 3 latest published articles with category and comments count
$stmtArticles = $pdo->query("
    SELECT a.*, c.name AS category_name, c.slug AS category_slug,
           (SELECT COUNT(*) FROM comments cm WHERE cm.article_id = a.id AND cm.status = 'approved') AS total_comments
    FROM articles a
    JOIN categories c ON a.category_id = c.id
    WHERE a.status = 'published'
    ORDER BY a.created_at DESC
    LIMIT 3
");
$latestArticles = $stmtArticles->fetchAll();
?>

<!-- HERO SECTION 3D -->
<section class="hero-section">
    <div class="hero-glow-1"></div>
    <div class="hero-glow-2"></div>
    <div class="container position-relative" style="z-index: 10;">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="hero-badge">
                    <i class="bi bi-patch-check-fill text-warning"></i>
                    <span>Akreditasi A Unggul & Center of Excellence</span>
                </div>
                <h1 class="hero-title">
                    Wujudkan Masa Depan <span class="hero-title-highlight">Teknologi Global</span> di SMK Bangun Nusa Bangsa
                </h1>
                <p class="hero-subtitle">
                    Pendidikan kejuruan terdepan yang mengintegrasikan Artificial Intelligence, Cyber Security, Animasi 3D, dan Robotika Industri dengan standar internasional.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="<?= SITE_URL ?>/jurusan.php" class="btn btn-ppdb">
                        <i class="bi bi-compass-fill"></i> Jelajahi Program Keahlian
                    </a>
                    <a href="<?= SITE_URL ?>/artikel.php" class="btn btn-outline-light rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2">
                        <i class="bi bi-newspaper"></i> Portal Artikel & Berita
                    </a>
                </div>
            </div>
            <div class="col-lg-6" data-aos="zoom-in" data-aos-delay="200">
                <div class="hero-3d-visual-wrapper">
                    <img src="<?= SITE_URL ?>/assets/images/hero-3d.jpg" alt="Kampus 3D SMK Bangun Nusa Bangsa" class="hero-3d-image">
                    
                    <!-- Floating Stat 1 -->
                    <div class="floating-stat-card floating-stat-1">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle p-2 bg-primary text-white d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                <i class="bi bi-trophy-fill fs-5"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-white fs-6">Juara 1 Nasional</div>
                                <small class="text-info">Robotika & IoT 2026</small>
                            </div>
                        </div>
                    </div>

                    <!-- Floating Stat 2 -->
                    <div class="floating-stat-card floating-stat-2">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle p-2 bg-success text-white d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                <i class="bi bi-building-check fs-5"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-white fs-6">95%+ Lulusan</div>
                                <small class="text-success-emphasis">Langsung Kerja di DUDI</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- STATISTICS BANNER -->
<section class="stats-banner">
    <div class="container">
        <div class="stats-card-container">
            <div class="row g-4">
                <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-item">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>
                        <div class="stat-number">1.250+</div>
                        <div class="stat-label">Siswa Berprestasi</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-item">
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="bi bi-briefcase-fill"></i>
                        </div>
                        <div class="stat-number">45+</div>
                        <div class="stat-label">Mitra Industri Global</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="stat-item">
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-award-fill"></i>
                        </div>
                        <div class="stat-number">98%</div>
                        <div class="stat-label">Sertifikasi BNSP & Int.</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                    <div class="stat-item">
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-person-check-fill"></i>
                        </div>
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Penyaluran Magang</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SAMBUTAN KEPALA SEKOLAH -->
<section class="section-padding bg-white mt-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5" data-aos="fade-right">
                <div class="position-relative">
                    <div class="p-3 bg-light rounded-4 shadow-sm border">
                        <img src="<?= SITE_URL ?>/assets/images/artikel-prestasi.jpg" alt="Prestasi SMK Bangun Nusa Bangsa" class="img-fluid rounded-4 shadow">
                    </div>
                    <div class="position-absolute bottom-0 end-0 bg-primary text-white p-3 rounded-4 shadow mb-n3 me-n3 d-none d-md-block">
                        <div class="fw-bold fs-5">Akreditasi A</div>
                        <small>Predikat Unggul</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-7" data-aos="fade-left">
                <span class="section-tag">Sambutan Kepala Sekolah</span>
                <h2 class="section-title">Membangun Karakter & Keahlian Abad 21</h2>
                <p class="text-muted fs-5 mb-4">
                    "<?= nl2br(htmlspecialchars($settings['principal_welcome'] ?? 'Selamat datang di SMK Bangun Nusa Bangsa.')) ?>"
                </p>
                <div class="p-3 bg-light rounded-3 border-start border-4 border-primary mb-4">
                    <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($settings['principal_name'] ?? 'Drs. H. Hendra Wijaya, M.Kom.') ?></div>
                    <small class="text-muted">Kepala SMK Bangun Nusa Bangsa</small>
                </div>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                            <span class="fw-semibold">Teaching Factory 4.0</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                            <span class="fw-semibold">Kelas Industri Bersertifikasi</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                            <span class="fw-semibold">Laboratorium AI & Immersive VR</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                            <span class="fw-semibold">Inkubator Startup Siswa</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- PROGRAM KEAHLIAN / JURUSAN 3D -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-tag">Program Keahlian Unggulan</span>
            <h2 class="section-title">Pilihan Jurusan Masa Depan</h2>
            <p class="section-subtitle mx-auto">
                Kurikulum terintegrasi dengan kebutuhan industri revolusi 4.0, didukung sarana praktikum 3D & AI berstandar internasional.
            </p>
        </div>

        <div class="row g-4">
            <?php foreach ($majors as $index => $major): ?>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="<?= ($index + 1) * 100 ?>">
                <div class="major-card">
                    <div class="major-img-wrap">
                        <img src="<?= SITE_URL . '/' . htmlspecialchars($major['image'] ?: 'assets/images/hero-3d.jpg') ?>" alt="<?= htmlspecialchars($major['name']) ?>">
                        <span class="major-badge-code bg-<?= htmlspecialchars($major['badge_color']) ?> text-white">
                            <?= htmlspecialchars($major['code']) ?>
                        </span>
                    </div>
                    <div class="major-card-body">
                        <h3 class="major-title"><?= htmlspecialchars($major['name']) ?></h3>
                        <p class="major-tagline"><?= htmlspecialchars($major['tagline']) ?></p>
                        <a href="<?= SITE_URL ?>/jurusan.php#<?= strtolower($major['code']) ?>" class="btn btn-outline-primary btn-sm rounded-pill fw-semibold mt-auto align-self-start">
                            Detail Kompetensi <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-5">
            <a href="<?= SITE_URL ?>/jurusan.php" class="btn btn-primary px-4 py-2 rounded-pill fw-bold">
                Lihat Seluruh Detail Kurikulum <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- ARTIKEL & BERITA TERKINI -->
<section class="section-padding bg-white">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-5" data-aos="fade-up">
            <div>
                <span class="section-tag">Kabar & Publikasi Terkini</span>
                <h2 class="section-title mb-0">Artikel, Prestasi & Kegiatan</h2>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="<?= SITE_URL ?>/artikel.php" class="btn btn-outline-primary rounded-pill px-4 fw-semibold">
                    Semua Artikel <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>

        <div class="row g-4">
            <?php if (empty($latestArticles)): ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">Belum ada artikel yang dipublikasikan.</p>
                </div>
            <?php else: ?>
                <?php foreach ($latestArticles as $idx => $art): ?>
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= ($idx + 1) * 100 ?>">
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
                                <span><i class="bi bi-chat-left-text"></i> <?= $art['total_comments'] ?> Komentar</span>
                                <span><i class="bi bi-eye"></i> <?= $art['views'] ?></span>
                            </div>
                            <h3 class="article-card-title">
                                <a href="<?= SITE_URL ?>/artikel-detail.php?slug=<?= urlencode($art['slug']) ?>" class="article-title-link">
                                    <?= htmlspecialchars($art['title']) ?>
                                </a>
                            </h3>
                            <p class="article-excerpt">
                                <?= htmlspecialchars(mb_substr(strip_tags($art['excerpt'] ?: $art['content']), 0, 115)) ?>...
                            </p>
                            <a href="<?= SITE_URL ?>/artikel-detail.php?slug=<?= urlencode($art['slug']) ?>" class="btn btn-link text-primary p-0 text-decoration-none fw-bold align-self-start mt-auto">
                                Baca Selengkapnya <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- CALL TO ACTION PPDB -->
<section class="section-padding text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #0b1329 0%, #172554 100%);">
    <div class="container text-center position-relative" style="z-index: 5;" data-aos="zoom-in">
        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold mb-3 fs-6">PENERIMAAN PESERTA DIDIK BARU 2026/2027</span>
        <h2 class="display-5 fw-bold mb-3">Siap Menjadi Ahli Teknologi Masa Depan?</h2>
        <p class="lead text-light opacity-75 max-w-700 mx-auto mb-4" style="max-width: 650px;">
            Daftarkan diri Anda sekarang dan dapatkan beasiswa keahlian serta prioritas penyaluran kerja di perusahaan mitra multinasional.
        </p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="<?= SITE_URL ?>/kontak.php#ppdb" class="btn btn-ppdb btn-lg px-5 py-3 fs-6">
                <i class="bi bi-file-earmark-person-fill"></i> Daftar PPDB Online Sekarang
            </a>
            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $settings['school_whatsapp'] ?? '081234567890') ?>" target="_blank" class="btn btn-outline-light btn-lg px-4 py-3 rounded-pill fw-semibold d-inline-flex align-items-center gap-2">
                <i class="bi bi-whatsapp text-success"></i> Konsultasi WhatsApp
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
