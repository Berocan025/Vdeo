<?php
/**
 * DOBİEN Video Platform - Kullanıcı Kaydı
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

$page_title = "Üye Kayıt";
$page_description = "DOBİEN Video Platform'a üye olun ve premium video deneyimini keşfedin";
$page_keywords = "üye ol, kayıt, register, DOBİEN";

$error_message = '';
$success_message = '';

// Form gönderildiğinde
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ad = trim($_POST['ad'] ?? '');
    $soyad = trim($_POST['soyad'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $terms_accepted = isset($_POST['terms']);
    $age_confirmed = isset($_POST['age_confirm']);
    $newsletter = isset($_POST['newsletter']);
    
    // Validasyon
    if (empty($ad) || empty($soyad) || empty($email) || empty($password)) {
        $error_message = 'Tüm zorunlu alanları doldurunuz.';
    } elseif (strlen($ad) < 2 || strlen($soyad) < 2) {
        $error_message = 'Ad ve soyad en az 2 karakter olmalıdır.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Geçerli bir e-posta adresi giriniz.';
    } elseif (strlen($password) < 6) {
        $error_message = 'Şifre en az 6 karakter olmalıdır.';
    } elseif ($password !== $password_confirm) {
        $error_message = 'Şifreler eşleşmiyor.';
    } elseif (!$terms_accepted) {
        $error_message = 'Kullanım şartlarını kabul etmelisiniz.';
    } elseif (!$age_confirmed) {
        $error_message = '18 yaş üstü olduğunuzu onaylamalısınız.';
    } else {
        // E-posta kontrolü
        $email_check = $pdo->prepare("SELECT id FROM kullanicilar WHERE email = ?");
        $email_check->execute([$email]);
        
        if ($email_check->fetch()) {
            $error_message = 'Bu e-posta adresi zaten kullanılıyor.';
        } else {
            // Yeni kullanıcı oluştur
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $activation_code = bin2hex(random_bytes(32));
            
            $insert_stmt = $pdo->prepare("
                INSERT INTO kullanicilar (
                    ad, soyad, email, sifre, uyelik_tipi, durum, 
                    kayit_tarihi, aktivasyon_kodu, newsletter_izni
                ) VALUES (?, ?, ?, ?, 'kullanici', 'beklemede', NOW(), ?, ?)
            ");
            
            try {
                $insert_stmt->execute([
                    $ad, $soyad, $email, $hashed_password, 
                    $activation_code, $newsletter ? 1 : 0
                ]);
                
                // Aktivasyon e-postası gönder (gerçek uygulamada)
                // sendActivationEmail($email, $activation_code);
                
                $success_message = 'Kayıt işleminiz başarılı! E-posta adresinize aktivasyon bağlantısı gönderildi.';
                
                // Başarılı kayıt sonrası yönlendirme (isteğe bağlı)
                // header('Location: ' . siteUrl('giris.php?success=register'));
                // exit;
                
            } catch (PDOException $e) {
                $error_message = 'Kayıt işlemi sırasında bir hata oluştu. Lütfen tekrar deneyiniz.';
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="auth-container">
    <div class="auth-wrapper">
        <div class="auth-info">
            <div class="info-content">
                <h3>
                    <i class="fas fa-rocket"></i>
                    DOBİEN'e Hoş Geldiniz
                </h3>
                <p>
                    Binlerce premium video içeriğe erişim sağlayın. 4K kalitesi, 
                    özel kategoriler ve sınırsız izleme deneyimi sizi bekliyor.
                </p>
                
                <div class="membership-tiers">
                    <div class="tier">
                        <div class="tier-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="tier-info">
                            <h4>Ücretsiz Üyelik</h4>
                            <p>720p kalitede temel videolar</p>
                        </div>
                    </div>
                    
                    <div class="tier">
                        <div class="tier-icon">
                            <i class="fas fa-crown"></i>
                        </div>
                        <div class="tier-info">
                            <h4>VIP Üyelik</h4>
                            <p>1080p kalitede premium içerikler</p>
                        </div>
                    </div>
                    
                    <div class="tier">
                        <div class="tier-icon">
                            <i class="fas fa-gem"></i>
                        </div>
                        <div class="tier-info">
                            <h4>Premium Üyelik</h4>
                            <p>4K kalitede özel videolar</p>
                        </div>
                    </div>
                </div>
                
                <div class="stats">
                    <div class="stat">
                        <div class="stat-number">10K+</div>
                        <div class="stat-label">Aktif Üye</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number">5K+</div>
                        <div class="stat-label">Video İçerik</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number">50+</div>
                        <div class="stat-label">Kategori</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="auth-card">
            <div class="auth-header">
                <div class="logo">
                    <h1>DOBİEN</h1>
                    <p>Video Platform</p>
                </div>
                <h2>Ücretsiz Hesap Oluşturun</h2>
                <p>Hemen üye olun ve video dünyasına adım atın</p>
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
            
            <form method="POST" class="auth-form" id="registerForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="ad">
                            <i class="fas fa-user"></i>
                            Ad <span class="required">*</span>
                        </label>
                        <input type="text" id="ad" name="ad" required 
                               value="<?php echo htmlspecialchars($_POST['ad'] ?? ''); ?>"
                               placeholder="Adınız">
                    </div>
                    
                    <div class="form-group">
                        <label for="soyad">
                            <i class="fas fa-user"></i>
                            Soyad <span class="required">*</span>
                        </label>
                        <input type="text" id="soyad" name="soyad" required 
                               value="<?php echo htmlspecialchars($_POST['soyad'] ?? ''); ?>"
                               placeholder="Soyadınız">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i>
                        E-posta Adresi <span class="required">*</span>
                    </label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                           placeholder="ornek@email.com">
                    <div class="field-help">
                        <i class="fas fa-info-circle"></i>
                        Bu e-posta adresi ile hesabınıza giriş yapacaksınız
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock"></i>
                            Şifre <span class="required">*</span>
                        </label>
                        <div class="password-field">
                            <input type="password" id="password" name="password" required 
                                   placeholder="Şifrenizi oluşturun" minlength="6">
                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                <i class="fas fa-eye" id="passwordIcon"></i>
                            </button>
                        </div>
                        <div class="password-strength" id="passwordStrength"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password_confirm">
                            <i class="fas fa-lock"></i>
                            Şifre Tekrar <span class="required">*</span>
                        </label>
                        <div class="password-field">
                            <input type="password" id="password_confirm" name="password_confirm" required 
                                   placeholder="Şifrenizi tekrar giriniz">
                            <button type="button" class="password-toggle" onclick="togglePassword('password_confirm')">
                                <i class="fas fa-eye" id="passwordConfirmIcon"></i>
                            </button>
                        </div>
                        <div class="password-match" id="passwordMatch"></div>
                    </div>
                </div>
                
                <div class="form-checkboxes">
                    <label class="checkbox-label">
                        <input type="checkbox" name="age_confirm" required>
                        <span class="checkbox-custom"></span>
                        18 yaşından büyük olduğumu onaylıyorum <span class="required">*</span>
                    </label>
                    
                    <label class="checkbox-label">
                        <input type="checkbox" name="terms" required>
                        <span class="checkbox-custom"></span>
                        <a href="<?php echo siteUrl('kullanim-sartlari.php'); ?>" target="_blank">Kullanım Şartları</a> 
                        ve <a href="<?php echo siteUrl('gizlilik-politikasi.php'); ?>" target="_blank">Gizlilik Politikası</a>'nı kabul ediyorum <span class="required">*</span>
                    </label>
                    
                    <label class="checkbox-label">
                        <input type="checkbox" name="newsletter" <?php echo isset($_POST['newsletter']) ? 'checked' : ''; ?>>
                        <span class="checkbox-custom"></span>
                        E-posta bülteni ve güncellemeler almak istiyorum
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fas fa-user-plus"></i>
                    Ücretsiz Hesap Oluştur
                </button>
            </form>
            
            <div class="auth-divider">
                <span>veya</span>
            </div>
            
            <div class="social-login">
                <button class="btn btn-social btn-google" onclick="registerWithGoogle()">
                    <i class="fab fa-google"></i>
                    Google ile Kayıt
                </button>
                <button class="btn btn-social btn-facebook" onclick="registerWithFacebook()">
                    <i class="fab fa-facebook"></i>
                    Facebook ile Kayıt
                </button>
            </div>
            
            <div class="auth-footer">
                <p>
                    Zaten hesabınız var mı? 
                    <a href="<?php echo siteUrl('giris.php'); ?>">Giriş Yapın</a>
                </p>
                
                <div class="security-note">
                    <i class="fas fa-shield-alt"></i>
                    <span>Güvenliğiniz bizim önceliğimiz. Verileriniz 256-bit SSL ile korunmaktadır.</span>
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
/* DOBİEN Video Platform - Kayıt Sayfası Stilleri */
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
    max-width: 1400px;
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

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
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

.required {
    color: #e74c3c;
    font-weight: bold;
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

.field-help {
    display: flex;
    align-items: center;
    gap: 6px;
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.8rem;
    margin-top: 5px;
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

.password-strength {
    margin-top: 5px;
    font-size: 0.8rem;
    transition: all 0.3s ease;
}

.password-strength.weak {
    color: #e74c3c;
}

.password-strength.medium {
    color: #f39c12;
}

.password-strength.strong {
    color: #27ae60;
}

.password-match {
    margin-top: 5px;
    font-size: 0.8rem;
    transition: all 0.3s ease;
}

.password-match.match {
    color: #27ae60;
}

.password-match.no-match {
    color: #e74c3c;
}

.form-checkboxes {
    margin: 25px 0;
}

.checkbox-label {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    color: rgba(255, 255, 255, 0.8);
    cursor: pointer;
    font-size: 0.9rem;
    margin-bottom: 15px;
    line-height: 1.4;
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
    flex-shrink: 0;
    margin-top: 1px;
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

.checkbox-label a {
    color: var(--primary-color);
    text-decoration: none;
}

.checkbox-label a:hover {
    text-decoration: underline;
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

.security-note {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: rgba(39, 174, 96, 0.1);
    border: 1px solid rgba(39, 174, 96, 0.3);
    border-radius: 8px;
    padding: 12px;
    font-size: 0.85rem;
    color: #27ae60;
}

.auth-info {
    padding: 40px;
}

.info-content h3 {
    color: #fff;
    font-size: 2.2rem;
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

.membership-tiers {
    margin-bottom: 40px;
}

.tier {
    display: flex;
    gap: 15px;
    align-items: center;
    margin-bottom: 20px;
    padding: 15px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    transition: transform 0.3s ease;
}

.tier:hover {
    transform: translateX(5px);
}

.tier-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, var(--primary-color), #e67e22);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.tier-icon i {
    font-size: 1.2rem;
    color: #000;
}

.tier-info h4 {
    color: #fff;
    margin: 0 0 5px;
    font-size: 1.1rem;
}

.tier-info p {
    color: rgba(255, 255, 255, 0.7);
    margin: 0;
    font-size: 0.9rem;
}

.stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.stat {
    text-align: center;
    padding: 20px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 15px;
}

.stat-number {
    color: var(--primary-color);
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.stat-label {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.9rem;
}

/* Responsive */
@media (max-width: 968px) {
    .auth-wrapper {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .auth-card {
        order: 1;
        padding: 30px 20px;
    }
    
    .auth-info {
        order: 2;
        padding: 20px;
    }
    
    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }
    
    .social-login {
        flex-direction: column;
    }
    
    .stats {
        grid-template-columns: 1fr;
        gap: 15px;
    }
}

@media (max-width: 480px) {
    .auth-container {
        padding: 10px;
    }
    
    .auth-card {
        padding: 20px 15px;
    }
    
    .checkbox-label {
        font-size: 0.8rem;
    }
}
</style>

<script>
/**
 * DOBİEN Video Platform - Kayıt Sayfası JavaScript
 * Geliştirici: DOBİEN
 */

function togglePassword(fieldId) {
    const passwordField = document.getElementById(fieldId);
    const iconId = fieldId === 'password' ? 'passwordIcon' : 'passwordConfirmIcon';
    const passwordIcon = document.getElementById(iconId);
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        passwordIcon.className = 'fas fa-eye-slash';
    } else {
        passwordField.type = 'password';
        passwordIcon.className = 'fas fa-eye';
    }
}

function checkPasswordStrength(password) {
    const strengthIndicator = document.getElementById('passwordStrength');
    let strength = 0;
    let message = '';
    
    if (password.length >= 6) strength++;
    if (password.match(/[a-z]+/)) strength++;
    if (password.match(/[A-Z]+/)) strength++;
    if (password.match(/[0-9]+/)) strength++;
    if (password.match(/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/)) strength++;
    
    switch(strength) {
        case 0:
        case 1:
            message = 'Çok zayıf şifre';
            strengthIndicator.className = 'password-strength weak';
            break;
        case 2:
        case 3:
            message = 'Orta güçlükte şifre';
            strengthIndicator.className = 'password-strength medium';
            break;
        case 4:
        case 5:
            message = 'Güçlü şifre';
            strengthIndicator.className = 'password-strength strong';
            break;
    }
    
    strengthIndicator.textContent = password.length > 0 ? message : '';
}

function checkPasswordMatch() {
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password_confirm').value;
    const matchIndicator = document.getElementById('passwordMatch');
    
    if (passwordConfirm.length === 0) {
        matchIndicator.textContent = '';
        matchIndicator.className = 'password-match';
        return;
    }
    
    if (password === passwordConfirm) {
        matchIndicator.textContent = 'Şifreler eşleşiyor ✓';
        matchIndicator.className = 'password-match match';
    } else {
        matchIndicator.textContent = 'Şifreler eşleşmiyor ✗';
        matchIndicator.className = 'password-match no-match';
    }
}

function registerWithGoogle() {
    alert('Google ile kayıt özelliği yakında aktif olacak.');
}

function registerWithFacebook() {
    alert('Facebook ile kayıt özelliği yakında aktif olacak.');
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    const passwordField = document.getElementById('password');
    const passwordConfirmField = document.getElementById('password_confirm');
    
    passwordField.addEventListener('input', function() {
        checkPasswordStrength(this.value);
        checkPasswordMatch();
    });
    
    passwordConfirmField.addEventListener('input', function() {
        checkPasswordMatch();
    });
    
    // Form validation
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const passwordConfirm = document.getElementById('password_confirm').value;
        const email = document.getElementById('email').value;
        const terms = document.querySelector('input[name="terms"]').checked;
        const ageConfirm = document.querySelector('input[name="age_confirm"]').checked;
        
        if (!email || !password || !passwordConfirm) {
            e.preventDefault();
            alert('Lütfen tüm zorunlu alanları doldurunuz.');
            return;
        }
        
        if (!isValidEmail(email)) {
            e.preventDefault();
            alert('Lütfen geçerli bir e-posta adresi giriniz.');
            return;
        }
        
        if (password.length < 6) {
            e.preventDefault();
            alert('Şifre en az 6 karakter olmalıdır.');
            return;
        }
        
        if (password !== passwordConfirm) {
            e.preventDefault();
            alert('Şifreler eşleşmiyor.');
            return;
        }
        
        if (!terms) {
            e.preventDefault();
            alert('Kullanım şartlarını kabul etmelisiniz.');
            return;
        }
        
        if (!ageConfirm) {
            e.preventDefault();
            alert('18 yaş üstü olduğunuzu onaylamalısınız.');
            return;
        }
    });
    
    // DOBİEN imzası animasyonu
    const signature = document.querySelector('.dobien-signature');
    if (signature) {
        signature.style.animation = 'fadeInUp 1s ease-out 0.5s both';
    }
});

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}
</script>

<?php include 'includes/footer.php'; ?>