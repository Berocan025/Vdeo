THIS SHOULD BE A LINTER ERROR<?php
/**
 * DOBİEN Video Platform - Admin Panel Ana Sayfası
 * Geliştirici: DOBİEN
 * Modern Video Paylaşım Platformu - Admin Yönetim Paneli
 * Tüm Hakları Saklıdır © DOBİEN
 * 
 * HER ŞEY BU ADMIN PANELİNDEN YÖNETİLEBİLİR:
 * - Site başlığı, açıklama, keywords
 * - Logo, favicon, tüm resimler
 * - Tüm sayfaların içerikleri
 * - Slider, videolar, kategoriler
 * - Kullanıcılar, üyelikler
 * - Yaş uyarısı popup'ı
 * - Bütün yazılar ve metinler
 */

require_once '../includes/config.php';

// Admin giriş kontrolü
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: giris.php');
    exit;
}

// Tablo varlık kontrolü - Daha güvenli hata yakalama
try {
    $missing_tables = checkAdminTables();
    if ($missing_tables !== true && is_array($missing_tables)) {
        die('
        <div style="font-family: Arial; background: #1a1f2e; color: #fff; padding: 50px; text-align: center;">
            <h2>⚠️ Veritabanı Hatası</h2>
            <p>Gerekli tablolar eksik: <strong>' . implode(', ', $missing_tables) . '</strong></p>
            <p><a href="../install.php" style="color: #ff6b35;">Kurulumu Tekrar Çalıştırın</a></p>
        </div>
        ');
    }
} catch (Exception $e) {
    // Tablo kontrolünde hata varsa devam et, kritik olmayan
}

// Admin bilgilerini al - hem yeni hem eski tablo yapısını destekle
$admin_user = false;

try {
    // Önce yeni tablo yapısını dene (admin_kullanicilar)
    $admin_query = "SELECT * FROM admin_kullanicilar WHERE id = ?";
    $admin_stmt = $pdo->prepare($admin_query);
    $admin_stmt->execute([$_SESSION['admin_id']]);
    $admin_user = $admin_stmt->fetch();
} catch (PDOException $e) {
    // Yeni tablo bulunamadı, eski tabloyu dene (adminler)
    try {
        $admin_query = "SELECT * FROM adminler WHERE id = ?";
        $admin_stmt = $pdo->prepare($admin_query);
        $admin_stmt->execute([$_SESSION['admin_id']]);
        $admin_user = $admin_stmt->fetch();
    } catch (PDOException $e2) {
        session_destroy();
        header('Location: giris.php');
        exit;
    }
}

if (!$admin_user || $admin_user['durum'] !== 'aktif') {
    session_destroy();
    header('Location: giris.php');
    exit;
}

// İstatistikler - hata kontrolü ile
$stats = [];

// Kullanıcı istatistikleri
try {
    $stats['kullanicilar'] = [
        'toplam' => $pdo->query("SELECT COUNT(*) FROM kullanicilar")->fetchColumn() ?: 0,
        'aktif' => $pdo->query("SELECT COUNT(*) FROM kullanicilar WHERE durum = 'aktif'")->fetchColumn() ?: 0,
        'premium' => $pdo->query("SELECT COUNT(*) FROM kullanicilar WHERE uyelik_tipi = 'premium'")->fetchColumn() ?: 0,
        'vip' => $pdo->query("SELECT COUNT(*) FROM kullanicilar WHERE uyelik_tipi = 'vip'")->fetchColumn() ?: 0,
        'bugun' => $pdo->query("SELECT COUNT(*) FROM kullanicilar WHERE DATE(kayit_tarihi) = CURDATE()")->fetchColumn() ?: 0
    ];
} catch (PDOException $e) {
    $stats['kullanicilar'] = ['toplam' => 0, 'aktif' => 0, 'premium' => 0, 'vip' => 0, 'bugun' => 0];
}

// Video istatistikleri
try {
    $stats['videolar'] = [
        'toplam' => $pdo->query("SELECT COUNT(*) FROM videolar")->fetchColumn() ?: 0,
        'aktif' => $pdo->query("SELECT COUNT(*) FROM videolar WHERE durum = 'aktif'")->fetchColumn() ?: 0,
        'beklemede' => $pdo->query("SELECT COUNT(*) FROM videolar WHERE durum = 'beklemede'")->fetchColumn() ?: 0,
        'premium' => $pdo->query("SELECT COUNT(*) FROM videolar WHERE goruntulenme_yetkisi = 'premium'")->fetchColumn() ?: 0,
        'bugun' => $pdo->query("SELECT COUNT(*) FROM videolar WHERE DATE(ekleme_tarihi) = CURDATE()")->fetchColumn() ?: 0
    ];
} catch (PDOException $e) {
    $stats['videolar'] = ['toplam' => 0, 'aktif' => 0, 'beklemede' => 0, 'premium' => 0, 'bugun' => 0];
}

// Kategori istatistikleri
try {
    $stats['kategoriler'] = [
        'toplam' => $pdo->query("SELECT COUNT(*) FROM kategoriler")->fetchColumn() ?: 0,
        'aktif' => $pdo->query("SELECT COUNT(*) FROM kategoriler WHERE durum = 'aktif'")->fetchColumn() ?: 0
    ];
} catch (PDOException $e) {
    $stats['kategoriler'] = ['toplam' => 0, 'aktif' => 0];
}

// Satın alma istatistikleri
try {
    $stats['satin_almalar'] = [
        'toplam' => $pdo->query("SELECT COUNT(*) FROM odeme_gecmisi")->fetchColumn() ?: 0,
        'onaylanan' => $pdo->query("SELECT COUNT(*) FROM odeme_gecmisi WHERE durum = 'tamamlandi'")->fetchColumn() ?: 0,
        'bekleyen' => $pdo->query("SELECT COUNT(*) FROM odeme_gecmisi WHERE durum = 'beklemede'")->fetchColumn() ?: 0,
        'bugun' => $pdo->query("SELECT COUNT(*) FROM odeme_gecmisi WHERE DATE(odeme_tarihi) = CURDATE()")->fetchColumn() ?: 0
    ];
} catch (PDOException $e) {
    $stats['satin_almalar'] = ['toplam' => 0, 'onaylanan' => 0, 'bekleyen' => 0, 'bugun' => 0];
}

// Toplam gelir
try {
    $toplam_gelir = $pdo->query("SELECT SUM(tutar) FROM odeme_gecmisi WHERE durum = 'tamamlandi'")->fetchColumn() ?: 0;
    $aylik_gelir = $pdo->query("SELECT SUM(tutar) FROM odeme_gecmisi WHERE durum = 'tamamlandi' AND MONTH(odeme_tarihi) = MONTH(CURDATE()) AND YEAR(odeme_tarihi) = YEAR(CURDATE())")->fetchColumn() ?: 0;
} catch (PDOException $e) {
    $toplam_gelir = 0;
    $aylik_gelir = 0;
}

// Son aktiviteler
try {
    $son_kullanicilar = $pdo->query("SELECT * FROM kullanicilar ORDER BY kayit_tarihi DESC LIMIT 5")->fetchAll() ?: [];
} catch (PDOException $e) {
    $son_kullanicilar = [];
}

try {
    $son_videolar = $pdo->query("SELECT * FROM videolar ORDER BY ekleme_tarihi DESC LIMIT 5")->fetchAll() ?: [];
} catch (PDOException $e) {
    $son_videolar = [];
}

try {
    $son_satislar = $pdo->query("SELECT og.*, k.ad, k.soyad FROM odeme_gecmisi og LEFT JOIN kullanicilar k ON og.kullanici_id = k.id ORDER BY og.odeme_tarihi DESC LIMIT 5")->fetchAll() ?: [];
} catch (PDOException $e) {
    $son_satislar = [];
}

// Sistem bilgileri
$sistem_bilgileri = [
    'php_version' => phpversion(),
    'mysql_version' => $pdo->query("SELECT VERSION()")->fetchColumn(),
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Bilinmiyor',
    'disk_usage' => function_exists('disk_free_space') ? disk_free_space('.') : 0,
    'memory_usage' => memory_get_usage(true),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size')
];

$page_title = "Admin Panel - Ana Sayfa";
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - DOBİEN Video Platform</title>
    
    <!-- DOBİEN Admin Panel CSS -->
    <link rel="stylesheet" href="assets/css/admin.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="admin-body">

<!-- Admin Panel Wrapper -->
<div class="admin-wrapper">
    
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <div class="admin-logo">
                <h2><span class="text-gradient">DOBİEN</span></h2>
                <p>Admin Panel</p>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item active">
                    <a href="index.php" class="nav-link">
                        <i class="fas fa-home"></i>
                        <span>Ana Sayfa</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="#" class="nav-link has-submenu" data-submenu="site">
                        <i class="fas fa-cog"></i>
                        <span>Site Yönetimi</span>
                        <i class="fas fa-chevron-down submenu-arrow"></i>
                    </a>
                    <ul class="submenu" id="submenu-site">
                        <li><a href="site-ayarlari.php"><i class="fas fa-globe"></i> Site Ayarları</a></li>
                        <li><a href="yazilar.php"><i class="fas fa-edit"></i> Yazılar & Metinler</a></li>
                        <li><a href="slider.php"><i class="fas fa-images"></i> Slider Yönetimi</a></li>
                        <li><a href="sayfalar.php"><i class="fas fa-file"></i> Sayfa İçerikleri</a></li>
                        <li><a href="yas-uyarisi.php"><i class="fas fa-exclamation-triangle"></i> Yaş Uyarısı</a></li>
                    </ul>
                </li>
                
                <li class="nav-item">
                    <a href="#" class="nav-link has-submenu" data-submenu="content">
                        <i class="fas fa-video"></i>
                        <span>İçerik Yönetimi</span>
                        <i class="fas fa-chevron-down submenu-arrow"></i>
                    </a>
                    <ul class="submenu" id="submenu-content">
                        <li><a href="videolar.php"><i class="fas fa-play"></i> Video Yönetimi</a></li>
                        <li><a href="kategoriler.php"><i class="fas fa-th-large"></i> Kategori Yönetimi</a></li>
                        <li><a href="etiketler.php"><i class="fas fa-tags"></i> Etiket Yönetimi</a></li>
                        <li><a href="yorumlar.php"><i class="fas fa-comments"></i> Yorum Yönetimi</a></li>
                    </ul>
                </li>
                
                <li class="nav-item">
                    <a href="#" class="nav-link has-submenu" data-submenu="users">
                        <i class="fas fa-users"></i>
                        <span>Kullanıcı Yönetimi</span>
                        <i class="fas fa-chevron-down submenu-arrow"></i>
                    </a>
                    <ul class="submenu" id="submenu-users">
                        <li><a href="kullanicilar.php"><i class="fas fa-user"></i> Kullanıcılar</a></li>
                        <li><a href="uyelikler.php"><i class="fas fa-crown"></i> Üyelik Paketleri</a></li>
                        <li><a href="odemeler.php"><i class="fas fa-credit-card"></i> Ödemeler</a></li>
                        <li><a href="mesajlar.php"><i class="fas fa-envelope"></i> Mesajlar</a></li>
                    </ul>
                </li>
                
                <li class="nav-item">
                    <a href="#" class="nav-link has-submenu" data-submenu="media">
                        <i class="fas fa-folder"></i>
                        <span>Medya Yönetimi</span>
                        <i class="fas fa-chevron-down submenu-arrow"></i>
                    </a>
                    <ul class="submenu" id="submenu-media">
                        <li><a href="medya-kutuphanesi.php"><i class="fas fa-images"></i> Medya Kütüphanesi</a></li>
                        <li><a href="dosya-yukle.php"><i class="fas fa-upload"></i> Dosya Yükle</a></li>
                        <li><a href="logo-favicon.php"><i class="fas fa-image"></i> Logo & Favicon</a></li>
                    </ul>
                </li>
                
                <li class="nav-item">
                    <a href="#" class="nav-link has-submenu" data-submenu="reports">
                        <i class="fas fa-chart-bar"></i>
                        <span>Raporlar & İstatistikler</span>
                        <i class="fas fa-chevron-down submenu-arrow"></i>
                    </a>
                    <ul class="submenu" id="submenu-reports">
                        <li><a href="istatistikler.php"><i class="fas fa-analytics"></i> Genel İstatistikler</a></li>
                        <li><a href="gelir-raporlari.php"><i class="fas fa-money-bill"></i> Gelir Raporları</a></li>
                        <li><a href="kullanici-analitiği.php"><i class="fas fa-user-chart"></i> Kullanıcı Analitikleri</a></li>
                        <li><a href="video-analitiği.php"><i class="fas fa-video"></i> Video Analitikleri</a></li>
                    </ul>
                </li>
                
                <li class="nav-item">
                    <a href="#" class="nav-link has-submenu" data-submenu="system">
                        <i class="fas fa-server"></i>
                        <span>Sistem</span>
                        <i class="fas fa-chevron-down submenu-arrow"></i>
                    </a>
                    <ul class="submenu" id="submenu-system">
                        <li><a href="yedekleme.php"><i class="fas fa-database"></i> Yedekleme</a></li>
                        <li><a href="sistem-logları.php"><i class="fas fa-list-alt"></i> Sistem Logları</a></li>
                        <li><a href="guvenlik.php"><i class="fas fa-shield-alt"></i> Güvenlik</a></li>
                        <li><a href="cache-temizle.php"><i class="fas fa-trash"></i> Cache Temizle</a></li>
                    </ul>
                </li>
                
                <li class="nav-item">
                    <a href="adminler.php" class="nav-link">
                        <i class="fas fa-user-shield"></i>
                        <span>Admin Yönetimi</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <div class="sidebar-footer">
            <div class="developer-info">
                <i class="fas fa-code"></i>
                <span><strong>DOBİEN</strong> Admin Panel</span>
            </div>
        </div>
    </aside>
    
    <!-- Main Content -->
    <main class="admin-main">
        
        <!-- Top Bar -->
        <header class="admin-topbar">
            <div class="topbar-left">
                <button class="mobile-sidebar-toggle" id="mobileSidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">
                    <i class="fas fa-home"></i>
                    Admin Panel
                </h1>
            </div>
            
            <div class="topbar-right">
                <div class="topbar-item">
                    <button class="btn btn-outline" onclick="window.open('../index.php', '_blank')">
                        <i class="fas fa-external-link-alt"></i>
                        Siteyi Görüntüle
                    </button>
                </div>
                
                <div class="topbar-item dropdown">
                    <button class="admin-profile-toggle">
                        <img src="<?php echo $admin_user['avatar'] ?? '../assets/images/default-avatar.png'; ?>" alt="Admin" class="admin-avatar">
                        <span class="admin-name"><?php echo safeOutput($admin_user['ad'] . ' ' . $admin_user['soyad'] ?: $admin_user['kullanici_adi'] ?: $admin_user['email']); ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a href="profil.php"><i class="fas fa-user"></i> Profilim</a>
                        <a href="ayarlar.php"><i class="fas fa-cog"></i> Ayarlar</a>
                        <div class="divider"></div>
                        <a href="cikis.php" class="text-danger"><i class="fas fa-sign-out-alt"></i> Çıkış Yap</a>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Dashboard Content -->
        <div class="admin-content">
            
            <!-- Welcome Section -->
            <div class="welcome-section">
                <div class="welcome-content">
                    <h2>Hoş Geldiniz, <?php echo safeOutput(($admin_user['ad'] . ' ' . $admin_user['soyad']) ?: $admin_user['ad_soyad'] ?: $admin_user['kullanici_adi'] ?: $admin_user['email']); ?>!</h2>
                    <p>DOBİEN Video Platform Admin Paneline hoş geldiniz. Burada sitenizin her yönünü kontrol edebilirsiniz.</p>
                    <div class="quick-actions">
                        <a href="videolar.php" class="quick-action-btn">
                            <i class="fas fa-plus"></i>
                            Yeni Video Ekle
                        </a>
                        <a href="site-ayarlari.php" class="quick-action-btn">
                            <i class="fas fa-cog"></i>
                            Site Ayarları
                        </a>
                        <a href="kullanicilar.php" class="quick-action-btn">
                            <i class="fas fa-users"></i>
                            Kullanıcıları Yönet
                        </a>
                    </div>
                </div>
                <div class="welcome-stats">
                    <div class="stat-card">
                        <i class="fas fa-users"></i>
                        <div class="stat-info">
                            <h3><?php echo number_format($stats['kullanicilar']['bugun']); ?></h3>
                            <p>Bugün Kayıt</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-video"></i>
                        <div class="stat-info">
                            <h3><?php echo number_format($stats['videolar']['bugun']); ?></h3>
                            <p>Bugün Eklenen</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-money-bill"></i>
                        <div class="stat-info">
                            <h3><?php echo number_format($aylik_gelir, 2); ?> ₺</h3>
                            <p>Bu Ay Gelir</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Ana İstatistikler -->
            <div class="dashboard-grid">
                
                <!-- Kullanıcı İstatistikleri -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3><i class="fas fa-users"></i> Kullanıcı İstatistikleri</h3>
                        <a href="kullanicilar.php" class="btn btn-sm btn-outline">Tümünü Gör</a>
                    </div>
                    <div class="card-content">
                        <div class="stats-grid">
                            <div class="stat-item">
                                <div class="stat-number"><?php echo number_format($stats['kullanicilar']['toplam']); ?></div>
                                <div class="stat-label">Toplam Kullanıcı</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number"><?php echo number_format($stats['kullanicilar']['aktif']); ?></div>
                                <div class="stat-label">Aktif Kullanıcı</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number"><?php echo number_format($stats['kullanicilar']['premium']); ?></div>
                                <div class="stat-label">Premium Üye</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number"><?php echo number_format($stats['kullanicilar']['vip']); ?></div>
                                <div class="stat-label">VIP Üye</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Video İstatistikleri -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3><i class="fas fa-video"></i> Video İstatistikleri</h3>
                        <a href="videolar.php" class="btn btn-sm btn-outline">Tümünü Gör</a>
                    </div>
                    <div class="card-content">
                        <div class="stats-grid">
                            <div class="stat-item">
                                <div class="stat-number"><?php echo number_format($stats['videolar']['toplam']); ?></div>
                                <div class="stat-label">Toplam Video</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number"><?php echo number_format($stats['videolar']['aktif']); ?></div>
                                <div class="stat-label">Aktif Video</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number"><?php echo number_format($stats['videolar']['beklemede']); ?></div>
                                <div class="stat-label">Beklemede</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number"><?php echo number_format($stats['videolar']['premium']); ?></div>
                                <div class="stat-label">Premium Video</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Gelir İstatistikleri -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3><i class="fas fa-money-bill"></i> Gelir İstatistikleri</h3>
                        <a href="gelir-raporlari.php" class="btn btn-sm btn-outline">Detaylı Rapor</a>
                    </div>
                    <div class="card-content">
                        <div class="revenue-stats">
                            <div class="revenue-item">
                                <div class="revenue-amount"><?php echo number_format($toplam_gelir, 2); ?> ₺</div>
                                <div class="revenue-label">Toplam Gelir</div>
                            </div>
                            <div class="revenue-item">
                                <div class="revenue-amount"><?php echo number_format($aylik_gelir, 2); ?> ₺</div>
                                <div class="revenue-label">Bu Ay</div>
                            </div>
                            <div class="mini-stats">
                                <div class="mini-stat">
                                    <span class="label">Bekleyen</span>
                                    <span class="value"><?php echo $stats['satin_almalar']['bekleyen']; ?></span>
                                </div>
                                <div class="mini-stat">
                                    <span class="label">Onaylanan</span>
                                    <span class="value"><?php echo $stats['satin_almalar']['onaylanan']; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sistem Durumu -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3><i class="fas fa-server"></i> Sistem Durumu</h3>
                        <a href="sistem-logları.php" class="btn btn-sm btn-outline">Logları Gör</a>
                    </div>
                    <div class="card-content">
                        <div class="system-info">
                            <div class="system-item">
                                <span class="label">PHP Sürümü:</span>
                                <span class="value"><?php echo $sistem_bilgileri['php_version']; ?></span>
                            </div>
                            <div class="system-item">
                                <span class="label">MySQL Sürümü:</span>
                                <span class="value"><?php echo explode('-', $sistem_bilgileri['mysql_version'])[0]; ?></span>
                            </div>
                            <div class="system-item">
                                <span class="label">Bellek Kullanımı:</span>
                                <span class="value"><?php echo formatFileSize($sistem_bilgileri['memory_usage']); ?></span>
                            </div>
                            <div class="system-item">
                                <span class="label">Max Upload:</span>
                                <span class="value"><?php echo $sistem_bilgileri['upload_max_filesize']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            
            <!-- Son Aktiviteler -->
            <div class="activity-section">
                
                <!-- Son Kullanıcılar -->
                <div class="activity-card">
                    <div class="card-header">
                        <h3><i class="fas fa-user-plus"></i> Son Kayıt Olan Kullanıcılar</h3>
                        <a href="kullanicilar.php" class="btn btn-sm btn-outline">Tümünü Gör</a>
                    </div>
                    <div class="card-content">
                        <div class="activity-list">
                            <?php foreach ($son_kullanicilar as $kullanici): ?>
                            <div class="activity-item">
                                <div class="activity-avatar">
                                    <img src="<?php echo $kullanici['profil_resmi'] ? '../uploads/avatars/' . $kullanici['profil_resmi'] : '../assets/images/default-avatar.png'; ?>" alt="<?php echo safeOutput($kullanici['ad'] . ' ' . $kullanici['soyad']); ?>">
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title"><?php echo safeOutput($kullanici['ad'] . ' ' . $kullanici['soyad'] ?: $kullanici['email']); ?></div>
                                    <div class="activity-meta">
                                        <span class="membership-badge <?php echo $kullanici['uyelik_tipi']; ?>">
                                            <?php echo ucfirst($kullanici['uyelik_tipi']); ?>
                                        </span>
                                        <span class="activity-date"><?php echo formatDate($kullanici['kayit_tarihi'], 'd.m.Y H:i'); ?></span>
                                    </div>
                                </div>
                                <div class="activity-actions">
                                    <a href="kullanici-duzenle.php?id=<?php echo $kullanici['id']; ?>" class="btn btn-xs btn-outline">Düzenle</a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Son Videolar -->
                <div class="activity-card">
                    <div class="card-header">
                        <h3><i class="fas fa-video"></i> Son Eklenen Videolar</h3>
                        <a href="videolar.php" class="btn btn-sm btn-outline">Tümünü Gör</a>
                    </div>
                    <div class="card-content">
                        <div class="activity-list">
                            <?php foreach ($son_videolar as $video): ?>
                            <div class="activity-item">
                                <div class="activity-thumbnail">
                                    <img src="<?php echo $video['kapak_resmi'] ? '../uploads/thumbnails/' . $video['kapak_resmi'] : '../assets/images/default-thumbnail.jpg'; ?>" alt="<?php echo safeOutput($video['baslik']); ?>">
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title"><?php echo safeOutput($video['baslik']); ?></div>
                                    <div class="activity-meta">
                                        <span class="status-badge <?php echo $video['durum']; ?>"><?php echo ucfirst($video['durum']); ?></span>
                                        <span class="membership-required"><?php echo ucfirst($video['goruntulenme_yetkisi']); ?></span>
                                        <span class="activity-date"><?php echo formatDate($video['ekleme_tarihi'], 'd.m.Y H:i'); ?></span>
                                    </div>
                                </div>
                                <div class="activity-actions">
                                    <a href="video-duzenle.php?id=<?php echo $video['id']; ?>" class="btn btn-xs btn-outline">Düzenle</a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Son Satışlar -->
                <div class="activity-card">
                    <div class="card-header">
                        <h3><i class="fas fa-shopping-cart"></i> Son Satışlar</h3>
                        <a href="odemeler.php" class="btn btn-sm btn-outline">Tümünü Gör</a>
                    </div>
                    <div class="card-content">
                        <div class="activity-list">
                            <?php foreach ($son_satislar as $satis): ?>
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title"><?php echo safeOutput($satis['kullanici_adi']); ?></div>
                                    <div class="activity-meta">
                                        <span class="amount"><?php echo number_format($satis['tutar'], 2); ?> ₺</span>
                                        <span class="product-type"><?php echo ucfirst($satis['urun_tipi']); ?></span>
                                        <span class="status-badge <?php echo $satis['durum']; ?>"><?php echo ucfirst($satis['durum']); ?></span>
                                        <span class="activity-date"><?php echo formatDate($satis['satin_alma_tarihi'], 'd.m.Y H:i'); ?></span>
                                    </div>
                                </div>
                                <div class="activity-actions">
                                    <a href="odeme-detay.php?id=<?php echo $satis['id']; ?>" class="btn btn-xs btn-outline">Detay</a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
            </div>
            
        </div>
        
    </main>
    
</div>

<!-- DOBİEN Developer Signature -->
<div class="admin-signature">
    <i class="fas fa-shield-alt"></i>
    <span><strong>DOBİEN</strong> Admin Panel v1.0</span>
</div>

<!-- Scripts -->
<script src="assets/js/admin.js?v=<?php echo time(); ?>"></script>

<script>
/**
 * DOBİEN Video Platform - Admin Panel JavaScript
 * Geliştirici: DOBİEN
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOBİEN Admin Panel yüklendi!');
    
    // Dashboard işlevleri burada
    initializeDashboard();
});

function initializeDashboard() {
    // Dashboard başlatma kodları
    console.log('Dashboard başlatıldı!');
}
</script>

</body>
</html>