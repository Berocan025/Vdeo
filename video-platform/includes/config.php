<?php
/**
 * DOBİEN Video Platform - Ana Config Dosyası
 * Geliştirici: DOBİEN
 * Modern Video Paylaşım Platformu
 */

// Session'ı başlat (session ayarları php.ini'de yapılmalı)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Hata raporlamayı ayarla
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Zaman dilimi ayarla
date_default_timezone_set('Europe/Istanbul');

// Config dosyasını dahil et
if (file_exists(__DIR__ . '/../config/config.php')) {
    require_once __DIR__ . '/../config/config.php';
} else {
    // Kurulum yapılmamışsa kurulum sayfasına yönlendir
    if (!strpos($_SERVER['REQUEST_URI'], 'install.php')) {
        header('Location: install.php');
        exit;
    }
}

// Veritabanı bağlantısı
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

// Site ayarlarını çek
function getSiteSettings() {
    global $pdo;
    try {
        // Önce yeni tablo formatını dene (site_ayarlari)
        $stmt = $pdo->query("SELECT anahtar, deger FROM site_ayarlari");
        $rows = $stmt->fetchAll();
        
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['anahtar']] = $row['deger'];
        }
        
        // Eski format için uyumluluk
        if (!empty($settings)) {
            return [
                'site_adi' => $settings['site_baslik'] ?? 'DOBİEN Video Platform',
                'site_url' => SITE_URL ?? 'http://localhost',
                'site_aciklama' => $settings['site_aciklama'] ?? 'Modern Video Paylaşım Platformu',
                'footer_metin' => $settings['footer_metin'] ?? 'DOBİEN tarafından geliştirildi.',
                'logo' => $settings['site_logo'] ?? '',
                'favicon' => $settings['site_favicon'] ?? '',
                'google_analytics' => $settings['google_analytics'] ?? '',
                'yas_dogrulama_aktif' => $settings['yas_dogrulama_aktif'] ?? '1',
                'yas_dogrulama_baslik' => $settings['yas_dogrulama_baslik'] ?? 'Yaş Doğrulama',
                'yas_dogrulama_mesaj' => $settings['yas_dogrulama_mesaj'] ?? '18 yaşından büyük olmalısınız.'
            ];
        }
    } catch (PDOException $e) {
        // Eski tablo formatını dene (ayarlar)
        try {
            $stmt = $pdo->query("SELECT * FROM ayarlar LIMIT 1");
            return $stmt->fetch();
        } catch (PDOException $e2) {
            // Varsayılan değerler döndür
            return [
                'site_adi' => 'DOBİEN Video Platform',
                'site_url' => SITE_URL ?? 'http://localhost',
                'site_aciklama' => 'Modern Video Paylaşım Platformu',
                'footer_metin' => 'DOBİEN tarafından geliştirildi.',
                'yas_dogrulama_aktif' => '1'
            ];
        }
    }
}

// Güvenli URL fonksiyonu
function siteUrl($path = '') {
    $url = rtrim(SITE_URL, '/');
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

// Admin oturum kontrolü
function checkAdminSession() {
    if (isset($_SESSION['admin_id'])) {
        global $pdo;
        
        // Önce yeni tablo yapısını dene (admin_kullanicilar)
        try {
            $stmt = $pdo->prepare("SELECT * FROM admin_kullanicilar WHERE id = ? AND durum = 'aktif'");
            $stmt->execute([$_SESSION['admin_id']]);
            $admin = $stmt->fetch();
            
            if (!$admin) {
                session_destroy();
                return false;
            }
            
            return $admin;
        } catch (PDOException $e) {
            // Yeni tablo bulunamadı, eski tabloyu dene (adminler)
            try {
                $stmt = $pdo->prepare("SELECT * FROM adminler WHERE id = ? AND durum = 'aktif'");
                $stmt->execute([$_SESSION['admin_id']]);
                $admin = $stmt->fetch();
                
                if (!$admin) {
                    session_destroy();
                    return false;
                }
                
                return $admin;
            } catch (PDOException $e2) {
                // Hiçbir admin tablosu bulunamadı
                session_destroy();
                return false;
            }
        }
    }
    return false;
}

// Tablo varlık kontrolü
function checkTableExists($table_name) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table_name]);
        return $stmt->fetch() !== false;
    } catch (PDOException $e) {
        return false;
    }
}

// Admin paneli için tablo varlık kontrolü
function checkAdminTables() {
    $required_tables = ['admin_kullanicilar', 'site_ayarlari', 'kullanicilar', 'kategoriler', 'videolar'];
    $missing_tables = [];
    
    foreach ($required_tables as $table) {
        if (!checkTableExists($table)) {
            $missing_tables[] = $table;
        }
    }
    
    return empty($missing_tables) ? true : $missing_tables;
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
    if (isset($_SESSION['user_id'])) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM kullanicilar WHERE id = ? AND durum = 'aktif'");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        if (!$user) {
            session_destroy();
            return false;
        }
        
        // Premium ve VIP sürelerini kontrol et (güvenli erişim)
        $now = date('Y-m-d H:i:s');
        
        // Premium süre kontrolü
        $premium_bitis = $user['premium_bitis'] ?? null;
        if ($premium_bitis && $premium_bitis < $now) {
            $pdo->prepare("UPDATE kullanicilar SET uyelik_tipi = 'kullanici', premium_bitis = NULL WHERE id = ?")->execute([$user['id']]);
            $_SESSION['user_membership'] = 'kullanici';
        }
        
        // VIP süre kontrolü  
        $vip_bitis = $user['vip_bitis'] ?? null;
        if ($vip_bitis && $vip_bitis < $now) {
            $pdo->prepare("UPDATE kullanicilar SET uyelik_tipi = 'kullanici', vip_bitis = NULL WHERE id = ?")->execute([$user['id']]);
            $_SESSION['user_membership'] = 'kullanici';
        }
        
        return $user;
    }
    return false;
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

// Site ayarlarını global değişkene al
$site_settings = getSiteSettings();
if (!$site_settings) {
    $site_settings = [
        'site_adi' => 'DOBİEN Video Platform',
        'site_url' => SITE_URL,
        'site_aciklama' => 'Modern Video Paylaşım Platformu',
        'footer_metin' => 'DOBİEN tarafından geliştirildi. Tüm hakları saklıdır.'
    ];
}

// Kullanıcı oturum kontrolü
$current_user = checkUserSession();

?>