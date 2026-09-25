<?php
// includes/header.php
// SMK Bangun Nusa Bangsa - Public Header

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

$settings = getSiteSettings();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : '' ?><?= htmlspecialchars($settings['school_name'] ?? 'SMK Bangun Nusa Bangsa') ?></title>
    <meta name="description" content="<?= isset($pageDescription) ? htmlspecialchars($pageDescription) : htmlspecialchars($settings['school_slogan'] ?? 'Mencetak Generasi Unggul, Berkarakter & Berdaya Saing Global') ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= SITE_URL ?>/assets/images/logo.png" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🎓</text></svg>'">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= SITE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>

    <!-- TOPBAR -->
    <div class="top-bar d-none d-md-block">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-4">
                <span><i class="bi bi-geo-alt-fill text-info me-2"></i><?= htmlspecialchars($settings['school_address'] ?? 'Jakarta') ?></span>
                <span><i class="bi bi-telephone-fill text-info me-2"></i><?= htmlspecialchars($settings['school_phone'] ?? '(021) 8899-7722') ?></span>
                <span><i class="bi bi-envelope-fill text-info me-2"></i><?= htmlspecialchars($settings['school_email'] ?? 'info@smk-bangunnusabangsa.sch.id') ?></span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="<?= htmlspecialchars($settings['facebook_url'] ?? '#') ?>" target="_blank" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                <a href="<?= htmlspecialchars($settings['instagram_url'] ?? '#') ?>" target="_blank" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                <a href="<?= htmlspecialchars($settings['youtube_url'] ?? '#') ?>" target="_blank" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                <a href="<?= SITE_URL ?>/admin/login.php" class="ms-2 badge bg-primary text-white text-decoration-none py-1 px-2"><i class="bi bi-shield-lock-fill me-1"></i>Portal Admin</a>
            </div>
        </div>
    </div>

    <!-- MAIN NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand navbar-brand-custom" href="<?= SITE_URL ?>/index.php">
                <div class="brand-icon-box">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <div>
                    <div class="brand-text-main">SMK BANGUN NUSA BANGSA</div>
                    <div class="brand-text-sub">Vocational Center of Excellence</div>
                </div>
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <i class="bi bi-list fs-2"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom <?= $currentPage === 'index' ? 'active' : '' ?>" href="<?= SITE_URL ?>/index.php">
                            <i class="bi bi-house-door me-1"></i> Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom <?= $currentPage === 'profil' ? 'active' : '' ?>" href="<?= SITE_URL ?>/profil.php">
                            <i class="bi bi-info-circle me-1"></i> Profil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom <?= $currentPage === 'jurusan' ? 'active' : '' ?>" href="<?= SITE_URL ?>/jurusan.php">
                            <i class="bi bi-grid-fill me-1"></i> Jurusan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom <?= in_array($currentPage, ['artikel', 'artikel-detail']) ? 'active' : '' ?>" href="<?= SITE_URL ?>/artikel.php">
                            <i class="bi bi-newspaper me-1"></i> Artikel & Berita
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom <?= $currentPage === 'kontak' ? 'active' : '' ?>" href="<?= SITE_URL ?>/kontak.php">
                            <i class="bi bi-telephone me-1"></i> Kontak
                        </a>
                    </li>
                    <li class="nav-item ms-lg-3 mt-3 mt-lg-0">
                        <a href="<?= SITE_URL ?>/kontak.php#ppdb" class="btn btn-ppdb">
                            <i class="bi bi-rocket-takeoff-fill"></i> PPDB 2026/2027
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
