<?php
// kontak.php
// SMK Bangun Nusa Bangsa - Contact & PPDB Inquiry Page

$pageTitle = 'Hubungi Kami & Pendaftaran PPDB';
require_once __DIR__ . '/includes/header.php';
$settings = getSiteSettings();

$messageSent = false;
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? 'Pesan Umum');
    $message = sanitize($_POST['message'] ?? '');

    if (!empty($name) && !empty($email) && !empty($message) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $subject, $message]);
        $messageSent = true;
    } else {
        $errorMessage = 'Mohon lengkapi seluruh isian formulir dengan benar.';
    }
}
?>

<!-- CONTACT HEADER -->
<section class="article-detail-header text-center">
    <div class="container position-relative" style="z-index: 5;" data-aos="fade-down">
        <span class="badge bg-primary bg-opacity-25 text-info px-3 py-2 rounded-pill fw-bold mb-3">LAYANAN INFORMASI</span>
        <h1 class="display-4 fw-bold text-white mb-3">Hubungi Kami & Informasi PPDB</h1>
        <p class="lead text-light opacity-75 mx-auto" style="max-width: 650px;">
            Kami siap melayani pertanyaan seputar program pendidikan, kemitraan industri, maupun pendaftaran peserta didik baru.
        </p>
    </div>
</section>

<!-- CONTACT & MAP SECTION -->
<section class="section-padding bg-light" id="ppdb">
    <div class="container">
        <div class="row g-5">
            <!-- CONTACT FORM (LEFT) -->
            <div class="col-lg-7" data-aos="fade-right">
                <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 bg-white">
                    <span class="section-tag">Formulir Pesan</span>
                    <h3 class="fw-bold mb-4">Kirim Pesan atau Pertanyaan PPDB</h3>

                    <?php if ($messageSent): ?>
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                        <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                        <div>
                            <strong>Pesan Anda Telah Terkirim!</strong> Tim administrasi kami akan segera menghubungi Anda kembali melalui email atau WhatsApp.
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php elseif (!empty($errorMessage)): ?>
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <?= htmlspecialchars($errorMessage) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <form action="<?= SITE_URL ?>/kontak.php" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control py-2" placeholder="Nama Anda / Calon Siswa" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Alamat Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control py-2" placeholder="email@domain.com" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Topik / Keperluan <span class="text-danger">*</span></label>
                                <select name="subject" class="form-select py-2" required>
                                    <option value="Informasi Pendaftaran PPDB 2026">Informasi Pendaftaran PPDB 2026/2027</option>
                                    <option value="Konsultasi Pemilihan Jurusan">Konsultasi Pemilihan Jurusan (RPL/TKJ/DKV/TRO)</option>
                                    <option value="Kerja Sama Kemitraan Industri">Kerja Sama Kemitraan Industri & Magang</option>
                                    <option value="Pertanyaan Umum Lainnya">Pertanyaan Umum Lainnya</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Isi Pesan <span class="text-danger">*</span></label>
                                <textarea name="message" rows="5" class="form-control" placeholder="Tuliskan pertanyaan atau informasi yang ingin Anda ketahui..." required></textarea>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-ppdb w-100 py-3 justify-content-center fs-6">
                                    <i class="bi bi-send-fill"></i> Kirim Pesan Sekarang
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- CONTACT DETAILS (RIGHT) -->
            <div class="col-lg-5" data-aos="fade-left">
                <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 text-white mb-4" style="background: linear-gradient(135deg, #0b1329 0%, #1e3a8a 100%);">
                    <h4 class="fw-bold mb-4">Informasi Kontak</h4>
                    
                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="rounded-circle p-2 bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                            <i class="bi bi-geo-alt-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-info">Alamat Kampus:</div>
                            <div class="small opacity-90"><?= htmlspecialchars($settings['school_address'] ?? 'Jl. Pendidikan Karakter Bangsa No. 88, Jakarta') ?></div>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="rounded-circle p-2 bg-success text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                            <i class="bi bi-telephone-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-info">Telepon & WhatsApp:</div>
                            <div class="small opacity-90"><?= htmlspecialchars($settings['school_phone'] ?? '(021) 8899-7722') ?> / <?= htmlspecialchars($settings['school_whatsapp'] ?? '081234567890') ?></div>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="rounded-circle p-2 bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                            <i class="bi bi-envelope-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-info">Email Resmi:</div>
                            <div class="small opacity-90"><?= htmlspecialchars($settings['school_email'] ?? 'info@smk-bangunnusabangsa.sch.id') ?></div>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-circle p-2 bg-info text-dark d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                            <i class="bi bi-clock-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-info">Jam Pelayanan:</div>
                            <div class="small opacity-90">Senin - Jumat: 07.30 - 16.00 WIB<br>Sabtu: 08.00 - 12.00 WIB</div>
                        </div>
                    </div>
                </div>

                <!-- EMBEDDED MAP -->
                <div class="card border-0 rounded-4 shadow-sm overflow-hidden" style="height: 250px;">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126917.4069818815!2d106.7891823972656!3d-6.241586599999994!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f3e8f1b637d7%3A0x6e3d23194a20b784!2sJakarta!5e0!3m2!1sid!2sid!4v1700000000000" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
