<?php
/**
 * DOBİEN Video Platform - Slider Yönetimi
 * Geliştirici: DOBİEN
 * Modern Video Paylaşım Platformu
 */

require_once '../includes/config.php';

// Admin kontrolü
if (!isAdmin()) {
    header('Location: giris.php');
    exit;
}

$page = 'slider';
$current_admin = checkAdminSession();

// CRUD İşlemleri
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $baslik = $_POST['baslik'] ?? '';
                $aciklama = $_POST['aciklama'] ?? '';
                $link = $_POST['link'] ?? '';
                $buton_metni = $_POST['buton_metni'] ?? 'İzle';
                $video_id = $_POST['video_id'] ?? null;
                $siralama = (int)($_POST['siralama'] ?? 0);
                $durum = $_POST['durum'] ?? 'aktif';
                
                // Resim upload işlemi
                $resim = '';
                if (isset($_FILES['resim']) && $_FILES['resim']['error'] == 0) {
                    $upload_dir = '../uploads/slider/';
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                    
                    $file_ext = pathinfo($_FILES['resim']['name'], PATHINFO_EXTENSION);
                    $file_name = 'slider_' . time() . '.' . $file_ext;
                    $target_file = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($_FILES['resim']['tmp_name'], $target_file)) {
                        $resim = 'uploads/slider/' . $file_name;
                    }
                }
                
                try {
                    $stmt = $pdo->prepare("INSERT INTO slider (baslik, aciklama, resim, link, buton_metni, video_id, siralama, durum) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$baslik, $aciklama, $resim, $link, $buton_metni, $video_id, $siralama, $durum]);
                    $success_message = 'Slider başarıyla eklendi.';
                } catch (PDOException $e) {
                    $error_message = 'Hata: ' . $e->getMessage();
                }
                break;
                
            case 'edit':
                $id = (int)$_POST['id'];
                $baslik = $_POST['baslik'] ?? '';
                $aciklama = $_POST['aciklama'] ?? '';
                $link = $_POST['link'] ?? '';
                $buton_metni = $_POST['buton_metni'] ?? 'İzle';
                $video_id = $_POST['video_id'] ?? null;
                $siralama = (int)($_POST['siralama'] ?? 0);
                $durum = $_POST['durum'] ?? 'aktif';
                
                // Mevcut resmi al
                $current_image = $pdo->prepare("SELECT resim FROM slider WHERE id = ?");
                $current_image->execute([$id]);
                $resim = $current_image->fetchColumn();
                
                // Yeni resim upload edildi mi?
                if (isset($_FILES['resim']) && $_FILES['resim']['error'] == 0) {
                    $upload_dir = '../uploads/slider/';
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                    
                    // Eski resmi sil
                    if ($resim && file_exists('../' . $resim)) {
                        unlink('../' . $resim);
                    }
                    
                    $file_ext = pathinfo($_FILES['resim']['name'], PATHINFO_EXTENSION);
                    $file_name = 'slider_' . time() . '.' . $file_ext;
                    $target_file = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($_FILES['resim']['tmp_name'], $target_file)) {
                        $resim = 'uploads/slider/' . $file_name;
                    }
                }
                
                try {
                    $stmt = $pdo->prepare("UPDATE slider SET baslik = ?, aciklama = ?, resim = ?, link = ?, buton_metni = ?, video_id = ?, siralama = ?, durum = ?, guncelleme_tarihi = NOW() WHERE id = ?");
                    $stmt->execute([$baslik, $aciklama, $resim, $link, $buton_metni, $video_id, $siralama, $durum, $id]);
                    $success_message = 'Slider başarıyla güncellendi.';
                } catch (PDOException $e) {
                    $error_message = 'Hata: ' . $e->getMessage();
                }
                break;
                
            case 'delete':
                $id = (int)$_POST['id'];
                try {
                    // Resmi sil
                    $current_image = $pdo->prepare("SELECT resim FROM slider WHERE id = ?");
                    $current_image->execute([$id]);
                    $resim = $current_image->fetchColumn();
                    
                    if ($resim && file_exists('../' . $resim)) {
                        unlink('../' . $resim);
                    }
                    
                    $stmt = $pdo->prepare("DELETE FROM slider WHERE id = ?");
                    $stmt->execute([$id]);
                    $success_message = 'Slider başarıyla silindi.';
                } catch (PDOException $e) {
                    $error_message = 'Hata: ' . $e->getMessage();
                }
                break;
        }
    }
}

// Slider listesini çek
try {
    $sliders = $pdo->query("SELECT s.*, v.baslik as video_baslik FROM slider s LEFT JOIN videolar v ON s.video_id = v.id ORDER BY s.siralama ASC, s.id DESC")->fetchAll();
} catch (PDOException $e) {
    $sliders = [];
    $error_message = 'Slider verileri alınamadı: ' . $e->getMessage();
}

// Video listesini çek (select için)
try {
    $videos = $pdo->query("SELECT id, baslik FROM videolar WHERE durum = 'aktif' ORDER BY baslik ASC")->fetchAll();
} catch (PDOException $e) {
    $videos = [];
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slider Yönetimi - DOBİEN Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">
    <style>
        .slider-image {
            max-width: 100px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        .form-container {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/topbar.php'; ?>
            
            <div class="content">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h3 mb-0">Slider Yönetimi</h1>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                            <i class="fas fa-plus"></i> Yeni Slider Ekle
                        </button>
                    </div>

                    <?php if (isset($success_message)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Resim</th>
                                            <th>Başlık</th>
                                            <th>Açıklama</th>
                                            <th>Video</th>
                                            <th>Sıralama</th>
                                            <th>Durum</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($sliders as $slider): ?>
                                            <tr>
                                                <td>
                                                    <?php if ($slider['resim']): ?>
                                                        <img src="../<?php echo htmlspecialchars($slider['resim']); ?>" alt="Slider" class="slider-image">
                                                    <?php else: ?>
                                                        <div class="bg-light d-flex align-items-center justify-content-center slider-image">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($slider['baslik']); ?></td>
                                                <td><?php echo htmlspecialchars(substr($slider['aciklama'], 0, 50)) . '...'; ?></td>
                                                <td><?php echo $slider['video_baslik'] ? htmlspecialchars($slider['video_baslik']) : 'Link'; ?></td>
                                                <td><?php echo $slider['siralama']; ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $slider['durum'] == 'aktif' ? 'success' : 'danger'; ?>">
                                                        <?php echo ucfirst($slider['durum']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary edit-btn" data-slider='<?php echo json_encode($slider); ?>'>
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form method="POST" style="display: inline-block;" onsubmit="return confirm('Bu slider silinecek. Emin misiniz?')">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?php echo $slider['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Yeni Slider Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="mb-3">
                            <label for="baslik" class="form-label">Başlık *</label>
                            <input type="text" class="form-control" id="baslik" name="baslik" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="aciklama" class="form-label">Açıklama</label>
                            <textarea class="form-control" id="aciklama" name="aciklama" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="resim" class="form-label">Slider Resmi *</label>
                            <input type="file" class="form-control" id="resim" name="resim" accept="image/*" required>
                            <div class="form-text">Önerilen boyut: 1920x1080px</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="video_id" class="form-label">Video</label>
                                    <select class="form-select" id="video_id" name="video_id">
                                        <option value="">Video Seçin (opsiyonel)</option>
                                        <?php foreach ($videos as $video): ?>
                                            <option value="<?php echo $video['id']; ?>"><?php echo htmlspecialchars($video['baslik']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="link" class="form-label">Özel Link</label>
                                    <input type="url" class="form-control" id="link" name="link" placeholder="https://...">
                                    <div class="form-text">Video seçilmemişse bu link kullanılır</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="buton_metni" class="form-label">Buton Metni</label>
                                    <input type="text" class="form-control" id="buton_metni" name="buton_metni" value="İzle">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="siralama" class="form-label">Sıralama</label>
                                    <input type="number" class="form-control" id="siralama" name="siralama" value="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="durum" class="form-label">Durum</label>
                                    <select class="form-select" id="durum" name="durum">
                                        <option value="aktif">Aktif</option>
                                        <option value="pasif">Pasif</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-primary">Slider Ekle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Slider Düzenle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data" id="editForm">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit_id">
                        
                        <div class="mb-3">
                            <label for="edit_baslik" class="form-label">Başlık *</label>
                            <input type="text" class="form-control" id="edit_baslik" name="baslik" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_aciklama" class="form-label">Açıklama</label>
                            <textarea class="form-control" id="edit_aciklama" name="aciklama" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_resim" class="form-label">Slider Resmi</label>
                            <input type="file" class="form-control" id="edit_resim" name="resim" accept="image/*">
                            <div class="form-text">Yeni resim seçilmezse mevcut resim korunur</div>
                            <div id="current_image"></div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_video_id" class="form-label">Video</label>
                                    <select class="form-select" id="edit_video_id" name="video_id">
                                        <option value="">Video Seçin (opsiyonel)</option>
                                        <?php foreach ($videos as $video): ?>
                                            <option value="<?php echo $video['id']; ?>"><?php echo htmlspecialchars($video['baslik']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_link" class="form-label">Özel Link</label>
                                    <input type="url" class="form-control" id="edit_link" name="link" placeholder="https://...">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_buton_metni" class="form-label">Buton Metni</label>
                                    <input type="text" class="form-control" id="edit_buton_metni" name="buton_metni">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_siralama" class="form-label">Sıralama</label>
                                    <input type="number" class="form-control" id="edit_siralama" name="siralama">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_durum" class="form-label">Durum</label>
                                    <select class="form-select" id="edit_durum" name="durum">
                                        <option value="aktif">Aktif</option>
                                        <option value="pasif">Pasif</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-primary">Güncelle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Edit button click handler
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const slider = JSON.parse(this.dataset.slider);
                
                document.getElementById('edit_id').value = slider.id;
                document.getElementById('edit_baslik').value = slider.baslik;
                document.getElementById('edit_aciklama').value = slider.aciklama || '';
                document.getElementById('edit_video_id').value = slider.video_id || '';
                document.getElementById('edit_link').value = slider.link || '';
                document.getElementById('edit_buton_metni').value = slider.buton_metni || 'İzle';
                document.getElementById('edit_siralama').value = slider.siralama || 0;
                document.getElementById('edit_durum').value = slider.durum;
                
                // Show current image
                const currentImageDiv = document.getElementById('current_image');
                if (slider.resim) {
                    currentImageDiv.innerHTML = `<div class="mt-2"><strong>Mevcut resim:</strong><br><img src="../${slider.resim}" class="slider-image"></div>`;
                } else {
                    currentImageDiv.innerHTML = '';
                }
                
                new bootstrap.Modal(document.getElementById('editModal')).show();
            });
        });
    </script>
</body>
</html>