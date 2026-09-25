<?php
// admin/article-delete.php
// SMK Bangun Nusa Bangsa - Delete Article Handler

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

requireAdminAuth();

$pdo = getDBConnection();
$articleId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($articleId > 0) {
    // Get article to remove uploaded thumbnail if not default
    $stmt = $pdo->prepare("SELECT thumbnail, title FROM articles WHERE id = ?");
    $stmt->execute([$articleId]);
    $art = $stmt->fetch();

    if ($art) {
        if (!empty($art['thumbnail']) && strpos($art['thumbnail'], 'uploads/') === 0) {
            $filePath = __DIR__ . '/../' . $art['thumbnail'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        // Delete comments associated with this article
        $delComments = $pdo->prepare("DELETE FROM comments WHERE article_id = ?");
        $delComments->execute([$articleId]);

        // Delete article
        $delArt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
        $delArt->execute([$articleId]);

        setFlashMessage('success', 'Artikel "' . htmlspecialchars($art['title']) . '" berhasil dihapus beserta seluruh komentarnya.');
    } else {
        setFlashMessage('warning', 'Artikel tidak ditemukan.');
    }
}

header('Location: ' . SITE_URL . '/admin/articles.php');
exit;
