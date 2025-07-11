<?php
/**
 * DOBİEN Video Platform - Admin Çıkış
 * Geliştirici: DOBİEN
 * Modern Video Paylaşım Platformu
 */

session_start();

// Admin oturumunu sonlandır
if (isset($_SESSION['admin_id'])) {
    // Güvenlik için tüm admin bilgilerini temizle
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_name']);
    unset($_SESSION['admin_email']);
    unset($_SESSION['admin_role']);
    unset($_SESSION['admin_logged_in']);
    
    // Session'ı tamamen temizle
    session_destroy();
    
    // Yeni session başlat
    session_start();
    
    // Başarı mesajı
    $_SESSION['message'] = 'Güvenli bir şekilde çıkış yaptınız.';
    $_SESSION['message_type'] = 'success';
}

// Admin giriş sayfasına yönlendir
header('Location: admin-giris.php');
exit;
?>