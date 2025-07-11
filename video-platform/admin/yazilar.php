<?php
/**
 * DOBİEN Video Platform - Yazılar & Metinler Yönetimi
 * Geliştirici: DOBİEN
 * Modern Video Paylaşım Platformu
 */

require_once '../includes/config.php';

// Admin kontrolü
if (!isAdmin()) {
    header('Location: giris.php');
    exit;
}

$page = 'yazilar';
$current_admin = checkAdminSession();

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yazılar & Metinler - DOBİEN Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/topbar.php'; ?>
            
            <div class="content">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h3 mb-0">Yazılar & Metinler Yönetimi</h1>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Bu sayfa yakında eklenecek. Site metinlerini ve yazılarını buradan yönetebileceksiniz.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>