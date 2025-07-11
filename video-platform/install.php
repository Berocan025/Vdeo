<?php
/**
 * DOBİEN Video Platform - Kurulum Scripti
 * Geliştirici: DOBİEN
 * Modern Video Paylaşım Platformu
 */

session_start();
$kurulum_tamamlandi = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $veritabani_host = $_POST['db_host'];
    $veritabani_adi = $_POST['db_name'];
    $veritabani_kullanici = $_POST['db_user'];
    $veritabani_sifre = $_POST['db_pass'];
    $admin_kullanici = $_POST['admin_user'];
    $admin_sifre = $_POST['admin_pass'];
    $admin_email = $_POST['admin_email'];
    $site_adi = $_POST['site_name'];
    $site_url = $_POST['site_url'];

    try {
        // Veritabanı bağlantısı
        $pdo = new PDO("mysql:host=$veritabani_host;charset=utf8mb4", $veritabani_kullanici, $veritabani_sifre);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Veritabanı oluştur
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$veritabani_adi` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$veritabani_adi`");

        // Tabloları oluştur
        $tablolar = "
        CREATE TABLE IF NOT EXISTS `ayarlar` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `site_adi` varchar(255) NOT NULL,
            `site_url` varchar(255) NOT NULL,
            `site_aciklama` text,
            `meta_anahtar` text,
            `logo` varchar(255),
            `favicon` varchar(255),
            `email` varchar(255),
            `telefon` varchar(20),
            `adres` text,
            `footer_metin` text,
            `sosyal_facebook` varchar(255),
            `sosyal_twitter` varchar(255),
            `sosyal_instagram` varchar(255),
            `sosyal_youtube` varchar(255),
            `analytics_kod` text,
            `kayit_durumu` tinyint(1) DEFAULT 1,
            `email_dogrulama` tinyint(1) DEFAULT 0,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `kullanicilar` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `kullanici_adi` varchar(50) NOT NULL UNIQUE,
            `email` varchar(100) NOT NULL UNIQUE,
            `sifre` varchar(255) NOT NULL,
            `ad_soyad` varchar(100),
            `telefon` varchar(20),
            `avatar` varchar(255),
            `uyelik_tipi` enum('kullanici','vip','premium') DEFAULT 'kullanici',
            `durum` enum('aktif','pasif','yasakli') DEFAULT 'aktif',
            `email_dogrulandi` tinyint(1) DEFAULT 0,
            `kayit_tarihi` datetime DEFAULT CURRENT_TIMESTAMP,
            `son_giris` datetime,
            `premium_bitis` datetime NULL,
            `vip_bitis` datetime NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `adminler` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `kullanici_adi` varchar(50) NOT NULL UNIQUE,
            `email` varchar(100) NOT NULL UNIQUE,
            `sifre` varchar(255) NOT NULL,
            `ad_soyad` varchar(100),
            `yetki_seviyesi` enum('super_admin','admin','moderator') DEFAULT 'admin',
            `durum` enum('aktif','pasif') DEFAULT 'aktif',
            `kayit_tarihi` datetime DEFAULT CURRENT_TIMESTAMP,
            `son_giris` datetime,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `kategoriler` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `kategori_adi` varchar(100) NOT NULL,
            `slug` varchar(100) NOT NULL UNIQUE,
            `aciklama` text,
            `resim` varchar(255),
            `siralama` int(11) DEFAULT 0,
            `durum` enum('aktif','pasif') DEFAULT 'aktif',
            `olusturma_tarihi` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `videolar` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `baslik` varchar(255) NOT NULL,
            `slug` varchar(255) NOT NULL UNIQUE,
            `aciklama` text,
            `video_dosya` varchar(255) NOT NULL,
            `kapak_resmi` varchar(255),
            `kategori_id` int(11),
            `etiketler` text,
            `sure` varchar(10),
            `boyut` bigint(20),
            `izlenme_sayisi` int(11) DEFAULT 0,
            `begeni_sayisi` int(11) DEFAULT 0,
            `begenmeme_sayisi` int(11) DEFAULT 0,
            `goruntulenme_yetkisi` enum('herkes','vip','premium') DEFAULT 'herkes',
            `fiyat` decimal(10,2) DEFAULT 0.00,
            `ucretsiz` tinyint(1) DEFAULT 1,
            `ozellik` enum('normal','ozel','populer','yeni') DEFAULT 'normal',
            `durum` enum('aktif','pasif','beklemede') DEFAULT 'aktif',
            `ekleyen_admin` int(11),
            `ekleme_tarihi` datetime DEFAULT CURRENT_TIMESTAMP,
            `guncelleme_tarihi` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `kategori_id` (`kategori_id`),
            KEY `ekleyen_admin` (`ekleyen_admin`),
            FOREIGN KEY (`kategori_id`) REFERENCES `kategoriler` (`id`) ON DELETE SET NULL,
            FOREIGN KEY (`ekleyen_admin`) REFERENCES `adminler` (`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `slider` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `baslik` varchar(255) NOT NULL,
            `aciklama` text,
            `resim` varchar(255) NOT NULL,
            `link` varchar(255),
            `buton_metni` varchar(50),
            `siralama` int(11) DEFAULT 0,
            `durum` enum('aktif','pasif') DEFAULT 'aktif',
            `olusturma_tarihi` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `satin_almalar` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `kullanici_id` int(11) NOT NULL,
            `urun_tipi` enum('video','vip','premium') NOT NULL,
            `urun_id` int(11),
            `tutar` decimal(10,2) NOT NULL,
            `odeme_yontemi` varchar(50),
            `islem_id` varchar(100),
            `durum` enum('beklemede','onaylandi','iptal') DEFAULT 'beklemede',
            `satin_alma_tarihi` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `kullanici_id` (`kullanici_id`),
            FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `yorumlar` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `video_id` int(11) NOT NULL,
            `kullanici_id` int(11) NOT NULL,
            `yorum` text NOT NULL,
            `durum` enum('aktif','pasif','beklemede') DEFAULT 'beklemede',
            `yorum_tarihi` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `video_id` (`video_id`),
            KEY `kullanici_id` (`kullanici_id`),
            FOREIGN KEY (`video_id`) REFERENCES `videolar` (`id`) ON DELETE CASCADE,
            FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `favoriler` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `kullanici_id` int(11) NOT NULL,
            `video_id` int(11) NOT NULL,
            `ekleme_tarihi` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `kullanici_video` (`kullanici_id`,`video_id`),
            FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE,
            FOREIGN KEY (`video_id`) REFERENCES `videolar` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `izleme_gecmisi` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `kullanici_id` int(11) NOT NULL,
            `video_id` int(11) NOT NULL,
            `izleme_suresi` int(11) DEFAULT 0,
            `son_izleme` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `kullanici_video` (`kullanici_id`,`video_id`),
            FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE,
            FOREIGN KEY (`video_id`) REFERENCES `videolar` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `begeniler` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `kullanici_id` int(11) NOT NULL,
            `video_id` int(11) NOT NULL,
            `tur` enum('begendi','begenmedi') NOT NULL,
            `tarih` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `kullanici_video` (`kullanici_id`,`video_id`),
            FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE,
            FOREIGN KEY (`video_id`) REFERENCES `videolar` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        // Tabloları çalıştır
        $pdo->exec($tablolar);

        // Admin kullanıcı ekle
        $admin_sifre_hash = password_hash($admin_sifre, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO adminler (kullanici_adi, email, sifre, ad_soyad, yetki_seviyesi) VALUES (?, ?, ?, 'DOBİEN Admin', 'super_admin')");
        $stmt->execute([$admin_kullanici, $admin_email, $admin_sifre_hash]);

        // Site ayarları ekle
        $stmt = $pdo->prepare("INSERT INTO ayarlar (site_adi, site_url, site_aciklama, footer_metin) VALUES (?, ?, 'DOBİEN Video Platform - Modern Video Paylaşım Sitesi', 'DOBİEN tarafından geliştirildi. Tüm hakları saklıdır.')");
        $stmt->execute([$site_adi, $site_url]);

        // Örnek kategoriler ekle
        $kategoriler = [
            ['Film', 'film', 'Film kategorisi'],
            ['Dizi', 'dizi', 'Dizi kategorisi'],
            ['Anime', 'anime', 'Anime kategorisi'],
            ['Belgesel', 'belgesel', 'Belgesel kategorisi'],
            ['Müzik', 'muzik', 'Müzik kategorisi']
        ];

        foreach ($kategoriler as $kategori) {
            $stmt = $pdo->prepare("INSERT INTO kategoriler (kategori_adi, slug, aciklama) VALUES (?, ?, ?)");
            $stmt->execute($kategori);
        }

        // Config dosyası oluştur
        $config_content = "<?php
/**
 * DOBİEN Video Platform - Veritabanı Ayarları
 * Geliştirici: DOBİEN
 */

define('DB_HOST', '$veritabani_host');
define('DB_NAME', '$veritabani_adi');
define('DB_USER', '$veritabani_kullanici');
define('DB_PASS', '$veritabani_sifre');

define('SITE_URL', '$site_url');
define('SITE_NAME', '$site_adi');

// Güvenlik anahtarı
define('SECURITY_KEY', '" . bin2hex(random_bytes(32)) . "');

// Dosya yükleme ayarları
define('UPLOAD_PATH', 'uploads/');
define('MAX_FILE_SIZE', 500 * 1024 * 1024); // 500MB

// Geliştirici bilgisi
define('DEVELOPER', 'DOBİEN');

?>";

        file_put_contents('config/config.php', $config_content);

        $kurulum_tamamlandi = true;
        $mesaj = "Kurulum başarıyla tamamlandı! Artık sitenizi kullanabilirsiniz.";

    } catch (Exception $e) {
        $hata = "Kurulum hatası: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DOBİEN Video Platform - Kurulum</title>
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
        }

        .kurulum-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            max-width: 600px;
            width: 90%;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
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
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .basarili {
            background: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }

        .hata {
            background: #f44336;
            color: white;
            padding: 15px;
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
        }

        .gereksinimler ul {
            list-style: none;
        }

        .gereksinimler li {
            padding: 5px 0;
            color: #666;
        }

        .developer-info {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="kurulum-container">
        <div class="logo">
            <h1>DOBİEN</h1>
            <p>Video Platform Kurulum</p>
        </div>

        <?php if (isset($mesaj)): ?>
            <div class="basarili">
                <?php echo $mesaj; ?>
                <br><br>
                <a href="index.php" style="color: white; text-decoration: underline;">Ana Sayfaya Git</a> |
                <a href="admin/" style="color: white; text-decoration: underline;">Admin Paneline Git</a>
            </div>
        <?php elseif (isset($hata)): ?>
            <div class="hata">
                <?php echo $hata; ?>
            </div>
        <?php endif; ?>

        <?php if (!$kurulum_tamamlandi): ?>
            <div class="gereksinimler">
                <h3>Sistem Gereksinimleri</h3>
                <ul>
                    <li>✓ PHP 7.4 veya üzeri</li>
                    <li>✓ MySQL 5.7 veya üzeri</li>
                    <li>✓ PDO Extension</li>
                    <li>✓ GD Library</li>
                    <li>✓ Dosya yazma izinleri</li>
                </ul>
            </div>

            <form method="POST">
                <h3 style="margin-bottom: 20px; color: #333;">Veritabanı Ayarları</h3>
                
                <div class="form-group">
                    <label>Veritabanı Sunucusu:</label>
                    <input type="text" name="db_host" value="localhost" required>
                </div>

                <div class="form-group">
                    <label>Veritabanı Adı:</label>
                    <input type="text" name="db_name" value="dobien_video" required>
                </div>

                <div class="form-group">
                    <label>Veritabanı Kullanıcı Adı:</label>
                    <input type="text" name="db_user" required>
                </div>

                <div class="form-group">
                    <label>Veritabanı Şifresi:</label>
                    <input type="password" name="db_pass">
                </div>

                <h3 style="margin: 30px 0 20px 0; color: #333;">Admin Hesabı</h3>

                <div class="form-group">
                    <label>Admin Kullanıcı Adı:</label>
                    <input type="text" name="admin_user" value="admin" required>
                </div>

                <div class="form-group">
                    <label>Admin E-posta:</label>
                    <input type="email" name="admin_email" required>
                </div>

                <div class="form-group">
                    <label>Admin Şifresi:</label>
                    <input type="password" name="admin_pass" required>
                </div>

                <h3 style="margin: 30px 0 20px 0; color: #333;">Site Ayarları</h3>

                <div class="form-group">
                    <label>Site Adı:</label>
                    <input type="text" name="site_name" value="DOBİEN Video Platform" required>
                </div>

                <div class="form-group">
                    <label>Site URL:</label>
                    <input type="url" name="site_url" value="http://localhost" required>
                </div>

                <button type="submit" class="btn">Kurulumu Başlat</button>
            </form>
        <?php endif; ?>

        <div class="developer-info">
            <strong>Geliştirici: DOBİEN</strong><br>
            Modern Video Platform Scripti
        </div>
    </div>
</body>
</html>