<?php
/**
 * DOBİEN Video Platform - Slider Yönetimi
 * Geliştirici: DOBİEN
 * Admin Panel - Slider Yönetimi
 * Tüm Hakları Saklıdır © DOBİEN
 */

require_once '../includes/config.php';

// Admin giriş kontrolü
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: giris.php');
    exit;
}

// Admin bilgilerini al
$admin_user = checkAdminSession();
if (!$admin_user) {
    header('Location: giris.php');
    exit;
}

// Slider tablosu yoksa oluştur
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS slider (
            id int(11) NOT NULL AUTO_INCREMENT,
            baslik varchar(255) NOT NULL,
            aciklama text,
            resim varchar(255) NOT NULL,
            link varchar(255),
            buton_metni varchar(100),
            siralama int(11) DEFAULT 0,
            durum enum('aktif','pasif') DEFAULT 'aktif',
            olusturma_tarihi timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
} catch (PDOException $e) {
    // Tablo oluşturulamadıysa devam et
}

// CRUD işlemleri
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            try {
                $baslik = trim($_POST['baslik'] ?? '');
                $aciklama = trim($_POST['aciklama'] ?? '');
                $link = trim($_POST['link'] ?? '');
                $buton_metni = trim($_POST['buton_metni'] ?? 'İzle');
                $siralama = (int)($_POST['siralama'] ?? 0);
                $durum = $_POST['durum'] ?? 'aktif';
                
                if (empty($baslik)) {
                    throw new Exception('Başlık alanı zorunludur.');
                }
                
                // Resim yükleme
                $resim_adi = '';
                if (isset($_FILES['resim']) && $_FILES['resim']['error'] == 0) {
                    $upload_dir = '../uploads/slider/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $file_extension = strtolower(pathinfo($_FILES['resim']['name'], PATHINFO_EXTENSION));
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                    
                    if (!in_array($file_extension, $allowed_extensions)) {
                        throw new Exception('Sadece JPG, JPEG, PNG, WebP ve GIF dosyaları kabul edilir.');
                    }
                    
                    $resim_adi = 'slider_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
                    $upload_path = $upload_dir . $resim_adi;
                    
                    if (!move_uploaded_file($_FILES['resim']['tmp_name'], $upload_path)) {
                        throw new Exception('Resim yüklenirken hata oluştu.');
                    }
                } else {
                    throw new Exception('Slider resmi zorunludur.');
                }
                
                // Slider ekle
                $insert_stmt = $pdo->prepare("
                    INSERT INTO slider (baslik, aciklama, resim, link, buton_metni, siralama, durum, olusturma_tarihi) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                $insert_stmt->execute([$baslik, $aciklama, $resim_adi, $link, $buton_metni, $siralama, $durum]);
                
                $message = 'Slider başarıyla eklendi.';
                $message_type = 'success';
                
            } catch (Exception $e) {
                $message = $e->getMessage();
                $message_type = 'error';
            }
            break;
            
        case 'edit':
            try {
                $id = (int)$_POST['id'];
                $baslik = trim($_POST['baslik'] ?? '');
                $aciklama = trim($_POST['aciklama'] ?? '');
                $link = trim($_POST['link'] ?? '');
                $buton_metni = trim($_POST['buton_metni'] ?? 'İzle');
                $siralama = (int)($_POST['siralama'] ?? 0);
                $durum = $_POST['durum'] ?? 'aktif';
                
                if (empty($baslik)) {
                    throw new Exception('Başlık alanı zorunludur.');
                }
                
                // Mevcut slider bilgilerini al
                $current_stmt = $pdo->prepare("SELECT * FROM slider WHERE id = ?");
                $current_stmt->execute([$id]);
                $current_slider = $current_stmt->fetch();
                
                if (!$current_slider) {
                    throw new Exception('Slider bulunamadı.');
                }
                
                $resim_adi = $current_slider['resim'];
                
                // Yeni resim yüklendi mi?
                if (isset($_FILES['resim']) && $_FILES['resim']['error'] == 0) {
                    $upload_dir = '../uploads/slider/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $file_extension = strtolower(pathinfo($_FILES['resim']['name'], PATHINFO_EXTENSION));
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                    
                    if (!in_array($file_extension, $allowed_extensions)) {
                        throw new Exception('Sadece JPG, JPEG, PNG, WebP ve GIF dosyaları kabul edilir.');
                    }
                    
                    $new_resim_adi = 'slider_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_resim_adi;
                    
                    if (move_uploaded_file($_FILES['resim']['tmp_name'], $upload_path)) {
                        // Eski resmi sil
                        if ($resim_adi && file_exists($upload_dir . $resim_adi)) {
                            unlink($upload_dir . $resim_adi);
                        }
                        $resim_adi = $new_resim_adi;
                    }
                }
                
                // Slider güncelle
                $update_stmt = $pdo->prepare("
                    UPDATE slider 
                    SET baslik = ?, aciklama = ?, resim = ?, link = ?, buton_metni = ?, siralama = ?, durum = ? 
                    WHERE id = ?
                ");
                $update_stmt->execute([$baslik, $aciklama, $resim_adi, $link, $buton_metni, $siralama, $durum, $id]);
                
                $message = 'Slider başarıyla güncellendi.';
                $message_type = 'success';
                
            } catch (Exception $e) {
                $message = $e->getMessage();
                $message_type = 'error';
            }
            break;
            
        case 'delete':
            try {
                $id = (int)$_POST['id'];
                
                // Slider bilgilerini al
                $stmt = $pdo->prepare("SELECT resim FROM slider WHERE id = ?");
                $stmt->execute([$id]);
                $slider = $stmt->fetch();
                
                if ($slider) {
                    // Resmi sil
                    $upload_dir = '../uploads/slider/';
                    if ($slider['resim'] && file_exists($upload_dir . $slider['resim'])) {
                        unlink($upload_dir . $slider['resim']);
                    }
                    
                    // Slider'ı sil
                    $delete_stmt = $pdo->prepare("DELETE FROM slider WHERE id = ?");
                    $delete_stmt->execute([$id]);
                    
                    $message = 'Slider başarıyla silindi.';
                    $message_type = 'success';
                } else {
                    throw new Exception('Slider bulunamadı.');
                }
                
            } catch (Exception $e) {
                $message = $e->getMessage();
                $message_type = 'error';
            }
            break;
            
        case 'toggle_status':
            try {
                $id = (int)$_POST['id'];
                $new_status = $_POST['status'] == 'aktif' ? 'pasif' : 'aktif';
                
                $update_stmt = $pdo->prepare("UPDATE slider SET durum = ? WHERE id = ?");
                $update_stmt->execute([$new_status, $id]);
                
                $message = 'Slider durumu güncellendi.';
                $message_type = 'success';
                
            } catch (Exception $e) {
                $message = $e->getMessage();
                $message_type = 'error';
            }
            break;
    }
}

// Slider listesini çek
try {
    $sliders = $pdo->query("SELECT * FROM slider ORDER BY siralama ASC, id DESC")->fetchAll();
} catch (PDOException $e) {
    $sliders = [];
}

$page_title = "Slider Yönetimi";
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - DOBİEN Admin Panel</title>
    
    <link rel="stylesheet" href="assets/css/admin.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="admin-body">

<div class="admin-wrapper">
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="admin-main">
        <?php include 'includes/topbar.php'; ?>
        
        <div class="admin-content">
            <div class="page-header">
                <h1><i class="fas fa-images"></i> Slider Yönetimi</h1>
                <button class="btn btn-primary" data-modal="sliderModal">
                    <i class="fas fa-plus"></i> Yeni Slider Ekle
                </button>
            </div>
            
            <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible">
                <button class="alert-close">&times;</button>
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>
            
            <div class="dashboard-card">
                <div class="card-header">
                    <h3>Slider Listesi</h3>
                    <p>Ana sayfada görüntülenecek slider resimlerini yönetin</p>
                </div>
                <div class="card-content">
                    <?php if (!empty($sliders)): ?>
                    <div class="slider-grid">
                        <?php foreach ($sliders as $slider): ?>
                        <div class="slider-item">
                            <div class="slider-image">
                                <img src="../uploads/slider/<?php echo htmlspecialchars($slider['resim']); ?>" 
                                     alt="<?php echo htmlspecialchars($slider['baslik']); ?>"
                                     onerror="this.src='../assets/images/no-image.jpg'">
                                <div class="slider-overlay">
                                    <div class="slider-actions">
                                        <button class="btn btn-sm btn-primary edit-slider-btn" 
                                                data-id="<?php echo $slider['id']; ?>"
                                                data-baslik="<?php echo htmlspecialchars($slider['baslik']); ?>"
                                                data-aciklama="<?php echo htmlspecialchars($slider['aciklama']); ?>"
                                                data-link="<?php echo htmlspecialchars($slider['link']); ?>"
                                                data-buton-metni="<?php echo htmlspecialchars($slider['buton_metni']); ?>"
                                                data-siralama="<?php echo $slider['siralama']; ?>"
                                                data-durum="<?php echo $slider['durum']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-<?php echo $slider['durum'] == 'aktif' ? 'warning' : 'success'; ?> toggle-status-btn"
                                                data-id="<?php echo $slider['id']; ?>"
                                                data-status="<?php echo $slider['durum']; ?>">
                                            <i class="fas fa-<?php echo $slider['durum'] == 'aktif' ? 'eye-slash' : 'eye'; ?>"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-slider-btn" 
                                                data-id="<?php echo $slider['id']; ?>"
                                                data-title="<?php echo htmlspecialchars($slider['baslik']); ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="slider-status">
                                    <span class="badge badge-<?php echo $slider['durum'] == 'aktif' ? 'success' : 'warning'; ?>">
                                        <?php echo ucfirst($slider['durum']); ?>
                                    </span>
                                    <span class="slider-order">#<?php echo $slider['siralama']; ?></span>
                                </div>
                            </div>
                            <div class="slider-info">
                                <h4><?php echo htmlspecialchars($slider['baslik']); ?></h4>
                                <p><?php echo htmlspecialchars(substr($slider['aciklama'], 0, 100)); ?><?php echo strlen($slider['aciklama']) > 100 ? '...' : ''; ?></p>
                                <?php if ($slider['link']): ?>
                                <div class="slider-link">
                                    <i class="fas fa-link"></i>
                                    <span><?php echo htmlspecialchars($slider['buton_metni']); ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="slider-date">
                                    <i class="fas fa-calendar"></i>
                                    <?php echo formatDate($slider['olusturma_tarihi'], 'd.m.Y'); ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-images"></i>
                        <h3>Henüz slider eklenmemiş</h3>
                        <p>Ana sayfanızı daha çekici hale getirmek için slider ekleyin.</p>
                        <button class="btn btn-primary" data-modal="sliderModal">
                            <i class="fas fa-plus"></i> İlk Slider'ı Ekle
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Slider Ekleme/Düzenleme Modal -->
<div class="modal" id="sliderModal">
    <div class="modal-overlay"></div>
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h3 id="modalTitle">Yeni Slider Ekle</h3>
            <button class="modal-close">&times;</button>
        </div>
        <form id="sliderForm" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
                <input type="hidden" name="action" id="action" value="add">
                <input type="hidden" name="id" id="sliderId">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="baslik">Başlık *</label>
                        <input type="text" id="baslik" name="baslik" required maxlength="255">
                    </div>
                    
                    <div class="form-group">
                        <label for="siralama">Sıralama</label>
                        <input type="number" id="siralama" name="siralama" value="0" min="0">
                        <small>Küçük sayılar önce görüntülenir</small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="aciklama">Açıklama</label>
                    <textarea id="aciklama" name="aciklama" rows="3" maxlength="500"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="resim">Slider Resmi *</label>
                    <div class="file-upload-area">
                        <input type="file" id="resim" name="resim" accept="image/*">
                        <div class="upload-text">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Resim seçin veya sürükleyip bırakın</p>
                            <small>Önerilen boyut: 1920x800px</small>
                        </div>
                    </div>
                    <div id="imagePreview" class="image-preview"></div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="link">Link URL</label>
                        <input type="url" id="link" name="link" placeholder="https://example.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="buton_metni">Buton Metni</label>
                        <input type="text" id="buton_metni" name="buton_metni" value="İzle" maxlength="100">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="durum">Durum</label>
                    <select id="durum" name="durum">
                        <option value="aktif">Aktif</option>
                        <option value="pasif">Pasif</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close">İptal</button>
                <button type="submit" class="btn btn-primary">Kaydet</button>
            </div>
        </form>
    </div>
</div>

<!-- Silme Onay Modal -->
<div class="modal" id="deleteModal">
    <div class="modal-overlay"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3>Slider Sil</h3>
            <button class="modal-close">&times;</button>
        </div>
        <form id="deleteForm" method="POST">
            <div class="modal-body">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="deleteSliderId">
                
                <div class="delete-confirmation">
                    <i class="fas fa-exclamation-triangle text-danger"></i>
                    <p>Bu slider'ı silmek istediğinizden emin misiniz?</p>
                    <p><strong id="deleteSliderTitle"></strong></p>
                    <p class="text-danger">Bu işlem geri alınamaz ve resim dosyası da silinecektir!</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close">İptal</button>
                <button type="submit" class="btn btn-danger">Sil</button>
            </div>
        </form>
    </div>
</div>

<style>
.slider-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.slider-item {
    background: var(--card-bg);
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.slider-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

.slider-image {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.slider-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.slider-item:hover .slider-image img {
    transform: scale(1.05);
}

.slider-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.slider-item:hover .slider-overlay {
    opacity: 1;
}

.slider-actions {
    display: flex;
    gap: 10px;
}

.slider-status {
    position: absolute;
    top: 10px;
    left: 10px;
    display: flex;
    gap: 5px;
}

.slider-order {
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.7rem;
}

.slider-info {
    padding: 15px;
}

.slider-info h4 {
    margin: 0 0 10px 0;
    font-size: 1.1rem;
    color: var(--text-primary);
}

.slider-info p {
    margin: 0 0 10px 0;
    color: var(--text-secondary);
    font-size: 0.9rem;
    line-height: 1.4;
}

.slider-link, .slider-date {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 0.8rem;
    color: var(--text-secondary);
    margin-bottom: 5px;
}

.file-upload-area {
    border: 2px dashed #ddd;
    border-radius: 8px;
    padding: 30px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.file-upload-area:hover {
    border-color: var(--primary-color);
    background: rgba(var(--primary-rgb), 0.05);
}

.file-upload-area input[type="file"] {
    display: none;
}

.upload-text i {
    font-size: 2rem;
    color: var(--primary-color);
    margin-bottom: 10px;
}

.image-preview {
    margin-top: 15px;
    text-align: center;
}

.image-preview img {
    max-width: 300px;
    max-height: 150px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--text-secondary);
}

.empty-state i {
    font-size: 4rem;
    color: var(--primary-color);
    margin-bottom: 20px;
}

.empty-state h3 {
    margin-bottom: 10px;
    color: var(--text-primary);
}

.modal-large .modal-content {
    max-width: 700px;
}

@media (max-width: 768px) {
    .slider-grid {
        grid-template-columns: 1fr;
    }
    
    .slider-actions {
        flex-direction: column;
    }
}
</style>

<script src="assets/js/admin.js"></script>
<script>
// Slider yönetimi JavaScript
$(document).ready(function() {
    // Alert kapatma
    $('.alert-close').click(function() {
        $(this).parent().fadeOut();
    });
    
    // Dosya seçimi ve önizleme
    $('#resim').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').html('<img src="' + e.target.result + '" alt="Önizleme">');
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Dosya yükleme alanı
    $('.file-upload-area').click(function() {
        $('#resim').click();
    });
    
    // Düzenleme butonu
    $('.edit-slider-btn').click(function() {
        const id = $(this).data('id');
        const baslik = $(this).data('baslik');
        const aciklama = $(this).data('aciklama');
        const link = $(this).data('link');
        const butonMetni = $(this).data('buton-metni');
        const siralama = $(this).data('siralama');
        const durum = $(this).data('durum');
        
        $('#modalTitle').text('Slider Düzenle');
        $('#action').val('edit');
        $('#sliderId').val(id);
        $('#baslik').val(baslik);
        $('#aciklama').val(aciklama);
        $('#link').val(link);
        $('#buton_metni').val(butonMetni);
        $('#siralama').val(siralama);
        $('#durum').val(durum);
        $('#resim').removeAttr('required');
        $('#imagePreview').empty();
        
        $('#sliderModal').addClass('show');
    });
    
    // Yeni slider butonu
    $('[data-modal="sliderModal"]').click(function() {
        $('#modalTitle').text('Yeni Slider Ekle');
        $('#action').val('add');
        $('#sliderForm')[0].reset();
        $('#sliderId').val('');
        $('#resim').attr('required', 'required');
        $('#imagePreview').empty();
        
        $('#sliderModal').addClass('show');
    });
    
    // Durum değiştirme
    $('.toggle-status-btn').click(function() {
        const id = $(this).data('id');
        const status = $(this).data('status');
        
        $.post('', {
            action: 'toggle_status',
            id: id,
            status: status
        }, function() {
            location.reload();
        });
    });
    
    // Silme butonu
    $('.delete-slider-btn').click(function() {
        const id = $(this).data('id');
        const title = $(this).data('title');
        
        $('#deleteSliderId').val(id);
        $('#deleteSliderTitle').text(title);
        
        $('#deleteModal').addClass('show');
    });
    
    // Modal kapatma
    $('.modal-close, .modal-overlay').click(function() {
        $('.modal').removeClass('show');
    });
    
    // ESC ile modal kapatma
    $(document).keydown(function(e) {
        if (e.key === 'Escape') {
            $('.modal').removeClass('show');
        }
    });
});
</script>

</body>
</html>