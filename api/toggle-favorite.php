<?php
/**
 * DOBİEN Video Platform - Favorite API
 * Geliştirici: DOBİEN
 * Video Favori API'si
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

// Veri doğrulama
if (empty($video_id) || !is_numeric($video_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Geçersiz video ID.']);
    exit;
}

try {
    // Video varlığını kontrol et
    $video_check = $pdo->prepare("SELECT id FROM videolar WHERE id = ? AND durum = 'aktif'");
    $video_check->execute([$video_id]);
    
    if (!$video_check->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Video bulunamadı.']);
        exit;
    }
    
    $user_id = $current_user['id'];
    
    // Mevcut favori durumunu kontrol et
    $existing_favorite = $pdo->prepare("SELECT id FROM favoriler WHERE kullanici_id = ? AND video_id = ?");
    $existing_favorite->execute([$user_id, $video_id]);
    $is_favorite = $existing_favorite->fetch();
    
    $pdo->beginTransaction();
    
    if ($is_favorite) {
        // Favorilerden çıkar
        $delete_favorite = $pdo->prepare("DELETE FROM favoriler WHERE kullanici_id = ? AND video_id = ?");
        $delete_favorite->execute([$user_id, $video_id]);
        
        // Video tablosundaki favori sayacını azalt
        $update_video = $pdo->prepare("UPDATE videolar SET favori_sayisi = GREATEST(0, favori_sayisi - 1) WHERE id = ?");
        $update_video->execute([$video_id]);
        
        $result_is_favorite = false;
        $message = 'Video favorilerden çıkarıldı.';
        
    } else {
        // Favorilere ekle
        $insert_favorite = $pdo->prepare("INSERT INTO favoriler (kullanici_id, video_id, ekleme_tarihi) VALUES (?, ?, NOW())");
        $insert_favorite->execute([$user_id, $video_id]);
        
        // Video tablosundaki favori sayacını artır
        $update_video = $pdo->prepare("UPDATE videolar SET favori_sayisi = favori_sayisi + 1 WHERE id = ?");
        $update_video->execute([$video_id]);
        
        $result_is_favorite = true;
        $message = 'Video favorilere eklendi.';
    }
    
    $pdo->commit();
    
    // Güncel favori sayısını çek
    $count_query = $pdo->prepare("SELECT favori_sayisi FROM videolar WHERE id = ?");
    $count_query->execute([$video_id]);
    $count = $count_query->fetch();
    
    echo json_encode([
        'success' => true,
        'is_favorite' => $result_is_favorite,
        'favorite_count' => (int)$count['favori_sayisi'],
        'message' => $message
    ]);
    
} catch (PDOException $e) {
    $pdo->rollback();
    error_log('Favorite API Error: ' . $e->getMessage());
    
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Veritabanı hatası oluştu.']);
    exit;
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollback();
    }
    error_log('Favorite API Error: ' . $e->getMessage());
    
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Bir hata oluştu.']);
    exit;
}
?>