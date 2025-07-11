<?php
/**
 * DOBİEN Video Platform - Admin Slider Yönetimi
 * Geliştirici: DOBİEN
 */

define('ADMIN_AREA', true);
session_start();

require_once '../includes/config.php';

$page_title = "Slider Yönetimi";

// Slider işlemleri
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $baslik = trim($_POST['baslik']);
                $aciklama = trim($_POST['aciklama']);
                $link = trim($_POST['link']);
                $buton_metni = trim($_POST['buton_metni']);
                $siralama = (int)$_POST['siralama'];
                
                // Resim yükleme
                $resim = '';
                if (isset($_FILES['resim']) && $_FILES['resim']['error'] == 0) {
                    $upload_dir = '../uploads/slider/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    $file_extension = strtolower(pathinfo($_FILES['resim']['name'], PATHINFO_EXTENSION));
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                    
                    if (in_array($file_extension, $allowed_extensions)) {
                        $resim = uniqid() . '.' . $file_extension;
                        $upload_path = $upload_dir . $resim;
                        
                        if (move_uploaded_file($_FILES['resim']['tmp_name'], $upload_path)) {
                            // Başarılı
                        } else {
                            $_SESSION['error'] = 'Resim yüklenirken hata oluştu!';
                            break;
                        }
                    } else {
                        $_SESSION['error'] = 'Geçersiz dosya formatı!';
                        break;
                    }
                }
                
                $insert_query = "INSERT INTO slider (baslik, aciklama, resim, link, buton_metni, siralama) VALUES (?, ?, ?, ?, ?, ?)";
                $insert_stmt = $pdo->prepare($insert_query);
                if ($insert_stmt->execute([$baslik, $aciklama, $resim, $link, $buton_metni, $siralama])) {
                    $_SESSION['success'] = 'Slider başarıyla eklendi!';
                } else {
                    $_SESSION['error'] = 'Slider eklenirken hata oluştu!';
                }
                break;
                
            case 'update':
                $id = (int)$_POST['id'];
                $baslik = trim($_POST['baslik']);
                $aciklama = trim($_POST['aciklama']);
                $link = trim($_POST['link']);
                $buton_metni = trim($_POST['buton_metni']);
                $siralama = (int)$_POST['siralama'];
                $durum = $_POST['durum'];
                
                // Mevcut slider bilgilerini al
                $current_query = "SELECT resim FROM slider WHERE id = ?";
                $current_stmt = $pdo->prepare($current_query);
                $current_stmt->execute([$id]);
                $current = $current_stmt->fetch();
                
                $resim = $current['resim'];
                
                // Yeni resim yüklendi mi?
                if (isset($_FILES['resim']) && $_FILES['resim']['error'] == 0) {
                    $upload_dir = '../uploads/slider/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    $file_extension = strtolower(pathinfo($_FILES['resim']['name'], PATHINFO_EXTENSION));
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                    
                    if (in_array($file_extension, $allowed_extensions)) {
                        // Eski resmi sil
                        if ($resim && file_exists($upload_dir . $resim)) {
                            unlink($upload_dir . $resim);
                        }
                        
                        $resim = uniqid() . '.' . $file_extension;
                        $upload_path = $upload_dir . $resim;
                        
                        if (!move_uploaded_file($_FILES['resim']['tmp_name'], $upload_path)) {
                            $_SESSION['error'] = 'Resim yüklenirken hata oluştu!';
                            break;
                        }
                    } else {
                        $_SESSION['error'] = 'Geçersiz dosya formatı!';
                        break;
                    }
                }
                
                $update_query = "UPDATE slider SET baslik = ?, aciklama = ?, resim = ?, link = ?, buton_metni = ?, siralama = ?, durum = ? WHERE id = ?";
                $update_stmt = $pdo->prepare($update_query);
                if ($update_stmt->execute([$baslik, $aciklama, $resim, $link, $buton_metni, $siralama, $durum, $id])) {
                    $_SESSION['success'] = 'Slider başarıyla güncellendi!';
                } else {
                    $_SESSION['error'] = 'Slider güncellenirken hata oluştu!';
                }
                break;
        }
    }
}

// Slider silme
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Resim dosyasını al
    $image_query = "SELECT resim FROM slider WHERE id = ?";
    $image_stmt = $pdo->prepare($image_query);
    $image_stmt->execute([$id]);
    $image_data = $image_stmt->fetch();
    
    $delete_query = "DELETE FROM slider WHERE id = ?";
    $delete_stmt = $pdo->prepare($delete_query);
    if ($delete_stmt->execute([$id])) {
        // Resim dosyasını sil
        if ($image_data && $image_data['resim'] && file_exists('../uploads/slider/' . $image_data['resim'])) {
            unlink('../uploads/slider/' . $image_data['resim']);
        }
        $_SESSION['success'] = 'Slider başarıyla silindi!';
    } else {
        $_SESSION['error'] = 'Slider silinirken hata oluştu!';
    }
    
    header('Location: slider.php');
    exit;
}

// Slider listesi
$sliders_query = "SELECT * FROM slider ORDER BY siralama ASC, id DESC";
$sliders = $pdo->query($sliders_query)->fetchAll();

include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-images"></i> Slider Yönetimi
    </h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSliderModal">
        <i class="fas fa-plus"></i> Yeni Slider Ekle
    </button>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Slider Listesi</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered data-table" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Resim</th>
                        <th>Başlık</th>
                        <th>Açıklama</th>
                        <th>Sıralama</th>
                        <th>Durum</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sliders as $slider): ?>
                    <tr>
                        <td><?php echo $slider['id']; ?></td>
                        <td>
                            <?php if ($slider['resim']): ?>
                                <img src="../uploads/slider/<?php echo $slider['resim']; ?>" 
                                     alt="<?php echo htmlspecialchars($slider['baslik']); ?>" 
                                     style="width: 100px; height: 60px; object-fit: cover;" 
                                     class="img-thumbnail">
                            <?php else: ?>
                                <span class="text-muted">Resim yok</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($slider['baslik']); ?></td>
                        <td><?php echo htmlspecialchars(substr($slider['aciklama'], 0, 100)); ?>...</td>
                        <td><?php echo $slider['siralama']; ?></td>
                        <td>
                            <span class="badge badge-<?php echo $slider['durum'] == 'aktif' ? 'success' : 'secondary'; ?>">
                                <?php echo ucfirst($slider['durum']); ?>
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-warning" 
                                    onclick="editSlider(<?php echo htmlspecialchars(json_encode($slider)); ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="?delete=<?php echo $slider['id']; ?>" 
                               class="btn btn-sm btn-danger delete-btn">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Slider Ekleme Modal -->
<div class="modal fade" id="addSliderModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Slider Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="baslik" class="form-label">Başlık *</label>
                                <input type="text" class="form-control" id="baslik" name="baslik" required>
                                <div class="invalid-feedback">Başlık gereklidir.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="siralama" class="form-label">Sıralama</label>
                                <input type="number" class="form-control" id="siralama" name="siralama" value="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="aciklama" class="form-label">Açıklama</label>
                        <textarea class="form-control" id="aciklama" name="aciklama" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="link" class="form-label">Link</label>
                                <input type="url" class="form-control" id="link" name="link">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="buton_metni" class="form-label">Buton Metni</label>
                                <input type="text" class="form-control" id="buton_metni" name="buton_metni">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="resim" class="form-label">Slider Resmi *</label>
                        <input type="file" class="form-control" id="resim" name="resim" accept="image/*" required>
                        <div class="invalid-feedback">Slider resmi gereklidir.</div>
                        <small class="form-text text-muted">Önerilen boyut: 1920x800px</small>
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

<!-- Slider Düzenleme Modal -->
<div class="modal fade" id="editSliderModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Slider Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_baslik" class="form-label">Başlık *</label>
                                <input type="text" class="form-control" id="edit_baslik" name="baslik" required>
                                <div class="invalid-feedback">Başlık gereklidir.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_siralama" class="form-label">Sıralama</label>
                                <input type="number" class="form-control" id="edit_siralama" name="siralama">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_aciklama" class="form-label">Açıklama</label>
                        <textarea class="form-control" id="edit_aciklama" name="aciklama" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_link" class="form-label">Link</label>
                                <input type="url" class="form-control" id="edit_link" name="link">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_buton_metni" class="form-label">Buton Metni</label>
                                <input type="text" class="form-control" id="edit_buton_metni" name="buton_metni">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_durum" class="form-label">Durum</label>
                                <select class="form-control" id="edit_durum" name="durum">
                                    <option value="aktif">Aktif</option>
                                    <option value="pasif">Pasif</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_resim" class="form-label">Yeni Resim (Opsiyonel)</label>
                                <input type="file" class="form-control" id="edit_resim" name="resim" accept="image/*">
                                <small class="form-text text-muted">Yeni resim yüklemezseniz mevcut resim korunur.</small>
                            </div>
                        </div>
                    </div>
                    
                    <div id="current_image" class="mb-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Slider Güncelle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editSlider(slider) {
    $('#edit_id').val(slider.id);
    $('#edit_baslik').val(slider.baslik);
    $('#edit_aciklama').val(slider.aciklama);
    $('#edit_link').val(slider.link);
    $('#edit_buton_metni').val(slider.buton_metni);
    $('#edit_siralama').val(slider.siralama);
    $('#edit_durum').val(slider.durum);
    
    if (slider.resim) {
        $('#current_image').html('<label>Mevcut Resim:</label><br><img src="../uploads/slider/' + slider.resim + '" style="max-width: 300px; height: auto;" class="img-thumbnail">');
    } else {
        $('#current_image').html('');
    }
    
    $('#editSliderModal').modal('show');
}
</script>

<?php include 'includes/footer.php'; ?>