<?php
/**
 * DOBİEN Video Platform - Admin Yönetimi
 * Geliştirici: DOBİEN
 * Admin Panel - Admin Kullanıcı Yönetimi
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

// CRUD işlemleri
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            try {
                $ad = trim($_POST['ad'] ?? '');
                $soyad = trim($_POST['soyad'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $kullanici_adi = trim($_POST['kullanici_adi'] ?? '');
                $sifre = $_POST['sifre'] ?? '';
                $rol = $_POST['rol'] ?? 'admin';
                $durum = $_POST['durum'] ?? 'aktif';
                
                // Validation
                if (empty($ad) || empty($soyad) || empty($email) || empty($kullanici_adi) || empty($sifre)) {
                    throw new Exception('Tüm alanları doldurunuz.');
                }
                
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('Geçerli bir e-posta adresi giriniz.');
                }
                
                if (strlen($sifre) < 6) {
                    throw new Exception('Şifre en az 6 karakter olmalıdır.');
                }
                
                // E-posta ve kullanıcı adı benzersizlik kontrolü
                $check_stmt = $pdo->prepare("SELECT id FROM admin_kullanicilar WHERE email = ? OR kullanici_adi = ?");
                $check_stmt->execute([$email, $kullanici_adi]);
                if ($check_stmt->fetch()) {
                    throw new Exception('Bu e-posta veya kullanıcı adı zaten kullanılıyor.');
                }
                
                // Şifreyi hashle
                $hashed_password = password_hash($sifre, PASSWORD_DEFAULT);
                
                // Admin ekle
                $insert_stmt = $pdo->prepare("
                    INSERT INTO admin_kullanicilar (ad, soyad, email, kullanici_adi, sifre, rol, durum, olusturma_tarihi) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                $insert_stmt->execute([$ad, $soyad, $email, $kullanici_adi, $hashed_password, $rol, $durum]);
                
                $message = 'Admin başarıyla eklendi.';
                $message_type = 'success';
                
            } catch (Exception $e) {
                $message = $e->getMessage();
                $message_type = 'error';
            }
            break;
            
        case 'edit':
            try {
                $id = (int)$_POST['id'];
                $ad = trim($_POST['ad'] ?? '');
                $soyad = trim($_POST['soyad'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $kullanici_adi = trim($_POST['kullanici_adi'] ?? '');
                $rol = $_POST['rol'] ?? 'admin';
                $durum = $_POST['durum'] ?? 'aktif';
                $sifre = $_POST['sifre'] ?? '';
                
                if (empty($ad) || empty($soyad) || empty($email) || empty($kullanici_adi)) {
                    throw new Exception('Tüm alanları doldurunuz.');
                }
                
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('Geçerli bir e-posta adresi giriniz.');
                }
                
                // E-posta ve kullanıcı adı benzersizlik kontrolü (kendisi hariç)
                $check_stmt = $pdo->prepare("SELECT id FROM admin_kullanicilar WHERE (email = ? OR kullanici_adi = ?) AND id != ?");
                $check_stmt->execute([$email, $kullanici_adi, $id]);
                if ($check_stmt->fetch()) {
                    throw new Exception('Bu e-posta veya kullanıcı adı başka bir admin tarafından kullanılıyor.');
                }
                
                // Şifre güncelleme kontrolü
                if (!empty($sifre)) {
                    if (strlen($sifre) < 6) {
                        throw new Exception('Şifre en az 6 karakter olmalıdır.');
                    }
                    $hashed_password = password_hash($sifre, PASSWORD_DEFAULT);
                    $update_stmt = $pdo->prepare("
                        UPDATE admin_kullanicilar 
                        SET ad = ?, soyad = ?, email = ?, kullanici_adi = ?, sifre = ?, rol = ?, durum = ? 
                        WHERE id = ?
                    ");
                    $update_stmt->execute([$ad, $soyad, $email, $kullanici_adi, $hashed_password, $rol, $durum, $id]);
                } else {
                    $update_stmt = $pdo->prepare("
                        UPDATE admin_kullanicilar 
                        SET ad = ?, soyad = ?, email = ?, kullanici_adi = ?, rol = ?, durum = ? 
                        WHERE id = ?
                    ");
                    $update_stmt->execute([$ad, $soyad, $email, $kullanici_adi, $rol, $durum, $id]);
                }
                
                $message = 'Admin bilgileri başarıyla güncellendi.';
                $message_type = 'success';
                
            } catch (Exception $e) {
                $message = $e->getMessage();
                $message_type = 'error';
            }
            break;
            
        case 'delete':
            try {
                $id = (int)$_POST['id'];
                
                // Kendi hesabını silmeye çalışıyor mu?
                if ($id == $_SESSION['admin_id']) {
                    throw new Exception('Kendi hesabınızı silemezsiniz.');
                }
                
                $delete_stmt = $pdo->prepare("DELETE FROM admin_kullanicilar WHERE id = ?");
                $delete_stmt->execute([$id]);
                
                $message = 'Admin başarıyla silindi.';
                $message_type = 'success';
                
            } catch (Exception $e) {
                $message = $e->getMessage();
                $message_type = 'error';
            }
            break;
    }
}

// Admin listesini çek
try {
    $admins = $pdo->query("SELECT * FROM admin_kullanicilar ORDER BY olusturma_tarihi DESC")->fetchAll();
} catch (PDOException $e) {
    $admins = [];
}

$page_title = "Admin Yönetimi";
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
                <h1><i class="fas fa-user-shield"></i> Admin Yönetimi</h1>
                <button class="btn btn-primary" data-modal="adminModal">
                    <i class="fas fa-plus"></i> Yeni Admin Ekle
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
                    <h3>Admin Kullanıcıları</h3>
                </div>
                <div class="card-content">
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Ad Soyad</th>
                                    <th>E-posta</th>
                                    <th>Kullanıcı Adı</th>
                                    <th>Rol</th>
                                    <th>Durum</th>
                                    <th>Oluşturma Tarihi</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($admins as $admin): ?>
                                <tr>
                                    <td>
                                        <div class="user-info">
                                            <img src="<?php echo $admin['avatar'] ?? '../assets/images/default-avatar.png'; ?>" 
                                                 alt="<?php echo htmlspecialchars($admin['ad'] . ' ' . $admin['soyad']); ?>" 
                                                 class="user-avatar">
                                            <div>
                                                <div class="user-name"><?php echo htmlspecialchars($admin['ad'] . ' ' . $admin['soyad']); ?></div>
                                                <?php if ($admin['id'] == $_SESSION['admin_id']): ?>
                                                <span class="badge badge-info">Siz</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($admin['email']); ?></td>
                                    <td><?php echo htmlspecialchars($admin['kullanici_adi']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $admin['rol'] == 'super_admin' ? 'danger' : 'primary'; ?>">
                                            <?php echo $admin['rol'] == 'super_admin' ? 'Süper Admin' : 'Admin'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php echo $admin['durum'] == 'aktif' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst($admin['durum']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo formatDate($admin['olusturma_tarihi'], 'd.m.Y H:i'); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-sm btn-primary edit-admin-btn" 
                                                    data-id="<?php echo $admin['id']; ?>"
                                                    data-ad="<?php echo htmlspecialchars($admin['ad']); ?>"
                                                    data-soyad="<?php echo htmlspecialchars($admin['soyad']); ?>"
                                                    data-email="<?php echo htmlspecialchars($admin['email']); ?>"
                                                    data-kullanici-adi="<?php echo htmlspecialchars($admin['kullanici_adi']); ?>"
                                                    data-rol="<?php echo $admin['rol']; ?>"
                                                    data-durum="<?php echo $admin['durum']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if ($admin['id'] != $_SESSION['admin_id']): ?>
                                            <button class="btn btn-sm btn-danger delete-admin-btn" 
                                                    data-id="<?php echo $admin['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($admin['ad'] . ' ' . $admin['soyad']); ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                
                                <?php if (empty($admins)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">Henüz admin kullanıcı bulunmuyor.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Admin Ekleme/Düzenleme Modal -->
<div class="modal" id="adminModal">
    <div class="modal-overlay"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Yeni Admin Ekle</h3>
            <button class="modal-close">&times;</button>
        </div>
        <form id="adminForm" method="POST">
            <div class="modal-body">
                <input type="hidden" name="action" id="action" value="add">
                <input type="hidden" name="id" id="adminId">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="ad">Ad *</label>
                        <input type="text" id="ad" name="ad" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="soyad">Soyad *</label>
                        <input type="text" id="soyad" name="soyad" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">E-posta *</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="kullanici_adi">Kullanıcı Adı *</label>
                    <input type="text" id="kullanici_adi" name="kullanici_adi" required>
                </div>
                
                <div class="form-group">
                    <label for="sifre">Şifre <span id="passwordNote">(En az 6 karakter)</span></label>
                    <input type="password" id="sifre" name="sifre" minlength="6">
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="rol">Rol</label>
                        <select id="rol" name="rol">
                            <option value="admin">Admin</option>
                            <option value="super_admin">Süper Admin</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="durum">Durum</label>
                        <select id="durum" name="durum">
                            <option value="aktif">Aktif</option>
                            <option value="pasif">Pasif</option>
                        </select>
                    </div>
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
            <h3>Admin Sil</h3>
            <button class="modal-close">&times;</button>
        </div>
        <form id="deleteForm" method="POST">
            <div class="modal-body">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="deleteAdminId">
                
                <div class="delete-confirmation">
                    <i class="fas fa-exclamation-triangle text-danger"></i>
                    <p>Bu admin kullanıcısını silmek istediğinizden emin misiniz?</p>
                    <p><strong id="deleteAdminName"></strong></p>
                    <p class="text-danger">Bu işlem geri alınamaz!</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close">İptal</button>
                <button type="submit" class="btn btn-danger">Sil</button>
            </div>
        </form>
    </div>
</div>

<script src="assets/js/admin.js"></script>
<script>
// Admin yönetimi JavaScript
$(document).ready(function() {
    // Alert kapatma
    $('.alert-close').click(function() {
        $(this).parent().fadeOut();
    });
    
    // Düzenleme butonu
    $('.edit-admin-btn').click(function() {
        const id = $(this).data('id');
        const ad = $(this).data('ad');
        const soyad = $(this).data('soyad');
        const email = $(this).data('email');
        const kullaniciAdi = $(this).data('kullanici-adi');
        const rol = $(this).data('rol');
        const durum = $(this).data('durum');
        
        $('#modalTitle').text('Admin Düzenle');
        $('#action').val('edit');
        $('#adminId').val(id);
        $('#ad').val(ad);
        $('#soyad').val(soyad);
        $('#email').val(email);
        $('#kullanici_adi').val(kullaniciAdi);
        $('#rol').val(rol);
        $('#durum').val(durum);
        $('#sifre').removeAttr('required');
        $('#passwordNote').text('(Boş bırakırsanız mevcut şifre korunur)');
        
        $('#adminModal').addClass('show');
    });
    
    // Yeni admin butonu
    $('[data-modal="adminModal"]').click(function() {
        $('#modalTitle').text('Yeni Admin Ekle');
        $('#action').val('add');
        $('#adminForm')[0].reset();
        $('#adminId').val('');
        $('#sifre').attr('required', 'required');
        $('#passwordNote').text('(En az 6 karakter)');
        
        $('#adminModal').addClass('show');
    });
    
    // Silme butonu
    $('.delete-admin-btn').click(function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        $('#deleteAdminId').val(id);
        $('#deleteAdminName').text(name);
        
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