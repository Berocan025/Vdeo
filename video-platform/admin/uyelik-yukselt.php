<?php
/**
 * DOBİEN Video Platform - Üyelik Yükseltme Yönetimi
 * Geliştirici: DOBİEN
 * Modern Video Paylaşım Platformu
 */

require_once '../includes/config.php';

// Admin kontrolü
if (!isAdmin()) {
    header('Location: giris.php');
    exit;
}

$page = 'uyelik-yukselt';
$current_admin = checkAdminSession();

// İşlemler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'upgrade_user':
                $user_id = (int)$_POST['user_id'];
                $new_membership = $_POST['new_membership'];
                $duration = (int)$_POST['duration'];
                
                try {
                    // Kullanıcı bilgilerini al
                    $user_stmt = $pdo->prepare("SELECT * FROM kullanicilar WHERE id = ?");
                    $user_stmt->execute([$user_id]);
                    $user = $user_stmt->fetch();
                    
                    if (!$user) {
                        throw new Exception('Kullanıcı bulunamadı.');
                    }
                    
                    // Yeni bitiş tarihini hesapla
                    $start_date = date('Y-m-d H:i:s');
                    $end_date = date('Y-m-d H:i:s', strtotime("+$duration days"));
                    
                    // Üyelik tipini güncelle
                    $update_field = $new_membership === 'vip' ? 'vip_bitis' : 'premium_bitis';
                    $update_stmt = $pdo->prepare("UPDATE kullanicilar SET uyelik_tipi = ?, $update_field = ?, guncelleme_tarihi = NOW() WHERE id = ?");
                    $update_stmt->execute([$new_membership, $end_date, $user_id]);
                    
                    // Ödeme geçmişine ekle
                    $payment_stmt = $pdo->prepare("INSERT INTO odeme_gecmisi (kullanici_id, plan, donem, tutar, odeme_yontemi, durum, baslangic_tarihi, bitis_tarihi) VALUES (?, ?, 'manual', 0.00, 'admin_upgrade', 'tamamlandi', ?, ?)");
                    $payment_stmt->execute([$user_id, $new_membership, $start_date, $end_date]);
                    
                    $success_message = $user['ad'] . ' ' . $user['soyad'] . ' kullanıcısının üyeliği ' . ucfirst($new_membership) . ' olarak yükseltildi.';
                    
                } catch (Exception $e) {
                    $error_message = 'Hata: ' . $e->getMessage();
                }
                break;
                
            case 'extend_membership':
                $user_id = (int)$_POST['user_id'];
                $extend_days = (int)$_POST['extend_days'];
                
                try {
                    $user_stmt = $pdo->prepare("SELECT * FROM kullanicilar WHERE id = ?");
                    $user_stmt->execute([$user_id]);
                    $user = $user_stmt->fetch();
                    
                    if (!$user) {
                        throw new Exception('Kullanıcı bulunamadı.');
                    }
                    
                    $membership_type = $user['uyelik_tipi'];
                    if ($membership_type === 'kullanici') {
                        throw new Exception('Bu kullanıcının aktif bir premium/vip üyeliği yok.');
                    }
                    
                    $current_end_field = $membership_type === 'vip' ? 'vip_bitis' : 'premium_bitis';
                    $current_end = $user[$current_end_field];
                    
                    // Mevcut bitiş tarihinden sonraya ekle
                    $new_end = date('Y-m-d H:i:s', strtotime($current_end . " +$extend_days days"));
                    
                    $update_stmt = $pdo->prepare("UPDATE kullanicilar SET $current_end_field = ? WHERE id = ?");
                    $update_stmt->execute([$new_end, $user_id]);
                    
                    $success_message = $user['ad'] . ' ' . $user['soyad'] . ' kullanıcısının ' . ucfirst($membership_type) . ' üyeliği ' . $extend_days . ' gün uzatıldı.';
                    
                } catch (Exception $e) {
                    $error_message = 'Hata: ' . $e->getMessage();
                }
                break;
                
            case 'downgrade_user':
                $user_id = (int)$_POST['user_id'];
                
                try {
                    $user_stmt = $pdo->prepare("SELECT * FROM kullanicilar WHERE id = ?");
                    $user_stmt->execute([$user_id]);
                    $user = $user_stmt->fetch();
                    
                    if (!$user) {
                        throw new Exception('Kullanıcı bulunamadı.');
                    }
                    
                    // Üyeliği normal kullanıcıya düşür
                    $update_stmt = $pdo->prepare("UPDATE kullanicilar SET uyelik_tipi = 'kullanici', vip_bitis = NULL, premium_bitis = NULL WHERE id = ?");
                    $update_stmt->execute([$user_id]);
                    
                    $success_message = $user['ad'] . ' ' . $user['soyad'] . ' kullanıcısının üyeliği normal kullanıcıya düşürüldü.';
                    
                } catch (Exception $e) {
                    $error_message = 'Hata: ' . $e->getMessage();
                }
                break;
        }
    }
}

// Sayfalama
$page_num = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page_num - 1) * $per_page;

// Arama filtresi
$search = $_GET['search'] ?? '';
$membership_filter = $_GET['membership'] ?? '';

// Kullanıcıları çek
$where_conditions = [];
$params = [];

if ($search) {
    $where_conditions[] = "(ad LIKE ? OR soyad LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($membership_filter) {
    $where_conditions[] = "uyelik_tipi = ?";
    $params[] = $membership_filter;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

try {
    // Toplam sayı
    $count_query = "SELECT COUNT(*) FROM kullanicilar $where_clause";
    $count_stmt = $pdo->prepare($count_query);
    $count_stmt->execute($params);
    $total_users = $count_stmt->fetchColumn();
    $total_pages = ceil($total_users / $per_page);
    
    // Kullanıcı listesi
    $users_query = "SELECT * FROM kullanicilar $where_clause ORDER BY id DESC LIMIT $per_page OFFSET $offset";
    $users_stmt = $pdo->prepare($users_query);
    $users_stmt->execute($params);
    $users = $users_stmt->fetchAll();
    
} catch (PDOException $e) {
    $users = [];
    $total_pages = 0;
    $error_message = 'Kullanıcı verileri alınamadı: ' . $e->getMessage();
}

// İstatistikler
try {
    $stats = [
        'total' => $pdo->query("SELECT COUNT(*) FROM kullanicilar")->fetchColumn(),
        'active' => $pdo->query("SELECT COUNT(*) FROM kullanicilar WHERE durum = 'aktif'")->fetchColumn(),
        'vip' => $pdo->query("SELECT COUNT(*) FROM kullanicilar WHERE uyelik_tipi = 'vip'")->fetchColumn(),
        'premium' => $pdo->query("SELECT COUNT(*) FROM kullanicilar WHERE uyelik_tipi = 'premium'")->fetchColumn()
    ];
} catch (PDOException $e) {
    $stats = ['total' => 0, 'active' => 0, 'vip' => 0, 'premium' => 0];
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Üyelik Yükseltme - DOBİEN Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">
    <style>
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
        }
        .stat-card.vip { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .stat-card.premium { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .stat-card.active { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        
        .membership-badge {
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: 600;
        }
        .membership-normal { background: #e9ecef; color: #495057; }
        .membership-vip { background: #f8d7da; color: #721c24; }
        .membership-premium { background: #d1ecf1; color: #0c5460; }
        
        .action-buttons .btn {
            margin: 2px;
            font-size: 0.8rem;
        }
        
        .filter-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
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
                        <h1 class="h3 mb-0">Üyelik Yönetimi</h1>
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

                    <!-- İstatistikler -->
                    <div class="stats-cards">
                        <div class="stat-card">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <h3><?php echo number_format($stats['total']); ?></h3>
                            <p>Toplam Kullanıcı</p>
                        </div>
                        <div class="stat-card active">
                            <i class="fas fa-user-check fa-2x mb-2"></i>
                            <h3><?php echo number_format($stats['active']); ?></h3>
                            <p>Aktif Kullanıcı</p>
                        </div>
                        <div class="stat-card vip">
                            <i class="fas fa-crown fa-2x mb-2"></i>
                            <h3><?php echo number_format($stats['vip']); ?></h3>
                            <p>VIP Üye</p>
                        </div>
                        <div class="stat-card premium">
                            <i class="fas fa-gem fa-2x mb-2"></i>
                            <h3><?php echo number_format($stats['premium']); ?></h3>
                            <p>Premium Üye</p>
                        </div>
                    </div>

                    <!-- Filtreler -->
                    <div class="filter-card">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Kullanıcı Ara</label>
                                <input type="text" class="form-control" id="search" name="search" placeholder="Ad, soyad veya e-posta..." value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="membership" class="form-label">Üyelik Tipi</label>
                                <select class="form-select" id="membership" name="membership">
                                    <option value="">Tümü</option>
                                    <option value="kullanici" <?php echo $membership_filter === 'kullanici' ? 'selected' : ''; ?>>Normal</option>
                                    <option value="vip" <?php echo $membership_filter === 'vip' ? 'selected' : ''; ?>>VIP</option>
                                    <option value="premium" <?php echo $membership_filter === 'premium' ? 'selected' : ''; ?>>Premium</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Ara
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <a href="?" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Temizle
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Kullanıcı Listesi -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Kullanıcı</th>
                                            <th>E-posta</th>
                                            <th>Üyelik Tipi</th>
                                            <th>Bitiş Tarihi</th>
                                            <th>Durum</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if ($user['profil_resmi']): ?>
                                                            <img src="../<?php echo htmlspecialchars($user['profil_resmi']); ?>" class="rounded-circle me-2" width="32" height="32">
                                                        <?php else: ?>
                                                            <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px;">
                                                                <?php echo strtoupper(substr($user['ad'], 0, 1)); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div>
                                                            <strong><?php echo htmlspecialchars($user['ad'] . ' ' . $user['soyad']); ?></strong>
                                                            <small class="text-muted d-block">ID: <?php echo $user['id']; ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td>
                                                    <span class="membership-badge membership-<?php echo $user['uyelik_tipi'] === 'kullanici' ? 'normal' : $user['uyelik_tipi']; ?>">
                                                        <?php 
                                                        echo $user['uyelik_tipi'] === 'kullanici' ? 'Normal' : strtoupper($user['uyelik_tipi']); 
                                                        ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php 
                                                    if ($user['uyelik_tipi'] === 'vip' && $user['vip_bitis']) {
                                                        echo formatDate($user['vip_bitis']);
                                                    } elseif ($user['uyelik_tipi'] === 'premium' && $user['premium_bitis']) {
                                                        echo formatDate($user['premium_bitis']);
                                                    } else {
                                                        echo '<span class="text-muted">-</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo $user['durum'] === 'aktif' ? 'success' : 'danger'; ?>">
                                                        <?php echo ucfirst($user['durum']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <?php if ($user['uyelik_tipi'] === 'kullanici'): ?>
                                                            <button class="btn btn-sm btn-outline-warning upgrade-btn" 
                                                                    data-user-id="<?php echo $user['id']; ?>"
                                                                    data-user-name="<?php echo htmlspecialchars($user['ad'] . ' ' . $user['soyad']); ?>">
                                                                <i class="fas fa-crown"></i> Yükselt
                                                            </button>
                                                        <?php else: ?>
                                                            <button class="btn btn-sm btn-outline-primary extend-btn"
                                                                    data-user-id="<?php echo $user['id']; ?>"
                                                                    data-user-name="<?php echo htmlspecialchars($user['ad'] . ' ' . $user['soyad']); ?>"
                                                                    data-membership="<?php echo $user['uyelik_tipi']; ?>">
                                                                <i class="fas fa-clock"></i> Uzat
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-danger downgrade-btn"
                                                                    data-user-id="<?php echo $user['id']; ?>"
                                                                    data-user-name="<?php echo htmlspecialchars($user['ad'] . ' ' . $user['soyad']); ?>">
                                                                <i class="fas fa-arrow-down"></i> Düşür
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Sayfalama -->
                            <?php if ($total_pages > 1): ?>
                                <nav aria-label="Sayfa navigasyonu">
                                    <ul class="pagination justify-content-center mt-4">
                                        <?php if ($page_num > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $page_num - 1; ?>&search=<?php echo urlencode($search); ?>&membership=<?php echo urlencode($membership_filter); ?>">Önceki</a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <?php for ($i = max(1, $page_num - 2); $i <= min($total_pages, $page_num + 2); $i++): ?>
                                            <li class="page-item <?php echo $i === $page_num ? 'active' : ''; ?>">
                                                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&membership=<?php echo urlencode($membership_filter); ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <?php if ($page_num < $total_pages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $page_num + 1; ?>&search=<?php echo urlencode($search); ?>&membership=<?php echo urlencode($membership_filter); ?>">Sonraki</a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upgrade Modal -->
    <div class="modal fade" id="upgradeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Üyelik Yükselt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="upgrade_user">
                        <input type="hidden" name="user_id" id="upgrade_user_id">
                        
                        <div class="alert alert-info">
                            <strong id="upgrade_user_name"></strong> kullanıcısının üyeliğini yükseltmek istiyorsunuz.
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_membership" class="form-label">Yeni Üyelik Tipi</label>
                            <select class="form-select" id="new_membership" name="new_membership" required>
                                <option value="">Seçin...</option>
                                <option value="vip">VIP Üyelik</option>
                                <option value="premium">Premium Üyelik</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="duration" class="form-label">Süre (Gün)</label>
                            <select class="form-select" id="duration" name="duration" required>
                                <option value="">Seçin...</option>
                                <option value="30">30 Gün (1 Ay)</option>
                                <option value="90">90 Gün (3 Ay)</option>
                                <option value="180">180 Gün (6 Ay)</option>
                                <option value="365">365 Gün (1 Yıl)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-warning">Üyeliği Yükselt</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Extend Modal -->
    <div class="modal fade" id="extendModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Üyelik Uzat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="extend_membership">
                        <input type="hidden" name="user_id" id="extend_user_id">
                        
                        <div class="alert alert-info">
                            <strong id="extend_user_name"></strong> kullanıcısının <span id="extend_membership_type"></span> üyeliğini uzatmak istiyorsunuz.
                        </div>
                        
                        <div class="mb-3">
                            <label for="extend_days" class="form-label">Uzatma Süresi (Gün)</label>
                            <select class="form-select" id="extend_days" name="extend_days" required>
                                <option value="">Seçin...</option>
                                <option value="7">7 Gün</option>
                                <option value="15">15 Gün</option>
                                <option value="30">30 Gün</option>
                                <option value="60">60 Gün</option>
                                <option value="90">90 Gün</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-primary">Üyeliği Uzat</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Downgrade Modal -->
    <div class="modal fade" id="downgradeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Üyelik Düşür</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="downgrade_user">
                        <input type="hidden" name="user_id" id="downgrade_user_id">
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong id="downgrade_user_name"></strong> kullanıcısının üyeliğini normal kullanıcıya düşürmek istiyorsunuz.
                        </div>
                        
                        <p class="text-muted">Bu işlem geri alınamaz ve kullanıcı premium içeriklere erişimini kaybedecektir.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-danger">Üyeliği Düşür</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Upgrade button handler
        document.querySelectorAll('.upgrade-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('upgrade_user_id').value = this.dataset.userId;
                document.getElementById('upgrade_user_name').textContent = this.dataset.userName;
                new bootstrap.Modal(document.getElementById('upgradeModal')).show();
            });
        });

        // Extend button handler
        document.querySelectorAll('.extend-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('extend_user_id').value = this.dataset.userId;
                document.getElementById('extend_user_name').textContent = this.dataset.userName;
                document.getElementById('extend_membership_type').textContent = this.dataset.membership.toUpperCase();
                new bootstrap.Modal(document.getElementById('extendModal')).show();
            });
        });

        // Downgrade button handler
        document.querySelectorAll('.downgrade-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('downgrade_user_id').value = this.dataset.userId;
                document.getElementById('downgrade_user_name').textContent = this.dataset.userName;
                new bootstrap.Modal(document.getElementById('downgradeModal')).show();
            });
        });
    </script>
</body>
</html>