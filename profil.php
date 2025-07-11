<?php
/**
 * DOBİEN Video Platform - Kullanıcı Profil Sayfası
 * Geliştirici: DOBİEN
 * Modern Video Paylaşım Platformu
 * Tüm Hakları Saklıdır © DOBİEN
 */

require_once 'includes/config.php';

// Giriş kontrolü
if (!$current_user) {
    header('Location: ' . siteUrl('giris.php'));
    exit;
}

// Sayfa bilgileri
$page_title = "Profilim";
$page_description = "DOBİEN Video Platform - Kullanıcı profil ayarları ve üyelik bilgileri";

// Kullanıcı istatistikleri
$stats_query = "
    SELECT 
        (SELECT COUNT(*) FROM favoriler WHERE kullanici_id = ?) as favori_sayisi,
        (SELECT COUNT(*) FROM izleme_gecmisi WHERE kullanici_id = ?) as izlenen_sayisi,
        (SELECT COUNT(*) FROM begeniler WHERE kullanici_id = ? AND tur = 'begendi') as begeni_sayisi,
        (SELECT SUM(izleme_suresi) FROM izleme_gecmisi WHERE kullanici_id = ?) as toplam_izleme_suresi
";
$stats = $pdo->prepare($stats_query);
$stats->execute([$current_user['id'], $current_user['id'], $current_user['id'], $current_user['id']]);
$user_stats = $stats->fetch();

// Premium/VIP bitiş tarihini hesapla
$membership_end_date = null;
if ($current_user['uyelik_tipi'] == 'premium' && $current_user['premium_bitis']) {
    $membership_end_date = $current_user['premium_bitis'];
} elseif ($current_user['uyelik_tipi'] == 'vip' && $current_user['vip_bitis']) {
    $membership_end_date = $current_user['vip_bitis'];
}

// Son izlenen videolar
$recent_watched_query = "
    SELECT v.*, k.kategori_adi, ig.son_izleme, ig.izleme_suresi
    FROM izleme_gecmisi ig
    JOIN videolar v ON ig.video_id = v.id
    LEFT JOIN kategoriler k ON v.kategori_id = k.id
    WHERE ig.kullanici_id = ? AND v.durum = 'aktif'
    ORDER BY ig.son_izleme DESC
    LIMIT 6
";
$recent_watched = $pdo->prepare($recent_watched_query);
$recent_watched->execute([$current_user['id']]);
$recent_watched_videos = $recent_watched->fetchAll();

// Favoriler
$favorites_query = "
    SELECT v.*, k.kategori_adi, f.ekleme_tarihi
    FROM favoriler f
    JOIN videolar v ON f.video_id = v.id
    LEFT JOIN kategoriler k ON v.kategori_id = k.id
    WHERE f.kullanici_id = ? AND v.durum = 'aktif'
    ORDER BY f.ekleme_tarihi DESC
    LIMIT 6
";
$favorites = $pdo->prepare($favorites_query);
$favorites->execute([$current_user['id']]);
$favorite_videos = $favorites->fetchAll();

// Beğendikleri
$liked_query = "
    SELECT v.*, k.kategori_adi, b.tarih
    FROM begeniler b
    JOIN videolar v ON b.video_id = v.id
    LEFT JOIN kategoriler k ON v.kategori_id = k.id
    WHERE b.kullanici_id = ? AND b.tur = 'begendi' AND v.durum = 'aktif'
    ORDER BY b.tarih DESC
    LIMIT 6
";
$liked = $pdo->prepare($liked_query);
$liked->execute([$current_user['id']]);
$liked_videos = $liked->fetchAll();

// Üyelik avantajları
$membership_benefits = [
    'kullanici' => [
        'name' => 'Ücretsiz Üye',
        'color' => 'default',
        'benefits' => ['720p video kalitesi', 'Sınırlı içerik erişimi', 'Topluluk desteği']
    ],
    'vip' => [
        'name' => 'VIP Üye',
        'color' => 'warning',
        'benefits' => ['1080p Full HD kalite', 'VIP özel içerikler', '24/7 öncelikli destek', '3 cihazda izleme', 'Offline indirme']
    ],
    'premium' => [
        'name' => 'Premium Üye',
        'color' => 'primary',
        'benefits' => ['4K Ultra HD + HDR', 'Tüm premium içerikler', 'Özel destek temsilcisi', 'Sınırsız cihaz', 'Dolby Atmos ses', 'Reklamsız deneyim']
    ]
];

include 'includes/header.php';
?>

<div class="container">
    <!-- Profil Header -->
    <section class="profile-header">
        <div class="profile-banner">
            <div class="profile-content">
                <div class="profile-avatar">
                    <img src="<?php echo $current_user['avatar'] ? siteUrl('uploads/avatars/' . $current_user['avatar']) : siteUrl('assets/images/default-avatar.png'); ?>" 
                         alt="<?php echo safeOutput($current_user['kullanici_adi']); ?>">
                    <div class="avatar-badge <?php echo $current_user['uyelik_tipi']; ?>">
                        <?php
                        switch($current_user['uyelik_tipi']) {
                            case 'premium':
                                echo '<i class="fas fa-gem"></i>';
                                break;
                            case 'vip':
                                echo '<i class="fas fa-crown"></i>';
                                break;
                            default:
                                echo '<i class="fas fa-user"></i>';
                                break;
                        }
                        ?>
                    </div>
                </div>
                
                <div class="profile-info">
                    <h1 class="profile-name"><?php echo safeOutput($current_user['kullanici_adi']); ?></h1>
                    <div class="membership-info">
                        <span class="membership-badge <?php echo $current_user['uyelik_tipi']; ?>">
                            <?php echo $membership_benefits[$current_user['uyelik_tipi']]['name']; ?>
                        </span>
                        <?php if ($membership_end_date): ?>
                        <span class="membership-expiry">
                            <i class="fas fa-calendar"></i>
                            <?php echo formatDate($membership_end_date, 'd.m.Y'); ?> tarihine kadar
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="profile-meta">
                        <div class="meta-item">
                            <i class="fas fa-calendar-plus"></i>
                            <span>Katılım: <?php echo formatDate($current_user['kayit_tarihi'], 'd.m.Y'); ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-clock"></i>
                            <span>Son giriş: <?php echo $current_user['son_giris'] ? formatDate($current_user['son_giris'], 'd.m.Y H:i') : 'İlk giriş'; ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="profile-actions">
                    <a href="<?php echo siteUrl('hesap-ayarlari.php'); ?>" class="btn btn-primary">
                        <i class="fas fa-cog"></i>
                        Hesap Ayarları
                    </a>
                    <?php if ($current_user['uyelik_tipi'] == 'kullanici'): ?>
                    <a href="<?php echo siteUrl('uyelik-yukselt.php'); ?>" class="btn btn-outline">
                        <i class="fas fa-crown"></i>
                        Üyeliği Yükselt
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- İstatistikler -->
    <section class="profile-stats">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-eye"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($user_stats['izlenen_sayisi'] ?: 0); ?></h3>
                    <p>İzlenen Video</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($user_stats['favori_sayisi'] ?: 0); ?></h3>
                    <p>Favori Video</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-thumbs-up"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($user_stats['begeni_sayisi'] ?: 0); ?></h3>
                    <p>Beğeni</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo formatDuration($user_stats['toplam_izleme_suresi'] ?: 0); ?></h3>
                    <p>Toplam İzleme</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Üyelik Avantajları -->
    <section class="membership-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-star"></i>
                Üyelik Avantajlarınız
            </h2>
        </div>
        
        <div class="membership-card <?php echo $current_user['uyelik_tipi']; ?>">
            <div class="membership-header">
                <div class="membership-icon">
                    <?php
                    switch($current_user['uyelik_tipi']) {
                        case 'premium':
                            echo '<i class="fas fa-gem"></i>';
                            break;
                        case 'vip':
                            echo '<i class="fas fa-crown"></i>';
                            break;
                        default:
                            echo '<i class="fas fa-user"></i>';
                            break;
                    }
                    ?>
                </div>
                <div class="membership-info">
                    <h3><?php echo $membership_benefits[$current_user['uyelik_tipi']]['name']; ?></h3>
                    <?php if ($membership_end_date): ?>
                    <p>Geçerlilik: <?php echo formatDate($membership_end_date, 'd.m.Y H:i'); ?></p>
                    <?php endif; ?>
                </div>
                <?php if ($current_user['uyelik_tipi'] != 'premium'): ?>
                <a href="<?php echo siteUrl('uyelik-yukselt.php'); ?>" class="btn btn-primary">
                    <i class="fas fa-arrow-up"></i>
                    Yükselt
                </a>
                <?php endif; ?>
            </div>
            
            <div class="membership-benefits">
                <?php foreach ($membership_benefits[$current_user['uyelik_tipi']]['benefits'] as $benefit): ?>
                <div class="benefit-item">
                    <i class="fas fa-check"></i>
                    <span><?php echo $benefit; ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Sekme Navigasyonu -->
    <section class="profile-tabs">
        <div class="tab-navigation">
            <button class="tab-btn active" data-tab="recent">
                <i class="fas fa-history"></i>
                Son İzlenenler
            </button>
            <button class="tab-btn" data-tab="favorites">
                <i class="fas fa-heart"></i>
                Favorilerim
            </button>
            <button class="tab-btn" data-tab="liked">
                <i class="fas fa-thumbs-up"></i>
                Beğendiklerim
            </button>
            <button class="tab-btn" data-tab="playlists">
                <i class="fas fa-list"></i>
                Listelerim
            </button>
        </div>

        <!-- Son İzlenenler -->
        <div class="tab-content active" id="recent">
            <div class="content-header">
                <h3>Son İzlenen Videolar</h3>
                <a href="<?php echo siteUrl('izleme-gecmisi.php'); ?>" class="btn btn-outline">
                    Tümünü Gör <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <?php if (!empty($recent_watched_videos)): ?>
            <div class="video-grid">
                <?php foreach ($recent_watched_videos as $video): ?>
                <div class="video-card" onclick="playVideo('<?php echo $video['id']; ?>')">
                    <div class="video-thumbnail">
                        <img src="<?php echo $video['kapak_resmi'] ? siteUrl('uploads/thumbnails/' . $video['kapak_resmi']) : siteUrl('assets/images/default-thumbnail.jpg'); ?>" 
                             alt="<?php echo safeOutput($video['baslik']); ?>">
                        
                        <div class="video-progress">
                            <div class="progress-bar" style="width: <?php echo $video['sure'] > 0 ? ($video['izleme_suresi'] / $video['sure']) * 100 : 0; ?>%"></div>
                        </div>
                        
                        <div class="video-duration"><?php echo formatDuration($video['sure']); ?></div>
                    </div>
                    
                    <div class="video-info">
                        <h3 class="video-title"><?php echo safeOutput($video['baslik']); ?></h3>
                        <div class="video-meta">
                            <span><i class="fas fa-calendar"></i> <?php echo formatDate($video['son_izleme'], 'd.m.Y'); ?></span>
                            <?php if ($video['kategori_adi']): ?>
                            <span class="video-category"><?php echo safeOutput($video['kategori_adi']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-history"></i>
                <h3>Henüz video izlemediniz</h3>
                <p>İzlemeye başladığınız videolar burada görünecek.</p>
                <a href="<?php echo siteUrl(); ?>" class="btn btn-primary">Videolara Göz At</a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Favoriler -->
        <div class="tab-content" id="favorites">
            <div class="content-header">
                <h3>Favori Videolarım</h3>
                <a href="<?php echo siteUrl('favoriler.php'); ?>" class="btn btn-outline">
                    Tümünü Gör <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <?php if (!empty($favorite_videos)): ?>
            <div class="video-grid">
                <?php foreach ($favorite_videos as $video): ?>
                <div class="video-card" onclick="playVideo('<?php echo $video['id']; ?>')">
                    <div class="video-thumbnail">
                        <img src="<?php echo $video['kapak_resmi'] ? siteUrl('uploads/thumbnails/' . $video['kapak_resmi']) : siteUrl('assets/images/default-thumbnail.jpg'); ?>" 
                             alt="<?php echo safeOutput($video['baslik']); ?>">
                        
                        <div class="video-duration"><?php echo formatDuration($video['sure']); ?></div>
                        
                        <div class="favorite-badge">
                            <i class="fas fa-heart"></i>
                        </div>
                    </div>
                    
                    <div class="video-info">
                        <h3 class="video-title"><?php echo safeOutput($video['baslik']); ?></h3>
                        <div class="video-meta">
                            <span><i class="fas fa-heart"></i> <?php echo formatDate($video['ekleme_tarihi'], 'd.m.Y'); ?></span>
                            <?php if ($video['kategori_adi']): ?>
                            <span class="video-category"><?php echo safeOutput($video['kategori_adi']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-heart"></i>
                <h3>Henüz favori videonuz yok</h3>
                <p>Beğendiğiniz videoları favorilerinize ekleyebilirsiniz.</p>
                <a href="<?php echo siteUrl(); ?>" class="btn btn-primary">Videolara Göz At</a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Beğendiklerim -->
        <div class="tab-content" id="liked">
            <div class="content-header">
                <h3>Beğendiğim Videolar</h3>
                <a href="<?php echo siteUrl('begendiklerim.php'); ?>" class="btn btn-outline">
                    Tümünü Gör <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <?php if (!empty($liked_videos)): ?>
            <div class="video-grid">
                <?php foreach ($liked_videos as $video): ?>
                <div class="video-card" onclick="playVideo('<?php echo $video['id']; ?>')">
                    <div class="video-thumbnail">
                        <img src="<?php echo $video['kapak_resmi'] ? siteUrl('uploads/thumbnails/' . $video['kapak_resmi']) : siteUrl('assets/images/default-thumbnail.jpg'); ?>" 
                             alt="<?php echo safeOutput($video['baslik']); ?>">
                        
                        <div class="video-duration"><?php echo formatDuration($video['sure']); ?></div>
                        
                        <div class="like-badge">
                            <i class="fas fa-thumbs-up"></i>
                        </div>
                    </div>
                    
                    <div class="video-info">
                        <h3 class="video-title"><?php echo safeOutput($video['baslik']); ?></h3>
                        <div class="video-meta">
                            <span><i class="fas fa-thumbs-up"></i> <?php echo formatDate($video['tarih'], 'd.m.Y'); ?></span>
                            <?php if ($video['kategori_adi']): ?>
                            <span class="video-category"><?php echo safeOutput($video['kategori_adi']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-thumbs-up"></i>
                <h3>Henüz beğendiğiniz video yok</h3>
                <p>Beğendiğiniz videolar burada görünecek.</p>
                <a href="<?php echo siteUrl(); ?>" class="btn btn-primary">Videolara Göz At</a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Listelerim -->
        <div class="tab-content" id="playlists">
            <div class="content-header">
                <h3>İzleme Listelerim</h3>
                <button class="btn btn-primary" onclick="createPlaylist()">
                    <i class="fas fa-plus"></i>
                    Yeni Liste Oluştur
                </button>
            </div>
            
            <div class="playlists-grid">
                <div class="playlist-card">
                    <div class="playlist-thumbnail">
                        <i class="fas fa-bookmark"></i>
                        <span class="video-count">0 video</span>
                    </div>
                    <h3>Daha Sonra İzle</h3>
                    <p>Daha sonra izlemek istediğiniz videolar</p>
                </div>
                
                <div class="empty-state">
                    <i class="fas fa-list"></i>
                    <h3>Henüz listeniz yok</h3>
                    <p>Videolarınızı organize etmek için listeler oluşturabilirsiniz.</p>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- DOBİEN Developer Signature -->
<div class="dobien-signature">
    <i class="fas fa-user"></i> DOBİEN Profil
</div>

<style>
/* Profil Sayfası Özel CSS */
.profile-header {
    margin-bottom: 3rem;
}

.profile-banner {
    background: var(--gradient-card);
    border-radius: var(--radius-2xl);
    padding: 3rem 2rem;
    position: relative;
    overflow: hidden;
}

.profile-banner::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="%23ffffff" opacity="0.05"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    pointer-events: none;
}

.profile-content {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    gap: 2rem;
    flex-wrap: wrap;
}

.profile-avatar {
    position: relative;
}

.profile-avatar img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 4px solid white;
    object-fit: cover;
    box-shadow: var(--shadow-lg);
}

.avatar-badge {
    position: absolute;
    bottom: 5px;
    right: 5px;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
    border: 3px solid white;
}

.avatar-badge.kullanici {
    background: var(--bg-primary);
}

.avatar-badge.vip {
    background: var(--warning-color);
}

.avatar-badge.premium {
    background: var(--primary-color);
}

.profile-info {
    flex: 1;
    min-width: 300px;
}

.profile-name {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.membership-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.membership-badge {
    padding: 0.5rem 1rem;
    border-radius: var(--radius-lg);
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.membership-badge.kullanici {
    background: var(--bg-tertiary);
    color: var(--text-muted);
}

.membership-badge.vip {
    background: var(--warning-color);
    color: white;
}

.membership-badge.premium {
    background: var(--primary-color);
    color: white;
}

.membership-expiry {
    color: var(--text-secondary);
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.profile-meta {
    display: flex;
    gap: 2rem;
    flex-wrap: wrap;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.meta-item i {
    color: var(--primary-color);
}

.profile-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

/* İstatistikler */
.profile-stats {
    margin-bottom: 3rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
}

.stat-card {
    background: var(--bg-card);
    padding: 2rem;
    border-radius: var(--radius-xl);
    display: flex;
    align-items: center;
    gap: 1.5rem;
    border: 1px solid var(--border-color);
    transition: var(--transition);
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    border-color: var(--primary-color);
}

.stat-icon {
    width: 60px;
    height: 60px;
    background: var(--gradient-primary);
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.stat-info h3 {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.stat-info p {
    color: var(--text-muted);
    font-size: 0.9rem;
}

/* Üyelik Kartı */
.membership-section {
    margin-bottom: 3rem;
}

.membership-card {
    background: var(--bg-card);
    border-radius: var(--radius-xl);
    padding: 2rem;
    border: 2px solid var(--border-color);
}

.membership-card.vip {
    border-color: var(--warning-color);
    box-shadow: 0 0 20px rgba(245, 158, 11, 0.2);
}

.membership-card.premium {
    border-color: var(--primary-color);
    box-shadow: 0 0 20px rgba(99, 102, 241, 0.2);
}

.membership-header {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.membership-icon {
    width: 80px;
    height: 80px;
    background: var(--gradient-primary);
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
}

.membership-info {
    flex: 1;
}

.membership-info h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.membership-benefits {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.benefit-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: var(--bg-tertiary);
    border-radius: var(--radius-md);
}

.benefit-item i {
    color: var(--success-color);
    font-size: 1rem;
}

/* Sekmeler */
.profile-tabs {
    margin-bottom: 3rem;
}

.tab-navigation {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 2rem;
    border-bottom: 2px solid var(--border-color);
    flex-wrap: wrap;
}

.tab-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 1.5rem;
    background: none;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    border-radius: var(--radius-md) var(--radius-md) 0 0;
    transition: var(--transition);
    font-weight: 500;
}

.tab-btn:hover,
.tab-btn.active {
    color: var(--primary-color);
    background: var(--bg-tertiary);
    border-bottom: 2px solid var(--primary-color);
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.content-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.content-header h3 {
    font-size: 1.5rem;
    color: var(--text-primary);
}

/* Video Grid */
.video-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
}

.video-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: rgba(255, 255, 255, 0.3);
}

.progress-bar {
    height: 100%;
    background: var(--primary-color);
    transition: width 0.3s ease;
}

.favorite-badge,
.like-badge {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    background: rgba(0, 0, 0, 0.8);
    color: var(--error-color);
    padding: 0.5rem;
    border-radius: 50%;
    font-size: 0.9rem;
}

.like-badge {
    color: var(--primary-color);
}

/* Boş Durum */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--text-muted);
}

.empty-state i {
    font-size: 4rem;
    color: var(--text-muted);
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state h3 {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    color: var(--text-secondary);
}

.empty-state p {
    margin-bottom: 2rem;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}

/* Playlist Grid */
.playlists-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
}

.playlist-card {
    background: var(--bg-card);
    border-radius: var(--radius-xl);
    padding: 1.5rem;
    border: 1px solid var(--border-color);
    transition: var(--transition);
    cursor: pointer;
}

.playlist-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    border-color: var(--primary-color);
}

.playlist-thumbnail {
    aspect-ratio: 16/9;
    background: var(--bg-tertiary);
    border-radius: var(--radius-md);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    position: relative;
}

.playlist-thumbnail i {
    font-size: 2rem;
    color: var(--text-muted);
    margin-bottom: 0.5rem;
}

.video-count {
    color: var(--text-muted);
    font-size: 0.8rem;
}

.playlist-card h3 {
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.playlist-card p {
    color: var(--text-muted);
    font-size: 0.9rem;
}

/* Responsive */
@media (max-width: 768px) {
    .profile-content {
        flex-direction: column;
        text-align: center;
    }
    
    .profile-name {
        font-size: 2rem;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .membership-header {
        flex-direction: column;
        text-align: center;
    }
    
    .tab-navigation {
        flex-direction: column;
    }
    
    .tab-btn {
        border-radius: var(--radius-md);
        border-bottom: none;
        border-left: 3px solid transparent;
    }
    
    .tab-btn:hover,
    .tab-btn.active {
        border-left-color: var(--primary-color);
        border-bottom: none;
    }
    
    .video-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
/**
 * DOBİEN Video Platform - Profil Sayfası JavaScript
 * Geliştirici: DOBİEN
 */

// Sekme işlevselliği
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        // Aktif sekmeyi kaldır
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        
        // Yeni sekmeyi aktif et
        btn.classList.add('active');
        const tabId = btn.getAttribute('data-tab');
        document.getElementById(tabId).classList.add('active');
    });
});

// Video oynatma
function playVideo(videoId) {
    // Video oynatma modal'ını aç
    console.log('Video oynatılıyor:', videoId);
    // Bu fonksiyon ana JavaScript dosyasında detaylandırılacak
}

// Playlist oluşturma
function createPlaylist() {
    const name = prompt('Playlist adını girin:');
    if (name) {
        // AJAX ile playlist oluştur
        console.log('Playlist oluşturuluyor:', name);
    }
}

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOBİEN Profil sayfası yüklendi!');
    
    // İlk sekmeyi aktif et
    document.querySelector('.tab-btn[data-tab="recent"]').click();
});
</script>

<?php include 'includes/footer.php'; ?>