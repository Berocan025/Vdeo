<?php
/**
 * DOBİEN Video Platform - İletişim Sayfası
 * Geliştirici: DOBİEN
 * Tüm Hakları Saklıdır © DOBİEN
 */

require_once 'includes/config.php';

$page_title = "İletişim";
$page_description = "DOBİEN Video Platform ile iletişime geçin. Sorularınız ve önerileriniz için bize ulaşın.";
$page_keywords = "iletişim, destek, yardım, DOBİEN";

$success_message = '';
$error_message = '';

// İletişim formu gönderimi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_contact'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validasyon
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = 'Lütfen tüm alanları doldurun.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Geçerli bir e-posta adresi girin.';
    } else {
        try {
            // Veritabanına kaydet
            $stmt = $pdo->prepare("
                INSERT INTO iletisim_mesajlari (ad_soyad, email, konu, mesaj, ekleme_tarihi, durum) 
                VALUES (?, ?, ?, ?, NOW(), 'yeni')
            ");
            $stmt->execute([$name, $email, $subject, $message]);
            
            $success_message = 'Mesajınız başarıyla gönderildi. En kısa sürede size geri dönüş yapacağız.';
            
            // Formu temizle
            $name = $email = $subject = $message = '';
            
        } catch (PDOException $e) {
            $error_message = 'Mesaj gönderilirken bir hata oluştu. Lütfen tekrar deneyin.';
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
    <div class="contact-page">
        <!-- Sayfa Başlığı -->
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-envelope"></i>
                İletişim
            </h1>
            <p class="page-subtitle">Bizimle iletişime geçin, size yardımcı olmaktan memnuniyet duyarız</p>
        </div>

        <div class="contact-content">
            <!-- İletişim Bilgileri -->
            <div class="contact-info">
                <div class="info-cards">
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h3>E-posta</h3>
                        <p>info@dobien.com</p>
                        <p>destek@dobien.com</p>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h3>Telefon</h3>
                        <p>+90 (212) 123 45 67</p>
                        <p>Pazartesi - Cuma: 09:00 - 18:00</p>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h3>Adres</h3>
                        <p>DOBİEN Video Platform</p>
                        <p>İstanbul, Türkiye</p>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3>Çalışma Saatleri</h3>
                        <p>Pazartesi - Cuma: 09:00 - 18:00</p>
                        <p>Hafta sonu: 10:00 - 16:00</p>
                    </div>
                </div>
            </div>

            <!-- İletişim Formu -->
            <div class="contact-form-section">
                <div class="form-header">
                    <h2>
                        <i class="fas fa-paper-plane"></i>
                        Mesaj Gönder
                    </h2>
                    <p>Aşağıdaki formu doldurarak bize mesaj gönderebilirsiniz. En kısa sürede size geri dönüş yapacağız.</p>
                </div>

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

                <form class="contact-form" method="POST" action="">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">
                                <i class="fas fa-user"></i>
                                Ad Soyad *
                            </label>
                            <input type="text" id="name" name="name" 
                                   value="<?php echo safeOutput($name ?? ''); ?>" 
                                   placeholder="Adınızı ve soyadınızı girin" required>
                        </div>

                        <div class="form-group">
                            <label for="email">
                                <i class="fas fa-envelope"></i>
                                E-posta *
                            </label>
                            <input type="email" id="email" name="email" 
                                   value="<?php echo safeOutput($email ?? ''); ?>" 
                                   placeholder="E-posta adresinizi girin" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="subject">
                            <i class="fas fa-tag"></i>
                            Konu *
                        </label>
                        <select id="subject" name="subject" required>
                            <option value="">Konu seçin</option>
                            <option value="Genel Bilgi" <?php echo ($subject ?? '') === 'Genel Bilgi' ? 'selected' : ''; ?>>Genel Bilgi</option>
                            <option value="Teknik Destek" <?php echo ($subject ?? '') === 'Teknik Destek' ? 'selected' : ''; ?>>Teknik Destek</option>
                            <option value="Üyelik İşlemleri" <?php echo ($subject ?? '') === 'Üyelik İşlemleri' ? 'selected' : ''; ?>>Üyelik İşlemleri</option>
                            <option value="Ödeme Sorunları" <?php echo ($subject ?? '') === 'Ödeme Sorunları' ? 'selected' : ''; ?>>Ödeme Sorunları</option>
                            <option value="İçerik Bildirimi" <?php echo ($subject ?? '') === 'İçerik Bildirimi' ? 'selected' : ''; ?>>İçerik Bildirimi</option>
                            <option value="Öneri & Şikayet" <?php echo ($subject ?? '') === 'Öneri & Şikayet' ? 'selected' : ''; ?>>Öneri & Şikayet</option>
                            <option value="İş Birliği" <?php echo ($subject ?? '') === 'İş Birliği' ? 'selected' : ''; ?>>İş Birliği</option>
                            <option value="Diğer" <?php echo ($subject ?? '') === 'Diğer' ? 'selected' : ''; ?>>Diğer</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="message">
                            <i class="fas fa-comment"></i>
                            Mesaj *
                        </label>
                        <textarea id="message" name="message" rows="6" 
                                  placeholder="Mesajınızı detaylı bir şekilde yazın..." required><?php echo safeOutput($message ?? ''); ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="submit_contact" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i>
                            Mesaj Gönder
                        </button>
                        <button type="reset" class="btn btn-outline">
                            <i class="fas fa-redo"></i>
                            Temizle
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- SSS Bölümü -->
        <div class="faq-section">
            <h2 class="section-title">
                <i class="fas fa-question-circle"></i>
                Sıkça Sorulan Sorular
            </h2>
            
            <div class="faq-items">
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <h3>Üyelik nasıl yükseltilir?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Profil sayfanızdan veya "Üyelik Yükselt" sayfasından kolayca üyeliğinizi VIP veya Premium seviyesine yükseltebilirsiniz. Ödeme işleminiz tamamlandıktan sonra hemen yeni özelliklerden faydalanmaya başlayabilirsiniz.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <h3>Video kalitesi nasıl değiştirilir?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Video oynatıcısında sağ alt köşedeki ayarlar simgesine tıklayarak kalite seçeneklerini görebilirsiniz. Üyelik seviyenize göre 720p, 1080p veya 4K kalitelerinden birini seçebilirsiniz.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <h3>Şifremi nasıl sıfırlarım?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Giriş sayfasında "Şifremi Unuttum" linkine tıklayarak e-posta adresinizi girin. Size gönderilen link ile şifrenizi sıfırlayabilirsiniz.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <h3>Mobil uygulamanız var mı?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Şu anda web sitesi üzerinden hizmet vermekteyiz. Ancak site tamamen mobil uyumludur ve tüm özellikler mobil cihazlarda sorunsuz çalışmaktadır.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <h3>Video indirme özelliği var mı?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Premium üyelerimiz için sınırlı video indirme özelliği planlanmaktadır. Şu anda sadece çevrimiçi izleme hizmeti sunulmaktadır.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hızlı Eylemler -->
        <div class="quick-actions">
            <h2 class="section-title">
                <i class="fas fa-rocket"></i>
                Hızlı Eylemler
            </h2>
            
            <div class="actions-grid">
                <a href="<?php echo siteUrl('uyelik-yukselt.php'); ?>" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-crown"></i>
                    </div>
                    <h3>Üyelik Yükselt</h3>
                    <p>VIP veya Premium üye olun ve daha fazla özellikten yararlanın</p>
                </a>
                
                <a href="<?php echo siteUrl('sifremi-unuttum.php'); ?>" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-key"></i>
                    </div>
                    <h3>Şifre Sıfırla</h3>
                    <p>Şifrenizi mi unuttunuz? Hemen sıfırlayın</p>
                </a>
                
                <a href="<?php echo siteUrl('kategoriler.php'); ?>" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <h3>Videoları Keşfet</h3>
                    <p>Binlerce video içeriğini kategorilere göre keşfedin</p>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* İletişim Sayfası Stilleri */
.contact-page {
    max-width: 1200px;
    margin: 0 auto;
}

.page-header {
    text-align: center;
    margin-bottom: 50px;
    padding: 40px 0;
}

.page-title {
    color: #fff;
    font-size: 3rem;
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
    font-size: 1.2rem;
    margin: 0;
    max-width: 600px;
    margin: 0 auto;
}

.contact-content {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 40px;
    margin-bottom: 60px;
}

.info-cards {
    display: grid;
    gap: 20px;
}

.info-card {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    padding: 25px;
    text-align: center;
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.info-card:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateY(-3px);
}

.info-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--primary-color), #e67e22);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
}

.info-icon i {
    font-size: 1.5rem;
    color: #000;
}

.info-card h3 {
    color: #fff;
    margin: 0 0 10px;
    font-size: 1.1rem;
}

.info-card p {
    color: rgba(255, 255, 255, 0.7);
    margin: 5px 0;
    font-size: 0.9rem;
}

.contact-form-section {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 15px;
    padding: 30px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.form-header {
    margin-bottom: 30px;
}

.form-header h2 {
    color: #fff;
    margin: 0 0 10px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.5rem;
}

.form-header h2 i {
    color: var(--primary-color);
}

.form-header p {
    color: rgba(255, 255, 255, 0.8);
    margin: 0;
    line-height: 1.5;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
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

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
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

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px 15px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    color: #fff;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.1);
}

.form-group input::placeholder,
.form-group textarea::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

.form-group select option {
    background: #1a1a2e;
    color: #fff;
}

.form-group textarea {
    resize: vertical;
    min-height: 120px;
}

.form-actions {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
    margin-top: 30px;
}

.section-title {
    color: #fff;
    font-size: 2rem;
    margin: 0 0 30px;
    display: flex;
    align-items: center;
    gap: 10px;
    text-align: center;
    justify-content: center;
}

.section-title i {
    color: var(--primary-color);
}

.faq-section {
    margin-bottom: 60px;
}

.faq-items {
    max-width: 800px;
    margin: 0 auto;
}

.faq-item {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    margin-bottom: 15px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    overflow: hidden;
}

.faq-question {
    padding: 20px 25px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.3s ease;
}

.faq-question:hover {
    background: rgba(255, 255, 255, 0.05);
}

.faq-question h3 {
    color: #fff;
    margin: 0;
    font-size: 1.1rem;
}

.faq-question i {
    color: var(--primary-color);
    transition: transform 0.3s ease;
}

.faq-question.active i {
    transform: rotate(180deg);
}

.faq-answer {
    padding: 0 25px;
    max-height: 0;
    overflow: hidden;
    transition: all 0.3s ease;
}

.faq-answer.show {
    padding: 0 25px 20px;
    max-height: 200px;
}

.faq-answer p {
    color: rgba(255, 255, 255, 0.8);
    margin: 0;
    line-height: 1.6;
}

.quick-actions {
    margin-bottom: 40px;
}

.actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
}

.action-card {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    padding: 25px;
    text-align: center;
    text-decoration: none;
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.action-card:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateY(-5px);
    border-color: var(--primary-color);
}

.action-icon {
    width: 60px;
    height: 60px;
    background: rgba(255, 193, 7, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
}

.action-icon i {
    font-size: 1.5rem;
    color: var(--primary-color);
}

.action-card h3 {
    color: #fff;
    margin: 0 0 10px;
    font-size: 1.1rem;
}

.action-card p {
    color: rgba(255, 255, 255, 0.7);
    margin: 0;
    font-size: 0.9rem;
    line-height: 1.4;
}

/* Responsive */
@media (max-width: 768px) {
    .page-title {
        font-size: 2rem;
        flex-direction: column;
        gap: 10px;
    }
    
    .contact-content {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .actions-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function toggleFaq(element) {
    const faqItem = element.parentElement;
    const answer = faqItem.querySelector('.faq-answer');
    const isActive = element.classList.contains('active');
    
    // Tüm FAQ'ları kapat
    document.querySelectorAll('.faq-question').forEach(q => {
        q.classList.remove('active');
        q.parentElement.querySelector('.faq-answer').classList.remove('show');
    });
    
    // Eğer tıklanan aktif değilse aç
    if (!isActive) {
        element.classList.add('active');
        answer.classList.add('show');
    }
}

// Form validasyonu
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.contact-form');
    const inputs = form.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
    });
    
    form.addEventListener('submit', function(e) {
        let isValid = true;
        inputs.forEach(input => {
            if (!validateField(input)) {
                isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
        }
    });
});

function validateField(field) {
    const value = field.value.trim();
    let isValid = true;
    
    // Gerekli alan kontrolü
    if (field.hasAttribute('required') && !value) {
        isValid = false;
    }
    
    // E-posta kontrolü
    if (field.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
        }
    }
    
    // Görsel feedback
    if (isValid) {
        field.style.borderColor = 'rgba(255, 255, 255, 0.2)';
    } else {
        field.style.borderColor = '#dc3545';
    }
    
    return isValid;
}
</script>

<?php include 'includes/footer.php'; ?>