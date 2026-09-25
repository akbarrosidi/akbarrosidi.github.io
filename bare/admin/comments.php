<?php
// admin/comments.php
// SMK Bangun Nusa Bangsa - Comments Moderation Management

$adminTitle = 'Moderasi Komentar';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$pdo = getDBConnection();

// Action Handlers (Approve / Spam / Delete)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $commentId = (int)$_GET['id'];

    if ($action === 'approve') {
        $stmt = $pdo->prepare("UPDATE comments SET status = 'approved' WHERE id = ?");
        $stmt->execute([$commentId]);
        setFlashMessage('success', 'Komentar berhasil disetujui dan kini tayang di web.');
    } elseif ($action === 'spam') {
        $stmt = $pdo->prepare("UPDATE comments SET status = 'spam' WHERE id = ?");
        $stmt->execute([$commentId]);
        setFlashMessage('warning', 'Komentar ditandai sebagai spam.');
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
        $stmt->execute([$commentId]);
        setFlashMessage('success', 'Komentar berhasil dihapus permanen.');
    }
    echo "<script>window.location.href = '" . SITE_URL . "/admin/comments.php';</script>";
    exit;
}

$statusFilter = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$typeFilter = isset($_GET['type']) ? sanitize($_GET['type']) : ''; // anon vs verified

$where = ["1=1"];
$params = [];

if (!empty($statusFilter)) {
    $where[] = "c.status = :status";
    $params[':status'] = $statusFilter;
}

if ($typeFilter === 'anonymous') {
    $where[] = "c.is_anonymous = 1";
} elseif ($typeFilter === 'verified') {
    $where[] = "c.is_anonymous = 0";
}

$whereSql = implode(' AND ', $where);

$sql = "
    SELECT c.*, a.title AS article_title, a.slug AS article_slug
    FROM comments c
    JOIN articles a ON c.article_id = a.id
    WHERE $whereSql
    ORDER BY c.created_at DESC
";
$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->execute();
$comments = $stmt->fetchAll();

// Counts for filters
$countAll = $pdo->query("SELECT COUNT(*) FROM comments")->fetchColumn();
$countApproved = $pdo->query("SELECT COUNT(*) FROM comments WHERE status = 'approved'")->fetchColumn();
$countPending = $pdo->query("SELECT COUNT(*) FROM comments WHERE status = 'pending'")->fetchColumn();
$countSpam = $pdo->query("SELECT COUNT(*) FROM comments WHERE status = 'spam'")->fetchColumn();
?>

<!-- PAGE HEADER -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Moderasi Komentar Pengunjung</h2>
        <p class="text-muted mb-0">Kelola komentar artikel dari pengunjung baik komentar anonim maupun dengan identitas email.</p>
    </div>
</div>

<!-- FILTER PILLS -->
<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="<?= SITE_URL ?>/admin/comments.php" class="btn btn-sm rounded-pill <?= empty($statusFilter) && empty($typeFilter) ? 'btn-primary' : 'btn-light border' ?> px-3">
        Semua (<?= $countAll ?>)
    </a>
    <a href="<?= SITE_URL ?>/admin/comments.php?status=approved" class="btn btn-sm rounded-pill <?= $statusFilter === 'approved' ? 'btn-success' : 'btn-light border text-success' ?> px-3">
        Disetujui (<?= $countApproved ?>)
    </a>
    <a href="<?= SITE_URL ?>/admin/comments.php?status=pending" class="btn btn-sm rounded-pill <?= $statusFilter === 'pending' ? 'btn-warning text-dark' : 'btn-light border text-warning' ?> px-3">
        Menunggu (<?= $countPending ?>)
    </a>
    <a href="<?= SITE_URL ?>/admin/comments.php?status=spam" class="btn btn-sm rounded-pill <?= $statusFilter === 'spam' ? 'btn-danger' : 'btn-light border text-danger' ?> px-3">
        Spam (<?= $countSpam ?>)
    </a>
    <span class="vr mx-2"></span>
    <a href="<?= SITE_URL ?>/admin/comments.php?type=anonymous" class="btn btn-sm rounded-pill <?= $typeFilter === 'anonymous' ? 'btn-dark' : 'btn-light border' ?> px-3">
        <i class="bi bi-incognito me-1"></i> Hanya Anonim
    </a>
    <a href="<?= SITE_URL ?>/admin/comments.php?type=verified" class="btn btn-sm rounded-pill <?= $typeFilter === 'verified' ? 'btn-info text-white' : 'btn-light border' ?> px-3">
        <i class="bi bi-check-circle me-1"></i> Nama & Email
    </a>
</div>

<!-- COMMENTS TABLE CARD -->
<div class="admin-card">
    <div class="admin-card-header">
        <h5 class="fw-bold mb-0">Daftar Komentar (<?= count($comments) ?>)</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>Pengirim & Tipe</th>
                    <th>Isi Komentar</th>
                    <th>Artikel Terkait</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th class="text-end">Aksi Moderasi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($comments)): ?>
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-chat-square-dots fs-1 d-block mb-2 text-secondary"></i>
                        Tidak ada komentar pada filter ini.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($comments as $com): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <strong><?= htmlspecialchars($com['name']) ?></strong>
                            </div>
                            <?php if ($com['is_anonymous']): ?>
                                <span class="comment-badge-anonymous"><i class="bi bi-incognito me-1"></i>Anonim</span>
                            <?php else: ?>
                                <span class="comment-badge-verified"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($com['email'] ?: 'Terverifikasi') ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="max-width: 320px;">
                            <div class="text-dark small" style="line-height: 1.5;">
                                <?= nl2br(htmlspecialchars($com['comment'])) ?>
                            </div>
                        </td>
                        <td>
                            <a href="<?= SITE_URL ?>/artikel-detail.php?slug=<?= urlencode($com['article_slug']) ?>" target="_blank" class="small text-decoration-none text-info fw-semibold">
                                <?= htmlspecialchars(mb_substr($com['article_title'], 0, 35)) ?>... <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                        </td>
                        <td>
                            <small class="text-muted"><?= formatDateIndo($com['created_at'], true) ?></small>
                        </td>
                        <td>
                            <?php if ($com['status'] === 'approved'): ?>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-1 rounded-pill">Approved</span>
                            <?php elseif ($com['status'] === 'pending'): ?>
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3 py-1 rounded-pill">Pending</span>
                            <?php else: ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-1 rounded-pill">Spam</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <?php if ($com['status'] !== 'approved'): ?>
                                <a href="<?= SITE_URL ?>/admin/comments.php?action=approve&id=<?= $com['id'] ?>" class="btn btn-sm btn-light border text-success" title="Setujui (Approve)">
                                    <i class="bi bi-check-lg"></i>
                                </a>
                                <?php endif; ?>

                                <?php if ($com['status'] !== 'spam'): ?>
                                <a href="<?= SITE_URL ?>/admin/comments.php?action=spam&id=<?= $com['id'] ?>" class="btn btn-sm btn-light border text-warning" title="Tandai Spam">
                                    <i class="bi bi-exclamation-octagon"></i>
                                </a>
                                <?php endif; ?>

                                <a href="<?= SITE_URL ?>/admin/comments.php?action=delete&id=<?= $com['id'] ?>" class="btn btn-sm btn-light border text-danger btn-confirm-delete" data-name="komentar dari <?= htmlspecialchars($com['name']) ?>" title="Hapus Permanen">
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
