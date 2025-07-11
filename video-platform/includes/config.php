<?php
/**
 * DOBİEN Video Platform - Ana Config Dosyası
 * Geliştirici: DOBİEN
 * Modern Video Paylaşım Platformu
 */

// Oturum başlat
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Hata raporlamayı ayarla
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Zaman dilimi ayarla
date_default_timezone_set('Europe/Istanbul');

// Config dosyasını dahil et
if (file_exists(__DIR__ . '/../config/database.php')) {
    require_once __DIR__ . '/../config/database.php';
} elseif (file_exists(__DIR__ . '/../config/config.php')) {
    require_once __DIR__ . '/../config/config.php';
} else {
    // Kurulum yapılmamışsa kurulum sayfasına yönlendir
    if (!strpos($_SERVER['REQUEST_URI'], 'install.php')) {
        header('Location: install.php');
        exit;
    }
}

// Veritabanı bağlantısı - sadece constants tanımlıysa bağlan
if (defined('DB_HOST') && defined('DB_NAME') && defined('DB_USER') && defined('DB_PASS')) {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
    } catch (PDOException $e) {
        // Kurulum sayfasında değilsek hata göster
        if (!strpos($_SERVER['REQUEST_URI'], 'install.php')) {
            die("Veritabanı bağlantı hatası: " . $e->getMessage());
        }
    }
}

// Site ayarlarını çek (hem eski hem yeni tablo yapısını destekle)
function getSiteSettings() {
    global $pdo;
    
    if (!isset($pdo)) {
        return false;
    }
    
    try {
        // Önce yeni tablo yapısını dene (site_ayarlari)
        $stmt = $pdo->query("SELECT anahtar, deger FROM site_ayarlari");
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['anahtar']] = $row['deger'];
        }
        return $settings;
    } catch (PDOException $e) {
        try {
            // Eski tablo yapısını dene (ayarlar)
            $stmt = $pdo->query("SELECT * FROM ayarlar LIMIT 1");
            return $stmt->fetch();
        } catch (PDOException $e2) {
            // Hiçbir tablo yoksa varsayılan değerler döndür
            return false;
        }
    }
}

// Admin oturum kontrolü (hem eski hem yeni tablo yapısını destekle)
function checkAdminSession() {
    global $pdo;
    
    if (!isset($_SESSION['admin_id']) || !isset($pdo)) {
        return false;
    }
    
    try {
        // Önce yeni tablo yapısını dene (admin_kullanicilar)
        $stmt = $pdo->prepare("SELECT * FROM admin_kullanicilar WHERE id = ? AND durum = 'aktif'");
        $stmt->execute([$_SESSION['admin_id']]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        try {
            // Eski tablo yapısını dene (adminler)
            $stmt = $pdo->prepare("SELECT * FROM adminler WHERE id = ? AND durum = 'aktif'");
            $stmt->execute([$_SESSION['admin_id']]);
            return $stmt->fetch();
        } catch (PDOException $e2) {
            return false;
        }
    }
}

// Güvenli URL fonksiyonu
function siteUrl($path = '') {
    $base_url = defined('SITE_URL') ? SITE_URL : 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
    $url = rtrim($base_url, '/');
    if ($path) {
        $url .= '/' . ltrim($path, '/');
    }
    return $url;
}

// Slug oluştur
function createSlug($text) {
    $turkish = array('ş','Ş','ı','I','İ','ğ','Ğ','ü','Ü','ö','Ö','Ç','ç','S','s');
    $english = array('s','s','i','i','i','g','g','u','u','o','o','c','c','s','s');
    $text = str_replace($turkish, $english, $text);
    
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text), '-'));
}

// Güvenli çıktı
function safeOutput($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Tarih formatla
function formatDate($date, $format = 'd.m.Y H:i') {
    return date($format, strtotime($date));
}

// Video süresini formatla
function formatDuration($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $seconds = $seconds % 60;
    
    if ($hours > 0) {
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    } else {
        return sprintf('%02d:%02d', $minutes, $seconds);
    }
}

// Dosya boyutunu formatla
function formatFileSize($bytes) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, 2) . ' ' . $units[$i];
}

// Üyelik kontrolü
function checkMembership($required_level = 'kullanici') {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    $user_level = $_SESSION['user_membership'] ?? 'kullanici';
    
    $levels = ['kullanici' => 1, 'vip' => 2, 'premium' => 3];
    
    return $levels[$user_level] >= $levels[$required_level];
}

// Admin kontrolü
function isAdmin() {
    return isset($_SESSION['admin_id']) && $_SESSION['admin_logged_in'] === true;
}

// CSRF token oluştur
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// CSRF token kontrol et
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Kullanıcı oturum bilgilerini kontrol et
function checkUserSession() {
    global $pdo;
    
    if (!isset($_SESSION['user_id']) || !isset($pdo)) {
        return false;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM kullanicilar WHERE id = ? AND durum = 'aktif'");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        if (!$user) {
            session_destroy();
            return false;
        }
        
        // Premium ve VIP sürelerini kontrol et
        $now = date('Y-m-d H:i:s');
        if (isset($user['uyelik_bitis']) && $user['uyelik_bitis'] && $user['uyelik_bitis'] < $now) {
            $pdo->prepare("UPDATE kullanicilar SET uyelik_tipi = 'kullanici', uyelik_bitis = NULL WHERE id = ?")->execute([$user['id']]);
            $_SESSION['user_membership'] = 'kullanici';
        }
        
        return $user;
    } catch (PDOException $e) {
        return false;
    }
}

// Sayfalama fonksiyonu
function pagination($current_page, $total_pages, $base_url) {
    $pagination = '';
    
    if ($total_pages > 1) {
        $pagination .= '<div class="pagination">';
        
        // Önceki sayfa
        if ($current_page > 1) {
            $pagination .= '<a href="' . $base_url . ($current_page - 1) . '" class="prev">‹ Önceki</a>';
        }
        
        // Sayfa numaraları
        $start = max(1, $current_page - 2);
        $end = min($total_pages, $current_page + 2);
        
        if ($start > 1) {
            $pagination .= '<a href="' . $base_url . '1">1</a>';
            if ($start > 2) {
                $pagination .= '<span class="dots">...</span>';
            }
        }
        
        for ($i = $start; $i <= $end; $i++) {
            if ($i == $current_page) {
                $pagination .= '<span class="current">' . $i . '</span>';
            } else {
                $pagination .= '<a href="' . $base_url . $i . '">' . $i . '</a>';
            }
        }
        
        if ($end < $total_pages) {
            if ($end < $total_pages - 1) {
                $pagination .= '<span class="dots">...</span>';
            }
            $pagination .= '<a href="' . $base_url . $total_pages . '">' . $total_pages . '</a>';
        }
        
        // Sonraki sayfa
        if ($current_page < $total_pages) {
            $pagination .= '<a href="' . $base_url . ($current_page + 1) . '" class="next">Sonraki ›</a>';
        }
        
        $pagination .= '</div>';
    }
    
    return $pagination;
}

// Kurulum kontrolü ve varsayılan ayarlar
$site_settings = [];
if (isset($pdo)) {
    try {
        $site_settings = getSiteSettings();
    } catch (Exception $e) {
        // Kurulum aşamasında hata varsa varsayılan değerler kullan
    }
}

// Varsayılan site ayarları
if (!$site_settings || empty($site_settings)) {
    $site_settings = [
        'site_baslik' => 'DOBİEN Video Platform',
        'site_aciklama' => 'Premium video deneyimi için üyeliğinizi yükseltin. 4K kalite, VIP üyelik avantajları ve sınırsız izleme deneyimi.',
        'site_anahtar_kelimeler' => 'video platform, premium videolar, 4k video, vip üyelik, DOBİEN',
        'footer_metin' => '© 2024 DOBİEN Video Platform. Tüm hakları saklıdır.',
        'yas_dogrulama_aktif' => '1',
        'varsayilan_video_kalite' => '720p'
    ];
}

// Kullanıcı oturum kontrolü
$current_user = checkUserSession();

// Veritabanı tablolarının varlığını kontrol et (kurulum sonrası)
function checkDatabaseTables() {
    global $pdo;
    
    if (!isset($pdo)) {
        return false;
    }
    
    $required_tables = ['admin_kullanicilar', 'site_ayarlari', 'kullanicilar', 'kategoriler', 'videolar'];
    
    foreach ($required_tables as $table) {
        try {
            $pdo->query("SELECT 1 FROM `$table` LIMIT 1");
        } catch (PDOException $e) {
            return false;
        }
    }
    
    return true;
}

?>