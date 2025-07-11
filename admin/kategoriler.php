<?php
/**
 * DOBİEN Video Platform - Admin Kategori Yönetimi
 * Geliştirici: DOBİEN
 * Tüm Hakları Saklıdır © DOBİEN
 */

require_once '../includes/config.php';

// Admin kontrolü
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: giris.php');
    exit;
}

$page_title = "Kategori Yönetimi";
$success_message = '';
$error_message = '';

// Kategori işlemleri
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_category'])) {
        // Kategori ekleme
        $kategori_adi = trim($_POST['kategori_adi']);
        $aciklama = trim($_POST['aciklama']);
        $siralama = (int)$_POST['siralama'];
        $durum = $_POST['durum'];
        
        if (empty($kategori_adi)) {
            $error_message = 'Kategori adı zorunludur.';
        } else {
            // Slug oluştur
            $slug = createSlug($kategori_adi);
            
            // Slug kontrolü
            $stmt = $pdo->prepare("SELECT id FROM kategoriler WHERE slug = ?");
            $stmt->execute([$slug]);
            if ($stmt->fetch()) {
                $error_message = 'Bu kategori adı zaten kullanılıyor.';
            } else {
                // Resim upload
                $resim = null;
                if (isset($_FILES['resim']) && $_FILES['resim']['error'] === 0) {
                    $upload_dir = '../uploads/categories/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $file_extension = strtolower(pathinfo($_FILES['resim']['name'], PATHINFO_EXTENSION));
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
                    
                    if (in_array($file_extension, $allowed_extensions)) {
                        $resim = uniqid() . '.' . $file_extension;
                        $upload_path = $upload_dir . $resim;
                        
                        if (!move_uploaded_file($_FILES['resim']['tmp_name'], $upload_path)) {
                            $resim = null;
                        }
                    }
                }
                
                try {
                    $stmt = $pdo->prepare("
                        INSERT INTO kategoriler (kategori_adi, slug, aciklama, resim, siralama, durum, ekleme_tarihi) 
                        VALUES (?, ?, ?, ?, ?, ?, NOW())
                    ");
                    $stmt->execute([$kategori_adi, $slug, $aciklama, $resim, $siralama, $durum]);
                    
                    $success_message = 'Kategori başarıyla eklendi.';
                } catch (PDOException $e) {
                    $error_message = 'Kategori eklenirken hata oluştu: ' . $e->getMessage();
                }
            }
        }
    }
    
    if (isset($_POST['update_category'])) {
        // Kategori güncelleme
        $kategori_id = $_POST['kategori_id'];
        $kategori_adi = trim($_POST['kategori_adi']);
        $aciklama = trim($_POST['aciklama']);
        $siralama = (int)$_POST['siralama'];
        $durum = $_POST['durum'];
        
        if (empty($kategori_adi)) {
            $error_message = 'Kategori adı zorunludur.';
        } else {
            // Slug oluştur
            $slug = createSlug($kategori_adi);
            
            // Slug kontrolü (diğer kategorilerde)
            $stmt = $pdo->prepare("SELECT id FROM kategoriler WHERE slug = ? AND id != ?");
            $stmt->execute([$slug, $kategori_id]);
            if ($stmt->fetch()) {
                $error_message = 'Bu kategori adı başka bir kategori tarafından kullanılıyor.';
            } else {
                try {
                    $stmt = $pdo->prepare("
                        UPDATE kategoriler SET kategori_adi = ?, slug = ?, aciklama = ?, siralama = ?, durum = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$kategori_adi, $slug, $aciklama, $siralama, $durum, $kategori_id]);
                    
                    $success_message = 'Kategori başarıyla güncellendi.';
                } catch (PDOException $e) {
                    $error_message = 'Kategori güncellenirken hata oluştu: ' . $e->getMessage();
                }
            }
        }
    }
    
    if (isset($_POST['delete_category'])) {
        // Kategori silme
        $kategori_id = $_POST['kategori_id'];
        
        try {
            // Kategoriye bağlı video kontrolü
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM videolar WHERE kategori_id = ?");
            $stmt->execute([$kategori_id]);
            $video_count = $stmt->fetchColumn();
            
            if ($video_count > 0) {
                $error_message = 'Bu kategoriye bağlı ' . $video_count . ' adet video bulunuyor. Önce videoları başka kategoriye taşıyın.';
            } else {
                // Önce resmi sil
                $stmt = $pdo->prepare("SELECT resim FROM kategoriler WHERE id = ?");
                $stmt->execute([$kategori_id]);
                $category = $stmt->fetch();
                
                if ($category && $category['resim']) {
                    $file_path = '../uploads/categories/' . $category['resim'];
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
                
                // Kategoriyi sil
                $stmt = $pdo->prepare("DELETE FROM kategoriler WHERE id = ?");
                $stmt->execute([$kategori_id]);
                
                $success_message = 'Kategori başarıyla silindi.';
            }
        } catch (PDOException $e) {
            $error_message = 'Kategori silinirken hata oluştu: ' . $e->getMessage();
        }
    }
}

// Kategorileri çek
$categories = $pdo->query("
    SELECT k.*, 
           COUNT(v.id) as video_count 
    FROM kategoriler k 
    LEFT JOIN videolar v ON k.id = v.kategori_id 
    GROUP BY k.id 
    ORDER BY k.siralama ASC, k.kategori_adi ASC
")->fetchAll();

// İstatistikler
$stats = [
    'total' => $pdo->query("SELECT COUNT(*) FROM kategoriler")->fetchColumn(),
    'active' => $pdo->query("SELECT COUNT(*) FROM kategoriler WHERE durum = 'aktif'")->fetchColumn(),
    'with_videos' => $pdo->query("SELECT COUNT(DISTINCT kategori_id) FROM videolar WHERE kategori_id IS NOT NULL")->fetchColumn(),
];

// Slug oluşturma fonksiyonu
function createSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    $text = trim($text, '-');
    return $text;
}

include 'includes/header.php';
?>

<div class="admin-content">
    <div class="content-header">
        <div class="content-title">
            <h1><i class="fas fa-tags"></i> Kategori Yönetimi</h1>
            <p>Video kategorilerini yönetin, düzenleyin ve yeni kategori ekleyin.</p>
        </div>
        <div class="content-actions">
            <button class="btn btn-primary" onclick="openCategoryModal()">
                <i class="fas fa-plus"></i> Yeni Kategori Ekle
            </button>
        </div>
    </div>

    <?php if ($success_message): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <?php echo $success_message; ?>
    </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <?php echo $error_message; ?>
    </div>
    <?php endif; ?>

    <!-- İstatistikler -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-tags"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo number_format($stats['total']); ?></div>
                <div class="stat-label">Toplam Kategori</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-eye"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo number_format($stats['active']); ?></div>
                <div class="stat-label">Aktif Kategori</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-video"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo number_format($stats['with_videos']); ?></div>
                <div class="stat-label">Videolu Kategori</div>
            </div>
        </div>
    </div>

    <!-- Kategori Listesi -->
    <div class="data-card">
        <div class="card-header">
            <h3>Kategoriler (<?php echo number_format(count($categories)); ?>)</h3>
        </div>
        
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Sıra</th>
                        <th>Resim</th>
                        <th>Kategori Adı</th>
                        <th>Slug</th>
                        <th>Video Sayısı</th>
                        <th>Durum</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody id="categoriesTable">
                    <?php foreach ($categories as $category): ?>
                    <tr data-id="<?php echo $category['id']; ?>">
                        <td>
                            <div class="sort-handle">
                                <i class="fas fa-grip-vertical"></i>
                                <span><?php echo $category['siralama']; ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="category-image">
                                <?php if ($category['resim']): ?>
                                <img src="../uploads/categories/<?php echo $category['resim']; ?>" 
                                     alt="<?php echo safeOutput($category['kategori_adi']); ?>">
                                <?php else: ?>
                                <div class="no-image">
                                    <i class="fas fa-image"></i>
                                </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <div class="category-info">
                                <strong><?php echo safeOutput($category['kategori_adi']); ?></strong>
                                <?php if ($category['aciklama']): ?>
                                <p><?php echo safeOutput(substr($category['aciklama'], 0, 80)); ?><?php echo strlen($category['aciklama']) > 80 ? '...' : ''; ?></p>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <code><?php echo $category['slug']; ?></code>
                        </td>
                        <td>
                            <span class="video-count">
                                <i class="fas fa-video"></i>
                                <?php echo number_format($category['video_count']); ?>
                            </span>
                            <?php if ($category['video_count'] > 0): ?>
                            <a href="videolar.php?kategori=<?php echo $category['id']; ?>" class="view-videos">
                                Videoları Gör
                            </a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="status status-<?php echo $category['durum']; ?>">
                                <?php echo ucfirst($category['durum']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-icon btn-primary" onclick="editCategory(<?php echo $category['id']; ?>)" 
                                        title="Düzenle">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="../kategoriler.php?kategori=<?php echo $category['slug']; ?>" 
                                   class="btn-icon btn-info" title="Görüntüle" target="_blank">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="btn-icon btn-danger" onclick="deleteCategory(<?php echo $category['id']; ?>)" 
                                        title="Sil">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Kategori Modal -->
<div class="modal" id="categoryModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Yeni Kategori Ekle</h3>
            <button class="modal-close" onclick="closeCategoryModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="categoryForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="kategori_id" id="kategori_id">
            
            <div class="form-group">
                <label for="kategori_adi">Kategori Adı *</label>
                <input type="text" name="kategori_adi" id="kategori_adi" required>
            </div>
            
            <div class="form-group">
                <label for="aciklama">Açıklama</label>
                <textarea name="aciklama" id="aciklama" rows="3" 
                          placeholder="Kategori hakkında kısa açıklama"></textarea>
            </div>
            
            <div class="form-group">
                <label for="resim">Kategori Resmi</label>
                <input type="file" name="resim" id="resim" accept="image/*">
                <small>JPG, PNG veya WebP formatında olmalıdır.</small>
                <div id="currentImage" style="display: none; margin-top: 10px;">
                    <img id="currentImagePreview" src="" style="max-width: 100px; border-radius: 5px;">
                    <p><small>Mevcut resim</small></p>
                </div>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="siralama">Sıralama</label>
                    <input type="number" name="siralama" id="siralama" min="0" value="0">
                    <small>Küçük sayılar önce gösterilir</small>
                </div>
                
                <div class="form-group">
                    <label for="durum">Durum</label>
                    <select name="durum" id="durum">
                        <option value="aktif">Aktif</option>
                        <option value="pasif">Pasif</option>
                    </select>
                </div>
            </div>
            
            <div class="modal-actions">
                <button type="submit" name="add_category" id="submitBtn" class="btn btn-primary">
                    <i class="fas fa-save"></i> Kaydet
                </button>
                <button type="button" class="btn btn-outline" onclick="closeCategoryModal()">
                    İptal
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.category-image {
    width: 60px;
    height: 40px;
    border-radius: 4px;
    overflow: hidden;
}

.category-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-image {
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255, 255, 255, 0.5);
}

.category-info strong {
    display: block;
    margin-bottom: 4px;
}

.category-info p {
    margin: 0;
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.8rem;
    line-height: 1.3;
}

.sort-handle {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: move;
    color: rgba(255, 255, 255, 0.7);
}

.sort-handle:hover {
    color: var(--primary-color);
}

.video-count {
    display: flex;
    align-items: center;
    gap: 4px;
    color: rgba(255, 255, 255, 0.8);
    font-weight: 500;
}

.video-count i {
    color: var(--primary-color);
}

.view-videos {
    display: block;
    color: var(--primary-color);
    text-decoration: none;
    font-size: 0.8rem;
    margin-top: 4px;
}

.view-videos:hover {
    text-decoration: underline;
}

code {
    background: rgba(255, 255, 255, 0.1);
    padding: 2px 6px;
    border-radius: 3px;
    font-family: 'Courier New', monospace;
    font-size: 0.8rem;
    color: var(--primary-color);
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function openCategoryModal() {
    document.getElementById('modalTitle').textContent = 'Yeni Kategori Ekle';
    document.getElementById('categoryForm').reset();
    document.getElementById('kategori_id').value = '';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save"></i> Kaydet';
    document.getElementById('submitBtn').name = 'add_category';
    document.getElementById('currentImage').style.display = 'none';
    document.getElementById('categoryModal').style.display = 'flex';
}

function editCategory(id) {
    // AJAX ile kategori bilgilerini çek
    fetch(`get_category.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const category = data.category;
                document.getElementById('modalTitle').textContent = 'Kategori Düzenle';
                document.getElementById('kategori_id').value = category.id;
                document.getElementById('kategori_adi').value = category.kategori_adi;
                document.getElementById('aciklama').value = category.aciklama || '';
                document.getElementById('siralama').value = category.siralama;
                document.getElementById('durum').value = category.durum;
                
                if (category.resim) {
                    document.getElementById('currentImage').style.display = 'block';
                    document.getElementById('currentImagePreview').src = '../uploads/categories/' + category.resim;
                } else {
                    document.getElementById('currentImage').style.display = 'none';
                }
                
                document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save"></i> Güncelle';
                document.getElementById('submitBtn').name = 'update_category';
                document.getElementById('categoryModal').style.display = 'flex';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Kategori bilgileri yüklenirken hata oluştu.');
        });
}

function deleteCategory(id) {
    if (confirm('Bu kategoriyi silmek istediğinizden emin misiniz?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="kategori_id" value="${id}">
            <input type="hidden" name="delete_category" value="1">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function closeCategoryModal() {
    document.getElementById('categoryModal').style.display = 'none';
}

// Modal dışına tıklayınca kapat
document.getElementById('categoryModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCategoryModal();
    }
});

// Sıralama özelliği (basit versiyon)
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('categoriesTable');
    if (table) {
        // Sortable özelliği eklenebilir (SortableJS library ile)
        // Şimdilik basit haliyle bırakıyoruz
    }
});
</script>

<?php include 'includes/footer.php'; ?>