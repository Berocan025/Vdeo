<?php
/**
 * DOBİEN Video Platform - Admin Çıkış
 * Geliştirici: DOBİEN
 * Admin Panel Çıkış İşlemi
 * Tüm Hakları Saklıdır © DOBİEN
 */

require_once '../includes/config.php';

// Session'ı temizle
session_destroy();

// Cookie'leri temizle
if (isset($_COOKIE['admin_remember'])) {
    setcookie('admin_remember', '', time() - 3600, '/');
}

// Success message ile giriş sayfasına yönlendir
header('Location: giris.php?message=Başarıyla çıkış yaptınız');
exit;
?>