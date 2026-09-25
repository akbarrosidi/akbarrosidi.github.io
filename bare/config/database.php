<?php
// config/database.php
// SMK Bangun Nusa Bangsa - Database Connection & Auto Initialization

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'smk_bangun_nusa_bangsa');
define('SITE_URL', 'http://localhost/bare');

function getDBConnection() {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    try {
        // Connect to MySQL server first to ensure database exists
        $tempPdo = new PDO("mysql:host=" . DB_HOST . ";charset=utf8mb4", DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        
        $tempPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        // Connect to the specific database
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        // Auto initialize and migrate tables
        migrateAndInitializeTables($pdo);

        return $pdo;
    } catch (PDOException $e) {
        die("<div style='font-family:sans-serif;padding:30px;background:#fff3f3;color:#c00;border:1px solid #f99;margin:40px;border-radius:8px;'>
            <h3>⚠️ Gagal Terhubung ke Database MySQL</h3>
            <p>Pastikan layanan <strong>MySQL</strong> di XAMPP Control Panel sudah berjalan (Running).</p>
            <p><small>Error detail: " . htmlspecialchars($e->getMessage()) . "</small></p>
        </div>");
    }
}

function migrateAndInitializeTables(PDO $pdo) {
    // 1. Users Table (Admin)
    $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(50) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `fullname` VARCHAR(100) NOT NULL,
        `email` VARCHAR(100) NOT NULL,
        `role` ENUM('admin', 'editor') DEFAULT 'admin',
        `avatar` VARCHAR(255) DEFAULT 'assets/images/admin-avatar.png',
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 2. Categories Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `categories` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(100) NOT NULL,
        `slug` VARCHAR(100) NOT NULL UNIQUE,
        `description` TEXT NULL,
        `icon` VARCHAR(50) DEFAULT 'bi-bookmark',
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 3. Articles Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `articles` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `slug` VARCHAR(255) NOT NULL UNIQUE,
        `category_id` INT NOT NULL,
        `author_id` INT NOT NULL,
        `thumbnail` VARCHAR(255) NULL,
        `excerpt` TEXT NULL,
        `content` LONGTEXT NOT NULL,
        `views` INT DEFAULT 0,
        `status` ENUM('published', 'draft') DEFAULT 'published',
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX (`category_id`),
        INDEX (`slug`),
        INDEX (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 4. Comments Table (Supports Anonymous toggle OR mandatory name & email)
    $pdo->exec("CREATE TABLE IF NOT EXISTS `comments` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `article_id` INT NOT NULL,
        `name` VARCHAR(100) NOT NULL,
        `email` VARCHAR(100) NULL,
        `is_anonymous` TINYINT(1) DEFAULT 0,
        `comment` TEXT NOT NULL,
        `status` ENUM('approved', 'pending', 'spam') DEFAULT 'approved',
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX (`article_id`),
        INDEX (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 5. Majors (Jurusan) Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `majors` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `code` VARCHAR(20) NOT NULL UNIQUE,
        `name` VARCHAR(100) NOT NULL,
        `slug` VARCHAR(100) NOT NULL UNIQUE,
        `tagline` VARCHAR(255) NULL,
        `description` TEXT NOT NULL,
        `competencies` TEXT NULL,
        `careers` TEXT NULL,
        `image` VARCHAR(255) NULL,
        `badge_color` VARCHAR(30) DEFAULT 'primary',
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 6. Site Settings Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `settings` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `setting_key` VARCHAR(50) NOT NULL UNIQUE,
        `setting_value` TEXT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 7. Contact Messages Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `messages` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(100) NOT NULL,
        `email` VARCHAR(100) NOT NULL,
        `subject` VARCHAR(150) NOT NULL,
        `message` TEXT NOT NULL,
        `is_read` TINYINT(1) DEFAULT 0,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Check if majors count != 3 or has old majors
    $majorsCount = $pdo->query("SELECT COUNT(*) FROM `majors`")->fetchColumn();
    $hasAkl = $pdo->query("SELECT COUNT(*) FROM `majors` WHERE code = 'AKL'")->fetchColumn();
    if ($majorsCount != 3 || !$hasAkl) {
        seedMajorsData($pdo);
    }

    // Seed default data if articles table is empty
    $artCount = $pdo->query("SELECT COUNT(*) FROM `articles`")->fetchColumn();
    if ($artCount == 0) {
        seedInitialData($pdo);
    }
}

function seedMajorsData(PDO $pdo) {
    $pdo->exec("DELETE FROM `majors`");
    
    $majors = [
        [
            'code' => 'AKL',
            'name' => 'Akuntansi & Keuangan Lembaga (AKL)',
            'slug' => 'akuntansi-dan-keuangan-lembaga',
            'tagline' => 'Menguasai Akuntansi Digital, Sistem Keuangan Modern, Perpajakan & Fintech 4.0',
            'description' => 'Program Keahlian Akuntansi dan Keuangan Lembaga membekali siswa dengan penguasaan pembukuan keuangan modern berbasis software (Accurate, MYOB, Spreadsheet), administrasi perpajakan digital (e-Tax), transaksi perbankan, serta analisis data keuangan perusahaan.',
            'competencies' => 'Komputer Akuntansi (Accurate & MYOB), Administrasi Perpajakan Digital & e-Faktur, Pengelolaan Kas & Rekonsiliasi Bank, Spreadsheet Finansial Lanjut, Akuntansi Syariah & Fintech.',
            'careers' => 'Digital Accountant, Staf Keuangan / Finance, Petugas Pajak Perusahaan, Customer Service & Teller Bank, Analis Anggaran Junior, Konsultan Keuangan Mandiri.',
            'image' => 'assets/images/jurusan-akuntansi.jpg',
            'badge_color' => 'success'
        ],
        [
            'code' => 'TKJ',
            'name' => 'Teknik Komputer & Jaringan (TKJ)',
            'slug' => 'teknik-komputer-dan-jaringan',
            'tagline' => 'Menguasai Arsitektur Jaringan, Infrastruktur Cloud, & Keamanan Siber',
            'description' => 'Jurusan TKJ berfokus pada perancangan infrastruktur jaringan skala enterprise, routing switching MikroTik/Cisco bersertifikasi internasional, implementasi Server Linux/Windows, instalasi Fiber Optic, serta pertahanan Cyber Security.',
            'competencies' => 'MikroTik Certified Network Associate (MTCNA), Cisco CCNA Routing & Switching, Cloud Server Architecture (AWS/GCP), Ethical Hacking & Cyber Defense, Fiber Optic Splicing.',
            'careers' => 'Network Administrator, Cyber Security Analyst, Cloud Engineer, System Administrator, IT Support & Infrastructure Specialist.',
            'image' => 'assets/images/jurusan-tkj.jpg',
            'badge_color' => 'info'
        ],
        [
            'code' => 'TKR',
            'name' => 'Teknik Kendaraan Ringan (TKR)',
            'slug' => 'teknik-kendaraan-ringan',
            'tagline' => 'Inovasi Otomotif Modern, Engine Management System & Kendaraan Listrik (EV)',
            'description' => 'Jurusan TKR membekali siswa dengan keahlian pemeliharaan dan perbaikan mesin otomotif modern, Electronic Fuel Injection (EFI), scanner diagnostik komputer, sistem transmisi otomatis, kelistrikan body, chassis, serta teknologi kendaraan listrik (Electric Vehicle).',
            'competencies' => 'Engine Management System & Scanner Diagnostik, Electronic Fuel Injection (EFI), Transmisi Otomatis & CVT, Sistem Rem ABS & Airbag, Teknologi Baterai & Perawatan Kendaraan Listrik (EV).',
            'careers' => 'Automotive Service Technician, Teknisi Spesialis Kendaraan Listrik (EV), Service Advisor Diler Resmi, Quality Control Otomotif, Wirausaha Bengkel Modern.',
            'image' => 'assets/images/jurusan-tkr.jpg',
            'badge_color' => 'warning'
        ]
    ];

    $stmtMajor = $pdo->prepare("INSERT INTO `majors` (`code`, `name`, `slug`, `tagline`, `description`, `competencies`, `careers`, `image`, `badge_color`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($majors as $m) {
        $stmtMajor->execute([$m['code'], $m['name'], $m['slug'], $m['tagline'], $m['description'], $m['competencies'], $m['careers'], $m['image'], $m['badge_color']]);
    }
}

function seedInitialData(PDO $pdo) {
    // 1. Seed Default Admin (password: admin123)
    $hashedPassword = password_hash('admin123', PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO `users` (`username`, `password`, `fullname`, `email`, `role`) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(['admin', $hashedPassword, 'Administrator Utama', 'admin@smk-bangunnusabangsa.sch.id', 'admin']);
    $adminId = $pdo->lastInsertId();

    // 2. Seed Settings
    $settings = [
        'school_name' => 'SMK Bangun Nusa Bangsa',
        'school_slogan' => 'Mencetak Generasi Unggul, Berkarakter, & Berdaya Saing Global',
        'school_vision' => 'Menjadi Sekolah Menengah Kejuruan Pusat Keunggulan (Center of Excellence) yang menghasilkan lulusan berakhlak mulia, kompeten berstandar internasional, serta siap berwirausaha di era revolusi industri 4.0 dan Society 5.0.',
        'school_mission' => "1. Menyelenggarakan pendidikan kejuruan berbasis kompetensi industri global.\n2. Mengembangkan karakter disiplin, religius, inovatif, dan berjiwa wirausaha.\n3. Memperluas jejaring kemitraan strategis dengan DUDI (Dunia Usaha & Dunia Industri) skala nasional & internasional.\n4. Menerapkan teknologi digital termutakhir dalam seluruh proses pembelajaran dan tata kelola sekolah.",
        'school_address' => 'Jl. Pendidikan Karakter Bangsa No. 88, Kawasan Pendidikan Terpadu, Jakarta',
        'school_phone' => '(021) 8899-7722',
        'school_email' => 'info@smk-bangunnusabangsa.sch.id',
        'school_whatsapp' => '081234567890',
        'principal_name' => 'Drs. H. Hendra Wijaya, M.Kom.',
        'principal_welcome' => 'Selamat datang di portal resmi SMK Bangun Nusa Bangsa. Kami berkomitmen memberikan kurikulum terbaik yang terintegrasi langsung dengan kebutuhan dunia industri pada Program Keahlian Akuntansi (AKL), Teknik Komputer Jaringan (TKJ), dan Teknik Kendaraan Ringan (TKR).',
        'facebook_url' => 'https://facebook.com',
        'instagram_url' => 'https://instagram.com',
        'youtube_url' => 'https://youtube.com',
        'linkedin_url' => 'https://linkedin.com'
    ];
    $stmtSetting = $pdo->prepare("INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `setting_value` = ?");
    foreach ($settings as $key => $val) {
        $stmtSetting->execute([$key, $val, $val]);
    }

    // 3. Seed Categories
    $categories = [
        ['name' => 'Berita & Kegiatan', 'slug' => 'berita-kegiatan', 'description' => 'Seputar kegiatan dan kabar terbaru di SMK Bangun Nusa Bangsa', 'icon' => 'bi-newspaper'],
        ['name' => 'Prestasi Siswa', 'slug' => 'prestasi-siswa', 'description' => 'Deretan capaian dan kejuaraan siswa tingkat nasional dan internasional', 'icon' => 'bi-trophy'],
        ['name' => 'Info PPDB & Akademik', 'slug' => 'info-ppdb-akademik', 'description' => 'Informasi pendaftaran peserta didik baru dan kalender pendidikan', 'icon' => 'bi-mortarboard'],
        ['name' => 'Kerjasama Industri', 'slug' => 'kerjasama-industri', 'description' => 'Program link and match, magang, dan penyerapan kerja lulusan', 'icon' => 'bi-briefcase'],
        ['name' => 'Artikel & Edukasi', 'slug' => 'artikel-edukasi', 'description' => 'Tulisan edukatif, tips teknologi, dan karya inovasi guru serta siswa', 'icon' => 'bi-lightbulb']
    ];
    $stmtCat = $pdo->prepare("INSERT INTO `categories` (`name`, `slug`, `description`, `icon`) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE `name` = VALUES(`name`)");
    $catIds = [];
    foreach ($categories as $cat) {
        $stmtCat->execute([$cat['name'], $cat['slug'], $cat['description'], $cat['icon']]);
        $catIds[$cat['slug']] = $pdo->lastInsertId();
    }

    // 4. Seed Majors (Akuntansi, TKJ, TKR)
    seedMajorsData($pdo);

    // 5. Seed Articles
    $articles = [
        [
            'title' => 'Siswa SMK Bangun Nusa Bangsa Raih Juara 1 Nasional Teknologi Otomotif & Inovasi 2026',
            'slug' => 'siswa-smk-bangun-nusa-bangsa-raih-juara-1-nasional-otomotif-2026',
            'category_id' => $catIds['prestasi-siswa'] ?? 2,
            'thumbnail' => 'assets/images/artikel-prestasi.jpg',
            'excerpt' => 'Prestasi membanggakan kembali ditorehkan oleh siswa SMK Bangun Nusa Bangsa dengan menyabet Juara 1 Nasional dalam ajang Kompetisi Inovasi Teknologi Nasional.',
            'content' => '<p class="lead">Prestasi gemilang dan membanggakan kembali diukir oleh putra-putri terbaik <strong>SMK Bangun Nusa Bangsa</strong>. Dalam perhelatan bergengsi tingkat nasional, tim perwakilan sekolah berhasil menyabet predikat <strong>Juara 1 Nasional</strong>.</p>
            <p>Inovasi yang diusung oleh tim adalah sistem diagnostik otomotif cerdas dan sistem keamanan jaringan terpadu yang memadukan keahlian teknik kendaraan ringan dan jaringan komputer. Inovasi ini mendapat apresiasi tinggi dari para juri praktisi industri internasional.</p>
            <div class="alert alert-primary my-4">
                <strong>Pesan Kepala Sekolah:</strong> "Kemenangan ini membuktikan bahwa kualitas kurikulum di 3 jurusan unggulan kami—Akuntansi (AKL), TKJ, dan TKR—berada di level teratas dan siap bersaing di pasar kerja global."
            </div>
            <p>Atas pencapaian ini, seluruh anggota tim mendapatkan beasiswa pendidikan penuh dan kesempatan rekrutmen kerja langsung dari mitra industri terkemuka.</p>',
            'views' => 1420
        ],
        [
            'title' => 'Pelatihan Software Akuntansi Digital & Accurate Cloud untuk Siswa Jurusan AKL',
            'slug' => 'pelatihan-software-akuntansi-digital-accurate-cloud-siswa-akl',
            'category_id' => $catIds['artikel-edukasi'] ?? 5,
            'thumbnail' => 'assets/images/jurusan-akuntansi.jpg',
            'excerpt' => 'Jurusan Akuntansi dan Keuangan Lembaga (AKL) mengadakan workshop sertifikasi keahlian Accurate Accounting Software dan e-Faktur Pajak.',
            'content' => '<p class="lead">Guna memastikan seluruh siswa Jurusan Akuntansi dan Keuangan Lembaga (AKL) memiliki sertifikasi kompetensi industri yang diakui secara nasional, SMK Bangun Nusa Bangsa menggelar <strong>Workshop & Uji Kompetensi Accurate Accounting Online</strong>.</p>
            <p>Pelatihan ini mencakup siklus akuntansi perusahaan dagang dan jasa, pencatatan transaksi terkomputerisasi, penyusunan laporan laba rugi instan, serta integrasi pelaporan pajak online. Dengan bekal keahlian ini, lulusan AKL siap langsung bekerja di kantor akuntan, perbankan, maupun divisi keuangan korporasi.</p>',
            'views' => 880
        ],
        [
            'title' => 'Peresmian Bengkel Modern TKR & Simulator Kendaraan Listrik (EV)',
            'slug' => 'peresmian-bengkel-modern-tkr-simulator-kendaraan-listrik-ev',
            'category_id' => $catIds['berita-kegiatan'] ?? 1,
            'thumbnail' => 'assets/images/jurusan-tkr.jpg',
            'excerpt' => 'Fasilitas baru bengkel diagnostik TKR dan trainer konversi kendaraan listrik resmi beroperasi di kampus SMK Bangun Nusa Bangsa.',
            'content' => '<p class="lead">Menjawab percepatan transisi menuju era kendaraan ramah lingkungan, SMK Bangun Nusa Bangsa meresmikan <strong>Bengkel Teaching Factory TKR & Laboratorium Kendaraan Listrik (Electric Vehicle)</strong>.</p>
            <p>Fasilitas ini dilengkapi dengan scanner komputer diagnostik multi-brand, car lift hidrolik modern, unit simulator baterai EV, serta peralatan tune-up berstandar bengkel resmi agen pemegang merek (APM). Siswa TKR dilatih secara intensif oleh instruktur bersertifikasi industri.</p>',
            'views' => 1120
        ],
        [
            'title' => 'Kemitraan Strategis dengan 25 Perusahaan Multinasional untuk Penyaluran Kerja Lulusan',
            'slug' => 'kemitraan-strategis-25-perusahaan-multinasional-penyaluran-kerja',
            'category_id' => $catIds['kerjasama-industri'] ?? 4,
            'thumbnail' => 'assets/images/jurusan-tkj.jpg',
            'excerpt' => 'Melalui program Link and Match, SMK Bangun Nusa Bangsa memperluas MoU dengan 25 perusahaan terkemuka di bidang Akuntansi, Jaringan IT, dan Otomotif.',
            'content' => '<p class="lead">Tingkat keterserapan kerja lulusan selalu menjadi komitmen utama SMK Bangun Nusa Bangsa. Melalui Bursa Kerja Khusus (BKK), sekolah secara konsisten menyalurkan lulusan AKL, TKJ, dan TKR ke berbagai sektor industri strategis nasional dan multinasional.</p>',
            'views' => 950
        ]
    ];

    $stmtArt = $pdo->prepare("INSERT INTO `articles` (`title`, `slug`, `category_id`, `author_id`, `thumbnail`, `excerpt`, `content`, `views`, `status`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'published')");
    $artIds = [];
    foreach ($articles as $art) {
        $stmtArt->execute([$art['title'], $art['slug'], $art['category_id'], $adminId, $art['thumbnail'], $art['excerpt'], $art['content'], $art['views']]);
        $artIds[] = $pdo->lastInsertId();
    }
}
