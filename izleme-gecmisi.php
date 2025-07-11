<?php
/**
 * DOBİEN Video Platform - İzleme Geçmişi
 * Geliştirici: DOBİEN
 * Modern Video Paylaşım Platformu
 */

require_once 'includes/config.php';

// Kullanıcı giriş kontrolü
if (!$current_user) {
    header('Location: giris.php');
    exit;
}

$page_title = "İzleme Geçmişi - " . $site_settings['site_adi'];

// Sayfalama
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 15;
$offset = ($page - 1) * $per_page;

// İzleme geçmişini çek
try {
    // Toplam sayı
    $count_stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM kullanici_video_etkilesimleri kve 
        INNER JOIN videolar v ON kve.video_id = v.id 
        WHERE kve.kullanici_id = ? AND kve.etkilesim_tipi = 'izleme' AND v.durum = 'aktif'
    ");
    $count_stmt->execute([$current_user['id']]);
    $total_videos = $count_stmt->fetchColumn();
    $total_pages = ceil($total_videos / $per_page);
    
    // Videolar
    $videos_stmt = $pdo->prepare("
        SELECT v.*, k.kategori_adi, kve.olusturma_tarihi as izleme_tarihi, kve.etkilesim_suresi
        FROM kullanici_video_etkilesimleri kve 
        INNER JOIN videolar v ON kve.video_id = v.id 
        LEFT JOIN kategoriler k ON v.kategori_id = k.id
        WHERE kve.kullanici_id = ? AND kve.etkilesim_tipi = 'izleme' AND v.durum = 'aktif'
        ORDER BY kve.olusturma_tarihi DESC 
        LIMIT $per_page OFFSET $offset
    ");
    $videos_stmt->execute([$current_user['id']]);
    $watched_videos = $videos_stmt->fetchAll();
    
} catch (PDOException $e) {
    $watched_videos = [];
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
                    <i class="fas fa-history text-primary me-3"></i>İzleme Geçmişi
                </h1>
                <p class="text-muted mb-0"><?php echo number_format($total_videos); ?> izlenen video</p>
            </div>
            <div class="d-flex gap-2">
                <a href="profil.php" class="btn btn-outline-primary">
                    <i class="fas fa-user me-2"></i>Profilime Dön
                </a>
                <button class="btn btn-outline-danger" onclick="clearHistory()">
                    <i class="fas fa-trash me-2"></i>Geçmişi Temizle
                </button>
            </div>
        </div>
    </div>

    <?php if (!empty($watched_videos)): ?>
        <div class="row">
            <?php foreach ($watched_videos as $video): ?>
                <div class="col-12 mb-4">
                    <div class="watch-history-item">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <div class="video-thumbnail">
                                    <img src="<?php echo htmlspecialchars($video['kapak_resmi'] ?: 'assets/images/default-thumbnail.jpg'); ?>" 
                                         alt="<?php echo htmlspecialchars($video['baslik']); ?>">
                                    
                                    <div class="video-overlay">
                                        <a href="video.php?id=<?php echo $video['id']; ?>" class="play-btn">
                                            <i class="fas fa-play"></i>
                                        </a>
                                    </div>
                                    
                                    <?php if ($video['sure'] && $video['etkilesim_suresi']): ?>
                                        <div class="watch-progress">
                                            <div class="progress-bar" style="width: <?php echo min(100, ($video['etkilesim_suresi'] / $video['sure']) * 100); ?>%"></div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($video['sure']): ?>
                                        <div class="video-duration">
                                            <?php echo formatDuration($video['sure']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="video-info">
                                    <h5 class="video-title mb-2">
                                        <a href="video.php?id=<?php echo $video['id']; ?>" class="text-decoration-none text-dark">
                                            <?php echo htmlspecialchars($video['baslik']); ?>
                                        </a>
                                    </h5>
                                    
                                    <?php if ($video['aciklama']): ?>
                                        <p class="video-description text-muted mb-2">
                                            <?php echo htmlspecialchars(substr($video['aciklama'], 0, 150)) . '...'; ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <div class="video-meta">
                                        <?php if ($video['kategori_adi']): ?>
                                            <span class="badge bg-light text-dark me-2">
                                                <?php echo htmlspecialchars($video['kategori_adi']); ?>
                                            </span>
                                        <?php endif; ?>
                                        
                                        <small class="text-muted">
                                            <i class="fas fa-eye me-1"></i>
                                            <?php echo number_format($video['izlenme_sayisi']); ?> izlenme
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 text-end">
                                <div class="watch-info">
                                    <div class="watch-date">
                                        <i class="fas fa-clock me-1"></i>
                                        <?php echo formatDate($video['izleme_tarihi'], 'd.m.Y H:i'); ?>
                                    </div>
                                    
                                    <?php if ($video['etkilesim_suresi']): ?>
                                        <div class="watch-duration mt-1">
                                            <small class="text-muted">
                                                <?php echo formatDuration($video['etkilesim_suresi']); ?> izlendi
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="watch-actions mt-2">
                                        <a href="video.php?id=<?php echo $video['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-play me-1"></i>Devam Et
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
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
            <i class="fas fa-history"></i>
            <h3>İzleme geçmişiniz boş</h3>
            <p>İzlediğiniz videolar burada görünecek.</p>
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-home me-2"></i>Videolara Göz At
            </a>
        </div>
    <?php endif; ?>
</div>

<style>
.watch-history-item {
    background: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.watch-history-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.video-thumbnail {
    position: relative;
    border-radius: 10px;
    overflow: hidden;
    aspect-ratio: 16/9;
}

.video-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.video-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.watch-history-item:hover .video-overlay {
    opacity: 1;
}

.play-btn {
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #667eea;
    font-size: 18px;
    text-decoration: none;
}

.watch-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: rgba(255,255,255,0.3);
}

.progress-bar {
    height: 100%;
    background: #667eea;
    transition: width 0.3s ease;
}

.video-duration {
    position: absolute;
    bottom: 8px;
    right: 8px;
    background: rgba(0,0,0,0.8);
    color: white;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.75rem;
}

.video-title {
    line-height: 1.3;
}

.video-description {
    font-size: 0.9rem;
    line-height: 1.4;
}

.watch-date {
    font-weight: 500;
    color: #495057;
}

.watch-info {
    text-align: right;
}

@media (max-width: 768px) {
    .watch-info {
        text-align: left;
        margin-top: 15px;
    }
}
</style>

<script>
function clearHistory() {
    if (confirm('İzleme geçmişinizi temizlemek istediğinizden emin misiniz? Bu işlem geri alınamaz.')) {
        // AJAX ile geçmişi temizle
        fetch('api/clear-history.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Bir hata oluştu: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Bir hata oluştu.');
        });
    }
}
</script>

<?php include 'includes/footer.php'; ?>