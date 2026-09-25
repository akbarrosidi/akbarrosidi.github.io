<?php
// admin/majors.php
// SMK Bangun Nusa Bangsa - Majors Management

$adminTitle = 'Program Keahlian (Jurusan)';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$pdo = getDBConnection();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_major') {
    $id = (int)$_POST['major_id'];
    $name = sanitize($_POST['name'] ?? '');
    $tagline = sanitize($_POST['tagline'] ?? '');
    $description = $_POST['description'] ?? '';
    $competencies = sanitize($_POST['competencies'] ?? '');
    $careers = sanitize($_POST['careers'] ?? '');

    try {
        $stmt = $pdo->prepare("UPDATE majors SET name = ?, tagline = ?, description = ?, competencies = ?, careers = ? WHERE id = ?");
        $stmt->execute([$name, $tagline, $description, $competencies, $careers, $id]);
        setFlashMessage('success', 'Data jurusan "' . htmlspecialchars($name) . '" berhasil diperbarui.');
        echo "<script>window.location.href = '" . SITE_URL . "/admin/majors.php';</script>";
        exit;
    } catch (Exception $e) {
        $error = 'Gagal menyimpan data: ' . $e->getMessage();
    }
}

$majors = $pdo->query("SELECT * FROM majors ORDER BY id ASC")->fetchAll();
?>

<!-- PAGE HEADER -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Program Keahlian (Jurusan)</h2>
        <p class="text-muted mb-0">Kelola informasi kurikulum, kompetensi utama, dan prospek karir tiap jurusan kejuruan.</p>
    </div>
</div>

<?php if (!empty($error)): ?>
<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
    <?= htmlspecialchars($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row g-4">
    <?php foreach ($majors as $m): ?>
    <div class="col-lg-6">
        <div class="admin-card h-100">
            <div class="admin-card-header bg-light">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-<?= htmlspecialchars($m['badge_color']) ?> px-3 py-1 fs-6"><?= htmlspecialchars($m['code']) ?></span>
                    <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($m['name']) ?></h6>
                </div>
            </div>
            <div class="admin-card-body">
                <form action="<?= SITE_URL ?>/admin/majors.php" method="POST">
                    <input type="hidden" name="action" value="update_major">
                    <input type="hidden" name="major_id" value="<?= $m['id'] ?>">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Program Keahlian</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($m['name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tagline Jurusan</label>
                        <input type="text" name="tagline" class="form-control" value="<?= htmlspecialchars($m['tagline']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi Jurusan</label>
                        <textarea name="description" rows="3" class="form-control"><?= htmlspecialchars($m['description']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kompetensi Utama (Pisahkan koma)</label>
                        <textarea name="competencies" rows="2" class="form-control"><?= htmlspecialchars($m['competencies']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Peluang Karir</label>
                        <textarea name="careers" rows="2" class="form-control"><?= htmlspecialchars($m['careers']) ?></textarea>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-custom-primary btn-sm">
                            <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
