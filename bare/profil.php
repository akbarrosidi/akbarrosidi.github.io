<?php
// profil.php
// SMK Bangun Nusa Bangsa - School Profile Page

$pageTitle = 'Profil Sekolah - Visi, Misi & Sejarah';
require_once __DIR__ . '/includes/header.php';
$settings = getSiteSettings();
?>

<!-- PROFILE HEADER -->
<section class="article-detail-header text-center">
    <div class="container position-relative" style="z-index: 5;" data-aos="fade-down">
        <span class="badge bg-primary bg-opacity-25 text-info px-3 py-2 rounded-pill fw-bold mb-3">TENTANG KAMI</span>
        <h1 class="display-4 fw-bold text-white mb-3">Profil SMK Bangun Nusa Bangsa</h1>
        <p class="lead text-light opacity-75 mx-auto" style="max-width: 650px;">
            Mengenal lebih dekat visi, misi, sejarah, dan ekosistem pendidikan kejuruan modern berbasis industri 4.0.
        </p>
    </div>
</section>

<!-- SEJARAH & SAMBUTAN -->
<section class="section-padding bg-white">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-right">
                <span class="section-tag">Sejarah & Perjalanan</span>
                <h2 class="section-title">Dedikasi Mencetak Insan Vokasi Berdaya Saing</h2>
                <p class="text-muted leading-relaxed mb-4">
                    <strong>SMK Bangun Nusa Bangsa</strong> didirikan dengan semangat untuk mentransformasi pendidikan kejuruan di Indonesia agar selaras dengan akselerasi teknologi global. Berawal dari inisiatif para praktisi industri IT dan akademisi terkemuka, sekolah ini berkembang pesat menjadi <em>Center of Excellence</em> vokasi.
                </p>
                <p class="text-muted leading-relaxed mb-4">
                    Melalui model pembelajaran <strong>Teaching Factory</strong> dan integrasi kurikulum industri secara berkesinambungan, kami memastikan seluruh lulusan memiliki kompetensi teknis (hard skills) yang mutakhir serta integritas karakter (soft skills) yang tangguh.
                </p>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-3 text-center border">
                            <div class="h3 fw-bold text-primary mb-1">A (Unggul)</div>
                            <small class="text-muted">Akreditasi BAN-SM</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-3 text-center border">
                            <div class="h3 fw-bold text-success mb-1">ISO 9001:2015</div>
                            <small class="text-muted">Standar Mutu Pendidikan</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="position-relative">
                    <img src="<?= SITE_URL ?>/assets/images/hero-3d.jpg" alt="Kampus SMK Bangun Nusa Bangsa" class="img-fluid rounded-4 shadow-lg border">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- VISI DAN MISI -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                <div class="h-100 p-5 bg-white rounded-4 shadow-sm border border-primary border-2">
                    <div class="d-inline-flex p-3 rounded-3 bg-primary text-white mb-4 fs-3">
                        <i class="bi bi-eye-fill"></i>
                    </div>
                    <h3 class="fw-bold mb-3">Visi Sekolah</h3>
                    <p class="fs-5 text-muted leading-relaxed">
                        "<?= nl2br(htmlspecialchars($settings['school_vision'] ?? 'Menjadi SMK Pusat Keunggulan yang mencetak lulusan berkarakter unggul, kompeten, dan siap bersaing di tingkat internasional.')) ?>"
                    </p>
                </div>
            </div>

            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
                <div class="h-100 p-5 bg-white rounded-4 shadow-sm border border-info border-2">
                    <div class="d-inline-flex p-3 rounded-3 bg-info text-white mb-4 fs-3">
                        <i class="bi bi-bullseye"></i>
                    </div>
                    <h3 class="fw-bold mb-3">Misi Sekolah</h3>
                    <div class="text-muted leading-relaxed">
                        <?= nl2br(htmlspecialchars($settings['school_mission'] ?? "1. Menyelenggarakan pendidikan kejuruan berstandar industri.\n2. Mengembangkan karakter disiplin, mandiri, dan inovatif.\n3. Memperluas kerja sama dengan dunia usaha dan dunia industri.")) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FASILITAS UNGGULAN -->
<section class="section-padding bg-white">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-tag">Sarana & Prasarana</span>
            <h2 class="section-title">Fasilitas Modern Standar Industri 4.0</h2>
            <p class="section-subtitle mx-auto">
                Dukungan infrastruktur belajar terlengkap untuk menunjang kenyamanan dan efektivitas proses pembelajaran siswa.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                <div class="p-4 bg-light rounded-4 h-100 border text-center">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary mx-auto mb-3">
                        <i class="bi bi-laptop"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Lab AI & Cloud Server</h5>
                    <p class="text-muted small">Workstation spesifikasi tinggi dengan koneksi internet gigabit untuk komputasi AI & Cloud.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                <div class="p-4 bg-light rounded-4 h-100 border text-center">
                    <div class="stat-icon bg-danger bg-opacity-10 text-danger mx-auto mb-3">
                        <i class="bi bi-badge-vr"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Studio 3D & Virtual Reality</h5>
                    <p class="text-muted small">Perangkat VR canggih, motion capture, dan display grafis penunjang produksi animasi dan game.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                <div class="p-4 bg-light rounded-4 h-100 border text-center">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning mx-auto mb-3">
                        <i class="bi bi-robot"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Bengkel Otomasi & Robotik</h5>
                    <p class="text-muted small">Lini perakitan robotik, trainer PLC industri, simulator EV (Electric Vehicle), & IoT Kit.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                <div class="p-4 bg-light rounded-4 h-100 border text-center">
                    <div class="stat-icon bg-success bg-opacity-10 text-success mx-auto mb-3">
                        <i class="bi bi-book"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Digital Smart Library</h5>
                    <p class="text-muted small">Perpustakaan digital interaktif dengan ribuan referensi e-book, jurnal internasional, dan area coworking.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
