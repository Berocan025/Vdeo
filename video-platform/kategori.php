<?php
/**
 * DOBİEN Video Platform - Kategori Videoları
 * Geliştirici: DOBİEN
 * Modern Video Paylaşım Platformu
 * Tüm Hakları Saklıdır © DOBİEN
 */

require_once 'includes/config.php';

$category_slug = $_GET['slug'] ?? '';
if (empty($category_slug)) {
    header('Location: ' . siteUrl('kategoriler.php'));
    exit;
}

// Kategori bilgilerini çek
$category_query = $pdo->prepare("SELECT * FROM kategoriler WHERE slug = ? AND durum = 'aktif'");
$category_query->execute([$category_slug]);
$category = $category_query->fetch();

if (!$category) {
    header('Location: ' . siteUrl('kategoriler.php'));
    exit;
}

$page_title = $category['kategori_adi'] . " Videoları";
$page_description = $category['aciklama'];
$page_keywords = $category['kategori_adi'] . ", videolar, DOBİEN";

// Sayfalama
$videos_per_page = 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $videos_per_page;

// Bu kategorideki videoları çek
$videos_query = "
    SELECT v.*, k.kategori_adi, k.slug as kategori_slug
    FROM videolar v 
    LEFT JOIN kategoriler k ON v.kategori_id = k.id 
    WHERE v.kategori_id = ? AND v.durum = 'aktif'
    ORDER BY v.ekleme_tarihi DESC 
    LIMIT $videos_per_page OFFSET $offset
";
$videos_stmt = $pdo->prepare($videos_query);
$videos_stmt->execute([$category['id']]);
$videos = $videos_stmt->fetchAll();

// Toplam video sayısı
$total_videos_query = $pdo->prepare("SELECT COUNT(*) as total FROM videolar WHERE kategori_id = ? AND durum = 'aktif'");
$total_videos_query->execute([$category['id']]);
$total_videos = $total_videos_query->fetch()['total'];

$total_pages = ceil($total_videos / $videos_per_page);

include 'includes/header.php';
?>

<div class="container">
    <div class="category-header">
        <div class="category-info">
            <h1>
                <i class="fas fa-tag"></i>
                <?php echo safeOutput($category['kategori_adi']); ?>
            </h1>
            <p><?php echo safeOutput($category['aciklama']); ?></p>
            <div class="category-stats">
                <span><i class="fas fa-video"></i> <?php echo number_format($total_videos); ?> video</span>
            </div>
        </div>
    </div>
    
    <?php if (!empty($videos)): ?>
    <div class="video-grid">
        <?php foreach ($videos as $video): ?>
        <div class="video-card" onclick="window.location.href='<?php echo siteUrl('video.php?id=' . $video['id']); ?>'">
            <div class="video-thumbnail">
                <img src="<?php echo $video['kapak_resmi'] ? siteUrl('uploads/thumbnails/' . $video['kapak_resmi']) : siteUrl('assets/images/default-thumbnail.jpg'); ?>" 
                     alt="<?php echo safeOutput($video['baslik']); ?>">
                
                <div class="video-duration"><?php echo $video['sure'] ? formatDuration($video['sure']) : '00:00'; ?></div>
                
                <div class="video-quality-badge quality-<?php echo strtolower($video['goruntulenme_yetkisi'] == 'premium' ? '4k' : ($video['goruntulenme_yetkisi'] == 'vip' ? '1080p' : '720p')); ?>">
                    <?php echo $video['goruntulenme_yetkisi'] == 'premium' ? '4K' : ($video['goruntulenme_yetkisi'] == 'vip' ? '1080p' : '720p'); ?>
                </div>
            </div>
            
            <div class="video-info">
                <h3 class="video-title"><?php echo safeOutput($video['baslik']); ?></h3>
                <div class="video-meta">
                    <div class="video-stats">
                        <span><i class="fas fa-eye"></i> <?php echo number_format($video['izlenme_sayisi']); ?></span>
                        <span><i class="fas fa-calendar"></i> <?php echo formatDate($video['ekleme_tarihi'], 'd.m.Y'); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <?php if ($total_pages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
        <a href="?slug=<?php echo $category_slug; ?>&page=<?php echo $page - 1; ?>" class="pagination-btn">
            <i class="fas fa-chevron-left"></i> Önceki
        </a>
        <?php endif; ?>
        
        <span class="pagination-info">
            Sayfa <?php echo $page; ?> / <?php echo $total_pages; ?>
        </span>
        
        <?php if ($page < $total_pages): ?>
        <a href="?slug=<?php echo $category_slug; ?>&page=<?php echo $page + 1; ?>" class="pagination-btn">
            Sonraki <i class="fas fa-chevron-right"></i>
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <?php else: ?>
    <div class="empty-state">
        <i class="fas fa-video-slash"></i>
        <h3>Bu kategoride henüz video bulunmuyor</h3>
        <p>Yakında bu kategoriye videolar eklenecek.</p>
        <a href="<?php echo siteUrl('kategoriler.php'); ?>" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Kategorilere Dön
        </a>
    </div>
    <?php endif; ?>
</div>

<style>
.category-header {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 15px;
    padding: 40px;
    margin-bottom: 40px;
    text-align: center;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.category-header h1 {
    color: #fff;
    font-size: 2.5rem;
    margin: 0 0 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
}

.category-header h1 i {
    color: var(--primary-color);
}

.category-header p {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1.1rem;
    margin: 0 0 20px;
    line-height: 1.6;
}

.category-stats {
    color: var(--primary-color);
    font-weight: 500;
}

.category-stats i {
    margin-right: 5px;
}

.empty-state {
    text-align: center;
    padding: 80px 20px;
    color: rgba(255, 255, 255, 0.7);
}

.empty-state i {
    font-size: 4rem;
    color: var(--primary-color);
    margin-bottom: 20px;
    display: block;
}

.empty-state h3 {
    color: #fff;
    font-size: 1.5rem;
    margin: 0 0 10px;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 20px;
    margin-top: 40px;
}

.pagination-btn {
    background: var(--primary-color);
    color: #000;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.pagination-btn:hover {
    background: #e67e22;
    transform: translateY(-2px);
}

.pagination-info {
    color: rgba(255, 255, 255, 0.8);
    font-weight: 500;
}

@media (max-width: 768px) {
    .category-header h1 {
        font-size: 2rem;
        flex-direction: column;
        gap: 10px;
    }
    
    .pagination {
        flex-direction: column;
        gap: 15px;
    }
}
</style>

<?php include 'includes/footer.php'; ?>