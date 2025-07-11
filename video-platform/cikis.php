<?php
/**
 * DOBİEN Video Platform - Çıkış
 * Geliştirici: DOBİEN
 * Tüm Hakları Saklıdır © DOBİEN
 */

session_start();

// Güvenlik için referrer kontrolü (isteğe bağlı)
$safe_logout = true;
if (!empty($_SERVER['HTTP_REFERER'])) {
    $referer_host = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
    $current_host = $_SERVER['HTTP_HOST'];
    $safe_logout = ($referer_host === $current_host);
}

// Session verilerini temizle
if ($safe_logout) {
    // Tüm session verilerini temizle
    $_SESSION = array();
    
    // Session cookie'sini sil
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Session'ı yok et
    session_destroy();
    
    // "Beni Hatırla" cookie'sini temizle (varsa)
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
        
        // Veritabanından da remember token'ı temizle
        require_once 'includes/config.php';
        
        try {
            $stmt = $pdo->prepare("UPDATE kullanicilar SET remember_token = NULL WHERE remember_token = ?");
            $stmt->execute([$_COOKIE['remember_token']]);
        } catch (PDOException $e) {
            // Hata durumunda sessizce devam et
            error_log("Remember token cleanup failed: " . $e->getMessage());
        }
    }
}

// Başarılı çıkış mesajı için geçici cookie
setcookie('logout_success', '1', time() + 10, '/');

// Ana sayfaya yönlendir
header('Location: index.php');
exit;
?>