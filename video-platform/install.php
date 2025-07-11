<?php
/**
 * DOBİEN Video Platform - Kurulum Dosyası
 * Geliştirici: DOBİEN
 * Tüm Hakları Saklıdır © DOBİEN
 */

// Güvenlik kontrolü
if (file_exists('config/config.php')) {
    $config_exists = true;
} else {
    $config_exists = false;
}

$kurulum_tamamlandi = false;
$mesaj = '';
$hata = '';

// Form gönderildi mi?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Form verilerini al
        $veritabani_host = $_POST['db_host'] ?? 'localhost';
        $veritabani_adi = $_POST['db_name'] ?? 'dobien_video';
        $veritabani_kullanici = $_POST['db_user'] ?? '';
        $veritabani_sifre = $_POST['db_pass'] ?? '';
        $admin_kullanici = $_POST['admin_user'] ?? 'admin';
        $admin_email = $_POST['admin_email'] ?? '';
        $admin_sifre = $_POST['admin_pass'] ?? '';
        $site_adi = $_POST['site_name'] ?? 'DOBİEN Video Platform';
        $site_url = $_POST['site_url'] ?? 'http://localhost';

        // Boş alanları kontrol et
        if (empty($veritabani_kullanici) || empty($admin_email) || empty($admin_sifre)) {
            throw new Exception("Lütfen tüm zorunlu alanları doldurun!");
        }

        // Veritabanı bağlantısını test et
        $dsn = "mysql:host=$veritabani_host;charset=utf8mb4";
        $pdo = new PDO($dsn, $veritabani_kullanici, $veritabani_sifre);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Veritabanını oluştur
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$veritabani_adi` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$veritabani_adi`");

        // Kapsamlı veritabanı şemasını yükle
        $sql_file = __DIR__ . '/database/complete_database.sql';
        
        if (file_exists($sql_file)) {
            $sql = file_get_contents($sql_file);
            
            // SQL dosyasını çalıştır
            $statements = explode(';', $sql);
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement) && !str_starts_with($statement, '--') && !str_starts_with($statement, 'SELECT')) {
                    try {
                        $pdo->exec($statement);
                    } catch (PDOException $e) {
                        // Hata durumunda devam et (bazı tablolar zaten var olabilir)
                        error_log("SQL Error: " . $e->getMessage() . " - Statement: " . $statement);
                        continue;
                    }
                }
            }
        } else {
            // Fallback: Temel tabloları oluştur
            $basic_sql = "
            SET FOREIGN_KEY_CHECKS = 0;
            
            CREATE TABLE IF NOT EXISTS `ayarlar` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `site_adi` VARCHAR(255) DEFAULT 'DOBİEN Video Platform',
                `site_url` VARCHAR(255) DEFAULT 'http://localhost',
                `site_aciklama` TEXT DEFAULT 'Modern Video Paylaşım Platformu',
                `meta_anahtar` TEXT,
                `email` VARCHAR(255),
                `telefon` VARCHAR(50),
                `adres` TEXT,
                `footer_metin` TEXT DEFAULT 'DOBİEN tarafından geliştirildi. Tüm hakları saklıdır.',
                `logo` VARCHAR(255),
                `favicon` VARCHAR(255),
                `sosyal_facebook` VARCHAR(255),
                `sosyal_twitter` VARCHAR(255),
                `sosyal_instagram` VARCHAR(255),
                `sosyal_youtube` VARCHAR(255),
                `analytics_kod` TEXT,
                `kayit_durumu` TINYINT DEFAULT 1,
                `email_dogrulama` TINYINT DEFAULT 0,
                `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `guncelleme_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            
            CREATE TABLE IF NOT EXISTS `admin_kullanicilar` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `kullanici_adi` VARCHAR(50) NOT NULL UNIQUE,
                `email` VARCHAR(100) NOT NULL UNIQUE,
                `sifre` VARCHAR(255) NOT NULL,
                `ad` VARCHAR(50),
                `soyad` VARCHAR(50),
                `telefon` VARCHAR(20),
                `avatar` VARCHAR(255),
                `rol` ENUM('super_admin', 'admin', 'editor', 'moderator') DEFAULT 'admin',
                `durum` TINYINT DEFAULT 1,
                `son_giris` TIMESTAMP NULL,
                `son_ip` VARCHAR(45),
                `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `guncelleme_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            
            CREATE TABLE IF NOT EXISTS `adminler` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `kullanici_adi` VARCHAR(50) NOT NULL UNIQUE,
                `email` VARCHAR(100) NOT NULL UNIQUE,
                `sifre` VARCHAR(255) NOT NULL,
                `ad` VARCHAR(50),
                `soyad` VARCHAR(50),
                `telefon` VARCHAR(20),
                `avatar` VARCHAR(255),
                `rol` ENUM('super_admin', 'admin', 'editor', 'moderator') DEFAULT 'admin',
                `durum` TINYINT DEFAULT 1,
                `son_giris` TIMESTAMP NULL,
                `son_ip` VARCHAR(45),
                `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `guncelleme_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            
            CREATE TABLE IF NOT EXISTS `slider` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `baslik` VARCHAR(255) NOT NULL,
                `aciklama` TEXT,
                `resim` VARCHAR(255) NOT NULL,
                `link` VARCHAR(500),
                `buton_metni` VARCHAR(50),
                `sira` INT DEFAULT 0,
                `durum` TINYINT DEFAULT 1,
                `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `guncelleme_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            
            CREATE TABLE IF NOT EXISTS `yas_uyarisi_ayarlari` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `aktif` TINYINT DEFAULT 1,
                `baslik` VARCHAR(255) DEFAULT 'DOBİEN',
                `alt_baslik` VARCHAR(255) DEFAULT 'Video Platform',
                `uyari_baslik` VARCHAR(255) DEFAULT 'Yaş Doğrulama Gerekli',
                `uyari_metni` TEXT DEFAULT 'Bu siteye erişebilmeniz için 18 yaşından büyük olmanız gerekmektedir.',
                `onay_butonu` VARCHAR(100) DEFAULT '18 yaşından büyüğüm',
                `red_butonu` VARCHAR(100) DEFAULT '18 yaşında değilim',
                `red_mesaji` TEXT DEFAULT 'Üzgünüz, sitemiz sizin için uygun değildir.',
                `gelistirici_notu` TEXT DEFAULT 'Bu sistem DOBİEN tarafından geliştirilmiştir.',
                `guncelleme_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            
            SET FOREIGN_KEY_CHECKS = 1;
            ";
            
            $statements = explode(';', $basic_sql);
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement) && !str_starts_with($statement, '--')) {
                    try {
                        $pdo->exec($statement);
                    } catch (PDOException $e) {
                        error_log("Basic SQL Error: " . $e->getMessage());
                        continue;
                    }
                }
            }
        }

        // Admin kullanıcı ekle
        $admin_sifre_hash = password_hash($admin_sifre, PASSWORD_DEFAULT);
        
        // Admin tablosuna veri ekle
        $admin_inserted = false;
        
        // Önce yeni tablo yapısını dene (admin_kullanicilar)
        try {
            $stmt = $pdo->prepare("INSERT INTO admin_kullanicilar (kullanici_adi, email, sifre, ad, soyad, rol) VALUES (?, ?, ?, ?, ?, 'super_admin')");
            $stmt->execute([$admin_kullanici, $admin_email, $admin_sifre_hash, 'DOBİEN', 'Admin']);
            $admin_inserted = true;
        } catch (PDOException $e) {
            // Yeni tablo yoksa eski tabloyu dene (adminler)
            try {
                $stmt = $pdo->prepare("INSERT INTO adminler (kullanici_adi, email, sifre, ad, soyad, rol) VALUES (?, ?, ?, ?, ?, 'super_admin')");
                $stmt->execute([$admin_kullanici, $admin_email, $admin_sifre_hash, 'DOBİEN', 'Admin']);
                $admin_inserted = true;
            } catch (PDOException $e2) {
                error_log("Admin insert error: " . $e2->getMessage());
            }
        }
        
        if (!$admin_inserted) {
            throw new Exception("Admin kullanıcı eklenemedi. Lütfen veritabanı ayarlarını kontrol edin.");
        }

        // Site ayarları ekle
        try {
            $stmt = $pdo->prepare("INSERT INTO ayarlar (site_adi, site_url, site_aciklama, footer_metin) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE site_adi = VALUES(site_adi), site_url = VALUES(site_url)");
            $stmt->execute([$site_adi, $site_url, 'Modern Video Paylaşım Platformu', 'DOBİEN tarafından geliştirildi. Tüm hakları saklıdır.']);
        } catch (PDOException $e) {
            error_log("Settings insert error: " . $e->getMessage());
        }

        // Yaş uyarısı ayarları ekle
        try {
            $stmt = $pdo->prepare("INSERT INTO yas_uyarisi_ayarlari (aktif, baslik, alt_baslik, uyari_baslik, uyari_metni, onay_butonu, red_butonu, red_mesaji, gelistirici_notu) VALUES (1, 'DOBİEN', 'Video Platform', 'Yaş Doğrulama Gerekli', 'Bu siteye erişebilmeniz için 18 yaşından büyük olmanız gerekmektedir. Sitemiz yetişkin içerikler barındırmaktadır ve yalnızca reşit kullanıcılar için uygundur.', '18 yaşından büyüğüm', '18 yaşında değilim', 'Üzgünüz, sitemiz sizin için uygun değildir. 18 yaş altındaki kullanıcılar siteye erişemez.', 'Bu sistem DOBİEN tarafından geliştirilmiştir ve kullanıcı güvenliği için tasarlanmıştır.') ON DUPLICATE KEY UPDATE aktif = VALUES(aktif)");
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Age warning insert error: " . $e->getMessage());
        }

        // Config klasörünü oluştur
        if (!is_dir('config')) {
            mkdir('config', 0755, true);
        }

        // Uploads klasörlerini oluştur
        $upload_dirs = [
            'uploads',
            'uploads/videos',
            'uploads/thumbnails', 
            'uploads/categories',
            'uploads/sliders',
            'uploads/avatars',
            'uploads/site'
        ];
        
        foreach ($upload_dirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }

        // Config dosyası oluştur
        $config_content = "<?php
/**
 * DOBİEN Video Platform - Ana Config Dosyası
 * Geliştirici: DOBİEN
 * Veritabanı ve Site Ayarları
 * Tüm Hakları Saklıdır © DOBİEN
 */

// Güvenlik kontrolü
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/../');
}

// Veritabanı Ayarları
define('DB_HOST', '$veritabani_host');
define('DB_NAME', '$veritabani_adi');
define('DB_USER', '$veritabani_kullanici');
define('DB_PASS', '$veritabani_sifre');
define('DB_CHARSET', 'utf8mb4');

// Site Ayarları
define('SITE_URL', '$site_url');
define('SITE_NAME', '$site_adi');
define('SITE_VERSION', '1.0.0');

// Dosya Yolları
define('UPLOADS_PATH', ABSPATH . 'uploads/');
define('ASSETS_PATH', ABSPATH . 'assets/');
define('INCLUDES_PATH', ABSPATH . 'includes/');

// Güvenlik Anahtarları
define('SALT_KEY', '" . bin2hex(random_bytes(32)) . "');
define('AUTH_KEY', '" . bin2hex(random_bytes(32)) . "');
define('SECURE_AUTH_KEY', '" . bin2hex(random_bytes(32)) . "');

// Dosya Upload Ayarları
define('MAX_FILE_SIZE', 500 * 1024 * 1024); // 500MB
define('ALLOWED_VIDEO_FORMATS', 'mp4,avi,mov,wmv,flv');
define('ALLOWED_IMAGE_FORMATS', 'jpg,jpeg,png,gif,webp');

// Cache ve Performans
define('CACHE_ENABLED', true);
define('CACHE_TIME', 3600);

// Zaman Dilimi
date_default_timezone_set('Europe/Istanbul');

// Bellek ve upload limitleri
ini_set('memory_limit', '256M');
ini_set('upload_max_filesize', '500M');
ini_set('post_max_size', '500M');
ini_set('max_execution_time', 300);

// Veritabanı bağlantısı
try {
    \$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    \$pdo = new PDO(\$dsn, DB_USER, DB_PASS);
    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    \$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException \$e) {
    die('Veritabanı bağlantı hatası: ' . \$e->getMessage());
}

// Güvenlik fonksiyonları
function safeOutput(\$data) {
    return htmlspecialchars(\$data ?? '', ENT_QUOTES, 'UTF-8');
}

function generateToken(\$length = 32) {
    return bin2hex(random_bytes(\$length));
}

function validateCSRF(\$token) {
    return isset(\$_SESSION['csrf_token']) && hash_equals(\$_SESSION['csrf_token'], \$token);
}

// Session başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CSRF token oluştur
if (!isset(\$_SESSION['csrf_token'])) {
    \$_SESSION['csrf_token'] = generateToken();
}

?>";

        file_put_contents('config/config.php', $config_content);

        // .htaccess dosyası oluştur
        $htaccess_content = "# DOBİEN Video Platform .htaccess
# Güvenlik ve Performans Ayarları

# Session ayarları
php_value session.cookie_httponly 1
php_value session.cookie_secure 0
php_value session.use_strict_mode 1
php_value session.cookie_samesite Lax

# Upload ayarları
php_value upload_max_filesize 500M
php_value post_max_size 500M
php_value max_execution_time 300
php_value memory_limit 256M

# Güvenlik başlıkları
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options SAMEORIGIN
    Header always set X-XSS-Protection \"1; mode=block\"
    Header always set Referrer-Policy \"strict-origin-when-cross-origin\"
</IfModule>

# Dosya erişim kısıtlamaları
<Files \"config.php\">
    Order allow,deny
    Deny from all
</Files>

<Files \"*.sql\">
    Order allow,deny
    Deny from all
</Files>

# URL Rewriting (isteğe bağlı)
RewriteEngine On

# Admin paneli yönlendirme
RewriteRule ^admin-giris$ admin/giris.php [L]
RewriteRule ^admin-panel$ admin/index.php [L]

# Video sayfaları
RewriteRule ^video/([^/]+)$ video.php?slug=\$1 [L]
RewriteRule ^kategori/([^/]+)$ kategori.php?slug=\$1 [L]

# Statik sayfalar
RewriteRule ^sayfa/([^/]+)$ sayfa.php?slug=\$1 [L]

# Gzip sıkıştırma
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json
</IfModule>

# Cache ayarları
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg \"access plus 1 month\"
    ExpiresByType image/jpeg \"access plus 1 month\"
    ExpiresByType image/gif \"access plus 1 month\"
    ExpiresByType image/png \"access plus 1 month\"
    ExpiresByType text/css \"access plus 1 month\"
    ExpiresByType application/pdf \"access plus 1 month\"
    ExpiresByType text/javascript \"access plus 1 month\"
    ExpiresByType application/javascript \"access plus 1 month\"
</IfModule>";

        file_put_contents('.htaccess', $htaccess_content);

        $kurulum_tamamlandi = true;
        $mesaj = "🎉 Kurulum başarıyla tamamlandı! Artık sitenizi kullanabilirsiniz.";

    } catch (Exception $e) {
        $hata = "❌ Kurulum hatası: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DOBİEN Video Platform - Kurulum</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .kurulum-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            max-width: 700px;
            width: 100%;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            max-height: 90vh;
            overflow-y: auto;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            color: #667eea;
            font-size: 2.5em;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .logo p {
            color: #666;
            font-size: 1.1em;
        }

        .form-section {
            margin-bottom: 30px;
        }

        .form-section h3 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 10px rgba(102, 126, 234, 0.2);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .basarili {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }

        .basarili .links {
            margin-top: 20px;
            display: flex;
            gap: 20px;
            justify-content: center;
        }

        .basarili a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .basarili a:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .hata {
            background: linear-gradient(135deg, #f44336, #d32f2f);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }

        .gereksinimler {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .gereksinimler h3 {
            color: #333;
            margin-bottom: 15px;
            border: none;
            padding: 0;
        }

        .gereksinimler ul {
            list-style: none;
        }

        .gereksinimler li {
            padding: 8px 0;
            color: #666;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .gereksinimler li i {
            color: #4CAF50;
            width: 20px;
        }

        .developer-info {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }

        .progress-bar {
            width: 100%;
            height: 4px;
            background: #e1e5e9;
            border-radius: 2px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            width: 0%;
            transition: width 0.3s ease;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .kurulum-container {
                padding: 20px;
            }
            
            .logo h1 {
                font-size: 2em;
            }
            
            .basarili .links {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="kurulum-container">
        <div class="logo">
            <h1><i class="fas fa-play-circle"></i> DOBİEN</h1>
            <p>Video Platform Kurulum Sihirbazı</p>
        </div>

        <?php if (isset($mesaj) && $kurulum_tamamlandi): ?>
            <div class="basarili">
                <h2><i class="fas fa-check-circle"></i> Kurulum Tamamlandı!</h2>
                <p><?php echo $mesaj; ?></p>
                <div class="links">
                    <a href="index.php"><i class="fas fa-home"></i> Ana Sayfaya Git</a>
                    <a href="admin/giris.php"><i class="fas fa-cog"></i> Admin Paneline Git</a>
                </div>
                <p style="margin-top: 20px; font-size: 14px; opacity: 0.9;">
                    <strong>Admin Bilgileri:</strong><br>
                    Kullanıcı Adı: <?php echo htmlspecialchars($admin_kullanici ?? 'admin'); ?><br>
                    E-posta: <?php echo htmlspecialchars($admin_email ?? ''); ?>
                </p>
            </div>
        <?php elseif (isset($hata)): ?>
            <div class="hata">
                <h3><i class="fas fa-exclamation-triangle"></i> Kurulum Hatası</h3>
                <p><?php echo $hata; ?></p>
            </div>
        <?php endif; ?>

        <?php if (!$kurulum_tamamlandi): ?>
            <div class="gereksinimler">
                <h3><i class="fas fa-server"></i> Sistem Gereksinimleri</h3>
                <ul>
                    <li><i class="fas fa-check"></i> PHP 7.4 veya üzeri</li>
                    <li><i class="fas fa-check"></i> MySQL 5.7 veya üzeri</li>
                    <li><i class="fas fa-check"></i> PDO Extension</li>
                    <li><i class="fas fa-check"></i> GD Library</li>
                    <li><i class="fas fa-check"></i> Dosya yazma izinleri</li>
                    <li><i class="fas fa-check"></i> mod_rewrite (önerilen)</li>
                </ul>
            </div>

            <form method="POST" id="kurulumForm">
                <div class="form-section">
                    <h3><i class="fas fa-database"></i> Veritabanı Ayarları</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-server"></i> Veritabanı Sunucusu:</label>
                            <input type="text" name="db_host" value="localhost" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-database"></i> Veritabanı Adı:</label>
                            <input type="text" name="db_name" value="dobien_video" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Kullanıcı Adı:</label>
                            <input type="text" name="db_user" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> Şifre:</label>
                            <input type="password" name="db_pass">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-user-shield"></i> Admin Hesabı</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Admin Kullanıcı Adı:</label>
                            <input type="text" name="admin_user" value="admin" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> Admin E-posta:</label>
                            <input type="email" name="admin_email" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-key"></i> Admin Şifresi:</label>
                        <input type="password" name="admin_pass" required minlength="6">
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-globe"></i> Site Ayarları</h3>

                    <div class="form-group">
                        <label><i class="fas fa-tag"></i> Site Adı:</label>
                        <input type="text" name="site_name" value="DOBİEN Video Platform" required>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-link"></i> Site URL:</label>
                        <input type="url" name="site_url" value="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']); ?>" required>
                    </div>
                </div>

                <button type="submit" class="btn">
                    <i class="fas fa-rocket"></i>
                    Kurulumu Başlat
                </button>
            </form>
        <?php endif; ?>

        <div class="developer-info">
            <strong><i class="fas fa-code"></i> Geliştirici: DOBİEN</strong><br>
            Modern Video Platform Scripti v1.0<br>
            <small>Tüm hakları saklıdır © <?php echo date('Y'); ?></small>
        </div>
    </div>

    <script>
        // Form gönderildiğinde loading göster
        document.getElementById('kurulumForm')?.addEventListener('submit', function() {
            const btn = this.querySelector('.btn');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Kurulum yapılıyor...';
            btn.disabled = true;
        });
    </script>
</body>
</html>