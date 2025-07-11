<?php
/**
 * DOBİEN Video Platform - Admin Giriş Sayfası
 * Geliştirici: DOBİEN
 * Modern Video Paylaşım Platformu - Admin Giriş
 * Tüm Hakları Saklıdır © DOBİEN
 */

require_once '../includes/config.php';

// Eğer zaten giriş yapmışsa admin paneline yönlendir
if (isset($_SESSION['admin_id']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

$error_message = '';
$success_message = '';

// Form gönderildi mi?
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kullanici_adi = trim($_POST['kullanici_adi'] ?? '');
    $sifre = $_POST['sifre'] ?? '';
    $beni_hatirla = isset($_POST['beni_hatirla']);
    
    if (empty($kullanici_adi) || empty($sifre)) {
        $error_message = 'Kullanıcı adı ve şifre gereklidir.';
    } else {
        try {
            // Admin kullanıcısını kontrol et - database.sql'deki tablo yapısı
            try {
                $stmt = $pdo->prepare("SELECT * FROM admin_kullanicilar WHERE email = ? AND durum = 'aktif'");
                $stmt->execute([$kullanici_adi]);
                $admin = $stmt->fetch();
            } catch (PDOException $e) {
                // Eski tablo adını dene
                $stmt = $pdo->prepare("SELECT * FROM adminler WHERE (kullanici_adi = ? OR email = ?) AND durum = 'aktif'");
                $stmt->execute([$kullanici_adi, $kullanici_adi]);
                $admin = $stmt->fetch();
            }
            
            if ($admin && password_verify($sifre, $admin['sifre'])) {
                // Giriş başarılı
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['admin_ad_soyad'] = $admin['ad'] . ' ' . $admin['soyad'];
                $_SESSION['admin_yetki'] = $admin['yetki_seviyesi'];
                
                // Son giriş tarihini güncelle
                try {
                    $update_stmt = $pdo->prepare("UPDATE admin_kullanicilar SET son_giris_tarihi = NOW(), son_giris_ip = ? WHERE id = ?");
                    $update_stmt->execute([$_SERVER['REMOTE_ADDR'], $admin['id']]);
                } catch (PDOException $e) {
                    // Eski tablo için
                    $update_stmt = $pdo->prepare("UPDATE adminler SET son_giris = NOW(), son_ip = ? WHERE id = ?");
                    $update_stmt->execute([$_SERVER['REMOTE_ADDR'], $admin['id']]);
                }
                
                // Sistem logunu kaydet
                try {
                    $log_stmt = $pdo->prepare("INSERT INTO sistem_loglari (kullanici_id, kullanici_tipi, islem, detaylar, ip_adresi) VALUES (?, 'admin', 'giris', 'Admin paneline giriş yapıldı', ?)");
                    $log_stmt->execute([$admin['id'], $_SERVER['REMOTE_ADDR']]);
                } catch (PDOException $e) {
                    // Log tablosu yoksa görmezden gel
                }
                
                $success_message = 'Giriş başarılı! Yönlendiriliyorsunuz...';
                
                // 2 saniye sonra yönlendir
                header("refresh:2;url=index.php");
            } else {
                $error_message = 'E-posta veya şifre hatalı.';
                
                // Başarısız giriş denemesini logla
                try {
                    $log_stmt = $pdo->prepare("INSERT INTO sistem_loglari (kullanici_id, kullanici_tipi, islem, detaylar, ip_adresi) VALUES (0, 'admin', 'giris_hatasi', 'Başarısız giriş denemesi: ' . ?, ?)");
                    $log_stmt->execute([$kullanici_adi, $_SERVER['REMOTE_ADDR']]);
                } catch (PDOException $e) {
                    // Log tablosu yoksa görmezden gel
                }
            }
        } catch (Exception $e) {
            $error_message = 'Bir hata oluştu. Lütfen tekrar deneyin.';
        }
    }
}

// Remember me token kontrolü
if (isset($_COOKIE['admin_remember_token']) && !isset($_SESSION['admin_id'])) {
    $token = $_COOKIE['admin_remember_token'];
    $stmt = $pdo->prepare("SELECT * FROM adminler WHERE remember_token = ? AND durum = 'aktif'");
    $stmt->execute([$token]);
    $admin = $stmt->fetch();
    
    if ($admin) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_kullanici_adi'] = $admin['kullanici_adi'];
        $_SESSION['admin_rol'] = $admin['rol'];
        
        header('Location: index.php');
        exit;
    }
}

$page_title = "Admin Giriş";
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - DOBİEN Video Platform</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Admin Login CSS */
        :root {
            --primary-color: #6366f1;
            --primary-dark: #4f46e5;
            --bg-primary: #0f1419;
            --bg-secondary: #1a1f2e;
            --bg-tertiary: #252d3d;
            --bg-card: #1e2530;
            --text-primary: #ffffff;
            --text-secondary: #e2e8f0;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --success-color: #10b981;
            --error-color: #ef4444;
            --gradient-primary: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            --gradient-bg: linear-gradient(135deg, #0f1419 0%, #1a1f2e 100%);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
            --radius-2xl: 1.5rem;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--gradient-bg);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Background Animation */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="%23334155" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
            pointer-events: none;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            position: relative;
            z-index: 2;
        }

        .login-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-2xl);
            padding: 3rem 2rem;
            box-shadow: var(--shadow-lg);
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-logo {
            margin-bottom: 1rem;
        }

        .login-logo h1 {
            font-size: 2rem;
            font-weight: 700;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .login-logo p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .login-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .login-form {
            space-y: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.9rem;
        }

        .form-input {
            width: 100%;
            padding: 0.875rem 1rem;
            background: var(--bg-tertiary);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            color: var(--text-primary);
            font-size: 0.95rem;
            transition: var(--transition);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .form-input::placeholder {
            color: var(--text-muted);
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1rem;
        }

        .input-group .form-input {
            padding-left: 3rem;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 1rem;
            transition: var(--transition);
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        .form-checkbox {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .form-checkbox input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary-color);
            cursor: pointer;
        }

        .form-checkbox label {
            color: var(--text-secondary);
            font-size: 0.9rem;
            cursor: pointer;
        }

        .login-button {
            width: 100%;
            padding: 0.875rem 1rem;
            background: var(--gradient-primary);
            color: white;
            border: none;
            border-radius: var(--radius-lg);
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
        }

        .login-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .alert {
            padding: 1rem;
            border-radius: var(--radius-lg);
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: var(--success-color);
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: var(--error-color);
        }

        .login-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }

        .footer-link {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .footer-link:hover {
            color: var(--primary-color);
        }

        .developer-info {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            color: var(--text-muted);
            font-size: 0.8rem;
        }

        .developer-info i {
            color: var(--primary-color);
            margin-right: 0.5rem;
        }

        /* Loading Animation */
        .loading {
            position: relative;
        }

        .loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-container {
                padding: 1rem;
            }

            .login-card {
                padding: 2rem 1.5rem;
            }

            .login-logo h1 {
                font-size: 1.75rem;
            }

            .login-title {
                font-size: 1.25rem;
            }
        }

        /* Animation */
        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

<div class="login-container fade-in">
    <div class="login-card">
        <div class="login-header">
            <div class="login-logo">
                <h1>DOBİEN</h1>
                <p>Video Platform</p>
            </div>
            <h2 class="login-title">Admin Paneli</h2>
            <p class="login-subtitle">Devam etmek için giriş yapın</p>
        </div>

        <?php if ($error_message): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo htmlspecialchars($error_message); ?>
        </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?php echo htmlspecialchars($success_message); ?>
        </div>
        <?php endif; ?>

        <form class="login-form" method="POST" action="">
            <div class="form-group">
                <label for="kullanici_adi" class="form-label">Kullanıcı Adı veya E-posta</label>
                <div class="input-group">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" 
                           id="kullanici_adi" 
                           name="kullanici_adi" 
                           class="form-input" 
                           placeholder="Kullanıcı adınızı girin"
                           value="<?php echo htmlspecialchars($_POST['kullanici_adi'] ?? ''); ?>"
                           required>
                </div>
            </div>

            <div class="form-group">
                <label for="sifre" class="form-label">Şifre</label>
                <div class="input-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" 
                           id="sifre" 
                           name="sifre" 
                           class="form-input" 
                           placeholder="Şifrenizi girin"
                           required>
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye" id="passwordIcon"></i>
                    </button>
                </div>
            </div>

            <div class="form-checkbox">
                <input type="checkbox" id="beni_hatirla" name="beni_hatirla">
                <label for="beni_hatirla">Beni hatırla</label>
            </div>

            <button type="submit" class="login-button" id="loginButton">
                <i class="fas fa-sign-in-alt"></i>
                Giriş Yap
            </button>
        </form>

        <div class="login-footer">
            <a href="../index.php" class="footer-link">
                <i class="fas fa-arrow-left"></i>
                Ana Siteye Dön
            </a>
        </div>
    </div>
</div>

<div class="developer-info">
    <i class="fas fa-code"></i>
    <strong>DOBİEN</strong> tarafından geliştirildi
</div>

<script>
/**
 * DOBİEN Video Platform - Admin Login JavaScript
 * Geliştirici: DOBİEN
 */

// Şifre göster/gizle
function togglePassword() {
    const passwordInput = document.getElementById('sifre');
    const passwordIcon = document.getElementById('passwordIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        passwordIcon.className = 'fas fa-eye-slash';
    } else {
        passwordInput.type = 'password';
        passwordIcon.className = 'fas fa-eye';
    }
}

// Form gönderimi
document.querySelector('.login-form').addEventListener('submit', function() {
    const button = document.getElementById('loginButton');
    button.classList.add('loading');
    button.disabled = true;
    button.innerHTML = '<span style="opacity: 0;">Giriş yapılıyor...</span>';
});

// Enter tuşu ile giriş
document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        const form = document.querySelector('.login-form');
        if (form) {
            form.submit();
        }
    }
});

// Auto focus
document.addEventListener('DOMContentLoaded', function() {
    const usernameInput = document.getElementById('kullanici_adi');
    if (usernameInput && !usernameInput.value) {
        usernameInput.focus();
    }
});

// Güvenlik: Console uyarısı
console.log('%c⚠️ UYARI!', 'color: #ef4444; font-size: 20px; font-weight: bold;');
console.log('%cBu bir geliştiricilere yönelik konsolüdür. Birisi size buraya kod yapıştırmanızı söylediyse, bu bir dolandırıcılık girişimi olabilir ve hesabınızı ele geçirmelerine neden olabilir.', 'color: #ef4444; font-size: 14px;');
console.log('%cDOBİEN Video Platform - Admin Panel', 'color: #6366f1; font-size: 12px;');

// Caps Lock uyarısı
document.getElementById('sifre').addEventListener('keydown', function(e) {
    if (e.getModifierState && e.getModifierState('CapsLock')) {
        // Caps Lock açık uyarısı gösterilebilir
        console.log('Caps Lock açık');
    }
});

console.log('DOBİEN Admin Login yüklendi!');
</script>

</body>
</html>