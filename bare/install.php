<?php
// install.php
// SMK Bangun Nusa Bangsa - Database Setup & Installer Script

require_once __DIR__ . '/config/database.php';

$message = '';
$status = '';

if (isset($_POST['install']) || isset($_GET['auto'])) {
    try {
        $pdo = getDBConnection();
        $message = "Database `smk_bangun_nusa_bangsa` dan seluruh tabel berhasil diinisialisasi beserta data awal!";
        $status = 'success';
    } catch (Exception $e) {
        $message = "Gagal inisialisasi: " . $e->getMessage();
        $status = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inisialisasi Database | SMK Bangun Nusa Bangsa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
    <div class="container" style="max-width: 600px;">
        <div class="card border-0 rounded-4 shadow p-4 p-md-5 bg-white text-center">
            <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 d-inline-flex mx-auto mb-3" style="width: 70px; height: 70px; align-items: center; justify-content: center; font-size: 2rem;">
                <i class="bi bi-database-check"></i>
            </div>
            
            <h3 class="fw-bold mb-2">Inisialisasi Database</h3>
            <p class="text-muted small mb-4">SMK BANGUN NUSA BANGSA</p>

            <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $status ?> text-start mb-4" role="alert">
                <i class="bi <?= $status === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?> me-2"></i>
                <?= htmlspecialchars($message) ?>
            </div>
            <?php if ($status === 'success'): ?>
                <div class="d-grid gap-2 mb-3">
                    <a href="<?= SITE_URL ?>/index.php" class="btn btn-primary py-2 fw-bold">
                        <i class="bi bi-globe me-1"></i> Buka Website Utama
                    </a>
                    <a href="<?= SITE_URL ?>/admin/login.php" class="btn btn-outline-dark py-2 fw-bold">
                        <i class="bi bi-shield-lock me-1"></i> Masuk Dashboard Admin (admin / admin123)
                    </a>
                </div>
            <?php endif; ?>
            <?php else: ?>
            <p class="text-secondary text-start mb-4">
                Klik tombol di bawah untuk membuat database <code>smk_bangun_nusa_bangsa</code>, tabel pengguna, kategori, artikel, jurusan, dan sample data secara otomatis.
            </p>
            <form action="install.php" method="POST">
                <button type="submit" name="install" class="btn btn-primary btn-lg w-100 py-3 rounded-pill fw-bold">
                    <i class="bi bi-gear-wide-connected me-2"></i> Jalankan Inisialisasi Database
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
