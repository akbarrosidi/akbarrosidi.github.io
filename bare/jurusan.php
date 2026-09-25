<?php
// jurusan.php
// SMK Bangun Nusa Bangsa - Program Keahlian Page

$pageTitle = 'Program Keahlian - Jurusan Unggulan';
require_once __DIR__ . '/includes/header.php';

$pdo = getDBConnection();
$stmt = $pdo->query("SELECT * FROM majors ORDER BY id ASC");
$majors = $stmt->fetchAll();
?>

<!-- JURUSAN HEADER -->
<section class="article-detail-header text-center">
    <div class="container position-relative" style="z-index: 5;" data-aos="fade-down">
        <span class="badge bg-primary bg-opacity-25 text-info px-3 py-2 rounded-pill fw-bold mb-3">PROGRAM KEAHLIAN</span>
        <h1 class="display-4 fw-bold text-white mb-3">Jurusan Berstandar Industri 4.0</h1>
        <p class="lead text-light opacity-75 mx-auto" style="max-width: 650px;">
            Kurikulum berbasis kompetensi masa depan untuk mencetak talenta unggul yang siap kerja, berdaya saing global, dan berwirausaha.
        </p>
    </div>
</section>

<!-- DAFTAR JURUSAN DETAIL -->
<section class="section-padding bg-light">
    <div class="container">
        <?php foreach ($majors as $index => $m): ?>
        <div id="<?= strtolower($m['code']) ?>" class="card border-0 rounded-4 shadow-sm mb-5 overflow-hidden" data-aos="fade-up" data-aos-delay="<?= ($index + 1) * 100 ?>">
            <div class="row g-0 align-items-center">
                <div class="col-lg-5 <?= $index % 2 == 1 ? 'order-lg-2' : '' ?>">
                    <div class="position-relative h-100" style="min-height: 380px;">
                        <img src="<?= SITE_URL . '/' . htmlspecialchars($m['image'] ?: 'assets/images/hero-3d.jpg') ?>" alt="<?= htmlspecialchars($m['name']) ?>" class="w-100 h-100 object-fit-cover">
                        <div class="position-absolute top-0 start-0 m-3">
                            <span class="badge bg-<?= htmlspecialchars($m['badge_color']) ?> fs-6 px-3 py-2 rounded-pill shadow">
                                <?= htmlspecialchars($m['code']) ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 <?= $index % 2 == 1 ? 'order-lg-1' : '' ?>">
                    <div class="p-4 p-md-5">
                        <span class="badge bg-light text-primary border px-3 py-2 rounded-pill fw-bold mb-2">Program Unggulan</span>
                        <h2 class="fw-bold mb-2"><?= htmlspecialchars($m['name']) ?></h2>
                        <p class="text-primary fw-semibold mb-3"><?= htmlspecialchars($m['tagline']) ?></p>
                        <p class="text-muted leading-relaxed mb-4">
                            <?= nl2br(htmlspecialchars($m['description'])) ?>
                        </p>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 h-100 border">
                                    <div class="d-flex align-items-center gap-2 mb-2 text-primary fw-bold">
                                        <i class="bi bi-tools"></i> <span>Kompetensi Utama</span>
                                    </div>
                                    <p class="small text-muted mb-0"><?= htmlspecialchars($m['competencies']) ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 h-100 border">
                                    <div class="d-flex align-items-center gap-2 mb-2 text-success fw-bold">
                                        <i class="bi bi-briefcase"></i> <span>Peluang Karir</span>
                                    </div>
                                    <p class="small text-muted mb-0"><?= htmlspecialchars($m['careers']) ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-3 flex-wrap">
                            <a href="<?= SITE_URL ?>/kontak.php#ppdb" class="btn btn-ppdb">
                                <i class="bi bi-person-plus-fill"></i> Daftar Jurusan Ini
                            </a>
                            <a href="<?= SITE_URL ?>/artikel.php" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold">
                                Lihat Berita Jurusan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
