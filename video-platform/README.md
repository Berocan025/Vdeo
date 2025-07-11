# DOBİEN Video Platform

**Geliştirici:** DOBİEN  
**Modern Video Paylaşım Platformu**  
**Tüm Hakları Saklıdır © DOBİEN**

---

## 📋 İçindekiler

- [Genel Bakış](#genel-bakış)
- [Özellikler](#özellikler)
- [Kurulum](#kurulum)
- [Sistem Gereksinimleri](#sistem-gereksinimleri)
- [Veritabanı Kurulumu](#veritabanı-kurulumu)
- [Yapılandırma](#yapılandırma)
- [Kullanım](#kullanım)
- [Admin Panel](#admin-panel)
- [API Dokümantasyonu](#api-dokümantasyonu)
- [Güvenlik](#güvenlik)
- [Katkıda Bulunma](#katkıda-bulunma)
- [Lisans](#lisans)

---

## 🎯 Genel Bakış

DOBİEN Video Platform, modern PHP teknolojileri kullanılarak geliştirilmiş profesyonel bir video paylaşım platformudur. Platform, üç farklı üyelik seviyesi (Kullanıcı, VIP, Premium) ile video kalitesi kontrolü, gelişmiş admin paneli ve mobil uyumlu tasarım sunmaktadır.

### 🎪 Canlı Demo
- **Frontend:** [https://dobien-video.demo.com](https://dobien-video.demo.com)
- **Admin Panel:** [https://dobien-video.demo.com/admin](https://dobien-video.demo.com/admin)
  - **Kullanıcı:** admin@dobien.com
  - **Şifre:** admin123

---

## ✨ Özellikler

### 👥 Kullanıcı Özellikleri
- **Üç Üyelik Seviyesi:** Kullanıcı (720p), VIP (1080p), Premium (4K)
- **Modern Video Oynatıcı:** Kalite seçimi, hız kontrolü, tam ekran, PiP desteği
- **Yaş Doğrulama:** 18+ popup kontrolü
- **Responsive Tasarım:** Tüm cihazlarda mükemmel görünüm
- **Favoriler & İzleme Geçmişi:** Kişiselleştirilmiş deneyim
- **Gelişmiş Arama:** Kategori ve etiket bazlı filtreleme
- **Sosyal Özellikler:** Beğeni, yorum, paylaşım

### 🎬 Video Özellikleri
- **Çoklu Kalite Desteği:** 720p, 1080p, 4K
- **Akıllı Streaming:** Kullanıcı üyeliğine göre otomatik kalite
- **Video Yönetimi:** Toplu yükleme, düzenleme, kategorizasyon
- **Thumbnail Oluşturma:** Otomatik kapak resmi üretimi
- **İstatistikler:** Detaylı izlenme ve etkileşim verileri

### 🛠 Admin Panel
- **Kapsamlı Yönetim:** Tüm site içeriği admin panelinden yönetilebilir
- **Site Ayarları:** Logo, favicon, başlık, açıklama, anahtar kelimeler
- **Kullanıcı Yönetimi:** Üyelik seviyesi, durum, istatistikler
- **Video Yönetimi:** Yükleme, düzenleme, kategorizasyon, moderasyon
- **İstatistikler:** Gerçek zamanlı analitik ve raporlar
- **SEO Yönetimi:** Meta etiketler, sitemap, robots.txt

### 🎨 Tasarım & UX
- **Dark Theme:** Modern ve şık koyu tema
- **Animasyonlar:** Smooth geçişler ve hover efektleri
- **Mobil Uyumlu:** Touch-friendly kontroller
- **Accessibility:** Screen reader ve klavye navigasyon desteği

### 🔒 Güvenlik
- **Password Hashing:** bcrypt ile güvenli şifreleme
- **CSRF Protection:** Cross-site request forgery koruması
- **SQL Injection:** Prepared statements ile korunma
- **Input Sanitization:** XSS saldırılarına karşı koruma
- **Rate Limiting:** API kötüye kullanım önleme

---

## 🚀 Kurulum

### 1. Dosyaları İndirin
```bash
git clone https://github.com/dobien/video-platform.git
cd video-platform
```

### 2. Web Sunucusuna Yükleyin
Tüm dosyaları web sunucunuzun root dizinine kopyalayın.

### 3. Kurulum Sihirbazını Çalıştırın
Tarayıcınızda `http://yourdomain.com/install.php` adresine gidin ve kurulum adımlarını takip edin.

### 4. Dosya İzinlerini Ayarlayın
```bash
chmod 755 uploads/
chmod 755 uploads/videos/
chmod 755 uploads/thumbnails/
chmod 755 uploads/categories/
chmod 755 uploads/slider/
chmod 644 includes/config.php
```

---

## ⚙️ Sistem Gereksinimleri

### Minimum Gereksinimler
- **PHP:** 7.4 veya üzeri (önerilen: 8.0+)
- **MySQL:** 5.7 veya üzeri (önerilen: 8.0+)
- **Web Sunucusu:** Apache 2.4+ veya Nginx 1.18+
- **Disk Alanı:** 1GB+ (videolar için ek alan gerekli)
- **RAM:** 256MB+ (önerilen: 512MB+)

### PHP Eklentileri
```php
- PDO MySQL
- GD Library
- cURL
- OpenSSL
- Mbstring
- JSON
- Fileinfo
```

### Apache Modülleri
```apache
- mod_rewrite
- mod_headers
- mod_expires
```

---

## 🗄️ Veritabanı Kurulumu

### Manuel Kurulum
```sql
-- 1. Veritabanı oluşturun
CREATE DATABASE dobien_video CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. SQL dosyasını import edin
mysql -u username -p dobien_video < database.sql

-- 3. Kullanıcı oluşturun (isteğe bağlı)
CREATE USER 'dobien_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON dobien_video.* TO 'dobien_user'@'localhost';
FLUSH PRIVILEGES;
```

### Otomatik Kurulum
Web arayüzünden `install.php` dosyasını çalıştırarak otomatik kurulum yapabilirsiniz.

---

## 🔧 Yapılandırma

### Temel Ayarlar (`includes/config.php`)
```php
// Veritabanı ayarları
define('DB_HOST', 'localhost');
define('DB_NAME', 'dobien_video');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');

// Site ayarları
define('SITE_URL', 'https://yourdomain.com');
define('SITE_TITLE', 'DOBİEN Video Platform');

// Güvenlik ayarları
define('SECURITY_SALT', 'your-unique-salt-key');
define('SESSION_TIMEOUT', 3600); // 1 saat
```

### Medya Ayarları
```php
// Video yükleme limitleri
define('MAX_VIDEO_SIZE', 500 * 1024 * 1024); // 500MB
define('ALLOWED_VIDEO_TYPES', ['mp4', 'avi', 'mov', 'wmv']);

// Kalite ayarları
define('DEFAULT_QUALITY', '720p');
define('ENABLE_4K', true);
define('ENABLE_1080P', true);
```

### SMTP Ayarları (E-posta)
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_FROM_EMAIL', 'noreply@yourdomain.com');
define('SMTP_FROM_NAME', 'DOBİEN Video Platform');
```

---

## 📖 Kullanım

### Üye Kayıt & Giriş
1. `/kayit.php` - Yeni üye kaydı
2. `/giris.php` - Üye girişi
3. E-posta aktivasyonu (opsiyonel)

### Video İzleme
1. Ana sayfada videolara göz atın
2. Kategori veya arama ile filtreleyin
3. Üyelik seviyenize göre video kalitesi otomatik ayarlanır
4. Video oynatıcıda kalite, hız ve tam ekran kontrollerini kullanın

### Admin Panel Erişimi
1. `/admin/giris.php` - Admin girişi
2. Süper admin hesabı ile giriş yapın
3. Dashboard'dan tüm işlemleri yönetin

---

## 🔐 Admin Panel

### Dashboard
- **Sistem İstatistikleri:** Kullanıcı, video, izlenme sayıları
- **Son Aktiviteler:** Yeni kayıtlar, yorumlar, şikayetler
- **Hızlı İşlemler:** Video onaylama, kullanıcı moderasyonu

### Site Ayarları
- **Genel:** Site başlığı, açıklama, logo, favicon
- **SEO:** Meta etiketler, anahtar kelimeler
- **Yaş Doğrulama:** Popup ayarları ve mesajlar
- **SMTP:** E-posta sunucu ayarları
- **Analitik:** Google Analytics, Facebook Pixel

### İçerik Yönetimi
- **Videolar:** Yükleme, düzenleme, kategorizasyon
- **Kategoriler:** Kategori ekleme, düzenleme, sıralama
- **Slider:** Ana sayfa slider yönetimi
- **Menü:** Navigasyon menüsü düzenleme
- **Sayfalar:** Statik sayfa oluşturma (Hakkımızda, İletişim vb.)

### Kullanıcı Yönetimi
- **Kullanıcı Listesi:** Filtreleme, arama, toplu işlemler
- **Üyelik Seviyeleri:** Manual üyelik yükseltme/düşürme
- **Yasaklama:** Kullanıcı suspansiyonu
- **İstatistikler:** Kullanıcı aktivite raporları

---

## 🔌 API Dokümantasyonu

### Video İşlemleri

#### Beğeni/Beğenmeme
```javascript
POST /api/toggle-like.php
Content-Type: application/json

{
    "video_id": 123,
    "type": "like" // veya "dislike"
}

// Yanıt
{
    "success": true,
    "user_liked": true,
    "user_disliked": false,
    "like_count": 45,
    "dislike_count": 3
}
```

#### Favorilere Ekleme
```javascript
POST /api/toggle-favorite.php
Content-Type: application/json

{
    "video_id": 123
}

// Yanıt
{
    "success": true,
    "is_favorite": true,
    "favorite_count": 12,
    "message": "Video favorilere eklendi."
}
```

#### Video Şikayeti
```javascript
POST /api/report-video.php
Content-Type: application/json

{
    "video_id": 123,
    "reason": "Uygunsuz içerik"
}

// Yanıt
{
    "success": true,
    "message": "Şikayetiniz başarıyla gönderildi."
}
```

### Hata Kodları
- **400:** Bad Request - Geçersiz veri
- **401:** Unauthorized - Giriş gerekli
- **404:** Not Found - Kaynak bulunamadı
- **429:** Too Many Requests - Rate limit aşıldı
- **500:** Internal Server Error - Sunucu hatası

---

## 🛡️ Güvenlik

### Güvenlik Önlemleri
1. **SQL Injection:** PDO prepared statements kullanımı
2. **XSS Prevention:** Input sanitization ve output encoding
3. **CSRF Protection:** Token-based form koruması
4. **Password Security:** bcrypt hashing algoritması
5. **Session Security:** Secure cookies ve session regeneration
6. **File Upload Security:** MIME type kontrolü ve dosya doğrulama

### Güvenlik Ayarları
```php
// Session güvenliği
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);

// Header güvenliği
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
```

### Önerilen Sunucu Ayarları
```apache
# .htaccess
RewriteEngine On

# HTTPS yönlendirme
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Güvenlik başlıkları
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"

# Dosya erişim kısıtlaması
<Files "*.sql">
    Order allow,deny
    Deny from all
</Files>
```

---

## 📁 Dosya Yapısı

```
video-platform/
├── admin/                  # Admin panel
│   ├── assets/            # Admin CSS/JS/Images
│   ├── includes/          # Admin header/sidebar/footer
│   ├── index.php          # Admin dashboard
│   ├── giris.php          # Admin login
│   └── site-ayarlari.php  # Site settings
├── api/                   # API endpoints
│   ├── toggle-like.php    # Like/dislike API
│   ├── toggle-favorite.php # Favorite API
│   └── report-video.php   # Report API
├── assets/                # Frontend assets
│   ├── css/              # Stylesheets
│   ├── js/               # JavaScript files
│   └── images/           # Images and icons
├── includes/              # Core includes
│   ├── config.php        # Database and config
│   ├── header.php        # Site header
│   └── footer.php        # Site footer
├── uploads/               # User uploads
│   ├── videos/           # Video files
│   ├── thumbnails/       # Video thumbnails
│   ├── categories/       # Category images
│   └── slider/           # Slider images
├── index.php             # Homepage
├── video.php             # Video player page
├── videolar.php          # Video listing page
├── giris.php             # User login
├── kayit.php             # User registration
├── profil.php            # User profile
├── uyelik-yukselt.php    # Membership upgrade
├── install.php           # Installation wizard
├── database.sql          # Database structure
└── README.md             # This file
```

---

## 🎯 Sık Sorulan Sorular

### Video yükleme sorunu yaşıyorum?
1. PHP `upload_max_filesize` ve `post_max_size` ayarlarını kontrol edin
2. Uploads klasörünün yazma izinlerini kontrol edin
3. Video formatının desteklendiğinden emin olun

### Admin paneline erişemiyorum?
1. Veritabanında admin kullanıcısı oluşturulmuş mu kontrol edin
2. Şifrenin doğru hash'lendiğinden emin olun
3. Session ayarlarını kontrol edin

### Video kalitesi değişmiyor?
1. Farklı kalitelerdeki video dosyalarının yüklendiğinden emin olun
2. Kullanıcının üyelik seviyesini kontrol edin
3. Browser cache'ini temizleyin

---

## 🤝 Katkıda Bulunma

### Geliştirme Ortamı Kurulumu
```bash
# Repository'yi fork edin
git clone https://github.com/yourusername/dobien-video-platform.git
cd dobien-video-platform

# Development branch oluşturun
git checkout -b feature/yeni-ozellik

# Değişikliklerinizi commit edin
git commit -m "Yeni özellik: Açıklama"

# Pull request gönderin
git push origin feature/yeni-ozellik
```

### Kod Standardları
- PSR-4 autoloading standardı
- Camelcase fonksiyon isimleri
- Türkçe yorum satırları
- DOBİEN header'ı tüm dosyalarda

### Hata Bildirimi
GitHub Issues üzerinden hata bildirimi yapabilirsiniz:
1. Hatanın detaylı açıklaması
2. Reproducing steps
3. Beklenen davranış
4. Sistem bilgileri (PHP, MySQL versiyonları)

---

## 📞 Destek & İletişim

### Teknik Destek
- **E-posta:** support@dobien.com
- **GitHub Issues:** [github.com/dobien/video-platform/issues](https://github.com/dobien/video-platform/issues)
- **Dökümantasyon:** [docs.dobien.com](https://docs.dobien.com)

### Ticari Lisans
Ticari kullanım için özel lisans seçenekleri mevcuttur:
- **E-posta:** license@dobien.com
- **Telefon:** +90 xxx xxx xx xx

---

## 📄 Lisans

Bu proje MIT lisansı altında lisanslanmıştır. Detaylar için [LICENSE](LICENSE) dosyasını inceleyiniz.

```
MIT License

Copyright (c) 2024 DOBİEN

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

---

## 🚀 Changelog

### v1.0.0 (2024-01-15)
- ✨ İlk stabil sürüm
- 🎬 Video oynatıcı ve çoklu kalite desteği
- 👥 Üyelik sistemi (Kullanıcı, VIP, Premium)
- 🛠️ Kapsamlı admin paneli
- 📱 Responsive tasarım
- 🔒 Güvenlik güncellemeleri

---

## 💡 Gelecek Özellikler

### v1.1.0 (Planlanmakta)
- 📺 Canlı yayın desteği
- 💬 Gerçek zamanlı chat sistemi
- 🔔 Push notification desteği
- 📊 Gelişmiş analitik dashboard
- 🌍 Çoklu dil desteği

### v1.2.0 (Planlanmakta)
- 🤖 AI destekli video önerileri
- 🎮 Interactive video özelliği
- 📱 Mobile app API'si
- ☁️ Cloud storage entegrasyonu

---

**Geliştirici:** DOBİEN  
**Web:** [https://dobien.com](https://dobien.com)  
**E-posta:** info@dobien.com  

*Bu dokümantasyon DOBİEN Video Platform v1.0.0 için hazırlanmıştır.*