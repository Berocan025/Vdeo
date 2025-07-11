<?php
/**
 * DOBİEN Video Platform - Gizlilik Politikası
 * Geliştirici: DOBİEN
 * Tüm Hakları Saklıdır © DOBİEN
 */

require_once 'includes/config.php';

$page_title = "Gizlilik Politikası";
$page_description = "DOBİEN Video Platform gizlilik politikası. Kişisel verilerinizin nasıl korunduğunu öğrenin.";
$page_keywords = "gizlilik politikası, veri koruma, DOBİEN, güvenlik";

include 'includes/header.php';
?>

<div class="container">
    <div class="privacy-page">
        <!-- Sayfa Başlığı -->
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-shield-alt"></i>
                Gizlilik Politikası
            </h1>
            <p class="page-subtitle">Kişisel verilerinizin korunması bizim için önceliklidir</p>
            <div class="last-updated">
                Son güncellenme: <?php echo date('d.m.Y'); ?>
            </div>
        </div>

        <div class="privacy-content">
            <!-- Giriş -->
            <section class="privacy-section">
                <h2>1. Giriş ve Kapsam</h2>
                <p>
                    DOBİEN Video Platform olarak, kullanıcılarımızın gizliliğini korumak bizim için en önemli 
                    önceliklerden biridir. Bu gizlilik politikası, kişisel verilerinizin nasıl toplandığını, 
                    kullanıldığını, saklandığını ve korunduğunu açıklamaktadır.
                </p>
                <p>
                    Bu politika, 6698 sayılı Kişisel Verilerin Korunması Kanunu (KVKK) ve ilgili mevzuata 
                    uygun olarak hazırlanmıştır. Platform'u kullanarak bu politikayı kabul etmiş sayılırsınız.
                </p>
                
                <div class="highlight-box">
                    <h3><i class="fas fa-info-circle"></i> Önemli Bilgi</h3>
                    <p>
                        Kişisel verileriniz hiçbir zaman üçüncü kişilerle paylaşılmaz ve sadece belirtilen 
                        amaçlar doğrultusunda kullanılır.
                    </p>
                </div>
            </section>

            <!-- Toplanan Veriler -->
            <section class="privacy-section">
                <h2>2. Toplanan Kişisel Veriler</h2>
                <p>Platform'umuzda aşağıdaki kişisel veriler toplanabilir:</p>
                
                <div class="data-categories">
                    <div class="data-category">
                        <h3><i class="fas fa-user"></i> Kimlik Bilgileri</h3>
                        <ul>
                            <li>Ad ve soyad</li>
                            <li>E-posta adresi</li>
                            <li>Doğum tarihi (yaş doğrulama için)</li>
                            <li>Telefon numarası (isteğe bağlı)</li>
                        </ul>
                    </div>
                    
                    <div class="data-category">
                        <h3><i class="fas fa-credit-card"></i> Ödeme Bilgileri</h3>
                        <ul>
                            <li>Ödeme yöntemi bilgileri</li>
                            <li>Fatura adresi</li>
                            <li>İşlem geçmişi</li>
                            <li>Abonelik durumu</li>
                        </ul>
                    </div>
                    
                    <div class="data-category">
                        <h3><i class="fas fa-chart-bar"></i> Kullanım Verileri</h3>
                        <ul>
                            <li>İzleme geçmişi</li>
                            <li>Tercihler ve favoriler</li>
                            <li>Arama sorguları</li>
                            <li>Platform kullanım istatistikleri</li>
                        </ul>
                    </div>
                    
                    <div class="data-category">
                        <h3><i class="fas fa-laptop"></i> Teknik Veriler</h3>
                        <ul>
                            <li>IP adresi</li>
                            <li>Tarayıcı bilgileri</li>
                            <li>Cihaz bilgileri</li>
                            <li>Çerez verileri</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Veri Toplama Amaçları -->
            <section class="privacy-section">
                <h2>3. Veri Toplama Amaçları</h2>
                <p>Kişisel verileriniz aşağıdaki amaçlarla toplanır ve işlenir:</p>
                
                <div class="purposes-grid">
                    <div class="purpose-item">
                        <div class="purpose-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <h3>Hesap Yönetimi</h3>
                        <p>Kullanıcı hesabı oluşturma, kimlik doğrulama ve hesap güvenliği sağlama</p>
                    </div>
                    
                    <div class="purpose-item">
                        <div class="purpose-icon">
                            <i class="fas fa-play-circle"></i>
                        </div>
                        <h3>Hizmet Sunumu</h3>
                        <p>Video içerik sunumu, kalite ayarları ve kişiselleştirilmiş deneyim</p>
                    </div>
                    
                    <div class="purpose-item">
                        <div class="purpose-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <h3>Ödeme İşlemleri</h3>
                        <p>Abonelik ödemeleri, fatura oluşturma ve ödeme güvenliği</p>
                    </div>
                    
                    <div class="purpose-item">
                        <div class="purpose-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3>Müşteri Desteği</h3>
                        <p>Teknik destek, şikayet çözümü ve iletişim</p>
                    </div>
                    
                    <div class="purpose-item">
                        <div class="purpose-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3>Platform İyileştirme</h3>
                        <p>Hizmet kalitesini artırma, yeni özellikler geliştirme</p>
                    </div>
                    
                    <div class="purpose-item">
                        <div class="purpose-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3>Güvenlik</h3>
                        <p>Dolandırıcılık önleme, hesap güvenliği ve sistem koruması</p>
                    </div>
                </div>
            </section>

            <!-- Çerezler -->
            <section class="privacy-section">
                <h2>4. Çerezler (Cookies)</h2>
                <p>
                    Platform'umuz deneyiminizi iyileştirmek için çerezler kullanır. Çerezler, web sitesinin 
                    daha iyi çalışmasını sağlayan küçük veri dosyalarıdır.
                </p>
                
                <div class="cookie-types">
                    <div class="cookie-type">
                        <h3><i class="fas fa-cog"></i> Zorunlu Çerezler</h3>
                        <p>Platform'un temel işlevleri için gerekli olan çerezlerdir. Bu çerezler devre dışı bırakılamaz.</p>
                        <ul>
                            <li>Oturum yönetimi</li>
                            <li>Güvenlik ayarları</li>
                            <li>Dil tercihleri</li>
                        </ul>
                    </div>
                    
                    <div class="cookie-type">
                        <h3><i class="fas fa-chart-pie"></i> Analitik Çerezler</h3>
                        <p>Platform kullanımını anlamamızı sağlayan istatistiksel veriler toplar.</p>
                        <ul>
                            <li>Sayfa görüntüleme sayıları</li>
                            <li>Kullanıcı davranış analizi</li>
                            <li>Performans ölçümleri</li>
                        </ul>
                    </div>
                    
                    <div class="cookie-type">
                        <h3><i class="fas fa-heart"></i> Tercih Çerezleri</h3>
                        <p>Kişiselleştirilmiş deneyim sunmak için kullanılan çerezlerdir.</p>
                        <ul>
                            <li>Video kalite tercihleri</li>
                            <li>Tema ayarları</li>
                            <li>İzleme geçmişi</li>
                        </ul>
                    </div>
                </div>
                
                <div class="cookie-control">
                    <h3>Çerez Kontrolü</h3>
                    <p>
                        Tarayıcı ayarlarından çerezleri yönetebilirsiniz. Ancak bazı çerezleri devre dışı 
                        bırakmanız platform işlevselliğini etkileyebilir.
                    </p>
                </div>
            </section>

            <!-- Veri Güvenliği -->
            <section class="privacy-section">
                <h2>5. Veri Güvenliği ve Koruma</h2>
                <p>
                    Kişisel verilerinizin güvenliği için endüstri standardı güvenlik önlemleri alınmıştır:
                </p>
                
                <div class="security-measures">
                    <div class="security-item">
                        <i class="fas fa-lock"></i>
                        <h3>SSL Şifreleme</h3>
                        <p>Tüm veri iletimi 256-bit SSL şifreleme ile korunur</p>
                    </div>
                    
                    <div class="security-item">
                        <i class="fas fa-server"></i>
                        <h3>Güvenli Sunucular</h3>
                        <p>Veriler güvenli veri merkezlerinde saklanır</p>
                    </div>
                    
                    <div class="security-item">
                        <i class="fas fa-eye-slash"></i>
                        <h3>Şifre Koruması</h3>
                        <p>Şifreler güçlü algoritma ile hash'lenir</p>
                    </div>
                    
                    <div class="security-item">
                        <i class="fas fa-user-shield"></i>
                        <h3>Erişim Kontrolü</h3>
                        <p>Veriye erişim sıkı kontrollerle sınırlandırılır</p>
                    </div>
                    
                    <div class="security-item">
                        <i class="fas fa-backup"></i>
                        <h3>Düzenli Yedekleme</h3>
                        <p>Veriler düzenli olarak yedeklenir</p>
                    </div>
                    
                    <div class="security-item">
                        <i class="fas fa-shield-virus"></i>
                        <h3>Güvenlik Taramaları</h3>
                        <p>Sistem güvenliği sürekli izlenir</p>
                    </div>
                </div>
            </section>

            <!-- Veri Saklama -->
            <section class="privacy-section">
                <h2>6. Veri Saklama Süreleri</h2>
                <p>
                    Kişisel verileriniz yalnızca gerekli olan süre boyunca saklanır:
                </p>
                
                <div class="retention-table">
                    <div class="retention-row header">
                        <div class="data-type">Veri Türü</div>
                        <div class="retention-period">Saklama Süresi</div>
                        <div class="reason">Sebep</div>
                    </div>
                    
                    <div class="retention-row">
                        <div class="data-type">Hesap Bilgileri</div>
                        <div class="retention-period">Hesap silinene kadar</div>
                        <div class="reason">Hizmet sunumu</div>
                    </div>
                    
                    <div class="retention-row">
                        <div class="data-type">İzleme Geçmişi</div>
                        <div class="retention-period">2 yıl</div>
                        <div class="reason">Öneri algoritması</div>
                    </div>
                    
                    <div class="retention-row">
                        <div class="data-type">Ödeme Bilgileri</div>
                        <div class="retention-period">Yasal zorunluluk süresi</div>
                        <div class="reason">Mali mevzuat</div>
                    </div>
                    
                    <div class="retention-row">
                        <div class="data-type">Log Kayıtları</div>
                        <div class="retention-period">6 ay</div>
                        <div class="reason">Güvenlik analizi</div>
                    </div>
                </div>
            </section>

            <!-- Kullanıcı Hakları -->
            <section class="privacy-section">
                <h2>7. Kullanıcı Hakları</h2>
                <p>
                    KVKK kapsamında aşağıdaki haklara sahipsiniz:
                </p>
                
                <div class="rights-grid">
                    <div class="right-item">
                        <i class="fas fa-search"></i>
                        <h3>Bilgi Alma Hakkı</h3>
                        <p>Hangi verilerinizin işlendiğini öğrenme hakkınız</p>
                    </div>
                    
                    <div class="right-item">
                        <i class="fas fa-edit"></i>
                        <h3>Düzeltme Hakkı</h3>
                        <p>Yanlış verilerin düzeltilmesini isteme hakkınız</p>
                    </div>
                    
                    <div class="right-item">
                        <i class="fas fa-trash"></i>
                        <h3>Silme Hakkı</h3>
                        <p>Verilerinizin silinmesini talep etme hakkınız</p>
                    </div>
                    
                    <div class="right-item">
                        <i class="fas fa-ban"></i>
                        <h3>İşleme İtiraz Hakkı</h3>
                        <p>Veri işlenmesine itiraz etme hakkınız</p>
                    </div>
                    
                    <div class="right-item">
                        <i class="fas fa-download"></i>
                        <h3>Veri Taşınabilirliği</h3>
                        <p>Verilerinizi taşınabilir formatta alma hakkınız</p>
                    </div>
                    
                    <div class="right-item">
                        <i class="fas fa-gavel"></i>
                        <h3>Şikayet Hakkı</h3>
                        <p>Denetim makamlarına şikayet etme hakkınız</p>
                    </div>
                </div>
                
                <div class="rights-contact">
                    <h3>Haklarınızı Kullanmak İçin</h3>
                    <p>
                        Yukarıdaki haklarınızı kullanmak için <strong>gizlilik@dobien.com</strong> 
                        adresine e-posta gönderebilir veya iletişim formunu kullanabilirsiniz.
                    </p>
                </div>
            </section>

            <!-- Üçüncü Taraf Hizmetler -->
            <section class="privacy-section">
                <h2>8. Üçüncü Taraf Hizmetler</h2>
                <p>
                    Platform'umuz sınırlı sayıda güvenilir üçüncü taraf hizmeti kullanır:
                </p>
                
                <div class="third-party-services">
                    <div class="service-item">
                        <h3><i class="fas fa-credit-card"></i> Ödeme Sağlayıcıları</h3>
                        <p>Güvenli ödeme işlemleri için sertifikalı ödeme sağlayıcıları kullanılır</p>
                    </div>
                    
                    <div class="service-item">
                        <h3><i class="fas fa-chart-bar"></i> Analitik Hizmetler</h3>
                        <p>Platform performansını ölçmek için anonimleştirilmiş analitik veriler kullanılır</p>
                    </div>
                    
                    <div class="service-item">
                        <h3><i class="fas fa-cloud"></i> Bulut Hizmetleri</h3>
                        <p>Video içerik dağıtımı için güvenli bulut hizmetleri kullanılır</p>
                    </div>
                </div>
            </section>

            <!-- Değişiklikler -->
            <section class="privacy-section">
                <h2>9. Politika Değişiklikleri</h2>
                <p>
                    Bu gizlilik politikası gerektiğinde güncellenebilir. Önemli değişiklikler 
                    e-posta ve platform bildirimleri ile duyurulur.
                </p>
                
                <div class="policy-updates">
                    <h3>Bildirim Yöntemleri:</h3>
                    <ul>
                        <li>E-posta bildirimi</li>
                        <li>Platform içi bildirim</li>
                        <li>Ana sayfa duyurusu</li>
                        <li>Bu sayfada güncellenme tarihi</li>
                    </ul>
                </div>
            </section>

            <!-- İletişim -->
            <section class="privacy-section">
                <h2>10. İletişim</h2>
                <p>
                    Gizlilik politikası ile ilgili sorularınız için bizimle iletişime geçebilirsiniz:
                </p>
                
                <div class="contact-details">
                    <div class="contact-method">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <h3>E-posta</h3>
                            <p>gizlilik@dobien.com</p>
                        </div>
                    </div>
                    
                    <div class="contact-method">
                        <i class="fas fa-phone"></i>
                        <div>
                            <h3>Telefon</h3>
                            <p>+90 (212) 123 45 67</p>
                        </div>
                    </div>
                    
                    <div class="contact-method">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <h3>Adres</h3>
                            <p>DOBİEN Video Platform<br>İstanbul, Türkiye</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Alt Navigasyon -->
        <div class="bottom-navigation">
            <a href="<?php echo siteUrl('kullanim-sartlari.php'); ?>" class="nav-link">
                <i class="fas fa-file-contract"></i>
                Kullanım Şartları
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
/* Gizlilik Politikası Sayfası Stilleri */
.privacy-page {
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

.privacy-content {
    color: rgba(255, 255, 255, 0.9);
}

.privacy-section {
    margin-bottom: 40px;
    padding: 30px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 15px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.privacy-section h2 {
    color: #fff;
    font-size: 1.5rem;
    margin: 0 0 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--primary-color);
}

.privacy-section p {
    margin-bottom: 15px;
    line-height: 1.7;
}

.highlight-box {
    background: rgba(255, 193, 7, 0.1);
    border: 1px solid rgba(255, 193, 7, 0.3);
    border-radius: 10px;
    padding: 20px;
    margin: 20px 0;
}

.highlight-box h3 {
    color: var(--primary-color);
    margin: 0 0 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.data-categories {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.data-category {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    padding: 20px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.data-category h3 {
    color: var(--primary-color);
    margin: 0 0 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.data-category ul {
    margin: 0;
    padding-left: 20px;
}

.data-category li {
    margin-bottom: 5px;
    color: rgba(255, 255, 255, 0.8);
}

.purposes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin: 25px 0;
}

.purpose-item {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    padding: 25px;
    text-align: center;
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.purpose-item:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateY(-3px);
}

.purpose-icon {
    width: 60px;
    height: 60px;
    background: rgba(255, 193, 7, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
}

.purpose-icon i {
    font-size: 1.5rem;
    color: var(--primary-color);
}

.purpose-item h3 {
    color: #fff;
    margin: 0 0 10px;
    font-size: 1.1rem;
}

.purpose-item p {
    color: rgba(255, 255, 255, 0.7);
    margin: 0;
    font-size: 0.9rem;
    line-height: 1.4;
}

.cookie-types {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.cookie-type {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    padding: 20px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.cookie-type h3 {
    color: var(--primary-color);
    margin: 0 0 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.cookie-type ul {
    margin: 10px 0 0;
    padding-left: 20px;
}

.cookie-type li {
    margin-bottom: 5px;
    color: rgba(255, 255, 255, 0.8);
}

.cookie-control {
    margin-top: 25px;
    padding: 20px;
    background: rgba(255, 193, 7, 0.1);
    border-radius: 10px;
    border: 1px solid rgba(255, 193, 7, 0.3);
}

.cookie-control h3 {
    color: var(--primary-color);
    margin: 0 0 10px;
}

.security-measures {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.security-item {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.security-item i {
    font-size: 2rem;
    color: var(--primary-color);
    margin-bottom: 10px;
}

.security-item h3 {
    color: #fff;
    margin: 0 0 8px;
    font-size: 1rem;
}

.security-item p {
    color: rgba(255, 255, 255, 0.7);
    margin: 0;
    font-size: 0.8rem;
    line-height: 1.3;
}

.retention-table {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    overflow: hidden;
    margin: 20px 0;
}

.retention-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 20px;
    padding: 15px 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.retention-row.header {
    background: rgba(255, 193, 7, 0.1);
    font-weight: 600;
    color: var(--primary-color);
}

.retention-row:last-child {
    border-bottom: none;
}

.rights-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.right-item {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.right-item i {
    font-size: 1.5rem;
    color: var(--primary-color);
    margin-bottom: 10px;
}

.right-item h3 {
    color: #fff;
    margin: 0 0 8px;
    font-size: 1rem;
}

.right-item p {
    color: rgba(255, 255, 255, 0.7);
    margin: 0;
    font-size: 0.8rem;
    line-height: 1.3;
}

.rights-contact {
    margin-top: 25px;
    padding: 20px;
    background: rgba(255, 193, 7, 0.1);
    border-radius: 10px;
    border: 1px solid rgba(255, 193, 7, 0.3);
}

.rights-contact h3 {
    color: var(--primary-color);
    margin: 0 0 10px;
}

.third-party-services {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin: 20px 0;
}

.service-item {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    padding: 20px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.service-item h3 {
    color: var(--primary-color);
    margin: 0 0 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.policy-updates {
    background: rgba(255, 193, 7, 0.1);
    border-radius: 10px;
    padding: 20px;
    margin: 20px 0;
    border: 1px solid rgba(255, 193, 7, 0.3);
}

.policy-updates h3 {
    color: var(--primary-color);
    margin: 0 0 15px;
}

.policy-updates ul {
    margin: 0;
    padding-left: 20px;
}

.policy-updates li {
    margin-bottom: 5px;
    color: rgba(255, 255, 255, 0.8);
}

.contact-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
    margin: 20px 0;
}

.contact-method {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    padding: 20px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.contact-method i {
    font-size: 1.5rem;
    color: var(--primary-color);
    margin-top: 5px;
}

.contact-method h3 {
    color: #fff;
    margin: 0 0 5px;
    font-size: 1rem;
}

.contact-method p {
    color: rgba(255, 255, 255, 0.8);
    margin: 0;
    font-size: 0.9rem;
    line-height: 1.4;
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
    
    .privacy-section {
        padding: 20px;
    }
    
    .data-categories,
    .purposes-grid,
    .cookie-types,
    .security-measures,
    .rights-grid {
        grid-template-columns: 1fr;
    }
    
    .retention-row {
        grid-template-columns: 1fr;
        gap: 5px;
    }
    
    .bottom-navigation {
        flex-direction: column;
        align-items: center;
    }
}
</style>

<?php include 'includes/footer.php'; ?>