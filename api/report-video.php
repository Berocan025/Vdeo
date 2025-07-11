<?php
/**
 * DOBİEN Video Platform - Report API
 * Geliştirici: DOBİEN
 * Video Şikayet API'si
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Sadece POST isteği kabul et
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

require_once '../includes/config.php';

// Giriş kontrolü
if (!$current_user) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Bu işlem için giriş yapmalısınız.']);
    exit;
}

// JSON veriyi oku
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Geçersiz veri formatı.']);
    exit;
}

$video_id = $input['video_id'] ?? '';
$reason = trim($input['reason'] ?? '');

// Veri doğrulama
if (empty($video_id) || !is_numeric($video_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Geçersiz video ID.']);
    exit;
}

if (empty($reason)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Şikayet sebebi belirtilmelidir.']);
    exit;
}

if (strlen($reason) < 5) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Şikayet sebebi en az 5 karakter olmalıdır.']);
    exit;
}

if (strlen($reason) > 500) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Şikayet sebebi en fazla 500 karakter olabilir.']);
    exit;
}

try {
    // Video varlığını kontrol et
    $video_check = $pdo->prepare("SELECT id, baslik FROM videolar WHERE id = ? AND durum = 'aktif'");
    $video_check->execute([$video_id]);
    $video = $video_check->fetch();
    
    if (!$video) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Video bulunamadı.']);
        exit;
    }
    
    $user_id = $current_user['id'];
    
    // Önceki şikayet kontrolü (spamı önlemek için)
    $existing_report = $pdo->prepare("
        SELECT id FROM video_sikayetler 
        WHERE kullanici_id = ? AND video_id = ? 
        AND sikayet_tarihi > DATE_SUB(NOW(), INTERVAL 24 HOUR)
    ");
    $existing_report->execute([$user_id, $video_id]);
    
    if ($existing_report->fetch()) {
        http_response_code(429);
        echo json_encode(['success' => false, 'message' => 'Bu video için son 24 saat içinde şikayet gönderdiniz.']);
        exit;
    }
    
    $pdo->beginTransaction();
    
    // Şikayeti kaydet
    $insert_report = $pdo->prepare("
        INSERT INTO video_sikayetler (
            kullanici_id, video_id, sikayet_sebebi, sikayet_tarihi, 
            durum, ip_adresi, user_agent
        ) VALUES (?, ?, ?, NOW(), 'beklemede', ?, ?)
    ");
    
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $insert_report->execute([
        $user_id, 
        $video_id, 
        $reason,
        $ip_address,
        $user_agent
    ]);
    
    // Video tablosundaki şikayet sayacını artır
    $update_video = $pdo->prepare("UPDATE videolar SET sikayet_sayisi = sikayet_sayisi + 1 WHERE id = ?");
    $update_video->execute([$video_id]);
    
    // Admin bildirim tablosuna ekle (isteğe bağlı)
    $insert_notification = $pdo->prepare("
        INSERT INTO admin_bildirimler (
            tip, baslik, mesaj, olusturma_tarihi, durum
        ) VALUES (
            'sikayet', 
            'Yeni Video Şikayeti',
            CONCAT('Video: \"', ?, '\" için yeni şikayet alındı. Sebep: ', ?),
            NOW(),
            'okunmamis'
        )
    ");
    $insert_notification->execute([$video['baslik'], $reason]);
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Şikayetiniz başarıyla gönderildi. İnceleme yapılacaktır.'
    ]);
    
} catch (PDOException $e) {
    $pdo->rollback();
    error_log('Report API Error: ' . $e->getMessage());
    
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Veritabanı hatası oluştu.']);
    exit;
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollback();
    }
    error_log('Report API Error: ' . $e->getMessage());
    
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Bir hata oluştu.']);
    exit;
}
?>