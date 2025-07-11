<?php
/**
 * DOBİEN Video Platform - Admin Kullanıcı Yönetimi
 * Geliştirici: DOBİEN
 * Tüm Hakları Saklıdır © DOBİEN
 */

require_once '../includes/config.php';

// Admin kontrolü
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: giris.php');
    exit;
}

$page_title = "Kullanıcı Yönetimi";
$success_message = '';
$error_message = '';

// Kullanıcı işlemleri
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user'])) {
        // Kullanıcı ekleme
        $ad_soyad = trim($_POST['ad_soyad']);
        $email = trim($_POST['email']);
        $sifre = $_POST['sifre'];
        $uyelik_tipi = $_POST['uyelik_tipi'];
        $durum = $_POST['durum'];
        
        if (empty($ad_soyad) || empty($email) || empty($sifre)) {
            $error_message = 'Ad soyad, e-posta ve şifre zorunludur.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = 'Geçerli bir e-posta adresi girin.';
        } else {
            // E-posta kontrolü
            $stmt = $pdo->prepare("SELECT id FROM kullanicilar WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error_message = 'Bu e-posta adresi zaten kullanılıyor.';
            } else {
                try {
                    $hashed_password = password_hash($sifre, PASSWORD_BCRYPT);
                    $stmt = $pdo->prepare("
                        INSERT INTO kullanicilar (ad_soyad, email, sifre, uyelik_tipi, durum, kayit_tarihi) 
                        VALUES (?, ?, ?, ?, ?, NOW())
                    ");
                    $stmt->execute([$ad_soyad, $email, $hashed_password, $uyelik_tipi, $durum]);
                    
                    $success_message = 'Kullanıcı başarıyla eklendi.';
                } catch (PDOException $e) {
                    $error_message = 'Kullanıcı eklenirken hata oluştu: ' . $e->getMessage();
                }
            }
        }
    }
    
    if (isset($_POST['update_user'])) {
        // Kullanıcı güncelleme
        $user_id = $_POST['user_id'];
        $ad_soyad = trim($_POST['ad_soyad']);
        $email = trim($_POST['email']);
        $uyelik_tipi = $_POST['uyelik_tipi'];
        $durum = $_POST['durum'];
        
        if (empty($ad_soyad) || empty($email)) {
            $error_message = 'Ad soyad ve e-posta zorunludur.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = 'Geçerli bir e-posta adresi girin.';
        } else {
            // E-posta kontrolü (diğer kullanıcılarda)
            $stmt = $pdo->prepare("SELECT id FROM kullanicilar WHERE email = ? AND id != ?");
            $stmt->execute([$email, $user_id]);
            if ($stmt->fetch()) {
                $error_message = 'Bu e-posta adresi başka bir kullanıcı tarafından kullanılıyor.';
            } else {
                try {
                    $update_query = "UPDATE kullanicilar SET ad_soyad = ?, email = ?, uyelik_tipi = ?, durum = ?";
                    $params = [$ad_soyad, $email, $uyelik_tipi, $durum];
                    
                    // Şifre güncellemesi varsa
                    if (!empty($_POST['sifre'])) {
                        $update_query .= ", sifre = ?";
                        $params[] = password_hash($_POST['sifre'], PASSWORD_BCRYPT);
                    }
                    
                    $update_query .= " WHERE id = ?";
                    $params[] = $user_id;
                    
                    $stmt = $pdo->prepare($update_query);
                    $stmt->execute($params);
                    
                    $success_message = 'Kullanıcı başarıyla güncellendi.';
                } catch (PDOException $e) {
                    $error_message = 'Kullanıcı güncellenirken hata oluştu: ' . $e->getMessage();
                }
            }
        }
    }
    
    if (isset($_POST['delete_user'])) {
        // Kullanıcı silme
        $user_id = $_POST['user_id'];
        
        try {
            // İlişkili verileri de sil
            $pdo->beginTransaction();
            
            $pdo->prepare("DELETE FROM kullanici_favorileri WHERE kullanici_id = ?")->execute([$user_id]);
            $pdo->prepare("DELETE FROM kullanici_izleme_gecmisi WHERE kullanici_id = ?")->execute([$user_id]);
            $pdo->prepare("DELETE FROM video_begeni WHERE kullanici_id = ?")->execute([$user_id]);
            $pdo->prepare("DELETE FROM kullanicilar WHERE id = ?")->execute([$user_id]);
            
            $pdo->commit();
            $success_message = 'Kullanıcı başarıyla silindi.';
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error_message = 'Kullanıcı silinirken hata oluştu: ' . $e->getMessage();
        }
    }
}

// Sayfalama
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 25;
$offset = ($page - 1) * $per_page;

// Filtreleme
$where_conditions = [];
$params = [];

if (!empty($_GET['search'])) {
    $where_conditions[] = "(ad_soyad LIKE ? OR email LIKE ?)";
    $search_param = '%' . $_GET['search'] . '%';
    $params[] = $search_param;
    $params[] = $search_param;
}

if (!empty($_GET['uyelik_tipi'])) {
    $where_conditions[] = "uyelik_tipi = ?";
    $params[] = $_GET['uyelik_tipi'];
}

if (!empty($_GET['durum'])) {
    $where_conditions[] = "durum = ?";
    $params[] = $_GET['durum'];
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Kullanıcıları çek
$query = "SELECT * FROM kullanicilar $where_clause ORDER BY kayit_tarihi DESC LIMIT $per_page OFFSET $offset";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll();

// Toplam kullanıcı sayısı
$count_query = "SELECT COUNT(*) FROM kullanicilar $where_clause";
$count_stmt = $pdo->prepare($count_query);
$count_stmt->execute($params);
$total_users = $count_stmt->fetchColumn();
$total_pages = ceil($total_users / $per_page);

// İstatistikler
$stats = [
    'total' => $pdo->query("SELECT COUNT(*) FROM kullanicilar")->fetchColumn(),
    'active' => $pdo->query("SELECT COUNT(*) FROM kullanicilar WHERE durum = 'aktif'")->fetchColumn(),
    'kullanici' => $pdo->query("SELECT COUNT(*) FROM kullanicilar WHERE uyelik_tipi = 'kullanici'")->fetchColumn(),
    'vip' => $pdo->query("SELECT COUNT(*) FROM kullanicilar WHERE uyelik_tipi = 'vip'")->fetchColumn(),
    'premium' => $pdo->query("SELECT COUNT(*) FROM kullanicilar WHERE uyelik_tipi = 'premium'")->fetchColumn(),
];

include 'includes/header.php';
?>

<div class="admin-content">
    <div class="content-header">
        <div class="content-title">
            <h1><i class="fas fa-users"></i> Kullanıcı Yönetimi</h1>
            <p>Sisteme kayıtlı kullanıcıları yönetin, düzenleyin ve yeni kullanıcı ekleyin.</p>
        </div>
        <div class="content-actions">
            <button class="btn btn-primary" onclick="openUserModal()">
                <i class="fas fa-plus"></i> Yeni Kullanıcı Ekle
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
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo number_format($stats['total']); ?></div>
                <div class="stat-label">Toplam Kullanıcı</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo number_format($stats['active']); ?></div>
                <div class="stat-label">Aktif Kullanıcı</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-star"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo number_format($stats['vip']); ?></div>
                <div class="stat-label">VIP Üye</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-crown"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo number_format($stats['premium']); ?></div>
                <div class="stat-label">Premium Üye</div>
            </div>
        </div>
    </div>

    <!-- Filtreler -->
    <div class="filters-card">
        <form method="GET" class="filters-form">
            <div class="filter-group">
                <input type="text" name="search" value="<?php echo safeOutput($_GET['search'] ?? ''); ?>" 
                       placeholder="Kullanıcı ara...">
            </div>
            
            <div class="filter-group">
                <select name="uyelik_tipi">
                    <option value="">Tüm Üyelik Tipleri</option>
                    <option value="kullanici" <?php echo ($_GET['uyelik_tipi'] ?? '') === 'kullanici' ? 'selected' : ''; ?>>Standart</option>
                    <option value="vip" <?php echo ($_GET['uyelik_tipi'] ?? '') === 'vip' ? 'selected' : ''; ?>>VIP</option>
                    <option value="premium" <?php echo ($_GET['uyelik_tipi'] ?? '') === 'premium' ? 'selected' : ''; ?>>Premium</option>
                </select>
            </div>
            
            <div class="filter-group">
                <select name="durum">
                    <option value="">Tüm Durumlar</option>
                    <option value="aktif" <?php echo ($_GET['durum'] ?? '') === 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                    <option value="pasif" <?php echo ($_GET['durum'] ?? '') === 'pasif' ? 'selected' : ''; ?>>Pasif</option>
                    <option value="yasakli" <?php echo ($_GET['durum'] ?? '') === 'yasakli' ? 'selected' : ''; ?>>Yasaklı</option>
                </select>
            </div>
            
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filtrele
                </button>
                <a href="kullanicilar.php" class="btn btn-outline">
                    <i class="fas fa-times"></i> Temizle
                </a>
            </div>
        </form>
    </div>

    <!-- Kullanıcı Listesi -->
    <div class="data-card">
        <div class="card-header">
            <h3>Kullanıcılar (<?php echo number_format($total_users); ?>)</h3>
        </div>
        
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kullanıcı</th>
                        <th>E-posta</th>
                        <th>Üyelik</th>
                        <th>Kayıt Tarihi</th>
                        <th>Son Giriş</th>
                        <th>Durum</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td>#<?php echo $user['id']; ?></td>
                        <td>
                            <div class="user-info">
                                <div class="user-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <strong><?php echo safeOutput($user['ad_soyad']); ?></strong>
                                    <small>Kullanıcı ID: <?php echo $user['id']; ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <a href="mailto:<?php echo $user['email']; ?>" class="email-link">
                                <?php echo safeOutput($user['email']); ?>
                            </a>
                        </td>
                        <td>
                            <span class="membership-badge membership-<?php echo $user['uyelik_tipi']; ?>">
                                <?php
                                echo match($user['uyelik_tipi']) {
                                    'kullanici' => 'Standart',
                                    'vip' => 'VIP',
                                    'premium' => 'Premium',
                                    default => 'Bilinmiyor'
                                };
                                ?>
                            </span>
                        </td>
                        <td>
                            <span class="date"><?php echo formatDate($user['kayit_tarihi'], 'd.m.Y'); ?></span>
                            <small><?php echo formatDate($user['kayit_tarihi'], 'H:i'); ?></small>
                        </td>
                        <td>
                            <?php if ($user['son_giris']): ?>
                            <span class="date"><?php echo formatDate($user['son_giris'], 'd.m.Y'); ?></span>
                            <small><?php echo formatDate($user['son_giris'], 'H:i'); ?></small>
                            <?php else: ?>
                            <span class="text-muted">Hiç giriş yapmamış</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="status status-<?php echo $user['durum']; ?>">
                                <?php echo ucfirst($user['durum']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-icon btn-primary" onclick="editUser(<?php echo $user['id']; ?>)" 
                                        title="Düzenle">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-icon btn-info" onclick="viewUserDetails(<?php echo $user['id']; ?>)" 
                                        title="Detaylar">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-icon btn-danger" onclick="deleteUser(<?php echo $user['id']; ?>)" 
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

<!-- Kullanıcı Modal -->
<div class="modal" id="userModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Yeni Kullanıcı Ekle</h3>
            <button class="modal-close" onclick="closeUserModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="userForm" method="POST">
            <input type="hidden" name="user_id" id="user_id">
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="ad_soyad">Ad Soyad *</label>
                    <input type="text" name="ad_soyad" id="ad_soyad" required>
                </div>
                
                <div class="form-group">
                    <label for="email">E-posta *</label>
                    <input type="email" name="email" id="email" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="sifre">Şifre <span id="passwordNote">*</span></label>
                <input type="password" name="sifre" id="sifre" minlength="6">
                <small id="passwordHelp">En az 6 karakter olmalıdır.</small>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="uyelik_tipi">Üyelik Tipi</label>
                    <select name="uyelik_tipi" id="uyelik_tipi">
                        <option value="kullanici">Standart</option>
                        <option value="vip">VIP</option>
                        <option value="premium">Premium</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="durum">Durum</label>
                    <select name="durum" id="durum">
                        <option value="aktif">Aktif</option>
                        <option value="pasif">Pasif</option>
                        <option value="yasakli">Yasaklı</option>
                    </select>
                </div>
            </div>
            
            <div class="modal-actions">
                <button type="submit" name="add_user" id="submitBtn" class="btn btn-primary">
                    <i class="fas fa-save"></i> Kaydet
                </button>
                <button type="button" class="btn btn-outline" onclick="closeUserModal()">
                    İptal
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.user-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-avatar {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--primary-color), #e67e22);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #000;
    font-size: 1.2rem;
}

.user-info strong {
    display: block;
    margin-bottom: 2px;
}

.user-info small {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.8rem;
}

.email-link {
    color: var(--primary-color);
    text-decoration: none;
}

.email-link:hover {
    text-decoration: underline;
}

.membership-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 600;
}

.membership-kullanici { background: #28a745; color: #fff; }
.membership-vip { background: #ffc107; color: #000; }
.membership-premium { background: #dc3545; color: #fff; }

.text-muted {
    color: rgba(255, 255, 255, 0.5);
    font-style: italic;
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
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<script>
function openUserModal() {
    document.getElementById('modalTitle').textContent = 'Yeni Kullanıcı Ekle';
    document.getElementById('userForm').reset();
    document.getElementById('user_id').value = '';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save"></i> Kaydet';
    document.getElementById('submitBtn').name = 'add_user';
    document.getElementById('sifre').required = true;
    document.getElementById('passwordNote').textContent = '*';
    document.getElementById('passwordHelp').textContent = 'En az 6 karakter olmalıdır.';
    document.getElementById('userModal').style.display = 'flex';
}

function editUser(id) {
    // AJAX ile kullanıcı bilgilerini çek
    fetch(`get_user.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const user = data.user;
                document.getElementById('modalTitle').textContent = 'Kullanıcı Düzenle';
                document.getElementById('user_id').value = user.id;
                document.getElementById('ad_soyad').value = user.ad_soyad;
                document.getElementById('email').value = user.email;
                document.getElementById('uyelik_tipi').value = user.uyelik_tipi;
                document.getElementById('durum').value = user.durum;
                document.getElementById('sifre').value = '';
                document.getElementById('sifre').required = false;
                document.getElementById('passwordNote').textContent = '(Değiştirmek istemiyorsanız boş bırakın)';
                document.getElementById('passwordHelp').textContent = 'Şifreyi değiştirmek istemiyorsanız boş bırakabilirsiniz.';
                document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save"></i> Güncelle';
                document.getElementById('submitBtn').name = 'update_user';
                document.getElementById('userModal').style.display = 'flex';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Kullanıcı bilgileri yüklenirken hata oluştu.');
        });
}

function deleteUser(id) {
    if (confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?\n\nBu işlem geri alınamaz ve kullanıcının tüm verileri silinir.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="user_id" value="${id}">
            <input type="hidden" name="delete_user" value="1">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function viewUserDetails(id) {
    // Kullanıcı detayları sayfasına yönlendir
    window.open(`kullanici-detay.php?id=${id}`, '_blank');
}

function closeUserModal() {
    document.getElementById('userModal').style.display = 'none';
}

// Modal dışına tıklayınca kapat
document.getElementById('userModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeUserModal();
    }
});
</script>

<?php include 'includes/footer.php'; ?>