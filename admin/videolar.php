<?php
/**
 * DOBİEN Video Platform - Admin Video Yönetimi
 * Geliştirici: DOBİEN
 * Tüm Hakları Saklıdır © DOBİEN
 */

require_once '../includes/config.php';

// Admin kontrolü
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: giris.php');
    exit;
}

$page_title = "Video Yönetimi";
$success_message = '';
$error_message = '';

// Video işlemleri
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_video'])) {
        // Video ekleme
        $baslik = trim($_POST['baslik']);
        $aciklama = trim($_POST['aciklama']);
        $kategori_id = $_POST['kategori_id'];
        $video_url = trim($_POST['video_url']);
        $goruntulenme_yetkisi = $_POST['goruntulenme_yetkisi'];
        $ozellik = $_POST['ozellik'];
        $etiketler = trim($_POST['etiketler']);
        $sure = $_POST['sure'] ?? null;
        
        if (empty($baslik) || empty($video_url)) {
            $error_message = 'Başlık ve video URL\'si zorunludur.';
        } else {
            // Kapak resmi upload
            $kapak_resmi = null;
            if (isset($_FILES['kapak_resmi']) && $_FILES['kapak_resmi']['error'] === 0) {
                $upload_dir = '../uploads/thumbnails/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_extension = strtolower(pathinfo($_FILES['kapak_resmi']['name'], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
                
                if (in_array($file_extension, $allowed_extensions)) {
                    $kapak_resmi = uniqid() . '.' . $file_extension;
                    $upload_path = $upload_dir . $kapak_resmi;
                    
                    if (!move_uploaded_file($_FILES['kapak_resmi']['tmp_name'], $upload_path)) {
                        $kapak_resmi = null;
                    }
                }
            }
            
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO videolar (baslik, aciklama, kategori_id, video_url, kapak_resmi, 
                                        goruntulenme_yetkisi, ozellik, etiketler, sure, ekleme_tarihi, durum) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'aktif')
                ");
                $stmt->execute([$baslik, $aciklama, $kategori_id, $video_url, $kapak_resmi, 
                               $goruntulenme_yetkisi, $ozellik, $etiketler, $sure]);
                
                $success_message = 'Video başarıyla eklendi.';
            } catch (PDOException $e) {
                $error_message = 'Video eklenirken hata oluştu: ' . $e->getMessage();
            }
        }
    }
    
    if (isset($_POST['update_video'])) {
        // Video güncelleme
        $video_id = $_POST['video_id'];
        $baslik = trim($_POST['baslik']);
        $aciklama = trim($_POST['aciklama']);
        $kategori_id = $_POST['kategori_id'];
        $video_url = trim($_POST['video_url']);
        $goruntulenme_yetkisi = $_POST['goruntulenme_yetkisi'];
        $ozellik = $_POST['ozellik'];
        $etiketler = trim($_POST['etiketler']);
        $sure = $_POST['sure'] ?? null;
        $durum = $_POST['durum'];
        
        if (empty($baslik) || empty($video_url)) {
            $error_message = 'Başlık ve video URL\'si zorunludur.';
        } else {
            try {
                $stmt = $pdo->prepare("
                    UPDATE videolar SET baslik = ?, aciklama = ?, kategori_id = ?, video_url = ?, 
                                      goruntulenme_yetkisi = ?, ozellik = ?, etiketler = ?, sure = ?, durum = ?
                    WHERE id = ?
                ");
                $stmt->execute([$baslik, $aciklama, $kategori_id, $video_url, 
                               $goruntulenme_yetkisi, $ozellik, $etiketler, $sure, $durum, $video_id]);
                
                $success_message = 'Video başarıyla güncellendi.';
            } catch (PDOException $e) {
                $error_message = 'Video güncellenirken hata oluştu: ' . $e->getMessage();
            }
        }
    }
    
    if (isset($_POST['delete_video'])) {
        // Video silme
        $video_id = $_POST['video_id'];
        
        try {
            // Önce kapak resmini sil
            $stmt = $pdo->prepare("SELECT kapak_resmi FROM videolar WHERE id = ?");
            $stmt->execute([$video_id]);
            $video = $stmt->fetch();
            
            if ($video && $video['kapak_resmi']) {
                $file_path = '../uploads/thumbnails/' . $video['kapak_resmi'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            
            // Videoyu sil
            $stmt = $pdo->prepare("DELETE FROM videolar WHERE id = ?");
            $stmt->execute([$video_id]);
            
            $success_message = 'Video başarıyla silindi.';
        } catch (PDOException $e) {
            $error_message = 'Video silinirken hata oluştu: ' . $e->getMessage();
        }
    }
}

// Sayfalama
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Filtreleme
$where_conditions = [];
$params = [];

if (!empty($_GET['search'])) {
    $where_conditions[] = "(v.baslik LIKE ? OR v.aciklama LIKE ?)";
    $search_param = '%' . $_GET['search'] . '%';
    $params[] = $search_param;
    $params[] = $search_param;
}

if (!empty($_GET['kategori'])) {
    $where_conditions[] = "v.kategori_id = ?";
    $params[] = $_GET['kategori'];
}

if (!empty($_GET['durum'])) {
    $where_conditions[] = "v.durum = ?";
    $params[] = $_GET['durum'];
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Videoları çek
$query = "
    SELECT v.*, k.kategori_adi 
    FROM videolar v 
    LEFT JOIN kategoriler k ON v.kategori_id = k.id 
    $where_clause
    ORDER BY v.ekleme_tarihi DESC 
    LIMIT $per_page OFFSET $offset
";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$videos = $stmt->fetchAll();

// Toplam video sayısı
$count_query = "SELECT COUNT(*) FROM videolar v LEFT JOIN kategoriler k ON v.kategori_id = k.id $where_clause";
$count_stmt = $pdo->prepare($count_query);
$count_stmt->execute($params);
$total_videos = $count_stmt->fetchColumn();
$total_pages = ceil($total_videos / $per_page);

// Kategorileri çek
$categories = $pdo->query("SELECT * FROM kategoriler WHERE durum = 'aktif' ORDER BY kategori_adi")->fetchAll();

include 'includes/header.php';
?>

<div class="admin-content">
    <div class="content-header">
        <div class="content-title">
            <h1><i class="fas fa-video"></i> Video Yönetimi</h1>
            <p>Sisteme eklenen videoları yönetin, düzenleyin ve yeni video ekleyin.</p>
        </div>
        <div class="content-actions">
            <button class="btn btn-primary" onclick="openVideoModal()">
                <i class="fas fa-plus"></i> Yeni Video Ekle
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

    <!-- Filtreler -->
    <div class="filters-card">
        <form method="GET" class="filters-form">
            <div class="filter-group">
                <input type="text" name="search" value="<?php echo safeOutput($_GET['search'] ?? ''); ?>" 
                       placeholder="Video ara...">
            </div>
            
            <div class="filter-group">
                <select name="kategori">
                    <option value="">Tüm Kategoriler</option>
                    <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>" 
                            <?php echo ($_GET['kategori'] ?? '') == $category['id'] ? 'selected' : ''; ?>>
                        <?php echo safeOutput($category['kategori_adi']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <select name="durum">
                    <option value="">Tüm Durumlar</option>
                    <option value="aktif" <?php echo ($_GET['durum'] ?? '') === 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                    <option value="pasif" <?php echo ($_GET['durum'] ?? '') === 'pasif' ? 'selected' : ''; ?>>Pasif</option>
                </select>
            </div>
            
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filtrele
                </button>
                <a href="videolar.php" class="btn btn-outline">
                    <i class="fas fa-times"></i> Temizle
                </a>
            </div>
        </form>
    </div>

    <!-- Video Listesi -->
    <div class="data-card">
        <div class="card-header">
            <h3>Videolar (<?php echo number_format($total_videos); ?>)</h3>
        </div>
        
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kapak</th>
                        <th>Başlık</th>
                        <th>Kategori</th>
                        <th>Yetki</th>
                        <th>İstatistik</th>
                        <th>Tarih</th>
                        <th>Durum</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($videos as $video): ?>
                    <tr>
                        <td>
                            <div class="video-thumbnail">
                                <?php if ($video['kapak_resmi']): ?>
                                <img src="../uploads/thumbnails/<?php echo $video['kapak_resmi']; ?>" 
                                     alt="<?php echo safeOutput($video['baslik']); ?>">
                                <?php else: ?>
                                <div class="no-thumbnail">
                                    <i class="fas fa-video"></i>
                                </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <div class="video-info">
                                <strong><?php echo safeOutput($video['baslik']); ?></strong>
                                <?php if ($video['ozellik'] != 'normal'): ?>
                                <span class="badge badge-<?php echo $video['ozellik']; ?>">
                                    <?php echo ucfirst($video['ozellik']); ?>
                                </span>
                                <?php endif; ?>
                                <?php if ($video['aciklama']): ?>
                                <p><?php echo safeOutput(substr($video['aciklama'], 0, 100)); ?>...</p>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <?php echo $video['kategori_adi'] ? safeOutput($video['kategori_adi']) : 'Kategorisiz'; ?>
                        </td>
                        <td>
                            <span class="access-level access-<?php echo $video['goruntulenme_yetkisi']; ?>">
                                <?php
                                echo match($video['goruntulenme_yetkisi']) {
                                    'herkes' => '720p',
                                    'vip' => '1080p',
                                    'premium' => '4K',
                                    default => 'Bilinmiyor'
                                };
                                ?>
                            </span>
                        </td>
                        <td>
                            <div class="video-stats">
                                <span title="İzlenme"><i class="fas fa-eye"></i> <?php echo number_format($video['izlenme_sayisi']); ?></span>
                                <span title="Beğeni"><i class="fas fa-thumbs-up"></i> <?php echo number_format($video['begeni_sayisi']); ?></span>
                            </div>
                        </td>
                        <td>
                            <span class="date"><?php echo formatDate($video['ekleme_tarihi'], 'd.m.Y'); ?></span>
                            <small><?php echo formatDate($video['ekleme_tarihi'], 'H:i'); ?></small>
                        </td>
                        <td>
                            <span class="status status-<?php echo $video['durum']; ?>">
                                <?php echo ucfirst($video['durum']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-icon btn-primary" onclick="editVideo(<?php echo $video['id']; ?>)" 
                                        title="Düzenle">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="../video.php?id=<?php echo $video['id']; ?>" class="btn-icon btn-info" 
                                   title="Görüntüle" target="_blank">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="btn-icon btn-danger" onclick="deleteVideo(<?php echo $video['id']; ?>)" 
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

        <!-- Sayfalama -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&<?php echo http_build_query(array_filter($_GET, fn($k) => $k !== 'page', ARRAY_FILTER_USE_KEY)); ?>" 
               class="page-link <?php echo $i == $page ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Video Modal -->
<div class="modal" id="videoModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Yeni Video Ekle</h3>
            <button class="modal-close" onclick="closeVideoModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="videoForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="video_id" id="video_id">
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="baslik">Başlık *</label>
                    <input type="text" name="baslik" id="baslik" required>
                </div>
                
                <div class="form-group">
                    <label for="kategori_id">Kategori</label>
                    <select name="kategori_id" id="kategori_id">
                        <option value="">Kategori Seçin</option>
                        <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>"><?php echo safeOutput($category['kategori_adi']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="aciklama">Açıklama</label>
                <textarea name="aciklama" id="aciklama" rows="3"></textarea>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="video_url">Video URL *</label>
                    <input type="url" name="video_url" id="video_url" required>
                </div>
                
                <div class="form-group">
                    <label for="sure">Süre (saniye)</label>
                    <input type="number" name="sure" id="sure" min="0">
                </div>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="goruntulenme_yetkisi">Görüntülenme Yetkisi</label>
                    <select name="goruntulenme_yetkisi" id="goruntulenme_yetkisi">
                        <option value="herkes">Herkes (720p)</option>
                        <option value="vip">VIP (1080p)</option>
                        <option value="premium">Premium (4K)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="ozellik">Özellik</label>
                    <select name="ozellik" id="ozellik">
                        <option value="normal">Normal</option>
                        <option value="yeni">Yeni</option>
                        <option value="populer">Popüler</option>
                        <option value="editor_secimi">Editör Seçimi</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="etiketler">Etiketler (virgülle ayırın)</label>
                <input type="text" name="etiketler" id="etiketler" placeholder="aksiyon, macera, gerilim">
            </div>
            
            <div class="form-group">
                <label for="kapak_resmi">Kapak Resmi</label>
                <input type="file" name="kapak_resmi" id="kapak_resmi" accept="image/*">
                <small>JPG, PNG veya WebP formatında olmalıdır.</small>
            </div>
            
            <div class="form-group" id="durumGroup" style="display: none;">
                <label for="durum">Durum</label>
                <select name="durum" id="durum">
                    <option value="aktif">Aktif</option>
                    <option value="pasif">Pasif</option>
                </select>
            </div>
            
            <div class="modal-actions">
                <button type="submit" name="add_video" id="submitBtn" class="btn btn-primary">
                    <i class="fas fa-save"></i> Kaydet
                </button>
                <button type="button" class="btn btn-outline" onclick="closeVideoModal()">
                    İptal
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.video-thumbnail {
    width: 60px;
    height: 40px;
    border-radius: 4px;
    overflow: hidden;
}

.video-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-thumbnail {
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255, 255, 255, 0.5);
}

.video-info strong {
    display: block;
    margin-bottom: 4px;
}

.video-info p {
    margin: 0;
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.8rem;
}

.badge {
    display: inline-block;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 0.7rem;
    font-weight: 500;
    margin-left: 8px;
}

.badge-yeni { background: #20c997; color: #fff; }
.badge-populer { background: #ff6b35; color: #fff; }
.badge-editor_secimi { background: #6f42c1; color: #fff; }

.access-level {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 600;
}

.access-herkes { background: #28a745; color: #fff; }
.access-vip { background: #ffc107; color: #000; }
.access-premium { background: #dc3545; color: #fff; }

.video-stats {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.video-stats span {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.7);
}

.video-stats i {
    width: 12px;
    color: var(--primary-color);
    margin-right: 4px;
}

.date {
    display: block;
    font-weight: 500;
}

.date + small {
    color: rgba(255, 255, 255, 0.6);
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
    
    .content-header {
        flex-direction: column;
        gap: 15px;
    }
}
</style>

<script>
function openVideoModal() {
    document.getElementById('modalTitle').textContent = 'Yeni Video Ekle';
    document.getElementById('videoForm').reset();
    document.getElementById('video_id').value = '';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save"></i> Kaydet';
    document.getElementById('submitBtn').name = 'add_video';
    document.getElementById('durumGroup').style.display = 'none';
    document.getElementById('videoModal').style.display = 'flex';
}

function editVideo(id) {
    // AJAX ile video bilgilerini çek ve formu doldur
    fetch(`get_video.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const video = data.video;
                document.getElementById('modalTitle').textContent = 'Video Düzenle';
                document.getElementById('video_id').value = video.id;
                document.getElementById('baslik').value = video.baslik;
                document.getElementById('aciklama').value = video.aciklama || '';
                document.getElementById('kategori_id').value = video.kategori_id || '';
                document.getElementById('video_url').value = video.video_url;
                document.getElementById('sure').value = video.sure || '';
                document.getElementById('goruntulenme_yetkisi').value = video.goruntulenme_yetkisi;
                document.getElementById('ozellik').value = video.ozellik;
                document.getElementById('etiketler').value = video.etiketler || '';
                document.getElementById('durum').value = video.durum;
                document.getElementById('durumGroup').style.display = 'block';
                document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save"></i> Güncelle';
                document.getElementById('submitBtn').name = 'update_video';
                document.getElementById('videoModal').style.display = 'flex';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Video bilgileri yüklenirken hata oluştu.');
        });
}

function deleteVideo(id) {
    if (confirm('Bu videoyu silmek istediğinizden emin misiniz?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="video_id" value="${id}">
            <input type="hidden" name="delete_video" value="1">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function closeVideoModal() {
    document.getElementById('videoModal').style.display = 'none';
}

// Modal dışına tıklayınca kapat
document.getElementById('videoModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeVideoModal();
    }
});
</script>

<?php include 'includes/footer.php'; ?>