<?php
/**
 * DOBİEN Video Platform - Admin Video Yönetimi
 * Geliştirici: DOBİEN
 * Modern Video Paylaşım Platformu
 */

require_once '../includes/config.php';

// Admin giriş kontrolü
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: giris.php');
    exit;
}

$admin_user = checkAdminSession();
if (!$admin_user) {
    header('Location: giris.php');
    exit;
}

$page_title = "Video Yönetimi";

// Video işlemleri
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        if ($action === 'add_video') {
            $baslik = trim($_POST['baslik'] ?? '');
            $aciklama = trim($_POST['aciklama'] ?? '');
            $kategori_id = (int)($_POST['kategori_id'] ?? 0);
            $video_url = trim($_POST['video_url'] ?? '');
            $kapak_resmi = trim($_POST['kapak_resmi'] ?? '');
            $durum = $_POST['durum'] ?? 'aktif';
            $goruntulenme_yetkisi = $_POST['goruntulenme_yetkisi'] ?? 'herkes';
            
            if (empty($baslik) || empty($video_url)) {
                throw new Exception('Başlık ve video URL zorunludur.');
            }
            
            $stmt = $pdo->prepare("
                INSERT INTO videolar (baslik, aciklama, kategori_id, video_url, kapak_resmi, durum, goruntulenme_yetkisi, ekleme_tarihi)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$baslik, $aciklama, $kategori_id, $video_url, $kapak_resmi, $durum, $goruntulenme_yetkisi]);
            
            $success_message = 'Video başarıyla eklendi.';
            
        } elseif ($action === 'update_video') {
            $id = (int)($_POST['id'] ?? 0);
            $baslik = trim($_POST['baslik'] ?? '');
            $aciklama = trim($_POST['aciklama'] ?? '');
            $kategori_id = (int)($_POST['kategori_id'] ?? 0);
            $video_url = trim($_POST['video_url'] ?? '');
            $kapak_resmi = trim($_POST['kapak_resmi'] ?? '');
            $durum = $_POST['durum'] ?? 'aktif';
            $goruntulenme_yetkisi = $_POST['goruntulenme_yetkisi'] ?? 'herkes';
            
            if ($id <= 0 || empty($baslik) || empty($video_url)) {
                throw new Exception('Geçersiz video ID veya eksik bilgiler.');
            }
            
            $stmt = $pdo->prepare("
                UPDATE videolar 
                SET baslik = ?, aciklama = ?, kategori_id = ?, video_url = ?, kapak_resmi = ?, durum = ?, goruntulenme_yetkisi = ?
                WHERE id = ?
            ");
            $stmt->execute([$baslik, $aciklama, $kategori_id, $video_url, $kapak_resmi, $durum, $goruntulenme_yetkisi, $id]);
            
            $success_message = 'Video başarıyla güncellendi.';
            
        } elseif ($action === 'delete_video') {
            $id = (int)($_POST['id'] ?? 0);
            
            if ($id <= 0) {
                throw new Exception('Geçersiz video ID.');
            }
            
            $stmt = $pdo->prepare("DELETE FROM videolar WHERE id = ?");
            $stmt->execute([$id]);
            
            $success_message = 'Video başarıyla silindi.';
        }
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Sayfalama
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Arama
$search = trim($_GET['search'] ?? '');
$category_filter = (int)($_GET['category'] ?? 0);
$status_filter = $_GET['status'] ?? '';

// Filtreli sorgu oluştur
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(baslik LIKE ? OR aciklama LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category_filter > 0) {
    $where_conditions[] = "kategori_id = ?";
    $params[] = $category_filter;
}

if (!empty($status_filter)) {
    $where_conditions[] = "durum = ?";
    $params[] = $status_filter;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Toplam video sayısı
$count_sql = "SELECT COUNT(*) FROM videolar $where_clause";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_videos = $count_stmt->fetchColumn();
$total_pages = ceil($total_videos / $per_page);

// Videoları çek
$videos_sql = "
    SELECT v.*, k.kategori_adi 
    FROM videolar v 
    LEFT JOIN kategoriler k ON v.kategori_id = k.id 
    $where_clause 
    ORDER BY v.ekleme_tarihi DESC 
    LIMIT $per_page OFFSET $offset
";
$videos_stmt = $pdo->prepare($videos_sql);
$videos_stmt->execute($params);
$videos = $videos_stmt->fetchAll();

// Kategorileri çek
$categories = $pdo->query("SELECT * FROM kategoriler WHERE durum = 'aktif' ORDER BY kategori_adi ASC")->fetchAll();

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - DOBİEN Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/admin-header.php'; ?>
    
    <div class="admin-container">
        <div class="admin-content">
            <div class="page-header">
                <h1><i class="fas fa-video me-2"></i>Video Yönetimi</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVideoModal">
                    <i class="fas fa-plus me-2"></i>Yeni Video Ekle
                </button>
            </div>

            <?php if (isset($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Filtreler -->
            <div class="filters-section mb-4">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="search" placeholder="Video ara..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="category" class="form-select">
                            <option value="">Tüm Kategoriler</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" 
                                        <?php echo $category_filter == $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['kategori_adi']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">Tüm Durumlar</option>
                            <option value="aktif" <?php echo $status_filter === 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                            <option value="pasif" <?php echo $status_filter === 'pasif' ? 'selected' : ''; ?>>Pasif</option>
                            <option value="beklemede" <?php echo $status_filter === 'beklemede' ? 'selected' : ''; ?>>Beklemede</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="fas fa-search"></i> Filtrele
                        </button>
                    </div>
                </form>
            </div>

            <!-- Video Listesi -->
            <div class="card">
                <div class="card-body">
                    <?php if (!empty($videos)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Kapak</th>
                                        <th>Başlık</th>
                                        <th>Kategori</th>
                                        <th>Durum</th>
                                        <th>İzlenme</th>
                                        <th>Tarih</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($videos as $video): ?>
                                        <tr>
                                            <td>
                                                <img src="<?php echo htmlspecialchars($video['kapak_resmi'] ?: '../assets/images/default-thumbnail.jpg'); ?>" 
                                                     alt="Kapak" class="thumbnail" style="width: 60px; height: 40px; object-fit: cover;">
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($video['baslik']); ?></strong>
                                                <?php if ($video['goruntulenme_yetkisi'] !== 'herkes'): ?>
                                                    <span class="badge bg-warning text-dark ms-1"><?php echo strtoupper($video['goruntulenme_yetkisi']); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($video['kategori_adi'] ?: 'Kategori Yok'); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $video['durum'] === 'aktif' ? 'success' : ($video['durum'] === 'beklemede' ? 'warning' : 'danger'); ?>">
                                                    <?php echo ucfirst($video['durum']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo number_format($video['izlenme_sayisi'] ?? 0); ?></td>
                                            <td><?php echo date('d.m.Y', strtotime($video['ekleme_tarihi'])); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary" onclick="editVideo(<?php echo $video['id']; ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger" onclick="deleteVideo(<?php echo $video['id']; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <a href="../video.php?id=<?php echo $video['id']; ?>" class="btn btn-outline-info" target="_blank">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <nav>
                                <ul class="pagination justify-content-center">
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_filter; ?>&status=<?php echo urlencode($status_filter); ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-video fa-3x text-muted mb-3"></i>
                            <h4>Hiç video bulunamadı</h4>
                            <p class="text-muted">Yeni video eklemek için yukarıdaki butonu kullanın.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Video Ekleme Modal -->
    <div class="modal fade" id="addVideoModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Yeni Video Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_video">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Video Başlığı *</label>
                                <input type="text" class="form-control" name="baslik" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kategori</label>
                                <select name="kategori_id" class="form-select">
                                    <option value="0">Kategori Seçin</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>">
                                            <?php echo htmlspecialchars($category['kategori_adi']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Video URL *</label>
                            <input type="url" class="form-control" name="video_url" required 
                                   placeholder="https://example.com/video.mp4">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Kapak Resmi URL</label>
                            <input type="url" class="form-control" name="kapak_resmi" 
                                   placeholder="https://example.com/thumbnail.jpg">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Açıklama</label>
                            <textarea class="form-control" name="aciklama" rows="4"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Durum</label>
                                <select name="durum" class="form-select">
                                    <option value="aktif">Aktif</option>
                                    <option value="pasif">Pasif</option>
                                    <option value="beklemede">Beklemede</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Görüntülenme Yetkisi</label>
                                <select name="goruntulenme_yetkisi" class="form-select">
                                    <option value="herkes">Herkes</option>
                                    <option value="vip">VIP Üyeler</option>
                                    <option value="premium">Premium Üyeler</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Video Ekle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteVideo(id) {
            if (confirm('Bu videoyu silmek istediğinizden emin misiniz?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_video">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function editVideo(id) {
            // Düzenleme modalı buraya eklenebilir
            alert('Düzenleme özelliği yakında eklenecek!');
        }
    </script>
</body>
</html>