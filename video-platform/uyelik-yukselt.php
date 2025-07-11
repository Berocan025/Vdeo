<?php
/**
 * DOBİEN Video Platform - Üyelik Yükseltme Sayfası
 * Geliştirici: DOBİEN
 * Üyelik Planları ve Satın Alma
 * Tüm Hakları Saklıdır © DOBİEN
 */

require_once 'includes/config.php';

$page_title = "Üyelik Yükselt";
$page_description = "DOBİEN Video Platform Premium ve VIP üyelik planları - 4K video kalitesi, sınırsız erişim";
$page_keywords = "premium üyelik, vip üyelik, üyelik yükselt, 4k video, DOBİEN";

// Seçili plan
$selected_plan = $_GET['plan'] ?? '';

// İşlem mesajları
$message = '';
$message_type = '';

// Form işleme
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'upgrade') {
        $plan = $_POST['plan'] ?? '';
        $period = $_POST['period'] ?? 'monthly';
        
        // Kullanıcı giriş kontrolü
        if (!$current_user) {
            $message = 'Üyelik yükseltmek için önce giriş yapmalısınız.';
            $message_type = 'warning';
        } else {
            // Fiyat hesaplama
            $prices = [
                'vip' => ['monthly' => 19.99, 'yearly' => 199.99],
                'premium' => ['monthly' => 29.99, 'yearly' => 299.99]
            ];
            
            if (isset($prices[$plan][$period])) {
                $amount = $prices[$plan][$period];
                
                // Ödeme kaydı oluştur
                try {
                    $stmt = $pdo->prepare("
                        INSERT INTO odeme_gecmisi (kullanici_id, plan, donem, tutar, durum, odeme_tarihi) 
                        VALUES (?, ?, ?, ?, 'beklemede', NOW())
                    ");
                    $stmt->execute([$current_user['id'], $plan, $period, $amount]);
                    
                    $payment_id = $pdo->lastInsertId();
                    
                    // Simüle edilmiş ödeme başarısı (gerçek projede ödeme gateway'i entegrasyonu yapılır)
                    $payment_success = true;
                    
                    if ($payment_success) {
                        // Üyelik güncellemesi
                        $end_date = $period == 'yearly' ? 
                            date('Y-m-d H:i:s', strtotime('+1 year')) : 
                            date('Y-m-d H:i:s', strtotime('+1 month'));
                        
                        $update_field = $plan == 'premium' ? 'premium_bitis' : 'vip_bitis';
                        
                        $update_stmt = $pdo->prepare("
                            UPDATE kullanicilar 
                            SET uyelik_tipi = ?, $update_field = ? 
                            WHERE id = ?
                        ");
                        $update_stmt->execute([$plan, $end_date, $current_user['id']]);
                        
                        // Ödeme durumunu güncelle
                        $pdo->prepare("UPDATE odeme_gecmisi SET durum = 'tamamlandi' WHERE id = ?")->execute([$payment_id]);
                        
                        // Session güncelle
                        $_SESSION['user_membership'] = $plan;
                        
                        $message = 'Tebrikler! Üyeliğiniz başarıyla yükseltildi. Artık ' . ($plan == 'premium' ? 'Premium' : 'VIP') . ' üye ayrıcalıklarından yararlanabilirsiniz.';
                        $message_type = 'success';
                        
                        // Kullanıcı bilgilerini yenile
                        $current_user = checkUserSession();
                        
                    } else {
                        $message = 'Ödeme işlemi başarısız oldu. Lütfen tekrar deneyin.';
                        $message_type = 'error';
                    }
                    
                } catch (PDOException $e) {
                    $message = 'Bir hata oluştu. Lütfen tekrar deneyin.';
                    $message_type = 'error';
                }
            } else {
                $message = 'Geçersiz plan veya dönem seçimi.';
                $message_type = 'error';
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
    <!-- Hero Section -->
    <section class="upgrade-hero">
        <div class="hero-content">
            <h1>
                <i class="fas fa-crown"></i>
                Üyeliğinizi Yükseltin
            </h1>
            <p>Premium kalitede video deneyimi için en uygun planı seçin ve avantajları keşfedin</p>
        </div>
    </section>

    <?php if ($message): ?>
    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible">
        <button class="alert-close" onclick="this.parentElement.style.display='none'">&times;</button>
        <?php echo htmlspecialchars($message); ?>
    </div>
    <?php endif; ?>

    <!-- Pricing Plans -->
    <section class="pricing-section">
        <div class="section-header">
            <h2>Üyelik Planları</h2>
            <p>Size en uygun planı seçin ve video deneyiminizi geliştirin</p>
        </div>

        <div class="pricing-toggle">
            <span class="toggle-label">Aylık</span>
            <label class="toggle-switch">
                <input type="checkbox" id="pricingToggle">
                <span class="toggle-slider"></span>
            </label>
            <span class="toggle-label">Yıllık <span class="discount-badge">2 Ay Bedava!</span></span>
        </div>

        <div class="pricing-plans">
            <!-- Ücretsiz Plan -->
            <div class="pricing-card basic">
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
                        <i class="fas fa-check"></i>
                        <span>720p Video Kalitesi</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check"></i>
                        <span>Sınırlı İçerik Erişimi</span>
                    </div>
                    <div class="feature limited">
                        <i class="fas fa-times"></i>
                        <span>Reklamlar Var</span>
                    </div>
                    <div class="feature limited">
                        <i class="fas fa-times"></i>
                        <span>Premium İçerik Yok</span>
                    </div>
                </div>
                <div class="plan-action">
                    <?php if (!$current_user): ?>
                    <a href="<?php echo siteUrl('kayit.php'); ?>" class="btn btn-outline">Ücretsiz Kayıt Ol</a>
                    <?php elseif ($current_user['uyelik_tipi'] == 'kullanici'): ?>
                    <button class="btn btn-outline" disabled>Mevcut Planınız</button>
                    <?php else: ?>
                    <button class="btn btn-outline" disabled>Geçmiş Plan</button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- VIP Plan -->
            <div class="pricing-card vip <?php echo $selected_plan == 'vip' ? 'selected' : ''; ?>">
                <div class="plan-header">
                    <h3>
                        <i class="fas fa-crown"></i>
                        VIP
                    </h3>
                    <div class="price">
                        <span class="amount monthly-price">₺19.99</span>
                        <span class="amount yearly-price" style="display: none;">₺199.99</span>
                        <span class="period monthly-period">/ay</span>
                        <span class="period yearly-period" style="display: none;">/yıl</span>
                    </div>
                    <div class="plan-badge">Popüler</div>
                </div>
                <div class="plan-features">
                    <div class="feature">
                        <i class="fas fa-check"></i>
                        <span>1080p Full HD Kalite</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check"></i>
                        <span>VIP İçeriklere Erişim</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check"></i>
                        <span>Daha Az Reklam</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check"></i>
                        <span>Öncelikli Destek</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check"></i>
                        <span>Offline İzleme</span>
                    </div>
                </div>
                <div class="plan-action">
                    <?php if (!$current_user): ?>
                    <a href="<?php echo siteUrl('giris.php'); ?>" class="btn btn-vip">Giriş Yap ve VIP Ol</a>
                    <?php elseif ($current_user['uyelik_tipi'] == 'vip'): ?>
                    <button class="btn btn-vip" disabled>Mevcut Planınız</button>
                    <?php elseif ($current_user['uyelik_tipi'] == 'premium'): ?>
                    <button class="btn btn-outline" disabled>Premium'dan Düşürme</button>
                    <?php else: ?>
                    <button class="btn btn-vip upgrade-btn" data-plan="vip">VIP Ol</button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Premium Plan -->
            <div class="pricing-card premium recommended <?php echo $selected_plan == 'premium' ? 'selected' : ''; ?>">
                <div class="plan-header">
                    <h3>
                        <i class="fas fa-gem"></i>
                        Premium
                    </h3>
                    <div class="price">
                        <span class="amount monthly-price">₺29.99</span>
                        <span class="amount yearly-price" style="display: none;">₺299.99</span>
                        <span class="period monthly-period">/ay</span>
                        <span class="period yearly-period" style="display: none;">/yıl</span>
                    </div>
                    <div class="plan-badge premium">Önerilen</div>
                </div>
                <div class="plan-features">
                    <div class="feature">
                        <i class="fas fa-check"></i>
                        <span>4K Ultra HD Kalite</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check"></i>
                        <span>Tüm İçeriklere Erişim</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check"></i>
                        <span>Tamamen Reklamsız</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check"></i>
                        <span>Özel Premium İçerikler</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check"></i>
                        <span>7/24 VIP Destek</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check"></i>
                        <span>Çoklu Cihaz Desteği</span>
                    </div>
                </div>
                <div class="plan-action">
                    <?php if (!$current_user): ?>
                    <a href="<?php echo siteUrl('giris.php'); ?>" class="btn btn-premium">Giriş Yap ve Premium Ol</a>
                    <?php elseif ($current_user['uyelik_tipi'] == 'premium'): ?>
                    <button class="btn btn-premium" disabled>Mevcut Planınız</button>
                    <?php else: ?>
                    <button class="btn btn-premium upgrade-btn" data-plan="premium">Premium Ol</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="benefits-section">
        <div class="section-header">
            <h2>Premium Üyelik Avantajları</h2>
        </div>
        
        <div class="benefits-grid">
            <div class="benefit-item">
                <i class="fas fa-video"></i>
                <h3>4K Ultra HD</h3>
                <p>En yüksek video kalitesi ile kristal berraklığında görüntü</p>
            </div>
            <div class="benefit-item">
                <i class="fas fa-ad"></i>
                <h3>Reklamsız</h3>
                <p>Hiçbir reklam kesintisi olmadan kesintisiz izleme</p>
            </div>
            <div class="benefit-item">
                <i class="fas fa-infinity"></i>
                <h3>Sınırsız Erişim</h3>
                <p>Tüm içeriklere sınırsız erişim ve offline izleme</p>
            </div>
            <div class="benefit-item">
                <i class="fas fa-headset"></i>
                <h3>Öncelikli Destek</h3>
                <p>7/24 premium kullanıcı desteği</p>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="section-header">
            <h2>Sık Sorulan Sorular</h2>
        </div>
        
        <div class="faq-container">
            <div class="faq-item">
                <div class="faq-question">
                    <span>Üyeliğimi nasıl iptal edebilirim?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Hesap ayarlarınızdan istediğiniz zaman üyeliğinizi iptal edebilirsiniz. İptal ettiğinizde mevcut dönem sonuna kadar avantajlarınızdan yararlanmaya devam edersiniz.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <span>Deneme süresi var mı?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Evet! Yeni üyelerimiz için 7 günlük ücretsiz deneme süresi sunuyoruz.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <span>Kaç cihazda kullanabilirim?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Premium hesabınızla 5 cihazda, VIP hesabınızla 3 cihazda aynı anda kullanabilirsiniz.</p>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Upgrade Modal -->
<div class="modal" id="upgradeModal">
    <div class="modal-overlay"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3>Üyelik Yükseltme</h3>
            <button class="modal-close">&times;</button>
        </div>
        <form method="POST">
            <div class="modal-body">
                <input type="hidden" name="action" value="upgrade">
                <input type="hidden" name="plan" id="selectedPlan">
                
                <div class="upgrade-summary">
                    <h4 id="planTitle"></h4>
                    <div class="price-options">
                        <label class="price-option">
                            <input type="radio" name="period" value="monthly" checked>
                            <div class="option-content">
                                <span class="option-title">Aylık</span>
                                <span class="option-price" id="monthlyPrice"></span>
                            </div>
                        </label>
                        <label class="price-option">
                            <input type="radio" name="period" value="yearly">
                            <div class="option-content">
                                <span class="option-title">Yıllık</span>
                                <span class="option-price" id="yearlyPrice"></span>
                                <span class="option-discount">2 Ay Bedava!</span>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div class="payment-note">
                    <i class="fas fa-info-circle"></i>
                    <p>Bu demo sürümünde ödeme simüle edilmektedir. Gerçek projede ödeme gateway entegrasyonu yapılır.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close">İptal</button>
                <button type="submit" class="btn btn-primary">Üyeliği Yükselt</button>
            </div>
        </form>
    </div>
</div>

<style>
.upgrade-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 80px 0;
    text-align: center;
    color: white;
    margin-bottom: 50px;
    border-radius: 15px;
}

.upgrade-hero h1 {
    font-size: 3rem;
    margin-bottom: 20px;
    font-weight: 700;
}

.upgrade-hero p {
    font-size: 1.2rem;
    opacity: 0.9;
}

.pricing-toggle {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    margin-bottom: 50px;
}

.toggle-switch {
    position: relative;
    width: 60px;
    height: 30px;
}

.toggle-switch input {
    display: none;
}

.toggle-slider {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: #ccc;
    border-radius: 30px;
    cursor: pointer;
    transition: 0.3s;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 22px;
    width: 22px;
    left: 4px;
    bottom: 4px;
    background: white;
    border-radius: 50%;
    transition: 0.3s;
}

.toggle-switch input:checked + .toggle-slider {
    background: var(--primary-color);
}

.toggle-switch input:checked + .toggle-slider:before {
    transform: translateX(30px);
}

.discount-badge {
    background: #ff6b35;
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
}

.pricing-plans {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin-bottom: 80px;
}

.pricing-card {
    background: var(--card-bg);
    border-radius: 15px;
    padding: 30px;
    border: 2px solid transparent;
    position: relative;
    transition: all 0.3s ease;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.pricing-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
}

.pricing-card.selected {
    border-color: var(--primary-color);
    transform: scale(1.02);
}

.pricing-card.recommended {
    border-color: #ff6b35;
}

.plan-badge {
    position: absolute;
    top: -10px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--primary-color);
    color: white;
    padding: 5px 20px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.plan-badge.premium {
    background: linear-gradient(45deg, #ff6b35, #f7931e);
}

.plan-header h3 {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.5rem;
    margin-bottom: 20px;
}

.price {
    margin-bottom: 30px;
}

.price .amount {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary-color);
}

.price .period {
    font-size: 1rem;
    opacity: 0.7;
}

.plan-features {
    margin-bottom: 30px;
}

.feature {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
}

.feature i {
    color: #28a745;
    width: 16px;
}

.feature.limited i {
    color: #dc3545;
}

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

.benefits-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
    margin-top: 30px;
}

.benefit-item {
    text-align: center;
    padding: 30px;
    background: var(--card-bg);
    border-radius: 15px;
    transition: transform 0.3s ease;
}

.benefit-item:hover {
    transform: translateY(-5px);
}

.benefit-item i {
    font-size: 3rem;
    color: var(--primary-color);
    margin-bottom: 20px;
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
    transition: background 0.3s ease;
}

.faq-question:hover {
    background: rgba(var(--primary-rgb), 0.05);
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

.upgrade-summary {
    text-align: center;
    margin-bottom: 30px;
}

.price-options {
    display: grid;
    gap: 15px;
    margin-top: 20px;
}

.price-option {
    border: 2px solid #ddd;
    border-radius: 10px;
    padding: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.price-option:hover {
    border-color: var(--primary-color);
}

.price-option input[type="radio"] {
    display: none;
}

.price-option input[type="radio"]:checked + .option-content {
    color: var(--primary-color);
}

.price-option input[type="radio"]:checked ~ * {
    border-color: var(--primary-color);
}

.option-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.option-discount {
    background: #ff6b35;
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.7rem;
}

.payment-note {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    display: flex;
    gap: 10px;
    color: #666;
}

@media (max-width: 768px) {
    .pricing-plans {
        grid-template-columns: 1fr;
    }
    
    .upgrade-hero h1 {
        font-size: 2rem;
    }
    
    .benefits-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// Pricing toggle
document.getElementById('pricingToggle').addEventListener('change', function() {
    const isYearly = this.checked;
    
    document.querySelectorAll('.monthly-price, .monthly-period').forEach(el => {
        el.style.display = isYearly ? 'none' : 'inline';
    });
    
    document.querySelectorAll('.yearly-price, .yearly-period').forEach(el => {
        el.style.display = isYearly ? 'inline' : 'none';
    });
});

// FAQ toggle
document.querySelectorAll('.faq-question').forEach(question => {
    question.addEventListener('click', () => {
        const faqItem = question.parentElement;
        const isActive = faqItem.classList.contains('active');
        
        // Close all FAQ items
        document.querySelectorAll('.faq-item').forEach(item => {
            item.classList.remove('active');
        });
        
        // Open clicked item if it wasn't active
        if (!isActive) {
            faqItem.classList.add('active');
        }
    });
});

// Upgrade modal
document.querySelectorAll('.upgrade-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const plan = btn.dataset.plan;
        const planTitles = {
            'vip': 'VIP Üyelik',
            'premium': 'Premium Üyelik'
        };
        const prices = {
            'vip': { monthly: '₺19.99/ay', yearly: '₺199.99/yıl' },
            'premium': { monthly: '₺29.99/ay', yearly: '₺299.99/yıl' }
        };
        
        document.getElementById('selectedPlan').value = plan;
        document.getElementById('planTitle').textContent = planTitles[plan];
        document.getElementById('monthlyPrice').textContent = prices[plan].monthly;
        document.getElementById('yearlyPrice').textContent = prices[plan].yearly;
        
        document.getElementById('upgradeModal').classList.add('show');
    });
});

// Modal close
document.querySelectorAll('.modal-close, .modal-overlay').forEach(el => {
    el.addEventListener('click', () => {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.classList.remove('show');
        });
    });
});

// ESC key close modal
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.classList.remove('show');
        });
    }
});

console.log('Üyelik Yükseltme sayfası yüklendi - DOBİEN');
</script>

<?php include 'includes/footer.php'; ?>