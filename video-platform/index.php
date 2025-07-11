<?php
/**
 * DOBİEN Video Platform - Ana Sayfa
 * Geliştirici: DOBİEN
 * Modern Video Paylaşım Platformu
 * Tüm Hakları Saklıdır © DOBİEN
 */

require_once 'includes/config.php';

// Kritik tabloları kontrol et
try {
    $pdo->query("SELECT 1 FROM videolar LIMIT 1");
    $pdo->query("SELECT 1 FROM kategoriler LIMIT 1");
    $pdo->query("SELECT 1 FROM kullanicilar LIMIT 1");
} catch (PDOException $e) {
    // Tablolar eksikse kuruluma yönlendir
    if (!file_exists('config/config.php')) {
        header('Location: install.php');
        exit;
    }
    // Config varsa ama tablolar eksikse uyarı ver
    die('
    <div style="font-family: Arial; background: #1a1f2e; color: #fff; padding: 50px; text-align: center;">
        <h2>⚠️ Veritabanı Hatası</h2>
        <p>Bazı veritabanı tabloları eksik. Lütfen kurulumu tekrar çalıştırın.</p>
        <p><a href="install.php" style="color: #ff6b35; text-decoration: none; background: #333; padding: 10px 20px; border-radius: 5px;">Kurulumu Çalıştır</a></p>
    </div>
    ');
}

// Sayfa bilgileri
$page_title = "Ana Sayfa";
$page_description = "DOBİEN Video Platform - En kaliteli video içerikleri, premium üyelik avantajları ve 4K video deneyimi";
$page_keywords = "video platform, premium videolar, 4k video, vip üyelik, DOBİEN";

// Yaş doğrulama kontrolü
$show_age_verification = !isset($_COOKIE['age_verified']);

// Video verilerini çek
$videos_per_page = 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $videos_per_page;

// Slider verilerini çek - Güvenli hata yakalama
$slider_items = [];
try {
    $slider_query = "SELECT * FROM slider WHERE durum = 'aktif' ORDER BY siralama ASC, id DESC LIMIT 5";
    $slider_result = $pdo->query($slider_query);
    $slider_items = $slider_result->fetchAll();
} catch (PDOException $e) {
    // Slider tablosu yoksa boş dizi kullan
    $slider_items = [];
}

// Son eklenen videolar - Güvenli hata yakalama
$recent_videos = [];
try {
    $recent_videos_query = "
        SELECT v.*, k.kategori_adi, k.slug as kategori_slug
        FROM videolar v 
        LEFT JOIN kategoriler k ON v.kategori_id = k.id 
        WHERE v.durum = 'aktif' 
        AND (v.goruntulenme_yetkisi = 'herkes' OR ? != '')
        ORDER BY v.ekleme_tarihi DESC 
        LIMIT $videos_per_page OFFSET $offset
    ";
    $recent_videos_stmt = $pdo->prepare($recent_videos_query);
    $recent_videos_stmt->execute([$current_user ? $current_user['uyelik_tipi'] : '']);
    $recent_videos = $recent_videos_stmt->fetchAll();
} catch (PDOException $e) {
    // Videolar tablosu yoksa boş dizi
    $recent_videos = [];
}

// Popüler videolar - Güvenli hata yakalama
$popular_videos = [];
try {
    $popular_videos_query = "
        SELECT v.*, k.kategori_adi, k.slug as kategori_slug
        FROM videolar v 
        LEFT JOIN kategoriler k ON v.kategori_id = k.id 
        WHERE v.durum = 'aktif' 
        AND (v.goruntulenme_yetkisi = 'herkes' OR ? != '')
        AND v.ozellik = 'populer'
        ORDER BY v.izlenme_sayisi DESC 
        LIMIT 8
    ";
    $popular_videos_stmt = $pdo->prepare($popular_videos_query);
    $popular_videos_stmt->execute([$current_user ? $current_user['uyelik_tipi'] : '']);
    $popular_videos = $popular_videos_stmt->fetchAll();
} catch (PDOException $e) {
    $popular_videos = [];
}

// Premium videolar (sadece premium üyeler için)
$premium_videos = [];
if ($current_user && $current_user['uyelik_tipi'] == 'premium') {
    try {
        $premium_videos_query = "
            SELECT v.*, k.kategori_adi, k.slug as kategori_slug
            FROM videolar v 
            LEFT JOIN kategoriler k ON v.kategori_id = k.id 
            WHERE v.durum = 'aktif' 
            AND v.goruntulenme_yetkisi = 'premium'
            ORDER BY v.ekleme_tarihi DESC 
            LIMIT 6
        ";
        $premium_videos = $pdo->query($premium_videos_query)->fetchAll();
    } catch (PDOException $e) {
        $premium_videos = [];
    }
}

// VIP videolar (VIP ve Premium üyeler için)
$vip_videos = [];
if ($current_user && ($current_user['uyelik_tipi'] == 'vip' || $current_user['uyelik_tipi'] == 'premium')) {
    try {
        $vip_videos_query = "
            SELECT v.*, k.kategori_adi, k.slug as kategori_slug
            FROM videolar v 
            LEFT JOIN kategoriler k ON v.kategori_id = k.id 
            WHERE v.durum = 'aktif' 
            AND v.goruntulenme_yetkisi IN ('vip', 'premium')
            ORDER BY v.ekleme_tarihi DESC 
            LIMIT 6
        ";
        $vip_videos = $pdo->query($vip_videos_query)->fetchAll();
    } catch (PDOException $e) {
        $vip_videos = [];
    }
}

// Kategoriler - Güvenli hata yakalama
$categories = [];
try {
    $categories_query = "SELECT * FROM kategoriler WHERE durum = 'aktif' ORDER BY siralama ASC, kategori_adi ASC LIMIT 8";
    $categories = $pdo->query($categories_query)->fetchAll();
} catch (PDOException $e) {
    $categories = [];
}

// Video kalitesi belirleme fonksiyonu
function getVideoQuality($membership_required) {
    switch($membership_required) {
        case 'premium': return '4K';
        case 'vip': return '1080p';
        default: return '720p';
    }
}

// Video erişim kontrolü
function canAccessVideo($video, $user) {
    if ($video['goruntulenme_yetkisi'] == 'herkes') {
        return true;
    }
    
    if (!$user) {
        return false;
    }
    
    $user_level = ['kullanici' => 1, 'vip' => 2, 'premium' => 3];
    $required_level = ['herkes' => 1, 'vip' => 2, 'premium' => 3];
    
    return $user_level[$user['uyelik_tipi']] >= $required_level[$video['goruntulenme_yetkisi']];
}

include 'includes/header.php';
?>

<!-- Yaş Doğrulama Popup'ı -->
<?php if ($show_age_verification): ?>
<div class="age-verification-overlay" id="ageVerificationOverlay">
    <div class="age-verification-popup">
        <div class="logo">
            <h2>DOBİEN</h2>
            <p class="subtitle">Video Platform</p>
        </div>
        
        <div class="age-warning">
            <div class="warning-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3>Yaş Doğrulama Gerekli</h3>
            <p>
                Bu siteye erişebilmeniz için <strong>18 yaşından büyük</strong> olmanız gerekmektedir. 
                Sitemiz yetişkin içerikler barındırmaktadır ve yalnızca reşit kullanıcılar için uygundur.
            </p>
            <p style="margin-top: 1rem;">
                Lütfen yaşınızı doğrulayarak devam edin:
            </p>
        </div>
        
        <div class="age-buttons">
            <button class="age-btn age-btn-confirm" onclick="confirmAge()">
                <i class="fas fa-check"></i>
                18 yaşından büyüğüm
            </button>
            <button class="age-btn age-btn-deny" onclick="denyAge()">
                <i class="fas fa-times"></i>
                18 yaşında değilim
            </button>
        </div>
        
        <div class="developer-note">
            <i class="fas fa-info-circle"></i>
            Bu sistem DOBİEN tarafından geliştirilmiştir ve kullanıcı güvenliği için tasarlanmıştır.
        </div>
    </div>
</div>
<?php endif; ?>

<div class="container">
    <!-- Hero Slider -->
    <?php if (!empty($slider_items)): ?>
    <section class="hero-section">
        <div class="hero-slider" id="heroSlider">
            <?php foreach ($slider_items as $index => $slide): ?>
            <div class="hero-slide <?php echo $index === 0 ? 'active' : ''; ?>">
                <img src="<?php echo siteUrl('uploads/slider/' . $slide['resim']); ?>" alt="<?php echo safeOutput($slide['baslik']); ?>">
                <div class="hero-content">
                    <div class="hero-text">
                        <h2><?php echo safeOutput($slide['baslik']); ?></h2>
                        <p><?php echo safeOutput($slide['aciklama']); ?></p>
                        <?php if ($slide['link']): ?>
                        <a href="<?php echo safeOutput($slide['link']); ?>" class="hero-button">
                            <i class="fas fa-play"></i>
                            <?php echo $slide['buton_metni'] ? safeOutput($slide['buton_metni']) : 'İzlemeye Başla'; ?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <!-- Slider Controls -->
            <div class="slider-controls">
                <button class="slider-btn prev" onclick="previousSlide()">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="slider-btn next" onclick="nextSlide()">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            
            <!-- Slider Indicators -->
            <div class="slider-indicators">
                <?php foreach ($slider_items as $index => $slide): ?>
                <button class="indicator <?php echo $index === 0 ? 'active' : ''; ?>" onclick="goToSlide(<?php echo $index; ?>)"></button>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Kategoriler -->
    <?php if (!empty($categories)): ?>
    <section class="categories-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-th-large"></i>
                Kategoriler
            </h2>
            <a href="<?php echo siteUrl('kategoriler.php'); ?>" class="btn btn-outline">
                Tümünü Gör <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="categories-grid">
            <?php foreach ($categories as $category): ?>
            <a href="<?php echo siteUrl('kategori/' . $category['slug']); ?>" class="category-card">
                <div class="category-image">
                    <img src="<?php echo $category['resim'] ? siteUrl('uploads/categories/' . $category['resim']) : siteUrl('assets/images/default-category.jpg'); ?>" alt="<?php echo safeOutput($category['kategori_adi']); ?>">
                    <div class="category-overlay">
                        <i class="fas fa-play-circle"></i>
                    </div>
                </div>
                <div class="category-info">
                    <h3><?php echo safeOutput($category['kategori_adi']); ?></h3>
                    <p><?php echo safeOutput($category['aciklama']); ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Son Eklenen Videolar -->
    <?php if (!empty($recent_videos)): ?>
    <section class="recent-videos-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-star"></i>
                Son Eklenen Videolar
            </h2>
            <a href="<?php echo siteUrl('yeni-videolar.php'); ?>" class="btn btn-outline">
                Tümünü Gör <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="video-grid">
            <?php foreach ($recent_videos as $video): ?>
            <div class="video-card <?php echo !canAccessVideo($video, $current_user) ? 'locked' : ''; ?>" 
                 onclick="<?php echo canAccessVideo($video, $current_user) ? "playVideo('" . $video['id'] . "')" : "showMembershipRequired('" . $video['goruntulenme_yetkisi'] . "')"; ?>">
                
                <div class="video-thumbnail">
                    <img src="<?php echo $video['kapak_resmi'] ? siteUrl('uploads/thumbnails/' . $video['kapak_resmi']) : siteUrl('assets/images/default-thumbnail.jpg'); ?>" 
                         alt="<?php echo safeOutput($video['baslik']); ?>">
                    
                    <div class="video-duration"><?php echo $video['sure'] ? formatDuration($video['sure']) : '00:00'; ?></div>
                    
                    <div class="video-quality-badge quality-<?php echo strtolower(getVideoQuality($video['goruntulenme_yetkisi'])); ?>">
                        <?php echo getVideoQuality($video['goruntulenme_yetkisi']); ?>
                    </div>
                    
                    <?php if (!canAccessVideo($video, $current_user)): ?>
                    <div class="membership-lock">
                        <i class="fas fa-lock"></i>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="video-info">
                    <h3 class="video-title"><?php echo safeOutput($video['baslik']); ?></h3>
                    <div class="video-meta">
                        <div class="video-stats">
                            <span><i class="fas fa-eye"></i> <?php echo number_format($video['izlenme_sayisi']); ?></span>
                            <span><i class="fas fa-calendar"></i> <?php echo formatDate($video['ekleme_tarihi'], 'd.m.Y'); ?></span>
                        </div>
                        <?php if ($video['kategori_adi']): ?>
                        <span class="video-category"><?php echo safeOutput($video['kategori_adi']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Popüler Videolar -->
    <?php if (!empty($popular_videos)): ?>
    <section class="popular-videos-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-fire"></i>
                Popüler Videolar
            </h2>
            <a href="<?php echo siteUrl('populer.php'); ?>" class="btn btn-outline">
                Tümünü Gör <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="video-grid">
            <?php foreach ($popular_videos as $video): ?>
            <div class="video-card <?php echo !canAccessVideo($video, $current_user) ? 'locked' : ''; ?>" 
                 onclick="<?php echo canAccessVideo($video, $current_user) ? "playVideo('" . $video['id'] . "')" : "showMembershipRequired('" . $video['goruntulenme_yetkisi'] . "')"; ?>">
                
                <div class="video-thumbnail">
                    <img src="<?php echo $video['kapak_resmi'] ? siteUrl('uploads/thumbnails/' . $video['kapak_resmi']) : siteUrl('assets/images/default-thumbnail.jpg'); ?>" 
                         alt="<?php echo safeOutput($video['baslik']); ?>">
                    
                    <div class="video-duration"><?php echo $video['sure'] ? formatDuration($video['sure']) : '00:00'; ?></div>
                    
                    <div class="video-quality-badge quality-<?php echo strtolower(getVideoQuality($video['goruntulenme_yetkisi'])); ?>">
                        <?php echo getVideoQuality($video['goruntulenme_yetkisi']); ?>
                    </div>
                    
                    <?php if (!canAccessVideo($video, $current_user)): ?>
                    <div class="membership-lock">
                        <i class="fas fa-lock"></i>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="video-info">
                    <h3 class="video-title"><?php echo safeOutput($video['baslik']); ?></h3>
                    <div class="video-meta">
                        <div class="video-stats">
                            <span><i class="fas fa-eye"></i> <?php echo number_format($video['izlenme_sayisi']); ?></span>
                            <span><i class="fas fa-thumbs-up"></i> <?php echo number_format($video['begeni_sayisi']); ?></span>
                        </div>
                        <?php if ($video['kategori_adi']): ?>
                        <span class="video-category"><?php echo safeOutput($video['kategori_adi']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- VIP Videolar -->
    <?php if (!empty($vip_videos)): ?>
    <section class="vip-videos-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-crown"></i>
                VIP Videolar
            </h2>
            <div class="membership-badge vip">VIP ÜYE ÖZEL</div>
        </div>
        
        <div class="video-grid">
            <?php foreach ($vip_videos as $video): ?>
            <div class="video-card" onclick="playVideo('<?php echo $video['id']; ?>')">
                <div class="video-thumbnail">
                    <img src="<?php echo $video['kapak_resmi'] ? siteUrl('uploads/thumbnails/' . $video['kapak_resmi']) : siteUrl('assets/images/default-thumbnail.jpg'); ?>" 
                         alt="<?php echo safeOutput($video['baslik']); ?>">
                    
                    <div class="video-duration"><?php echo $video['sure'] ? formatDuration($video['sure']) : '00:00'; ?></div>
                    
                    <div class="video-quality-badge quality-1080p">1080p</div>
                </div>
                
                <div class="video-info">
                    <h3 class="video-title"><?php echo safeOutput($video['baslik']); ?></h3>
                    <div class="video-meta">
                        <div class="video-stats">
                            <span><i class="fas fa-eye"></i> <?php echo number_format($video['izlenme_sayisi']); ?></span>
                            <span><i class="fas fa-crown"></i> VIP</span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Premium Videolar -->
    <?php if (!empty($premium_videos)): ?>
    <section class="premium-videos-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-gem"></i>
                Premium Videolar
            </h2>
            <div class="membership-badge premium">PREMIUM ÜYE ÖZEL</div>
        </div>
        
        <div class="video-grid">
            <?php foreach ($premium_videos as $video): ?>
            <div class="video-card" onclick="playVideo('<?php echo $video['id']; ?>')">
                <div class="video-thumbnail">
                    <img src="<?php echo $video['kapak_resmi'] ? siteUrl('uploads/thumbnails/' . $video['kapak_resmi']) : siteUrl('assets/images/default-thumbnail.jpg'); ?>" 
                         alt="<?php echo safeOutput($video['baslik']); ?>">
                    
                    <div class="video-duration"><?php echo $video['sure'] ? formatDuration($video['sure']) : '00:00'; ?></div>
                    
                    <div class="video-quality-badge quality-4k">4K</div>
                </div>
                
                <div class="video-info">
                    <h3 class="video-title"><?php echo safeOutput($video['baslik']); ?></h3>
                    <div class="video-meta">
                        <div class="video-stats">
                            <span><i class="fas fa-eye"></i> <?php echo number_format($video['izlenme_sayisi']); ?></span>
                            <span><i class="fas fa-gem"></i> Premium</span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Üyelik Yükselt Çağrısı -->
    <?php if (!$current_user || $current_user['uyelik_tipi'] == 'kullanici'): ?>
    <section class="upgrade-cta">
        <div class="cta-content">
            <div class="cta-text">
                <h2>
                    <i class="fas fa-star"></i>
                    Premium Deneyimi Keşfedin
                </h2>
                <p>4K video kalitesi, sınırsız izleme ve özel içeriklerle video deneyiminizi bir üst seviyeye taşıyın.</p>
                <div class="cta-features">
                    <div class="feature">
                        <i class="fas fa-check"></i>
                        <span>4K Ultra HD Video Kalitesi</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check"></i>
                        <span>Sınırsız Video İzleme</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check"></i>
                        <span>Özel Premium İçerikler</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check"></i>
                        <span>Reklamsız İzleme Deneyimi</span>
                    </div>
                </div>
            </div>
            <div class="cta-actions">
                <a href="<?php echo siteUrl('uyelik-yukselt.php'); ?>" class="btn btn-primary btn-large">
                    <i class="fas fa-crown"></i>
                    Üyeliği Yükselt
                </a>
                <a href="<?php echo siteUrl('premium-avantajlar.php'); ?>" class="btn btn-outline">
                    Avantajları Keşfet
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>
</div>

<!-- DOBİEN Developer Signature -->
<div class="dobien-signature">
    <i class="fas fa-code"></i> DOBİEN
</div>

<script>
/**
 * DOBİEN Video Platform - Ana Sayfa JavaScript
 * Geliştirici: DOBİEN
 */

// Yaş doğrulama fonksiyonları
function confirmAge() {
    // 1 yıl süreyle cookie oluştur
    const expirationDate = new Date();
    expirationDate.setFullYear(expirationDate.getFullYear() + 1);
    
    document.cookie = `age_verified=true; expires=${expirationDate.toUTCString()}; path=/`;
    
    document.getElementById('ageVerificationOverlay').style.display = 'none';
}

function denyAge() {
    alert('Üzgünüz, sitemiz sizin için uygun değildir. 18 yaş altındaki kullanıcılar siteye erişemez.');
    window.location.href = 'https://www.google.com';
}

// Slider fonksiyonları
let currentSlide = 0;
const slides = document.querySelectorAll('.hero-slide');
const indicators = document.querySelectorAll('.indicator');

function showSlide(index) {
    slides.forEach((slide, i) => {
        slide.classList.toggle('active', i === index);
    });
    
    indicators.forEach((indicator, i) => {
        indicator.classList.toggle('active', i === index);
    });
}

function nextSlide() {
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
}

function previousSlide() {
    currentSlide = (currentSlide - 1 + slides.length) % slides.length;
    showSlide(currentSlide);
}

function goToSlide(index) {
    currentSlide = index;
    showSlide(currentSlide);
}

// Otomatik slider
setInterval(nextSlide, 5000);

// Video oynatma fonksiyonları
function playVideo(videoId) {
    // AJAX ile video bilgilerini al ve modal'da göster
    fetch(`get-video.php?id=${videoId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Video modal'ını göster
                showVideoModal(data.video);
                // İzlenme sayısını artır
                updateViewCount(videoId);
            }
        })
        .catch(error => {
            console.error('Video yüklenirken hata:', error);
        });
}

function showMembershipRequired(requiredLevel) {
    let message = '';
    let upgradeUrl = '';
    
    switch(requiredLevel) {
        case 'vip':
            message = 'Bu video VIP üyeler için özeldir. VIP üyeliğe geçerek 1080p kalitesinde videolara erişebilirsiniz.';
            upgradeUrl = 'uyelik-yukselt.php?plan=vip';
            break;
        case 'premium':
            message = 'Bu video Premium üyeler için özeldir. Premium üyeliğe geçerek 4K kalitesinde videolara erişebilirsiniz.';
            upgradeUrl = 'uyelik-yukselt.php?plan=premium';
            break;
    }
    
    if (confirm(message + '\n\nÜyelik sayfasına gitmek ister misiniz?')) {
        window.location.href = upgradeUrl;
    }
}

function showVideoModal(video) {
    // Modal elementlerini doldur ve göster
    // Bu fonksiyon ayrı bir JavaScript dosyasında detaylandırılacak
}

function updateViewCount(videoId) {
    fetch('update-view-count.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ video_id: videoId })
    });
}

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
    
    console.log('DOBİEN Video Platform başarıyla yüklendi!');
});
</script>

<?php include 'includes/footer.php'; ?>