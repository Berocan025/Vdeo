<?php
/**
 * DOBİEN Video Platform - Kullanıcı Profili
 * Geliştirici: DOBİEN
 * Modern Video Paylaşım Platformu
 */

require_once 'includes/config.php';

// Kullanıcı giriş kontrolü
if (!$current_user) {
    header('Location: giris.php');
    exit;
}

$page_title = "Profilim - " . $site_settings['site_adi'];

// Profil güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_profile') {
        $ad = trim($_POST['ad'] ?? '');
        $soyad = trim($_POST['soyad'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefon = trim($_POST['telefon'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        
        try {
            // E-posta kontrolü (başka kullanıcıda var mı)
            $stmt = $pdo->prepare("SELECT id FROM kullanicilar WHERE email = ? AND id != ?");
            $stmt->execute([$email, $current_user['id']]);
            if ($stmt->fetch()) {
                throw new Exception('Bu e-posta adresi başka bir kullanıcı tarafından kullanılıyor.');
            }
            
            // Profil güncelle
            $stmt = $pdo->prepare("UPDATE kullanicilar SET ad = ?, soyad = ?, email = ?, telefon = ?, bio = ? WHERE id = ?");
            $stmt->execute([$ad, $soyad, $email, $telefon, $bio, $current_user['id']]);
            
            $success_message = 'Profil bilgileriniz başarıyla güncellendi.';
            
            // Mevcut kullanıcı bilgilerini güncelle
            $current_user['ad'] = $ad;
            $current_user['soyad'] = $soyad;
            $current_user['email'] = $email;
            $current_user['telefon'] = $telefon;
            $current_user['bio'] = $bio;
            
        } catch (Exception $e) {
            $error_message = $e->getMessage();
        }
    }
}

// Kullanıcı istatistikleri
try {
    $stats = [
        'toplam_izleme' => $pdo->prepare("SELECT SUM(etkilesim_suresi) FROM kullanici_video_etkilesimleri WHERE kullanici_id = ? AND etkilesim_tipi = 'izleme'"),
        'toplam_begeni' => $pdo->prepare("SELECT COUNT(*) FROM kullanici_video_etkilesimleri WHERE kullanici_id = ? AND etkilesim_tipi = 'begeni'"),
        'toplam_favori' => $pdo->prepare("SELECT COUNT(*) FROM kullanici_video_etkilesimleri WHERE kullanici_id = ? AND etkilesim_tipi = 'favori'"),
        'son_izlenenler' => $pdo->prepare("SELECT v.*, kve.olusturma_tarihi as izleme_tarihi 
                                          FROM videolar v 
                                          INNER JOIN kullanici_video_etkilesimleri kve ON v.id = kve.video_id 
                                          WHERE kve.kullanici_id = ? AND kve.etkilesim_tipi = 'izleme' 
                                          ORDER BY kve.olusturma_tarihi DESC LIMIT 5")
    ];
    
    foreach ($stats as $key => $stmt) {
        if ($key === 'son_izlenenler') {
            $stmt->execute([$current_user['id']]);
            $stats[$key] = $stmt->fetchAll();
        } else {
            $stmt->execute([$current_user['id']]);
            $stats[$key] = $stmt->fetchColumn() ?: 0;
        }
    }
} catch (PDOException $e) {
    $stats = ['toplam_izleme' => 0, 'toplam_begeni' => 0, 'toplam_favori' => 0, 'son_izlenenler' => []];
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
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 40px;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid white;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }
        
        .membership-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9rem;
        }
        
        .membership-kullanici { background: #6c757d; color: white; }
        .membership-vip { background: #ffc107; color: #212529; }
        .membership-premium { background: #dc3545; color: white; }
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
        }
        
        .form-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .recent-videos {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .video-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        
        .video-item:last-child {
            border-bottom: none;
        }
        
        .video-thumbnail {
            width: 80px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 15px;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <!-- Profile Header -->
    <section class="profile-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-3 text-center">
                    <img src="<?php echo $current_user['profil_resmi'] ? htmlspecialchars($current_user['profil_resmi']) : 'assets/images/default-avatar.png'; ?>" 
                         alt="Profil Resmi" class="profile-avatar">
                </div>
                <div class="col-md-9">
                    <h1 class="mb-2"><?php echo htmlspecialchars($current_user['ad'] . ' ' . $current_user['soyad']); ?></h1>
                    <p class="mb-3"><?php echo htmlspecialchars($current_user['email']); ?></p>
                    
                    <span class="membership-badge membership-<?php echo $current_user['uyelik_tipi']; ?>">
                        <?php 
                        switch($current_user['uyelik_tipi']) {
                            case 'premium': echo '<i class="fas fa-gem"></i> PREMIUM ÜYE'; break;
                            case 'vip': echo '<i class="fas fa-crown"></i> VIP ÜYE'; break;
                            default: echo '<i class="fas fa-user"></i> NORMAL ÜYE'; break;
                        }
                        ?>
                    </span>
                    
                    <?php if ($current_user['bio']): ?>
                        <p class="mt-3 mb-0"><?php echo htmlspecialchars($current_user['bio']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    
    <div class="container">
        <div class="row">
            <!-- Sol Kolon - İstatistikler -->
            <div class="col-md-4">
                <h3 class="mb-4">İstatistiklerim</h3>
                
                <div class="stats-card">
                    <div class="stats-number"><?php echo number_format($stats['toplam_izleme'] / 60); ?></div>
                    <div class="text-muted">Dakika İzleme</div>
                </div>
                
                <div class="stats-card">
                    <div class="stats-number"><?php echo number_format($stats['toplam_begeni']); ?></div>
                    <div class="text-muted">Beğenilen Video</div>
                </div>
                
                <div class="stats-card">
                    <div class="stats-number"><?php echo number_format($stats['toplam_favori']); ?></div>
                    <div class="text-muted">Favori Video</div>
                </div>
                
                <div class="stats-card">
                    <div class="stats-number"><?php echo date('Y') - date('Y', strtotime($current_user['kayit_tarihi'])); ?></div>
                    <div class="text-muted">Yıl Üye</div>
                </div>
            </div>
            
            <!-- Sağ Kolon - Profil Düzenleme -->
            <div class="col-md-8">
                <h3 class="mb-4">Profil Bilgileri</h3>
                
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <div class="form-container">
                    <form method="POST">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="ad" class="form-label">Ad *</label>
                                <input type="text" class="form-control" id="ad" name="ad" 
                                       value="<?php echo htmlspecialchars($current_user['ad']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="soyad" class="form-label">Soyad *</label>
                                <input type="text" class="form-control" id="soyad" name="soyad" 
                                       value="<?php echo htmlspecialchars($current_user['soyad']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">E-posta *</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($current_user['email']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="telefon" class="form-label">Telefon</label>
                            <input type="tel" class="form-control" id="telefon" name="telefon" 
                                   value="<?php echo htmlspecialchars($current_user['telefon'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="bio" class="form-label">Hakkımda</label>
                            <textarea class="form-control" id="bio" name="bio" rows="4" 
                                      placeholder="Kendiniz hakkında kısa bir açıklama yazın..."><?php echo htmlspecialchars($current_user['bio'] ?? ''); ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Bilgileri Güncelle
                        </button>
                    </form>
                </div>
                
                <!-- Son İzlenen Videolar -->
                <?php if (!empty($stats['son_izlenenler'])): ?>
                    <div class="recent-videos mt-5">
                        <h4 class="mb-4">Son İzlenen Videolar</h4>
                        
                        <?php foreach ($stats['son_izlenenler'] as $video): ?>
                            <div class="video-item">
                                <img src="<?php echo htmlspecialchars($video['kapak_resmi'] ?: 'assets/images/default-thumbnail.jpg'); ?>" 
                                     alt="<?php echo htmlspecialchars($video['baslik']); ?>" class="video-thumbnail">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="video.php?id=<?php echo $video['id']; ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($video['baslik']); ?>
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        <?php echo formatDate($video['izleme_tarihi'], 'd.m.Y H:i'); ?>
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>