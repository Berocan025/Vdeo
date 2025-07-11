<?php
/**
 * DOBİEN Video Platform - Kullanım Şartları
 * Geliştirici: DOBİEN
 * Tüm Hakları Saklıdır © DOBİEN
 */

require_once 'includes/config.php';

$page_title = "Kullanım Şartları";
$page_description = "DOBİEN Video Platform kullanım şartları ve kuralları. Platform kullanımında uyulması gereken koşullar.";
$page_keywords = "kullanım şartları, kurallar, DOBİEN, yasal";

include 'includes/header.php';
?>

<div class="container">
    <div class="terms-page">
        <!-- Sayfa Başlığı -->
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-file-contract"></i>
                Kullanım Şartları
            </h1>
            <p class="page-subtitle">Platform kullanımında uyulması gereken kurallar ve koşullar</p>
            <div class="last-updated">
                Son güncellenme: <?php echo date('d.m.Y'); ?>
            </div>
        </div>

        <div class="terms-content">
            <!-- Giriş -->
            <section class="terms-section">
                <h2>1. Giriş</h2>
                <p>
                    Bu kullanım şartları, DOBİEN Video Platform ("Platform", "Site", "Hizmet") kullanımını düzenler. 
                    Platformu kullanarak bu şartları kabul etmiş sayılırsınız. Bu şartları kabul etmiyorsanız, 
                    lütfen platformu kullanmayınız.
                </p>
                <p>
                    DOBİEN Video Platform, kullanıcılarına video içerik izleme hizmeti sunan bir dijital platformdur. 
                    Bu belgede yer alan tüm koşullar ve kurallar, platform kullanımı için zorunludur.
                </p>
            </section>

            <!-- Tanımlar -->
            <section class="terms-section">
                <h2>2. Tanımlar</h2>
                <div class="definition-list">
                    <div class="definition-item">
                        <strong>Platform:</strong> DOBİEN Video Platform web sitesi ve tüm alt sayfaları
                    </div>
                    <div class="definition-item">
                        <strong>Kullanıcı:</strong> Platform'u kullanan gerçek veya tüzel kişiler
                    </div>
                    <div class="definition-item">
                        <strong>İçerik:</strong> Platform'da bulunan tüm video, metin, görsel ve ses materyalleri
                    </div>
                    <div class="definition-item">
                        <strong>Hesap:</strong> Kullanıcının platform'da oluşturduğu kişisel profil
                    </div>
                    <div class="definition-item">
                        <strong>Üyelik:</strong> Standart, VIP ve Premium olmak üzere üç farklı seviye
                    </div>
                </div>
            </section>

            <!-- Hesap Oluşturma -->
            <section class="terms-section">
                <h2>3. Hesap Oluşturma ve Yönetim</h2>
                <ul class="terms-list">
                    <li>Platform'u kullanabilmek için 18 yaşında veya daha büyük olmanız gerekmektedir.</li>
                    <li>18 yaşından küçükseniz, ebeveyn veya yasal vasi iznini almış olmanız zorunludur.</li>
                    <li>Hesap oluştururken doğru ve güncel bilgiler vermeniz gerekmektedir.</li>
                    <li>Hesap güvenliğinizden sorumlusunuz ve şifrenizi kimseyle paylaşmamalısınız.</li>
                    <li>Sahte bilgilerle hesap oluşturmak yasaktır ve hesabınızın kapatılmasına neden olabilir.</li>
                    <li>Bir kişi yalnızca bir hesap oluşturabilir.</li>
                    <li>Hesabınızı başkalarıyla paylaşmanız yasaktır.</li>
                </ul>
            </section>

            <!-- Üyelik Seviyeleri -->
            <section class="terms-section">
                <h2>4. Üyelik Seviyeleri ve Ödemeler</h2>
                <div class="membership-info">
                    <div class="membership-tier">
                        <h3><i class="fas fa-user"></i> Standart Üyelik (Ücretsiz)</h3>
                        <ul>
                            <li>720p kalitede video izleme</li>
                            <li>Temel platform özelliklerine erişim</li>
                            <li>Reklam destekli izleme</li>
                        </ul>
                    </div>
                    
                    <div class="membership-tier">
                        <h3><i class="fas fa-star"></i> VIP Üyelik</h3>
                        <ul>
                            <li>1080p Full HD kalitede video izleme</li>
                            <li>Reklamsız izleme deneyimi</li>
                            <li>Öncelikli müşteri desteği</li>
                            <li>Özel VIP içeriklerine erişim</li>
                        </ul>
                    </div>
                    
                    <div class="membership-tier">
                        <h3><i class="fas fa-crown"></i> Premium Üyelik</h3>
                        <ul>
                            <li>4K Ultra HD kalitede video izleme</li>
                            <li>Tüm premium içeriklere erişim</li>
                            <li>Çoklu cihaz desteği</li>
                            <li>Gelişmiş profil özellikleri</li>
                            <li>Öncelikli yeni içerik erişimi</li>
                        </ul>
                    </div>
                </div>
                
                <div class="payment-terms">
                    <h3>Ödeme Koşulları:</h3>
                    <ul class="terms-list">
                        <li>Ücretli üyelikler aylık veya yıllık olarak faturalandırılır.</li>
                        <li>Ödemeler otomatik olarak yenilenir.</li>
                        <li>Aboneliği istediğiniz zaman iptal edebilirsiniz.</li>
                        <li>İade politikamız gereği, kullanılmış dönemler için iade yapılmaz.</li>
                        <li>Fiyatlar değişiklik gösterebilir, değişiklikler önceden bildirilir.</li>
                    </ul>
                </div>
            </section>

            <!-- Platform Kullanım Kuralları -->
            <section class="terms-section">
                <h2>5. Platform Kullanım Kuralları</h2>
                <div class="rules-grid">
                    <div class="rule-category">
                        <h3><i class="fas fa-check-circle"></i> İzin Verilen Kullanımlar</h3>
                        <ul>
                            <li>Kişisel ve ticari olmayan amaçlarla video izleme</li>
                            <li>Sosyal medyada platform linklerini paylaşma</li>
                            <li>Platform özelliklerini kurallara uygun kullanma</li>
                            <li>Geri bildirim ve öneri gönderme</li>
                        </ul>
                    </div>
                    
                    <div class="rule-category prohibited">
                        <h3><i class="fas fa-times-circle"></i> Yasaklanan Davranışlar</h3>
                        <ul>
                            <li>İçerikleri izinsiz indirme veya kopyalama</li>
                            <li>Platform güvenliğini tehdit edici faaliyetler</li>
                            <li>Sahte hesap oluşturma</li>
                            <li>Spam veya zararlı içerik gönderme</li>
                            <li>Telif hakkı ihlali</li>
                            <li>Platformu ticari amaçlarla kullanma</li>
                            <li>Teknik güvenlik önlemlerini aşmaya çalışma</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- İçerik Politikası -->
            <section class="terms-section">
                <h2>6. İçerik Politikası</h2>
                <p>
                    Platform'da yer alan tüm içerikler telif hakkı koruması altındadır. İçeriklerin izinsiz 
                    kullanımı, kopyalanması, dağıtılması veya ticari amaçlarla kullanılması yasaktır.
                </p>
                <ul class="terms-list">
                    <li>İçerikler yalnızca platform üzerinde izlenebilir.</li>
                    <li>İçerikleri üçüncü kişilerle paylaşmak yasaktır.</li>
                    <li>Platform'da yer alan içerikler yaş sınırlamasına tabi olabilir.</li>
                    <li>18+ içerikler için yaş doğrulaması gereklidir.</li>
                    <li>İçerik kalitesi üyelik seviyenize göre belirlenir.</li>
                </ul>
            </section>

            <!-- Gizlilik ve Veri Güvenliği -->
            <section class="terms-section">
                <h2>7. Gizlilik ve Veri Güvenliği</h2>
                <p>
                    Kişisel verilerinizin korunması bizim için önemlidir. Veri işleme politikalarımız 
                    <a href="<?php echo siteUrl('gizlilik-politikasi.php'); ?>">Gizlilik Politikası</a> 
                    sayfasında detaylı olarak açıklanmıştır.
                </p>
                <ul class="terms-list">
                    <li>Verileriniz güvenli sunucularda saklanır.</li>
                    <li>Kişisel bilgileriniz üçüncü kişilerle paylaşılmaz.</li>
                    <li>Platform kullanımınız analiz edilebilir.</li>
                    <li>Çerezler kullanılarak deneyiminiz iyileştirilir.</li>
                </ul>
            </section>

            <!-- Sorumluluk Sınırlaması -->
            <section class="terms-section">
                <h2>8. Sorumluluk Sınırlaması</h2>
                <p>
                    DOBİEN Video Platform, hizmet kesintileri, teknik sorunlar veya içerik erişim 
                    problemlerinden dolayı sorumlu değildir. Platform "olduğu gibi" sunulmaktadır.
                </p>
                <ul class="terms-list">
                    <li>Hizmet kesintileri önceden bildirilmeyebilir.</li>
                    <li>İçerik kalitesi ve erişimi garanti edilmez.</li>
                    <li>Üçüncü taraf bağlantılarından sorumlu değiliz.</li>
                    <li>Kullanıcı kaynaklı zararlardan sorumlu değiliz.</li>
                </ul>
            </section>

            <!-- Hesap Askıya Alma ve Kapatma -->
            <section class="terms-section">
                <h2>9. Hesap Askıya Alma ve Kapatma</h2>
                <p>
                    Kullanım şartlarını ihlal eden hesaplar uyarı, askıya alma veya kalıcı kapatma 
                    yaptırımlarına tabi tutulabilir.
                </p>
                <div class="violation-levels">
                    <div class="violation-level">
                        <h4><i class="fas fa-exclamation-triangle"></i> Uyarı</h4>
                        <p>Hafif ihlaller için e-posta ile uyarı gönderilir.</p>
                    </div>
                    <div class="violation-level">
                        <h4><i class="fas fa-pause-circle"></i> Geçici Askıya Alma</h4>
                        <p>Tekrarlanan ihlaller için 7-30 gün askıya alma.</p>
                    </div>
                    <div class="violation-level">
                        <h4><i class="fas fa-ban"></i> Kalıcı Kapatma</h4>
                        <p>Ciddi ihlaller için hesabın kalıcı olarak kapatılması.</p>
                    </div>
                </div>
            </section>

            <!-- Değişiklikler -->
            <section class="terms-section">
                <h2>10. Kullanım Şartlarında Değişiklikler</h2>
                <p>
                    DOBİEN, bu kullanım şartlarını dilediği zaman değiştirme hakkını saklı tutar. 
                    Önemli değişiklikler e-posta ve platform bildirimleri ile duyurulur.
                </p>
                <ul class="terms-list">
                    <li>Değişiklikler yayınlandığı tarihte yürürlüğe girer.</li>
                    <li>Kullanmaya devam etmeniz değişiklikleri kabul ettiğiniz anlamına gelir.</li>
                    <li>Güncel şartları düzenli olarak kontrol etmeniz önerilir.</li>
                </ul>
            </section>

            <!-- İletişim -->
            <section class="terms-section">
                <h2>11. İletişim ve Yasal Süreç</h2>
                <p>
                    Kullanım şartları ile ilgili sorularınız için bizimle iletişime geçebilirsiniz:
                </p>
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>E-posta: yasal@dobien.com</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span>Telefon: +90 (212) 123 45 67</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Adres: DOBİEN Video Platform, İstanbul, Türkiye</span>
                    </div>
                </div>
                
                <p class="legal-jurisdiction">
                    Bu kullanım şartları Türkiye Cumhuriyeti yasalarına tabidir. Herhangi bir uyuşmazlık 
                    durumunda İstanbul mahkemeleri yetkilidir.
                </p>
            </section>

            <!-- Kabul -->
            <section class="terms-section acceptance">
                <h2>12. Kabul Beyanı</h2>
                <div class="acceptance-box">
                    <p>
                        <strong>Bu kullanım şartlarını okudum, anladım ve kabul ediyorum.</strong>
                    </p>
                    <p>
                        Platform'u kullanmaya devam ederek bu şartların tamamını kabul etmiş sayılırsınız. 
                        Bu şartları kabul etmiyorsanız, lütfen platform kullanımını sonlandırınız.
                    </p>
                    <div class="acceptance-date">
                        Yürürlük tarihi: <?php echo date('d.m.Y'); ?>
                    </div>
                </div>
            </section>
        </div>

        <!-- Alt Navigasyon -->
        <div class="bottom-navigation">
            <a href="<?php echo siteUrl('gizlilik-politikasi.php'); ?>" class="nav-link">
                <i class="fas fa-shield-alt"></i>
                Gizlilik Politikası
            </a>
            <a href="<?php echo siteUrl('iletisim.php'); ?>" class="nav-link">
                <i class="fas fa-envelope"></i>
                İletişim
            </a>
            <a href="<?php echo siteUrl(); ?>" class="nav-link primary">
                <i class="fas fa-home"></i>
                Ana Sayfa
            </a>
        </div>
    </div>
</div>

<style>
/* Kullanım Şartları Sayfası Stilleri */
.terms-page {
    max-width: 1000px;
    margin: 0 auto;
    line-height: 1.6;
}

.page-header {
    text-align: center;
    margin-bottom: 50px;
    padding: 40px 0;
    border-bottom: 2px solid rgba(255, 255, 255, 0.1);
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
    margin: 0 0 20px;
}

.last-updated {
    color: var(--primary-color);
    font-size: 0.9rem;
    background: rgba(255, 193, 7, 0.1);
    padding: 8px 16px;
    border-radius: 20px;
    display: inline-block;
}

.terms-content {
    color: rgba(255, 255, 255, 0.9);
}

.terms-section {
    margin-bottom: 40px;
    padding: 30px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 15px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.terms-section h2 {
    color: #fff;
    font-size: 1.5rem;
    margin: 0 0 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--primary-color);
}

.terms-section p {
    margin-bottom: 15px;
    line-height: 1.7;
}

.terms-list {
    padding-left: 20px;
    margin: 15px 0;
}

.terms-list li {
    margin-bottom: 8px;
    position: relative;
}

.terms-list li::marker {
    color: var(--primary-color);
}

.definition-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.definition-item {
    padding: 15px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    border-left: 4px solid var(--primary-color);
}

.definition-item strong {
    color: var(--primary-color);
}

.membership-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.membership-tier {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    padding: 20px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.membership-tier h3 {
    color: var(--primary-color);
    margin: 0 0 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.membership-tier ul {
    margin: 0;
    padding-left: 20px;
}

.membership-tier li {
    margin-bottom: 8px;
    color: rgba(255, 255, 255, 0.8);
}

.payment-terms {
    margin-top: 30px;
    padding: 20px;
    background: rgba(255, 193, 7, 0.1);
    border-radius: 10px;
    border: 1px solid rgba(255, 193, 7, 0.3);
}

.payment-terms h3 {
    color: var(--primary-color);
    margin: 0 0 15px;
}

.rules-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
    margin: 20px 0;
}

.rule-category {
    padding: 20px;
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.rule-category:first-child {
    background: rgba(40, 167, 69, 0.1);
    border-color: rgba(40, 167, 69, 0.3);
}

.rule-category.prohibited {
    background: rgba(220, 53, 69, 0.1);
    border-color: rgba(220, 53, 69, 0.3);
}

.rule-category h3 {
    margin: 0 0 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.rule-category:first-child h3 {
    color: #28a745;
}

.rule-category.prohibited h3 {
    color: #dc3545;
}

.rule-category ul {
    margin: 0;
    padding-left: 20px;
}

.rule-category li {
    margin-bottom: 8px;
    color: rgba(255, 255, 255, 0.8);
}

.violation-levels {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.violation-level {
    padding: 20px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    text-align: center;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.violation-level h4 {
    color: var(--primary-color);
    margin: 0 0 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.violation-level p {
    margin: 0;
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.8);
}

.contact-info {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin: 20px 0;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 10px;
    color: rgba(255, 255, 255, 0.8);
}

.contact-item i {
    color: var(--primary-color);
    width: 20px;
}

.legal-jurisdiction {
    margin-top: 20px;
    padding: 15px;
    background: rgba(255, 193, 7, 0.1);
    border-radius: 8px;
    border-left: 4px solid var(--primary-color);
    font-size: 0.9rem;
}

.acceptance {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(230, 126, 34, 0.1)) !important;
    border: 2px solid var(--primary-color) !important;
}

.acceptance-box {
    text-align: center;
    padding: 20px;
}

.acceptance-box p:first-child {
    font-size: 1.1rem;
    color: var(--primary-color);
    margin-bottom: 15px;
}

.acceptance-date {
    margin-top: 20px;
    padding: 10px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 6px;
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.8);
}

.bottom-navigation {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin: 50px 0;
    padding: 30px 0;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.nav-link {
    padding: 12px 24px;
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
    text-decoration: none;
    border-radius: 8px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.nav-link:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
}

.nav-link.primary {
    background: var(--primary-color);
    color: #000;
    border-color: var(--primary-color);
}

.nav-link.primary:hover {
    background: #e67e22;
}

/* Responsive */
@media (max-width: 768px) {
    .page-title {
        font-size: 2rem;
        flex-direction: column;
        gap: 10px;
    }
    
    .terms-section {
        padding: 20px;
    }
    
    .membership-info {
        grid-template-columns: 1fr;
    }
    
    .rules-grid {
        grid-template-columns: 1fr;
    }
    
    .violation-levels {
        grid-template-columns: 1fr;
    }
    
    .bottom-navigation {
        flex-direction: column;
        align-items: center;
    }
}
</style>

<?php include 'includes/footer.php'; ?>