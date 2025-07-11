<?php
/**
 * DOBİEN Video Platform - Yeni Videolar Sayfası
 * Geliştirici: DOBİEN
 * Son eklenen video içerikleri
 */

require_once 'includes/config.php';

$page_title = "Yeni Videolar";
$page_description = "En son eklenen video içeriklerini keşfedin - DOBİEN Video Platform";

// Sayfalama ayarları
$videos_per_page = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $videos_per_page;

// Yeni videoları çek
try {
    $videos_query = "
        SELECT v.*, k.kategori_adi, k.slug as kategori_slug
        FROM videolar v 
        LEFT JOIN kategoriler k ON v.kategori_id = k.id 
        WHERE v.durum = 'aktif' 
        AND (v.goruntulenme_yetkisi = 'herkes' OR ? != '')
        ORDER BY v.ekleme_tarihi DESC 
        LIMIT $videos_per_page OFFSET $offset
    ";
    $stmt = $pdo->prepare($videos_query);
    $stmt->execute([$current_user ? $current_user['uyelik_tipi'] : '']);
    $videos = $stmt->fetchAll();

    // Toplam video sayısı
    $total_query = "
        SELECT COUNT(*) 
        FROM videolar v 
        WHERE v.durum = 'aktif' 
        AND (v.goruntulenme_yetkisi = 'herkes' OR ? != '')
    ";
    $total_stmt = $pdo->prepare($total_query);
    $total_stmt->execute([$current_user ? $current_user['uyelik_tipi'] : '']);
    $total_videos = $total_stmt->fetchColumn();
    $total_pages = ceil($total_videos / $videos_per_page);

} catch (PDOException $e) {
    $videos = [];
    $total_videos = 0;
    $total_pages = 0;
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
    <!-- Sayfa Başlığı -->
    <section class="page-header">
        <div class="page-header-content">
            <h1><i class="fas fa-star"></i> Yeni Videolar</h1>
            <p>En son eklenen video içeriklerini keşfedin</p>
            <div class="stats">
                <span><?php echo number_format($total_videos); ?> video bulundu</span>
            </div>
        </div>
    </section>

    <!-- Filtreler -->
    <section class="filters-section">
        <div class="filters-container">
            <div class="filter-item">
                <label>Sıralama:</label>
                <select id="sortOrder" onchange="changeSorting()">
                    <option value="newest">En Yeni</option>
                    <option value="popular">En Popüler</option>
                    <option value="most_viewed">En Çok İzlenen</option>
                    <option value="highest_rated">En Beğenilen</option>
                </select>
            </div>
            <div class="filter-item">
                <label>Kalite:</label>
                <select id="qualityFilter" onchange="filterByQuality()">
                    <option value="all">Tümü</option>
                    <option value="4k">4K Ultra HD</option>
                    <option value="1080p">Full HD 1080p</option>
                    <option value="720p">HD 720p</option>
                </select>
            </div>
        </div>
    </section>

    <!-- Videolar Grid -->
    <section class="videos-section">
        <?php if (!empty($videos)): ?>
        <div class="video-grid">
            <?php foreach ($videos as $video): ?>
            <div class="video-card <?php echo !canAccessVideo($video, $current_user) ? 'locked' : ''; ?>" 
                 onclick="<?php echo canAccessVideo($video, $current_user) ? "playVideo('" . $video['id'] . "')" : "showMembershipRequired('" . $video['goruntulenme_yetkisi'] . "')"; ?>">
                
                <div class="video-thumbnail">
                    <img src="<?php echo $video['kapak_resmi'] ? siteUrl('uploads/thumbnails/' . $video['kapak_resmi']) : siteUrl('assets/images/default-thumbnail.jpg'); ?>" 
                         alt="<?php echo safeOutput($video['baslik']); ?>">
                    
                    <div class="video-duration"><?php echo $video['sure'] ? formatDuration($video['sure']) : '00:00'; ?></div>
                    
                    <?php
                    $quality = 'HD';
                    if ($video['goruntulenme_yetkisi'] == 'premium') $quality = '4K';
                    elseif ($video['goruntulenme_yetkisi'] == 'vip') $quality = '1080p';
                    ?>
                    <div class="video-quality-badge quality-<?php echo strtolower($quality); ?>">
                        <?php echo $quality; ?>
                    </div>
                    
                    <div class="video-overlay">
                        <i class="fas fa-play-circle"></i>
                    </div>
                    
                    <?php if (!canAccessVideo($video, $current_user)): ?>
                    <div class="membership-lock">
                        <i class="fas fa-lock"></i>
                        <span><?php echo strtoupper($video['goruntulenme_yetkisi']); ?></span>
                    </div>
                    <?php endif; ?>
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

        <!-- Sayfalama -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination-container">
            <div class="pagination">
                <?php if ($page > 1): ?>
                <a href="?page=<?php echo ($page - 1); ?>" class="prev-page">
                    <i class="fas fa-chevron-left"></i> Önceki
                </a>
                <?php endif; ?>
                
                <?php
                $start = max(1, $page - 2);
                $end = min($total_pages, $page + 2);
                
                if ($start > 1) {
                    echo '<a href="?page=1">1</a>';
                    if ($start > 2) echo '<span class="dots">...</span>';
                }
                
                for ($i = $start; $i <= $end; $i++) {
                    if ($i == $page) {
                        echo '<span class="current">' . $i . '</span>';
                    } else {
                        echo '<a href="?page=' . $i . '">' . $i . '</a>';
                    }
                }
                
                if ($end < $total_pages) {
                    if ($end < $total_pages - 1) echo '<span class="dots">...</span>';
                    echo '<a href="?page=' . $total_pages . '">' . $total_pages . '</a>';
                }
                ?>
                
                <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo ($page + 1); ?>" class="next-page">
                    Sonraki <i class="fas fa-chevron-right"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <div class="no-videos">
            <div class="no-videos-content">
                <i class="fas fa-video"></i>
                <h3>Henüz Video Yok</h3>
                <p>Yakında harika videolar eklenecek!</p>
                <a href="<?php echo siteUrl(); ?>" class="btn btn-primary">Ana Sayfaya Dön</a>
            </div>
        </div>
        <?php endif; ?>
    </section>

    <!-- Üyelik Çağrısı -->
    <?php if (!$current_user || $current_user['uyelik_tipi'] == 'kullanici'): ?>
    <section class="membership-cta">
        <div class="cta-content">
            <h2><i class="fas fa-crown"></i> Premium Videoları Kaçırmayın!</h2>
            <p>Premium ve VIP üyelik ile tüm videolara sınırsız erişim sağlayın</p>
            <div class="cta-buttons">
                <a href="<?php echo siteUrl('uyelik-yukselt.php'); ?>" class="btn btn-primary">
                    <i class="fas fa-star"></i> Üyeliği Yükselt
                </a>
                <a href="<?php echo siteUrl('premium-avantajlar.php'); ?>" class="btn btn-outline">
                    Avantajları Gör
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>
</div>

<style>
/* Yeni Videolar Sayfası Stilleri */
.page-header {
    text-align: center;
    padding: 3rem 0;
    background: var(--gradient-secondary);
    border-radius: var(--radius-xl);
    margin-bottom: 2rem;
}

.page-header h1 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    color: var(--text-primary);
}

.page-header p {
    font-size: 1.1rem;
    color: var(--text-muted);
    margin-bottom: 1rem;
}

.stats {
    color: var(--primary-color);
    font-weight: 600;
}

.filters-section {
    margin-bottom: 2rem;
}

.filters-container {
    display: flex;
    gap: 2rem;
    flex-wrap: wrap;
    align-items: center;
    background: var(--bg-card);
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    border: 1px solid var(--border-color);
}

.filter-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-item label {
    color: var(--text-secondary);
    font-weight: 500;
    font-size: 0.9rem;
}

.filter-item select {
    background: var(--bg-tertiary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    padding: 0.5rem 1rem;
    color: var(--text-primary);
    font-size: 0.9rem;
    min-width: 150px;
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
    pointer-events: none;
}

.video-card:hover .video-overlay {
    opacity: 1;
}

.pagination-container {
    margin-top: 3rem;
    display: flex;
    justify-content: center;
}

.pagination {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    background: var(--bg-card);
    padding: 1rem;
    border-radius: var(--radius-lg);
    border: 1px solid var(--border-color);
}

.pagination a,
.pagination span {
    padding: 0.5rem 1rem;
    border-radius: var(--radius-md);
    text-decoration: none;
    color: var(--text-secondary);
    transition: var(--transition);
}

.pagination a:hover {
    background: var(--primary-color);
    color: white;
}

.pagination .current {
    background: var(--primary-color);
    color: white;
    font-weight: 600;
}

.pagination .dots {
    color: var(--text-muted);
}

.no-videos {
    text-align: center;
    padding: 4rem 2rem;
}

.no-videos-content i {
    font-size: 4rem;
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.no-videos-content h3 {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    color: var(--text-primary);
}

.membership-cta {
    background: var(--gradient-card);
    border-radius: var(--radius-xl);
    padding: 3rem;
    text-align: center;
    margin-top: 3rem;
    border: 1px solid var(--border-color);
}

.cta-content h2 {
    font-size: 1.8rem;
    margin-bottom: 1rem;
    color: var(--text-primary);
}

.cta-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
    margin-top: 1.5rem;
}
</style>

<script>
function changeSorting() {
    const sortOrder = document.getElementById('sortOrder').value;
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.set('sort', sortOrder);
    currentUrl.searchParams.delete('page');
    window.location.href = currentUrl.toString();
}

function filterByQuality() {
    const quality = document.getElementById('qualityFilter').value;
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.set('quality', quality);
    currentUrl.searchParams.delete('page');
    window.location.href = currentUrl.toString();
}

function playVideo(videoId) {
    window.location.href = 'video.php?id=' + videoId;
}

function showMembershipRequired(level) {
    alert('Bu videoyu izlemek için ' + level.toUpperCase() + ' üyelik gereklidir.');
    window.location.href = 'uyelik-yukselt.php';
}
</script>

<?php include 'includes/footer.php'; ?>