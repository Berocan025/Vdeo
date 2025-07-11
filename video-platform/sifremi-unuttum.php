<?php
/**
 * DOBİEN Video Platform - Şifremi Unuttum
 * Geliştirici: DOBİEN
 * Tüm Hakları Saklıdır © DOBİEN
 */

require_once 'includes/config.php';

$page_title = "Şifremi Unuttum";
$page_description = "DOBİEN Video Platform şifre sıfırlama. Unuttuğunuz şifrenizi kolayca yenileyin.";
$page_keywords = "şifre sıfırlama, şifremi unuttum, DOBİEN";

$success_message = '';
$error_message = '';
$step = 1; // 1: Email gir, 2: Kod doğrula, 3: Yeni şifre

// Reset token kontrolü
if (isset($_GET['token']) && isset($_GET['email'])) {
    $step = 3;
    $reset_token = $_GET['token'];
    $reset_email = $_GET['email'];
    
    // Token doğrula
    $stmt = $pdo->prepare("
        SELECT * FROM password_resets 
        WHERE email = ? AND token = ? AND expires_at > NOW() AND used = 0
    ");
    $stmt->execute([$reset_email, $reset_token]);
    $reset_data = $stmt->fetch();
    
    if (!$reset_data) {
        $error_message = 'Geçersiz veya süresi dolmuş şifre sıfırlama linki.';
        $step = 1;
    }
}

// Form işlemleri
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['send_reset_email'])) {
        // Email gönderme
        $email = trim($_POST['email']);
        
        if (empty($email)) {
            $error_message = 'E-posta adresi gereklidir.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = 'Geçerli bir e-posta adresi girin.';
        } else {
            // Kullanıcı kontrolü
            $stmt = $pdo->prepare("SELECT * FROM kullanicilar WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Reset token oluştur
                $token = bin2hex(random_bytes(32));
                $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Eski tokenları temizle
                $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
                $stmt->execute([$email]);
                
                // Yeni token kaydet
                $stmt = $pdo->prepare("
                    INSERT INTO password_resets (email, token, expires_at, created_at) 
                    VALUES (?, ?, ?, NOW())
                ");
                $stmt->execute([$email, $token, $expires_at]);
                
                // Email gönder (gerçek uygulamada mail fonksiyonu kullanılacak)
                $reset_link = siteUrl("sifremi-unuttum.php?token=$token&email=" . urlencode($email));
                
                $success_message = 'Şifre sıfırlama linki e-posta adresinize gönderildi. Lütfen e-postanızı kontrol edin.';
                
                // Demo için konsola yazdır
                error_log("Password Reset Link: $reset_link");
                
            } else {
                $error_message = 'Bu e-posta adresi ile kayıtlı kullanıcı bulunamadı.';
            }
        }
    }
    
    if (isset($_POST['reset_password'])) {
        // Yeni şifre belirleme
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        $token = $_POST['token'];
        $email = $_POST['email'];
        
        if (empty($new_password) || empty($confirm_password)) {
            $error_message = 'Lütfen tüm alanları doldurun.';
        } elseif (strlen($new_password) < 6) {
            $error_message = 'Şifre en az 6 karakter olmalıdır.';
        } elseif ($new_password !== $confirm_password) {
            $error_message = 'Şifreler eşleşmiyor.';
        } else {
            // Token kontrol et
            $stmt = $pdo->prepare("
                SELECT * FROM password_resets 
                WHERE email = ? AND token = ? AND expires_at > NOW() AND used = 0
            ");
            $stmt->execute([$email, $token]);
            $reset_data = $stmt->fetch();
            
            if ($reset_data) {
                // Şifreyi güncelle
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                
                $stmt = $pdo->prepare("UPDATE kullanicilar SET sifre = ? WHERE email = ?");
                $stmt->execute([$hashed_password, $email]);
                
                // Token'ı kullanıldı olarak işaretle
                $stmt = $pdo->prepare("UPDATE password_resets SET used = 1 WHERE id = ?");
                $stmt->execute([$reset_data['id']]);
                
                $success_message = 'Şifreniz başarıyla güncellendi. Artık yeni şifrenizle giriş yapabilirsiniz.';
                $step = 4; // Tamamlandı
                
            } else {
                $error_message = 'Geçersiz veya süresi dolmuş şifre sıfırlama linki.';
                $step = 1;
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
    <div class="reset-password-page">
        <!-- Sayfa Başlığı -->
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-key"></i>
                Şifremi Unuttum
            </h1>
            <p class="page-subtitle">Şifrenizi kolayca sıfırlayın ve hesabınıza erişiminizi geri kazanın</p>
        </div>

        <div class="reset-container">
            <!-- Adım Göstergesi -->
            <div class="steps-indicator">
                <div class="step <?php echo $step >= 1 ? 'active' : ''; ?> <?php echo $step > 1 ? 'completed' : ''; ?>">
                    <div class="step-number">1</div>
                    <div class="step-label">E-posta Adresi</div>
                </div>
                
                <div class="step-line <?php echo $step > 1 ? 'active' : ''; ?>"></div>
                
                <div class="step <?php echo $step >= 2 ? 'active' : ''; ?> <?php echo $step > 2 ? 'completed' : ''; ?>">
                    <div class="step-number">2</div>
                    <div class="step-label">E-posta Kontrolü</div>
                </div>
                
                <div class="step-line <?php echo $step > 2 ? 'active' : ''; ?>"></div>
                
                <div class="step <?php echo $step >= 3 ? 'active' : ''; ?> <?php echo $step > 3 ? 'completed' : ''; ?>">
                    <div class="step-number">3</div>
                    <div class="step-label">Yeni Şifre</div>
                </div>
            </div>

            <!-- Form Alanı -->
            <div class="form-container">
                <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success_message; ?>
                </div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error_message; ?>
                </div>
                <?php endif; ?>

                <?php if ($step == 1): ?>
                <!-- Adım 1: E-posta Gir -->
                <div class="form-step">
                    <div class="step-header">
                        <h2>E-posta Adresinizi Girin</h2>
                        <p>Hesabınızla ilişkili e-posta adresini girin. Size şifre sıfırlama linki göndereceğiz.</p>
                    </div>
                    
                    <form method="POST" action="" class="reset-form">
                        <div class="form-group">
                            <label for="email">
                                <i class="fas fa-envelope"></i>
                                E-posta Adresi
                            </label>
                            <input type="email" id="email" name="email" 
                                   placeholder="ornek@email.com" required autofocus>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="send_reset_email" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i>
                                Sıfırlama Linki Gönder
                            </button>
                            <a href="<?php echo siteUrl('giris.php'); ?>" class="btn btn-outline">
                                <i class="fas fa-arrow-left"></i>
                                Giriş Sayfasına Dön
                            </a>
                        </div>
                    </form>
                </div>

                <?php elseif ($step == 2): ?>
                <!-- Adım 2: E-posta Kontrolü -->
                <div class="form-step">
                    <div class="step-header success">
                        <div class="success-icon">
                            <i class="fas fa-envelope-open"></i>
                        </div>
                        <h2>E-posta Gönderildi!</h2>
                        <p>Şifre sıfırlama linki e-posta adresinize gönderildi. Lütfen e-postanızı kontrol edin ve linke tıklayın.</p>
                    </div>
                    
                    <div class="email-instructions">
                        <h3>E-postayı göremiyorsanız:</h3>
                        <ul>
                            <li>Spam/gereksiz e-posta klasörünüzü kontrol edin</li>
                            <li>E-posta adresinizi doğru yazdığınızdan emin olun</li>
                            <li>Birkaç dakika bekleyip tekrar kontrol edin</li>
                            <li>Sorun devam ederse müşteri desteği ile iletişime geçin</li>
                        </ul>
                    </div>
                    
                    <div class="form-actions">
                        <a href="<?php echo siteUrl('sifremi-unuttum.php'); ?>" class="btn btn-primary">
                            <i class="fas fa-redo"></i>
                            Tekrar Dene
                        </a>
                        <a href="<?php echo siteUrl('iletisim.php'); ?>" class="btn btn-outline">
                            <i class="fas fa-headset"></i>
                            Destek
                        </a>
                    </div>
                </div>

                <?php elseif ($step == 3): ?>
                <!-- Adım 3: Yeni Şifre Belirle -->
                <div class="form-step">
                    <div class="step-header">
                        <h2>Yeni Şifrenizi Belirleyin</h2>
                        <p>Güvenli bir şifre seçin. Şifreniz en az 6 karakter olmalıdır.</p>
                    </div>
                    
                    <form method="POST" action="" class="reset-form">
                        <input type="hidden" name="token" value="<?php echo safeOutput($reset_token ?? ''); ?>">
                        <input type="hidden" name="email" value="<?php echo safeOutput($reset_email ?? ''); ?>">
                        
                        <div class="form-group">
                            <label for="new_password">
                                <i class="fas fa-lock"></i>
                                Yeni Şifre
                            </label>
                            <div class="password-input">
                                <input type="password" id="new_password" name="new_password" 
                                       placeholder="En az 6 karakter" required minlength="6">
                                <button type="button" class="password-toggle" onclick="togglePassword('new_password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">
                                <i class="fas fa-lock"></i>
                                Şifre Tekrarı
                            </label>
                            <div class="password-input">
                                <input type="password" id="confirm_password" name="confirm_password" 
                                       placeholder="Şifrenizi tekrar girin" required minlength="6">
                                <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="password-strength" id="passwordStrength" style="display: none;">
                            <div class="strength-bar">
                                <div class="strength-fill"></div>
                            </div>
                            <div class="strength-text">Şifre Gücü: <span id="strengthText">Zayıf</span></div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="reset_password" class="btn btn-primary">
                                <i class="fas fa-check"></i>
                                Şifreyi Güncelle
                            </button>
                            <a href="<?php echo siteUrl('giris.php'); ?>" class="btn btn-outline">
                                <i class="fas fa-times"></i>
                                İptal
                            </a>
                        </div>
                    </form>
                </div>

                <?php elseif ($step == 4): ?>
                <!-- Adım 4: Tamamlandı -->
                <div class="form-step">
                    <div class="step-header success">
                        <div class="success-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h2>Şifre Başarıyla Güncellendi!</h2>
                        <p>Şifreniz başarıyla değiştirildi. Artık yeni şifrenizle giriş yapabilirsiniz.</p>
                    </div>
                    
                    <div class="completion-info">
                        <div class="info-item">
                            <i class="fas fa-shield-alt"></i>
                            <span>Hesabınız güvenli</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <span>Şifre değişikliği: <?php echo date('d.m.Y H:i'); ?></span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-key"></i>
                            <span>Yeni şifreniz aktif</span>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <a href="<?php echo siteUrl('giris.php'); ?>" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i>
                            Giriş Yap
                        </a>
                        <a href="<?php echo siteUrl(); ?>" class="btn btn-outline">
                            <i class="fas fa-home"></i>
                            Ana Sayfa
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Güvenlik İpuçları -->
            <div class="security-tips">
                <h3><i class="fas fa-lightbulb"></i> Güvenlik İpuçları</h3>
                <div class="tips-grid">
                    <div class="tip-item">
                        <i class="fas fa-random"></i>
                        <span>Karmaşık şifreler kullanın</span>
                    </div>
                    <div class="tip-item">
                        <i class="fas fa-ban"></i>
                        <span>Şifrenizi paylaşmayın</span>
                    </div>
                    <div class="tip-item">
                        <i class="fas fa-sync"></i>
                        <span>Düzenli olarak değiştirin</span>
                    </div>
                    <div class="tip-item">
                        <i class="fas fa-user-secret"></i>
                        <span>Farklı hesaplar için farklı şifreler</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Şifre Sıfırlama Sayfası Stilleri */
.reset-password-page {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px 0;
}

.page-header {
    text-align: center;
    margin-bottom: 40px;
}

.page-title {
    color: #fff;
    font-size: 2.5rem;
    margin: 0 0 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
}

.page-title i {
    color: var(--primary-color);
}

.page-subtitle {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1.1rem;
    margin: 0;
    max-width: 500px;
    margin: 0 auto;
    line-height: 1.5;
}

.reset-container {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 20px;
    padding: 40px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
}

.steps-indicator {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
    padding: 0 20px;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    flex: 1;
    max-width: 120px;
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    color: rgba(255, 255, 255, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    transition: all 0.3s ease;
}

.step.active .step-number {
    background: var(--primary-color);
    color: #000;
}

.step.completed .step-number {
    background: #28a745;
    color: #fff;
}

.step-label {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.8rem;
    text-align: center;
    font-weight: 500;
}

.step.active .step-label {
    color: var(--primary-color);
}

.step.completed .step-label {
    color: #28a745;
}

.step-line {
    height: 2px;
    background: rgba(255, 255, 255, 0.2);
    flex: 1;
    margin: 0 15px;
    transition: all 0.3s ease;
}

.step-line.active {
    background: var(--primary-color);
}

.form-container {
    min-height: 300px;
}

.form-step {
    animation: fadeInUp 0.5s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.step-header {
    text-align: center;
    margin-bottom: 30px;
}

.step-header.success {
    margin-bottom: 40px;
}

.step-header h2 {
    color: #fff;
    margin: 0 0 10px;
    font-size: 1.5rem;
}

.step-header p {
    color: rgba(255, 255, 255, 0.8);
    margin: 0;
    line-height: 1.5;
}

.success-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #28a745, #20c997);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.success-icon i {
    font-size: 2.5rem;
    color: #fff;
}

.alert {
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 500;
}

.alert-success {
    background: rgba(40, 167, 69, 0.2);
    border: 1px solid rgba(40, 167, 69, 0.5);
    color: #28a745;
}

.alert-error {
    background: rgba(220, 53, 69, 0.2);
    border: 1px solid rgba(220, 53, 69, 0.5);
    color: #dc3545;
}

.reset-form {
    max-width: 400px;
    margin: 0 auto;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #fff;
    margin-bottom: 8px;
    font-weight: 500;
}

.form-group label i {
    color: var(--primary-color);
    font-size: 0.9rem;
}

.form-group input {
    width: 100%;
    padding: 12px 15px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
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

.password-input {
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
    padding: 5px;
    transition: color 0.3s ease;
}

.password-toggle:hover {
    color: var(--primary-color);
}

.password-strength {
    margin-top: 10px;
}

.strength-bar {
    height: 4px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 2px;
    overflow: hidden;
    margin-bottom: 5px;
}

.strength-fill {
    height: 100%;
    width: 0%;
    transition: all 0.3s ease;
    border-radius: 2px;
}

.strength-text {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.7);
}

.form-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 30px;
    flex-wrap: wrap;
}

.email-instructions {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    padding: 20px;
    margin: 30px 0;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.email-instructions h3 {
    color: var(--primary-color);
    margin: 0 0 15px;
    font-size: 1rem;
}

.email-instructions ul {
    margin: 0;
    padding-left: 20px;
    color: rgba(255, 255, 255, 0.8);
}

.email-instructions li {
    margin-bottom: 8px;
    line-height: 1.4;
}

.completion-info {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    padding: 25px;
    margin: 30px 0;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.info-item {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
    color: rgba(255, 255, 255, 0.8);
}

.info-item:last-child {
    margin-bottom: 0;
}

.info-item i {
    color: var(--primary-color);
    width: 16px;
}

.security-tips {
    margin-top: 40px;
    padding: 25px;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.security-tips h3 {
    color: #fff;
    margin: 0 0 20px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 1rem;
}

.security-tips h3 i {
    color: var(--primary-color);
}

.tips-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.tip-item {
    display: flex;
    align-items: center;
    gap: 10px;
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.9rem;
}

.tip-item i {
    color: var(--primary-color);
    width: 16px;
}

/* Responsive */
@media (max-width: 768px) {
    .reset-password-page {
        padding: 10px;
    }
    
    .page-title {
        font-size: 2rem;
        flex-direction: column;
        gap: 10px;
    }
    
    .reset-container {
        padding: 25px 20px;
    }
    
    .steps-indicator {
        padding: 0 10px;
    }
    
    .step {
        max-width: 80px;
    }
    
    .step-label {
        font-size: 0.7rem;
    }
    
    .step-line {
        margin: 0 5px;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .tips-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const toggle = input.nextElementSibling.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        toggle.classList.remove('fa-eye');
        toggle.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        toggle.classList.remove('fa-eye-slash');
        toggle.classList.add('fa-eye');
    }
}

// Şifre gücü kontrolü
document.addEventListener('DOMContentLoaded', function() {
    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const strengthIndicator = document.getElementById('passwordStrength');
    
    if (newPasswordInput) {
        newPasswordInput.addEventListener('input', function() {
            const password = this.value;
            checkPasswordStrength(password);
            
            if (password.length > 0) {
                strengthIndicator.style.display = 'block';
            } else {
                strengthIndicator.style.display = 'none';
            }
        });
        
        // Şifre eşleşme kontrolü
        if (confirmPasswordInput) {
            confirmPasswordInput.addEventListener('input', function() {
                const password = newPasswordInput.value;
                const confirm = this.value;
                
                if (confirm.length > 0) {
                    if (password === confirm) {
                        this.style.borderColor = '#28a745';
                    } else {
                        this.style.borderColor = '#dc3545';
                    }
                } else {
                    this.style.borderColor = 'rgba(255, 255, 255, 0.2)';
                }
            });
        }
    }
});

function checkPasswordStrength(password) {
    const strengthFill = document.querySelector('.strength-fill');
    const strengthText = document.getElementById('strengthText');
    
    let score = 0;
    let feedback = '';
    
    // Uzunluk kontrolü
    if (password.length >= 8) score += 25;
    if (password.length >= 12) score += 10;
    
    // Karakter çeşitliliği
    if (/[a-z]/.test(password)) score += 15;
    if (/[A-Z]/.test(password)) score += 15;
    if (/[0-9]/.test(password)) score += 15;
    if (/[^A-Za-z0-9]/.test(password)) score += 20;
    
    // Gücü belirle
    if (score < 30) {
        feedback = 'Çok Zayıf';
        strengthFill.style.backgroundColor = '#dc3545';
    } else if (score < 50) {
        feedback = 'Zayıf';
        strengthFill.style.backgroundColor = '#fd7e14';
    } else if (score < 70) {
        feedback = 'Orta';
        strengthFill.style.backgroundColor = '#ffc107';
    } else if (score < 85) {
        feedback = 'Güçlü';
        strengthFill.style.backgroundColor = '#20c997';
    } else {
        feedback = 'Çok Güçlü';
        strengthFill.style.backgroundColor = '#28a745';
    }
    
    strengthFill.style.width = Math.min(score, 100) + '%';
    strengthText.textContent = 'Şifre Gücü: ' + feedback;
}
</script>

<?php include 'includes/footer.php'; ?>