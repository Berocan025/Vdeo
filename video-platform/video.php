<?php
/**
 * DOBİEN Video Platform - Video Oynatıcı
 * Geliştirici: DOBİEN
 * Modern Video Paylaşım Platformu
 * Tüm Hakları Saklıdır © DOBİEN
 */

require_once 'includes/config.php';

// Video ID'si kontrolü
$video_id = $_GET['id'] ?? '';
if (empty($video_id) || !is_numeric($video_id)) {
    header('Location: ' . siteUrl());
    exit;
}

// Video bilgilerini çek
$video_query = "
    SELECT v.*, k.kategori_adi, k.slug as kategori_slug 
    FROM videolar v 
    LEFT JOIN kategoriler k ON v.kategori_id = k.id 
    WHERE v.id = ? AND v.durum = 'aktif'
";
$video_stmt = $pdo->prepare($video_query);
$video_stmt->execute([$video_id]);
$video = $video_stmt->fetch();

if (!$video) {
    header('Location: ' . siteUrl());
    exit;
}

// Erişim kontrolü
$can_access = true;
$membership_required = '';

if ($video['goruntulenme_yetkisi'] != 'herkes') {
    if (!$current_user) {
        $can_access = false;
        $membership_required = $video['goruntulenme_yetkisi'];
    } else {
        $user_level = ['kullanici' => 1, 'vip' => 2, 'premium' => 3];
        $required_level = ['herkes' => 1, 'vip' => 2, 'premium' => 3];
        
        if ($user_level[$current_user['uyelik_tipi']] < $required_level[$video['goruntulenme_yetkisi']]) {
            $can_access = false;
            $membership_required = $video['goruntulenme_yetkisi'];
        }
    }
}

// İzlenme sayısını artır (erişimi olan kullanıcılar için)
if ($can_access) {
    $update_views = $pdo->prepare("UPDATE videolar SET izlenme_sayisi = izlenme_sayisi + 1 WHERE id = ?");
    $update_views->execute([$video_id]);
    
    // İzleme geçmişini kaydet
    if ($current_user) {
        $history_check = $pdo->prepare("SELECT id FROM izleme_gecmisi WHERE kullanici_id = ? AND video_id = ?");
        $history_check->execute([$current_user['id'], $video_id]);
        
        if ($history_check->fetch()) {
            // Güncelle
            $update_history = $pdo->prepare("UPDATE izleme_gecmisi SET izleme_tarihi = NOW() WHERE kullanici_id = ? AND video_id = ?");
            $update_history->execute([$current_user['id'], $video_id]);
        } else {
            // Yeni kayıt
            $insert_history = $pdo->prepare("INSERT INTO izleme_gecmisi (kullanici_id, video_id, izleme_tarihi) VALUES (?, ?, NOW())");
            $insert_history->execute([$current_user['id'], $video_id]);
        }
    }
}

// Benzer videolar
$similar_videos_query = "
    SELECT v.*, k.kategori_adi, k.slug as kategori_slug
    FROM videolar v 
    LEFT JOIN kategoriler k ON v.kategori_id = k.id 
    WHERE v.kategori_id = ? AND v.id != ? AND v.durum = 'aktif'
    ORDER BY v.izlenme_sayisi DESC 
    LIMIT 8
";
$similar_videos = $pdo->prepare($similar_videos_query);
$similar_videos->execute([$video['kategori_id'], $video_id]);
$similar_videos = $similar_videos->fetchAll();

// Beğeni durumu kontrolü
$user_liked = false;
$user_disliked = false;
$is_favorite = false;

if ($current_user) {
    $like_check = $pdo->prepare("SELECT tur FROM video_begeniler WHERE kullanici_id = ? AND video_id = ?");
    $like_check->execute([$current_user['id'], $video_id]);
    $like_status = $like_check->fetch();
    
    if ($like_status) {
        $user_liked = ($like_status['tur'] == 'begeni');
        $user_disliked = ($like_status['tur'] == 'begenme');
    }
    
    $fav_check = $pdo->prepare("SELECT id FROM favoriler WHERE kullanici_id = ? AND video_id = ?");
    $fav_check->execute([$current_user['id'], $video_id]);
    $is_favorite = (bool)$fav_check->fetch();
}

// Sayfa bilgileri
$page_title = $video['baslik'];
$page_description = $video['aciklama'] ? substr(strip_tags($video['aciklama']), 0, 160) : $video['baslik'];
$page_keywords = $video['etiketler'] . ', ' . $video['kategori_adi'] . ', video, DOBİEN';

include 'includes/header.php';
?>

<?php if (!$can_access): ?>
<!-- Üyelik Gerekli Modalı -->
<div class="membership-required-modal" id="membershipModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="lock-icon">
                <i class="fas fa-lock"></i>
            </div>
            <h2>Premium İçerik</h2>
            <p>Bu videoya erişebilmek için <?php echo $membership_required == 'vip' ? 'VIP' : 'Premium'; ?> üyelik gereklidir.</p>
        </div>
        
        <div class="modal-body">
            <div class="membership-benefits">
                <h3><?php echo $membership_required == 'vip' ? 'VIP' : 'Premium'; ?> Üyelik Avantajları:</h3>
                <ul>
                    <?php if ($membership_required == 'vip'): ?>
                    <li><i class="fas fa-check"></i> 1080p HD kalitede video izleme</li>
                    <li><i class="fas fa-check"></i> VIP özel içeriklerine erişim</li>
                    <li><i class="fas fa-check"></i> Öncelikli müşteri desteği</li>
                    <li><i class="fas fa-check"></i> Reklamsız izleme deneyimi</li>
                    <?php else: ?>
                    <li><i class="fas fa-check"></i> 4K Ultra HD kalitede video izleme</li>
                    <li><i class="fas fa-check"></i> Tüm premium içeriklere erişim</li>
                    <li><i class="fas fa-check"></i> İndirme ve çevrimdışı izleme</li>
                    <li><i class="fas fa-check"></i> Erken erişim ve özel etkinlikler</li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div class="modal-actions">
                <a href="<?php echo siteUrl('uyelik-yukselt.php'); ?>" class="btn btn-primary">
                    <i class="fas fa-crown"></i>
                    Üyeliği Yükselt
                </a>
                <a href="<?php echo siteUrl(); ?>" class="btn btn-outline">
                    Ana Sayfaya Dön
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="video-container">
    <?php if ($can_access): ?>
    <!-- Video Player Alanı -->
    <div class="video-player-section">
        <div class="video-player-wrapper">
            <div class="video-player" id="videoPlayer">
                <video 
                    id="mainVideo" 
                    poster="<?php echo $video['kapak_resmi'] ? siteUrl('uploads/thumbnails/' . $video['kapak_resmi']) : siteUrl('assets/images/default-thumbnail.jpg'); ?>"
                    preload="metadata"
                    data-video-id="<?php echo $video['id']; ?>">
                    
                    <!-- Video kalite seçenekleri -->
                    <?php if ($video['video_dosyasi_4k'] && ($current_user && $current_user['uyelik_tipi'] == 'premium')): ?>
                    <source src="<?php echo siteUrl('uploads/videos/' . $video['video_dosyasi_4k']); ?>" type="video/mp4" data-quality="4K" data-res="2160">
                    <?php endif; ?>
                    
                    <?php if ($video['video_dosyasi_1080p'] && ($current_user && ($current_user['uyelik_tipi'] == 'vip' || $current_user['uyelik_tipi'] == 'premium'))): ?>
                    <source src="<?php echo siteUrl('uploads/videos/' . $video['video_dosyasi_1080p']); ?>" type="video/mp4" data-quality="1080p" data-res="1080">
                    <?php endif; ?>
                    
                    <?php if ($video['video_dosyasi_720p']): ?>
                    <source src="<?php echo siteUrl('uploads/videos/' . $video['video_dosyasi_720p']); ?>" type="video/mp4" data-quality="720p" data-res="720">
                    <?php endif; ?>
                    
                    Your browser does not support the video tag.
                </video>
                
                <!-- Custom Video Controls -->
                <div class="video-controls" id="videoControls">
                    <div class="progress-container">
                        <div class="progress-bar">
                            <div class="progress" id="progress"></div>
                            <div class="progress-handle" id="progressHandle"></div>
                        </div>
                        <div class="time-tooltip" id="timeTooltip"></div>
                    </div>
                    
                    <div class="controls-wrapper">
                        <div class="controls-left">
                            <button class="control-btn play-pause" id="playPauseBtn">
                                <i class="fas fa-play"></i>
                            </button>
                            <button class="control-btn volume" id="volumeBtn">
                                <i class="fas fa-volume-up"></i>
                            </button>
                            <div class="volume-slider">
                                <input type="range" id="volumeSlider" min="0" max="100" value="100">
                            </div>
                            <div class="time-display">
                                <span id="currentTime">00:00</span> / <span id="duration">00:00</span>
                            </div>
                        </div>
                        
                        <div class="controls-right">
                            <button class="control-btn quality" id="qualityBtn">
                                <i class="fas fa-cog"></i>
                                <span class="quality-text">720p</span>
                            </button>
                            <button class="control-btn speed" id="speedBtn">
                                <i class="fas fa-tachometer-alt"></i>
                                <span class="speed-text">1x</span>
                            </button>
                            <button class="control-btn pip" id="pipBtn">
                                <i class="fas fa-external-link-alt"></i>
                            </button>
                            <button class="control-btn fullscreen" id="fullscreenBtn">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Quality Selection Menu -->
                <div class="quality-menu" id="qualityMenu">
                    <div class="menu-header">Kalite Seçin</div>
                    <div class="quality-options" id="qualityOptions">
                        <!-- JavaScript ile doldurulacak -->
                    </div>
                </div>
                
                <!-- Speed Selection Menu -->
                <div class="speed-menu" id="speedMenu">
                    <div class="menu-header">Hız Seçin</div>
                    <div class="speed-options">
                        <div class="speed-option" data-speed="0.5">0.5x</div>
                        <div class="speed-option" data-speed="0.75">0.75x</div>
                        <div class="speed-option active" data-speed="1">1x</div>
                        <div class="speed-option" data-speed="1.25">1.25x</div>
                        <div class="speed-option" data-speed="1.5">1.5x</div>
                        <div class="speed-option" data-speed="2">2x</div>
                    </div>
                </div>
                
                <!-- Loading Spinner -->
                <div class="loading-spinner" id="loadingSpinner">
                    <div class="spinner"></div>
                </div>
                
                <!-- Big Play Button -->
                <div class="big-play-button" id="bigPlayButton">
                    <i class="fas fa-play"></i>
                </div>
            </div>
        </div>
        
        <!-- Video Info -->
        <div class="video-info">
            <div class="video-header">
                <h1 class="video-title"><?php echo safeOutput($video['baslik']); ?></h1>
                <div class="video-stats">
                    <div class="stats-left">
                        <span class="views">
                            <i class="fas fa-eye"></i>
                            <?php echo number_format($video['izlenme_sayisi']); ?> izlenme
                        </span>
                        <span class="date">
                            <i class="fas fa-calendar"></i>
                            <?php echo formatDate($video['ekleme_tarihi'], 'd.m.Y'); ?>
                        </span>
                        <?php if ($video['kategori_adi']): ?>
                        <a href="<?php echo siteUrl('kategori/' . $video['kategori_slug']); ?>" class="category">
                            <i class="fas fa-tag"></i>
                            <?php echo safeOutput($video['kategori_adi']); ?>
                        </a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="stats-right">
                        <div class="video-actions">
                            <button class="action-btn like-btn <?php echo $user_liked ? 'active' : ''; ?>" onclick="toggleLike('like')">
                                <i class="fas fa-thumbs-up"></i>
                                <span id="likeCount"><?php echo number_format($video['begeni_sayisi']); ?></span>
                            </button>
                            <button class="action-btn dislike-btn <?php echo $user_disliked ? 'active' : ''; ?>" onclick="toggleLike('dislike')">
                                <i class="fas fa-thumbs-down"></i>
                                <span id="dislikeCount"><?php echo number_format($video['begenme_sayisi']); ?></span>
                            </button>
                            <button class="action-btn favorite-btn <?php echo $is_favorite ? 'active' : ''; ?>" onclick="toggleFavorite()">
                                <i class="fas fa-heart"></i>
                                Favorilere Ekle
                            </button>
                            <button class="action-btn share-btn" onclick="shareVideo()">
                                <i class="fas fa-share"></i>
                                Paylaş
                            </button>
                            <button class="action-btn report-btn" onclick="reportVideo()">
                                <i class="fas fa-flag"></i>
                                Şikayet Et
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if ($video['aciklama']): ?>
            <div class="video-description">
                <div class="description-content" id="descriptionContent">
                    <?php echo nl2br(safeOutput($video['aciklama'])); ?>
                </div>
                <button class="show-more-btn" id="showMoreBtn">Daha fazla göster</button>
            </div>
            <?php endif; ?>
            
            <?php if ($video['etiketler']): ?>
            <div class="video-tags">
                <?php 
                $tags = explode(',', $video['etiketler']);
                foreach ($tags as $tag): 
                    $tag = trim($tag);
                    if ($tag):
                ?>
                <a href="<?php echo siteUrl('arama.php?q=' . urlencode($tag)); ?>" class="tag">
                    #<?php echo safeOutput($tag); ?>
                </a>
                <?php 
                    endif;
                endforeach; 
                ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="video-sidebar">
        <!-- Reklam Alanı -->
        <div class="ad-container">
            <div class="ad-content">
                <div class="ad-label">Reklam</div>
                <div class="ad-placeholder">
                    <i class="fas fa-ad"></i>
                    <p>Reklam Alanı<br><small>300x250</small></p>
                </div>
            </div>
        </div>
        
        <!-- Benzer Videolar -->
        <?php if (!empty($similar_videos)): ?>
        <div class="similar-videos">
            <h3 class="section-title">
                <i class="fas fa-video"></i>
                Benzer Videolar
            </h3>
            
            <div class="similar-videos-list">
                <?php foreach ($similar_videos as $similar): ?>
                <a href="<?php echo siteUrl('video.php?id=' . $similar['id']); ?>" class="similar-video-item">
                    <div class="similar-thumbnail">
                        <img src="<?php echo $similar['kapak_resmi'] ? siteUrl('uploads/thumbnails/' . $similar['kapak_resmi']) : siteUrl('assets/images/default-thumbnail.jpg'); ?>" 
                             alt="<?php echo safeOutput($similar['baslik']); ?>">
                        <div class="duration"><?php echo $similar['sure'] ? formatDuration($similar['sure']) : '00:00'; ?></div>
                        <div class="quality-badge">
                            <?php
                            switch($similar['goruntulenme_yetkisi']) {
                                case 'premium': echo '4K'; break;
                                case 'vip': echo '1080p'; break;
                                default: echo '720p';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="similar-info">
                        <h4 class="similar-title"><?php echo safeOutput($similar['baslik']); ?></h4>
                        <div class="similar-meta">
                            <span class="views"><?php echo number_format($similar['izlenme_sayisi']); ?> izlenme</span>
                            <span class="date"><?php echo formatDate($similar['ekleme_tarihi'], 'd.m.Y'); ?></span>
                        </div>
                        <?php if ($similar['kategori_adi']): ?>
                        <span class="similar-category"><?php echo safeOutput($similar['kategori_adi']); ?></span>
                        <?php endif; ?>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Alt Reklam Alanı -->
        <div class="ad-container">
            <div class="ad-content">
                <div class="ad-label">Reklam</div>
                <div class="ad-placeholder">
                    <i class="fas fa-ad"></i>
                    <p>Reklam Alanı<br><small>300x600</small></p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Paylaşım Modalı -->
<div class="share-modal" id="shareModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Videoyu Paylaş</h3>
            <button class="close-btn" onclick="closeShareModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="share-url">
                <label>Video Linki:</label>
                <div class="url-container">
                    <input type="text" id="shareUrl" value="<?php echo siteUrl('video.php?id=' . $video_id); ?>" readonly>
                    <button onclick="copyUrl()" class="copy-btn">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
            
            <div class="social-share">
                <h4>Sosyal Medyada Paylaş:</h4>
                <div class="social-buttons">
                    <a href="#" onclick="shareOnFacebook()" class="social-btn facebook">
                        <i class="fab fa-facebook"></i>
                        Facebook
                    </a>
                    <a href="#" onclick="shareOnTwitter()" class="social-btn twitter">
                        <i class="fab fa-twitter"></i>
                        Twitter
                    </a>
                    <a href="#" onclick="shareOnWhatsApp()" class="social-btn whatsapp">
                        <i class="fab fa-whatsapp"></i>
                        WhatsApp
                    </a>
                    <a href="#" onclick="shareOnTelegram()" class="social-btn telegram">
                        <i class="fab fa-telegram"></i>
                        Telegram
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DOBİEN Developer Signature -->
<div class="dobien-signature">
    <i class="fas fa-code"></i> DOBİEN
</div>

<?php include 'includes/footer.php'; ?>

<link rel="stylesheet" href="<?php echo siteUrl('assets/css/video-player.css'); ?>">
<script src="<?php echo siteUrl('assets/js/video-player.js'); ?>"></script>

<script>
/**
 * DOBİEN Video Platform - Video Oynatıcı JavaScript
 * Geliştirici: DOBİEN
 */

// Video oynatıcı başlatma
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($can_access): ?>
    initVideoPlayer();
    setupVideoEvents();
    loadQualityOptions();
    <?php else: ?>
    document.getElementById('membershipModal').style.display = 'flex';
    <?php endif; ?>
});

// Beğeni/Beğenmeme işlemi
function toggleLike(type) {
    <?php if (!$current_user): ?>
    alert('Bu işlem için giriş yapmalısınız.');
    window.location.href = '<?php echo siteUrl('giris.php?redirect=' . urlencode($_SERVER['REQUEST_URI'])); ?>';
    return;
    <?php else: ?>
    
    fetch('<?php echo siteUrl('api/toggle-like.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            video_id: <?php echo $video_id; ?>,
            type: type
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('likeCount').textContent = data.like_count;
            document.getElementById('dislikeCount').textContent = data.dislike_count;
            
            // Button durumlarını güncelle
            document.querySelector('.like-btn').classList.toggle('active', data.user_liked);
            document.querySelector('.dislike-btn').classList.toggle('active', data.user_disliked);
        } else {
            alert(data.message || 'Bir hata oluştu.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Bir hata oluştu.');
    });
    <?php endif; ?>
}

// Favorilere ekleme/çıkarma
function toggleFavorite() {
    <?php if (!$current_user): ?>
    alert('Bu işlem için giriş yapmalısınız.');
    window.location.href = '<?php echo siteUrl('giris.php?redirect=' . urlencode($_SERVER['REQUEST_URI'])); ?>';
    return;
    <?php else: ?>
    
    fetch('<?php echo siteUrl('api/toggle-favorite.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            video_id: <?php echo $video_id; ?>
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const favoriteBtn = document.querySelector('.favorite-btn');
            favoriteBtn.classList.toggle('active', data.is_favorite);
            favoriteBtn.innerHTML = `<i class="fas fa-heart"></i> ${data.is_favorite ? 'Favorilerden Çıkar' : 'Favorilere Ekle'}`;
        } else {
            alert(data.message || 'Bir hata oluştu.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Bir hata oluştu.');
    });
    <?php endif; ?>
}

// Video paylaşımı
function shareVideo() {
    document.getElementById('shareModal').style.display = 'flex';
}

function closeShareModal() {
    document.getElementById('shareModal').style.display = 'none';
}

function copyUrl() {
    const urlInput = document.getElementById('shareUrl');
    urlInput.select();
    document.execCommand('copy');
    
    const copyBtn = document.querySelector('.copy-btn');
    copyBtn.innerHTML = '<i class="fas fa-check"></i>';
    setTimeout(() => {
        copyBtn.innerHTML = '<i class="fas fa-copy"></i>';
    }, 2000);
}

function shareOnFacebook() {
    const url = encodeURIComponent(document.getElementById('shareUrl').value);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
}

function shareOnTwitter() {
    const url = encodeURIComponent(document.getElementById('shareUrl').value);
    const text = encodeURIComponent('<?php echo safeOutput($video['baslik']); ?>');
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank');
}

function shareOnWhatsApp() {
    const url = encodeURIComponent(document.getElementById('shareUrl').value);
    const text = encodeURIComponent('<?php echo safeOutput($video['baslik']); ?> - ' + url);
    window.open(`https://wa.me/?text=${text}`, '_blank');
}

function shareOnTelegram() {
    const url = encodeURIComponent(document.getElementById('shareUrl').value);
    const text = encodeURIComponent('<?php echo safeOutput($video['baslik']); ?>');
    window.open(`https://t.me/share/url?url=${url}&text=${text}`, '_blank');
}

// Video şikayeti
function reportVideo() {
    <?php if (!$current_user): ?>
    alert('Bu işlem için giriş yapmalısınız.');
    window.location.href = '<?php echo siteUrl('giris.php?redirect=' . urlencode($_SERVER['REQUEST_URI'])); ?>';
    return;
    <?php else: ?>
    
    const reason = prompt('Şikayet sebebinizi belirtiniz:');
    if (reason && reason.trim()) {
        fetch('<?php echo siteUrl('api/report-video.php'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                video_id: <?php echo $video_id; ?>,
                reason: reason.trim()
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Şikayetiniz başarıyla gönderildi. İnceleme yapılacaktır.');
            } else {
                alert(data.message || 'Bir hata oluştu.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Bir hata oluştu.');
        });
    }
    <?php endif; ?>
}

// Açıklama göster/gizle
document.addEventListener('DOMContentLoaded', function() {
    const showMoreBtn = document.getElementById('showMoreBtn');
    const descriptionContent = document.getElementById('descriptionContent');
    
    if (showMoreBtn && descriptionContent) {
        const maxHeight = 100; // px
        
        if (descriptionContent.scrollHeight > maxHeight) {
            descriptionContent.style.maxHeight = maxHeight + 'px';
            descriptionContent.style.overflow = 'hidden';
            
            showMoreBtn.addEventListener('click', function() {
                if (descriptionContent.style.maxHeight === maxHeight + 'px') {
                    descriptionContent.style.maxHeight = 'none';
                    this.textContent = 'Daha az göster';
                } else {
                    descriptionContent.style.maxHeight = maxHeight + 'px';
                    this.textContent = 'Daha fazla göster';
                }
            });
        } else {
            showMoreBtn.style.display = 'none';
        }
    }
});
</script>