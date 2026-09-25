<?php
// admin/includes/header.php
// SMK Bangun Nusa Bangsa - Admin Header

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/functions.php';

requireAdminAuth();
$adminUser = getLoggedInAdmin();
$settings = getSiteSettings();
$currentAdminPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($adminTitle) ? htmlspecialchars($adminTitle) . ' | ' : '' ?>Dashboard Admin - <?= htmlspecialchars($settings['school_name'] ?? 'SMK Bangun Nusa Bangsa') ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Summernote WYSIWYG CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <!-- Admin CSS -->
    <link href="<?= SITE_URL ?>/assets/css/admin.css" rel="stylesheet">
</head>
<body>
<div class="admin-wrapper">
