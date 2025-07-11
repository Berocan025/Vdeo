<?php
require_once 'includes/config.php';

// Session'ı temizle
if (isset($_SESSION['user_id'])) {
    $_SESSION = array();
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
}

// Ana sayfaya yönlendir
header('Location: index.php');
exit;
?>
