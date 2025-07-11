<?php
/**
 * DOBİEN Video Platform - Arama Sayfası
 * Geliştirici: DOBİEN
 * Tüm Hakları Saklıdır © DOBİEN
 */

require_once 'includes/config.php';

$page_title = "Arama";
$page_description = "Videolar arasında arama yapın ve istediğiniz içeriği kolayca bulun";
$page_keywords = "arama, video arama, DOBİEN";

// Arama parametreleri
$search_query = trim($_GET['q'] ?? '');
$category_filter = $_GET['kategori'] ?? '';
$quality_filter = $_GET['kalite'] ?? '';
$sort_order = $_GET['siralama'] ?? 'yeni';

// Sayfalama
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$videos_per_page = 12;
$offset = ($page - 1) * $videos_per_page;

$videos = [];
$total_videos = 0;

if ($search_query) {
    $page_title = "\"" . $search_query . "\" için arama sonuçları";
    
    // WHERE koşulları
    $where_conditions = ["v.durum = 'aktif'"];
    $params = [];
    
    // Arama terimi
    $where_conditions[] = "(v.baslik LIKE ? OR v.aciklama LIKE ? OR v.etiketler LIKE ?)";
    $search_param = '%' . $search_query . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    
    // Kategori filtresi
    if ($category_filter) {
        $where_conditions[] = "k.slug = ?";
        $params[] = $category_filter;
    }
    
    // Kalite filtresi
    if ($quality_filter) {
        $where_conditions[] = "v.goruntulenme_yetkisi = ?";
        $params[] = $quality_filter;
    }
    
    // Sıralama
    $order_by = match($sort_order) {
        'yeni' => 'v.ekleme_tarihi DESC',
        'eski' => 'v.ekleme_tarihi ASC',
        'izlenme' => 'v.izlenme_sayisi DESC',
        'begeni' => 'v.begeni_sayisi DESC',
        'baslik' => 'v.baslik ASC',
        default => 'v.ekleme_tarihi DESC'
    };
    
    // Ana sorgu
    $sql = "
        SELECT v.*, k.kategori_adi, k.slug as kategori_slug
        FROM videolar v 
        LEFT JOIN kategoriler k ON v.kategori_id = k.id 
        WHERE " . implode(' AND ', $where_conditions) . "
        ORDER BY $order_by 
        LIMIT $videos_per_page OFFSET $offset
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $videos = $stmt->fetchAll();
    
    // Toplam sonuç sayısı
    $count_sql = "
        SELECT COUNT(*) 
        FROM videolar v 
        LEFT JOIN kategoriler k ON v.kategori_id = k.id 
        WHERE " . implode(' AND ', $where_conditions);
    
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($params);
    $total_videos = $count_stmt->fetchColumn();
}

$total_pages = ceil($total_videos / $videos_per_page);

// Kategorileri çek
$categories = $pdo->query("SELECT * FROM kategoriler WHERE durum = 'aktif' ORDER BY kategori_adi ASC")->fetchAll();

include 'includes/header.php';
?>

<div class="container">
    <!-- Arama Başlığı -->
    <div class="search-header">
        <h1 class="page-title">
            <i class="fas fa-search"></i>
            <?php if ($search_query): ?>
                "<?php echo safeOutput($search_query); ?>" için arama sonuçları
                <?php if ($total_videos > 0): ?>
                <span class="result-count">(<?php echo number_format($total_videos); ?> sonuç)</span>
                <?php endif; ?>
            <?php else: ?>
                Video Arama
            <?php endif; ?>
        </h1>
    </div>
    
    <!-- Arama Formu -->
    <div class="search-form-container">
        <form class="search-form" method="GET" action="">
            <div class="search-input-group">
                <div class="search-field">
                    <i class="fas fa-search"></i>
                    <input type="text" name="q" value="<?php echo safeOutput($search_query); ?>" 
                           placeholder="Video ara..." required>
                </div>
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i>
                    Ara
                </button>
            </div>
            
            <!-- Gelişmiş Filtreler -->
            <div class="advanced-filters">
                <div class="filter-group">
                    <label for="kategori">Kategori:</label>
                    <select name="kategori" id="kategori">
                        <option value="">Tüm Kategoriler</option>
                        <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['slug']; ?>" 
                                <?php echo $category_filter === $category['slug'] ? 'selected' : ''; ?>>
                            <?php echo safeOutput($category['kategori_adi']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="kalite">Video Kalitesi:</label>
                    <select name="kalite" id="kalite">
                        <option value="">Tüm Kaliteler</option>
                        <option value="herkes" <?php echo $quality_filter === 'herkes' ? 'selected' : ''; ?>>720p</option>
                        <option value="vip" <?php echo $quality_filter === 'vip' ? 'selected' : ''; ?>>1080p (VIP)</option>
                        <option value="premium" <?php echo $quality_filter === 'premium' ? 'selected' : ''; ?>>4K (Premium)</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="siralama">Sıralama:</label>
                    <select name="siralama" id="siralama">
                        <option value="yeni" <?php echo $sort_order === 'yeni' ? 'selected' : ''; ?>>En Yeni</option>
                        <option value="eski" <?php echo $sort_order === 'eski' ? 'selected' : ''; ?>>En Eski</option>
                        <option value="izlenme" <?php echo $sort_order === 'izlenme' ? 'selected' : ''; ?>>En Çok İzlenen</option>
                        <option value="begeni" <?php echo $sort_order === 'begeni' ? 'selected' : ''; ?>>En Çok Beğenilen</option>
                        <option value="baslik" <?php echo $sort_order === 'baslik' ? 'selected' : ''; ?>>Alfabetik</option>
                    </select>
                </div>
                
                <button type="button" class="filter-clear-btn" onclick="clearFilters()">
                    <i class="fas fa-times"></i>
                    Temizle
                </button>
            </div>
        </form>
    </div>
    
    <?php if ($search_query): ?>
        <?php if (!empty($videos)): ?>
        <!-- Arama Sonuçları -->
        <div class="search-results">
            <div class="results-info">
                <p>
                    <strong><?php echo number_format($total_videos); ?></strong> video bulundu
                    <?php if ($category_filter): ?>
                        <span class="filter-tag">Kategori: <?php echo safeOutput($category_filter); ?></span>
                    <?php endif; ?>
                    <?php if ($quality_filter): ?>
                        <span class="filter-tag">Kalite: <?php echo $quality_filter === 'herkes' ? '720p' : ($quality_filter === 'vip' ? '1080p' : '4K'); ?></span>
                    <?php endif; ?>
                </p>
            </div>
            
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
                    </div>
                    
                    <div class="video-info">
                        <h3 class="video-title"><?php echo highlightSearchTerm(safeOutput($video['baslik']), $search_query); ?></h3>
                        
                        <?php if ($video['aciklama']): ?>
                        <p class="video-description"><?php echo highlightSearchTerm(safeOutput(substr($video['aciklama'], 0, 120)), $search_query); ?><?php echo strlen($video['aciklama']) > 120 ? '...' : ''; ?></p>
                        <?php endif; ?>
                        
                        <div class="video-meta">
                            <div class="video-stats">
                                <span><i class="fas fa-eye"></i> <?php echo number_format($video['izlenme_sayisi']); ?></span>
                                <span><i class="fas fa-thumbs-up"></i> <?php echo number_format($video['begeni_sayisi']); ?></span>
                                <span><i class="fas fa-calendar"></i> <?php echo formatDate($video['ekleme_tarihi'], 'd.m.Y'); ?></span>
                            </div>
                            
                            <?php if ($video['kategori_adi']): ?>
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
                <a href="<?php echo buildSearchUrl(['page' => $page - 1]); ?>" class="pagination-btn">
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
                    <a href="<?php echo buildSearchUrl(['page' => $i]); ?>" 
                       class="pagination-number <?php echo $i == $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                    <?php endfor; ?>
                </div>
                
                <?php if ($page < $total_pages): ?>
                <a href="<?php echo buildSearchUrl(['page' => $page + 1]); ?>" class="pagination-btn">
                    Sonraki
                    <i class="fas fa-chevron-right"></i>
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <?php else: ?>
        <!-- Sonuç Bulunamadı -->
        <div class="no-results">
            <div class="no-results-icon">
                <i class="fas fa-search-minus"></i>
            </div>
            <h3>Sonuç Bulunamadı</h3>
            <p>"<?php echo safeOutput($search_query); ?>" için herhangi bir video bulunamadı.</p>
            
            <div class="search-suggestions">
                <h4>Arama önerilerimiz:</h4>
                <ul>
                    <li>Anahtar kelimeleri kontrol edin</li>
                    <li>Daha genel terimler kullanın</li>
                    <li>Farklı kategorilerden aramayı deneyin</li>
                    <li>Filtreleri temizleyerek tekrar deneyin</li>
                </ul>
            </div>
            
            <a href="<?php echo siteUrl(); ?>" class="btn btn-primary">
                <i class="fas fa-home"></i>
                Ana Sayfaya Dön
            </a>
        </div>
        <?php endif; ?>
        
    <?php else: ?>
    <!-- İlk Arama Durumu -->
    <div class="search-welcome">
        <div class="search-icon">
            <i class="fas fa-search"></i>
        </div>
        <h3>Ne aramak istiyorsunuz?</h3>
        <p>Binlerce video arasından istediğinizi kolayca bulun. Gelişmiş filtreleme seçenekleri ile aradığınız içeriğe hızlıca ulaşın.</p>
        
        <!-- Popüler Aramalar -->
        <div class="popular-searches">
            <h4>Popüler Aramalar:</h4>
            <div class="search-tags">
                <?php
                $popular_tags = ['aksiyon', 'komedi', 'drama', 'korku', 'romantik', 'bilim kurgu'];
                foreach ($popular_tags as $tag):
                ?>
                <a href="<?php echo siteUrl('arama.php?q=' . urlencode($tag)); ?>" class="search-tag">
                    <?php echo ucfirst($tag); ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Son Eklenen Kategoriler -->
        <?php if (!empty($categories)): ?>
        <div class="category-suggestions">
            <h4>Kategoriler:</h4>
            <div class="category-links">
                <?php foreach (array_slice($categories, 0, 6) as $category): ?>
                <a href="<?php echo siteUrl('kategoriler.php?kategori=' . $category['slug']); ?>" class="category-link">
                    <i class="fas fa-folder"></i>
                    <?php echo safeOutput($category['kategori_adi']); ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<style>
/* Arama Sayfası Stilleri */
.search-header {
    text-align: center;
    margin-bottom: 30px;
    padding: 20px 0;
}

.page-title {
    color: #fff;
    font-size: 2rem;
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    flex-wrap: wrap;
}

.page-title i {
    color: var(--primary-color);
}

.result-count {
    color: rgba(255, 255, 255, 0.6);
    font-size: 1rem;
    font-weight: normal;
}

.search-form-container {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 15px;
    padding: 30px;
    margin-bottom: 30px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.search-input-group {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.search-field {
    position: relative;
    flex: 1;
}

.search-field i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(255, 255, 255, 0.5);
}

.search-field input {
    width: 100%;
    padding: 12px 15px 12px 45px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    color: #fff;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.search-field input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.1);
}

.search-field input::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

.search-btn {
    padding: 12px 25px;
    background: var(--primary-color);
    color: #000;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.search-btn:hover {
    background: #e67e22;
    transform: translateY(-2px);
}

.advanced-filters {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    align-items: end;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.filter-group label {
    color: #fff;
    font-size: 0.9rem;
    font-weight: 500;
}

.filter-group select {
    padding: 8px 10px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 6px;
    color: #fff;
    font-size: 0.9rem;
}

.filter-group select option {
    background: #1a1a2e;
    color: #fff;
}

.filter-clear-btn {
    padding: 8px 15px;
    background: transparent;
    color: rgba(255, 255, 255, 0.7);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 0.9rem;
}

.filter-clear-btn:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
}

.results-info {
    margin-bottom: 20px;
    padding: 15px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.results-info p {
    color: rgba(255, 255, 255, 0.8);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.filter-tag {
    background: var(--primary-color);
    color: #000;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 500;
}

.video-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
}

.search-highlight {
    background: var(--primary-color);
    color: #000;
    padding: 0 2px;
    border-radius: 2px;
}

.no-results {
    text-align: center;
    padding: 60px 20px;
}

.no-results-icon {
    font-size: 4rem;
    color: rgba(255, 255, 255, 0.3);
    margin-bottom: 20px;
}

.no-results h3 {
    color: #fff;
    margin: 0 0 15px;
    font-size: 1.5rem;
}

.no-results p {
    color: rgba(255, 255, 255, 0.7);
    margin: 0 0 30px;
    font-size: 1.1rem;
}

.search-suggestions {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    padding: 20px;
    margin: 30px 0;
    text-align: left;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

.search-suggestions h4 {
    color: #fff;
    margin: 0 0 15px;
}

.search-suggestions ul {
    color: rgba(255, 255, 255, 0.8);
    margin: 0;
    padding-left: 20px;
}

.search-suggestions li {
    margin-bottom: 5px;
}

.search-welcome {
    text-align: center;
    padding: 60px 20px;
}

.search-icon {
    font-size: 4rem;
    color: var(--primary-color);
    margin-bottom: 20px;
}

.search-welcome h3 {
    color: #fff;
    margin: 0 0 15px;
    font-size: 1.8rem;
}

.search-welcome p {
    color: rgba(255, 255, 255, 0.8);
    margin: 0 0 40px;
    font-size: 1.1rem;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
    margin-bottom: 40px;
}

.popular-searches,
.category-suggestions {
    margin: 40px 0;
}

.popular-searches h4,
.category-suggestions h4 {
    color: #fff;
    margin: 0 0 15px;
    font-size: 1.1rem;
}

.search-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
}

.search-tag {
    background: rgba(255, 255, 255, 0.1);
    color: var(--primary-color);
    padding: 6px 12px;
    border-radius: 20px;
    text-decoration: none;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.search-tag:hover {
    background: var(--primary-color);
    color: #000;
}

.category-links {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 10px;
    max-width: 600px;
    margin: 0 auto;
}

.category-link {
    background: rgba(255, 255, 255, 0.05);
    color: #fff;
    padding: 10px 15px;
    border-radius: 8px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.category-link:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: var(--primary-color);
}

.category-link i {
    color: var(--primary-color);
}

/* Responsive */
@media (max-width: 768px) {
    .page-title {
        font-size: 1.5rem;
        flex-direction: column;
        gap: 5px;
    }
    
    .search-input-group {
        flex-direction: column;
    }
    
    .advanced-filters {
        grid-template-columns: 1fr;
    }
    
    .video-grid {
        grid-template-columns: 1fr;
    }
    
    .results-info p {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    
    .search-tags {
        justify-content: flex-start;
    }
    
    .category-links {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function clearFilters() {
    const searchQuery = document.querySelector('input[name="q"]').value;
    window.location.href = 'arama.php' + (searchQuery ? '?q=' + encodeURIComponent(searchQuery) : '');
}

// Form değişikliklerinde otomatik arama
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.search-form');
    const selects = form.querySelectorAll('select');
    
    selects.forEach(select => {
        select.addEventListener('change', function() {
            if (document.querySelector('input[name="q"]').value) {
                form.submit();
            }
        });
    });
});
</script>

<?php
// Yardımcı fonksiyonlar
function highlightSearchTerm($text, $term) {
    if (!$term) return $text;
    return preg_replace('/(' . preg_quote($term, '/') . ')/i', '<span class="search-highlight">$1</span>', $text);
}

function buildSearchUrl($params = []) {
    global $search_query, $category_filter, $quality_filter, $sort_order;
    
    $url_params = [
        'q' => $search_query,
        'kategori' => $category_filter,
        'kalite' => $quality_filter,
        'siralama' => $sort_order
    ];
    
    // Yeni parametreleri ekle/güncelle
    $url_params = array_merge($url_params, $params);
    
    // Boş parametreleri temizle
    $url_params = array_filter($url_params, function($value) {
        return $value !== '' && $value !== null;
    });
    
    return siteUrl('arama.php?' . http_build_query($url_params));
}
?>

<?php include 'includes/footer.php'; ?>