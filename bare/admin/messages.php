<?php
// admin/messages.php
// SMK Bangun Nusa Bangsa - Contact & PPDB Messages Inbox

$adminTitle = 'Pesan Masuk';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$pdo = getDBConnection();

// Mark as read or delete
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $msgId = (int)$_GET['id'];

    if ($action === 'read') {
        $stmt = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
        $stmt->execute([$msgId]);
        setFlashMessage('success', 'Pesan ditandai sebagai sudah dibaca.');
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
        $stmt->execute([$msgId]);
        setFlashMessage('success', 'Pesan berhasil dihapus.');
    }
    echo "<script>window.location.href = '" . SITE_URL . "/admin/messages.php';</script>";
    exit;
}

$messages = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll();
?>

<!-- PAGE HEADER -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Pesan Masuk & Pendaftar PPDB</h2>
        <p class="text-muted mb-0">Pertanyaan dan formulir kontak yang dikirimkan pengunjung website.</p>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-header">
        <h5 class="fw-bold mb-0">Kotak Masuk (<?= count($messages) ?>)</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>Pengirim & Email</th>
                    <th>Subjek / Topik</th>
                    <th>Isi Pesan</th>
                    <th>Waktu</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($messages)): ?>
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                        Belum ada pesan masuk dari pengunjung.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($messages as $msg): ?>
                    <tr class="<?= $msg['is_read'] ? '' : 'table-light fw-semibold' ?>">
                        <td>
                            <strong class="text-dark"><?= htmlspecialchars($msg['name']) ?></strong>
                            <div class="small text-muted"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($msg['email']) ?></div>
                        </td>
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary border"><?= htmlspecialchars($msg['subject']) ?></span>
                        </td>
                        <td style="max-width: 350px;">
                            <div class="small text-dark" style="line-height: 1.5;">
                                <?= nl2br(htmlspecialchars($msg['message'])) ?>
                            </div>
                        </td>
                        <td>
                            <small class="text-muted"><?= formatDateIndo($msg['created_at'], true) ?></small>
                        </td>
                        <td>
                            <?php if ($msg['is_read']): ?>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-3 py-1 rounded-pill">Dibaca</span>
                            <?php else: ?>
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3 py-1 rounded-pill">Baru</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <?php if (!$msg['is_read']): ?>
                                <a href="<?= SITE_URL ?>/admin/messages.php?action=read&id=<?= $msg['id'] ?>" class="btn btn-sm btn-light border text-primary" title="Tandai Sudah Dibaca">
                                    <i class="bi bi-envelope-open-fill"></i>
                                </a>
                                <?php endif; ?>
                                <a href="mailto:<?= htmlspecialchars($msg['email']) ?>?subject=Re:%20<?= urlencode($msg['subject']) ?>" class="btn btn-sm btn-light border text-success" title="Balas Email">
                                    <i class="bi bi-reply-fill"></i>
                                </a>
                                <a href="<?= SITE_URL ?>/admin/messages.php?action=delete&id=<?= $msg['id'] ?>" class="btn btn-sm btn-light border text-danger btn-confirm-delete" data-name="pesan dari <?= htmlspecialchars($msg['name']) ?>" title="Hapus">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
