<?php
// admin/includes/sidebar.php
// SMK Bangun Nusa Bangsa - Admin Sidebar

$pdo = getDBConnection();
$unreadCommentsCount = $pdo->query("SELECT COUNT(*) FROM comments WHERE status = 'pending'")->fetchColumn();
$unreadMessagesCount = $pdo->query("SELECT COUNT(*) FROM messages WHERE is_read = 0")->fetchColumn();
?>
    <!-- ADMIN SIDEBAR -->
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo-icon">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <div>
                <div class="sidebar-title">PANEL ADMIN</div>
                <div class="sidebar-subtitle">SMK BANGUN NUSA BANGSA</div>
            </div>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-heading">Utama</li>
            <li>
                <a href="<?= SITE_URL ?>/admin/index.php" class="sidebar-link <?= $currentAdminPage === 'index' ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="menu-heading">Manajemen Artikel</li>
            <li>
                <a href="<?= SITE_URL ?>/admin/articles.php" class="sidebar-link <?= in_array($currentAdminPage, ['articles', 'article-add', 'article-edit']) ? 'active' : '' ?>">
                    <i class="bi bi-journal-text"></i>
                    <span>Semua Artikel</span>
                </a>
            </li>
            <li>
                <a href="<?= SITE_URL ?>/admin/article-add.php" class="sidebar-link <?= $currentAdminPage === 'article-add' ? 'active' : '' ?>">
                    <i class="bi bi-plus-circle-dotted"></i>
                    <span>Tulis Artikel Baru</span>
                </a>
            </li>
            <li>
                <a href="<?= SITE_URL ?>/admin/categories.php" class="sidebar-link <?= $currentAdminPage === 'categories' ? 'active' : '' ?>">
                    <i class="bi bi-folder-fill"></i>
                    <span>Kategori Artikel</span>
                </a>
            </li>
            <li>
                <a href="<?= SITE_URL ?>/admin/comments.php" class="sidebar-link <?= $currentAdminPage === 'comments' ? 'active' : '' ?>">
                    <i class="bi bi-chat-dots-fill"></i>
                    <span>Moderasi Komentar</span>
                    <?php if ($unreadCommentsCount > 0): ?>
                        <span class="badge bg-danger rounded-pill ms-auto"><?= $unreadCommentsCount ?></span>
                    <?php endif; ?>
                </a>
            </li>

            <li class="menu-heading">Konten & Sekolah</li>
            <li>
                <a href="<?= SITE_URL ?>/admin/majors.php" class="sidebar-link <?= $currentAdminPage === 'majors' ? 'active' : '' ?>">
                    <i class="bi bi-mortarboard-fill"></i>
                    <span>Program Jurusan</span>
                </a>
            </li>
            <li>
                <a href="<?= SITE_URL ?>/admin/messages.php" class="sidebar-link <?= $currentAdminPage === 'messages' ? 'active' : '' ?>">
                    <i class="bi bi-envelope-fill"></i>
                    <span>Pesan Masuk</span>
                    <?php if ($unreadMessagesCount > 0): ?>
                        <span class="badge bg-warning text-dark rounded-pill ms-auto"><?= $unreadMessagesCount ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li>
                <a href="<?= SITE_URL ?>/admin/settings.php" class="sidebar-link <?= $currentAdminPage === 'settings' ? 'active' : '' ?>">
                    <i class="bi bi-gear-fill"></i>
                    <span>Profil & Pengaturan</span>
                </a>
            </li>

            <li class="menu-heading">Tautan Eksternal</li>
            <li>
                <a href="<?= SITE_URL ?>/index.php" target="_blank" class="sidebar-link text-info">
                    <i class="bi bi-box-arrow-up-right"></i>
                    <span>Lihat Website</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <div class="admin-profile-pill">
                <div class="admin-avatar"><?= strtoupper(mb_substr($adminUser['fullname'] ?? 'A', 0, 1)) ?></div>
                <div class="overflow-hidden flex-grow-1">
                    <div class="text-white fw-bold small text-truncate"><?= htmlspecialchars($adminUser['fullname'] ?? 'Admin') ?></div>
                    <div class="text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars($adminUser['role'] ?? 'admin') ?></div>
                </div>
                <a href="<?= SITE_URL ?>/admin/logout.php" class="text-danger fs-5 ms-auto" title="Keluar / Logout" onclick="return confirm('Apakah Anda yakin ingin logout?');">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            </div>
        </div>
    </aside>

    <div class="admin-main">
        <!-- ADMIN TOPBAR -->
        <header class="admin-topbar">
            <button class="btn btn-light d-lg-none" id="toggleSidebar">
                <i class="bi bi-list fs-4"></i>
            </button>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small">Selamat Datang, <strong><?= htmlspecialchars($adminUser['fullname'] ?? 'Administrator') ?></strong></span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="<?= SITE_URL ?>/index.php" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill">
                    <i class="bi bi-globe me-1"></i> Lihat Frontend
                </a>
                <a href="<?= SITE_URL ?>/admin/logout.php" class="btn btn-sm btn-outline-danger rounded-pill" onclick="return confirm('Apakah Anda yakin ingin keluar?');">
                    <i class="bi bi-power me-1"></i> Logout
                </a>
            </div>
        </header>

        <div class="admin-content">
            <?php 
            $flash = getFlashMessage();
            if ($flash): 
            ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show mb-4 shadow-sm" role="alert">
                <i class="bi <?= $flash['type'] === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?> me-2"></i>
                <?= htmlspecialchars($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
