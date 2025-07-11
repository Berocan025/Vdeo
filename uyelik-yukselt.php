<?php
/**
 * DOBİEN Video Platform - Üyelik Yükseltme Sayfası
 * Geliştirici: DOBİEN
 * Modern Video Paylaşım Platformu
 * Tüm Hakları Saklıdır © DOBİEN
 */

require_once 'includes/config.php';

// Sayfa bilgileri
$page_title = "Üyelik Yükselt";
$page_description = "DOBİEN Video Platform - Premium ve VIP üyelik avantajları, 4K video kalitesi ve özel içerikler";
$page_keywords = "premium üyelik, vip üyelik, 4k video, video platform üyelik, DOBİEN";

// Seçilen plan
$selected_plan = isset($_GET['plan']) ? $_GET['plan'] : '';

// Üyelik paketleri tanımla
$membership_plans = [
    'kullanici' => [
        'name' => 'Ücretsiz Üye',
        'price' => 0,
        'price_text' => 'Ücretsiz',
        'duration' => 'Süresiz',
        'color' => 'default',
        'icon' => 'fas fa-user',
        'popular' => false,
        'features' => [
            'video_quality' => '720p HD',
            'ads' => 'Reklamlar var',
            'downloads' => 'İndirme yok',
            'support' => 'Topluluk desteği',
            'content' => 'Sınırlı içerik',
            'devices' => '1 cihaz',
            'quality' => 'Standart ses kalitesi'
        ]
    ],
    'vip' => [
        'name' => 'VIP Üye',
        'price' => 29.99,
        'price_text' => '29.99 TL',
        'duration' => 'Aylık',
        'color' => 'warning',
        'icon' => 'fas fa-crown',
        'popular' => true,
        'features' => [
            'video_quality' => '1080p Full HD',
            'ads' => 'Sınırlı reklam',
            'downloads' => '5 video indirme',
            'support' => '24/7 öncelikli destek',
            'content' => 'VIP özel içerikler',
            'devices' => '3 cihaz',
            'quality' => 'Yüksek ses kalitesi'
        ]
    ],
    'premium' => [
        'name' => 'Premium Üye',
        'price' => 49.99,
        'price_text' => '49.99 TL',
        'duration' => 'Aylık',
        'color' => 'primary',
        'icon' => 'fas fa-gem',
        'popular' => false,
        'features' => [
            'video_quality' => '4K Ultra HD + HDR',
            'ads' => 'Reklamsız deneyim',
            'downloads' => 'Sınırsız indirme',
            'support' => 'Özel destek temsilcisi',
            'content' => 'Tüm premium içerikler',
            'devices' => 'Sınırsız cihaz',
            'quality' => 'Dolby Atmos ses'
        ]
    ]
];

// Özellik karşılaştırma tablosu
$comparison_features = [
    [
        'name' => 'Video Kalitesi',
        'icon' => 'fas fa-video',
        'kullanici' => '720p HD',
        'vip' => '1080p Full HD',
        'premium' => '4K Ultra HD + HDR'
    ],
    [
        'name' => 'Ses Kalitesi',
        'icon' => 'fas fa-volume-up',
        'kullanici' => 'Standart',
        'vip' => 'Yüksek Kalite',
        'premium' => 'Dolby Atmos'
    ],
    [
        'name' => 'Reklamlar',
        'icon' => 'fas fa-ad',
        'kullanici' => 'Var',
        'vip' => 'Sınırlı',
        'premium' => 'Yok'
    ],
    [
        'name' => 'İndirme',
        'icon' => 'fas fa-download',
        'kullanici' => false,
        'vip' => '5 Video',
        'premium' => 'Sınırsız'
    ],
    [
        'name' => 'Eş Zamanlı İzleme',
        'icon' => 'fas fa-users',
        'kullanici' => '1 Cihaz',
        'vip' => '3 Cihaz',
        'premium' => 'Sınırsız'
    ],
    [
        'name' => 'Özel İçerikler',
        'icon' => 'fas fa-star',
        'kullanici' => false,
        'vip' => 'VIP İçerikler',
        'premium' => 'Tüm İçerikler'
    ],
    [
        'name' => 'Müşteri Desteği',
        'icon' => 'fas fa-headset',
        'kullanici' => 'Topluluk',
        'vip' => '24/7 Öncelikli',
        'premium' => 'Özel Temsilci'
    ],
    [
        'name' => 'Offline İzleme',
        'icon' => 'fas fa-wifi',
        'kullanici' => false,
        'vip' => true,
        'premium' => true
    ],
    [
        'name' => 'Erken Erişim',
        'icon' => 'fas fa-clock',
        'kullanici' => false,
        'vip' => true,
        'premium' => true
    ],
    [
        'name' => 'Özel Etkinlikler',
        'icon' => 'fas fa-calendar',
        'kullanici' => false,
        'vip' => 'Sınırlı',
        'premium' => 'Tam Erişim'
    ]
];

include 'includes/header.php';
?>

<div class="container">
    <!-- Hero Section -->
    <section class="upgrade-hero">
        <div class="hero-content">
            <h1 class="hero-title">
                <i class="fas fa-crown"></i>
                Üyeliğinizi Yükseltin
            </h1>
            <p class="hero-subtitle">
                Premium video deneyimi ve özel içeriklerle video izleme keyfini en üst seviyeye taşıyın.
                <strong>DOBİEN</strong> kalitesi ile tanışın!
            </p>
            <div class="hero-stats">
                <div class="stat">
                    <i class="fas fa-video"></i>
                    <strong>10,000+</strong>
                    <span>4K Video</span>
                </div>
                <div class="stat">
                    <i class="fas fa-users"></i>
                    <strong>50,000+</strong>
                    <span>Mutlu Üye</span>
                </div>
                <div class="stat">
                    <i class="fas fa-star"></i>
                    <strong>4.9/5</strong>
                    <span>Müşteri Memnuniyeti</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Üyelik Paketleri -->
    <section class="membership-plans">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-gem"></i>
                Üyelik Paketlerimizi Keşfedin
            </h2>
            <p class="section-description">
                Size en uygun paketi seçin ve premium video deneyiminin tadını çıkarın.
            </p>
        </div>

        <div class="plans-grid">
            <?php foreach ($membership_plans as $plan_id => $plan): ?>
            <div class="plan-card <?php echo $plan['color']; ?> <?php echo $plan['popular'] ? 'popular' : ''; ?> <?php echo $selected_plan == $plan_id ? 'selected' : ''; ?>">
                <?php if ($plan['popular']): ?>
                <div class="popular-badge">
                    <i class="fas fa-fire"></i>
                    En Popüler
                </div>
                <?php endif; ?>

                <div class="plan-header">
                    <div class="plan-icon">
                        <i class="<?php echo $plan['icon']; ?>"></i>
                    </div>
                    <h3 class="plan-name"><?php echo $plan['name']; ?></h3>
                    <div class="plan-price">
                        <span class="price"><?php echo $plan['price_text']; ?></span>
                        <?php if ($plan['price'] > 0): ?>
                        <span class="duration">/ <?php echo $plan['duration']; ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="plan-features">
                    <div class="feature">
                        <i class="fas fa-video"></i>
                        <span><?php echo $plan['features']['video_quality']; ?></span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-volume-up"></i>
                        <span><?php echo $plan['features']['quality']; ?></span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-ad"></i>
                        <span><?php echo $plan['features']['ads']; ?></span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-download"></i>
                        <span><?php echo $plan['features']['downloads']; ?></span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-devices"></i>
                        <span><?php echo $plan['features']['devices']; ?></span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-headset"></i>
                        <span><?php echo $plan['features']['support']; ?></span>
                    </div>
                </div>

                <div class="plan-action">
                    <?php if ($current_user && $current_user['uyelik_tipi'] == $plan_id): ?>
                        <button class="btn btn-success current-plan" disabled>
                            <i class="fas fa-check"></i>
                            Mevcut Paketiniz
                        </button>
                    <?php elseif ($plan_id == 'kullanici'): ?>
                        <button class="btn btn-outline" disabled>
                            Ücretsiz Plan
                        </button>
                    <?php else: ?>
                        <a href="<?php echo siteUrl('odeme.php?plan=' . $plan_id); ?>" class="btn btn-primary">
                            <i class="fas fa-credit-card"></i>
                            <?php echo $plan_id == 'vip' ? 'VIP Ol' : 'Premium Ol'; ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Detaylı Karşılaştırma Tablosu -->
    <section class="comparison-table-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-balance-scale"></i>
                Detaylı Karşılaştırma
            </h2>
            <p class="section-description">
                Tüm özellikleri karşılaştırın ve size en uygun paketi seçin.
            </p>
        </div>

        <div class="comparison-table-container">
            <table class="comparison-table">
                <thead>
                    <tr>
                        <th class="feature-column">Özellikler</th>
                        <th class="plan-column kullanici">
                            <div class="plan-header-cell">
                                <i class="fas fa-user"></i>
                                <span>Ücretsiz</span>
                                <small>0 TL</small>
                            </div>
                        </th>
                        <th class="plan-column vip popular">
                            <div class="plan-header-cell">
                                <i class="fas fa-crown"></i>
                                <span>VIP</span>
                                <small>29.99 TL/ay</small>
                                <div class="popular-mark">Popüler</div>
                            </div>
                        </th>
                        <th class="plan-column premium">
                            <div class="plan-header-cell">
                                <i class="fas fa-gem"></i>
                                <span>Premium</span>
                                <small>49.99 TL/ay</small>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($comparison_features as $feature): ?>
                    <tr>
                        <td class="feature-name">
                            <i class="<?php echo $feature['icon']; ?>"></i>
                            <?php echo $feature['name']; ?>
                        </td>
                        <td class="feature-value kullanici">
                            <?php if ($feature['kullanici'] === false): ?>
                                <i class="fas fa-times text-error"></i>
                            <?php elseif ($feature['kullanici'] === true): ?>
                                <i class="fas fa-check text-success"></i>
                            <?php else: ?>
                                <?php echo $feature['kullanici']; ?>
                            <?php endif; ?>
                        </td>
                        <td class="feature-value vip">
                            <?php if ($feature['vip'] === false): ?>
                                <i class="fas fa-times text-error"></i>
                            <?php elseif ($feature['vip'] === true): ?>
                                <i class="fas fa-check text-success"></i>
                            <?php else: ?>
                                <?php echo $feature['vip']; ?>
                            <?php endif; ?>
                        </td>
                        <td class="feature-value premium">
                            <?php if ($feature['premium'] === false): ?>
                                <i class="fas fa-times text-error"></i>
                            <?php elseif ($feature['premium'] === true): ?>
                                <i class="fas fa-check text-success"></i>
                            <?php else: ?>
                                <?php echo $feature['premium']; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td></td>
                        <td class="action-cell kullanici">
                            <button class="btn btn-outline" disabled>
                                Mevcut Plan
                            </button>
                        </td>
                        <td class="action-cell vip">
                            <a href="<?php echo siteUrl('odeme.php?plan=vip'); ?>" class="btn btn-warning">
                                <i class="fas fa-crown"></i>
                                VIP Ol
                            </a>
                        </td>
                        <td class="action-cell premium">
                            <a href="<?php echo siteUrl('odeme.php?plan=premium'); ?>" class="btn btn-primary">
                                <i class="fas fa-gem"></i>
                                Premium Ol
                            </a>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </section>

    <!-- Avantajlar -->
    <section class="benefits-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-gift"></i>
                Neden DOBİEN Premium?
            </h2>
        </div>

        <div class="benefits-grid">
            <div class="benefit-card">
                <div class="benefit-icon">
                    <i class="fas fa-tv"></i>
                </div>
                <h3>4K Ultra HD</h3>
                <p>Kristal berraklığında görüntü kalitesi ile videolarınızı en yüksek çözünürlükte izleyin.</p>
            </div>

            <div class="benefit-card">
                <div class="benefit-icon">
                    <i class="fas fa-ban"></i>
                </div>
                <h3>Reklamsız Deneyim</h3>
                <p>Hiçbir kesinti olmadan videolarınızı izleyin. Tam konsantrasyon, tam keyif.</p>
            </div>

            <div class="benefit-card">
                <div class="benefit-icon">
                    <i class="fas fa-download"></i>
                </div>
                <h3>Offline İzleme</h3>
                <p>Videolarınızı indirin ve internet bağlantısı olmadan istediğiniz zaman izleyin.</p>
            </div>

            <div class="benefit-card">
                <div class="benefit-icon">
                    <i class="fas fa-headphones"></i>
                </div>
                <h3>Dolby Atmos</h3>
                <p>Sinema kalitesinde ses deneyimi ile videolarınızı daha da keyifli hale getirin.</p>
            </div>

            <div class="benefit-card">
                <div class="benefit-icon">
                    <i class="fas fa-star"></i>
                </div>
                <h3>Özel İçerikler</h3>
                <p>Sadece premium üyeler için hazırlanan özel videolara erişim sağlayın.</p>
            </div>

            <div class="benefit-card">
                <div class="benefit-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h3>Erken Erişim</h3>
                <p>Yeni içerikleri herkesten önce izleme fırsatını yakalayın.</p>
            </div>
        </div>
    </section>

    <!-- Müşteri Yorumları -->
    <section class="testimonials-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-comments"></i>
                Müşterilerimiz Ne Diyor?
            </h2>
        </div>

        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p>"4K video kalitesi gerçekten muhteşem! DOBİEN Premium'a geçtikten sonra video izleme deneyimim tamamen değişti."</p>
                <div class="testimonial-author">
                    <img src="<?php echo siteUrl('assets/images/testimonial1.jpg'); ?>" alt="Müşteri">
                    <div class="author-info">
                        <strong>Ahmet Yılmaz</strong>
                        <span>Premium Üye</span>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p>"VIP üyelik sayesinde özel içeriklere erişebiliyorum. Müşteri desteği de çok hızlı ve çözüm odaklı."</p>
                <div class="testimonial-author">
                    <img src="<?php echo siteUrl('assets/images/testimonial2.jpg'); ?>" alt="Müşteri">
                    <div class="author-info">
                        <strong>Elif Demir</strong>
                        <span>VIP Üye</span>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p>"Offline izleme özelliği harika! Yolculuklarımda internet olmadan da videolarımı izleyebiliyorum."</p>
                <div class="testimonial-author">
                    <img src="<?php echo siteUrl('assets/images/testimonial3.jpg'); ?>" alt="Müşteri">
                    <div class="author-info">
                        <strong>Mehmet Kaya</strong>
                        <span>Premium Üye</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Güvenlik ve Garanti -->
    <section class="security-section">
        <div class="security-content">
            <div class="security-text">
                <h2>
                    <i class="fas fa-shield-alt"></i>
                    %100 Güvenli Ödeme
                </h2>
                <p>DOBİEN olarak müşteri bilgilerinin güvenliği bizim önceliğimizdir. Tüm ödemeleriniz SSL ile şifrelenir.</p>
                <div class="security-features">
                    <div class="security-feature">
                        <i class="fas fa-lock"></i>
                        <span>256-bit SSL Şifreleme</span>
                    </div>
                    <div class="security-feature">
                        <i class="fas fa-credit-card"></i>
                        <span>Güvenli Ödeme Yöntemleri</span>
                    </div>
                    <div class="security-feature">
                        <i class="fas fa-undo"></i>
                        <span>7 Gün Para İade Garantisi</span>
                    </div>
                </div>
            </div>
            <div class="security-badges">
                <img src="<?php echo siteUrl('assets/images/ssl-badge.png'); ?>" alt="SSL Güvenli">
                <img src="<?php echo siteUrl('assets/images/payment-secure.png'); ?>" alt="Güvenli Ödeme">
            </div>
        </div>
    </section>

    <!-- SSS -->
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
                    <h3>Üyeliğimi istediğim zaman iptal edebilir miyim?</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Evet, üyeliğinizi istediğiniz zaman iptal edebilirsiniz. İptal işlemi anında gerçekleşir ve gelecek ödemeler durdurulur.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <h3>Kaç cihazda aynı anda izleyebilirim?</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>VIP üyelikle 3 cihazda, Premium üyelikle sınırsız cihazda aynı anda izleyebilirsiniz.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <h3>4K video izlemek için özel bir cihaz gerekli mi?</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>4K videoları izlemek için 4K destekli bir ekran ve yeterli internet hızı gereklidir. Platform otomatik olarak cihazınıza uygun kaliteyi seçer.</p>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- DOBİEN Developer Signature -->
<div class="dobien-signature">
    <i class="fas fa-code"></i> DOBİEN Premium
</div>

<style>
/* Üyelik Yükseltme Sayfası Özel CSS */
.upgrade-hero {
    background: var(--gradient-card);
    border-radius: var(--radius-2xl);
    padding: 4rem 2rem;
    text-align: center;
    margin-bottom: 4rem;
    position: relative;
    overflow: hidden;
}

.upgrade-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(99, 102, 241, 0.1), transparent);
    animation: rotate 20s linear infinite;
}

.hero-content {
    position: relative;
    z-index: 2;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 700;
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 1rem;
}

.hero-subtitle {
    font-size: 1.3rem;
    color: var(--text-secondary);
    margin-bottom: 3rem;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
}

.hero-stats {
    display: flex;
    justify-content: center;
    gap: 3rem;
    flex-wrap: wrap;
}

.stat {
    text-align: center;
}

.stat i {
    font-size: 2rem;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
    display: block;
}

.stat strong {
    display: block;
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
}

.stat span {
    color: var(--text-muted);
    font-size: 0.9rem;
}

/* Üyelik Planları */
.plans-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 2rem;
    margin-bottom: 4rem;
}

.plan-card {
    background: var(--bg-card);
    border-radius: var(--radius-2xl);
    padding: 2rem;
    border: 2px solid var(--border-color);
    position: relative;
    transition: var(--transition);
    overflow: hidden;
}

.plan-card:hover {
    transform: translateY(-10px);
    box-shadow: var(--shadow-xl);
}

.plan-card.popular {
    border-color: var(--warning-color);
    box-shadow: 0 0 30px rgba(245, 158, 11, 0.3);
}

.plan-card.selected {
    border-color: var(--primary-color);
    box-shadow: 0 0 30px rgba(99, 102, 241, 0.3);
}

.popular-badge {
    position: absolute;
    top: -1px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--warning-color);
    color: white;
    padding: 0.5rem 1.5rem;
    border-radius: 0 0 var(--radius-lg) var(--radius-lg);
    font-weight: 600;
    font-size: 0.8rem;
    text-transform: uppercase;
}

.plan-header {
    text-align: center;
    margin-bottom: 2rem;
}

.plan-icon {
    width: 80px;
    height: 80px;
    background: var(--gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 2rem;
    color: white;
}

.plan-name {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: var(--text-primary);
}

.plan-price .price {
    font-size: 3rem;
    font-weight: 700;
    color: var(--primary-color);
}

.plan-price .duration {
    color: var(--text-muted);
    margin-left: 0.5rem;
}

.plan-features {
    margin-bottom: 2rem;
}

.plan-features .feature {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--border-color);
}

.plan-features .feature:last-child {
    border-bottom: none;
}

.plan-features .feature i {
    color: var(--primary-color);
    width: 20px;
}

.plan-action .btn {
    width: 100%;
    padding: 1rem 2rem;
    font-size: 1.1rem;
    font-weight: 600;
}

/* Karşılaştırma Tablosu */
.comparison-table-container {
    overflow-x: auto;
    background: var(--bg-card);
    border-radius: var(--radius-2xl);
    box-shadow: var(--shadow-lg);
}

.comparison-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px;
}

.comparison-table th,
.comparison-table td {
    padding: 1.5rem 1rem;
    text-align: center;
    border-bottom: 1px solid var(--border-color);
}

.comparison-table th {
    background: var(--bg-tertiary);
    font-weight: 600;
    position: sticky;
    top: 0;
    z-index: 10;
}

.feature-column {
    text-align: left !important;
    min-width: 200px;
    font-weight: 500;
}

.plan-header-cell {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.plan-header-cell i {
    font-size: 1.5rem;
    color: var(--primary-color);
}

.popular-mark {
    background: var(--warning-color);
    color: white;
    padding: 0.2rem 0.5rem;
    border-radius: var(--radius-sm);
    font-size: 0.7rem;
    text-transform: uppercase;
    font-weight: 600;
}

.feature-name {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 500;
}

.feature-name i {
    color: var(--primary-color);
    width: 20px;
}

.feature-value {
    font-weight: 500;
}

.text-success {
    color: var(--success-color);
    font-size: 1.2rem;
}

.text-error {
    color: var(--error-color);
    font-size: 1.2rem;
}

.action-cell .btn {
    padding: 0.75rem 1.5rem;
    min-width: 120px;
}

/* Avantajlar */
.benefits-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 4rem;
}

.benefit-card {
    background: var(--bg-card);
    padding: 2rem;
    border-radius: var(--radius-xl);
    text-align: center;
    border: 1px solid var(--border-color);
    transition: var(--transition);
}

.benefit-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary-color);
}

.benefit-icon {
    width: 80px;
    height: 80px;
    background: var(--gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2rem;
    color: white;
}

.benefit-card h3 {
    font-size: 1.3rem;
    margin-bottom: 1rem;
    color: var(--text-primary);
}

.benefit-card p {
    color: var(--text-secondary);
    line-height: 1.6;
}

/* Müşteri Yorumları */
.testimonials-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    margin-bottom: 4rem;
}

.testimonial-card {
    background: var(--bg-card);
    padding: 2rem;
    border-radius: var(--radius-xl);
    border: 1px solid var(--border-color);
}

.stars {
    margin-bottom: 1rem;
}

.stars i {
    color: var(--warning-color);
    margin-right: 0.2rem;
}

.testimonial-card p {
    color: var(--text-secondary);
    font-style: italic;
    margin-bottom: 1.5rem;
    line-height: 1.6;
}

.testimonial-author {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.testimonial-author img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
}

.author-info strong {
    display: block;
    color: var(--text-primary);
    font-weight: 600;
}

.author-info span {
    color: var(--text-muted);
    font-size: 0.9rem;
}

/* Güvenlik */
.security-section {
    background: var(--bg-card);
    padding: 3rem;
    border-radius: var(--radius-2xl);
    margin-bottom: 4rem;
}

.security-content {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 3rem;
    align-items: center;
}

.security-text h2 {
    font-size: 2rem;
    margin-bottom: 1rem;
    color: var(--text-primary);
}

.security-features {
    display: flex;
    gap: 2rem;
    margin-top: 1.5rem;
    flex-wrap: wrap;
}

.security-feature {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-secondary);
}

.security-feature i {
    color: var(--success-color);
}

.security-badges {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.security-badges img {
    max-height: 60px;
}

/* SSS */
.faq-container {
    max-width: 800px;
    margin: 0 auto;
}

.faq-item {
    background: var(--bg-card);
    border-radius: var(--radius-lg);
    margin-bottom: 1rem;
    border: 1px solid var(--border-color);
    overflow: hidden;
}

.faq-question {
    padding: 1.5rem;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: var(--transition);
}

.faq-question:hover {
    background: var(--bg-tertiary);
}

.faq-question h3 {
    font-size: 1.1rem;
    color: var(--text-primary);
    margin: 0;
}

.faq-answer {
    padding: 0 1.5rem 1.5rem;
    color: var(--text-secondary);
    line-height: 1.6;
}

/* Responsive */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-stats {
        gap: 2rem;
    }
    
    .plans-grid {
        grid-template-columns: 1fr;
    }
    
    .comparison-table-container {
        font-size: 0.9rem;
    }
    
    .security-content {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .security-features {
        justify-content: center;
    }
}

@keyframes rotate {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>

<script>
// FAQ accordion
document.querySelectorAll('.faq-question').forEach(question => {
    question.addEventListener('click', () => {
        const item = question.parentElement;
        const answer = item.querySelector('.faq-answer');
        const icon = question.querySelector('i');
        
        item.classList.toggle('active');
        
        if (item.classList.contains('active')) {
            answer.style.display = 'block';
            icon.style.transform = 'rotate(180deg)';
        } else {
            answer.style.display = 'none';
            icon.style.transform = 'rotate(0deg)';
        }
    });
});

console.log('DOBİEN Üyelik Yükseltme sayfası yüklendi!');
</script>

<?php include 'includes/footer.php'; ?>