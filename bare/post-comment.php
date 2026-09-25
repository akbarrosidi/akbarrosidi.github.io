<?php
// post-comment.php
// SMK Bangun Nusa Bangsa - API / Handler for Submitting Comments

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Metode permintaan tidak valid.']);
    exit;
}

$articleId = isset($_POST['article_id']) ? (int)$_POST['article_id'] : 0;
$isAnonymous = isset($_POST['is_anonymous']) && $_POST['is_anonymous'] == '1' ? 1 : 0;
$commentText = isset($_POST['comment']) ? sanitize($_POST['comment']) : '';
$captchaAnswer = isset($_POST['captcha_answer']) ? (int)$_POST['captcha_answer'] : 0;
$captchaExpected = isset($_POST['captcha_expected']) ? (int)$_POST['captcha_expected'] : -999;

// Validate Math Captcha
if ($captchaAnswer !== $captchaExpected) {
    echo json_encode(['status' => 'error', 'message' => 'Jawaban verifikasi matematika salah. Silakan coba lagi.']);
    exit;
}

// Validate Article ID
if ($articleId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Artikel tidak ditemukan.']);
    exit;
}

$pdo = getDBConnection();
$artCheck = $pdo->prepare("SELECT id FROM articles WHERE id = ? AND status = 'published'");
$artCheck->execute([$articleId]);
if (!$artCheck->fetch()) {
    echo json_encode(['status' => 'error', 'message' => 'Artikel tidak aktif atau tidak ditemukan.']);
    exit;
}

// Validate Comment content
if (empty($commentText) || mb_strlen($commentText) < 3) {
    echo json_encode(['status' => 'error', 'message' => 'Komentar tidak boleh kosong (minimal 3 karakter).']);
    exit;
}

if (mb_strlen($commentText) > 2000) {
    echo json_encode(['status' => 'error', 'message' => 'Komentar terlalu panjang (maksimal 2000 karakter).']);
    exit;
}

// Handle Name & Email based on Anonymous toggle
$name = 'Anonim';
$email = null;

if ($isAnonymous) {
    $name = 'Anonim';
    $email = null;
} else {
    $rawName = isset($_POST['name']) ? sanitize($_POST['name']) : '';
    $rawEmail = isset($_POST['email']) ? sanitize($_POST['email']) : '';

    if (empty($rawName)) {
        echo json_encode(['status' => 'error', 'message' => 'Nama wajib diisi jika tidak memilih opsi anonim.']);
        exit;
    }
    if (empty($rawEmail) || !filter_var($rawEmail, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Format email tidak valid.']);
        exit;
    }

    $name = $rawName;
    $email = $rawEmail;
}

try {
    $stmt = $pdo->prepare("INSERT INTO comments (article_id, name, email, is_anonymous, comment, status) VALUES (?, ?, ?, ?, ?, 'approved')");
    $stmt->execute([$articleId, $name, $email, $isAnonymous, $commentText]);

    $commentId = $pdo->lastInsertId();
    $avatarLetter = $isAnonymous ? '?' : strtoupper(mb_substr($name, 0, 1));
    $avatarClass = $isAnonymous ? 'comment-avatar anonymous' : 'comment-avatar';
    $badgeHtml = $isAnonymous 
        ? '<span class="badge-anonymous"><i class="bi bi-incognito me-1"></i>Anonim</span>' 
        : '<span class="badge-verified"><i class="bi bi-shield-check me-1"></i>Terverifikasi</span>';

    $commentHtml = '
    <div class="comment-item" id="comment-' . $commentId . '">
        <div class="comment-header">
            <div class="comment-user-info">
                <div class="' . $avatarClass . '">' . htmlspecialchars($avatarLetter) . '</div>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <strong class="text-dark">' . htmlspecialchars($name) . '</strong>
                        ' . $badgeHtml . '
                    </div>
                    <small class="text-muted"><i class="bi bi-clock me-1"></i>Baru saja</small>
                </div>
            </div>
        </div>
        <p class="mb-0 text-secondary" style="font-size: 0.95rem; line-height: 1.6;">
            ' . nl2br(htmlspecialchars($commentText)) . '
        </p>
    </div>';

    echo json_encode([
        'status' => 'success',
        'message' => 'Terima kasih! Komentar Anda berhasil dikirim dan ditampilkan.',
        'comment_html' => $commentHtml
    ]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan komentar: ' . $e->getMessage()]);
}
