<?php
/**
 * DOBİEN Video Platform - Premium Avantajları
 * Geliştirici: DOBİEN
 * Modern Video Paylaşım Platformu
 */

require_once 'includes/config.php';

$page_title = "Premium Avantajları - " . $site_settings['site_adi'];
$page_description = "Premium üyelik avantajlarını keşfedin. 4K video kalitesi, reklamsız izleme, sınırsız indirme ve daha fazlası!";

// Premium planları
$premium_plans = [
    'monthly' => [
        'name' => 'Aylık Premium',
        'price' => '29.99',
        'duration' => '30 gün',
        'savings' => null
    ],
    'yearly' => [
        'name' => 'Yıllık Premium',
        'price' => '299.99',
        'duration' => '365 gün',
        'savings' => '60.00'
    ]
];

// Premium avantajları
$premium_features = [
    [
        'icon' => 'fas fa-video',
        'title' => '4K Ultra HD Kalite',
        'description' => 'Tüm videoları 4K Ultra HD kalitede izleyin. Kristal netliğinde görüntü deneyimi yaşayın.'
    ],
    [
        'icon' => 'fas fa-ad',
        'title' => 'Reklamsız İzleme',
        'description' => 'Hiçbir reklam molası olmadan kesintisiz video izleme deneyimi.'
    ],
    [
        'icon' => 'fas fa-download',
        'title' => 'Sınırsız İndirme',
        'description' => 'İstediğiniz videoları cihazınıza indirin ve çevrimdışı izleyin.'
    ],
    [
        'icon' => 'fas fa-crown',
        'title' => 'Özel Premium İçerik',
        'description' => 'Sadece premium üyeler için hazırlanan özel video içeriklerine erişin.'
    ],
    [
        'icon' => 'fas fa-forward',
        'title' => 'Erken Erişim',
        'description' => 'Yeni çıkan videolara diğer kullanıcılardan önce erişim sağlayın.'
    ],
    [
        'icon' => 'fas fa-headset',
        'title' => 'Öncelikli Destek',
        'description' => '7/24 öncelikli müşteri desteği ve canlı yardım hizmeti.'
    ],
    [
        'icon' => 'fas fa-users',
        'title' => 'Çoklu Cihaz Desteği',
        'description' => 'Aynı anda 5 farklı cihazda Premium hesabınızı kullanın.'
    ],
    [
        'icon' => 'fas fa-sync-alt',
        'title' => 'Otomatik Senkronizasyon',
        'description' => 'İzleme geçmişiniz ve favorileriniz tüm cihazlarınızda senkronize.'
    ]
];

// Kullanıcı üyelik kontrolü
$user_membership = $current_user ? $current_user['uyelik_tipi'] : 'guest';
$is_premium = $user_membership === 'premium';

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta name="keywords" content="premium üyelik, 4k video, reklamsız izleme, video indirme, DOBİEN">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    
    <style>
        :root {
            --premium-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --premium-gold: #ffd700;
            --premium-dark: #2c3e50;
        }
        
        .hero-section {
            background: var(--premium-gradient);
            color: white;
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .premium-crown {
            font-size: 4rem;
            color: var(--premium-gold);
            margin-bottom: 20px;
            animation: glow 2s ease-in-out infinite alternate;
        }
        
        @keyframes glow {
            from { text-shadow: 0 0 20px rgba(255, 215, 0, 0.5); }
            to { text-shadow: 0 0 30px rgba(255, 215, 0, 0.8); }
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 60px;
        }
        
        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: 3px solid transparent;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            border-color: var(--premium-gold);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .feature-icon {
            font-size: 3rem;
            color: var(--premium-gold);
            margin-bottom: 20px;
        }
        
        .pricing-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 80px 0;
        }
        
        .pricing-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .pricing-card.featured {
            transform: scale(1.05);
            border: 3px solid var(--premium-gold);
        }
        
        .pricing-card.featured::before {
            content: 'En Popüler';
            position: absolute;
            top: 20px;
            right: -30px;
            background: var(--premium-gold);
            color: var(--premium-dark);
            padding: 5px 40px;
            font-weight: bold;
            font-size: 0.9rem;
            transform: rotate(45deg);
        }
        
        .price {
            font-size: 3rem;
            font-weight: bold;
            color: var(--premium-dark);
            margin: 20px 0;
        }
        
        .savings {
            background: #28a745;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-top: 10px;
            display: inline-block;
        }
        
        .premium-btn {
            background: var(--premium-gradient);
            border: none;
            color: white;
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: bold;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .premium-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .comparison-section {
            padding: 80px 0;
        }
        
        .comparison-table {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .comparison-table th {
            background: var(--premium-gradient);
            color: white;
            padding: 20px;
            border: none;
        }
        
        .comparison-table td {
            padding: 15px 20px;
            border-color: #f8f9fa;
        }
        
        .check-icon {
            color: #28a745;
            font-size: 1.2rem;
        }
        
        .cross-icon {
            color: #dc3545;
            font-size: 1.2rem;
        }
        
        .cta-section {
            background: var(--premium-gradient);
            color: white;
            padding: 80px 0;
            text-align: center;
        }
        
        .already-premium {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 20px;
            border-radius: 15px;
            margin-top: 30px;
        }
        
        @media (max-width: 768px) {
            .hero-section {
                padding: 60px 0;
            }
            
            .premium-crown {
                font-size: 3rem;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .pricing-card.featured {
                transform: none;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center hero-content">
                    <div class="premium-crown">
                        <i class="fas fa-crown"></i>
                    </div>
                    <h1 class="display-4 fw-bold mb-4">Premium Avantajlarını Keşfedin</h1>
                    <p class="lead mb-4">4K kalitede video izleme, reklamsız deneyim ve özel içeriklere erişim. Premium üyelikle video deneyiminizi bir üst seviyeye taşıyın.</p>
                    
                    <?php if ($is_premium): ?>
                        <div class="already-premium">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Tebrikler!</strong> Zaten Premium üyesiniz ve tüm avantajlardan yararlanıyorsunuz.
                        </div>
                    <?php else: ?>
                        <a href="#pricing" class="premium-btn">
                            <i class="fas fa-crown me-2"></i>Premium'a Geç
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center mb-5">
                    <h2 class="display-5 fw-bold">Premium Üyelik Avantajları</h2>
                    <p class="lead text-muted">Premium üyelikle elde edeceğiniz tüm avantajları keşfedin</p>
                </div>
            </div>
            
            <div class="features-grid">
                <?php foreach ($premium_features as $feature): ?>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="<?php echo $feature['icon']; ?>"></i>
                        </div>
                        <h4 class="fw-bold mb-3"><?php echo htmlspecialchars($feature['title']); ?></h4>
                        <p class="text-muted"><?php echo htmlspecialchars($feature['description']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    <!-- Pricing Section -->
    <?php if (!$is_premium): ?>
        <section class="pricing-section" id="pricing">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 mx-auto text-center mb-5">
                        <h2 class="display-5 fw-bold">Premium Planları</h2>
                        <p class="lead text-muted">Size en uygun planı seçin ve premium avantajlardan yararlanmaya başlayın</p>
                    </div>
                </div>
                
                <div class="row justify-content-center">
                    <div class="col-lg-5 col-md-6 mb-4">
                        <div class="pricing-card">
                            <h3 class="fw-bold"><?php echo $premium_plans['monthly']['name']; ?></h3>
                            <div class="price">₺<?php echo $premium_plans['monthly']['price']; ?></div>
                            <p class="text-muted"><?php echo $premium_plans['monthly']['duration']; ?></p>
                            
                            <div class="d-grid">
                                <a href="premium.php?plan=monthly" class="premium-btn">
                                    <i class="fas fa-credit-card me-2"></i>Hemen Başla
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-5 col-md-6 mb-4">
                        <div class="pricing-card featured">
                            <h3 class="fw-bold"><?php echo $premium_plans['yearly']['name']; ?></h3>
                            <div class="price">₺<?php echo $premium_plans['yearly']['price']; ?></div>
                            <p class="text-muted"><?php echo $premium_plans['yearly']['duration']; ?></p>
                            
                            <?php if ($premium_plans['yearly']['savings']): ?>
                                <div class="savings">
                                    ₺<?php echo $premium_plans['yearly']['savings']; ?> Tasarruf!
                                </div>
                            <?php endif; ?>
                            
                            <div class="d-grid mt-3">
                                <a href="premium.php?plan=yearly" class="premium-btn">
                                    <i class="fas fa-crown me-2"></i>En İyi Seçenek
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>
    
    <!-- Comparison Section -->
    <section class="comparison-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <h2 class="display-5 fw-bold text-center mb-5">Üyelik Karşılaştırması</h2>
                    
                    <div class="table-responsive">
                        <table class="table comparison-table">
                            <thead>
                                <tr>
                                    <th>Özellik</th>
                                    <th class="text-center">Ücretsiz</th>
                                    <th class="text-center">Premium</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Video Kalitesi</strong></td>
                                    <td class="text-center">720p HD</td>
                                    <td class="text-center">4K Ultra HD</td>
                                </tr>
                                <tr>
                                    <td><strong>Reklam</strong></td>
                                    <td class="text-center"><i class="fas fa-times cross-icon"></i></td>
                                    <td class="text-center"><i class="fas fa-check check-icon"></i> Reklamsız</td>
                                </tr>
                                <tr>
                                    <td><strong>Video İndirme</strong></td>
                                    <td class="text-center"><i class="fas fa-times cross-icon"></i></td>
                                    <td class="text-center"><i class="fas fa-check check-icon"></i> Sınırsız</td>
                                </tr>
                                <tr>
                                    <td><strong>Özel İçerik</strong></td>
                                    <td class="text-center"><i class="fas fa-times cross-icon"></i></td>
                                    <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                                </tr>
                                <tr>
                                    <td><strong>Erken Erişim</strong></td>
                                    <td class="text-center"><i class="fas fa-times cross-icon"></i></td>
                                    <td class="text-center"><i class="fas fa-check check-icon"></i></td>
                                </tr>
                                <tr>
                                    <td><strong>Cihaz Sayısı</strong></td>
                                    <td class="text-center">1 Cihaz</td>
                                    <td class="text-center">5 Cihaz</td>
                                </tr>
                                <tr>
                                    <td><strong>Müşteri Desteği</strong></td>
                                    <td class="text-center">E-posta</td>
                                    <td class="text-center">7/24 Öncelikli</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <?php if (!$is_premium): ?>
        <section class="cta-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 text-center">
                        <h2 class="display-5 fw-bold mb-4">Premium Deneyimi Başlatın!</h2>
                        <p class="lead mb-4">Hemen şimdi Premium'a geçin ve tüm avantajlardan yararlanmaya başlayın. İlk 7 gün ücretsiz deneme fırsatını kaçırmayın!</p>
                        
                        <div class="d-flex flex-column flex-md-row gap-3 justify-content-center">
                            <a href="premium.php?plan=yearly" class="premium-btn">
                                <i class="fas fa-crown me-2"></i>Yıllık Plan - En Avantajlı
                            </a>
                            <a href="premium.php?plan=monthly" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-calendar me-2"></i>Aylık Plan
                            </a>
                        </div>
                        
                        <div class="mt-4">
                            <small class="text-white-50">
                                <i class="fas fa-shield-alt me-1"></i>
                                7 gün ücretsiz deneme • İstediğiniz zaman iptal edebilirsiniz
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Add animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);
        
        // Observe feature cards
        document.querySelectorAll('.feature-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });
    </script>
</body>
</html>