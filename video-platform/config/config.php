<?php
/**
 * DOBİEN Video Platform - Ana Config Dosyası
 * Geliştirici: DOBİEN
 * Veritabanı ve Site Ayarları
 * Tüm Hakları Saklıdır © DOBİEN
 */

// Güvenlik kontrolü
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/../');
}

// Veritabanı Ayarları
define('DB_HOST', 'localhost');
define('DB_NAME', 'dobien_video_platform');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Site Ayarları
define('SITE_URL', 'http://localhost/video-platform');
define('SITE_NAME', 'DOBİEN Video Platform');
define('SITE_VERSION', '1.0.0');

// Dosya Yolları
define('UPLOADS_PATH', ABSPATH . 'uploads/');
define('ASSETS_PATH', ABSPATH . 'assets/');
define('INCLUDES_PATH', ABSPATH . 'includes/');

// Güvenlik Anahtarları
define('SALT_KEY', 'dobien_video_platform_salt_2024_secure_key_here');
define('AUTH_KEY', 'dobien_video_platform_auth_2024_secure_key_here');
define('SECURE_AUTH_KEY', 'dobien_video_platform_secure_2024_auth_key_here');

// Dosya Upload Ayarları
define('MAX_FILE_SIZE', 500 * 1024 * 1024); // 500MB
define('ALLOWED_VIDEO_FORMATS', 'mp4,avi,mov,wmv,flv');
define('ALLOWED_IMAGE_FORMATS', 'jpg,jpeg,png,gif,webp');

// Cache Ayarları
define('CACHE_ENABLED', true);
define('CACHE_TIME', 3600); // 1 saat

// E-posta Ayarları
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('SMTP_FROM_EMAIL', 'noreply@dobien.com');
define('SMTP_FROM_NAME', 'DOBİEN Video Platform');

// Video Player Ayarları
define('DEFAULT_VIDEO_QUALITY', '720p');
define('AUTOPLAY_ENABLED', false);
define('PRELOAD_VIDEO', 'metadata');

// Üyelik Ayarları
define('PREMIUM_PRICE_MONTHLY', 29.99);
define('VIP_PRICE_MONTHLY', 19.99);
define('TRIAL_PERIOD_DAYS', 7);

// Sosyal Medya Paylaşım Anahtarları
define('FACEBOOK_APP_ID', '');
define('TWITTER_API_KEY', '');
define('GOOGLE_CLIENT_ID', '');

// Hata Raporlama
if (defined('WP_DEBUG') && WP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Zaman Dilimi
date_default_timezone_set('Europe/Istanbul');

// Session ayarları
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // HTTPS için 1 yapın
ini_set('session.use_strict_mode', 1);

// Bellek limiti
ini_set('memory_limit', '256M');

// Dosya upload limitleri
ini_set('upload_max_filesize', '500M');
ini_set('post_max_size', '500M');
ini_set('max_execution_time', 300);

?>