<?php
/**
 * DOBİEN Video Platform - Site Ayarları
 * Geliştirici: DOBİEN
 * HER ŞEY BU SAYFADAN YÖNETİLEBİLİR:
 * - Site başlığı, açıklama, keywords
 * - Logo, favicon, tüm resimler  
 * - Yaş uyarısı popup ayarları
 * - Footer yazıları, sosyal medya
 * - Analytics kodları
 * - Bütün metinler ve yazılar
 * Tüm Hakları Saklıdır © DOBİEN
 */

require_once '../includes/config.php';

// Admin giriş kontrolü
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: giris.php');
    exit;
}

// Form gönderildi mi?
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Site Genel Ayarları
        if (isset($_POST['genel_ayarlar'])) {
            $site_adi = $_POST['site_adi'];
            $site_url = $_POST['site_url'];
            $site_aciklama = $_POST['site_aciklama'];
            $meta_anahtar = $_POST['meta_anahtar'];
            $email = $_POST['email'];
            $telefon = $_POST['telefon'];
            $adres = $_POST['adres'];
            
            $stmt = $pdo->prepare("UPDATE ayarlar SET site_adi = ?, site_url = ?, site_aciklama = ?, meta_anahtar = ?, email = ?, telefon = ?, adres = ? WHERE id = 1");
            $stmt->execute([$site_adi, $site_url, $site_aciklama, $meta_anahtar, $email, $telefon, $adres]);
            
            $_SESSION['success_message'] = 'Genel ayarlar başarıyla güncellendi!';
        }
        
        // Footer Ayarları
        if (isset($_POST['footer_ayarlar'])) {
            $footer_metin = $_POST['footer_metin'];
            $sosyal_facebook = $_POST['sosyal_facebook'];
            $sosyal_twitter = $_POST['sosyal_twitter'];
            $sosyal_instagram = $_POST['sosyal_instagram'];
            $sosyal_youtube = $_POST['sosyal_youtube'];
            
            $stmt = $pdo->prepare("UPDATE ayarlar SET footer_metin = ?, sosyal_facebook = ?, sosyal_twitter = ?, sosyal_instagram = ?, sosyal_youtube = ? WHERE id = 1");
            $stmt->execute([$footer_metin, $sosyal_facebook, $sosyal_twitter, $sosyal_instagram, $sosyal_youtube]);
            
            $_SESSION['success_message'] = 'Footer ayarları başarıyla güncellendi!';
        }
        
        // Logo & Favicon Yükleme
        if (isset($_POST['logo_favicon'])) {
            $upload_dir = '../uploads/site/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Logo yükleme
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
                $logo_name = 'logo_' . time() . '.' . pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_dir . $logo_name)) {
                    $stmt = $pdo->prepare("UPDATE ayarlar SET logo = ? WHERE id = 1");
                    $stmt->execute([$logo_name]);
                }
            }
            
            // Favicon yükleme
            if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] == 0) {
                $favicon_name = 'favicon_' . time() . '.' . pathinfo($_FILES['favicon']['name'], PATHINFO_EXTENSION);
                if (move_uploaded_file($_FILES['favicon']['tmp_name'], $upload_dir . $favicon_name)) {
                    $stmt = $pdo->prepare("UPDATE ayarlar SET favicon = ? WHERE id = 1");
                    $stmt->execute([$favicon_name]);
                }
            }
            
            $_SESSION['success_message'] = 'Logo ve favicon başarıyla güncellendi!';
        }
        
        // Yaş Uyarısı Ayarları
        if (isset($_POST['yas_uyarisi'])) {
            // Yaş uyarısı ayarları için yeni tablo oluşturalım
            $pdo->exec("CREATE TABLE IF NOT EXISTS yas_uyarisi_ayarlari (
                id INT PRIMARY KEY AUTO_INCREMENT,
                aktif TINYINT(1) DEFAULT 1,
                baslik VARCHAR(255) DEFAULT 'DOBİEN',
                alt_baslik VARCHAR(255) DEFAULT 'Video Platform',
                uyari_baslik VARCHAR(255) DEFAULT 'Yaş Doğrulama Gerekli',
                uyari_metni TEXT,
                onay_butonu VARCHAR(100) DEFAULT '18 yaşından büyüğüm',
                red_butonu VARCHAR(100) DEFAULT '18 yaşında değilim',
                red_mesaji TEXT,
                gelistirici_notu TEXT
            )");
            
            // Mevcut kaydı kontrol et
            $existing = $pdo->query("SELECT COUNT(*) FROM yas_uyarisi_ayarlari")->fetchColumn();
            
            if ($existing == 0) {
                // İlk kez ekle
                $stmt = $pdo->prepare("INSERT INTO yas_uyarisi_ayarlari (aktif, baslik, alt_baslik, uyari_baslik, uyari_metni, onay_butonu, red_butonu, red_mesaji, gelistirici_notu) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            } else {
                // Güncelle
                $stmt = $pdo->prepare("UPDATE yas_uyarisi_ayarlari SET aktif = ?, baslik = ?, alt_baslik = ?, uyari_baslik = ?, uyari_metni = ?, onay_butonu = ?, red_butonu = ?, red_mesaji = ?, gelistirici_notu = ? WHERE id = 1");
            }
            
            $stmt->execute([
                isset($_POST['yas_uyarisi_aktif']) ? 1 : 0,
                $_POST['yas_baslik'],
                $_POST['yas_alt_baslik'],
                $_POST['yas_uyari_baslik'],
                $_POST['yas_uyari_metni'],
                $_POST['yas_onay_butonu'],
                $_POST['yas_red_butonu'],
                $_POST['yas_red_mesaji'],
                $_POST['yas_gelistirici_notu']
            ]);
            
            $_SESSION['success_message'] = 'Yaş uyarısı ayarları başarıyla güncellendi!';
        }
        
        // Analytics ve Diğer Kodlar
        if (isset($_POST['analytics_kodlar'])) {
            $analytics_kod = $_POST['analytics_kod'];
            $kayit_durumu = isset($_POST['kayit_durumu']) ? 1 : 0;
            $email_dogrulama = isset($_POST['email_dogrulama']) ? 1 : 0;
            
            $stmt = $pdo->prepare("UPDATE ayarlar SET analytics_kod = ?, kayit_durumu = ?, email_dogrulama = ? WHERE id = 1");
            $stmt->execute([$analytics_kod, $kayit_durumu, $email_dogrulama]);
            
            $_SESSION['success_message'] = 'Analytics ve sistem ayarları başarıyla güncellendi!';
        }
        
        header('Location: site-ayarlari.php');
        exit;
        
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Hata: ' . $e->getMessage();
    }
}

// Mevcut ayarları çek - site_ayarlari tablosundan
try {
    $stmt = $pdo->query("SELECT anahtar, deger FROM site_ayarlari");
    $settings_rows = $stmt->fetchAll();
    
    $ayarlar = [];
    foreach ($settings_rows as $row) {
        $ayarlar[$row['anahtar']] = $row['deger'];
    }
    
    // Eksik ayarları varsayılan değerlerle doldur
    $default_settings = [
        'site_baslik' => 'DOBİEN Video Platform',
        'site_aciklama' => 'Modern Video Paylaşım Platformu',
        'site_anahtar_kelimeler' => 'video, platform, DOBİEN',
        'footer_metin' => 'DOBİEN tarafından geliştirildi. Tüm hakları saklıdır.',
        'sistem_email' => 'info@dobien.com',
        'yas_dogrulama_aktif' => '1'
    ];
    
    foreach ($default_settings as $key => $value) {
        if (!isset($ayarlar[$key])) {
            $ayarlar[$key] = $value;
            // Veritabanına da ekle
            try {
                $stmt = $pdo->prepare("INSERT INTO site_ayarlari (anahtar, deger, aciklama) VALUES (?, ?, ?)");
                $stmt->execute([$key, $value, ucfirst(str_replace('_', ' ', $key))]);
            } catch (PDOException $e) {
                // Duplicate key hatası görmezden gel
            }
        }
    }
    
    // Eski format için uyumluluk
    $ayarlar['site_adi'] = $ayarlar['site_baslik'] ?? 'DOBİEN Video Platform';
    $ayarlar['site_url'] = $_SERVER['HTTP_HOST'] ? 'http://' . $_SERVER['HTTP_HOST'] : 'http://localhost';
    $ayarlar['site_aciklama'] = $ayarlar['site_aciklama'] ?? 'Modern Video Paylaşım Platformu';
    $ayarlar['meta_anahtar'] = $ayarlar['site_anahtar_kelimeler'] ?? '';
    $ayarlar['email'] = $ayarlar['sistem_email'] ?? '';
    $ayarlar['telefon'] = $ayarlar['telefon'] ?? '';
    $ayarlar['adres'] = $ayarlar['adres'] ?? '';
    $ayarlar['sosyal_facebook'] = $ayarlar['sosyal_facebook'] ?? '';
    $ayarlar['sosyal_twitter'] = $ayarlar['sosyal_twitter'] ?? '';
    $ayarlar['sosyal_instagram'] = $ayarlar['sosyal_instagram'] ?? '';
    $ayarlar['sosyal_youtube'] = $ayarlar['sosyal_youtube'] ?? '';
    $ayarlar['analytics_kod'] = $ayarlar['google_analytics'] ?? '';
    $ayarlar['kayit_durumu'] = $ayarlar['kayit_durumu'] ?? '1';
    $ayarlar['email_dogrulama'] = $ayarlar['email_dogrulama'] ?? '0';
    $ayarlar['logo'] = $ayarlar['site_logo'] ?? '';
    $ayarlar['favicon'] = $ayarlar['site_favicon'] ?? '';
    
} catch (PDOException $e) {
    // Tablo yoksa varsayılan değerler kullan
    $ayarlar = [
        'site_adi' => 'DOBİEN Video Platform',
        'site_url' => 'http://localhost',
        'site_aciklama' => 'Modern Video Paylaşım Platformu',
        'meta_anahtar' => 'video, platform, DOBİEN',
        'footer_metin' => 'DOBİEN tarafından geliştirildi. Tüm hakları saklıdır.',
        'email' => '',
        'telefon' => '',
        'adres' => '',
        'sosyal_facebook' => '',
        'sosyal_twitter' => '',
        'sosyal_instagram' => '',
        'sosyal_youtube' => '',
        'analytics_kod' => '',
        'kayit_durumu' => '1',
        'email_dogrulama' => '0',
        'logo' => '',
        'favicon' => ''
    ];
}

// Yaş uyarısı ayarlarını site_ayarlari tablosundan çek
$yas_uyarisi = [
    'aktif' => $ayarlar['yas_dogrulama_aktif'] ?? '1',
    'baslik' => $ayarlar['yas_dogrulama_site_baslik'] ?? 'DOBİEN',
    'alt_baslik' => $ayarlar['yas_dogrulama_site_alt_baslik'] ?? 'Video Platform',
    'uyari_baslik' => $ayarlar['yas_dogrulama_baslik'] ?? 'Yaş Doğrulama Gerekli',
    'uyari_metni' => $ayarlar['yas_dogrulama_mesaj'] ?? 'Bu siteye erişebilmeniz için 18 yaşından büyük olmanız gerekmektedir. Sitemiz yetişkin içerikler barındırmaktadır ve yalnızca reşit kullanıcılar için uygundur.',
    'onay_butonu' => $ayarlar['yas_dogrulama_onay_butonu'] ?? '18 yaşından büyüğüm',
    'red_butonu' => $ayarlar['yas_dogrulama_red_butonu'] ?? '18 yaşında değilim',
    'red_mesaji' => $ayarlar['yas_dogrulama_red_mesaji'] ?? 'Üzgünüz, sitemiz sizin için uygun değildir. 18 yaş altındaki kullanıcılar siteye erişemez.',
    'gelistirici_notu' => $ayarlar['yas_dogrulama_gelistirici_notu'] ?? 'Bu sistem DOBİEN tarafından geliştirilmiştir ve kullanıcı güvenliği için tasarlanmıştır.'
];

$page_title = "Site Ayarları";
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - DOBİEN Admin Panel</title>
    
    <link rel="stylesheet" href="assets/css/admin.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="admin-body">

<div class="admin-wrapper">
    
    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>
    
    <!-- Main Content -->
    <main class="admin-main">
        
        <!-- Top Bar -->
        <?php include 'includes/topbar.php'; ?>
        
        <!-- Content -->
        <div class="admin-content">
            
            <!-- Page Header -->
            <div class="page-header">
                <div class="page-header-content">
                    <h1 class="page-title">
                        <i class="fas fa-cog"></i>
                        Site Ayarları
                    </h1>
                    <p class="page-description">
                        Sitenizin her yönünü buradan kontrol edebilirsiniz. Logo, yazılar, popup'lar, sosyal medya - her şey!
                    </p>
                </div>
                <div class="page-actions">
                    <button class="btn btn-primary" onclick="window.open('../index.php', '_blank')">
                        <i class="fas fa-external-link-alt"></i>
                        Siteyi Görüntüle
                    </button>
                </div>
            </div>
            
            <!-- Messages -->
            <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            </div>
            <?php endif; ?>
            
            <!-- Settings Tabs -->
            <div class="settings-container">
                
                <!-- Tab Navigation -->
                <div class="settings-tabs">
                    <button class="tab-btn active" data-tab="genel">
                        <i class="fas fa-globe"></i>
                        Genel Ayarlar
                    </button>
                    <button class="tab-btn" data-tab="logo-favicon">
                        <i class="fas fa-image"></i>
                        Logo & Favicon
                    </button>
                    <button class="tab-btn" data-tab="yas-uyarisi">
                        <i class="fas fa-exclamation-triangle"></i>
                        Yaş Uyarısı
                    </button>
                    <button class="tab-btn" data-tab="footer">
                        <i class="fas fa-link"></i>
                        Footer & Sosyal Medya
                    </button>
                    <button class="tab-btn" data-tab="analytics">
                        <i class="fas fa-chart-line"></i>
                        Analytics & Sistem
                    </button>
                </div>
                
                <!-- Genel Ayarlar -->
                <div class="tab-content active" id="genel">
                    <div class="settings-card">
                        <div class="card-header">
                            <h3><i class="fas fa-globe"></i> Site Genel Ayarları</h3>
                            <p>Site başlığı, açıklama, iletişim bilgileri ve meta etiketleri</p>
                        </div>
                        <div class="card-content">
                            <form method="POST" class="settings-form">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="site_adi">Site Adı</label>
                                        <input type="text" id="site_adi" name="site_adi" value="<?php echo safeOutput($ayarlar['site_adi']); ?>" required>
                                        <small>Sitenizin ana başlığı (browser başlığı ve logo yanında görünür)</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="site_url">Site URL</label>
                                        <input type="url" id="site_url" name="site_url" value="<?php echo safeOutput($ayarlar['site_url']); ?>" required>
                                        <small>Sitenizin ana domain adresi (https://example.com)</small>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="site_aciklama">Site Açıklaması</label>
                                    <textarea id="site_aciklama" name="site_aciklama" rows="3" required><?php echo safeOutput($ayarlar['site_aciklama']); ?></textarea>
                                    <small>SEO için site açıklaması (meta description)</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="meta_anahtar">Meta Anahtar Kelimeler</label>
                                    <textarea id="meta_anahtar" name="meta_anahtar" rows="2"><?php echo safeOutput($ayarlar['meta_anahtar']); ?></textarea>
                                    <small>SEO için anahtar kelimeler (virgülle ayırarak yazın)</small>
                                </div>
                                
                                <div class="form-divider">
                                    <h4><i class="fas fa-phone"></i> İletişim Bilgileri</h4>
                                </div>
                                
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="email">E-posta Adresi</label>
                                        <input type="email" id="email" name="email" value="<?php echo safeOutput($ayarlar['email']); ?>">
                                        <small>İletişim için ana e-posta adresi</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="telefon">Telefon</label>
                                        <input type="tel" id="telefon" name="telefon" value="<?php echo safeOutput($ayarlar['telefon']); ?>">
                                        <small>İletişim telefon numarası</small>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="adres">Adres</label>
                                    <textarea id="adres" name="adres" rows="2"><?php echo safeOutput($ayarlar['adres']); ?></textarea>
                                    <small>Fiziksel adres bilgisi</small>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" name="genel_ayarlar" class="btn btn-primary">
                                        <i class="fas fa-save"></i>
                                        Genel Ayarları Kaydet
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Logo & Favicon -->
                <div class="tab-content" id="logo-favicon">
                    <div class="settings-card">
                        <div class="card-header">
                            <h3><i class="fas fa-image"></i> Logo & Favicon Yönetimi</h3>
                            <p>Site logosu ve favicon'unuzu yükleyin ve yönetin</p>
                        </div>
                        <div class="card-content">
                            <form method="POST" enctype="multipart/form-data" class="settings-form">
                                
                                <div class="media-grid">
                                    <!-- Logo -->
                                    <div class="media-item">
                                        <div class="media-preview">
                                            <img src="<?php echo $ayarlar['logo'] ? '../uploads/site/' . $ayarlar['logo'] : '../assets/images/default-logo.png'; ?>" 
                                                 alt="Site Logo" id="logo-preview">
                                        </div>
                                        <div class="media-info">
                                            <h4>Site Logosu</h4>
                                            <p>Header'da görünecek ana logo</p>
                                            <small>Önerilen: 200x60 px, PNG/JPG</small>
                                            
                                            <div class="file-input-wrapper">
                                                <input type="file" id="logo" name="logo" accept="image/*" onchange="previewImage(this, 'logo-preview')">
                                                <label for="logo" class="file-input-label">
                                                    <i class="fas fa-upload"></i>
                                                    Logo Seç
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Favicon -->
                                    <div class="media-item">
                                        <div class="media-preview">
                                            <img src="<?php echo $ayarlar['favicon'] ? '../uploads/site/' . $ayarlar['favicon'] : '../assets/images/default-favicon.png'; ?>" 
                                                 alt="Favicon" id="favicon-preview">
                                        </div>
                                        <div class="media-info">
                                            <h4>Favicon</h4>
                                            <p>Browser sekmesinde görünecek küçük ikon</p>
                                            <small>Önerilen: 32x32 px, ICO/PNG</small>
                                            
                                            <div class="file-input-wrapper">
                                                <input type="file" id="favicon" name="favicon" accept="image/*" onchange="previewImage(this, 'favicon-preview')">
                                                <label for="favicon" class="file-input-label">
                                                    <i class="fas fa-upload"></i>
                                                    Favicon Seç
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" name="logo_favicon" class="btn btn-primary">
                                        <i class="fas fa-save"></i>
                                        Logo & Favicon'u Kaydet
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Yaş Uyarısı Ayarları -->
                <div class="tab-content" id="yas-uyarisi">
                    <div class="settings-card">
                        <div class="card-header">
                            <h3><i class="fas fa-exclamation-triangle"></i> 18+ Yaş Uyarısı Popup Ayarları</h3>
                            <p>Site ilk açıldığında görünen yaş doğrulama popup'ının tüm ayarları</p>
                        </div>
                        <div class="card-content">
                            <form method="POST" class="settings-form">
                                
                                <div class="form-group">
                                    <label class="switch-label">
                                        <input type="checkbox" name="yas_uyarisi_aktif" <?php echo $yas_uyarisi['aktif'] ? 'checked' : ''; ?>>
                                        <span class="switch-slider"></span>
                                        Yaş Uyarısı Popup'ı Aktif
                                    </label>
                                    <small>Bu popup site ilk açıldığında kullanıcılara gösterilir</small>
                                </div>
                                
                                <div class="form-divider">
                                    <h4><i class="fas fa-heading"></i> Popup Başlık Ayarları</h4>
                                </div>
                                
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="yas_baslik">Ana Başlık</label>
                                        <input type="text" id="yas_baslik" name="yas_baslik" value="<?php echo safeOutput($yas_uyarisi['baslik']); ?>">
                                        <small>Popup'ın en üstündeki büyük başlık</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="yas_alt_baslik">Alt Başlık</label>
                                        <input type="text" id="yas_alt_baslik" name="yas_alt_baslik" value="<?php echo safeOutput($yas_uyarisi['alt_baslik']); ?>">
                                        <small>Ana başlığın altındaki açıklama</small>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="yas_uyari_baslik">Uyarı Başlığı</label>
                                    <input type="text" id="yas_uyari_baslik" name="yas_uyari_baslik" value="<?php echo safeOutput($yas_uyarisi['uyari_baslik']); ?>">
                                    <small>Uyarı kutusunun başlığı</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="yas_uyari_metni">Uyarı Metni</label>
                                    <textarea id="yas_uyari_metni" name="yas_uyari_metni" rows="4"><?php echo safeOutput($yas_uyarisi['uyari_metni']); ?></textarea>
                                    <small>Kullanıcılara gösterilecek uyarı mesajı</small>
                                </div>
                                
                                <div class="form-divider">
                                    <h4><i class="fas fa-mouse-pointer"></i> Buton Ayarları</h4>
                                </div>
                                
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="yas_onay_butonu">Onay Butonu Metni</label>
                                        <input type="text" id="yas_onay_butonu" name="yas_onay_butonu" value="<?php echo safeOutput($yas_uyarisi['onay_butonu']); ?>">
                                        <small>18+ onay butonu üzerindeki yazı</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="yas_red_butonu">Red Butonu Metni</label>
                                        <input type="text" id="yas_red_butonu" name="yas_red_butonu" value="<?php echo safeOutput($yas_uyarisi['red_butonu']); ?>">
                                        <small>Ret butonu üzerindeki yazı</small>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="yas_red_mesaji">Red Mesajı</label>
                                    <textarea id="yas_red_mesaji" name="yas_red_mesaji" rows="2"><?php echo safeOutput($yas_uyarisi['red_mesaji']); ?></textarea>
                                    <small>18 yaş altı kullanıcılara gösterilecek mesaj</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="yas_gelistirici_notu">Geliştirici Notu</label>
                                    <textarea id="yas_gelistirici_notu" name="yas_gelistirici_notu" rows="2"><?php echo safeOutput($yas_uyarisi['gelistirici_notu']); ?></textarea>
                                    <small>Popup'ın altında görünecek geliştirici notu</small>
                                </div>
                                
                                <div class="popup-preview">
                                    <h4><i class="fas fa-eye"></i> Önizleme</h4>
                                    <div class="preview-popup">
                                        <div class="preview-header">
                                            <h3 id="preview-baslik"><?php echo safeOutput($yas_uyarisi['baslik']); ?></h3>
                                            <p id="preview-alt-baslik"><?php echo safeOutput($yas_uyarisi['alt_baslik']); ?></p>
                                        </div>
                                        <div class="preview-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <h4 id="preview-uyari-baslik"><?php echo safeOutput($yas_uyarisi['uyari_baslik']); ?></h4>
                                            <p id="preview-uyari-metni"><?php echo safeOutput($yas_uyarisi['uyari_metni']); ?></p>
                                        </div>
                                        <div class="preview-buttons">
                                            <button class="preview-btn preview-btn-confirm" id="preview-onay">
                                                <?php echo safeOutput($yas_uyarisi['onay_butonu']); ?>
                                            </button>
                                            <button class="preview-btn preview-btn-deny" id="preview-red">
                                                <?php echo safeOutput($yas_uyarisi['red_butonu']); ?>
                                            </button>
                                        </div>
                                        <div class="preview-note">
                                            <small id="preview-gelistirici"><?php echo safeOutput($yas_uyarisi['gelistirici_notu']); ?></small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" name="yas_uyarisi" class="btn btn-primary">
                                        <i class="fas fa-save"></i>
                                        Yaş Uyarısı Ayarlarını Kaydet
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Footer & Sosyal Medya -->
                <div class="tab-content" id="footer">
                    <div class="settings-card">
                        <div class="card-header">
                            <h3><i class="fas fa-link"></i> Footer & Sosyal Medya Ayarları</h3>
                            <p>Footer yazıları ve sosyal medya linklerini yönetin</p>
                        </div>
                        <div class="card-content">
                            <form method="POST" class="settings-form">
                                
                                <div class="form-group">
                                    <label for="footer_metin">Footer Metni</label>
                                    <textarea id="footer_metin" name="footer_metin" rows="3"><?php echo safeOutput($ayarlar['footer_metin']); ?></textarea>
                                    <small>Sitenin altında görünecek telif hakkı ve geliştirici bilgisi</small>
                                </div>
                                
                                <div class="form-divider">
                                    <h4><i class="fas fa-share-alt"></i> Sosyal Medya Linkleri</h4>
                                </div>
                                
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="sosyal_facebook">
                                            <i class="fab fa-facebook"></i> Facebook
                                        </label>
                                        <input type="url" id="sosyal_facebook" name="sosyal_facebook" value="<?php echo safeOutput($ayarlar['sosyal_facebook']); ?>" placeholder="https://facebook.com/sayfa">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="sosyal_twitter">
                                            <i class="fab fa-twitter"></i> Twitter
                                        </label>
                                        <input type="url" id="sosyal_twitter" name="sosyal_twitter" value="<?php echo safeOutput($ayarlar['sosyal_twitter']); ?>" placeholder="https://twitter.com/kullanici">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="sosyal_instagram">
                                            <i class="fab fa-instagram"></i> Instagram
                                        </label>
                                        <input type="url" id="sosyal_instagram" name="sosyal_instagram" value="<?php echo safeOutput($ayarlar['sosyal_instagram']); ?>" placeholder="https://instagram.com/kullanici">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="sosyal_youtube">
                                            <i class="fab fa-youtube"></i> YouTube
                                        </label>
                                        <input type="url" id="sosyal_youtube" name="sosyal_youtube" value="<?php echo safeOutput($ayarlar['sosyal_youtube']); ?>" placeholder="https://youtube.com/kanal">
                                    </div>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" name="footer_ayarlar" class="btn btn-primary">
                                        <i class="fas fa-save"></i>
                                        Footer Ayarlarını Kaydet
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Analytics & Sistem -->
                <div class="tab-content" id="analytics">
                    <div class="settings-card">
                        <div class="card-header">
                            <h3><i class="fas fa-chart-line"></i> Analytics & Sistem Ayarları</h3>
                            <p>Google Analytics, sistem ayarları ve güvenlik seçenekleri</p>
                        </div>
                        <div class="card-content">
                            <form method="POST" class="settings-form">
                                
                                <div class="form-group">
                                    <label for="analytics_kod">Google Analytics Kodu</label>
                                    <textarea id="analytics_kod" name="analytics_kod" rows="6" placeholder="<!-- Google Analytics kodu buraya -->"><?php echo safeOutput($ayarlar['analytics_kod']); ?></textarea>
                                    <small>Google Analytics veya diğer takip kodlarınızı buraya ekleyin</small>
                                </div>
                                
                                <div class="form-divider">
                                    <h4><i class="fas fa-shield-alt"></i> Sistem Ayarları</h4>
                                </div>
                                
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label class="switch-label">
                                            <input type="checkbox" name="kayit_durumu" <?php echo $ayarlar['kayit_durumu'] ? 'checked' : ''; ?>>
                                            <span class="switch-slider"></span>
                                            Yeni Üye Kayıtları Açık
                                        </label>
                                        <small>Kullanıcıların yeni hesap açabilmesini kontrol eder</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="switch-label">
                                            <input type="checkbox" name="email_dogrulama" <?php echo $ayarlar['email_dogrulama'] ? 'checked' : ''; ?>>
                                            <span class="switch-slider"></span>
                                            E-posta Doğrulama Zorunlu
                                        </label>
                                        <small>Yeni üyelerin e-postalarını doğrulamasını zorunlu kılar</small>
                                    </div>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" name="analytics_kodlar" class="btn btn-primary">
                                        <i class="fas fa-save"></i>
                                        Analytics & Sistem Ayarlarını Kaydet
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        
    </main>
    
</div>

<!-- DOBİEN Developer Signature -->
<div class="admin-signature">
    <i class="fas fa-cog"></i>
    <span><strong>DOBİEN</strong> Site Ayarları</span>
</div>

<script>
/**
 * DOBİEN Video Platform - Site Ayarları JavaScript
 * Geliştirici: DOBİEN
 */

// Tab işlevselliği
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        // Aktif tab'ı kaldır
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        
        // Yeni tab'ı aktif et
        btn.classList.add('active');
        const tabId = btn.getAttribute('data-tab');
        document.getElementById(tabId).classList.add('active');
    });
});

// Image preview functionality
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(previewId).src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Yaş uyarısı önizleme güncelleme
function updatePreview() {
    document.getElementById('preview-baslik').textContent = document.getElementById('yas_baslik').value;
    document.getElementById('preview-alt-baslik').textContent = document.getElementById('yas_alt_baslik').value;
    document.getElementById('preview-uyari-baslik').textContent = document.getElementById('yas_uyari_baslik').value;
    document.getElementById('preview-uyari-metni').textContent = document.getElementById('yas_uyari_metni').value;
    document.getElementById('preview-onay').textContent = document.getElementById('yas_onay_butonu').value;
    document.getElementById('preview-red').textContent = document.getElementById('yas_red_butonu').value;
    document.getElementById('preview-gelistirici').textContent = document.getElementById('yas_gelistirici_notu').value;
}

// Yaş uyarısı form alanlarına event listener ekle
['yas_baslik', 'yas_alt_baslik', 'yas_uyari_baslik', 'yas_uyari_metni', 'yas_onay_butonu', 'yas_red_butonu', 'yas_gelistirici_notu'].forEach(id => {
    const element = document.getElementById(id);
    if (element) {
        element.addEventListener('input', updatePreview);
    }
});

console.log('DOBİEN Site Ayarları yüklendi!');
</script>

<style>
/* Site Ayarları Özel CSS */
.settings-container {
    background: var(--bg-card);
    border-radius: var(--radius-2xl);
    overflow: hidden;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--border-color);
}

.settings-tabs {
    display: flex;
    border-bottom: 1px solid var(--border-color);
    background: var(--bg-tertiary);
    overflow-x: auto;
}

.tab-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 1.5rem;
    background: none;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    transition: var(--transition);
    font-weight: 500;
    white-space: nowrap;
    border-bottom: 3px solid transparent;
}

.tab-btn:hover,
.tab-btn.active {
    color: var(--primary-color);
    background: var(--bg-card);
    border-bottom-color: var(--primary-color);
}

.tab-content {
    display: none;
    padding: 2rem;
}

.tab-content.active {
    display: block;
}

.settings-card {
    margin-bottom: 2rem;
}

.card-header {
    margin-bottom: 2rem;
}

.card-header h3 {
    font-size: 1.5rem;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.card-header p {
    color: var(--text-muted);
}

.settings-form {
    max-width: 800px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    background: var(--bg-tertiary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    color: var(--text-primary);
    font-size: 0.95rem;
    transition: var(--transition);
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.form-group small {
    color: var(--text-muted);
    font-size: 0.8rem;
    margin-top: 0.25rem;
    display: block;
}

.form-divider {
    margin: 2rem 0 1.5rem 0;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border-color);
}

.form-divider h4 {
    color: var(--text-primary);
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Switch Toggle */
.switch-label {
    display: flex !important;
    align-items: center;
    gap: 1rem;
    cursor: pointer;
    user-select: none;
}

.switch-label input[type="checkbox"] {
    width: auto !important;
    margin: 0;
    opacity: 0;
    position: absolute;
}

.switch-slider {
    position: relative;
    width: 50px;
    height: 26px;
    background: var(--bg-primary);
    border-radius: 26px;
    transition: var(--transition);
    border: 1px solid var(--border-color);
}

.switch-slider:before {
    content: '';
    position: absolute;
    top: 2px;
    left: 2px;
    width: 20px;
    height: 20px;
    background: var(--text-muted);
    border-radius: 50%;
    transition: var(--transition);
}

.switch-label input[type="checkbox"]:checked + .switch-slider {
    background: var(--primary-color);
}

.switch-label input[type="checkbox"]:checked + .switch-slider:before {
    transform: translateX(24px);
    background: white;
}

/* Media Grid */
.media-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.media-item {
    background: var(--bg-tertiary);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    border: 1px solid var(--border-color);
}

.media-preview {
    width: 100%;
    height: 120px;
    background: var(--bg-primary);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    overflow: hidden;
}

.media-preview img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.media-info h4 {
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.media-info p {
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
}

.media-info small {
    color: var(--text-muted);
    display: block;
    margin-bottom: 1rem;
}

.file-input-wrapper {
    position: relative;
}

.file-input-wrapper input[type="file"] {
    opacity: 0;
    position: absolute;
    z-index: -1;
}

.file-input-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: var(--gradient-primary);
    color: white;
    border-radius: var(--radius-md);
    cursor: pointer;
    transition: var(--transition);
    justify-content: center;
    font-weight: 500;
}

.file-input-label:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* Popup Preview */
.popup-preview {
    margin-top: 2rem;
    padding: 1.5rem;
    background: var(--bg-tertiary);
    border-radius: var(--radius-lg);
    border: 1px solid var(--border-color);
}

.popup-preview h4 {
    color: var(--text-primary);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.preview-popup {
    background: var(--bg-card);
    border-radius: var(--radius-xl);
    padding: 2rem;
    text-align: center;
    border: 1px solid var(--border-color);
    max-width: 400px;
    margin: 0 auto;
}

.preview-header h3 {
    font-size: 1.8rem;
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 0.5rem;
}

.preview-header p {
    color: var(--text-muted);
    margin-bottom: 1.5rem;
}

.preview-warning {
    background: var(--bg-tertiary);
    border: 1px solid var(--warning-color);
    border-radius: var(--radius-md);
    padding: 1rem;
    margin-bottom: 1.5rem;
}

.preview-warning i {
    font-size: 2rem;
    color: var(--warning-color);
    margin-bottom: 0.5rem;
}

.preview-warning h4 {
    color: var(--warning-color);
    margin-bottom: 0.5rem;
}

.preview-warning p {
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.preview-buttons {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.preview-btn {
    flex: 1;
    padding: 0.75rem 1rem;
    border: none;
    border-radius: var(--radius-md);
    font-weight: 600;
    cursor: pointer;
    min-width: 120px;
}

.preview-btn-confirm {
    background: var(--gradient-primary);
    color: white;
}

.preview-btn-deny {
    background: var(--bg-tertiary);
    color: var(--text-primary);
    border: 1px solid var(--border-color);
}

.preview-note {
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
}

.preview-note small {
    color: var(--text-muted);
}

.form-actions {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border-color);
}

/* Responsive */
@media (max-width: 768px) {
    .settings-tabs {
        flex-direction: column;
    }
    
    .tab-content {
        padding: 1rem;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .media-grid {
        grid-template-columns: 1fr;
    }
    
    .preview-buttons {
        flex-direction: column;
    }
    
    .preview-btn {
        min-width: 100%;
    }
}
</style>

</body>
</html>