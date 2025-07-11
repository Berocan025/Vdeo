<?php
/**
 * DOBİEN Video Platform - Admin Bildirimler
 * Geliştirici: DOBİEN
 */

define('ADMIN_AREA', true);
session_start();

require_once '../includes/config.php';

$page_title = "Bildirimler";

// Bildirimi okundu olarak işaretle
if (isset($_GET['mark_read']) && is_numeric($_GET['mark_read'])) {
    $id = (int)$_GET['mark_read'];
    $update_query = "UPDATE admin_bildirimler SET durum = 'okunmus', okunma_tarihi = NOW() WHERE id = ?";
    $update_stmt = $pdo->prepare($update_query);
    $update_stmt->execute([$id]);
    
    header('Location: bildirimler.php');
    exit;
}

// Bildirimi sil
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $delete_query = "DELETE FROM admin_bildirimler WHERE id = ?";
    $delete_stmt = $pdo->prepare($delete_query);
    if ($delete_stmt->execute([$id])) {
        $_SESSION['success'] = 'Bildirim başarıyla silindi!';
    } else {
        $_SESSION['error'] = 'Bildirim silinirken hata oluştu!';
    }
    
    header('Location: bildirimler.php');
    exit;
}

// Tüm bildirimleri okundu işaretle
if (isset($_GET['mark_all_read'])) {
    $update_query = "UPDATE admin_bildirimler SET durum = 'okunmus', okunma_tarihi = NOW() WHERE durum = 'okunmamis'";
    $pdo->query($update_query);
    
    $_SESSION['success'] = 'Tüm bildirimler okundu olarak işaretlendi!';
    header('Location: bildirimler.php');
    exit;
}

// Bildirimler listesi
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

$notifications_query = "SELECT * FROM admin_bildirimler ORDER BY olusturma_tarihi DESC LIMIT $per_page OFFSET $offset";
$notifications = $pdo->query($notifications_query)->fetchAll();

// Toplam bildirim sayısı
$total_query = "SELECT COUNT(*) as total FROM admin_bildirimler";
$total_notifications = $pdo->query($total_query)->fetch()['total'];
$total_pages = ceil($total_notifications / $per_page);

// Okunmamış bildirim sayısı
$unread_query = "SELECT COUNT(*) as unread FROM admin_bildirimler WHERE durum = 'okunmamis'";
$unread_count = $pdo->query($unread_query)->fetch()['unread'];

include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-bell"></i> Bildirimler 
        <?php if ($unread_count > 0): ?>
            <span class="badge bg-danger"><?php echo $unread_count; ?></span>
        <?php endif; ?>
    </h1>
    <?php if ($unread_count > 0): ?>
    <a href="?mark_all_read" class="btn btn-success">
        <i class="fas fa-check-double"></i> Tümünü Okundu İşaretle
    </a>
    <?php endif; ?>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            Bildirim Listesi 
            <small class="text-muted">(Toplam: <?php echo $total_notifications; ?>)</small>
        </h6>
    </div>
    <div class="card-body">
        <?php if (empty($notifications)): ?>
            <div class="text-center py-4">
                <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                <p class="text-muted">Henüz hiç bildirim bulunmuyor.</p>
            </div>
        <?php else: ?>
            <div class="list-group">
                <?php foreach ($notifications as $notification): ?>
                <div class="list-group-item <?php echo $notification['durum'] == 'okunmamis' ? 'list-group-item-warning' : ''; ?>">
                    <div class="d-flex w-100 justify-content-between">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-<?php 
                                    switch($notification['tip']) {
                                        case 'bilgi': echo 'info-circle text-info'; break;
                                        case 'uyari': echo 'exclamation-triangle text-warning'; break;
                                        case 'hata': echo 'times-circle text-danger'; break;
                                        case 'basari': echo 'check-circle text-success'; break;
                                        case 'sikayet': echo 'flag text-danger'; break;
                                        default: echo 'bell text-primary';
                                    }
                                ?> me-2"></i>
                                <h6 class="mb-0 me-2"><?php echo htmlspecialchars($notification['baslik']); ?></h6>
                                <?php if ($notification['durum'] == 'okunmamis'): ?>
                                    <span class="badge bg-warning text-dark">Yeni</span>
                                <?php endif; ?>
                            </div>
                            <p class="mb-1 text-muted"><?php echo htmlspecialchars($notification['mesaj']); ?></p>
                            <small class="text-muted">
                                <i class="fas fa-clock"></i> 
                                <?php echo date('d.m.Y H:i', strtotime($notification['olusturma_tarihi'])); ?>
                                <?php if ($notification['durum'] == 'okunmus' && $notification['okunma_tarihi']): ?>
                                    | Okunma: <?php echo date('d.m.Y H:i', strtotime($notification['okunma_tarihi'])); ?>
                                <?php endif; ?>
                            </small>
                        </div>
                        <div class="d-flex flex-column align-items-end">
                            <?php if ($notification['link']): ?>
                                <a href="<?php echo htmlspecialchars($notification['link']); ?>" 
                                   class="btn btn-sm btn-outline-primary mb-1">
                                    <i class="fas fa-external-link-alt"></i> Git
                                </a>
                            <?php endif; ?>
                            
                            <div class="btn-group">
                                <?php if ($notification['durum'] == 'okunmamis'): ?>
                                    <a href="?mark_read=<?php echo $notification['id']; ?>" 
                                       class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-check"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="?delete=<?php echo $notification['id']; ?>" 
                                   class="btn btn-sm btn-outline-danger delete-btn">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Sayfalama -->
            <?php if ($total_pages > 1): ?>
            <nav aria-label="Bildirim sayfalama" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>">&laquo; Önceki</a>
                        </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>">Sonraki &raquo;</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>