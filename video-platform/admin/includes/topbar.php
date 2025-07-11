<?php
/**
 * DOBİEN Video Platform - Admin Panel Topbar
 * Geliştirici: DOBİEN
 * Tüm Hakları Saklıdır © DOBİEN
 */

// Admin bilgilerini al (eğer henüz yüklenmemişse)
if (!isset($admin_user)) {
    // Admin bilgilerini güvenli şekilde al
    try {
        // Önce yeni tablo yapısını dene (admin_kullanicilar)
        $admin_query = "SELECT * FROM admin_kullanicilar WHERE id = ?";
        $admin_stmt = $pdo->prepare($admin_query);
        $admin_stmt->execute([$_SESSION['admin_id']]);
        $admin_user = $admin_stmt->fetch();
    } catch (PDOException $e) {
        // Eski tablo yapısını dene (adminler)
        try {
            $admin_query = "SELECT * FROM adminler WHERE id = ?";
            $admin_stmt = $pdo->prepare($admin_query);
            $admin_stmt->execute([$_SESSION['admin_id']]);
            $admin_user = $admin_stmt->fetch();
        } catch (PDOException $e2) {
            // Hiçbir admin tablosu bulunamadı
            $admin_user = [
                'id' => $_SESSION['admin_id'],
                'kullanici_adi' => 'Admin',
                'ad' => 'DOBİEN',
                'soyad' => 'Admin',
                'email' => 'admin@dobien.com',
                'rol' => 'super_admin',
                'avatar' => null
            ];
        }
    }
}

// Sayfa başlığını belirle
$page_titles = [
    'index.php' => 'Admin Panel',
    'site-ayarlari.php' => 'Site Ayarları',
    'yazilar.php' => 'Yazılar & Metinler',
    'slider.php' => 'Slider Yönetimi',
    'sayfalar.php' => 'Sayfa İçerikleri',
    'yas-uyarisi.php' => 'Yaş Uyarısı',
    'videolar.php' => 'Video Yönetimi',
    'kategoriler.php' => 'Kategori Yönetimi',
    'etiketler.php' => 'Etiket Yönetimi',
    'yorumlar.php' => 'Yorum Yönetimi',
    'kullanicilar.php' => 'Kullanıcı Yönetimi',
    'uyelikler.php' => 'Üyelik Paketleri',
    'odemeler.php' => 'Ödemeler',
    'mesajlar.php' => 'Mesajlar',
    'medya-kutuphanesi.php' => 'Medya Kütüphanesi',
    'dosya-yukle.php' => 'Dosya Yükle',
    'logo-favicon.php' => 'Logo & Favicon',
    'istatistikler.php' => 'İstatistikler',
    'gelir-raporlari.php' => 'Gelir Raporları',
    'kullanici-analitigi.php' => 'Kullanıcı Analitikleri',
    'video-analitigi.php' => 'Video Analitikleri',
    'yedekleme.php' => 'Yedekleme',
    'sistem-loglari.php' => 'Sistem Logları',
    'guvenlik.php' => 'Güvenlik',
    'cache-temizle.php' => 'Cache Temizle',
    'adminler.php' => 'Admin Yönetimi'
];

$current_page = basename($_SERVER['PHP_SELF']);
$page_title = $page_titles[$current_page] ?? 'Admin Panel';

// Sayfa ikonları
$page_icons = [
    'index.php' => 'fas fa-home',
    'site-ayarlari.php' => 'fas fa-cog',
    'yazilar.php' => 'fas fa-edit',
    'slider.php' => 'fas fa-images',
    'sayfalar.php' => 'fas fa-file',
    'yas-uyarisi.php' => 'fas fa-exclamation-triangle',
    'videolar.php' => 'fas fa-video',
    'kategoriler.php' => 'fas fa-th-large',
    'etiketler.php' => 'fas fa-tags',
    'yorumlar.php' => 'fas fa-comments',
    'kullanicilar.php' => 'fas fa-users',
    'uyelikler.php' => 'fas fa-crown',
    'odemeler.php' => 'fas fa-credit-card',
    'mesajlar.php' => 'fas fa-envelope',
    'medya-kutuphanesi.php' => 'fas fa-folder',
    'dosya-yukle.php' => 'fas fa-upload',
    'logo-favicon.php' => 'fas fa-image',
    'istatistikler.php' => 'fas fa-chart-bar',
    'gelir-raporlari.php' => 'fas fa-money-bill',
    'kullanici-analitigi.php' => 'fas fa-user-chart',
    'video-analitigi.php' => 'fas fa-video',
    'yedekleme.php' => 'fas fa-database',
    'sistem-loglari.php' => 'fas fa-list-alt',
    'guvenlik.php' => 'fas fa-shield-alt',
    'cache-temizle.php' => 'fas fa-trash',
    'adminler.php' => 'fas fa-user-shield'
];

$page_icon = $page_icons[$current_page] ?? 'fas fa-home';

// Bildirimler (dummy data - gerçek projede veritabanından gelecek)
$notifications = [
    ['type' => 'user', 'message' => 'Yeni kullanıcı kaydı', 'time' => '5 dakika önce'],
    ['type' => 'video', 'message' => 'Yeni video yüklendi', 'time' => '1 saat önce'],
    ['type' => 'payment', 'message' => 'Yeni ödeme alındı', 'time' => '2 saat önce']
];
?>

<!-- Top Bar -->
<header class="admin-topbar">
    <div class="topbar-left">
        <button class="mobile-sidebar-toggle" id="mobileSidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <h1 class="page-title">
            <i class="<?php echo $page_icon; ?>"></i>
            <?php echo $page_title; ?>
        </h1>
    </div>
    
    <div class="topbar-right">
        <!-- Site Görüntüle Butonu -->
        <div class="topbar-item">
            <button class="btn btn-outline" onclick="window.open('../index.php', '_blank')" title="Siteyi Görüntüle">
                <i class="fas fa-external-link-alt"></i>
                <span class="hide-mobile">Siteyi Görüntüle</span>
            </button>
        </div>
        
        <!-- Bildirimler -->
        <div class="topbar-item dropdown">
            <button class="notification-toggle" title="Bildirimler">
                <i class="fas fa-bell"></i>
                <span class="notification-badge">3</span>
            </button>
            <div class="dropdown-menu notification-menu">
                <div class="notification-header">
                    <h4>Bildirimler</h4>
                    <span class="notification-count">3 yeni</span>
                </div>
                <div class="notification-list">
                    <?php foreach ($notifications as $notification): ?>
                    <div class="notification-item">
                        <div class="notification-icon">
                            <?php
                            switch ($notification['type']) {
                                case 'user':
                                    echo '<i class="fas fa-user-plus"></i>';
                                    break;
                                case 'video':
                                    echo '<i class="fas fa-video"></i>';
                                    break;
                                case 'payment':
                                    echo '<i class="fas fa-credit-card"></i>';
                                    break;
                                default:
                                    echo '<i class="fas fa-bell"></i>';
                                    break;
                            }
                            ?>
                        </div>
                        <div class="notification-content">
                            <p><?php echo safeOutput($notification['message']); ?></p>
                            <small><?php echo safeOutput($notification['time']); ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="notification-footer">
                    <a href="bildirimler.php">Tüm bildirimleri gör</a>
                </div>
            </div>
        </div>
        
        <!-- Hızlı Eylemler -->
        <div class="topbar-item dropdown">
            <button class="quick-actions-toggle" title="Hızlı Eylemler">
                <i class="fas fa-plus"></i>
            </button>
            <div class="dropdown-menu quick-actions-menu">
                <div class="quick-actions-header">
                    <h4>Hızlı Eylemler</h4>
                </div>
                <div class="quick-actions-list">
                    <a href="videolar.php?action=add" class="quick-action-item">
                        <i class="fas fa-video"></i>
                        <span>Yeni Video Ekle</span>
                    </a>
                    <a href="kategoriler.php?action=add" class="quick-action-item">
                        <i class="fas fa-th-large"></i>
                        <span>Yeni Kategori</span>
                    </a>
                    <a href="kullanicilar.php?action=add" class="quick-action-item">
                        <i class="fas fa-user-plus"></i>
                        <span>Yeni Kullanıcı</span>
                    </a>
                    <a href="slider.php?action=add" class="quick-action-item">
                        <i class="fas fa-images"></i>
                        <span>Slider Ekle</span>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Admin Profil -->
        <div class="topbar-item dropdown">
            <button class="admin-profile-toggle">
                <img src="<?php echo $admin_user['avatar'] ? '../uploads/avatars/' . $admin_user['avatar'] : '../assets/images/default-avatar.png'; ?>" 
                     alt="Admin" class="admin-avatar">
                <span class="admin-name"><?php echo safeOutput($admin_user['kullanici_adi']); ?></span>
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="dropdown-menu admin-menu">
                <div class="admin-info">
                    <div class="admin-avatar-large">
                        <img src="<?php echo $admin_user['avatar'] ? '../uploads/avatars/' . $admin_user['avatar'] : '../assets/images/default-avatar.png'; ?>" 
                             alt="Admin">
                    </div>
                    <div class="admin-details">
                        <h4><?php echo safeOutput($admin_user['ad_soyad'] ?: $admin_user['kullanici_adi']); ?></h4>
                        <p><?php echo safeOutput($admin_user['email']); ?></p>
                        <span class="admin-role"><?php echo ucfirst($admin_user['rol']); ?> Admin</span>
                    </div>
                </div>
                <div class="divider"></div>
                <a href="profil.php"><i class="fas fa-user"></i> Profilim</a>
                <a href="hesap-ayarlari.php"><i class="fas fa-cog"></i> Hesap Ayarları</a>
                <a href="admin-loglar.php"><i class="fas fa-history"></i> Aktivite Logları</a>
                <div class="divider"></div>
                <a href="site-ayarlari.php"><i class="fas fa-globe"></i> Site Ayarları</a>
                <a href="yedekleme.php"><i class="fas fa-database"></i> Yedekleme</a>
                <div class="divider"></div>
                <a href="cikis.php" class="text-danger"><i class="fas fa-sign-out-alt"></i> Çıkış Yap</a>
            </div>
        </div>
    </div>
</header>

<style>
/* Topbar Ek CSS */
.notification-toggle, .quick-actions-toggle {
    position: relative;
    background: none;
    border: none;
    color: var(--text-muted);
    font-size: 1.25rem;
    cursor: pointer;
    padding: 0.75rem;
    border-radius: var(--radius-lg);
    transition: var(--transition);
}

.notification-toggle:hover, .quick-actions-toggle:hover {
    color: var(--primary-color);
    background: var(--bg-tertiary);
}

.notification-badge {
    position: absolute;
    top: 0.25rem;
    right: 0.25rem;
    background: var(--error-color);
    color: white;
    font-size: 0.7rem;
    font-weight: 600;
    padding: 0.125rem 0.375rem;
    border-radius: 10px;
    min-width: 18px;
    text-align: center;
}

.notification-menu, .quick-actions-menu, .admin-menu {
    min-width: 300px;
    max-height: 400px;
    overflow-y: auto;
}

.notification-header, .quick-actions-header {
    padding: 1rem;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.notification-header h4, .quick-actions-header h4 {
    margin: 0;
    color: var(--text-primary);
    font-size: 1rem;
}

.notification-count {
    color: var(--primary-color);
    font-size: 0.8rem;
    font-weight: 500;
}

.notification-list, .quick-actions-list {
    padding: 0.5rem 0;
}

.notification-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 0.75rem 1rem;
    transition: var(--transition);
    border-bottom: 1px solid var(--border-color);
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-item:hover {
    background: var(--bg-tertiary);
}

.notification-icon {
    width: 40px;
    height: 40px;
    background: var(--gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
    flex-shrink: 0;
}

.notification-content {
    flex: 1;
}

.notification-content p {
    margin: 0 0 0.25rem 0;
    color: var(--text-primary);
    font-size: 0.9rem;
    font-weight: 500;
}

.notification-content small {
    color: var(--text-muted);
    font-size: 0.8rem;
}

.notification-footer {
    padding: 0.75rem 1rem;
    border-top: 1px solid var(--border-color);
    text-align: center;
}

.notification-footer a {
    color: var(--primary-color);
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
}

.quick-action-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    color: var(--text-secondary);
    text-decoration: none;
    transition: var(--transition);
    font-size: 0.9rem;
}

.quick-action-item:hover {
    background: var(--bg-tertiary);
    color: var(--text-primary);
}

.quick-action-item i {
    width: 20px;
    text-align: center;
    color: var(--primary-color);
}

.admin-info {
    padding: 1rem;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    gap: 1rem;
    align-items: center;
}

.admin-avatar-large {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    overflow: hidden;
    border: 2px solid var(--primary-color);
    flex-shrink: 0;
}

.admin-avatar-large img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.admin-details h4 {
    margin: 0 0 0.25rem 0;
    color: var(--text-primary);
    font-size: 1rem;
}

.admin-details p {
    margin: 0 0 0.25rem 0;
    color: var(--text-muted);
    font-size: 0.8rem;
}

.admin-role {
    background: var(--gradient-primary);
    color: white;
    font-size: 0.7rem;
    font-weight: 600;
    padding: 0.25rem 0.5rem;
    border-radius: var(--radius-sm);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.hide-mobile {
    display: inline;
}

@media (max-width: 768px) {
    .hide-mobile {
        display: none;
    }
    
    .notification-menu, .quick-actions-menu, .admin-menu {
        min-width: 280px;
        right: -50px;
    }
    
    .admin-info {
        flex-direction: column;
        text-align: center;
    }
    
    .page-title {
        font-size: 1.25rem;
    }
    
    .topbar-right {
        gap: 0.5rem;
    }
    
    .admin-name {
        display: none;
    }
}

@media (max-width: 480px) {
    .notification-menu, .quick-actions-menu, .admin-menu {
        min-width: calc(100vw - 2rem);
        right: -20px;
        left: auto;
    }
    
    .page-title {
        font-size: 1.1rem;
    }
    
    .page-title span {
        display: none;
    }
}
</style>

<script>
/**
 * DOBİEN Video Platform - Topbar JavaScript
 * Geliştirici: DOBİEN
 */

document.addEventListener('DOMContentLoaded', function() {
    // Dropdown toggle functionality
    document.querySelectorAll('.dropdown').forEach(dropdown => {
        const toggle = dropdown.querySelector('button');
        const menu = dropdown.querySelector('.dropdown-menu');
        
        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            
            // Close other dropdowns
            document.querySelectorAll('.dropdown-menu').forEach(otherMenu => {
                if (otherMenu !== menu) {
                    otherMenu.classList.remove('show');
                }
            });
            
            // Toggle current dropdown
            menu.classList.toggle('show');
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function() {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.classList.remove('show');
        });
    });
    
    // Prevent dropdown from closing when clicking inside
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
    
    console.log('DOBİEN Admin Topbar yüklendi!');
});

// Add show class functionality to CSS
const style = document.createElement('style');
style.textContent = `
    .dropdown-menu.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
`;
document.head.appendChild(style);
</script>