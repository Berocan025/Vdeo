<?php
/**
 * DOBİEN Video Platform - Video API
 * Geliştirici: DOBİEN
 * Tüm Hakları Saklıdır © DOBİEN
 */

require_once '../includes/config.php';

// Admin kontrolü
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim']);
    exit;
}

// Content type
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Video ID gereklidir']);
    exit;
}

$video_id = (int)$_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM videolar WHERE id = ?");
    $stmt->execute([$video_id]);
    $video = $stmt->fetch();
    
    if ($video) {
        echo json_encode([
            'success' => true,
            'video' => $video
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Video bulunamadı'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Veritabanı hatası: ' . $e->getMessage()
    ]);
}
?>