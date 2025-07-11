<?php
/**
 * DOBİEN Video Platform - Admin Kategori API
 * Geliştirici: DOBİEN
 */

define('ADMIN_AREA', true);
session_start();

// Admin oturum kontrolü
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once '../includes/config.php';

// Content-Type header'ı ayarla
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
        // Tek kategori getir
        $id = (int)$_GET['id'];
        $query = "SELECT * FROM kategoriler WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id]);
        $category = $stmt->fetch();
        
        if ($category) {
            echo json_encode($category);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Kategori bulunamadı']);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
        // Tüm kategorileri getir
        $query = "SELECT * FROM kategoriler ORDER BY siralama ASC, kategori_adi ASC";
        $categories = $pdo->query($query)->fetchAll();
        echo json_encode($categories);
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Sunucu hatası: ' . $e->getMessage()]);
}
?>