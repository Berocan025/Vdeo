<?php
/**
 * DOBİEN Video Platform - Premium Videolar Sayfası
 * Geliştirici: DOBİEN
 * Premium üyeler için 4K kalite özel içerikler
 */

require_once 'includes/config.php';

$page_title = "Premium Videolar";
$page_description = "Premium üyeler için 4K kalite özel video içerikleri - DOBİEN Video Platform";

// Veritabanı tablolarını kontrol et
try {
    $pdo->query("SELECT 1 FROM videolar LIMIT 1");
    $pdo->query("SELECT 1 FROM kategoriler LIMIT 1");
} catch (PDOException $e) {
    header('Location: install.php');
    exit;
}

// Premium kontrolü
if (!$current_user || $current_user['uyelik_tipi'] != 'premium') {
    // Premium değilse yönlendir
    header('Location: uyelik-yukselt.php?plan=premium');
    exit;
}

// Premium videoları çek
try {
    $premium_videos_query = "
        SELECT v.*, k.kategori_adi, k.slug as kategori_slug
        FROM videolar v 
        LEFT JOIN kategoriler k ON v.kategori_id = k.id 
        WHERE v.durum = 'aktif' 
        AND v.goruntulenme_yetkisi = 'premium'
        ORDER BY v.ekleme_tarihi DESC 
        LIMIT 50
    ";
    $premium_videos = $pdo->query($premium_videos_query)->fetchAll();
} catch (PDOException $e) {
    $premium_videos = [];
}

include 'includes/header.php';
?>

<div class="container">
    <!-- Premium Başlık -->
    <section class="premium-header">
        <div class="premium-banner">
            <div class="premium-content">
                <div class="premium-icon">
                    <i class="fas fa-gem"></i>
                </div>
                <h1>Premium Videolar</h1>
                <p>Premium üyeler için 4K Ultra HD kalite özel içerikler</p>
                <div class="premium-stats">
                    <span class="stat">
                        <i class="fas fa-video"></i>
                        <?php echo count($premium_videos); ?> Premium Video
                    </span>
                    <span class="stat">
                        <i class="fas fa-magic"></i>
                        4K Ultra HD
                    </span>
                    <span class="stat">
                        <i class="fas fa-infinity"></i>
                        Sınırsız İzleme
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- Premium Videolar -->
    <section class="premium-videos">
        <?php if (!empty($premium_videos)): ?>
        <div class="video-grid">
            <?php foreach ($premium_videos as $video): ?>
            <div class="video-card premium-video" onclick="playVideo('<?php echo $video['id']; ?>')">
                <div class="video-thumbnail">
                    <img src="<?php echo $video['kapak_resmi'] ? siteUrl('uploads/thumbnails/' . $video['kapak_resmi']) : siteUrl('assets/images/default-thumbnail.jpg'); ?>" 
                         alt="<?php echo safeOutput($video['baslik']); ?>">
                    
                    <div class="video-duration"><?php echo $video['sure'] ? formatDuration($video['sure']) : '00:00'; ?></div>
                    
                    <div class="premium-badge">
                        <i class="fas fa-gem"></i> PREMIUM
                    </div>
                    
                    <div class="video-quality-badge quality-4k">4K Ultra HD</div>
                    
                    <div class="video-overlay">
                        <i class="fas fa-play-circle"></i>
                    </div>
                </div>
                
                <div class="video-info">
                    <h3 class="video-title"><?php echo safeOutput($video['baslik']); ?></h3>
                    <div class="video-meta">
                        <div class="video-stats">
                            <span><i class="fas fa-eye"></i> <?php echo number_format($video['izlenme_sayisi']); ?></span>
                            <span><i class="fas fa-thumbs-up"></i> <?php echo number_format($video['begeni_sayisi']); ?></span>
                            <span><i class="fas fa-calendar"></i> <?php echo formatDate($video['ekleme_tarihi'], 'd.m.Y'); ?></span>
                        </div>
                        <?php if ($video['kategori_adi']): ?>
                        <a href="<?php echo siteUrl('kategori/' . $video['kategori_slug']); ?>" class="video-category">
                            <?php echo safeOutput($video['kategori_adi']); ?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="no-videos">
            <div class="no-videos-content">
                <i class="fas fa-gem"></i>
                <h3>Premium Videolar Hazırlanıyor</h3>
                <p>En kaliteli 4K videolar çok yakında!</p>
            </div>
        </div>
        <?php endif; ?>
    </section>

    <!-- Premium Avantajları -->
    <section class="premium-benefits">
        <h2><i class="fas fa-crown"></i> Premium Üyelik Avantajlarınız</h2>
        <div class="benefits-grid">
            <div class="benefit-card">
                <i class="fas fa-magic"></i>
                <h3>4K Ultra HD</h3>
                <p>En yüksek kalitede kristal netliği</p>
            </div>
            <div class="benefit-card">
                <i class="fas fa-gem"></i>
                <h3>Özel Premium İçerik</h3>
                <p>Sadece Premium üyeler için özel videolar</p>
            </div>
            <div class="benefit-card">
                <i class="fas fa-bolt"></i>
                <h3>Öncelikli Erişim</h3>
                <p>Yeni videolara ilk siz erişin</p>
            </div>
            <div class="benefit-card">
                <i class="fas fa-cloud-download-alt"></i>
                <h3>Offline İzleme</h3>
                <p>Videoları indirip offline izleyin</p>
            </div>
            <div class="benefit-card">
                <i class="fas fa-headphones"></i>
                <h3>Premium Destek</h3>
                <p>7/24 öncelikli müşteri desteği</p>
            </div>
            <div class="benefit-card">
                <i class="fas fa-users"></i>
                <h3>Çoklu Cihaz</h3>
                <p>5 cihazda aynı anda izleme</p>
            </div>
        </div>
        
        <div class="premium-exclusive">
            <h3><i class="fas fa-star"></i> Premium Özel Ayrıcalıklar</h3>
            <div class="exclusive-features">
                <div class="feature">
                    <i class="fas fa-trophy"></i>
                    <span>Haftalık özel yayınlar</span>
                </div>
                <div class="feature">
                    <i class="fas fa-gift"></i>
                    <span>Aylık hediye içerikler</span>
                </div>
                <div class="feature">
                    <i class="fas fa-calendar-star"></i>
                    <span>Özel etkinliklere davet</span>
                </div>
                <div class="feature">
                    <i class="fas fa-medal"></i>
                    <span>Premium rozet ve profil</span>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
/* Premium Sayfası Stilleri */
.premium-header {
    margin-bottom: 3rem;
}

.premium-banner {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    border-radius: var(--radius-xl);
    padding: 3rem 2rem;
    text-align: center;
    color: white;
    position: relative;
    overflow: hidden;
}

.premium-banner::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="gem-pattern" width="25" height="25" patternUnits="userSpaceOnUse"><polygon points="12.5,5 20,12.5 12.5,20 5,12.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23gem-pattern)"/></svg>');
    pointer-events: none;
}

.premium-content {
    position: relative;
    z-index: 2;
}

.premium-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    color: #c4b5fd;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.premium-banner h1 {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 1rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.premium-banner p {
    font-size: 1.2rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

.premium-stats {
    display: flex;
    justify-content: center;
    gap: 2rem;
    flex-wrap: wrap;
}

.premium-stats .stat {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255,255,255,0.2);
    padding: 0.75rem 1.5rem;
    border-radius: var(--radius-lg);
    backdrop-filter: blur(10px);
    font-weight: 600;
}

.premium-video {
    position: relative;
    overflow: hidden;
}

.premium-video::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(139, 92, 246, 0.1) 0%, transparent 100%);
    z-index: 1;
    pointer-events: none;
}

.premium-badge {
    position: absolute;
    top: 0.5rem;
    left: 0.5rem;
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: var(--radius-md);
    font-size: 0.75rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    z-index: 2;
}

.video-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 3rem;
    opacity: 0;
    transition: var(--transition);
    z-index: 2;
}

.premium-video:hover .video-overlay {
    opacity: 1;
}

.premium-benefits {
    background: var(--gradient-card);
    border-radius: var(--radius-xl);
    padding: 3rem;
    margin-top: 3rem;
    border: 1px solid var(--border-color);
}

.premium-benefits h2 {
    text-align: center;
    font-size: 2rem;
    margin-bottom: 2rem;
    color: var(--text-primary);
}

.benefits-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.benefit-card {
    background: var(--bg-tertiary);
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    text-align: center;
    border: 1px solid var(--border-color);
    transition: var(--transition);
}

.benefit-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
    border-color: #8b5cf6;
}

.benefit-card i {
    font-size: 2rem;
    color: #8b5cf6;
    margin-bottom: 1rem;
}

.benefit-card h3 {
    font-size: 1.2rem;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
}

.benefit-card p {
    color: var(--text-muted);
    font-size: 0.9rem;
}

.premium-exclusive {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    padding: 2rem;
    border-radius: var(--radius-lg);
    text-align: center;
    color: white;
}

.premium-exclusive h3 {
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
}

.exclusive-features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.feature {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255,255,255,0.2);
    padding: 0.75rem 1rem;
    border-radius: var(--radius-md);
    backdrop-filter: blur(10px);
}

.feature i {
    font-size: 1.25rem;
}

.no-videos {
    text-align: center;
    padding: 4rem 2rem;
}

.no-videos-content i {
    font-size: 4rem;
    color: #8b5cf6;
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .premium-banner {
        padding: 2rem 1rem;
    }
    
    .premium-banner h1 {
        font-size: 2rem;
    }
    
    .premium-stats {
        gap: 1rem;
    }
    
    .premium-stats .stat {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }
    
    .benefits-grid {
        grid-template-columns: 1fr;
    }
    
    .exclusive-features {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function playVideo(videoId) {
    window.location.href = 'video.php?id=' + videoId;
}
</script>

<?php include 'includes/footer.php'; ?>