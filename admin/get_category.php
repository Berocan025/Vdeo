<?php
/**
 * DOBİEN Video Platform - Kategori API
 * Geliştirici: DOBİEN
 * Tüm Hakları Saklıdır © DOBİEN
 */

require_once '../includes/config.php';

// Admin kontrolü
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim']);
    exit;
}

// Content type
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Kategori ID gereklidir']);
    exit;
}

$category_id = (int)$_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM kategoriler WHERE id = ?");
    $stmt->execute([$category_id]);
    $category = $stmt->fetch();
    
    if ($category) {
        echo json_encode([
            'success' => true,
            'category' => $category
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Kategori bulunamadı'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Veritabanı hatası: ' . $e->getMessage()
    ]);
}
?>