<?php
/**
 * DOBİEN Video Platform - İzleme Listesi
 * Geliştirici: DOBİEN
 * Modern Video Paylaşım Platformu
 */

require_once 'includes/config.php';

// Kullanıcı giriş kontrolü
if (!$current_user) {
    header('Location: giris.php');
    exit;
}

$page_title = "İzleme Listesi - " . $site_settings['site_adi'];

// Sayfalama
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

// İzleme listesini çek
try {
    // Toplam sayı
    $count_stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM kullanici_video_etkilesimleri kve 
        INNER JOIN videolar v ON kve.video_id = v.id 
        WHERE kve.kullanici_id = ? AND kve.etkilesim_tipi = 'izleme_listesi' AND v.durum = 'aktif'
    ");
    $count_stmt->execute([$current_user['id']]);
    $total_videos = $count_stmt->fetchColumn();
    $total_pages = ceil($total_videos / $per_page);
    
    // Videolar
    $videos_stmt = $pdo->prepare("
        SELECT v.*, k.kategori_adi, kve.olusturma_tarihi as ekleme_tarihi
        FROM kullanici_video_etkilesimleri kve 
        INNER JOIN videolar v ON kve.video_id = v.id 
        LEFT JOIN kategoriler k ON v.kategori_id = k.id
        WHERE kve.kullanici_id = ? AND kve.etkilesim_tipi = 'izleme_listesi' AND v.durum = 'aktif'
        ORDER BY kve.olusturma_tarihi DESC 
        LIMIT $per_page OFFSET $offset
    ");
    $videos_stmt->execute([$current_user['id']]);
    $playlist_videos = $videos_stmt->fetchAll();
    
} catch (PDOException $e) {
    $playlist_videos = [];
    $total_videos = 0;
    $total_pages = 0;
}

include 'includes/header.php';
?>

<div class="container">
    <div class="page-header mb-5">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-2">
                    <i class="fas fa-bookmark text-warning me-3"></i>İzleme Listesi
                </h1>
                <p class="text-muted mb-0">Daha sonra izlemek için <?php echo number_format($total_videos); ?> video</p>
            </div>
            <a href="profil.php" class="btn btn-outline-primary">
                <i class="fas fa-user me-2"></i>Profilime Dön
            </a>
        </div>
    </div>

    <?php if (!empty($playlist_videos)): ?>
        <div class="video-grid mb-5">
            <?php foreach ($playlist_videos as $video): ?>
                <div class="video-card">
                    <div class="video-thumbnail">
                        <img src="<?php echo htmlspecialchars($video['kapak_resmi'] ?: 'assets/images/default-thumbnail.jpg'); ?>" 
                             alt="<?php echo htmlspecialchars($video['baslik']); ?>">
                        
                        <div class="video-overlay">
                            <a href="video.php?id=<?php echo $video['id']; ?>" class="play-btn">
                                <i class="fas fa-play"></i>
                            </a>
                        </div>
                        
                        <div class="playlist-badge">
                            <i class="fas fa-bookmark"></i>
                            Listede
                        </div>
                        
                        <?php if ($video['sure']): ?>
                            <div class="video-duration">
                                <?php echo formatDuration($video['sure']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="video-info">
                        <h3 class="video-title">
                            <a href="video.php?id=<?php echo $video['id']; ?>">
                                <?php echo htmlspecialchars($video['baslik']); ?>
                            </a>
                        </h3>
                        
                        <div class="video-meta">
                            <span>
                                <i class="fas fa-bookmark"></i>
                                <?php echo formatDate($video['ekleme_tarihi'], 'd.m.Y'); ?>
                            </span>
                            <span>
                                <i class="fas fa-eye"></i>
                                <?php echo number_format($video['izlenme_sayisi']); ?>
                            </span>
                        </div>
                        
                        <?php if ($video['kategori_adi']): ?>
                            <div class="video-category">
                                <?php echo htmlspecialchars($video['kategori_adi']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <?php echo pagination($page, $total_pages, '?page='); ?>
        <?php endif; ?>
        
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-bookmark"></i>
            <h3>İzleme listeniz boş</h3>
            <p>Daha sonra izlemek istediğiniz videolar burada görünecek.</p>
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-home me-2"></i>Videolara Göz At
            </a>
        </div>
    <?php endif; ?>
</div>

<style>
.playlist-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: rgba(255, 193, 7, 0.9);
    color: #212529;
    padding: 6px 10px;
    border-radius: 15px;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 5px;
    font-weight: 500;
}
</style>

<?php include 'includes/footer.php'; ?>