<?php
/**
 * DOBİEN Video Platform - Kategoriler Sayfası
 * Geliştirici: DOBİEN
 * Tüm Hakları Saklıdır © DOBİEN
 */

require_once 'includes/config.php';

$page_title = "Kategoriler";
$page_description = "Video kategorilerine göz atın ve istediğiniz içeriği kolayca bulun";
$page_keywords = "kategoriler, video kategorileri, DOBİEN";

// Kategori slug kontrolü
$category_slug = $_GET['kategori'] ?? '';
$selected_category = null;

if ($category_slug) {
    $category_stmt = $pdo->prepare("SELECT * FROM kategoriler WHERE slug = ? AND durum = 'aktif'");
    $category_stmt->execute([$category_slug]);
    $selected_category = $category_stmt->fetch();
    
    if ($selected_category) {
        $page_title = $selected_category['kategori_adi'] . " Videoları";
        $page_description = $selected_category['aciklama'] ?: $selected_category['kategori_adi'] . " kategorisindeki videolar";
    }
}

// Sayfalama
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$videos_per_page = 12;
$offset = ($page - 1) * $videos_per_page;

// Kategorileri çek
$categories_query = "SELECT * FROM kategoriler WHERE durum = 'aktif' ORDER BY siralama ASC, kategori_adi ASC";
$categories = $pdo->query($categories_query)->fetchAll();

// Videoları çek
if ($selected_category) {
    // Belirli kategori videoları
    $videos_query = "
        SELECT v.*, k.kategori_adi, k.slug as kategori_slug
        FROM videolar v 
        LEFT JOIN kategoriler k ON v.kategori_id = k.id 
        WHERE v.kategori_id = ? AND v.durum = 'aktif'
        ORDER BY v.ekleme_tarihi DESC 
        LIMIT $videos_per_page OFFSET $offset
    ";
    $videos_stmt = $pdo->prepare($videos_query);
    $videos_stmt->execute([$selected_category['id']]);
    $videos = $videos_stmt->fetchAll();
    
    // Toplam video sayısı
    $count_query = "SELECT COUNT(*) FROM videolar WHERE kategori_id = ? AND durum = 'aktif'";
    $count_stmt = $pdo->prepare($count_query);
    $count_stmt->execute([$selected_category['id']]);
    $total_videos = $count_stmt->fetchColumn();
} else {
    // Tüm videolar
    $videos_query = "
        SELECT v.*, k.kategori_adi, k.slug as kategori_slug
        FROM videolar v 
        LEFT JOIN kategoriler k ON v.kategori_id = k.id 
        WHERE v.durum = 'aktif'
        ORDER BY v.ekleme_tarihi DESC 
        LIMIT $videos_per_page OFFSET $offset
    ";
    $videos = $pdo->query($videos_query)->fetchAll();
    
    // Toplam video sayısı
    $total_videos = $pdo->query("SELECT COUNT(*) FROM videolar WHERE durum = 'aktif'")->fetchColumn();
}

$total_pages = ceil($total_videos / $videos_per_page);

include 'includes/header.php';
?>

<div class="container">
    <!-- Sayfa Başlığı -->
    <div class="page-header">
        <div class="page-title-section">
            <h1 class="page-title">
                <?php if ($selected_category): ?>
                    <i class="fas fa-tag"></i>
                    <?php echo safeOutput($selected_category['kategori_adi']); ?>
                    <span class="video-count">(<?php echo number_format($total_videos); ?> video)</span>
                <?php else: ?>
                    <i class="fas fa-th-large"></i>
                    Tüm Kategoriler
                <?php endif; ?>
            </h1>
            
            <?php if ($selected_category && $selected_category['aciklama']): ?>
            <p class="page-description"><?php echo safeOutput($selected_category['aciklama']); ?></p>
            <?php endif; ?>
        </div>
        
        <div class="page-actions">
            <?php if ($selected_category): ?>
            <a href="<?php echo siteUrl('kategoriler.php'); ?>" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                Tüm Kategoriler
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Kategori Filtreleri -->
    <?php if (!$selected_category && !empty($categories)): ?>
    <section class="category-filters">
        <h2 class="section-title">
            <i class="fas fa-filter"></i>
            Kategoriler
        </h2>
        
        <div class="categories-grid">
            <?php foreach ($categories as $category): ?>
            <a href="<?php echo siteUrl('kategoriler.php?kategori=' . $category['slug']); ?>" class="category-card">
                <div class="category-image">
                    <?php if ($category['resim']): ?>
                    <img src="<?php echo siteUrl('uploads/categories/' . $category['resim']); ?>" alt="<?php echo safeOutput($category['kategori_adi']); ?>">
                    <?php else: ?>
                    <div class="category-placeholder">
                        <i class="fas fa-video"></i>
                    </div>
                    <?php endif; ?>
                    <div class="category-overlay">
                        <i class="fas fa-play-circle"></i>
                    </div>
                </div>
                <div class="category-info">
                    <h3><?php echo safeOutput($category['kategori_adi']); ?></h3>
                    <?php if ($category['aciklama']): ?>
                    <p><?php echo safeOutput($category['aciklama']); ?></p>
                    <?php endif; ?>
                    
                    <?php
                    // Kategori video sayısını çek
                    $cat_count_stmt = $pdo->prepare("SELECT COUNT(*) FROM videolar WHERE kategori_id = ? AND durum = 'aktif'");
                    $cat_count_stmt->execute([$category['id']]);
                    $cat_video_count = $cat_count_stmt->fetchColumn();
                    ?>
                    <span class="video-count"><?php echo number_format($cat_video_count); ?> video</span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
    
    <!-- Videolar -->
    <?php if (!empty($videos)): ?>
    <section class="videos-section">
        <?php if ($selected_category): ?>
        <h2 class="section-title">
            <i class="fas fa-video"></i>
            <?php echo safeOutput($selected_category['kategori_adi']); ?> Videoları
        </h2>
        <?php endif; ?>
        
        <div class="video-grid">
            <?php foreach ($videos as $video): ?>
            <?php
            // Erişim kontrolü
            $can_access = true;
            if ($video['goruntulenme_yetkisi'] != 'herkes') {
                if (!$current_user) {
                    $can_access = false;
                } else {
                    $user_level = ['kullanici' => 1, 'vip' => 2, 'premium' => 3];
                    $required_level = ['herkes' => 1, 'vip' => 2, 'premium' => 3];
                    if ($user_level[$current_user['uyelik_tipi']] < $required_level[$video['goruntulenme_yetkisi']]) {
                        $can_access = false;
                    }
                }
            }
            
            // Video kalitesi
            $quality = 'herkes' == $video['goruntulenme_yetkisi'] ? '720p' : ($video['goruntulenme_yetkisi'] == 'vip' ? '1080p' : '4K');
            ?>
            
            <div class="video-card <?php echo !$can_access ? 'locked' : ''; ?>" 
                 onclick="<?php echo $can_access ? "window.location.href='" . siteUrl('video.php?id=' . $video['id']) . "'" : "showMembershipRequired('" . $video['goruntulenme_yetkisi'] . "')"; ?>">
                
                <div class="video-thumbnail">
                    <img src="<?php echo $video['kapak_resmi'] ? siteUrl('uploads/thumbnails/' . $video['kapak_resmi']) : siteUrl('assets/images/default-thumbnail.jpg'); ?>" 
                         alt="<?php echo safeOutput($video['baslik']); ?>">
                    
                    <div class="video-duration"><?php echo $video['sure'] ? formatDuration($video['sure']) : '00:00'; ?></div>
                    
                    <div class="video-quality-badge quality-<?php echo strtolower($quality); ?>">
                        <?php echo $quality; ?>
                    </div>
                    
                    <?php if (!$can_access): ?>
                    <div class="membership-lock">
                        <i class="fas fa-lock"></i>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($video['ozellik'] == 'populer'): ?>
                    <div class="video-badge popular">
                        <i class="fas fa-fire"></i>
                        Popüler
                    </div>
                    <?php elseif ($video['ozellik'] == 'editor_secimi'): ?>
                    <div class="video-badge editor">
                        <i class="fas fa-star"></i>
                        Editör Seçimi
                    </div>
                    <?php elseif ($video['ozellik'] == 'yeni'): ?>
                    <div class="video-badge new">
                        <i class="fas fa-plus"></i>
                        Yeni
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="video-info">
                    <h3 class="video-title"><?php echo safeOutput($video['baslik']); ?></h3>
                    
                    <?php if ($video['aciklama']): ?>
                    <p class="video-description"><?php echo safeOutput(substr($video['aciklama'], 0, 100)); ?><?php echo strlen($video['aciklama']) > 100 ? '...' : ''; ?></p>
                    <?php endif; ?>
                    
                    <div class="video-meta">
                        <div class="video-stats">
                            <span><i class="fas fa-eye"></i> <?php echo number_format($video['izlenme_sayisi']); ?></span>
                            <span><i class="fas fa-thumbs-up"></i> <?php echo number_format($video['begeni_sayisi']); ?></span>
                            <span><i class="fas fa-calendar"></i> <?php echo formatDate($video['ekleme_tarihi'], 'd.m.Y'); ?></span>
                        </div>
                        
                        <?php if ($video['kategori_adi'] && !$selected_category): ?>
                        <a href="<?php echo siteUrl('kategoriler.php?kategori=' . $video['kategori_slug']); ?>" class="video-category">
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
        <div class="pagination">
            <?php if ($page > 1): ?>
            <a href="<?php echo siteUrl('kategoriler.php' . ($selected_category ? '?kategori=' . $selected_category['slug'] . '&' : '?') . 'page=' . ($page - 1)); ?>" class="pagination-btn">
                <i class="fas fa-chevron-left"></i>
                Önceki
            </a>
            <?php endif; ?>
            
            <div class="pagination-numbers">
                <?php
                $start = max(1, $page - 2);
                $end = min($total_pages, $page + 2);
                
                for ($i = $start; $i <= $end; $i++):
                ?>
                <a href="<?php echo siteUrl('kategoriler.php' . ($selected_category ? '?kategori=' . $selected_category['slug'] . '&' : '?') . 'page=' . $i); ?>" 
                   class="pagination-number <?php echo $i == $page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
                <?php endfor; ?>
            </div>
            
            <?php if ($page < $total_pages): ?>
            <a href="<?php echo siteUrl('kategoriler.php' . ($selected_category ? '?kategori=' . $selected_category['slug'] . '&' : '?') . 'page=' . ($page + 1)); ?>" class="pagination-btn">
                Sonraki
                <i class="fas fa-chevron-right"></i>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </section>
    
    <?php else: ?>
    <!-- Boş Durum -->
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-video-slash"></i>
        </div>
        <h3>Video Bulunamadı</h3>
        <p>
            <?php if ($selected_category): ?>
            Bu kategoride henüz video bulunmuyor.
            <?php else: ?>
            Henüz hiç video eklenmemiş.
            <?php endif; ?>
        </p>
        <a href="<?php echo siteUrl(); ?>" class="btn btn-primary">
            <i class="fas fa-home"></i>
            Ana Sayfaya Dön
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- Üyelik Gerekli Modalı -->
<div class="membership-modal" id="membershipModal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <div class="lock-icon">
                <i class="fas fa-lock"></i>
            </div>
            <h3 id="modalTitle">Premium İçerik</h3>
            <p id="modalMessage">Bu videoya erişebilmek için üyelik yükseltmeniz gereklidir.</p>
        </div>
        
        <div class="modal-actions">
            <a href="<?php echo siteUrl('uyelik-yukselt.php'); ?>" class="btn btn-primary">
                <i class="fas fa-crown"></i>
                Üyeliği Yükselt
            </a>
            <button onclick="closeMembershipModal()" class="btn btn-outline">
                İptal
            </button>
        </div>
    </div>
</div>

<style>
/* Kategoriler Sayfası Özel Stilleri */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding: 20px 0;
    border-bottom: 2px solid rgba(255, 255, 255, 0.1);
}

.page-title {
    color: #fff;
    font-size: 2rem;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.page-title i {
    color: var(--primary-color);
}

.video-count {
    color: rgba(255, 255, 255, 0.6);
    font-size: 1rem;
    font-weight: normal;
}

.page-description {
    color: rgba(255, 255, 255, 0.8);
    margin: 10px 0 0;
    font-size: 1.1rem;
}

.category-filters {
    margin-bottom: 40px;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.category-card {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 15px;
    overflow: hidden;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.category-card:hover {
    transform: translateY(-5px);
    background: rgba(255, 255, 255, 0.1);
    border-color: var(--primary-color);
}

.category-image {
    position: relative;
    height: 150px;
    overflow: hidden;
}

.category-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.category-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, var(--primary-color), #e67e22);
    display: flex;
    align-items: center;
    justify-content: center;
}

.category-placeholder i {
    font-size: 3rem;
    color: rgba(0, 0, 0, 0.7);
}

.category-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.category-card:hover .category-overlay {
    opacity: 1;
}

.category-overlay i {
    color: var(--primary-color);
    font-size: 3rem;
}

.category-info {
    padding: 20px;
}

.category-info h3 {
    color: #fff;
    margin: 0 0 10px;
    font-size: 1.2rem;
}

.category-info p {
    color: rgba(255, 255, 255, 0.7);
    margin: 0 0 10px;
    font-size: 0.9rem;
    line-height: 1.4;
}

.category-info .video-count {
    color: var(--primary-color);
    font-weight: 600;
    font-size: 0.9rem;
}

.video-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin-top: 20px;
}

.video-card {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
    cursor: pointer;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.video-card:hover {
    transform: translateY(-5px);
    background: rgba(255, 255, 255, 0.1);
    border-color: var(--primary-color);
}

.video-card.locked {
    opacity: 0.7;
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
}

.video-duration {
    position: absolute;
    bottom: 8px;
    right: 8px;
    background: rgba(0, 0, 0, 0.8);
    color: #fff;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 600;
}

.video-quality-badge {
    position: absolute;
    top: 8px;
    left: 8px;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
}

.quality-720p {
    background: #28a745;
    color: #fff;
}

.quality-1080p {
    background: #ffc107;
    color: #000;
}

.quality-4k {
    background: #dc3545;
    color: #fff;
}

.membership-lock {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(0, 0, 0, 0.8);
    color: var(--primary-color);
    padding: 10px;
    border-radius: 50%;
    font-size: 1.5rem;
}

.video-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
}

.video-badge.popular {
    background: #ff6b35;
    color: #fff;
}

.video-badge.editor {
    background: #6f42c1;
    color: #fff;
}

.video-badge.new {
    background: #20c997;
    color: #fff;
}

.video-info {
    padding: 20px;
}

.video-title {
    color: #fff;
    margin: 0 0 10px;
    font-size: 1.1rem;
    font-weight: 600;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.video-description {
    color: rgba(255, 255, 255, 0.7);
    margin: 0 0 15px;
    font-size: 0.9rem;
    line-height: 1.4;
}

.video-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
}

.video-stats {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.video-stats span {
    display: flex;
    align-items: center;
    gap: 4px;
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.8rem;
}

.video-category {
    background: rgba(255, 255, 255, 0.1);
    color: var(--primary-color);
    padding: 4px 8px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.8rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.video-category:hover {
    background: var(--primary-color);
    color: #000;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    margin-top: 40px;
    padding: 20px 0;
}

.pagination-btn,
.pagination-number {
    padding: 8px 16px;
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
    text-decoration: none;
    border-radius: 6px;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    gap: 5px;
}

.pagination-btn:hover,
.pagination-number:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: var(--primary-color);
}

.pagination-number.active {
    background: var(--primary-color);
    color: #000;
    border-color: var(--primary-color);
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-icon {
    font-size: 4rem;
    color: rgba(255, 255, 255, 0.3);
    margin-bottom: 20px;
}

.empty-state h3 {
    color: #fff;
    margin: 0 0 10px;
    font-size: 1.5rem;
}

.empty-state p {
    color: rgba(255, 255, 255, 0.7);
    margin: 0 0 30px;
    font-size: 1.1rem;
}

.membership-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    padding: 20px;
}

.membership-modal .modal-content {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 40px;
    max-width: 500px;
    width: 100%;
    text-align: center;
}

.lock-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--primary-color), #e67e22);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.lock-icon i {
    font-size: 2rem;
    color: #000;
}

.modal-header h3 {
    color: #fff;
    margin: 0 0 10px;
    font-size: 1.5rem;
}

.modal-header p {
    color: rgba(255, 255, 255, 0.8);
    margin: 0 0 30px;
}

.modal-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
}

/* Responsive */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }
    
    .categories-grid {
        grid-template-columns: 1fr;
    }
    
    .video-grid {
        grid-template-columns: 1fr;
    }
    
    .video-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .pagination {
        flex-wrap: wrap;
    }
}
</style>

<script>
function showMembershipRequired(type) {
    const modal = document.getElementById('membershipModal');
    const title = document.getElementById('modalTitle');
    const message = document.getElementById('modalMessage');
    
    if (type === 'vip') {
        title.textContent = 'VIP İçerik';
        message.textContent = 'Bu videoya erişebilmek için VIP üyelik gereklidir.';
    } else if (type === 'premium') {
        title.textContent = 'Premium İçerik';
        message.textContent = 'Bu videoya erişebilmek için Premium üyelik gereklidir.';
    }
    
    modal.style.display = 'flex';
}

function closeMembershipModal() {
    document.getElementById('membershipModal').style.display = 'none';
}

// Modal dışına tıklayınca kapat
document.getElementById('membershipModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeMembershipModal();
    }
});
</script>

<?php include 'includes/footer.php'; ?>