<?php
/**
 * DOBİEN Video Platform - Admin Panel Sidebar
 * Geliştirici: DOBİEN
 * Tüm Hakları Saklıdır © DOBİEN
 */

// Mevcut sayfa adını belirle
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar -->
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-header">
        <div class="admin-logo">
            <h2><span class="text-gradient">DOBİEN</span></h2>
            <p>Admin Panel</p>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    
    <nav class="sidebar-nav">
        <ul class="nav-menu">
            <li class="nav-item <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                <a href="index.php" class="nav-link">
                    <i class="fas fa-home"></i>
                    <span>Ana Sayfa</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="#" class="nav-link has-submenu" data-submenu="site">
                    <i class="fas fa-cog"></i>
                    <span>Site Yönetimi</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu" id="submenu-site">
                    <li><a href="site-ayarlari.php" class="<?php echo $current_page == 'site-ayarlari.php' ? 'active' : ''; ?>">
                        <i class="fas fa-globe"></i> Site Ayarları
                    </a></li>
                    <li><a href="yazilar.php" class="<?php echo $current_page == 'yazilar.php' ? 'active' : ''; ?>">
                        <i class="fas fa-edit"></i> Yazılar & Metinler
                    </a></li>
                    <li><a href="slider.php" class="<?php echo $current_page == 'slider.php' ? 'active' : ''; ?>">
                        <i class="fas fa-images"></i> Slider Yönetimi
                    </a></li>
                    <li><a href="sayfalar.php" class="<?php echo $current_page == 'sayfalar.php' ? 'active' : ''; ?>">
                        <i class="fas fa-file"></i> Sayfa İçerikleri
                    </a></li>
                    <li><a href="yas-uyarisi.php" class="<?php echo $current_page == 'yas-uyarisi.php' ? 'active' : ''; ?>">
                        <i class="fas fa-exclamation-triangle"></i> Yaş Uyarısı
                    </a></li>
                </ul>
            </li>
            
            <li class="nav-item">
                <a href="#" class="nav-link has-submenu" data-submenu="content">
                    <i class="fas fa-video"></i>
                    <span>İçerik Yönetimi</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu" id="submenu-content">
                    <li><a href="videolar.php" class="<?php echo $current_page == 'videolar.php' ? 'active' : ''; ?>">
                        <i class="fas fa-play"></i> Video Yönetimi
                    </a></li>
                    <li><a href="kategoriler.php" class="<?php echo $current_page == 'kategoriler.php' ? 'active' : ''; ?>">
                        <i class="fas fa-th-large"></i> Kategori Yönetimi
                    </a></li>
                    <li><a href="etiketler.php" class="<?php echo $current_page == 'etiketler.php' ? 'active' : ''; ?>">
                        <i class="fas fa-tags"></i> Etiket Yönetimi
                    </a></li>
                    <li><a href="yorumlar.php" class="<?php echo $current_page == 'yorumlar.php' ? 'active' : ''; ?>">
                        <i class="fas fa-comments"></i> Yorum Yönetimi
                    </a></li>
                </ul>
            </li>
            
            <li class="nav-item">
                <a href="#" class="nav-link has-submenu" data-submenu="users">
                    <i class="fas fa-users"></i>
                    <span>Kullanıcı Yönetimi</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu" id="submenu-users">
                    <li><a href="kullanicilar.php" class="<?php echo $current_page == 'kullanicilar.php' ? 'active' : ''; ?>">
                        <i class="fas fa-user"></i> Kullanıcılar
                    </a></li>
                    <li><a href="uyelikler.php" class="<?php echo $current_page == 'uyelikler.php' ? 'active' : ''; ?>">
                        <i class="fas fa-crown"></i> Üyelik Paketleri
                    </a></li>
                    <li><a href="odemeler.php" class="<?php echo $current_page == 'odemeler.php' ? 'active' : ''; ?>">
                        <i class="fas fa-credit-card"></i> Ödemeler
                    </a></li>
                    <li><a href="mesajlar.php" class="<?php echo $current_page == 'mesajlar.php' ? 'active' : ''; ?>">
                        <i class="fas fa-envelope"></i> Mesajlar
                    </a></li>
                </ul>
            </li>
            
            <li class="nav-item">
                <a href="#" class="nav-link has-submenu" data-submenu="media">
                    <i class="fas fa-folder"></i>
                    <span>Medya Yönetimi</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu" id="submenu-media">
                    <li><a href="medya-kutuphanesi.php" class="<?php echo $current_page == 'medya-kutuphanesi.php' ? 'active' : ''; ?>">
                        <i class="fas fa-images"></i> Medya Kütüphanesi
                    </a></li>
                    <li><a href="dosya-yukle.php" class="<?php echo $current_page == 'dosya-yukle.php' ? 'active' : ''; ?>">
                        <i class="fas fa-upload"></i> Dosya Yükle
                    </a></li>
                    <li><a href="logo-favicon.php" class="<?php echo $current_page == 'logo-favicon.php' ? 'active' : ''; ?>">
                        <i class="fas fa-image"></i> Logo & Favicon
                    </a></li>
                </ul>
            </li>
            
            <li class="nav-item">
                <a href="#" class="nav-link has-submenu" data-submenu="reports">
                    <i class="fas fa-chart-bar"></i>
                    <span>Raporlar & İstatistikler</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu" id="submenu-reports">
                    <li><a href="istatistikler.php" class="<?php echo $current_page == 'istatistikler.php' ? 'active' : ''; ?>">
                        <i class="fas fa-analytics"></i> Genel İstatistikler
                    </a></li>
                    <li><a href="gelir-raporlari.php" class="<?php echo $current_page == 'gelir-raporlari.php' ? 'active' : ''; ?>">
                        <i class="fas fa-money-bill"></i> Gelir Raporları
                    </a></li>
                    <li><a href="kullanici-analitigi.php" class="<?php echo $current_page == 'kullanici-analitigi.php' ? 'active' : ''; ?>">
                        <i class="fas fa-user-chart"></i> Kullanıcı Analitikleri
                    </a></li>
                    <li><a href="video-analitigi.php" class="<?php echo $current_page == 'video-analitigi.php' ? 'active' : ''; ?>">
                        <i class="fas fa-video"></i> Video Analitikleri
                    </a></li>
                </ul>
            </li>
            
            <li class="nav-item">
                <a href="#" class="nav-link has-submenu" data-submenu="system">
                    <i class="fas fa-server"></i>
                    <span>Sistem</span>
                    <i class="fas fa-chevron-down submenu-arrow"></i>
                </a>
                <ul class="submenu" id="submenu-system">
                    <li><a href="yedekleme.php" class="<?php echo $current_page == 'yedekleme.php' ? 'active' : ''; ?>">
                        <i class="fas fa-database"></i> Yedekleme
                    </a></li>
                    <li><a href="sistem-loglari.php" class="<?php echo $current_page == 'sistem-loglari.php' ? 'active' : ''; ?>">
                        <i class="fas fa-list-alt"></i> Sistem Logları
                    </a></li>
                    <li><a href="guvenlik.php" class="<?php echo $current_page == 'guvenlik.php' ? 'active' : ''; ?>">
                        <i class="fas fa-shield-alt"></i> Güvenlik
                    </a></li>
                    <li><a href="cache-temizle.php" class="<?php echo $current_page == 'cache-temizle.php' ? 'active' : ''; ?>">
                        <i class="fas fa-trash"></i> Cache Temizle
                    </a></li>
                </ul>
            </li>
            
            <li class="nav-item <?php echo $current_page == 'adminler.php' ? 'active' : ''; ?>">
                <a href="adminler.php" class="nav-link">
                    <i class="fas fa-user-shield"></i>
                    <span>Admin Yönetimi</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <div class="sidebar-footer">
        <div class="developer-info">
            <i class="fas fa-code"></i>
            <span><strong>DOBİEN</strong> Admin Panel</span>
        </div>
    </div>
</aside>

<script>
/**
 * DOBİEN Video Platform - Sidebar JavaScript
 * Geliştirici: DOBİEN
 */

document.addEventListener('DOMContentLoaded', function() {
    // Submenu toggle functionality
    document.querySelectorAll('.has-submenu').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            const submenuId = this.getAttribute('data-submenu');
            const submenu = document.getElementById(`submenu-${submenuId}`);
            const arrow = this.querySelector('.submenu-arrow');
            
            // Close other open submenus
            document.querySelectorAll('.submenu').forEach(menu => {
                if (menu !== submenu) {
                    menu.classList.remove('active');
                }
            });
            
            document.querySelectorAll('.has-submenu').forEach(menuItem => {
                if (menuItem !== this) {
                    menuItem.classList.remove('active');
                }
            });
            
            // Toggle current submenu
            submenu.classList.toggle('active');
            this.classList.toggle('active');
        });
    });
    
    // Mobile sidebar toggle
    const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
    const sidebar = document.getElementById('adminSidebar');
    
    if (mobileSidebarToggle) {
        mobileSidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('mobile-open');
        });
    }
    
    // Auto-open submenu if current page is in submenu
    const currentPage = '<?php echo $current_page; ?>';
    const activeSubmenuLink = document.querySelector(`.submenu a[href="${currentPage}"]`);
    
    if (activeSubmenuLink) {
        const parentSubmenu = activeSubmenuLink.closest('.submenu');
        const parentMenuItem = document.querySelector(`[data-submenu="${parentSubmenu.id.replace('submenu-', '')}"]`);
        
        if (parentSubmenu && parentMenuItem) {
            parentSubmenu.classList.add('active');
            parentMenuItem.classList.add('active');
        }
    }
    
    console.log('DOBİEN Admin Sidebar yüklendi!');
});
</script>