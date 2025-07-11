<?php
/**
 * DOBİEN Video Platform - Hakkımızda Sayfası
 * Geliştirici: DOBİEN
 * Tüm Hakları Saklıdır © DOBİEN
 */

require_once 'includes/config.php';

$page_title = "Hakkımızda";
$page_description = "DOBİEN Video Platform hakkında bilgi edinin. Misyonumuz, vizyonumuz ve takımımız hakkında detaylı bilgiler.";
$page_keywords = "hakkımızda, DOBİEN, video platform, misyon, vizyon";

include 'includes/header.php';
?>

<div class="container">
    <div class="about-page">
        <!-- Sayfa Başlığı -->
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-info-circle"></i>
                Hakkımızda
            </h1>
            <p class="page-subtitle">DOBİEN Video Platform'u tanıyın</p>
        </div>

        <!-- Ana İçerik -->
        <div class="about-content">
            <!-- Misyon & Vizyon -->
            <section class="about-section">
                <div class="section-grid">
                    <div class="content-block">
                        <div class="content-icon">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h2>Misyonumuz</h2>
                        <p>
                            DOBİEN olarak, en kaliteli video içeriklerini kullanıcılarımızla buluşturmak ve 
                            herkes için erişilebilir bir dijital platform sunmak misyonumuzdur. 
                            Teknoloji ve yaratıcılığı birleştirerek, sınırsız eğlence deneyimi yaşatıyoruz.
                        </p>
                    </div>
                    
                    <div class="content-block">
                        <div class="content-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h2>Vizyonumuz</h2>
                        <p>
                            Dünyanın önde gelen video platformlarından biri olmak ve kullanıcılarımıza 
                            4K kalitesinde, kesintisiz ve güvenli video izleme deneyimi sunmak vizyonumuzdur. 
                            İnovasyon ve kullanıcı memnuniyeti odaklı çalışmalarımızla sektörde öncü olmayı hedefliyoruz.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Platform Özellikleri -->
            <section class="about-section">
                <h2 class="section-title">
                    <i class="fas fa-star"></i>
                    Platform Özelliklerimiz
                </h2>
                
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-hd-video"></i>
                        </div>
                        <h3>4K Ultra HD Kalite</h3>
                        <p>Premium üyelerimiz için 4K Ultra HD kalitede video deneyimi sunuyoruz.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3>Güvenli Platform</h3>
                        <p>SSL şifreleme ve gelişmiş güvenlik önlemleri ile verilerinizi koruyoruz.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3>Mobil Uyumlu</h3>
                        <p>Tüm cihazlarda mükemmel çalışan responsive tasarım.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-crown"></i>
                        </div>
                        <h3>Üyelik Seviyeleri</h3>
                        <p>Farklı ihtiyaçlara yönelik Standart, VIP ve Premium üyelik seçenekleri.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3>Gelişmiş Arama</h3>
                        <p>Akıllı filtreleme ve kategori sistemi ile istediğiniz içeriği kolayca bulun.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3>7/24 Erişim</h3>
                        <p>Dilediğiniz zaman, dilediğiniz yerden platformumuza erişebilirsiniz.</p>
                    </div>
                </div>
            </section>

            <!-- Değerlerimiz -->
            <section class="about-section">
                <h2 class="section-title">
                    <i class="fas fa-heart"></i>
                    Değerlerimiz
                </h2>
                
                <div class="values-list">
                    <div class="value-item">
                        <div class="value-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="value-content">
                            <h3>Kullanıcı Odaklılık</h3>
                            <p>Kullanıcılarımızın memnuniyeti bizim için her şeyden önemlidir. Geri bildirimlerinizi dinler ve platformumuzu sürekli geliştiririz.</p>
                        </div>
                    </div>
                    
                    <div class="value-item">
                        <div class="value-icon">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <div class="value-content">
                            <h3>İnovasyon</h3>
                            <p>Teknolojinin sunduğu yenilikleri takip eder ve platformumuza entegre ederek en iyi deneyimi sunarız.</p>
                        </div>
                    </div>
                    
                    <div class="value-item">
                        <div class="value-icon">
                            <i class="fas fa-medal"></i>
                        </div>
                        <div class="value-content">
                            <h3>Kalite</h3>
                            <p>Her alanda en yüksek kalite standartlarını benimser ve asla kaliteden ödün vermeyiz.</p>
                        </div>
                    </div>
                    
                    <div class="value-item">
                        <div class="value-icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <div class="value-content">
                            <h3>Güvenilirlik</h3>
                            <p>Kullanıcılarımızın güvenini kazanmak ve korumak için şeffaf ve dürüst olmaya özen gösteririz.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- İstatistikler -->
            <section class="about-section">
                <h2 class="section-title">
                    <i class="fas fa-chart-line"></i>
                    Platform İstatistikleri
                </h2>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number">1000+</div>
                        <div class="stat-label">Aktif Kullanıcı</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">Video İçeriği</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-number">50+</div>
                        <div class="stat-label">Farklı Kategori</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-number">99.9%</div>
                        <div class="stat-label">Uptime Garantisi</div>
                    </div>
                </div>
            </section>

            <!-- İletişim Çağrısı -->
            <section class="about-section">
                <div class="contact-cta">
                    <div class="cta-content">
                        <h2>Bizimle İletişime Geçin</h2>
                        <p>Sorularınız, önerileriniz veya geri bildirimleriniz için bize ulaşmaktan çekinmeyin.</p>
                        <div class="cta-buttons">
                            <a href="<?php echo siteUrl('iletisim.php'); ?>" class="btn btn-primary">
                                <i class="fas fa-envelope"></i>
                                İletişim
                            </a>
                            <a href="<?php echo siteUrl('uyelik-yukselt.php'); ?>" class="btn btn-outline">
                                <i class="fas fa-crown"></i>
                                Üyelik Yükselt
                            </a>
                        </div>
                    </div>
                    <div class="cta-image">
                        <i class="fas fa-comments"></i>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<style>
/* Hakkımızda Sayfası Stilleri */
.about-page {
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
}

.about-section {
    margin-bottom: 60px;
}

.section-title {
    color: #fff;
    font-size: 2rem;
    margin: 0 0 30px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-title i {
    color: var(--primary-color);
}

.section-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 30px;
}

.content-block {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 15px;
    padding: 30px;
    text-align: center;
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.content-block:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateY(-5px);
}

.content-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--primary-color), #e67e22);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.content-icon i {
    font-size: 2rem;
    color: #000;
}

.content-block h2 {
    color: #fff;
    margin: 0 0 15px;
    font-size: 1.5rem;
}

.content-block p {
    color: rgba(255, 255, 255, 0.8);
    line-height: 1.6;
    margin: 0;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
}

.feature-card {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    padding: 25px;
    text-align: center;
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.feature-card:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateY(-3px);
}

.feature-icon {
    width: 60px;
    height: 60px;
    background: rgba(255, 193, 7, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
}

.feature-icon i {
    font-size: 1.5rem;
    color: var(--primary-color);
}

.feature-card h3 {
    color: #fff;
    margin: 0 0 10px;
    font-size: 1.1rem;
}

.feature-card p {
    color: rgba(255, 255, 255, 0.7);
    margin: 0;
    font-size: 0.9rem;
    line-height: 1.5;
}

.values-list {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.value-item {
    display: flex;
    gap: 20px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    padding: 25px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.value-item:hover {
    background: rgba(255, 255, 255, 0.08);
}

.value-icon {
    width: 50px;
    height: 50px;
    background: rgba(255, 193, 7, 0.2);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.value-icon i {
    font-size: 1.2rem;
    color: var(--primary-color);
}

.value-content h3 {
    color: #fff;
    margin: 0 0 8px;
    font-size: 1.2rem;
}

.value-content p {
    color: rgba(255, 255, 255, 0.8);
    margin: 0;
    line-height: 1.5;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 25px;
}

.stat-card {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(230, 126, 34, 0.1));
    border-radius: 12px;
    padding: 30px;
    text-align: center;
    border: 1px solid rgba(255, 193, 7, 0.3);
}

.stat-number {
    color: var(--primary-color);
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.stat-label {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1rem;
    font-weight: 500;
}

.contact-cta {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(230, 126, 34, 0.1));
    border-radius: 15px;
    padding: 40px;
    display: flex;
    align-items: center;
    gap: 40px;
    border: 1px solid rgba(255, 193, 7, 0.2);
}

.cta-content {
    flex: 1;
}

.cta-content h2 {
    color: #fff;
    margin: 0 0 15px;
    font-size: 1.8rem;
}

.cta-content p {
    color: rgba(255, 255, 255, 0.8);
    margin: 0 0 25px;
    line-height: 1.6;
}

.cta-buttons {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.cta-image {
    width: 120px;
    height: 120px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.cta-image i {
    font-size: 3rem;
    color: var(--primary-color);
}

/* Responsive */
@media (max-width: 768px) {
    .page-title {
        font-size: 2rem;
        flex-direction: column;
        gap: 10px;
    }
    
    .section-grid {
        grid-template-columns: 1fr;
    }
    
    .features-grid {
        grid-template-columns: 1fr;
    }
    
    .value-item {
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .contact-cta {
        flex-direction: column;
        text-align: center;
        gap: 25px;
    }
    
    .cta-buttons {
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .cta-buttons {
        flex-direction: column;
    }
}
</style>

<?php include 'includes/footer.php'; ?>