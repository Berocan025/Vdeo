<?php
/**
 * DOBİEN Video Platform - Admin Panel Ana Sayfa
 * Geliştirici: DOBİEN
 */

define('ADMIN_AREA', true);
session_start();

require_once '../includes/config.php';

$page_title = "Admin Panel";

// İstatistikleri al
$stats = [];

// Toplam kullanıcı sayısı
$user_query = "SELECT COUNT(*) as total FROM kullanicilar";
$stats['total_users'] = $pdo->query($user_query)->fetch()['total'];

// Aktif kullanıcı sayısı  
$active_user_query = "SELECT COUNT(*) as total FROM kullanicilar WHERE durum = 'aktif'";
$stats['active_users'] = $pdo->query($active_user_query)->fetch()['total'];

// Toplam video sayısı
$video_query = "SELECT COUNT(*) as total FROM videolar";
$stats['total_videos'] = $pdo->query($video_query)->fetch()['total'];

// Aktif video sayısı
$active_video_query = "SELECT COUNT(*) as total FROM videolar WHERE durum = 'aktif'";
$stats['active_videos'] = $pdo->query($active_video_query)->fetch()['total'];

// VIP kullanıcı sayısı
$vip_query = "SELECT COUNT(*) as total FROM kullanicilar WHERE uyelik_tipi = 'vip'";
$stats['vip_users'] = $pdo->query($vip_query)->fetch()['total'];

// Premium kullanıcı sayısı
$premium_query = "SELECT COUNT(*) as total FROM kullanicilar WHERE uyelik_tipi = 'premium'";
$stats['premium_users'] = $pdo->query($premium_query)->fetch()['total'];

// Toplam kategori sayısı
$category_query = "SELECT COUNT(*) as total FROM kategoriler";
$stats['total_categories'] = $pdo->query($category_query)->fetch()['total'];

// Toplam izlenme sayısı
$view_query = "SELECT SUM(izlenme_sayisi) as total FROM videolar";
$result = $pdo->query($view_query)->fetch();
$stats['total_views'] = $result['total'] ?? 0;

// Son eklenen kullanıcılar (5 kişi)
$recent_users_query = "SELECT * FROM kullanicilar ORDER BY kayit_tarihi DESC LIMIT 5";
$recent_users = $pdo->query($recent_users_query)->fetchAll();

// Son eklenen videolar (5 video)
$recent_videos_query = "SELECT v.*, k.kategori_adi FROM videolar v LEFT JOIN kategoriler k ON v.kategori_id = k.id ORDER BY v.ekleme_tarihi DESC LIMIT 5";
$recent_videos = $pdo->query($recent_videos_query)->fetchAll();

// Sistem bilgileri
$system_info = [
    'php_version' => phpversion(),
    'mysql_version' => $pdo->query("SELECT VERSION() as version")->fetch()['version'],
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Bilinmiyor',
    'memory_limit' => ini_get('memory_limit'),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size')
];

include 'includes/header.php';
?>

<!-- Ana Sayfa İçeriği -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-tachometer-alt"></i> Admin Panel
    </h1>
    <div class="text-muted">
        <i class="fas fa-calendar"></i> <?php echo date('d.m.Y H:i'); ?>
    </div>
</div>

<!-- Hoş Geldin Mesajı -->
<div class="welcome-section mb-4">
    <div class="card border-left-primary shadow">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-8">
                    <h4 class="text-primary mb-3">
                        <i class="fas fa-user-shield"></i> Hoş Geldiniz, <?php echo htmlspecialchars($admin['ad'] . ' ' . $admin['soyad']); ?>!
                    </h4>
                    <p class="text-muted mb-3">DOBİEN Video Platform admin paneline hoş geldiniz. Burada tüm sistem işlemlerini yönetebilirsiniz.</p>
                    <div class="d-flex gap-2">
                        <a href="videolar.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-video"></i> Video Yönetimi
                        </a>
                        <a href="kullanicilar.php" class="btn btn-success btn-sm">
                            <i class="fas fa-users"></i> Kullanıcı Yönetimi
                        </a>
                        <a href="slider.php" class="btn btn-info btn-sm">
                            <i class="fas fa-images"></i> Slider Yönetimi
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <i class="fas fa-crown fa-5x text-warning opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- İstatistik Kartları -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Toplam Kullanıcı
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['total_users']); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Toplam Video
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['total_videos']); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-video fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Premium Üyeler</div>
                        <div class="row no-gutters align-items-center">
                            <div class="col-auto">
                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo number_format($stats['premium_users']); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-crown fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Toplam İzlenme
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['total_views']); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-eye fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- İçerik Satırı -->
<div class="row">
    <!-- Son Kullanıcılar -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Son Kayıt Olan Kullanıcılar</h6>
                <a href="kullanicilar.php" class="btn btn-sm btn-primary">Tümünü Gör</a>
            </div>
            <div class="card-body">
                <?php if (empty($recent_users)): ?>
                    <p class="text-muted text-center">Henüz kullanıcı bulunmuyor.</p>
                <?php else: ?>
                    <?php foreach ($recent_users as $user): ?>
                    <div class="d-flex align-items-center py-2 border-bottom">
                        <div class="mr-3">
                            <div class="icon-circle bg-primary">
                                <i class="fas fa-user text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="small text-gray-500"><?php echo date('d.m.Y', strtotime($user['kayit_tarihi'])); ?></div>
                            <div class="font-weight-bold"><?php echo htmlspecialchars($user['ad'] . ' ' . $user['soyad']); ?></div>
                            <div class="small text-muted"><?php echo htmlspecialchars($user['email']); ?></div>
                        </div>
                        <div>
                            <span class="badge badge-<?php 
                                echo $user['uyelik_tipi'] == 'premium' ? 'primary' : 
                                    ($user['uyelik_tipi'] == 'vip' ? 'warning' : 'secondary'); 
                            ?>">
                                <?php echo ucfirst($user['uyelik_tipi']); ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Son Videolar -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Son Eklenen Videolar</h6>
                <a href="videolar.php" class="btn btn-sm btn-primary">Tümünü Gör</a>
            </div>
            <div class="card-body">
                <?php if (empty($recent_videos)): ?>
                    <p class="text-muted text-center">Henüz video bulunmuyor.</p>
                <?php else: ?>
                    <?php foreach ($recent_videos as $video): ?>
                    <div class="d-flex align-items-center py-2 border-bottom">
                        <div class="mr-3">
                            <div class="icon-circle bg-success">
                                <i class="fas fa-video text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="small text-gray-500"><?php echo date('d.m.Y', strtotime($video['ekleme_tarihi'])); ?></div>
                            <div class="font-weight-bold"><?php echo htmlspecialchars($video['baslik']); ?></div>
                            <div class="small text-muted">
                                <?php echo $video['kategori_adi'] ? htmlspecialchars($video['kategori_adi']) : 'Kategori Yok'; ?>
                                | <?php echo number_format($video['izlenme_sayisi']); ?> izlenme
                            </div>
                        </div>
                        <div>
                            <span class="badge badge-<?php echo $video['durum'] == 'aktif' ? 'success' : 'warning'; ?>">
                                <?php echo ucfirst($video['durum']); ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Sistem Bilgileri -->
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Hızlı İstatistikler</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <div class="p-3">
                            <h4 class="text-success"><?php echo $stats['active_users']; ?></h4>
                            <p class="small text-muted">Aktif Kullanıcı</p>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="p-3">
                            <h4 class="text-info"><?php echo $stats['vip_users']; ?></h4>
                            <p class="small text-muted">VIP Üye</p>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="p-3">
                            <h4 class="text-warning"><?php echo $stats['total_categories']; ?></h4>
                            <p class="small text-muted">Kategori</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Sistem Bilgileri</h6>
            </div>
            <div class="card-body">
                <div class="small">
                    <div class="d-flex justify-content-between mb-2">
                        <span>PHP Versiyon:</span>
                        <strong><?php echo $system_info['php_version']; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>MySQL Versiyon:</span>
                        <strong><?php echo explode('-', $system_info['mysql_version'])[0]; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Memory Limit:</span>
                        <strong><?php echo $system_info['memory_limit']; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Upload Limit:</span>
                        <strong><?php echo $system_info['upload_max_filesize']; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Post Max Size:</span>
                        <strong><?php echo $system_info['post_max_size']; ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-circle {
    height: 2.5rem;
    width: 2.5rem;
    border-radius: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.welcome-section .card {
    border-radius: 0.75rem;
}

.border-bottom:last-child {
    border-bottom: none !important;
}
</style>

<?php include 'includes/footer.php'; ?>