<?php
// config/functions.php
// SMK Bangun Nusa Bangsa - Core Helper Functions

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/database.php';

/**
 * Get all site settings as associative array
 */
function getSiteSettings() {
    static $settings = null;
    if ($settings !== null) {
        return $settings;
    }

    $pdo = getDBConnection();
    try {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
        $results = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        $settings = $results;
        return $settings;
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Sanitize user input
 */
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate SEO slug from title
 */
function slugify($text) {
    // Replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    // Transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // Remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
    // Trim
    $text = trim($text, '-');
    // Remove duplicate -
    $text = preg_replace('~-+~', '-', $text);
    // Lowercase
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a-' . time();
    }
    return $text;
}

/**
 * Format datetime to Indonesian format
 */
function formatDateIndo($datetime, $withTime = false) {
    if (!$datetime) return '-';
    
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $timestamp = strtotime($datetime);
    $tgl = date('d', $timestamp);
    $bln = $bulan[(int)date('m', $timestamp)];
    $thn = date('Y', $timestamp);
    $waktu = date('H:i', $timestamp);

    if ($withTime) {
        return "$tgl $bln $thn, $waktu WIB";
    }
    return "$tgl $bln $thn";
}

/**
 * Time Ago in Indonesian
 */
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $difference = time() - $timestamp;

    if ($difference < 60) {
        return 'Baru saja';
    } elseif ($difference < 3600) {
        $mins = round($difference / 60);
        return "$mins menit yang lalu";
    } elseif ($difference < 86400) {
        $hours = round($difference / 3600);
        return "$hours jam yang lalu";
    } elseif ($difference < 604800) {
        $days = round($difference / 86400);
        return "$days hari yang lalu";
    } else {
        return formatDateIndo($datetime);
    }
}

/**
 * Auth helpers
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireAdminAuth() {
    if (!isAdminLoggedIn()) {
        header('Location: ' . SITE_URL . '/admin/login.php');
        exit;
    }
}

function getLoggedInAdmin() {
    if (!isAdminLoggedIn()) return null;
    return [
        'id' => $_SESSION['admin_id'] ?? null,
        'username' => $_SESSION['admin_username'] ?? '',
        'fullname' => $_SESSION['admin_fullname'] ?? 'Administrator',
        'role' => $_SESSION['admin_role'] ?? 'admin',
        'avatar' => $_SESSION['admin_avatar'] ?? 'assets/images/admin-avatar.png'
    ];
}

/**
 * Flash messages
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type, // 'success', 'danger', 'warning', 'info'
        'message' => $message
    ];
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

/**
 * Increment article views counter
 */
function incrementArticleViews($articleId) {
    $sessionKey = 'viewed_article_' . $articleId;
    if (!isset($_SESSION[$sessionKey])) {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("UPDATE articles SET views = views + 1 WHERE id = ?");
        $stmt->execute([$articleId]);
        $_SESSION[$sessionKey] = true;
    }
}

/**
 * Upload thumbnail image
 */
function uploadArticleThumbnail($file, $targetDir = '../uploads/') {
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['status' => false, 'message' => 'Parameter berkas tidak valid.'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['status' => false, 'message' => 'Terjadi kesalahan saat mengunggah berkas.'];
    }

    if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
        return ['status' => false, 'message' => 'Ukuran berkas melebihi batas 5MB.'];
    }

    $allowedMimes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    if (!isset($allowedMimes[$mime])) {
        return ['status' => false, 'message' => 'Format berkas harus berupa JPG, PNG, atau WEBP.'];
    }

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $ext = $allowedMimes[$mime];
    $filename = sprintf('artikel_%s_%s.%s', date('Ymd_His'), bin2hex(random_bytes(4)), $ext);
    $destination = rtrim($targetDir, '/') . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['status' => false, 'message' => 'Gagal memindahkan berkas yang diunggah.'];
    }

    // Return path relative to web root
    $publicPath = 'uploads/' . $filename;
    return ['status' => true, 'filepath' => $publicPath];
}
