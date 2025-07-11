<?php
/**
 * DOBİEN Video Platform - Beğendiklerim
 * Geliştirici: DOBİEN
 * Modern Video Paylaşım Platformu
 */

require_once 'includes/config.php';

// Kullanıcı giriş kontrolü
if (!$current_user) {
    header('Location: giris.php');
    exit;
}

$page_title = "Beğendiklerim - " . $site_settings['site_adi'];

// Sayfalama
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

// Beğenilen videoları çek
try {
    // Toplam sayı
    $count_stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM kullanici_video_etkilesimleri kve 
        INNER JOIN videolar v ON kve.video_id = v.id 
        WHERE kve.kullanici_id = ? AND kve.etkilesim_tipi = 'begeni' AND v.durum = 'aktif'
    ");
    $count_stmt->execute([$current_user['id']]);
    $total_videos = $count_stmt->fetchColumn();
    $total_pages = ceil($total_videos / $per_page);
    
    // Videolar
    $videos_stmt = $pdo->prepare("
        SELECT v.*, k.kategori_adi, kve.olusturma_tarihi as begeni_tarihi
        FROM kullanici_video_etkilesimleri kve 
        INNER JOIN videolar v ON kve.video_id = v.id 
        LEFT JOIN kategoriler k ON v.kategori_id = k.id
        WHERE kve.kullanici_id = ? AND kve.etkilesim_tipi = 'begeni' AND v.durum = 'aktif'
        ORDER BY kve.olusturma_tarihi DESC 
        LIMIT $per_page OFFSET $offset
    ");
    $videos_stmt->execute([$current_user['id']]);
    $liked_videos = $videos_stmt->fetchAll();
    
} catch (PDOException $e) {
    $liked_videos = [];
    $total_videos = 0;
    $total_pages = 0;
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    
    <style>
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 40px;
        }
        
        .video-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .video-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            position: relative;
        }
        
        .video-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
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
        
        .video-card:hover .video-overlay {
            opacity: 1;
        }
        
        .play-btn {
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #667eea;
            font-size: 24px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .play-btn:hover {
            background: white;
            transform: scale(1.1);
            color: #667eea;
        }
        
        .video-duration {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
        }
        
        .like-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            padding: 6px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .video-info {
            padding: 20px;
        }
        
        .video-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .video-meta {
            display: flex;
            align-items: center;
            gap: 15px;
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        
        .video-meta i {
            color: #667eea;
        }
        
        .video-category {
            background: #f8f9fa;
            color: #495057;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            text-decoration: none;
        }
        
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #666;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 20px;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 40px;
        }
        
        .pagination a, .pagination span {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
        }
        
        .pagination a:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        
        .pagination .current {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="mb-2">
                        <i class="fas fa-thumbs-up me-3"></i>Beğendiklerim
                    </h1>
                    <p class="mb-0">Beğendiğiniz <?php echo number_format($total_videos); ?> video</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="profil.php" class="btn btn-outline-light">
                        <i class="fas fa-user me-2"></i>Profilime Dön
                    </a>
                </div>
            </div>
        </div>
    </section>
    
    <div class="container">
        <?php if (!empty($liked_videos)): ?>
            <div class="video-grid">
                <?php foreach ($liked_videos as $video): ?>
                    <div class="video-card">
                        <div class="video-thumbnail">
                            <img src="<?php echo htmlspecialchars($video['kapak_resmi'] ?: 'assets/images/default-thumbnail.jpg'); ?>" 
                                 alt="<?php echo htmlspecialchars($video['baslik']); ?>">
                            
                            <div class="video-overlay">
                                <a href="video.php?id=<?php echo $video['id']; ?>" class="play-btn">
                                    <i class="fas fa-play"></i>
                                </a>
                            </div>
                            
                            <div class="like-badge">
                                <i class="fas fa-heart"></i>
                                Beğenildi
                            </div>
                            
                            <?php if ($video['sure']): ?>
                                <div class="video-duration">
                                    <?php echo formatDuration($video['sure']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="video-info">
                            <h3 class="video-title">
                                <a href="video.php?id=<?php echo $video['id']; ?>" class="text-decoration-none text-dark">
                                    <?php echo htmlspecialchars($video['baslik']); ?>
                                </a>
                            </h3>
                            
                            <div class="video-meta">
                                <span>
                                    <i class="fas fa-heart"></i>
                                    <?php echo formatDate($video['begeni_tarihi'], 'd.m.Y'); ?>
                                </span>
                                <span>
                                    <i class="fas fa-eye"></i>
                                    <?php echo number_format($video['izlenme_sayisi']); ?>
                                </span>
                            </div>
                            
                            <?php if ($video['kategori_adi']): ?>
                                <a href="kategori.php?slug=<?php echo urlencode($video['kategori_adi']); ?>" class="video-category">
                                    <?php echo htmlspecialchars($video['kategori_adi']); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>">‹ Önceki</a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="current"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>">Sonraki ›</a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-thumbs-up"></i>
                <h3>Henüz beğendiğiniz video yok</h3>
                <p>Beğendiğiniz videolar burada görünecek.</p>
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-home me-2"></i>Ana Sayfaya Git
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>