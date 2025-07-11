<?php
/**
 * DOBİEN Video Platform - VIP Videolar Sayfası
 * Geliştirici: DOBİEN
 * VIP üyeler için özel içerikler
 */

require_once 'includes/config.php';

$page_title = "VIP Videolar";
$page_description = "VIP üyeler için özel video içerikleri - DOBİEN Video Platform";

// Veritabanı tablolarını kontrol et
try {
    $pdo->query("SELECT 1 FROM videolar LIMIT 1");
    $pdo->query("SELECT 1 FROM kategoriler LIMIT 1");
} catch (PDOException $e) {
    header('Location: install.php');
    exit;
}

// VIP kontrolü
if (!$current_user || ($current_user['uyelik_tipi'] != 'vip' && $current_user['uyelik_tipi'] != 'premium')) {
    // VIP değilse yönlendir
    header('Location: uyelik-yukselt.php?plan=vip');
    exit;
}

// VIP videoları çek
try {
    $vip_videos_query = "
        SELECT v.*, k.kategori_adi, k.slug as kategori_slug
        FROM videolar v 
        LEFT JOIN kategoriler k ON v.kategori_id = k.id 
        WHERE v.durum = 'aktif' 
        AND v.goruntulenme_yetkisi IN ('vip', 'premium')
        ORDER BY v.ekleme_tarihi DESC 
        LIMIT 50
    ";
    $vip_videos = $pdo->query($vip_videos_query)->fetchAll();
} catch (PDOException $e) {
    $vip_videos = [];
}

include 'includes/header.php';
?>

<div class="container">
    <!-- VIP Başlık -->
    <section class="vip-header">
        <div class="vip-banner">
            <div class="vip-content">
                <div class="vip-icon">
                    <i class="fas fa-crown"></i>
                </div>
                <h1>VIP Videolar</h1>
                <p>VIP üyeler için özel seçilmiş premium içerikler</p>
                <div class="vip-stats">
                    <span class="stat">
                        <i class="fas fa-video"></i>
                        <?php echo count($vip_videos); ?> Özel Video
                    </span>
                    <span class="stat">
                        <i class="fas fa-hd-video"></i>
                        1080p HD Kalite
                    </span>
                    <span class="stat">
                        <i class="fas fa-clock"></i>
                        Sınırsız İzleme
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- VIP Videolar -->
    <section class="vip-videos">
        <?php if (!empty($vip_videos)): ?>
        <div class="video-grid">
            <?php foreach ($vip_videos as $video): ?>
            <div class="video-card vip-video" onclick="playVideo('<?php echo $video['id']; ?>')">
                <div class="video-thumbnail">
                    <img src="<?php echo $video['kapak_resmi'] ? siteUrl('uploads/thumbnails/' . $video['kapak_resmi']) : siteUrl('assets/images/default-thumbnail.jpg'); ?>" 
                         alt="<?php echo safeOutput($video['baslik']); ?>">
                    
                    <div class="video-duration"><?php echo $video['sure'] ? formatDuration($video['sure']) : '00:00'; ?></div>
                    
                    <div class="vip-badge">
                        <i class="fas fa-crown"></i> VIP
                    </div>
                    
                    <div class="video-quality-badge quality-1080p">1080p HD</div>
                    
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
                <i class="fas fa-crown"></i>
                <h3>VIP Videolar Hazırlanıyor</h3>
                <p>Yakında özel VIP içerikler eklenecek!</p>
            </div>
        </div>
        <?php endif; ?>
    </section>

    <!-- VIP Avantajları -->
    <section class="vip-benefits">
        <h2><i class="fas fa-star"></i> VIP Üyelik Avantajlarınız</h2>
        <div class="benefits-grid">
            <div class="benefit-card">
                <i class="fas fa-hd-video"></i>
                <h3>1080p HD Kalite</h3>
                <p>Tüm videolarımızı kristal netliğinde izleyin</p>
            </div>
            <div class="benefit-card">
                <i class="fas fa-crown"></i>
                <h3>Özel İçerikler</h3>
                <p>Sadece VIP üyeler için hazırlanmış içerikler</p>
            </div>
            <div class="benefit-card">
                <i class="fas fa-clock"></i>
                <h3>Sınırsız İzleme</h3>
                <p>İstediğiniz kadar video izleyin, limit yok</p>
            </div>
            <div class="benefit-card">
                <i class="fas fa-ad"></i>
                <h3>Reklamsız Deneyim</h3>
                <p>Hiç reklam olmadan kesintisiz izleme</p>
            </div>
        </div>
        
        <?php if ($current_user['uyelik_tipi'] == 'vip'): ?>
        <div class="premium-upgrade">
            <h3><i class="fas fa-gem"></i> Premium'a Yükseltmek İster misiniz?</h3>
            <p>Premium üyelik ile 4K Ultra HD kalite ve daha fazla özel içerik</p>
            <a href="<?php echo siteUrl('uyelik-yukselt.php?plan=premium'); ?>" class="btn btn-primary">
                <i class="fas fa-arrow-up"></i> Premium'a Yükselt
            </a>
        </div>
        <?php endif; ?>
    </section>
</div>

<style>
/* VIP Sayfası Stilleri */
.vip-header {
    margin-bottom: 3rem;
}

.vip-banner {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    border-radius: var(--radius-xl);
    padding: 3rem 2rem;
    text-align: center;
    color: white;
    position: relative;
    overflow: hidden;
}

.vip-banner::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="crown-pattern" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M10 5 L15 15 L5 15 Z" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23crown-pattern)"/></svg>');
    pointer-events: none;
}

.vip-content {
    position: relative;
    z-index: 2;
}

.vip-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    color: #fbbf24;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.vip-banner h1 {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 1rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.vip-banner p {
    font-size: 1.2rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

.vip-stats {
    display: flex;
    justify-content: center;
    gap: 2rem;
    flex-wrap: wrap;
}

.vip-stats .stat {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255,255,255,0.2);
    padding: 0.75rem 1.5rem;
    border-radius: var(--radius-lg);
    backdrop-filter: blur(10px);
    font-weight: 600;
}

.vip-video {
    position: relative;
    overflow: hidden;
}

.vip-video::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(245, 158, 11, 0.1) 0%, transparent 100%);
    z-index: 1;
    pointer-events: none;
}

.vip-badge {
    position: absolute;
    top: 0.5rem;
    left: 0.5rem;
    background: linear-gradient(135deg, #f59e0b, #d97706);
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

.vip-video:hover .video-overlay {
    opacity: 1;
}

.vip-benefits {
    background: var(--gradient-card);
    border-radius: var(--radius-xl);
    padding: 3rem;
    margin-top: 3rem;
    border: 1px solid var(--border-color);
}

.vip-benefits h2 {
    text-align: center;
    font-size: 2rem;
    margin-bottom: 2rem;
    color: var(--text-primary);
}

.benefits-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
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
    border-color: #f59e0b;
}

.benefit-card i {
    font-size: 2rem;
    color: #f59e0b;
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

.premium-upgrade {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    padding: 2rem;
    border-radius: var(--radius-lg);
    text-align: center;
    color: white;
    margin-top: 2rem;
}

.premium-upgrade h3 {
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.premium-upgrade p {
    margin-bottom: 1.5rem;
    opacity: 0.9;
}

.no-videos {
    text-align: center;
    padding: 4rem 2rem;
}

.no-videos-content i {
    font-size: 4rem;
    color: #f59e0b;
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .vip-banner {
        padding: 2rem 1rem;
    }
    
    .vip-banner h1 {
        font-size: 2rem;
    }
    
    .vip-stats {
        gap: 1rem;
    }
    
    .vip-stats .stat {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }
    
    .benefits-grid {
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