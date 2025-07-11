<?php
/**
 * DOBİEN Video Platform - Premium Avantajlar Sayfası
 * Geliştirici: DOBİEN
 * Premium Üyelik Avantajları
 * Tüm Hakları Saklıdır © DOBİEN
 */

require_once 'includes/config.php';

$page_title = "Premium Avantajlar";
$page_description = "DOBİEN Video Platform Premium üyelik avantajları - 4K video kalitesi, sınırsız erişim ve özel içerikler";
$page_keywords = "premium avantajlar, 4k video, vip üyelik, premium üyelik, DOBİEN";

include 'includes/header.php';
?>

<div class="container">
    <!-- Hero Section -->
    <section class="advantages-hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1>
                    <i class="fas fa-crown"></i>
                    Premium Avantajları Keşfedin
                </h1>
                <p class="hero-subtitle">
                    En kaliteli video deneyimi için tasarlanmış özel avantajlarla video izleme deneyiminizi zirveye taşıyın
                </p>
            </div>
            <div class="hero-image">
                <div class="floating-cards">
                    <div class="advantage-card">
                        <i class="fas fa-video"></i>
                        <span>4K Ultra HD</span>
                    </div>
                    <div class="advantage-card">
                        <i class="fas fa-infinity"></i>
                        <span>Sınırsız İzleme</span>
                    </div>
                    <div class="advantage-card">
                        <i class="fas fa-ad"></i>
                        <span>Reklamsız</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Üyelik Planları Karşılaştırması -->
    <section class="membership-comparison">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-balance-scale"></i>
                Üyelik Planları Karşılaştırması
            </h2>
            <p>Size en uygun planı seçin ve video deneyiminizi geliştirin</p>
        </div>

        <div class="comparison-table">
            <div class="plan-column basic">
                <div class="plan-header">
                    <h3>
                        <i class="fas fa-user"></i>
                        Ücretsiz
                    </h3>
                    <div class="price">
                        <span class="amount">₺0</span>
                        <span class="period">/ay</span>
                    </div>
                </div>
                <div class="plan-features">
                    <div class="feature">
                        <i class="fas fa-check text-success"></i>
                        <span>720p Video Kalitesi</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check text-success"></i>
                        <span>Sınırlı İçerik Erişimi</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-times text-danger"></i>
                        <span>Reklamlar Var</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-times text-danger"></i>
                        <span>Premium İçerik Yok</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-times text-danger"></i>
                        <span>4K Kalite Yok</span>
                    </div>
                </div>
                <div class="plan-action">
                    <button class="btn btn-outline" disabled>Mevcut Plan</button>
                </div>
            </div>

            <div class="plan-column vip">
                <div class="plan-header">
                    <h3>
                        <i class="fas fa-crown"></i>
                        VIP
                    </h3>
                    <div class="price">
                        <span class="amount">₺19.99</span>
                        <span class="period">/ay</span>
                    </div>
                    <div class="plan-badge">Popüler</div>
                </div>
                <div class="plan-features">
                    <div class="feature">
                        <i class="fas fa-check text-success"></i>
                        <span>1080p Full HD Kalite</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check text-success"></i>
                        <span>VIP İçeriklere Erişim</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check text-success"></i>
                        <span>Daha Az Reklam</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check text-success"></i>
                        <span>Öncelikli Destek</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-times text-danger"></i>
                        <span>Premium İçerik Sınırlı</span>
                    </div>
                </div>
                <div class="plan-action">
                    <a href="<?php echo siteUrl('uyelik-yukselt.php?plan=vip'); ?>" class="btn btn-vip">
                        VIP Ol
                    </a>
                </div>
            </div>

            <div class="plan-column premium recommended">
                <div class="plan-header">
                    <h3>
                        <i class="fas fa-gem"></i>
                        Premium
                    </h3>
                    <div class="price">
                        <span class="amount">₺29.99</span>
                        <span class="period">/ay</span>
                    </div>
                    <div class="plan-badge premium">Önerilen</div>
                </div>
                <div class="plan-features">
                    <div class="feature">
                        <i class="fas fa-check text-success"></i>
                        <span>4K Ultra HD Kalite</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check text-success"></i>
                        <span>Tüm İçeriklere Erişim</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check text-success"></i>
                        <span>Tamamen Reklamsız</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check text-success"></i>
                        <span>Özel Premium İçerikler</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check text-success"></i>
                        <span>7/24 VIP Destek</span>
                    </div>
                </div>
                <div class="plan-action">
                    <a href="<?php echo siteUrl('uyelik-yukselt.php?plan=premium'); ?>" class="btn btn-premium">
                        Premium Ol
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Detaylı Avantajlar -->
    <section class="detailed-advantages">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-star"></i>
                Premium Avantajlar Detayı
            </h2>
        </div>

        <div class="advantages-grid">
            <div class="advantage-item">
                <div class="advantage-icon">
                    <i class="fas fa-video"></i>
                </div>
                <div class="advantage-content">
                    <h3>4K Ultra HD Video Kalitesi</h3>
                    <p>En yüksek video kalitesi ile kristal berraklığında görüntü deneyimi yaşayın. Her detay gözünüzün önünde.</p>
                    <ul>
                        <li>3840x2160 çözünürlük</li>
                        <li>HDR desteği</li>
                        <li>Gelişmiş ses kalitesi</li>
                    </ul>
                </div>
            </div>

            <div class="advantage-item">
                <div class="advantage-icon">
                    <i class="fas fa-infinity"></i>
                </div>
                <div class="advantage-content">
                    <h3>Sınırsız Video İzleme</h3>
                    <p>Hiçbir kısıtlama olmadan, istediğiniz kadar video izleyebilir, favori içeriklerinizi tekrar tekrar izleyebilirsiniz.</p>
                    <ul>
                        <li>Zaman sınırı yok</li>
                        <li>İzleme sayısı limiti yok</li>
                        <li>Offline izleme desteği</li>
                    </ul>
                </div>
            </div>

            <div class="advantage-item">
                <div class="advantage-icon">
                    <i class="fas fa-ad"></i>
                </div>
                <div class="advantage-content">
                    <h3>Tamamen Reklamsız Deneyim</h3>
                    <p>Hiçbir reklam kesintisi olmadan video izleme keyfini çıkarın. Odaklanın, rahatlayın ve izleyin.</p>
                    <ul>
                        <li>Pre-roll reklamları yok</li>
                        <li>Mid-roll reklamları yok</li>
                        <li>Popup reklamları yok</li>
                    </ul>
                </div>
            </div>

            <div class="advantage-item">
                <div class="advantage-icon">
                    <i class="fas fa-gem"></i>
                </div>
                <div class="advantage-content">
                    <h3>Özel Premium İçerikler</h3>
                    <p>Sadece Premium üyeler için hazırlanmış özel içeriklere erişim. En yeni ve en kaliteli videolar önce size gelsin.</p>
                    <ul>
                        <li>Özel çekimler</li>
                        <li>Behind the scenes içerikler</li>
                        <li>Erken erişim videoları</li>
                    </ul>
                </div>
            </div>

            <div class="advantage-item">
                <div class="advantage-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <div class="advantage-content">
                    <h3>7/24 VIP Destek</h3>
                    <p>Her an yanınızda olan profesyonel destek ekibimiz. Sorularınız için hızlı ve etkili çözümler.</p>
                    <ul>
                        <li>Canlı chat desteği</li>
                        <li>Öncelikli ticket sistemi</li>
                        <li>Telefon desteği</li>
                    </ul>
                </div>
            </div>

            <div class="advantage-item">
                <div class="advantage-icon">
                    <i class="fas fa-download"></i>
                </div>
                <div class="advantage-content">
                    <h3>Offline İzleme</h3>
                    <p>Videoları indirerek internet bağlantısı olmadan da izleyebilirsiniz. Seyahat ederken bile eğlence devam eder.</p>
                    <ul>
                        <li>Yüksek kalitede indirme</li>
                        <li>Çoklu cihaz desteği</li>
                        <li>Otomatik senkronizasyon</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Sık Sorulan Sorular -->
    <section class="faq-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-question-circle"></i>
                Sık Sorulan Sorular
            </h2>
        </div>

        <div class="faq-container">
            <div class="faq-item">
                <div class="faq-question">
                    <h3>Premium üyeliği nasıl iptal edebilirim?</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Premium üyeliğinizi istediğiniz zaman hesap ayarlarından iptal edebilirsiniz. İptal etmeniz durumunda mevcut dönem sonuna kadar Premium avantajlarınızdan yararlanmaya devam edersiniz.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <h3>Deneme süresi var mı?</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Evet! Yeni üyelerimiz için 7 günlük ücretsiz deneme süresi sunuyoruz. Bu süre zarfında tüm Premium özelliklerden ücretsiz yararlanabilirsiniz.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <h3>Birden fazla cihazda kullanabilir miyim?</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Premium hesabınızla 5 farklı cihazda aynı anda oturum açabilir ve aynı anda 2 cihazda izleyebilirsiniz.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <h3>VIP'den Premium'a nasıl geçiş yapabilirim?</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>VIP üyeliğiniz varken Premium'a geçiş yapabilirsiniz. Fark ücreti otomatik olarak hesaplanır ve kalan süreniz Premium üyeliğe dönüştürülür.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="final-cta">
        <div class="cta-content">
            <h2>
                <i class="fas fa-rocket"></i>
                Şimdi Premium Deneyimi Başlatın!
            </h2>
            <p>En kaliteli video deneyimi için Premium üyeliğe geçin ve farkı hemen hissedin.</p>
            <div class="cta-buttons">
                <a href="<?php echo siteUrl('uyelik-yukselt.php?plan=premium'); ?>" class="btn btn-premium btn-large">
                    <i class="fas fa-gem"></i>
                    Premium Ol - ₺29.99/ay
                </a>
                <a href="<?php echo siteUrl('uyelik-yukselt.php?plan=vip'); ?>" class="btn btn-vip btn-large">
                    <i class="fas fa-crown"></i>
                    VIP Ol - ₺19.99/ay
                </a>
            </div>
            <p class="guarantee">
                <i class="fas fa-shield-alt"></i>
                7 gün para iade garantisi
            </p>
        </div>
    </section>
</div>

<style>
.advantages-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 80px 0;
    margin-bottom: 50px;
    border-radius: 15px;
    color: white;
}

.hero-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 50px;
}

.hero-text h1 {
    font-size: 3rem;
    margin-bottom: 20px;
    font-weight: 700;
}

.hero-subtitle {
    font-size: 1.2rem;
    opacity: 0.9;
    line-height: 1.6;
}

.floating-cards {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.advantage-card {
    background: rgba(255,255,255,0.1);
    padding: 20px;
    border-radius: 10px;
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    gap: 15px;
    animation: float 3s ease-in-out infinite;
}

.advantage-card:nth-child(2) {
    animation-delay: -1s;
}

.advantage-card:nth-child(3) {
    animation-delay: -2s;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.comparison-table {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin: 50px 0;
}

.plan-column {
    background: var(--card-bg);
    border-radius: 15px;
    padding: 30px;
    border: 2px solid transparent;
    position: relative;
    transition: all 0.3s ease;
}

.plan-column:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}

.plan-column.recommended {
    border-color: #ff6b35;
    transform: scale(1.05);
}

.plan-header h3 {
    font-size: 1.5rem;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.price {
    margin-bottom: 30px;
}

.price .amount {
    font-size: 2.5rem;
    font-weight: 700;
    color: #ff6b35;
}

.price .period {
    font-size: 1rem;
    opacity: 0.7;
}

.plan-badge {
    position: absolute;
    top: -10px;
    left: 50%;
    transform: translateX(-50%);
    background: #ff6b35;
    color: white;
    padding: 5px 20px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.plan-badge.premium {
    background: linear-gradient(45deg, #ff6b35, #f7931e);
}

.plan-features {
    margin-bottom: 30px;
}

.feature {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.text-success { color: #28a745; }
.text-danger { color: #dc3545; }

.btn-vip {
    background: linear-gradient(45deg, #6c5ce7, #a29bfe);
    color: white;
    border: none;
}

.btn-premium {
    background: linear-gradient(45deg, #ff6b35, #f7931e);
    color: white;
    border: none;
}

.advantages-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 30px;
    margin: 50px 0;
}

.advantage-item {
    background: var(--card-bg);
    padding: 30px;
    border-radius: 15px;
    transition: all 0.3s ease;
}

.advantage-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.2);
}

.advantage-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(45deg, #ff6b35, #f7931e);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    margin-bottom: 20px;
}

.advantage-content h3 {
    font-size: 1.3rem;
    margin-bottom: 15px;
    color: #ff6b35;
}

.advantage-content ul {
    margin-top: 15px;
    padding-left: 20px;
}

.advantage-content li {
    margin-bottom: 5px;
    opacity: 0.8;
}

.faq-container {
    max-width: 800px;
    margin: 0 auto;
}

.faq-item {
    background: var(--card-bg);
    margin-bottom: 15px;
    border-radius: 10px;
    overflow: hidden;
}

.faq-question {
    padding: 20px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.3s ease;
}

.faq-question:hover {
    background: rgba(255,107,53,0.1);
}

.faq-answer {
    padding: 0 20px;
    max-height: 0;
    overflow: hidden;
    transition: all 0.3s ease;
}

.faq-item.active .faq-answer {
    padding: 20px;
    max-height: 200px;
}

.final-cta {
    background: linear-gradient(135deg, #1a1f2e 0%, #16213e 100%);
    padding: 80px 0;
    text-align: center;
    border-radius: 15px;
    margin: 50px 0;
}

.cta-content h2 {
    font-size: 2.5rem;
    margin-bottom: 20px;
    color: white;
}

.cta-content p {
    font-size: 1.2rem;
    margin-bottom: 40px;
    color: rgba(255,255,255,0.8);
}

.cta-buttons {
    display: flex;
    gap: 20px;
    justify-content: center;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.btn-large {
    padding: 15px 30px;
    font-size: 1.1rem;
    font-weight: 600;
}

.guarantee {
    color: rgba(255,255,255,0.7);
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .hero-content {
        flex-direction: column;
        text-align: center;
    }
    
    .hero-text h1 {
        font-size: 2rem;
    }
    
    .advantages-grid {
        grid-template-columns: 1fr;
    }
    
    .comparison-table {
        grid-template-columns: 1fr;
    }
    
    .plan-column.recommended {
        transform: none;
    }
    
    .cta-buttons {
        flex-direction: column;
        align-items: center;
    }
}
</style>

<script>
// FAQ açma/kapama
document.querySelectorAll('.faq-question').forEach(question => {
    question.addEventListener('click', () => {
        const faqItem = question.parentElement;
        const isActive = faqItem.classList.contains('active');
        
        // Tüm FAQ'ları kapat
        document.querySelectorAll('.faq-item').forEach(item => {
            item.classList.remove('active');
        });
        
        // Tıklanan FAQ'ı aç (eğer kapalıysa)
        if (!isActive) {
            faqItem.classList.add('active');
        }
        
        // Icon döndürme
        const icon = question.querySelector('i');
        if (faqItem.classList.contains('active')) {
            icon.style.transform = 'rotate(180deg)';
        } else {
            icon.style.transform = 'rotate(0deg)';
        }
    });
});

console.log('Premium Avantajlar sayfası yüklendi - DOBİEN');
</script>

<?php include 'includes/footer.php'; ?>