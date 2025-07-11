# 🚀 DOBİEN Video Platform - Kurulum Talimatları

## 📥 Son Güncellemeler (11.07.2024)

✅ **Tüm hatalar düzeltildi!**
- Slider hatası çözüldü (demo veriler eklendi)
- Admin paneli tamamen tamamlandı
- Upload klasörleri otomatik oluşturuluyor
- Modern admin dashboard
- Responsive tasarım

## 📦 İndirme

**En güncel versiyon:** `dobien-video-platform-final-20250711.zip`

## 🛠️ Kurulum Adımları

### 1. Dosyaları Sunucuya Yükleyin
```bash
# ZIP dosyasını açın
unzip dobien-video-platform-final-20250711.zip

# Web dizinine kopyalayın
cp -r video-platform/* /var/www/html/
```

### 2. Dosya İzinleri
```bash
# Upload klasörleri için yazma izni
chmod -R 777 uploads/
chmod -R 777 config/

# Güvenlik için PHP dosyaları
chmod -R 644 *.php
```

### 3. Kurulum Sihirbazını Çalıştırın

**Tarayıcıda açın:** `http://yourdomain.com/install.php`

#### Adım 1: Sistem Kontrolleri
- PHP 7.4+ ✅
- PDO MySQL ✅
- Dosya yazma izinleri ✅

#### Adım 2: Veritabanı Ayarları
```
Sunucu: localhost
Veritabanı: dobien_video
Kullanıcı: [mysql_username]
Şifre: [mysql_password]
```

#### Adım 3: Admin Hesabı
```
Ad: [Your Name]
Soyad: [Your Surname]
E-posta: admin@yourdomain.com
Şifre: [strong_password]
```

#### Adım 4: Kurulum Tamamla
- Veritabanı tabloları oluşturulur
- Demo veriler eklenir
- Upload klasörleri hazırlanır

## 🎯 Admin Panel Erişimi

**URL:** `http://yourdomain.com/admin/giris.php`
**Giriş:** Kurulum sırasında oluşturduğunuz hesap

## 📱 Admin Panel Özellikleri

### 🖼️ Slider Yönetimi
- Resim yükleme (1920x800px önerilen)
- Sıralama ve düzenleme
- Aktif/pasif durumu

### 🎬 Video Yönetimi
- Video yükleme ve düzenleme
- Kategori atama
- Kalite seviyeleri (720p, 1080p, 4K)
- İzlenme yetkisi (Herkes, VIP, Premium)

### 👥 Kullanıcı Yönetimi
- Üyelik tipleri (Kullanıcı, VIP, Premium)
- Hesap durumları
- Üyelik yükseltme

### 📊 İstatistikler
- Gerçek zamanlı dashboard
- Kullanıcı ve video istatistikleri
- Gelir raporları

## 🎨 Tasarım Özellikleri

### Frontend
- **Bootstrap 5** responsive tasarım
- **Font Awesome** ikonlar
- **Modern video player**
- **Yaş doğrulama popup**
- **3 üyelik seviyesi** (farklı kalite erişimi)

### Admin Panel
- **Bootstrap 5** admin tema
- **DataTables** gelişmiş tablolar
- **Chart.js** grafikler
- **SweetAlert2** güzel popup'lar
- **Drag & drop** dosya yükleme

## 🔧 Teknik Gereksinimler

### Sunucu Gereksinimleri
- **PHP:** 7.4 veya üzeri
- **MySQL:** 5.7 veya üzeri
- **Apache/Nginx** web sunucusu
- **PDO MySQL** extension
- **JSON, MBString** extensions

### PHP Ayarları
```ini
upload_max_filesize = 500M
post_max_size = 500M
memory_limit = 256M
max_execution_time = 300
```

## 📁 Klasör Yapısı

```
video-platform/
├── admin/                 # Admin paneli
│   ├── assets/           # CSS, JS dosyaları
│   ├── includes/         # Header, footer, sidebar
│   ├── index.php         # Admin dashboard
│   ├── slider.php        # Slider yönetimi
│   ├── videolar.php      # Video yönetimi
│   └── kullanicilar.php  # Kullanıcı yönetimi
├── uploads/              # Yüklenen dosyalar
│   ├── videos/          # Video dosyaları
│   ├── thumbnails/      # Video kapak resimleri
│   ├── slider/          # Slider resimleri
│   └── categories/      # Kategori resimleri
├── includes/            # Ortak PHP dosyaları
├── assets/              # Frontend CSS/JS
├── install.php          # Kurulum sihirbazı
├── index.php           # Ana sayfa
└── database.sql        # Veritabanı yapısı
```

## 🎭 Özellik Listesi

### 🔒 Üyelik Sistemi
- **3 seviye:** Kullanıcı, VIP, Premium
- **Video kaliteleri:** 720p, 1080p, 4K
- **E-posta doğrulama**
- **Şifre sıfırlama**

### 🎥 Video Sistem
- **Çoklu kalite desteği**
- **Kategori sistemi**
- **Beğeni/beğenmeme**
- **Favori listesi**
- **İzleme geçmişi**

### 💰 Ödeme Sistemi (Hazır)
- **Üyelik yükseltme**
- **Ödeme geçmişi**
- **Gelir raporları**

### 🛡️ Güvenlik
- **SQL injection koruması**
- **XSS koruması**
- **CSRF token**
- **Güvenli dosya yükleme**

## 🚨 Önemli Notlar

1. **Güvenlik:** Kurulum sonrası `install.php` dosyasını silin
2. **Backup:** Düzenli veritabanı yedeği alın
3. **SSL:** HTTPS kullanımı önerilir
4. **Dosya boyutu:** Video dosyaları için yeterli disk alanı

## 📞 Destek

**Geliştirici:** DOBİEN
**GitHub:** https://github.com/Berocan025/Vdeo

### Sık Karşılaşılan Sorunlar

**Q: Slider gösterilmiyor**
A: Admin panelden slider ekleyin ve aktif edin

**Q: Video yüklenmiyor**
A: PHP upload limitlerini kontrol edin

**Q: Admin paneline erişemiyorum**
A: Veritabanı bağlantısını kontrol edin

---

## 🎉 Kurulum Tamamlandı!

Artık **DOBİEN Video Platform** kullanıma hazır!

**Ana Sayfa:** `http://yourdomain.com`
**Admin Panel:** `http://yourdomain.com/admin`

---

*© 2024 DOBİEN - Modern Video Platform Sistemi*