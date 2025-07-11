<?php
/**
 * DOBİEN Video Platform - Kullanıcı Girişi
 * Geliştirici: DOBİEN
 * Modern Video Paylaşım Platformu
 * Tüm Hakları Saklıdır © DOBİEN
 */

require_once 'includes/config.php';

// Zaten giriş yapmış kullanıcıyı ana sayfaya yönlendir
if ($current_user) {
    header('Location: ' . siteUrl());
    exit;
}

$page_title = "Kullanıcı Girişi";
$page_description = "DOBİEN Video Platform'a giriş yapın ve premium video içeriklerine erişin";
$page_keywords = "giriş, login, üye girişi, DOBİEN";

$error_message = '';
$success_message = '';

// Form gönderildiğinde
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Basit validasyon
    if (empty($email) || empty($password)) {
        $error_message = 'E-posta ve şifre alanları zorunludur.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Geçerli bir e-posta adresi giriniz.';
    } else {
        // Kullanıcıyı veritabanında ara
        $stmt = $pdo->prepare("SELECT * FROM kullanicilar WHERE email = ? AND durum = 'aktif'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['sifre'])) {
            // Başarılı giriş
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['ad'] . ' ' . $user['soyad'];
            $_SESSION['user_membership'] = $user['uyelik_tipi'];
            
            // Son giriş tarihini güncelle
            $update_stmt = $pdo->prepare("UPDATE kullanicilar SET son_giris_tarihi = NOW() WHERE id = ?");
            $update_stmt->execute([$user['id']]);
            
            // Beni hatırla seçeneği
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/'); // 30 gün
                
                // Token'ı veritabanına kaydet
                $token_stmt = $pdo->prepare("UPDATE kullanicilar SET remember_token = ? WHERE id = ?");
                $token_stmt->execute([$token, $user['id']]);
            }
            
            // Yönlendirme
            $redirect = $_GET['redirect'] ?? '';
            if ($redirect && filter_var($redirect, FILTER_VALIDATE_URL)) {
                header('Location: ' . $redirect);
            } else {
                header('Location: ' . siteUrl());
            }
            exit;
        } else {
            $error_message = 'E-posta veya şifre hatalı.';
        }
    }
}

include 'includes/header.php';
?>

<div class="auth-container">
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <div class="logo">
                    <h1>DOBİEN</h1>
                    <p>Video Platform</p>
                </div>
                <h2>Hesabınıza Giriş Yapın</h2>
                <p>Premium video deneyimine devam etmek için giriş yapın</p>
            </div>
            
            <?php if ($error_message): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo htmlspecialchars($error_message); ?>
            </div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success_message); ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i>
                        E-posta Adresi
                    </label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                           placeholder="ornek@email.com">
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i>
                        Şifre
                    </label>
                    <div class="password-field">
                        <input type="password" id="password" name="password" required 
                               placeholder="Şifrenizi giriniz">
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="passwordIcon"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" <?php echo isset($_POST['remember']) ? 'checked' : ''; ?>>
                        <span class="checkbox-custom"></span>
                        Beni Hatırla
                    </label>
                    
                    <a href="<?php echo siteUrl('sifremi-unuttum.php'); ?>" class="forgot-password">
                        Şifremi Unuttum
                    </a>
                </div>
                
                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fas fa-sign-in-alt"></i>
                    Giriş Yap
                </button>
            </form>
            
            <div class="auth-divider">
                <span>veya</span>
            </div>
            
            <div class="social-login">
                <button class="btn btn-social btn-google" onclick="loginWithGoogle()">
                    <i class="fab fa-google"></i>
                    Google ile Giriş
                </button>
                <button class="btn btn-social btn-facebook" onclick="loginWithFacebook()">
                    <i class="fab fa-facebook"></i>
                    Facebook ile Giriş
                </button>
            </div>
            
            <div class="auth-footer">
                <p>
                    Henüz hesabınız yok mu? 
                    <a href="<?php echo siteUrl('kayit.php'); ?>">Hemen Üye Olun</a>
                </p>
                
                <div class="membership-benefits">
                    <h4>Üyelik Avantajları:</h4>
                    <ul>
                        <li><i class="fas fa-check"></i> HD ve 4K video kalitesi</li>
                        <li><i class="fas fa-check"></i> Favori listesi oluşturma</li>
                        <li><i class="fas fa-check"></i> Kişiselleştirilmiş öneriler</li>
                        <li><i class="fas fa-check"></i> İzleme geçmişi</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="auth-info">
            <div class="info-content">
                <h3>
                    <i class="fas fa-star"></i>
                    Premium Video Deneyimi
                </h3>
                <p>
                    DOBİEN Video Platform ile en kaliteli video içeriklerine erişin. 
                    4K çözünürlük, premium içerikler ve sınırsız izleme imkanı.
                </p>
                
                <div class="features-list">
                    <div class="feature">
                        <div class="feature-icon">
                            <i class="fas fa-play-circle"></i>
                        </div>
                        <div class="feature-text">
                            <h4>Binlerce Video</h4>
                            <p>Geniş video arşivimizden istediğiniz içeriği bulun</p>
                        </div>
                    </div>
                    
                    <div class="feature">
                        <div class="feature-icon">
                            <i class="fas fa-hd-video"></i>
                        </div>
                        <div class="feature-text">
                            <h4>4K Kalite</h4>
                            <p>Ultra HD çözünürlükle mükemmel görüntü deneyimi</p>
                        </div>
                    </div>
                    
                    <div class="feature">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <div class="feature-text">
                            <h4>Mobil Uyumlu</h4>
                            <p>Her cihazda sorunsuz video izleme deneyimi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DOBİEN Developer Signature -->
<div class="dobien-signature">
    <i class="fas fa-code"></i> DOBİEN
</div>

<style>
/* DOBİEN Video Platform - Giriş Sayfası Stilleri */
.auth-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.auth-wrapper {
    display: grid;
    grid-template-columns: 1fr 1fr;
    max-width: 1200px;
    width: 100%;
    gap: 40px;
    align-items: center;
}

.auth-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

.auth-header {
    text-align: center;
    margin-bottom: 30px;
}

.auth-header .logo h1 {
    color: var(--primary-color);
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
    text-shadow: 0 0 20px rgba(255, 193, 7, 0.3);
}

.auth-header .logo p {
    color: rgba(255, 255, 255, 0.7);
    margin: 5px 0 20px;
    font-size: 1.1rem;
}

.auth-header h2 {
    color: #fff;
    font-size: 1.8rem;
    margin: 0 0 10px;
    font-weight: 600;
}

.auth-header p {
    color: rgba(255, 255, 255, 0.7);
    margin: 0;
}

.auth-form {
    margin: 30px 0;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #fff;
    font-weight: 500;
    margin-bottom: 8px;
    font-size: 0.95rem;
}

.form-group input {
    width: 100%;
    padding: 12px 16px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    color: #fff;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-group input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.1);
}

.form-group input::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

.password-field {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: rgba(255, 255, 255, 0.6);
    cursor: pointer;
    padding: 4px;
    transition: color 0.3s ease;
}

.password-toggle:hover {
    color: var(--primary-color);
}

.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 20px 0;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    color: rgba(255, 255, 255, 0.8);
    cursor: pointer;
    font-size: 0.9rem;
}

.checkbox-label input[type="checkbox"] {
    display: none;
}

.checkbox-custom {
    width: 18px;
    height: 18px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 4px;
    position: relative;
    transition: all 0.3s ease;
}

.checkbox-label input[type="checkbox"]:checked + .checkbox-custom {
    background: var(--primary-color);
    border-color: var(--primary-color);
}

.checkbox-label input[type="checkbox"]:checked + .checkbox-custom::after {
    content: '\2713';
    position: absolute;
    top: -2px;
    left: 2px;
    color: #000;
    font-weight: bold;
    font-size: 12px;
}

.forgot-password {
    color: var(--primary-color);
    text-decoration: none;
    font-size: 0.9rem;
    transition: opacity 0.3s ease;
}

.forgot-password:hover {
    opacity: 0.8;
}

.btn-full {
    width: 100%;
    padding: 14px;
    font-size: 1.1rem;
    font-weight: 600;
}

.auth-divider {
    text-align: center;
    margin: 30px 0;
    position: relative;
}

.auth-divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: rgba(255, 255, 255, 0.2);
}

.auth-divider span {
    background: rgba(255, 255, 255, 0.05);
    color: rgba(255, 255, 255, 0.7);
    padding: 0 20px;
    position: relative;
}

.social-login {
    display: flex;
    gap: 15px;
    margin-bottom: 30px;
}

.btn-social {
    flex: 1;
    padding: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border-radius: 10px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-google {
    background: #db4437;
    border: 1px solid #db4437;
}

.btn-google:hover {
    background: #c23321;
}

.btn-facebook {
    background: #4267B2;
    border: 1px solid #4267B2;
}

.btn-facebook:hover {
    background: #365899;
}

.auth-footer {
    text-align: center;
    color: rgba(255, 255, 255, 0.7);
}

.auth-footer p {
    margin-bottom: 20px;
}

.auth-footer a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
}

.membership-benefits {
    text-align: left;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    padding: 20px;
    margin-top: 20px;
}

.membership-benefits h4 {
    color: #fff;
    margin: 0 0 15px;
    font-size: 1.1rem;
}

.membership-benefits ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.membership-benefits li {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 8px;
    font-size: 0.9rem;
}

.membership-benefits li i {
    color: var(--success-color);
    font-size: 0.8rem;
}

.auth-info {
    padding: 40px;
}

.info-content h3 {
    color: #fff;
    font-size: 2rem;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.info-content h3 i {
    color: var(--primary-color);
}

.info-content > p {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1.1rem;
    line-height: 1.6;
    margin-bottom: 40px;
}

.features-list {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.feature {
    display: flex;
    gap: 20px;
    align-items: flex-start;
}

.feature-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--primary-color), #e67e22);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.feature-icon i {
    font-size: 1.5rem;
    color: #000;
}

.feature-text h4 {
    color: #fff;
    margin: 0 0 8px;
    font-size: 1.2rem;
}

.feature-text p {
    color: rgba(255, 255, 255, 0.7);
    margin: 0;
    line-height: 1.5;
}

/* Responsive */
@media (max-width: 768px) {
    .auth-wrapper {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .auth-card {
        padding: 30px 20px;
    }
    
    .auth-info {
        padding: 20px;
    }
    
    .social-login {
        flex-direction: column;
    }
    
    .features-list {
        gap: 20px;
    }
    
    .feature {
        gap: 15px;
    }
    
    .feature-icon {
        width: 50px;
        height: 50px;
    }
}
</style>

<script>
/**
 * DOBİEN Video Platform - Giriş Sayfası JavaScript
 * Geliştirici: DOBİEN
 */

function togglePassword() {
    const passwordField = document.getElementById('password');
    const passwordIcon = document.getElementById('passwordIcon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        passwordIcon.className = 'fas fa-eye-slash';
    } else {
        passwordField.type = 'password';
        passwordIcon.className = 'fas fa-eye';
    }
}

function loginWithGoogle() {
    // Google OAuth entegrasyonu buraya gelecek
    alert('Google ile giriş özelliği yakında aktif olacak.');
}

function loginWithFacebook() {
    // Facebook OAuth entegrasyonu buraya gelecek
    alert('Facebook ile giriş özelliği yakında aktif olacak.');
}

// Form validasyonu
document.querySelector('.auth-form').addEventListener('submit', function(e) {
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    if (!email || !password) {
        e.preventDefault();
        alert('Lütfen tüm alanları doldurunuz.');
        return;
    }
    
    if (!isValidEmail(email)) {
        e.preventDefault();
        alert('Lütfen geçerli bir e-posta adresi giriniz.');
        return;
    }
});

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// DOBİEN imzası animasyonu
document.addEventListener('DOMContentLoaded', function() {
    const signature = document.querySelector('.dobien-signature');
    if (signature) {
        signature.style.animation = 'fadeInUp 1s ease-out 0.5s both';
    }
});
</script>

<?php include 'includes/footer.php'; ?>