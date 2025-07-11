<?php
/**
 * DOBİEN Video Platform - Popüler Videolar
 * Geliştirici: DOBİEN
 * Modern Video Paylaşım Platformu
 * Tüm Hakları Saklıdır © DOBİEN
 */

require_once 'includes/config.php';

$page_title = "Popüler Videolar";
$page_description = "En çok izlenen ve beğenilen popüler video içeriklerini keşfedin";
$page_keywords = "popüler videolar, trend videolar, en çok izlenen, DOBİEN";

// Sayfalama
$videos_per_page = 24;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $videos_per_page;

// Popüler videoları çek
$videos_query = "
    SELECT v.*, k.kategori_adi, k.slug as kategori_slug
    FROM videolar v 
    LEFT JOIN kategoriler k ON v.kategori_id = k.id 
    WHERE v.durum = 'aktif' 
    AND (v.goruntulenme_yetkisi = 'herkes' OR ? != '')
    ORDER BY v.izlenme_sayisi DESC, v.begeni_sayisi DESC
    LIMIT $videos_per_page OFFSET $offset
";
$videos_stmt = $pdo->prepare($videos_query);
$videos_stmt->execute([$current_user ? $current_user['uyelik_tipi'] : '']);
$videos = $videos_stmt->fetchAll();

// Toplam video sayısı
$total_query = "
    SELECT COUNT(*) as total 
    FROM videolar v 
    WHERE v.durum = 'aktif' 
    AND (v.goruntulenme_yetkisi = 'herkes' OR ? != '')
";
$total_stmt = $pdo->prepare($total_query);
$total_stmt->execute([$current_user ? $current_user['uyelik_tipi'] : '']);
$total_videos = $total_stmt->fetch()['total'];
$total_pages = ceil($total_videos / $videos_per_page);

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

<div class="container">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-fire"></i>
            Popüler Videolar
        </h1>
        <p class="page-description">
            En çok izlenen ve beğenilen video içeriklerini keşfedin. Trend olan videolardan kaçırmayın!
        </p>
        
        <div class="page-stats">
            <div class="stat">
                <i class="fas fa-video"></i>
                <span><?php echo number_format($total_videos); ?> Video</span>
            </div>
            <div class="stat">
                <i class="fas fa-eye"></i>
                <span>Popülerlik Sıralaması</span>
            </div>
        </div>
    </div>

    <?php if (!empty($videos)): ?>
    <div class="video-grid">
        <?php foreach ($videos as $video): ?>
        <div class="video-card <?php echo !canAccessVideo($video, $current_user) ? 'locked' : ''; ?>" 
             onclick="<?php echo canAccessVideo($video, $current_user) ? "window.location.href='" . siteUrl('video.php?id=' . $video['id']) . "'" : "showMembershipRequired('" . $video['goruntulenme_yetkisi'] . "')"; ?>">
            
            <div class="video-thumbnail">
                <img src="<?php echo $video['kapak_resmi'] ? siteUrl('uploads/thumbnails/' . $video['kapak_resmi']) : siteUrl('assets/images/default-thumbnail.jpg'); ?>" 
                     alt="<?php echo safeOutput($video['baslik']); ?>"
                     loading="lazy">
                
                <div class="video-duration"><?php echo $video['sure'] ? formatDuration($video['sure']) : '00:00'; ?></div>
                
                <div class="video-quality-badge quality-<?php echo strtolower(getVideoQuality($video['goruntulenme_yetkisi'])); ?>">
                    <?php echo getVideoQuality($video['goruntulenme_yetkisi']); ?>
                </div>
                
                <?php if (!canAccessVideo($video, $current_user)): ?>
                <div class="membership-lock">
                    <i class="fas fa-lock"></i>
                </div>
                <?php endif; ?>
                
                <div class="popularity-badge">
                    <i class="fas fa-fire"></i>
                    Popüler
                </div>
            </div>
            
            <div class="video-info">
                <h3 class="video-title"><?php echo safeOutput($video['baslik']); ?></h3>
                <div class="video-meta">
                    <div class="video-stats">
                        <span class="views">
                            <i class="fas fa-eye"></i> 
                            <?php echo number_format($video['izlenme_sayisi']); ?>
                        </span>
                        <span class="likes">
                            <i class="fas fa-thumbs-up"></i> 
                            <?php echo number_format($video['begeni_sayisi']); ?>
                        </span>
                    </div>
                    <div class="video-date">
                        <i class="fas fa-calendar"></i>
                        <?php echo formatDate($video['ekleme_tarihi'], 'd.m.Y'); ?>
                    </div>
                </div>
                <?php if ($video['kategori_adi']): ?>
                <span class="video-category"><?php echo safeOutput($video['kategori_adi']); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Sayfalama -->
    <?php if ($total_pages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>" class="pagination-btn">
            <i class="fas fa-chevron-left"></i>
            Önceki
        </a>
        <?php endif; ?>
        
        <div class="pagination-numbers">
            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
            <a href="?page=<?php echo $i; ?>" 
               class="pagination-number <?php echo $i === $page ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
            <?php endfor; ?>
        </div>
        
        <?php if ($page < $total_pages): ?>
        <a href="?page=<?php echo $page + 1; ?>" class="pagination-btn">
            Sonraki
            <i class="fas fa-chevron-right"></i>
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <?php else: ?>
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-fire-alt"></i>
        </div>
        <h3>Henüz popüler video bulunmuyor</h3>
        <p>Videolar izlendikçe popüler olanlar burada görünecek.</p>
    </div>
    <?php endif; ?>
</div>

<style>
.page-header {
    text-align: center;
    margin-bottom: 40px;
    padding: 40px 0;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 15px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.page-title {
    color: #fff;
    font-size: 2.5rem;
    margin: 0 0 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
}

.page-title i {
    color: #ff6b35;
    text-shadow: 0 0 20px rgba(255, 107, 53, 0.5);
}

.page-description {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1.1rem;
    margin: 0 0 20px;
    max-width: 600px;
    margin: 0 auto 20px;
    line-height: 1.6;
}

.page-stats {
    display: flex;
    justify-content: center;
    gap: 30px;
    margin-top: 20px;
}

.stat {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--primary-color);
    font-weight: 500;
}

.popularity-badge {
    position: absolute;
    top: 8px;
    left: 8px;
    background: linear-gradient(135deg, #ff6b35, #f7931e);
    color: #fff;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 0.7rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

.video-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
}

.video-card {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    cursor: pointer;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.video-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
    border-color: rgba(255, 107, 53, 0.5);
}

.video-thumbnail {
    position: relative;
    aspect-ratio: 16/9;
    overflow: hidden;
}

.video-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.video-card:hover .video-thumbnail img {
    transform: scale(1.05);
}

.video-duration {
    position: absolute;
    bottom: 8px;
    right: 8px;
    background: rgba(0, 0, 0, 0.8);
    color: #fff;
    padding: 3px 6px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.video-quality-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    padding: 3px 6px;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 600;
    color: #000;
}

.quality-720p { background: #28a745; }
.quality-1080p { background: var(--primary-color); }
.quality-4k { background: #e74c3c; }

.membership-lock {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 2rem;
}

.video-info {
    padding: 15px;
}

.video-title {
    color: #fff;
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 10px;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.video-meta {
    margin-bottom: 10px;
}

.video-stats {
    display: flex;
    gap: 15px;
    margin-bottom: 5px;
}

.video-stats span {
    display: flex;
    align-items: center;
    gap: 4px;
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.8rem;
}

.video-date {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 4px;
}

.video-category {
    background: rgba(255, 255, 255, 0.1);
    color: var(--primary-color);
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
    display: inline-block;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    margin: 40px 0;
}

.pagination-btn,
.pagination-number {
    padding: 10px 15px;
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.pagination-btn:hover,
.pagination-number:hover {
    background: var(--primary-color);
    color: #000;
    transform: translateY(-2px);
}

.pagination-number.active {
    background: var(--primary-color);
    color: #000;
    font-weight: 600;
}

.pagination-numbers {
    display: flex;
    gap: 5px;
}

.empty-state {
    text-align: center;
    padding: 80px 20px;
    color: rgba(255, 255, 255, 0.7);
}

.empty-icon {
    font-size: 4rem;
    color: #ff6b35;
    margin-bottom: 20px;
}

.empty-state h3 {
    color: #fff;
    font-size: 1.5rem;
    margin: 0 0 10px;
}

@media (max-width: 768px) {
    .page-title {
        font-size: 2rem;
        flex-direction: column;
        gap: 10px;
    }
    
    .page-stats {
        flex-direction: column;
        gap: 15px;
    }
    
    .video-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }
    
    .pagination {
        flex-wrap: wrap;
        gap: 5px;
    }
    
    .pagination-btn,
    .pagination-number {
        padding: 8px 12px;
        font-size: 0.9rem;
    }
}
</style>

<script>
function showMembershipRequired(membership) {
    const membershipText = membership === 'vip' ? 'VIP' : 'Premium';
    alert(`Bu video için ${membershipText} üyelik gereklidir. Üyeliğinizi yükseltmek için profil sayfanızı ziyaret edin.`);
}
</script>

<?php include 'includes/footer.php'; ?>