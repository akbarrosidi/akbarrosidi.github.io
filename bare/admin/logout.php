<?php
// admin/logout.php
// SMK Bangun Nusa Bangsa - Admin Logout

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

unset($_SESSION['admin_logged_in']);
unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);
unset($_SESSION['admin_fullname']);
unset($_SESSION['admin_role']);

session_destroy();

header('Location: ' . SITE_URL . '/admin/login.php');
exit;
