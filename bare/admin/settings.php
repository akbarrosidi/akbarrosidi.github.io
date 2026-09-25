<?php
// admin/settings.php
// SMK Bangun Nusa Bangsa - School Settings & Profile Information

$adminTitle = 'Profil & Pengaturan Sekolah';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$pdo = getDBConnection();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_settings') {
    $settingsToUpdate = [
        'school_name', 'school_slogan', 'school_address', 'school_phone',
        'school_email', 'school_whatsapp', 'principal_name', 'principal_welcome',
        'school_vision', 'school_mission', 'facebook_url', 'instagram_url',
        'youtube_url', 'linkedin_url'
    ];

    try {
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        foreach ($settingsToUpdate as $key) {
            $val = $_POST[$key] ?? '';
            $stmt->execute([$key, $val, $val]);
        }

        setFlashMessage('success', 'Pengaturan profil sekolah berhasil disimpan.');
        echo "<script>window.location.href = '" . SITE_URL . "/admin/settings.php';</script>";
        exit;
    } catch (Exception $e) {
        $error = 'Gagal menyimpan pengaturan: ' . $e->getMessage();
    }
}

$settings = getSiteSettings();
?>

<!-- PAGE HEADER -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Profil & Pengaturan Sekolah</h2>
        <p class="text-muted mb-0">Ubah identitas sekolah, visi-misi, kontak resmi, dan sambutan kepala sekolah.</p>
    </div>
</div>

<?php if (!empty($error)): ?>
<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
    <?= htmlspecialchars($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<form action="<?= SITE_URL ?>/admin/settings.php" method="POST">
    <input type="hidden" name="action" value="save_settings">

    <div class="row g-4">
        <!-- GENERAL & PRINCIPAL (LEFT) -->
        <div class="col-lg-6">
            <div class="admin-card mb-4">
                <div class="admin-card-header">
                    <h6 class="fw-bold mb-0"><i class="bi bi-building me-2 text-primary"></i>Identitas Sekolah</h6>
                </div>
                <div class="admin-card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Sekolah</label>
                        <input type="text" name="school_name" class="form-control" value="<?= htmlspecialchars($settings['school_name'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Slogan / Tagline Sekolah</label>
                        <input type="text" name="school_slogan" class="form-control" value="<?= htmlspecialchars($settings['school_slogan'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kepala Sekolah</label>
                        <input type="text" name="principal_name" class="form-control" value="<?= htmlspecialchars($settings['principal_name'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Sambutan Kepala Sekolah</label>
                        <textarea name="principal_welcome" rows="4" class="form-control"><?= htmlspecialchars($settings['principal_welcome'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header">
                    <h6 class="fw-bold mb-0"><i class="bi bi-geo-alt me-2 text-danger"></i>Kontak & Alamat</h6>
                </div>
                <div class="admin-card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat Lengkap</label>
                        <textarea name="school_address" rows="2" class="form-control"><?= htmlspecialchars($settings['school_address'] ?? '') ?></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. Telepon</label>
                            <input type="text" name="school_phone" class="form-control" value="<?= htmlspecialchars($settings['school_phone'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. WhatsApp PPDB</label>
                            <input type="text" name="school_whatsapp" class="form-control" value="<?= htmlspecialchars($settings['school_whatsapp'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Email Resmi</label>
                            <input type="email" name="school_email" class="form-control" value="<?= htmlspecialchars($settings['school_email'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- VISION, MISSION & SOCIAL LINKS (RIGHT) -->
        <div class="col-lg-6">
            <div class="admin-card mb-4">
                <div class="admin-card-header">
                    <h6 class="fw-bold mb-0"><i class="bi bi-bullseye me-2 text-info"></i>Visi & Misi Sekolah</h6>
                </div>
                <div class="admin-card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Visi</label>
                        <textarea name="school_vision" rows="3" class="form-control"><?= htmlspecialchars($settings['school_vision'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Misi (Format Nomor Baris)</label>
                        <textarea name="school_mission" rows="5" class="form-control"><?= htmlspecialchars($settings['school_mission'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <div class="admin-card mb-4">
                <div class="admin-card-header">
                    <h6 class="fw-bold mb-0"><i class="bi bi-share me-2 text-success"></i>Tautan Media Sosial</h6>
                </div>
                <div class="admin-card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Facebook URL</label>
                        <input type="url" name="facebook_url" class="form-control" value="<?= htmlspecialchars($settings['facebook_url'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Instagram URL</label>
                        <input type="url" name="instagram_url" class="form-control" value="<?= htmlspecialchars($settings['instagram_url'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">YouTube URL</label>
                        <input type="url" name="youtube_url" class="form-control" value="<?= htmlspecialchars($settings['youtube_url'] ?? '') ?>">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">LinkedIn URL</label>
                        <input type="url" name="linkedin_url" class="form-control" value="<?= htmlspecialchars($settings['linkedin_url'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-custom-primary py-3 fs-6">
                    <i class="bi bi-save-fill me-2"></i> Simpan Seluruh Pengaturan
                </button>
            </div>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
