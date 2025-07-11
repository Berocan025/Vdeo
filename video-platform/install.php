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

        // Database.sql dosyasını çalıştır
        $sql_content = file_get_contents('database.sql');
        
        // SQL'i ';' karakterine göre böl ve çalıştır
        $statements = array_filter(explode(';', $sql_content));
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement) && !preg_match('/^(--|\#|\/\*)/', $statement)) {
                try {
                    $pdo->exec($statement);
                } catch (PDOException $e) {
                    // Hata olsa bile devam et
                    continue;
                }
            }
        }

        // Admin kullanıcı ekle
        $admin_sifre_hash = password_hash($admin_sifre, PASSWORD_DEFAULT);
        
        // Admin tablosuna veri eklerken database.sql'deki tablo isimlerini kullan
        try {
            $stmt = $pdo->prepare("INSERT INTO admin_kullanicilar (ad, soyad, email, sifre, yetki_seviyesi) VALUES (?, ?, ?, ?, 'super_admin')");
            $stmt->execute(['DOBİEN', 'Admin', $admin_email, $admin_sifre_hash]);
        } catch (PDOException $e) {
            // Eski tablo adını dene
            $stmt = $pdo->prepare("INSERT INTO adminler (kullanici_adi, email, sifre, ad_soyad, yetki_seviyesi) VALUES (?, ?, ?, 'DOBİEN Admin', 'super_admin')");
            $stmt->execute([$admin_kullanici, $admin_email, $admin_sifre_hash]);
        }

        // Site ayarları ekle 
        try {
            // Önce site_ayarlari tablosuna ekle (database.sql'deki format)
            $site_ayarlari = [
                ['site_baslik', $site_adi, 'Site başlığı'],
                ['site_aciklama', 'DOBİEN Video Platform - Modern Video Paylaşım Sitesi', 'Site açıklaması'],
                ['site_anahtar_kelimeler', 'video platform, premium videolar, 4k video, vip üyelik, DOBİEN', 'SEO anahtar kelimeleri'],
                ['footer_metin', 'DOBİEN tarafından geliştirildi. Tüm hakları saklıdır.', 'Footer metni'],
                ['yas_dogrulama_aktif', '1', 'Yaş doğrulama popup aktif/pasif'],
                ['yas_dogrulama_baslik', 'Yaş Doğrulama Gerekli', 'Yaş doğrulama popup başlığı'],
                ['yas_dogrulama_mesaj', 'Bu siteye erişebilmeniz için 18 yaşından büyük olmanız gerekmektedir.', 'Yaş doğrulama mesajı'],
                ['sistem_email', $admin_email, 'Sistem e-posta adresi'],
                ['max_video_boyut', '500', 'Maksimum video boyutu (MB)'],
                ['izin_verilen_formatlar', 'mp4,avi,mov,wmv', 'İzin verilen video formatları'],
                ['varsayilan_video_kalite', '720p', 'Varsayılan video kalitesi']
            ];
            
            foreach ($site_ayarlari as $ayar) {
                $stmt = $pdo->prepare("INSERT INTO site_ayarlari (anahtar, deger, aciklama) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE deger = VALUES(deger)");
                $stmt->execute($ayar);
            }
        } catch (PDOException $e) {
            // Eski tablo adını dene
            $stmt = $pdo->prepare("INSERT INTO ayarlar (site_adi, site_url, site_aciklama, footer_metin) VALUES (?, ?, 'DOBİEN Video Platform - Modern Video Paylaşım Sitesi', 'DOBİEN tarafından geliştirildi. Tüm hakları saklıdır.')");
            $stmt->execute([$site_adi, $site_url]);
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
            'uploads/avatars'
        ];
        
        foreach ($upload_dirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
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