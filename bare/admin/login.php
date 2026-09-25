<?php
// admin/login.php
// SMK Bangun Nusa Bangsa - Admin Login

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

if (isAdminLoggedIn()) {
    header('Location: ' . SITE_URL . '/admin/index.php');
    exit;
}

$error = '';
$settings = getSiteSettings();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Silakan masukkan username dan password Anda.';
    } else {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_fullname'] = $user['fullname'];
            $_SESSION['admin_role'] = $user['role'];
            $_SESSION['admin_avatar'] = $user['avatar'];

            header('Location: ' . SITE_URL . '/admin/index.php');
            exit;
        } else {
            $error = 'Username atau password yang Anda masukkan salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator | SMK Bangun Nusa Bangsa</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= SITE_URL ?>/assets/css/admin.css" rel="stylesheet">
</head>
<body class="login-body">

    <div class="login-card">
        <div class="text-center mb-4">
            <div class="brand-icon-box mx-auto mb-3" style="width: 58px; height: 58px; font-size: 1.8rem;">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <h3 class="fw-bold mb-1 text-dark">Portal Administrator</h3>
            <p class="text-muted small">SMK BANGUN NUSA BANGSA</p>
        </div>

        <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show small d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div><?= htmlspecialchars($error) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <form action="<?= SITE_URL ?>/admin/login.php" method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold text-dark">Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-person text-muted"></i></span>
                    <input type="text" name="username" class="form-control py-2" placeholder="admin" value="admin" required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold text-dark">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-key text-muted"></i></span>
                    <input type="password" name="password" class="form-control py-2" placeholder="admin123" value="admin123" required>
                </div>
                <div class="form-text text-muted mt-1 small">Default login: <strong>admin</strong> / <strong>admin123</strong></div>
            </div>

            <button type="submit" class="btn btn-custom-primary w-100 py-2 fs-6 mb-3">
                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk ke Dashboard
            </button>

            <div class="text-center">
                <a href="<?= SITE_URL ?>/index.php" class="text-decoration-none text-muted small">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Website Utama
                </a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
