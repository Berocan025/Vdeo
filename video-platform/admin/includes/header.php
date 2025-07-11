<?php
/**
 * DOBİEN Video Platform - Admin Panel Header
 * Geliştirici: DOBİEN
 */

// Oturum kontrolü
if (!defined('ADMIN_AREA')) {
    die('Bu sayfaya doğrudan erişim yasaktır!');
}

if (!isset($_SESSION['admin_id'])) {
    header('Location: giris.php');
    exit;
}

// Admin bilgilerini al
$admin_query = "SELECT * FROM admin_kullanicilar WHERE id = ? AND durum = 'aktif'";
$admin_stmt = $pdo->prepare($admin_query);
$admin_stmt->execute([$_SESSION['admin_id']]);
$admin = $admin_stmt->fetch();

if (!$admin) {
    session_destroy();
    header('Location: giris.php');
    exit;
}

// Site ayarlarını al
$settings_query = "SELECT * FROM site_ayarlari";
$settings_result = $pdo->query($settings_query);
$settings = [];
while ($row = $settings_result->fetch()) {
    $settings[$row['anahtar']] = $row['deger'];
}

// Okunmamış bildirimleri al
$notifications_query = "SELECT COUNT(*) as count FROM admin_bildirimler WHERE durum = 'okunmamis'";
$notifications_count = $pdo->query($notifications_query)->fetch()['count'];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>DOBİEN Admin Panel</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Admin CSS -->
    <link href="assets/css/admin.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        .admin-sidebar {
            width: 280px;
            background: #2c3e50;
            color: white;
            flex-shrink: 0;
        }
        
        .admin-content {
            flex: 1;
            background: #ecf0f1;
        }
        
        .admin-topbar {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
        }
        
        .main-content {
            padding: 2rem;
        }
        
        .dobien-brand {
            font-weight: bold;
            color: #3498db;
        }
        
        .notification-badge {
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.75rem;
            position: absolute;
            top: -5px;
            right: -8px;
        }
        
        .admin-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .admin-menu li {
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .admin-menu a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 1rem 1.5rem;
            transition: all 0.3s;
        }
        
        .admin-menu a:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .admin-menu .active {
            background: #3498db;
        }
        
        .admin-menu i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
        }
        
        .submenu {
            background: rgba(0,0,0,0.2);
            display: none;
        }
        
        .submenu a {
            padding-left: 3rem;
            font-size: 0.9rem;
        }
        
        .admin-menu .has-submenu > a::after {
            content: '\f107';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            float: right;
            transition: transform 0.3s;
        }
        
        .admin-menu .has-submenu.open > a::after {
            transform: rotate(180deg);
        }
        
        .admin-menu .has-submenu.open .submenu {
            display: block;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <?php include 'sidebar.php'; ?>
        </div>
        
        <!-- Main Content -->
        <div class="admin-content">
            <!-- Topbar -->
            <div class="admin-topbar">
                <?php include 'topbar.php'; ?>
            </div>
            
            <!-- Content -->
            <div class="main-content">