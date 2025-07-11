<?php
/**
 * DOBİEN Video Platform - Header Include
 * Geliştirici: DOBİEN
 */

if (!defined('DB_HOST')) {
    require_once 'includes/config.php';
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? safeOutput($page_title) . ' - ' : ''; ?><?php echo safeOutput($site_settings['site_adi']); ?></title>
    <meta name="description" content="<?php echo isset($page_description) ? safeOutput($page_description) : safeOutput($site_settings['site_aciklama']); ?>">
    <meta name="author" content="DOBİEN">
    
    <link rel="stylesheet" href="<?php echo siteUrl('assets/css/style.css?v=' . time()); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>

<header class="header">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <a href="<?php echo siteUrl(); ?>">
                    <h1><span class="text-gradient">DOBİEN</span></h1>
                    <span class="logo-subtitle">Video Platform</span>
                </a>
            </div>

            <nav class="nav-menu">
                <ul>
                    <li><a href="<?php echo siteUrl(); ?>"><i class="fas fa-home"></i> Ana Sayfa</a></li>
                    <li><a href="<?php echo siteUrl('kategoriler.php'); ?>"><i class="fas fa-th-large"></i> Kategoriler</a></li>
                    <li><a href="<?php echo siteUrl('populer.php'); ?>"><i class="fas fa-fire"></i> Popüler</a></li>
                    <li><a href="<?php echo siteUrl('yeni-videolar.php'); ?>"><i class="fas fa-star"></i> Yeni</a></li>
                </ul>
            </nav>

            <div class="search-container">
                <form class="search-form" action="<?php echo siteUrl('arama.php'); ?>" method="GET">
                    <input type="text" name="q" placeholder="Video ara...">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>

            <div class="user-menu">
                <?php if ($current_user): ?>
                    <div class="user-dropdown">
                        <button class="user-toggle">
                            <img src="<?php echo $current_user['avatar'] ? siteUrl('uploads/avatars/' . $current_user['avatar']) : siteUrl('assets/images/default-avatar.png'); ?>" alt="Avatar" class="user-avatar">
                            <span class="username"><?php echo safeOutput($current_user['ad'] . ' ' . $current_user['soyad']); ?></span>
                            <span class="membership-badge <?php echo $current_user['uyelik_tipi']; ?>">
                                <?php 
                                switch($current_user['uyelik_tipi']) {
                                    case 'premium': echo 'PREMIUM'; break;
                                    case 'vip': echo 'VIP'; break;
                                    default: echo 'ÜYE'; break;
                                }
                                ?>
                            </span>
                        </button>
                        <div class="dropdown-menu">
                            <a href="<?php echo siteUrl('profil.php'); ?>"><i class="fas fa-user"></i> Profilim</a>
                            <a href="<?php echo siteUrl('begendiklerim.php'); ?>"><i class="fas fa-thumbs-up"></i> Beğendiklerim</a>
                            <a href="<?php echo siteUrl('favoriler.php'); ?>"><i class="fas fa-heart"></i> Favorilerim</a>
                            <a href="<?php echo siteUrl('izleme-gecmisi.php'); ?>"><i class="fas fa-history"></i> İzleme Geçmişi</a>
                            <a href="<?php echo siteUrl('izleme-listesi.php'); ?>"><i class="fas fa-bookmark"></i> İzleme Listesi</a>
                            <div class="divider"></div>
                            <?php if ($current_user['uyelik_tipi'] == 'kullanici'): ?>
                                <a href="<?php echo siteUrl('uyelik-yukselt.php'); ?>" class="upgrade-link">
                                    <i class="fas fa-crown"></i> Üyeliği Yükselt
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo siteUrl('hesap-ayarlari.php'); ?>"><i class="fas fa-cog"></i> Hesap Ayarları</a>
                            <a href="<?php echo siteUrl('logout.php'); ?>" class="logout"><i class="fas fa-sign-out-alt"></i> Çıkış</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="auth-buttons">
                        <a href="<?php echo siteUrl('giris.php'); ?>" class="btn btn-outline">Giriş Yap</a>
                        <a href="<?php echo siteUrl('kayit.php'); ?>" class="btn btn-primary">Kayıt Ol</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<main class="main-content">