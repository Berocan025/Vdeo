<?php
/**
 * DOBİEN Video Platform Kurulum Sihirbazı
 * Geliştirici: DOBİEN
 * Modern Video Paylaşım Platformu Kurulum Sistemi
 */

// Hata raporlamayı aç
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Kurulum tamamlandıysa yönlendir
if (file_exists('config/installed.lock')) {
    header('Location: index.php');
    exit('Kurulum zaten tamamlanmış. Ana sayfaya yönlendiriliyorsunuz...');
}

session_start();

// Kurulum adımı
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$max_step = 4;

// Form işleme
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    switch ($step) {
        case 1:
            // Adım 1: Sistem Kontrolleri
            $_SESSION['requirements_checked'] = true;
            header('Location: install.php?step=2');
            exit;
            
        case 2:
            // Adım 2: Veritabanı Ayarları
            $db_host = trim($_POST['db_host']);
            $db_name = trim($_POST['db_name']);
            $db_user = trim($_POST['db_user']);
            $db_pass = trim($_POST['db_pass']);
            
            // Veritabanı bağlantısını test et
            try {
                $pdo = new PDO("mysql:host=$db_host;charset=utf8mb4", $db_user, $db_pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
                
                // Veritabanını oluştur
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $pdo->exec("USE `$db_name`");
                
                // Session'a kaydet
                $_SESSION['db_config'] = [
                    'host' => $db_host,
                    'name' => $db_name,
                    'user' => $db_user,
                    'pass' => $db_pass
                ];
                
                header('Location: install.php?step=3');
                exit;
                
            } catch (PDOException $e) {
                $error = "Veritabanı bağlantı hatası: " . $e->getMessage();
            }
            break;
            
        case 3:
            // Adım 3: Admin Hesabı
            $admin_name = trim($_POST['admin_name']);
            $admin_surname = trim($_POST['admin_surname']);
            $admin_email = trim($_POST['admin_email']);
            $admin_password = trim($_POST['admin_password']);
            
            if (empty($admin_name) || empty($admin_email) || empty($admin_password)) {
                $error = "Tüm alanları doldurmanız gerekiyor!";
            } elseif (!filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
                $error = "Geçerli bir e-posta adresi giriniz!";
            } elseif (strlen($admin_password) < 6) {
                $error = "Şifre en az 6 karakter olmalıdır!";
            } else {
                $_SESSION['admin_config'] = [
                    'name' => $admin_name,
                    'surname' => $admin_surname,
                    'email' => $admin_email,
                    'password' => password_hash($admin_password, PASSWORD_DEFAULT)
                ];
                
                header('Location: install.php?step=4');
                exit;
            }
            break;
            
        case 4:
            // Adım 4: Kurulumu Tamamla
            if (isset($_SESSION['db_config']) && isset($_SESSION['admin_config'])) {
                try {
                    $db = $_SESSION['db_config'];
                    $admin = $_SESSION['admin_config'];
                    
                    // Veritabanına bağlan
                    $pdo = new PDO("mysql:host={$db['host']};dbname={$db['name']};charset=utf8mb4", 
                                   $db['user'], $db['pass'], [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]);
                    
                    // SQL dosyasını oku ve çalıştır
                    $sql_file = 'database.sql';
                    if (!file_exists($sql_file)) {
                        throw new Exception("database.sql dosyası bulunamadı!");
                    }
                    
                    $sql_content = file_get_contents($sql_file);
                    if ($sql_content === false) {
                        throw new Exception("database.sql dosyası okunamadı!");
                    }
                    
                    // SQL komutlarını daha güvenli bir şekilde ayır
                    $statements = [];
                    $current_statement = '';
                    $lines = explode("\n", $sql_content);
                    
                    foreach ($lines as $line) {
                        $line = trim($line);
                        
                        // Boş satırlar ve yorumları geç
                        if (empty($line) || substr($line, 0, 2) === '--' || substr($line, 0, 2) === '/*') {
                            continue;
                        }
                        
                        $current_statement .= $line . "\n";
                        
                        // Eğer satır ; ile bitiyorsa statement tamamlandı
                        if (substr($line, -1) === ';') {
                            $statements[] = trim($current_statement);
                            $current_statement = '';
                        }
                    }
                    
                    // SQL komutlarını çalıştır ve kritik hataları yakala
                    $executed_statements = 0;
                    $critical_tables = ['admin_kullanicilar', 'site_ayarlari', 'kullanicilar', 'kategoriler', 'videolar'];
                    
                    foreach ($statements as $statement) {
                        if (!empty($statement)) {
                            try {
                                $pdo->exec($statement);
                                $executed_statements++;
                            } catch (PDOException $e) {
                                // Kritik tablo oluşturma hatalarını özel olarak kontrol et
                                $is_critical = false;
                                foreach ($critical_tables as $table) {
                                    if (strpos($statement, "CREATE TABLE `$table`") !== false || 
                                        strpos($statement, "CREATE TABLE IF NOT EXISTS `$table`") !== false) {
                                        $is_critical = true;
                                        break;
                                    }
                                }
                                
                                if ($is_critical) {
                                    throw new Exception("Kritik tablo oluşturma hatası: " . $e->getMessage() . " - SQL: " . substr($statement, 0, 100) . "...");
                                }
                                
                                // Diğer hataları logla ama devam et
                                error_log("SQL Warning: " . $e->getMessage());
                            }
                        }
                    }
                    
                    if ($executed_statements === 0) {
                        throw new Exception("Hiçbir SQL komutu çalıştırılamadı!");
                    }
                    
                    // Kritik tabloların varlığını kontrol et
                    foreach ($critical_tables as $table) {
                        try {
                            $pdo->query("SELECT 1 FROM `$table` LIMIT 1");
                        } catch (PDOException $e) {
                            throw new Exception("Kritik tablo '$table' oluşturulamadı veya erişilemiyor!");
                        }
                    }
                    
                    // Admin kullanıcısını ekle - önce tablo yapısını kontrol et
                    try {
                        $admin_query = "INSERT INTO admin_kullanicilar (ad, soyad, email, sifre, yetki_seviyesi) VALUES (?, ?, ?, ?, 'super_admin')";
                        $admin_stmt = $pdo->prepare($admin_query);
                        $admin_result = $admin_stmt->execute([$admin['name'], $admin['surname'], $admin['email'], $admin['password']]);
                        
                        if (!$admin_result) {
                            throw new Exception("Admin kullanıcısı oluşturulamadı!");
                        }
                        
                        // Admin kullanıcısının oluşturulduğunu doğrula
                        $verify_admin = $pdo->prepare("SELECT id FROM admin_kullanicilar WHERE email = ?");
                        $verify_admin->execute([$admin['email']]);
                        if (!$verify_admin->fetch()) {
                            throw new Exception("Admin kullanıcısı doğrulanamadı!");
                        }
                        
                    } catch (PDOException $e) {
                        throw new Exception("Admin kullanıcısı ekleme hatası: " . $e->getMessage());
                    }
                    
                    // Config klasörünü oluştur
                    if (!file_exists('config')) {
                        mkdir('config', 0777, true);
                    }
                    
                    // Includes klasörünü oluştur
                    if (!file_exists('includes')) {
                        mkdir('includes', 0777, true);
                    }
                    
                    // Upload klasörlerini oluştur
                    $upload_dirs = [
                        'uploads',
                        'uploads/videos',
                        'uploads/thumbnails', 
                        'uploads/categories',
                        'uploads/slider',
                        'uploads/users'
                    ];
                    
                    foreach ($upload_dirs as $dir) {
                        if (!file_exists($dir)) {
                            mkdir($dir, 0777, true);
                        }
                    }
                    
                    // Config dosyasını oluştur
                    $config_content = "<?php\n";
                    $config_content .= "/**\n";
                    $config_content .= " * DOBİEN Video Platform Konfigürasyon\n";
                    $config_content .= " * Geliştirici: DOBİEN\n";
                    $config_content .= " */\n\n";
                    $config_content .= "// Veritabanı ayarları\n";
                    $config_content .= "define('DB_HOST', '{$db['host']}');\n";
                    $config_content .= "define('DB_NAME', '{$db['name']}');\n";
                    $config_content .= "define('DB_USER', '{$db['user']}');\n";
                    $config_content .= "define('DB_PASS', '{$db['pass']}');\n\n";
                    $config_content .= "// Site ayarları\n";
                    $config_content .= "define('SITE_URL', 'http://{$_SERVER['HTTP_HOST']}" . dirname($_SERVER['REQUEST_URI']) . "');\n\n";
                    $config_content .= "// Güvenlik\n";
                    $config_content .= "define('SECRET_KEY', '" . bin2hex(random_bytes(32)) . "');\n\n";
                    $config_content .= "// Kurulum tarihi\n";
                    $config_content .= "define('INSTALL_DATE', '" . date('Y-m-d H:i:s') . "');\n";
                    
                    file_put_contents('config/database.php', $config_content);
                    
                    // Ana config dosyasını oluştur
                    $main_config = file_get_contents('includes/config.php.example');
                    if ($main_config === false) {
                        // Örnek dosya yoksa oluştur
                        $main_config = "<?php\n";
                        $main_config .= "/**\n * DOBİEN Video Platform Ana Konfigürasyon\n */\n\n";
                        $main_config .= "require_once __DIR__ . '/../config/database.php';\n\n";
                        $main_config .= "// Veritabanı bağlantısı\n";
                        $main_config .= "try {\n";
                        $main_config .= "    \$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS, [\n";
                        $main_config .= "        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,\n";
                        $main_config .= "        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,\n";
                        $main_config .= "        PDO::ATTR_EMULATE_PREPARES => false\n";
                        $main_config .= "    ]);\n";
                        $main_config .= "} catch (PDOException \$e) {\n";
                        $main_config .= "    die('Veritabanı bağlantı hatası: ' . \$e->getMessage());\n";
                        $main_config .= "}\n\n";
                        $main_config .= "// Yardımcı fonksiyonlar\n";
                        $main_config .= "function siteUrl(\$path = '') {\n";
                        $main_config .= "    return SITE_URL . '/' . ltrim(\$path, '/');\n";
                        $main_config .= "}\n\n";
                        $main_config .= "function safeOutput(\$string) {\n";
                        $main_config .= "    return htmlspecialchars(\$string, ENT_QUOTES, 'UTF-8');\n";
                        $main_config .= "}\n\n";
                        $main_config .= "function formatDate(\$date, \$format = 'd.m.Y') {\n";
                        $main_config .= "    return date(\$format, strtotime(\$date));\n";
                        $main_config .= "}\n\n";
                        $main_config .= "function formatDuration(\$seconds) {\n";
                        $main_config .= "    \$hours = floor(\$seconds / 3600);\n";
                        $main_config .= "    \$minutes = floor((\$seconds % 3600) / 60);\n";
                        $main_config .= "    \$seconds = \$seconds % 60;\n";
                        $main_config .= "    if (\$hours > 0) {\n";
                        $main_config .= "        return sprintf('%d:%02d:%02d', \$hours, \$minutes, \$seconds);\n";
                        $main_config .= "    } else {\n";
                        $main_config .= "        return sprintf('%d:%02d', \$minutes, \$seconds);\n";
                        $main_config .= "    }\n";
                        $main_config .= "}\n";
                    }
                    
                    file_put_contents('includes/config.php', $main_config);
                    
                    // Kurulum tamamlandı işareti
                    file_put_contents('config/installed.lock', date('Y-m-d H:i:s'));
                    
                    // Session temizle
                    session_destroy();
                    
                    $success = "Kurulum başarıyla tamamlandı!";
                    
                } catch (Exception $e) {
                    $error = "Kurulum hatası: " . $e->getMessage();
                }
            } else {
                $error = "Eksik bilgiler. Lütfen baştan başlayın.";
            }
            break;
    }
}

// Sistem gereksinimleri kontrolü
function checkRequirements() {
    $requirements = [
        'PHP Version >= 7.4' => version_compare(PHP_VERSION, '7.4.0', '>='),
        'PDO Extension' => extension_loaded('pdo'),
        'PDO MySQL Extension' => extension_loaded('pdo_mysql'),
        'JSON Extension' => extension_loaded('json'),
        'MBString Extension' => extension_loaded('mbstring'),
        'File Upload' => ini_get('file_uploads'),
        'Config Directory Writable' => is_writable('.') || is_writable('./config'),
        'Uploads Directory Writable' => is_writable('.') || is_writable('./uploads')
    ];
    
    return $requirements;
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DOBİEN Video Platform - Kurulum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .install-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .install-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .install-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .install-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: bold;
        }
        
        .install-header .subtitle {
            margin-top: 0.5rem;
            opacity: 0.8;
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            padding: 1.5rem 0;
            background: #f8f9fa;
        }
        
        .step {
            display: flex;
            align-items: center;
            margin: 0 1rem;
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 0.5rem;
        }
        
        .step.active .step-number {
            background: #3498db;
            color: white;
        }
        
        .step.completed .step-number {
            background: #27ae60;
            color: white;
        }
        
        .step:not(.active):not(.completed) .step-number {
            background: #e9ecef;
            color: #6c757d;
        }
        
        .install-content {
            padding: 2rem;
        }
        
        .requirement-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .requirement-item:last-child {
            border-bottom: none;
        }
        
        .dobien-brand {
            color: #3498db;
            font-weight: bold;
        }
        
        .text-success { color: #27ae60 !important; }
        .text-danger { color: #e74c3c !important; }
        
        .btn-dobien {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-dobien:hover {
            background: linear-gradient(135deg, #2980b9 0%, #2471a3 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="install-card">
            <div class="install-header">
                <h1><span class="dobien-brand">DOBİEN</span> Video Platform</h1>
                <p class="subtitle">Modern Video Paylaşım Platformu Kurulum Sihirbazı</p>
            </div>
            
            <div class="step-indicator">
                <div class="step <?php echo $step >= 1 ? ($step == 1 ? 'active' : 'completed') : ''; ?>">
                    <div class="step-number">1</div>
                    <span>Gereksinimler</span>
                </div>
                <div class="step <?php echo $step >= 2 ? ($step == 2 ? 'active' : 'completed') : ''; ?>">
                    <div class="step-number">2</div>
                    <span>Veritabanı</span>
                </div>
                <div class="step <?php echo $step >= 3 ? ($step == 3 ? 'active' : 'completed') : ''; ?>">
                    <div class="step-number">3</div>
                    <span>Admin Hesabı</span>
                </div>
                <div class="step <?php echo $step >= 4 ? ($step == 4 ? 'active' : 'completed') : ''; ?>">
                    <div class="step-number">4</div>
                    <span>Tamamla</span>
                </div>
            </div>
            
            <div class="install-content">
                <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                </div>
                <?php endif; ?>
                
                <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    <div class="mt-3">
                        <a href="index.php" class="btn btn-dobien">
                            <i class="fas fa-home"></i> Ana Sayfaya Git
                        </a>
                        <a href="admin/giris.php" class="btn btn-outline-primary ms-2">
                            <i class="fas fa-user-shield"></i> Admin Girişi
                        </a>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($step == 1): ?>
                <!-- Adım 1: Sistem Kontrolleri -->
                <h3><i class="fas fa-check-circle text-primary"></i> Sistem Gereksinimleri</h3>
                <p class="text-muted">DOBİEN Video Platform'un çalışması için gerekli sistem gereksinimlerini kontrol ediyoruz.</p>
                
                <div class="mt-4">
                    <?php 
                    $requirements = checkRequirements();
                    $all_passed = true;
                    foreach ($requirements as $requirement => $status):
                        if (!$status) $all_passed = false;
                    ?>
                    <div class="requirement-item">
                        <span><?php echo $requirement; ?></span>
                        <span class="<?php echo $status ? 'text-success' : 'text-danger'; ?>">
                            <i class="fas fa-<?php echo $status ? 'check-circle' : 'times-circle'; ?>"></i>
                            <?php echo $status ? 'Geçti' : 'Başarısız'; ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="mt-4">
                    <?php if ($all_passed): ?>
                    <form method="POST">
                        <button type="submit" class="btn btn-dobien">
                            <i class="fas fa-arrow-right"></i> Devam Et
                        </button>
                    </form>
                    <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Lütfen başarısız olan gereksinimleri düzeltin ve sayfayı yenileyin.
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php elseif ($step == 2): ?>
                <!-- Adım 2: Veritabanı Ayarları -->
                <h3><i class="fas fa-database text-primary"></i> Veritabanı Ayarları</h3>
                <p class="text-muted">Veritabanı bağlantı bilgilerini girin. Veritabanı otomatik olarak oluşturulacaktır.</p>
                
                <form method="POST" class="mt-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="db_host" class="form-label">Sunucu Adresi</label>
                                <input type="text" class="form-control" id="db_host" name="db_host" value="localhost" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="db_name" class="form-label">Veritabanı Adı</label>
                                <input type="text" class="form-control" id="db_name" name="db_name" value="dobien_video" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="db_user" class="form-label">Kullanıcı Adı</label>
                                <input type="text" class="form-control" id="db_user" name="db_user" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="db_pass" class="form-label">Şifre</label>
                                <input type="password" class="form-control" id="db_pass" name="db_pass">
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="install.php?step=1" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Geri
                        </a>
                        <button type="submit" class="btn btn-dobien">
                            <i class="fas fa-database"></i> Veritabanını Test Et
                        </button>
                    </div>
                </form>
                
                <?php elseif ($step == 3): ?>
                <!-- Adım 3: Admin Hesabı -->
                <h3><i class="fas fa-user-shield text-primary"></i> Admin Hesabı Oluştur</h3>
                <p class="text-muted">Sistem yöneticisi hesabını oluşturun. Bu hesap ile admin paneline erişebileceksiniz.</p>
                
                <form method="POST" class="mt-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="admin_name" class="form-label">Ad</label>
                                <input type="text" class="form-control" id="admin_name" name="admin_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="admin_surname" class="form-label">Soyad</label>
                                <input type="text" class="form-control" id="admin_surname" name="admin_surname" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="admin_email" class="form-label">E-posta Adresi</label>
                        <input type="email" class="form-control" id="admin_email" name="admin_email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="admin_password" class="form-label">Şifre</label>
                        <input type="password" class="form-control" id="admin_password" name="admin_password" minlength="6" required>
                        <div class="form-text">Şifre en az 6 karakter olmalıdır.</div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="install.php?step=2" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Geri
                        </a>
                        <button type="submit" class="btn btn-dobien">
                            <i class="fas fa-user-plus"></i> Hesap Oluştur
                        </button>
                    </div>
                </form>
                
                <?php elseif ($step == 4): ?>
                <!-- Adım 4: Kurulumu Tamamla -->
                <h3><i class="fas fa-rocket text-primary"></i> Kurulumu Tamamla</h3>
                <p class="text-muted">Tüm ayarlar hazır! Kurulumu tamamlamak için butona tıklayın.</p>
                
                <div class="mt-4">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> Kurulum Özeti</h5>
                        <ul class="mb-0">
                            <li>Veritabanı tabloları oluşturulacak</li>
                            <li>Admin hesabı oluşturulacak</li>
                            <li>Konfigürasyon dosyaları hazırlanacak</li>
                            <li>Upload klasörleri oluşturulacak</li>
                            <li>Demo veriler eklenecek</li>
                        </ul>
                    </div>
                    
                    <form method="POST">
                        <div class="d-flex justify-content-between">
                            <a href="install.php?step=3" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Geri
                            </a>
                            <button type="submit" class="btn btn-dobien btn-lg">
                                <i class="fas fa-magic"></i> Kurulumu Tamamla
                            </button>
                        </div>
                    </form>
                </div>
                
                <?php endif; ?>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <p class="text-white">
                <i class="fas fa-code"></i> 
                <span class="dobien-brand">DOBİEN</span> tarafından geliştirilmiştir
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>