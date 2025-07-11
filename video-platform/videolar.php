<?php
/**
 * DOBİEN Video Platform - Tüm Videolar Sayfası
 * Geliştirici: DOBİEN
 * Modern Video Paylaşım Platformu
 * Tüm Hakları Saklıdır © DOBİEN
 * 
 * GELİŞMİŞ ÖZELLİKLER:
 * - Gelişmiş filtreleme sistemi
 * - Canlı arama
 * - Çoklu sıralama seçenekleri
 * - Sayfalama (AJAX ile)
 * - Grid/Liste görünümü
 * - Favori ekleme
 * - Üyelik seviyesi kontrolü
 */

require_once 'includes/config.php';

// Sayfa parametreleri
$page = isset($_GET['sayfa']) ? (int)$_GET['sayfa'] : 1;
$per_page = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
$view_mode = isset($_GET['gorunum']) ? $_GET['gorunum'] : 'grid'; // grid veya list

// Filtreleme parametreleri
$search = isset($_GET['arama']) ? trim($_GET['arama']) : '';
$category = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 0;
$quality = isset($_GET['kalite']) ? $_GET['kalite'] : '';
$membership = isset($_GET['uyelik']) ? $_GET['uyelik'] : '';
$sort = isset($_GET['sirala']) ? $_GET['sirala'] : 'yeni';
$duration = isset($_GET['sure']) ? $_GET['sure'] : '';

// Sayfa bilgileri
$page_title = "Tüm Videolar";
$page_description = "DOBİEN Video Platform - En kaliteli videoları keşfedin";

// Kategorileri çek
$categories_query = "SELECT * FROM kategoriler WHERE durum = 'aktif' ORDER BY kategori_adi ASC";
$categories = $pdo->query($categories_query)->fetchAll();

// Toplam video sayısını hesapla (filtrelere göre)
$count_query = "SELECT COUNT(*) FROM videolar v 
                LEFT JOIN kategoriler k ON v.kategori_id = k.id 
                WHERE v.durum = 'aktif'";

$count_params = [];

// Arama filtresi
if (!empty($search)) {
    $count_query .= " AND (v.baslik LIKE ? OR v.aciklama LIKE ? OR v.etiketler LIKE ?)";
    $count_params[] = "%$search%";
    $count_params[] = "%$search%";
    $count_params[] = "%$search%";
}

// Kategori filtresi
if ($category > 0) {
    $count_query .= " AND v.kategori_id = ?";
    $count_params[] = $category;
}

// Kalite filtresi
if (!empty($quality)) {
    $count_query .= " AND v.kalite LIKE ?";
    $count_params[] = "%$quality%";
}

// Üyelik filtresi
if (!empty($membership)) {
    $count_query .= " AND v.goruntulenme_yetkisi = ?";
    $count_params[] = $membership;
}

// Süre filtresi
if (!empty($duration)) {
    switch ($duration) {
        case 'kisa': // 0-5 dakika
            $count_query .= " AND v.sure <= 300";
            break;
        case 'orta': // 5-20 dakika
            $count_query .= " AND v.sure > 300 AND v.sure <= 1200";
            break;
        case 'uzun': // 20+ dakika
            $count_query .= " AND v.sure > 1200";
            break;
    }
}

$count_stmt = $pdo->prepare($count_query);
$count_stmt->execute($count_params);
$total_videos = $count_stmt->fetchColumn();

// Sayfalama hesapları
$total_pages = ceil($total_videos / $per_page);
$offset = ($page - 1) * $per_page;

// Ana sorgu
$query = "SELECT v.*, k.kategori_adi, k.renk,
          (SELECT COUNT(*) FROM begeniler WHERE video_id = v.id AND tur = 'begendi') as begeni_sayisi,
          (SELECT COUNT(*) FROM begeniler WHERE video_id = v.id AND tur = 'begenme') as begenme_sayisi,
          (SELECT COUNT(*) FROM yorumlar WHERE video_id = v.id AND durum = 'onaylandı') as yorum_sayisi,
          (SELECT COUNT(*) FROM favoriler WHERE video_id = v.id) as favori_sayisi
          FROM videolar v 
          LEFT JOIN kategoriler k ON v.kategori_id = k.id 
          WHERE v.durum = 'aktif'";

$params = $count_params; // Aynı parametreleri kullan

// Sıralama
switch ($sort) {
    case 'eski':
        $query .= " ORDER BY v.ekleme_tarihi ASC";
        break;
    case 'populer':
        $query .= " ORDER BY v.izlenme_sayisi DESC";
        break;
    case 'begeni':
        $query .= " ORDER BY begeni_sayisi DESC";
        break;
    case 'sure_kisa':
        $query .= " ORDER BY v.sure ASC";
        break;
    case 'sure_uzun':
        $query .= " ORDER BY v.sure DESC";
        break;
    case 'alfabetik':
        $query .= " ORDER BY v.baslik ASC";
        break;
    case 'yeni':
    default:
        $query .= " ORDER BY v.ekleme_tarihi DESC";
        break;
}

$query .= " LIMIT $per_page OFFSET $offset";

$videos_stmt = $pdo->prepare($query);
$videos_stmt->execute($params);
$videos = $videos_stmt->fetchAll();

include 'includes/header.php';
?>

<!-- Ana İçerik -->
<div class="container">
    
    <!-- Sayfa Başlığı -->
    <section class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i class="fas fa-video"></i>
                Tüm Videolar
            </h1>
            <p class="page-description">
                Toplam <strong><?php echo number_format($total_videos); ?></strong> video bulundu
            </p>
        </div>
        <div class="page-actions">
            <div class="view-toggles">
                <button class="view-toggle <?php echo $view_mode === 'grid' ? 'active' : ''; ?>" 
                        data-view="grid" title="Grid Görünümü">
                    <i class="fas fa-th-large"></i>
                </button>
                <button class="view-toggle <?php echo $view_mode === 'list' ? 'active' : ''; ?>" 
                        data-view="list" title="Liste Görünümü">
                    <i class="fas fa-list"></i>
                </button>
            </div>
        </div>
    </section>

    <!-- Filtreleme ve Arama -->
    <section class="filters-section">
        <div class="filters-container">
            
            <!-- Arama Kutusu -->
            <div class="search-container">
                <div class="search-box">
                    <input type="text" 
                           id="searchInput" 
                           placeholder="Video ara..." 
                           value="<?php echo safeOutput($search); ?>"
                           class="search-input">
                    <button class="search-btn" onclick="performSearch()">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <div class="search-suggestions" id="searchSuggestions"></div>
            </div>

            <!-- Filtreler -->
            <div class="filters-grid">
                
                <!-- Kategori Filtresi -->
                <div class="filter-group">
                    <label for="categoryFilter" class="filter-label">
                        <i class="fas fa-folder"></i>
                        Kategori
                    </label>
                    <select id="categoryFilter" class="filter-select">
                        <option value="">Tüm Kategoriler</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" 
                                <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo safeOutput($cat['kategori_adi']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Kalite Filtresi -->
                <div class="filter-group">
                    <label for="qualityFilter" class="filter-label">
                        <i class="fas fa-video"></i>
                        Kalite
                    </label>
                    <select id="qualityFilter" class="filter-select">
                        <option value="">Tüm Kaliteler</option>
                        <option value="720p" <?php echo $quality === '720p' ? 'selected' : ''; ?>>720p HD</option>
                        <option value="1080p" <?php echo $quality === '1080p' ? 'selected' : ''; ?>>1080p Full HD</option>
                        <option value="4K" <?php echo $quality === '4K' ? 'selected' : ''; ?>>4K Ultra HD</option>
                    </select>
                </div>

                <!-- Üyelik Filtresi -->
                <div class="filter-group">
                    <label for="membershipFilter" class="filter-label">
                        <i class="fas fa-crown"></i>
                        Üyelik Seviyesi
                    </label>
                    <select id="membershipFilter" class="filter-select">
                        <option value="">Tüm Seviyeler</option>
                        <option value="kullanici" <?php echo $membership === 'kullanici' ? 'selected' : ''; ?>>Ücretsiz</option>
                        <option value="vip" <?php echo $membership === 'vip' ? 'selected' : ''; ?>>VIP</option>
                        <option value="premium" <?php echo $membership === 'premium' ? 'selected' : ''; ?>>Premium</option>
                    </select>
                </div>

                <!-- Süre Filtresi -->
                <div class="filter-group">
                    <label for="durationFilter" class="filter-label">
                        <i class="fas fa-clock"></i>
                        Video Süresi
                    </label>
                    <select id="durationFilter" class="filter-select">
                        <option value="">Tüm Süreler</option>
                        <option value="kisa" <?php echo $duration === 'kisa' ? 'selected' : ''; ?>>Kısa (0-5 dk)</option>
                        <option value="orta" <?php echo $duration === 'orta' ? 'selected' : ''; ?>>Orta (5-20 dk)</option>
                        <option value="uzun" <?php echo $duration === 'uzun' ? 'selected' : ''; ?>>Uzun (20+ dk)</option>
                    </select>
                </div>

                <!-- Sıralama -->
                <div class="filter-group">
                    <label for="sortFilter" class="filter-label">
                        <i class="fas fa-sort"></i>
                        Sıralama
                    </label>
                    <select id="sortFilter" class="filter-select">
                        <option value="yeni" <?php echo $sort === 'yeni' ? 'selected' : ''; ?>>En Yeni</option>
                        <option value="eski" <?php echo $sort === 'eski' ? 'selected' : ''; ?>>En Eski</option>
                        <option value="populer" <?php echo $sort === 'populer' ? 'selected' : ''; ?>>En Popüler</option>
                        <option value="begeni" <?php echo $sort === 'begeni' ? 'selected' : ''; ?>>En Beğenilen</option>
                        <option value="sure_kisa" <?php echo $sort === 'sure_kisa' ? 'selected' : ''; ?>>En Kısa</option>
                        <option value="sure_uzun" <?php echo $sort === 'sure_uzun' ? 'selected' : ''; ?>>En Uzun</option>
                        <option value="alfabetik" <?php echo $sort === 'alfabetik' ? 'selected' : ''; ?>>Alfabetik</option>
                    </select>
                </div>

                <!-- Sayfa Başına Video -->
                <div class="filter-group">
                    <label for="limitFilter" class="filter-label">
                        <i class="fas fa-list-ol"></i>
                        Sayfa Başına
                    </label>
                    <select id="limitFilter" class="filter-select">
                        <option value="12" <?php echo $per_page == 12 ? 'selected' : ''; ?>>12 Video</option>
                        <option value="20" <?php echo $per_page == 20 ? 'selected' : ''; ?>>20 Video</option>
                        <option value="36" <?php echo $per_page == 36 ? 'selected' : ''; ?>>36 Video</option>
                        <option value="48" <?php echo $per_page == 48 ? 'selected' : ''; ?>>48 Video</option>
                    </select>
                </div>

            </div>

            <!-- Filtre Sıfırla -->
            <div class="filter-actions">
                <button class="btn btn-outline" onclick="clearFilters()">
                    <i class="fas fa-eraser"></i>
                    Filtreleri Temizle
                </button>
                <button class="btn btn-primary" onclick="applyFilters()">
                    <i class="fas fa-filter"></i>
                    Filtreleri Uygula
                </button>
            </div>

        </div>
    </section>

    <!-- Aktif Filtreler -->
    <?php if (!empty($search) || $category > 0 || !empty($quality) || !empty($membership) || !empty($duration) || $sort !== 'yeni'): ?>
    <section class="active-filters">
        <div class="active-filters-container">
            <span class="active-filters-label">Aktif Filtreler:</span>
            <div class="active-filters-list">
                
                <?php if (!empty($search)): ?>
                <div class="filter-tag">
                    <i class="fas fa-search"></i>
                    <span>Arama: "<?php echo safeOutput($search); ?>"</span>
                    <button onclick="removeFilter('arama')" class="filter-remove">×</button>
                </div>
                <?php endif; ?>

                <?php if ($category > 0): 
                    $cat_name = '';
                    foreach ($categories as $cat) {
                        if ($cat['id'] == $category) {
                            $cat_name = $cat['kategori_adi'];
                            break;
                        }
                    }
                ?>
                <div class="filter-tag">
                    <i class="fas fa-folder"></i>
                    <span>Kategori: <?php echo safeOutput($cat_name); ?></span>
                    <button onclick="removeFilter('kategori')" class="filter-remove">×</button>
                </div>
                <?php endif; ?>

                <?php if (!empty($quality)): ?>
                <div class="filter-tag">
                    <i class="fas fa-video"></i>
                    <span>Kalite: <?php echo safeOutput($quality); ?></span>
                    <button onclick="removeFilter('kalite')" class="filter-remove">×</button>
                </div>
                <?php endif; ?>

                <?php if (!empty($membership)): ?>
                <div class="filter-tag">
                    <i class="fas fa-crown"></i>
                    <span>Üyelik: <?php echo ucfirst($membership); ?></span>
                    <button onclick="removeFilter('uyelik')" class="filter-remove">×</button>
                </div>
                <?php endif; ?>

                <?php if (!empty($duration)): ?>
                <div class="filter-tag">
                    <i class="fas fa-clock"></i>
                    <span>Süre: <?php 
                        echo $duration === 'kisa' ? 'Kısa (0-5 dk)' : 
                            ($duration === 'orta' ? 'Orta (5-20 dk)' : 'Uzun (20+ dk)'); 
                    ?></span>
                    <button onclick="removeFilter('sure')" class="filter-remove">×</button>
                </div>
                <?php endif; ?>

                <?php if ($sort !== 'yeni'): ?>
                <div class="filter-tag">
                    <i class="fas fa-sort"></i>
                    <span>Sıralama: <?php 
                        $sort_names = [
                            'eski' => 'En Eski',
                            'populer' => 'En Popüler',
                            'begeni' => 'En Beğenilen',
                            'sure_kisa' => 'En Kısa',
                            'sure_uzun' => 'En Uzun',
                            'alfabetik' => 'Alfabetik'
                        ];
                        echo $sort_names[$sort] ?? 'En Yeni';
                    ?></span>
                    <button onclick="removeFilter('sirala')" class="filter-remove">×</button>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Video Listesi -->
    <section class="videos-section">
        <div id="videosContainer" class="videos-container view-<?php echo $view_mode; ?>">
            
            <?php if (empty($videos)): ?>
            <!-- Boş Durum -->
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3>Video bulunamadı</h3>
                <p>Arama kriterlerinize uygun video bulunmuyor. Farklı filtreler deneyebilirsiniz.</p>
                <button class="btn btn-primary" onclick="clearFilters()">
                    <i class="fas fa-eraser"></i>
                    Filtreleri Temizle
                </button>
            </div>
            <?php else: ?>
            
            <!-- Video Kartları -->
            <div class="videos-grid">
                <?php foreach ($videos as $video): ?>
                <div class="video-card" data-video-id="<?php echo $video['id']; ?>">
                    
                    <!-- Video Thumbnail -->
                    <div class="video-thumbnail">
                        <img src="<?php echo $video['kapak_resmi'] ? siteUrl('uploads/thumbnails/' . $video['kapak_resmi']) : siteUrl('assets/images/default-thumbnail.jpg'); ?>" 
                             alt="<?php echo safeOutput($video['baslik']); ?>"
                             loading="lazy">
                        
                        <!-- Hover Overlay -->
                        <div class="video-overlay">
                            <button class="play-button" onclick="playVideo(<?php echo $video['id']; ?>)">
                                <i class="fas fa-play"></i>
                            </button>
                        </div>
                        
                        <!-- Video Süre -->
                        <div class="video-duration">
                            <?php echo formatDuration($video['sure']); ?>
                        </div>
                        
                        <!-- Kalite Badge -->
                        <div class="quality-badge quality-<?php echo strtolower(str_replace(['p', 'K'], '', $video['kalite'])); ?>">
                            <?php echo $video['kalite']; ?>
                        </div>
                        
                        <!-- Üyelik Badge -->
                        <?php if ($video['goruntulenme_yetkisi'] !== 'kullanici'): ?>
                        <div class="membership-badge membership-<?php echo $video['goruntulenme_yetkisi']; ?>">
                            <?php if ($video['goruntulenme_yetkisi'] === 'premium'): ?>
                                <i class="fas fa-gem"></i>
                            <?php elseif ($video['goruntulenme_yetkisi'] === 'vip'): ?>
                                <i class="fas fa-crown"></i>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Favori Butonu -->
                        <button class="favorite-btn <?php echo $current_user ? '' : 'login-required'; ?>" 
                                onclick="toggleFavorite(<?php echo $video['id']; ?>, this)"
                                title="Favorilere Ekle">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                    
                    <!-- Video Info -->
                    <div class="video-info">
                        <h3 class="video-title">
                            <a href="<?php echo siteUrl('video/' . $video['id'] . '/' . createSlug($video['baslik'])); ?>">
                                <?php echo safeOutput($video['baslik']); ?>
                            </a>
                        </h3>
                        
                        <div class="video-meta">
                            <?php if ($video['kategori_adi']): ?>
                            <a href="<?php echo siteUrl('kategori/' . $video['kategori_id'] . '/' . createSlug($video['kategori_adi'])); ?>" 
                               class="video-category" 
                               style="color: <?php echo $video['renk'] ?? '#6366f1'; ?>">
                                <i class="fas fa-folder"></i>
                                <?php echo safeOutput($video['kategori_adi']); ?>
                            </a>
                            <?php endif; ?>
                            
                            <span class="video-date">
                                <i class="fas fa-calendar"></i>
                                <?php echo formatDate($video['ekleme_tarihi'], 'd.m.Y'); ?>
                            </span>
                        </div>
                        
                        <div class="video-description">
                            <?php echo truncateString($video['aciklama'], 120); ?>
                        </div>
                        
                        <div class="video-stats">
                            <span class="stat">
                                <i class="fas fa-eye"></i>
                                <?php echo formatNumber($video['izlenme_sayisi']); ?>
                            </span>
                            <span class="stat">
                                <i class="fas fa-thumbs-up"></i>
                                <?php echo formatNumber($video['begeni_sayisi']); ?>
                            </span>
                            <span class="stat">
                                <i class="fas fa-comment"></i>
                                <?php echo formatNumber($video['yorum_sayisi']); ?>
                            </span>
                            <span class="stat">
                                <i class="fas fa-heart"></i>
                                <?php echo formatNumber($video['favori_sayisi']); ?>
                            </span>
                        </div>

                        <!-- Liste Görünümü için Ek Bilgiler -->
                        <div class="video-list-extra">
                            <div class="video-tags">
                                <?php if (!empty($video['etiketler'])): 
                                    $tags = explode(',', $video['etiketler']);
                                    foreach (array_slice($tags, 0, 3) as $tag): ?>
                                    <span class="tag"><?php echo safeOutput(trim($tag)); ?></span>
                                <?php endforeach; endif; ?>
                            </div>
                            
                            <div class="video-actions">
                                <button class="btn btn-sm btn-outline" onclick="playVideo(<?php echo $video['id']; ?>)">
                                    <i class="fas fa-play"></i>
                                    İzle
                                </button>
                                
                                <?php if ($current_user): ?>
                                <button class="btn btn-sm btn-outline" onclick="addToWatchLater(<?php echo $video['id']; ?>)">
                                    <i class="fas fa-bookmark"></i>
                                    Sonra İzle
                                </button>
                                <button class="btn btn-sm btn-outline" onclick="shareVideo(<?php echo $video['id']; ?>)">
                                    <i class="fas fa-share"></i>
                                    Paylaş
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php endif; ?>
        </div>

        <!-- Loading Indicator -->
        <div id="loadingIndicator" class="loading-indicator" style="display: none;">
            <div class="loading-spinner"></div>
            <span>Yükleniyor...</span>
        </div>
    </section>

    <!-- Sayfalama -->
    <?php if ($total_pages > 1): ?>
    <section class="pagination-section">
        <div class="pagination-container">
            <div class="pagination-info">
                <span>
                    Sayfa <strong><?php echo $page; ?></strong> / <strong><?php echo $total_pages; ?></strong>
                    (Toplam <strong><?php echo number_format($total_videos); ?></strong> video)
                </span>
            </div>
            
            <nav class="pagination" aria-label="Video sayfalama">
                <?php
                // Sayfalama linkleri
                $start = max(1, $page - 2);
                $end = min($total_pages, $page + 2);
                
                // İlk sayfa
                if ($page > 1): ?>
                <a href="?<?php echo buildQueryString(['sayfa' => 1]); ?>" class="pagination-link">
                    <i class="fas fa-angle-double-left"></i>
                </a>
                <a href="?<?php echo buildQueryString(['sayfa' => $page - 1]); ?>" class="pagination-link">
                    <i class="fas fa-angle-left"></i>
                </a>
                <?php endif;
                
                // Sayfa numaraları
                for ($i = $start; $i <= $end; $i++): ?>
                <a href="?<?php echo buildQueryString(['sayfa' => $i]); ?>" 
                   class="pagination-link <?php echo $i === $page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
                <?php endfor;
                
                // Son sayfa
                if ($page < $total_pages): ?>
                <a href="?<?php echo buildQueryString(['sayfa' => $page + 1]); ?>" class="pagination-link">
                    <i class="fas fa-angle-right"></i>
                </a>
                <a href="?<?php echo buildQueryString(['sayfa' => $total_pages]); ?>" class="pagination-link">
                    <i class="fas fa-angle-double-right"></i>
                </a>
                <?php endif; ?>
            </nav>
        </div>
    </section>
    <?php endif; ?>

</div>

<!-- DOBİEN Developer Signature -->
<div class="dobien-signature">
    <i class="fas fa-film"></i> DOBİEN Gelişmiş Video Listesi
</div>

<style>
/* Video Listesi Özel CSS */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    padding: 2rem 0;
    border-bottom: 1px solid var(--border-color);
    flex-wrap: wrap;
    gap: 1rem;
}

.page-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 0.5rem;
}

.page-title i {
    color: var(--primary-color);
}

.page-description {
    color: var(--text-muted);
    font-size: 1.1rem;
}

.view-toggles {
    display: flex;
    gap: 0.5rem;
    background: var(--bg-tertiary);
    border-radius: var(--radius-lg);
    padding: 0.5rem;
}

.view-toggle {
    padding: 0.75rem 1rem;
    background: none;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    border-radius: var(--radius-md);
    transition: var(--transition);
    font-size: 1rem;
}

.view-toggle:hover,
.view-toggle.active {
    background: var(--primary-color);
    color: white;
}

/* Filtreler */
.filters-section {
    margin-bottom: 2rem;
}

.filters-container {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-2xl);
    padding: 2rem;
}

.search-container {
    position: relative;
    margin-bottom: 2rem;
}

.search-box {
    position: relative;
    max-width: 600px;
    margin: 0 auto;
}

.search-input {
    width: 100%;
    padding: 1rem 4rem 1rem 1.5rem;
    background: var(--bg-tertiary);
    border: 2px solid var(--border-color);
    border-radius: var(--radius-xl);
    color: var(--text-primary);
    font-size: 1.1rem;
    transition: var(--transition);
}

.search-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
}

.search-btn {
    position: absolute;
    right: 0.5rem;
    top: 50%;
    transform: translateY(-50%);
    background: var(--gradient-primary);
    color: white;
    border: none;
    border-radius: var(--radius-lg);
    padding: 0.75rem 1rem;
    cursor: pointer;
    transition: var(--transition);
}

.search-btn:hover {
    transform: translateY(-50%) scale(1.05);
}

.search-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    z-index: 1000;
    max-height: 300px;
    overflow-y: auto;
    display: none;
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.filter-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-secondary);
    font-weight: 500;
    font-size: 0.9rem;
}

.filter-label i {
    color: var(--primary-color);
}

.filter-select {
    padding: 0.75rem;
    background: var(--bg-tertiary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    color: var(--text-primary);
    cursor: pointer;
    transition: var(--transition);
}

.filter-select:focus {
    outline: none;
    border-color: var(--primary-color);
}

.filter-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

/* Aktif Filtreler */
.active-filters {
    margin-bottom: 2rem;
}

.active-filters-container {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.active-filters-label {
    color: var(--text-secondary);
    font-weight: 500;
    white-space: nowrap;
}

.active-filters-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.filter-tag {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    padding: 0.5rem 0.75rem;
    font-size: 0.9rem;
    color: var(--text-secondary);
}

.filter-tag i {
    color: var(--primary-color);
}

.filter-remove {
    background: none;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    font-size: 1.2rem;
    line-height: 1;
    padding: 0;
    margin-left: 0.5rem;
    transition: var(--transition);
}

.filter-remove:hover {
    color: var(--error-color);
}

/* Video Kartları */
.videos-container.view-grid .videos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 2rem;
}

.videos-container.view-list .videos-grid {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.video-card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-xl);
    overflow: hidden;
    transition: var(--transition);
    position: relative;
}

.video-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary-color);
}

.videos-container.view-list .video-card {
    display: flex;
    align-items: flex-start;
    gap: 1.5rem;
    padding: 1.5rem;
}

.videos-container.view-list .video-thumbnail {
    width: 300px;
    flex-shrink: 0;
}

.video-thumbnail {
    position: relative;
    aspect-ratio: 16/9;
    overflow: hidden;
    background: var(--bg-primary);
}

.video-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--transition);
}

.video-card:hover .video-thumbnail img {
    transform: scale(1.05);
}

.video-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: var(--transition);
}

.video-card:hover .video-overlay {
    opacity: 1;
}

.play-button {
    width: 60px;
    height: 60px;
    background: var(--gradient-primary);
    border: none;
    border-radius: 50%;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
}

.play-button:hover {
    transform: scale(1.1);
}

.video-duration {
    position: absolute;
    bottom: 0.5rem;
    right: 0.5rem;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: var(--radius-sm);
    font-size: 0.8rem;
    font-weight: 500;
}

.quality-badge {
    position: absolute;
    top: 0.5rem;
    left: 0.5rem;
    padding: 0.25rem 0.5rem;
    border-radius: var(--radius-sm);
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    color: white;
}

.quality-720 {
    background: var(--success-color);
}

.quality-1080 {
    background: var(--warning-color);
}

.quality-4 {
    background: var(--primary-color);
}

.membership-badge {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
}

.membership-vip {
    background: var(--warning-color);
}

.membership-premium {
    background: var(--primary-color);
}

.favorite-btn {
    position: absolute;
    bottom: 0.5rem;
    left: 0.5rem;
    width: 36px;
    height: 36px;
    background: rgba(0, 0, 0, 0.8);
    border: none;
    border-radius: 50%;
    color: var(--text-muted);
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
}

.favorite-btn:hover,
.favorite-btn.active {
    color: var(--error-color);
    background: rgba(239, 68, 68, 0.2);
}

.video-info {
    padding: 1.5rem;
}

.videos-container.view-list .video-info {
    padding: 0;
    flex: 1;
}

.video-title {
    margin-bottom: 0.75rem;
}

.video-title a {
    color: var(--text-primary);
    text-decoration: none;
    font-size: 1.1rem;
    font-weight: 600;
    line-height: 1.4;
    display: block;
    transition: var(--transition);
}

.video-title a:hover {
    color: var(--primary-color);
}

.video-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 0.75rem;
    flex-wrap: wrap;
}

.video-category,
.video-date {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.8rem;
    color: var(--text-muted);
    text-decoration: none;
    transition: var(--transition);
}

.video-category:hover {
    color: var(--primary-color);
}

.video-description {
    color: var(--text-secondary);
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 1rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.videos-container.view-list .video-description {
    -webkit-line-clamp: 3;
}

.video-stats {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.stat {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.8rem;
    color: var(--text-muted);
}

.stat i {
    color: var(--primary-color);
}

/* Liste görünümü ek özellikler */
.video-list-extra {
    display: none;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
}

.videos-container.view-list .video-list-extra {
    display: block;
}

.video-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.tag {
    background: var(--bg-tertiary);
    color: var(--text-muted);
    padding: 0.25rem 0.5rem;
    border-radius: var(--radius-sm);
    font-size: 0.75rem;
}

.video-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

/* Boş durum */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--text-muted);
}

.empty-state-icon {
    font-size: 4rem;
    margin-bottom: 1.5rem;
    opacity: 0.5;
}

.empty-state h3 {
    font-size: 1.5rem;
    color: var(--text-secondary);
    margin-bottom: 1rem;
}

.empty-state p {
    margin-bottom: 2rem;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

/* Loading indicator */
.loading-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    padding: 2rem;
    color: var(--text-muted);
}

.loading-spinner {
    width: 24px;
    height: 24px;
    border: 2px solid var(--border-color);
    border-top: 2px solid var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

/* Sayfalama */
.pagination-section {
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1px solid var(--border-color);
}

.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.pagination-info {
    color: var(--text-muted);
    font-size: 0.9rem;
}

.pagination {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.pagination-link {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    color: var(--text-secondary);
    text-decoration: none;
    transition: var(--transition);
    font-weight: 500;
}

.pagination-link:hover,
.pagination-link.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

/* Responsive */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        text-align: center;
    }
    
    .page-title {
        font-size: 2rem;
    }
    
    .filters-grid {
        grid-template-columns: 1fr;
    }
    
    .filter-actions {
        flex-direction: column;
    }
    
    .videos-container.view-grid .videos-grid {
        grid-template-columns: 1fr;
    }
    
    .videos-container.view-list .video-card {
        flex-direction: column;
    }
    
    .videos-container.view-list .video-thumbnail {
        width: 100%;
    }
    
    .active-filters-container {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .pagination-container {
        flex-direction: column;
        text-align: center;
    }
}

@media (max-width: 480px) {
    .search-input {
        padding: 0.875rem 3.5rem 0.875rem 1rem;
        font-size: 1rem;
    }
    
    .video-stats {
        justify-content: center;
    }
    
    .video-actions {
        justify-content: center;
    }
}
</style>

<script>
/**
 * DOBİEN Video Platform - Video Listesi JavaScript
 * Geliştirici: DOBİEN
 */

// Değişkenler
let searchTimeout;
let currentPage = <?php echo $page; ?>;
let isLoading = false;

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
    initializeVideoList();
});

function initializeVideoList() {
    // Arama kutusu
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                showSearchSuggestions(this.value);
            }, 300);
        });
    }
    
    // Filter değişiklikleri
    document.querySelectorAll('.filter-select').forEach(select => {
        select.addEventListener('change', applyFilters);
    });
    
    // Görünüm değiştirme
    document.querySelectorAll('.view-toggle').forEach(toggle => {
        toggle.addEventListener('click', function() {
            const view = this.getAttribute('data-view');
            changeView(view);
        });
    });
    
    // Infinite scroll (isteğe bağlı)
    // window.addEventListener('scroll', handleScroll);
    
    console.log('DOBİEN Video Listesi yüklendi!');
}

// Arama fonksiyonu
function performSearch() {
    const searchValue = document.getElementById('searchInput').value;
    updateURL({arama: searchValue, sayfa: 1});
}

// Arama önerileri
function showSearchSuggestions(query) {
    if (query.length < 2) {
        document.getElementById('searchSuggestions').style.display = 'none';
        return;
    }
    
    // AJAX ile öneriler getir
    fetch(`ajax/search-suggestions.php?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            displaySuggestions(data);
        })
        .catch(error => {
            console.error('Arama önerisi hatası:', error);
        });
}

function displaySuggestions(suggestions) {
    const container = document.getElementById('searchSuggestions');
    
    if (suggestions.length === 0) {
        container.style.display = 'none';
        return;
    }
    
    container.innerHTML = suggestions.map(item => 
        `<div class="suggestion-item" onclick="selectSuggestion('${item}')">${item}</div>`
    ).join('');
    
    container.style.display = 'block';
}

function selectSuggestion(text) {
    document.getElementById('searchInput').value = text;
    document.getElementById('searchSuggestions').style.display = 'none';
    performSearch();
}

// Filtreleri uygula
function applyFilters() {
    const filters = {
        arama: document.getElementById('searchInput').value,
        kategori: document.getElementById('categoryFilter').value,
        kalite: document.getElementById('qualityFilter').value,
        uyelik: document.getElementById('membershipFilter').value,
        sure: document.getElementById('durationFilter').value,
        sirala: document.getElementById('sortFilter').value,
        limit: document.getElementById('limitFilter').value,
        sayfa: 1
    };
    
    updateURL(filters);
}

// Filtreleri temizle
function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('categoryFilter').value = '';
    document.getElementById('qualityFilter').value = '';
    document.getElementById('membershipFilter').value = '';
    document.getElementById('durationFilter').value = '';
    document.getElementById('sortFilter').value = 'yeni';
    document.getElementById('limitFilter').value = '20';
    
    updateURL({});
}

// Tekil filtre kaldır
function removeFilter(filterName) {
    const currentURL = new URL(window.location);
    currentURL.searchParams.delete(filterName);
    currentURL.searchParams.set('sayfa', '1');
    window.location.href = currentURL.toString();
}

// Görünüm değiştir
function changeView(view) {
    document.querySelectorAll('.view-toggle').forEach(t => t.classList.remove('active'));
    document.querySelector(`[data-view="${view}"]`).classList.add('active');
    
    const container = document.getElementById('videosContainer');
    container.className = `videos-container view-${view}`;
    
    // URL'yi güncelle
    const currentURL = new URL(window.location);
    currentURL.searchParams.set('gorunum', view);
    history.pushState(null, '', currentURL.toString());
}

// URL güncelle
function updateURL(params) {
    const url = new URL(window.location);
    
    // Mevcut parametreleri temizle
    if (Object.keys(params).length === 0) {
        url.search = '';
    } else {
        Object.entries(params).forEach(([key, value]) => {
            if (value) {
                url.searchParams.set(key, value);
            } else {
                url.searchParams.delete(key);
            }
        });
    }
    
    window.location.href = url.toString();
}

// Video oynat
function playVideo(videoId) {
    // Video detay sayfasına git veya modal aç
    window.location.href = `video/${videoId}`;
}

// Favoriye ekle/çıkar
function toggleFavorite(videoId, button) {
    if (!<?php echo $current_user ? 'true' : 'false'; ?>) {
        showLoginModal();
        return;
    }
    
    fetch('ajax/toggle-favorite.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({video_id: videoId})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.classList.toggle('active');
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Favori hatası:', error);
        showNotification('Bir hata oluştu', 'error');
    });
}

// Sonra izle listesine ekle
function addToWatchLater(videoId) {
    if (!<?php echo $current_user ? 'true' : 'false'; ?>) {
        showLoginModal();
        return;
    }
    
    fetch('ajax/add-watch-later.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({video_id: videoId})
    })
    .then(response => response.json())
    .then(data => {
        showNotification(data.message, data.success ? 'success' : 'error');
    });
}

// Video paylaş
function shareVideo(videoId) {
    const url = `${window.location.origin}/video/${videoId}`;
    
    if (navigator.share) {
        navigator.share({
            title: 'DOBİEN Video',
            url: url
        });
    } else {
        navigator.clipboard.writeText(url).then(() => {
            showNotification('Link kopyalandı!', 'success');
        });
    }
}

// Giriş modal'ı göster
function showLoginModal() {
    // Ana sayfadaki login modal'ını kullan
    if (typeof openLoginModal === 'function') {
        openLoginModal();
    } else {
        window.location.href = 'giris.php';
    }
}

// Bildirim göster
function showNotification(message, type = 'info') {
    // Ana CSS'teki notification sistemi
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'}-circle"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Infinite scroll (isteğe bağlı)
function handleScroll() {
    if (isLoading) return;
    
    const scrollTop = window.pageYOffset;
    const windowHeight = window.innerHeight;
    const docHeight = document.documentElement.scrollHeight;
    
    if (scrollTop + windowHeight >= docHeight - 1000) {
        loadMoreVideos();
    }
}

function loadMoreVideos() {
    if (currentPage >= <?php echo $total_pages; ?>) return;
    
    isLoading = true;
    document.getElementById('loadingIndicator').style.display = 'flex';
    
    const currentURL = new URL(window.location);
    currentURL.searchParams.set('sayfa', currentPage + 1);
    currentURL.searchParams.set('ajax', '1');
    
    fetch(currentURL.toString())
        .then(response => response.json())
        .then(data => {
            appendVideos(data.videos);
            currentPage++;
            isLoading = false;
            document.getElementById('loadingIndicator').style.display = 'none';
        })
        .catch(error => {
            console.error('Video yükleme hatası:', error);
            isLoading = false;
            document.getElementById('loadingIndicator').style.display = 'none';
        });
}

function appendVideos(videos) {
    const container = document.querySelector('.videos-grid');
    videos.forEach(video => {
        const videoElement = createVideoElement(video);
        container.appendChild(videoElement);
    });
}

console.log('DOBİEN Gelişmiş Video Listesi yüklendi!');
</script>

<?php 
include 'includes/footer.php';

// Helper fonksiyonlar
function buildQueryString($params) {
    global $_GET;
    $query = $_GET;
    
    foreach ($params as $key => $value) {
        if ($value) {
            $query[$key] = $value;
        } else {
            unset($query[$key]);
        }
    }
    
    return http_build_query($query);
}

function createSlug($text) {
    $text = mb_strtolower($text, 'UTF-8');
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

function formatNumber($number) {
    if ($number >= 1000000) {
        return number_format($number / 1000000, 1) . 'M';
    } elseif ($number >= 1000) {
        return number_format($number / 1000, 1) . 'K';
    }
    return number_format($number);
}

function truncateString($string, $length) {
    if (strlen($string) <= $length) {
        return $string;
    }
    return substr($string, 0, $length) . '...';
}
?>