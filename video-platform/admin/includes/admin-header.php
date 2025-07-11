<?php
/**
 * DOBİEN Video Platform - Admin Header
 * Admin paneli için genel header dosyası
 */

// Oturum kontrolü
if (!isset($_SESSION['admin_id'])) {
    header("Location: giris.php");
    exit();
}

// Veritabanı bağlantısı
require_once '../config/config.php';

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

// Sayfa başlığını belirle
$page_title = $page_title ?? 'Admin Panel';
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - DOBİEN Admin Panel</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/images/favicon.ico">
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/admin.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom Meta -->
    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="DOBİEN Video Platform Admin Panel">
    <meta name="author" content="DOBİEN">
    
    <!-- Admin Panel CSS Variables -->
    <style>
        :root {
            --admin-primary: #6366f1;
            --admin-secondary: #8b5cf6;
            --admin-success: #10b981;
            --admin-danger: #ef4444;
            --admin-warning: #f59e0b;
            --admin-info: #3b82f6;
            --admin-dark: #1f2937;
            --admin-light: #f9fafb;
            --admin-border: #e5e7eb;
            --admin-text: #374151;
            --admin-text-muted: #6b7280;
            --admin-bg: #ffffff;
            --admin-bg-secondary: #f8fafc;
            --admin-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            --admin-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --admin-radius: 0.5rem;
            --admin-radius-lg: 0.75rem;
            --admin-transition: all 0.3s ease;
        }
        
        [data-theme="dark"] {
            --admin-bg: #111827;
            --admin-bg-secondary: #1f2937;
            --admin-text: #f9fafb;
            --admin-text-muted: #d1d5db;
            --admin-border: #374151;
            --admin-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.3);
            --admin-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--admin-bg-secondary);
            color: var(--admin-text);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        
        .admin-loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--admin-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.3s ease;
        }
        
        .admin-loading.hidden {
            opacity: 0;
            pointer-events: none;
        }
        
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid var(--admin-border);
            border-top: 4px solid var(--admin-primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .admin-header-info {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: var(--admin-primary);
            color: white;
            padding: 0.5rem;
            text-align: center;
            font-size: 0.875rem;
            z-index: 1000;
        }
        
        .admin-header-info.show {
            display: block;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .admin-header-info {
                font-size: 0.8rem;
                padding: 0.4rem;
            }
        }
        
        /* Print Styles */
        @media print {
            .admin-sidebar,
            .admin-topbar,
            .admin-header-info {
                display: none !important;
            }
            
            .admin-main {
                margin-left: 0 !important;
                padding: 0 !important;
            }
            
            .admin-content {
                padding: 1rem !important;
            }
        }
        
        /* Accessibility */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
        
        /* Focus Styles */
        button:focus,
        input:focus,
        textarea:focus,
        select:focus {
            outline: 2px solid var(--admin-primary);
            outline-offset: 2px;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--admin-bg-secondary);
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--admin-border);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--admin-text-muted);
        }
    </style>
</head>
<body class="admin-body">

<!-- Loading Screen -->
<div class="admin-loading" id="adminLoading">
    <div class="loading-spinner"></div>
</div>

<!-- Admin Header Info -->
<div class="admin-header-info" id="adminHeaderInfo">
    <i class="fas fa-info-circle"></i>
    DOBİEN Video Platform Admin Panel - Hoş Geldiniz <?php echo safeOutput($admin_user['kullanici_adi']); ?>
</div>

<!-- Admin Wrapper -->
<div class="admin-wrapper">

<script>
/**
 * DOBİEN Video Platform - Admin Header JavaScript
 * Geliştirici: DOBİEN
 */

// Loading ekranını gizle
window.addEventListener('load', function() {
    const loading = document.getElementById('adminLoading');
    if (loading) {
        setTimeout(() => {
            loading.classList.add('hidden');
        }, 500);
    }
});

// Admin bilgi mesajını göster
document.addEventListener('DOMContentLoaded', function() {
    const headerInfo = document.getElementById('adminHeaderInfo');
    if (headerInfo) {
        setTimeout(() => {
            headerInfo.classList.add('show');
        }, 1000);
        
        // 5 saniye sonra gizle
        setTimeout(() => {
            headerInfo.classList.remove('show');
        }, 6000);
    }
});

// Tema değiştirme
function toggleTheme() {
    const body = document.body;
    const currentTheme = body.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    body.setAttribute('data-theme', newTheme);
    localStorage.setItem('admin-theme', newTheme);
}

// Tema yükleme
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('admin-theme');
    if (savedTheme) {
        document.body.setAttribute('data-theme', savedTheme);
    }
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl + Shift + T: Tema değiştir
    if (e.ctrlKey && e.shiftKey && e.key === 'T') {
        e.preventDefault();
        toggleTheme();
    }
    
    // Ctrl + Shift + H: Ana sayfa
    if (e.ctrlKey && e.shiftKey && e.key === 'H') {
        e.preventDefault();
        window.location.href = 'index.php';
    }
    
    // Ctrl + Shift + S: Site ayarları
    if (e.ctrlKey && e.shiftKey && e.key === 'S') {
        e.preventDefault();
        window.location.href = 'site-ayarlari.php';
    }
});

// Global admin functions
window.AdminPanel = {
    showNotification: function(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `admin-notification admin-notification-${type}`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation' : 'info'}-circle"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        document.body.appendChild(notification);
        
        // Otomatik kaldır
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    },
    
    confirmDelete: function(message = 'Bu işlemi geri alamazsınız!') {
        return confirm(`Bu öğeyi silmek istediğinizden emin misiniz?\n\n${message}`);
    },
    
    formatDate: function(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('tr-TR', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    },
    
    formatFileSize: function(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
};

console.log('DOBİEN Admin Panel Header yüklendi!');
</script>