<?php
/**
 * DOBİEN Video Platform - Hesap Ayarları
 * Geliştirici: DOBİEN
 * Modern Video Paylaşım Platformu
 */

require_once 'includes/config.php';

// Kullanıcı giriş kontrolü
if (!$current_user) {
    header('Location: giris.php');
    exit;
}

$page_title = "Hesap Ayarları - " . $site_settings['site_adi'];

// Form işlemleri
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        if ($action === 'change_password') {
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            // Mevcut şifre kontrolü
            if (!password_verify($current_password, $current_user['sifre'])) {
                throw new Exception('Mevcut şifreniz yanlış.');
            }
            
            // Yeni şifre kontrolü
            if (strlen($new_password) < 6) {
                throw new Exception('Yeni şifre en az 6 karakter olmalı.');
            }
            
            if ($new_password !== $confirm_password) {
                throw new Exception('Yeni şifreler eşleşmiyor.');
            }
            
            // Şifreyi güncelle
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE kullanicilar SET sifre = ? WHERE id = ?");
            $stmt->execute([$hashed_password, $current_user['id']]);
            
            $success_message = 'Şifreniz başarıyla değiştirildi.';
            
        } elseif ($action === 'update_notifications') {
            $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;
            $push_notifications = isset($_POST['push_notifications']) ? 1 : 0;
            
            $stmt = $pdo->prepare("UPDATE kullanicilar SET email_bildirimleri = ?, push_bildirimleri = ? WHERE id = ?");
            $stmt->execute([$email_notifications, $push_notifications, $current_user['id']]);
            
            $success_message = 'Bildirim ayarlarınız güncellendi.';
            
        } elseif ($action === 'delete_account') {
            $password = $_POST['delete_password'] ?? '';
            
            if (!password_verify($password, $current_user['sifre'])) {
                throw new Exception('Şifreniz yanlış.');
            }
            
            // Hesabı pasif yap (tamamen silmek yerine)
            $stmt = $pdo->prepare("UPDATE kullanicilar SET durum = 'silindi', silme_tarihi = NOW() WHERE id = ?");
            $stmt->execute([$current_user['id']]);
            
            // Oturumu sonlandır
            session_destroy();
            header('Location: index.php?deleted=1');
            exit;
        }
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Güncel kullanıcı bilgilerini al
$current_user = checkUserSession();

include 'includes/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="page-header mb-5">
                <h1 class="mb-2">
                    <i class="fas fa-cog me-3"></i>Hesap Ayarları
                </h1>
                <p class="text-muted">Hesap bilgilerinizi ve tercihlerinizi yönetin</p>
            </div>

            <?php if (isset($success_message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <!-- Şifre Değiştirme -->
            <div class="settings-card mb-4">
                <div class="card-header">
                    <h4><i class="fas fa-lock me-2"></i>Şifre Değiştir</h4>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="change_password">
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mevcut Şifre</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Yeni Şifre</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" 
                                   minlength="6" required>
                            <div class="form-text">En az 6 karakter olmalı</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Yeni Şifre (Tekrar)</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                   minlength="6" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Şifreyi Değiştir
                        </button>
                    </form>
                </div>
            </div>

            <!-- Bildirim Ayarları -->
            <div class="settings-card mb-4">
                <div class="card-header">
                    <h4><i class="fas fa-bell me-2"></i>Bildirim Ayarları</h4>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="update_notifications">
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="email_notifications" 
                                   name="email_notifications" <?php echo ($current_user['email_bildirimleri'] ?? 1) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="email_notifications">
                                <strong>E-posta Bildirimleri</strong>
                                <div class="form-text">Yeni videolar ve önemli güncellemeler hakkında e-posta alın</div>
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="push_notifications" 
                                   name="push_notifications" <?php echo ($current_user['push_bildirimleri'] ?? 1) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="push_notifications">
                                <strong>Push Bildirimleri</strong>
                                <div class="form-text">Tarayıcı bildirimleri alın</div>
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Bildirimleri Güncelle
                        </button>
                    </form>
                </div>
            </div>

            <!-- Hesap Bilgileri -->
            <div class="settings-card mb-4">
                <div class="card-header">
                    <h4><i class="fas fa-user me-2"></i>Hesap Bilgileri</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-3"><strong>Ad Soyad:</strong></div>
                        <div class="col-sm-9"><?php echo htmlspecialchars($current_user['ad'] . ' ' . $current_user['soyad']); ?></div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3"><strong>E-posta:</strong></div>
                        <div class="col-sm-9"><?php echo htmlspecialchars($current_user['email']); ?></div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3"><strong>Üyelik Tipi:</strong></div>
                        <div class="col-sm-9">
                            <span class="badge bg-<?php echo $current_user['uyelik_tipi'] === 'premium' ? 'danger' : ($current_user['uyelik_tipi'] === 'vip' ? 'warning' : 'secondary'); ?>">
                                <?php echo strtoupper($current_user['uyelik_tipi']); ?>
                            </span>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3"><strong>Kayıt Tarihi:</strong></div>
                        <div class="col-sm-9"><?php echo formatDate($current_user['kayit_tarihi'], 'd.m.Y H:i'); ?></div>
                    </div>
                    
                    <div class="mt-3">
                        <a href="profil.php" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i>Profili Düzenle
                        </a>
                    </div>
                </div>
            </div>

            <!-- Hesap Silme -->
            <div class="settings-card danger-zone">
                <div class="card-header bg-danger text-white">
                    <h4><i class="fas fa-exclamation-triangle me-2"></i>Tehlikeli Bölge</h4>
                </div>
                <div class="card-body">
                    <h5 class="text-danger">Hesabı Sil</h5>
                    <p class="text-muted">
                        Hesabınızı silmek istiyorsanız, bu işlem geri alınamaz. 
                        Tüm verileriniz kalıcı olarak silinecektir.
                    </p>
                    
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash me-2"></i>Hesabı Sil
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hesap Silme Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Hesabı Sil</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete_account">
                    
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Dikkat!</strong> Bu işlem geri alınamaz.
                    </div>
                    
                    <p>Hesabınızı silmek için şifrenizi onaylayın:</p>
                    
                    <div class="mb-3">
                        <label for="delete_password" class="form-label">Şifreniz</label>
                        <input type="password" class="form-control" id="delete_password" name="delete_password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Hesabı Sil
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.settings-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    overflow: hidden;
}

.settings-card .card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 20px;
}

.settings-card .card-header h4 {
    margin: 0;
    color: #495057;
}

.settings-card .card-body {
    padding: 30px;
}

.danger-zone {
    border: 2px solid #dc3545;
}

.form-check-label .form-text {
    margin-top: 5px;
    color: #6c757d;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'includes/footer.php'; ?>