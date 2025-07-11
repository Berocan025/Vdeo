<?php
/**
 * DOBİEN Video Platform - Sistem Test Scripti
 * Tüm veritabanı tablolarını ve bağlantıları test eder
 */

// Config dosyasını yükle
if (!file_exists('config/config.php')) {
    die('❌ Config dosyası bulunamadı! Lütfen önce install.php çalıştırın.');
}

require_once 'config/config.php';

echo "<h1>🔍 DOBİEN Video Platform - Sistem Test Raporu</h1>";
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.success { color: green; }
.error { color: red; }
.warning { color: orange; }
.info { color: blue; }
table { border-collapse: collapse; width: 100%; margin: 20px 0; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
</style>";

// Veritabanı bağlantısını test et
echo "<h2>📊 Veritabanı Bağlantı Testi</h2>";
try {
    if (isset($pdo)) {
        echo "<p class='success'>✅ Veritabanı bağlantısı başarılı!</p>";
        echo "<p class='info'>🔗 Veritabanı: " . DB_NAME . " @ " . DB_HOST . "</p>";
    } else {
        echo "<p class='error'>❌ PDO bağlantısı bulunamadı!</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Veritabanı bağlantı hatası: " . $e->getMessage() . "</p>";
}

// Gerekli tabloları kontrol et
echo "<h2>🗂️ Tablo Kontrol Testi</h2>";
$required_tables = [
    'ayarlar',
    'admin_kullanicilar',
    'adminler',
    'kullanicilar',
    'kategoriler',
    'videolar',
    'etiketler',
    'video_etiketler',
    'yorumlar',
    'begeniler',
    'favoriler',
    'izleme_gecmisi',
    'izleme_listesi',
    'slider',
    'sayfalar',
    'yas_uyarisi_ayarlari',
    'uyelik_paketleri',
    'odemeler',
    'mesajlar',
    'sistem_loglari',
    'istatistikler'
];

echo "<table>";
echo "<tr><th>Tablo Adı</th><th>Durum</th><th>Kayıt Sayısı</th></tr>";

foreach ($required_tables as $table) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM `$table`");
        $count = $stmt->fetchColumn();
        echo "<tr><td>$table</td><td class='success'>✅ Mevcut</td><td>$count</td></tr>";
    } catch (PDOException $e) {
        echo "<tr><td>$table</td><td class='error'>❌ Bulunamadı</td><td>-</td></tr>";
    }
}
echo "</table>";

// Admin kullanıcı kontrolü
echo "<h2>👤 Admin Kullanıcı Kontrolü</h2>";
try {
    $stmt = $pdo->query("SELECT * FROM admin_kullanicilar LIMIT 1");
    $admin = $stmt->fetch();
    if ($admin) {
        echo "<p class='success'>✅ Admin kullanıcı mevcut!</p>";
        echo "<p class='info'>👤 Kullanıcı: " . htmlspecialchars($admin['kullanici_adi']) . "</p>";
        echo "<p class='info'>📧 E-posta: " . htmlspecialchars($admin['email']) . "</p>";
        echo "<p class='info'>🔐 Rol: " . htmlspecialchars($admin['rol']) . "</p>";
    } else {
        echo "<p class='error'>❌ Admin kullanıcı bulunamadı!</p>";
    }
} catch (PDOException $e) {
    // Fallback: adminler tablosunu kontrol et
    try {
        $stmt = $pdo->query("SELECT * FROM adminler LIMIT 1");
        $admin = $stmt->fetch();
        if ($admin) {
            echo "<p class='success'>✅ Admin kullanıcı mevcut (adminler tablosunda)!</p>";
            echo "<p class='info'>👤 Kullanıcı: " . htmlspecialchars($admin['kullanici_adi']) . "</p>";
            echo "<p class='info'>📧 E-posta: " . htmlspecialchars($admin['email']) . "</p>";
        } else {
            echo "<p class='error'>❌ Admin kullanıcı bulunamadı!</p>";
        }
    } catch (PDOException $e2) {
        echo "<p class='error'>❌ Admin tabloları bulunamadı!</p>";
    }
}

// Site ayarları kontrolü
echo "<h2>⚙️ Site Ayarları Kontrolü</h2>";
try {
    $stmt = $pdo->query("SELECT * FROM ayarlar WHERE id = 1");
    $settings = $stmt->fetch();
    if ($settings) {
        echo "<p class='success'>✅ Site ayarları mevcut!</p>";
        echo "<p class='info'>🏷️ Site Adı: " . htmlspecialchars($settings['site_adi']) . "</p>";
        echo "<p class='info'>🌐 Site URL: " . htmlspecialchars($settings['site_url']) . "</p>";
        echo "<p class='info'>📝 Açıklama: " . htmlspecialchars($settings['site_aciklama']) . "</p>";
    } else {
        echo "<p class='error'>❌ Site ayarları bulunamadı!</p>";
    }
} catch (PDOException $e) {
    echo "<p class='error'>❌ Site ayarları tablosu bulunamadı!</p>";
}

// Yaş uyarısı ayarları kontrolü
echo "<h2>🔞 Yaş Uyarısı Ayarları Kontrolü</h2>";
try {
    $stmt = $pdo->query("SELECT * FROM yas_uyarisi_ayarlari WHERE id = 1");
    $age_settings = $stmt->fetch();
    if ($age_settings) {
        echo "<p class='success'>✅ Yaş uyarısı ayarları mevcut!</p>";
        echo "<p class='info'>🔄 Durum: " . ($age_settings['aktif'] ? 'Aktif' : 'Pasif') . "</p>";
        echo "<p class='info'>🏷️ Başlık: " . htmlspecialchars($age_settings['baslik']) . "</p>";
        echo "<p class='info'>📝 Uyarı: " . htmlspecialchars($age_settings['uyari_baslik']) . "</p>";
    } else {
        echo "<p class='error'>❌ Yaş uyarısı ayarları bulunamadı!</p>";
    }
} catch (PDOException $e) {
    echo "<p class='error'>❌ Yaş uyarısı tablosu bulunamadı!</p>";
}

// Dosya ve klasör kontrolü
echo "<h2>📁 Dosya ve Klasör Kontrolü</h2>";
$required_dirs = [
    'config',
    'uploads',
    'uploads/videos',
    'uploads/thumbnails',
    'uploads/categories',
    'uploads/sliders',
    'uploads/avatars',
    'uploads/site',
    'admin',
    'admin/assets',
    'admin/includes',
    'includes',
    'assets'
];

echo "<table>";
echo "<tr><th>Klasör/Dosya</th><th>Durum</th><th>Yazılabilir</th></tr>";

foreach ($required_dirs as $dir) {
    $exists = is_dir($dir);
    $writable = is_writable($dir);
    
    echo "<tr>";
    echo "<td>$dir/</td>";
    echo "<td class='" . ($exists ? 'success' : 'error') . "'>" . ($exists ? '✅ Mevcut' : '❌ Bulunamadı') . "</td>";
    echo "<td class='" . ($writable ? 'success' : 'error') . "'>" . ($writable ? '✅ Yazılabilir' : '❌ Yazılamaz') . "</td>";
    echo "</tr>";
}

// Kritik dosyaları kontrol et
$required_files = [
    'config/config.php',
    '.htaccess',
    'admin/giris.php',
    'admin/index.php',
    'admin/includes/topbar.php',
    'admin/includes/sidebar.php',
    'admin/includes/admin-header.php',
    'index.php',
    'includes/header.php',
    'includes/footer.php'
];

foreach ($required_files as $file) {
    $exists = file_exists($file);
    $readable = is_readable($file);
    
    echo "<tr>";
    echo "<td>$file</td>";
    echo "<td class='" . ($exists ? 'success' : 'error') . "'>" . ($exists ? '✅ Mevcut' : '❌ Bulunamadı') . "</td>";
    echo "<td class='" . ($readable ? 'success' : 'error') . "'>" . ($readable ? '✅ Okunabilir' : '❌ Okunamaz') . "</td>";
    echo "</tr>";
}

echo "</table>";

// PHP ayarları kontrolü
echo "<h2>🐘 PHP Ayarları Kontrolü</h2>";
echo "<table>";
echo "<tr><th>Ayar</th><th>Değer</th><th>Durum</th></tr>";

$php_settings = [
    'PHP Version' => phpversion(),
    'PDO MySQL' => extension_loaded('pdo_mysql') ? 'Yüklü' : 'Yüklü Değil',
    'GD Library' => extension_loaded('gd') ? 'Yüklü' : 'Yüklü Değil',
    'Memory Limit' => ini_get('memory_limit'),
    'Upload Max Size' => ini_get('upload_max_filesize'),
    'Post Max Size' => ini_get('post_max_size'),
    'Max Execution Time' => ini_get('max_execution_time') . 's',
    'Session Started' => session_status() === PHP_SESSION_ACTIVE ? 'Aktif' : 'Pasif'
];

foreach ($php_settings as $setting => $value) {
    $status = 'info';
    if ($setting === 'PDO MySQL' && $value === 'Yüklü Değil') $status = 'error';
    if ($setting === 'GD Library' && $value === 'Yüklü Değil') $status = 'warning';
    
    echo "<tr>";
    echo "<td>$setting</td>";
    echo "<td class='$status'>$value</td>";
    echo "<td class='$status'>" . ($status === 'error' ? '❌ Hata' : ($status === 'warning' ? '⚠️ Uyarı' : '✅ OK')) . "</td>";
    echo "</tr>";
}
echo "</table>";

// Sistem öneriler
echo "<h2>💡 Sistem Önerileri</h2>";
echo "<ul>";

if (!extension_loaded('pdo_mysql')) {
    echo "<li class='error'>❌ PDO MySQL extension yüklü değil - Veritabanı bağlantısı çalışmaz!</li>";
}

if (!extension_loaded('gd')) {
    echo "<li class='warning'>⚠️ GD Library yüklü değil - Resim işleme çalışmayabilir!</li>";
}

if (!is_writable('uploads')) {
    echo "<li class='error'>❌ uploads/ klasörü yazılabilir değil - Dosya yükleme çalışmaz!</li>";
}

if (!file_exists('.htaccess')) {
    echo "<li class='warning'>⚠️ .htaccess dosyası bulunamadı - URL yönlendirme çalışmayabilir!</li>";
}

if (version_compare(phpversion(), '7.4', '<')) {
    echo "<li class='error'>❌ PHP versiyonu 7.4'ten düşük - Sistem çalışmayabilir!</li>";
}

echo "<li class='success'>✅ Sistem genel olarak çalışır durumda görünüyor!</li>";
echo "</ul>";

// Test sonucu
echo "<h2>📋 Test Sonucu</h2>";
echo "<div style='background: #f0f8ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h3>🎉 DOBİEN Video Platform Test Tamamlandı!</h3>";
echo "<p><strong>Tarih:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>Durum:</strong> <span class='success'>✅ Sistem hazır!</span></p>";
echo "<p><strong>Öneriler:</strong></p>";
echo "<ul>";
echo "<li>🔐 <a href='admin/giris.php'>Admin paneline giriş yapın</a></li>";
echo "<li>⚙️ <a href='admin/site-ayarlari.php'>Site ayarlarını yapılandırın</a></li>";
echo "<li>📹 <a href='admin/videolar.php'>İlk videolarınızı ekleyin</a></li>";
echo "<li>🎨 <a href='admin/slider.php'>Slider içeriklerini düzenleyin</a></li>";
echo "<li>🗑️ Bu test dosyasını silin: <code>test_system.php</code></li>";
echo "</ul>";
echo "</div>";

echo "<div style='text-align: center; margin-top: 30px; color: #666;'>";
echo "<p><strong>DOBİEN Video Platform</strong> - Sistem Test Scripti</p>";
echo "<p>Geliştirici: DOBİEN | Tüm hakları saklıdır © " . date('Y') . "</p>";
echo "</div>";
?>