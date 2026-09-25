<?php
// includes/footer.php
// SMK Bangun Nusa Bangsa - Public Footer

$settings = getSiteSettings();
?>
    <!-- FOOTER -->
    <footer class="main-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="brand-icon-box">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>
                        <div>
                            <div class="footer-brand-title">SMK BANGUN NUSA BANGSA</div>
                            <small class="text-info">Center of Vocational Excellence</small>
                        </div>
                    </div>
                    <p class="text-secondary small mb-4">
                        <?= htmlspecialchars($settings['school_slogan'] ?? 'Mencetak Generasi Unggul, Berkarakter & Berdaya Saing Global') ?>. Sekolah kejuruan berbasis industri dengan kurikulum terkini, teknologi 4.0, dan penyaluran kerja terjamin.
                    </p>
                    <div class="d-flex gap-2">
                        <a href="<?= htmlspecialchars($settings['facebook_url'] ?? '#') ?>" class="footer-social-icon" target="_blank"><i class="bi bi-facebook"></i></a>
                        <a href="<?= htmlspecialchars($settings['instagram_url'] ?? '#') ?>" class="footer-social-icon" target="_blank"><i class="bi bi-instagram"></i></a>
                        <a href="<?= htmlspecialchars($settings['youtube_url'] ?? '#') ?>" class="footer-social-icon" target="_blank"><i class="bi bi-youtube"></i></a>
                        <a href="<?= htmlspecialchars($settings['linkedin_url'] ?? '#') ?>" class="footer-social-icon" target="_blank"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h5 class="footer-heading">Navigasi</h5>
                    <ul class="footer-links">
                        <li><a href="<?= SITE_URL ?>/index.php"><i class="bi bi-chevron-right text-primary"></i> Beranda</a></li>
                        <li><a href="<?= SITE_URL ?>/profil.php"><i class="bi bi-chevron-right text-primary"></i> Profil Sekolah</a></li>
                        <li><a href="<?= SITE_URL ?>/jurusan.php"><i class="bi bi-chevron-right text-primary"></i> Program Keahlian</a></li>
                        <li><a href="<?= SITE_URL ?>/artikel.php"><i class="bi bi-chevron-right text-primary"></i> Berita & Artikel</a></li>
                        <li><a href="<?= SITE_URL ?>/kontak.php"><i class="bi bi-chevron-right text-primary"></i> Hubungi Kami</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-heading">Program Keahlian</h5>
                    <ul class="footer-links">
                        <li><a href="<?= SITE_URL ?>/jurusan.php#rpl"><i class="bi bi-code-slash text-info"></i> Rekayasa Perangkat Lunak & AI</a></li>
                        <li><a href="<?= SITE_URL ?>/jurusan.php#tkj"><i class="bi bi-shield-lock text-info"></i> Jaringan Komputer & Cyber</a></li>
                        <li><a href="<?= SITE_URL ?>/jurusan.php#dkv"><i class="bi bi-palette text-info"></i> Desain Komunikasi Visual (DKV)</a></li>
                        <li><a href="<?= SITE_URL ?>/jurusan.php#tro"><i class="bi bi-robot text-info"></i> Mekatronika & Otomasi Industri</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-heading">Kontak & Lokasi</h5>
                    <ul class="footer-links text-secondary small">
                        <li class="d-flex align-items-start gap-2 mb-2">
                            <i class="bi bi-geo-alt-fill text-danger fs-5"></i>
                            <span><?= htmlspecialchars($settings['school_address'] ?? 'Jl. Pendidikan Karakter Bangsa No. 88, Jakarta') ?></span>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-telephone-fill text-success fs-5"></i>
                            <span><?= htmlspecialchars($settings['school_phone'] ?? '(021) 8899-7722') ?></span>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-whatsapp text-success fs-5"></i>
                            <span><?= htmlspecialchars($settings['school_whatsapp'] ?? '081234567890') ?></span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-envelope-fill text-warning fs-5"></i>
                            <span><?= htmlspecialchars($settings['school_email'] ?? 'info@smk-bangunnusabangsa.sch.id') ?></span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center text-center">
                <p class="mb-2 mb-md-0 text-secondary">
                    &copy; <?= date('Y') ?> <strong>SMK Bangun Nusa Bangsa</strong>. All Rights Reserved. Built with Modern Excellence.
                </p>
                <div class="d-flex gap-3 small text-secondary">
                    <a href="<?= SITE_URL ?>/admin/login.php" class="text-secondary text-decoration-none hover-primary"><i class="bi bi-lock-fill me-1"></i>Administrator Login</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
    </script>
    <!-- Custom Frontend JS -->
    <script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</body>
</html>
