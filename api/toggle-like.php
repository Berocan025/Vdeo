<?php
/**
 * DOBİEN Video Platform - Like/Dislike API
 * Geliştirici: DOBİEN
 * Video Beğeni/Beğenmeme API'si
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
$type = $input['type'] ?? ''; // 'like' veya 'dislike'

// Veri doğrulama
if (empty($video_id) || !is_numeric($video_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Geçersiz video ID.']);
    exit;
}

if (!in_array($type, ['like', 'dislike'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Geçersiz işlem türü.']);
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
    $like_type = $type === 'like' ? 'begeni' : 'begenme';
    
    // Mevcut beğeni durumunu kontrol et
    $existing_like = $pdo->prepare("SELECT tur FROM video_begeniler WHERE kullanici_id = ? AND video_id = ?");
    $existing_like->execute([$user_id, $video_id]);
    $current_like = $existing_like->fetch();
    
    $pdo->beginTransaction();
    
    if ($current_like) {
        if ($current_like['tur'] === $like_type) {
            // Aynı işlem - beğeniyi kaldır
            $delete_like = $pdo->prepare("DELETE FROM video_begeniler WHERE kullanici_id = ? AND video_id = ?");
            $delete_like->execute([$user_id, $video_id]);
            
            // Video tablosundaki sayacı güncelle
            if ($like_type === 'begeni') {
                $update_video = $pdo->prepare("UPDATE videolar SET begeni_sayisi = GREATEST(0, begeni_sayisi - 1) WHERE id = ?");
            } else {
                $update_video = $pdo->prepare("UPDATE videolar SET begenme_sayisi = GREATEST(0, begenme_sayisi - 1) WHERE id = ?");
            }
            $update_video->execute([$video_id]);
            
            $user_liked = false;
            $user_disliked = false;
            
        } else {
            // Farklı işlem - beğeniyi güncelle
            $update_like = $pdo->prepare("UPDATE video_begeniler SET tur = ?, tarih = NOW() WHERE kullanici_id = ? AND video_id = ?");
            $update_like->execute([$like_type, $user_id, $video_id]);
            
            // Video tablosundaki sayaçları güncelle
            if ($like_type === 'begeni') {
                // Beğeni artır, beğenmeme azalt
                $update_video = $pdo->prepare("
                    UPDATE videolar 
                    SET begeni_sayisi = begeni_sayisi + 1,
                        begenme_sayisi = GREATEST(0, begenme_sayisi - 1)
                    WHERE id = ?
                ");
                $user_liked = true;
                $user_disliked = false;
            } else {
                // Beğenmeme artır, beğeni azalt
                $update_video = $pdo->prepare("
                    UPDATE videolar 
                    SET begenme_sayisi = begenme_sayisi + 1,
                        begeni_sayisi = GREATEST(0, begeni_sayisi - 1)
                    WHERE id = ?
                ");
                $user_liked = false;
                $user_disliked = true;
            }
            $update_video->execute([$video_id]);
        }
    } else {
        // Yeni beğeni ekle
        $insert_like = $pdo->prepare("INSERT INTO video_begeniler (kullanici_id, video_id, tur, tarih) VALUES (?, ?, ?, NOW())");
        $insert_like->execute([$user_id, $video_id, $like_type]);
        
        // Video tablosundaki sayacı güncelle
        if ($like_type === 'begeni') {
            $update_video = $pdo->prepare("UPDATE videolar SET begeni_sayisi = begeni_sayisi + 1 WHERE id = ?");
            $user_liked = true;
            $user_disliked = false;
        } else {
            $update_video = $pdo->prepare("UPDATE videolar SET begenme_sayisi = begenme_sayisi + 1 WHERE id = ?");
            $user_liked = false;
            $user_disliked = true;
        }
        $update_video->execute([$video_id]);
    }
    
    $pdo->commit();
    
    // Güncel sayıları çek
    $counts_query = $pdo->prepare("SELECT begeni_sayisi, begenme_sayisi FROM videolar WHERE id = ?");
    $counts_query->execute([$video_id]);
    $counts = $counts_query->fetch();
    
    echo json_encode([
        'success' => true,
        'user_liked' => $user_liked ?? false,
        'user_disliked' => $user_disliked ?? false,
        'like_count' => (int)$counts['begeni_sayisi'],
        'dislike_count' => (int)$counts['begenme_sayisi']
    ]);
    
} catch (PDOException $e) {
    $pdo->rollback();
    error_log('Like API Error: ' . $e->getMessage());
    
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Veritabanı hatası oluştu.']);
    exit;
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollback();
    }
    error_log('Like API Error: ' . $e->getMessage());
    
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Bir hata oluştu.']);
    exit;
}
?>