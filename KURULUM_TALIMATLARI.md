# 🎬 DOBİEN Video Platform - Kurulum Talimatları

## 🔧 Sorun Çözüldü! ✅

**Ana Sorun:** Install.php ile kurulum yaptıktan sonra hiçbir sayfa açılmıyor ve sürekli install.php'ye yönlendiriyordu.

**Çözüm:** Config klasörü eksikliği ve database tablo uyumsuzluğu sorunu tamamen giderildi.

---

## 📋 Sistem Gereksinimleri

- **PHP 7.4 veya üzeri**
- **MySQL 5.7 veya üzeri**
- **Apache/Nginx Web Sunucusu**
- **PDO MySQL Extension**
- **GD Library** (resim işlemleri için)
- **mod_rewrite** aktif (Apache için)

---

## 🚀 Kurulum Adımları

### 1. Dosyaları İndirin
```bash
# GitHub'dan projeyi klonlayın
git clone -b cursor/geli-mi-video-payla-m-sitesi-olu-turma-3864 https://github.com/Berocan025/Vdeo.git

# Veya ZIP dosyasını indirin
# dobien-video-platform-guncel.zip dosyasını web sunucunuzun root klasörüne çıkartın
```

### 2. Dosya İzinlerini Ayarlayın
```bash
# Linux/macOS için
chmod -R 755 video-platform/
chmod -R 777 video-platform/uploads/
chmod -R 777 video-platform/config/

# Windows'ta uploads ve config klasörlerine yazma izni verin
```

### 3. Veritabanı Hazırlığı
- MySQL/MariaDB'de yeni bir veritabanı oluşturun
- Veritabanı kullanıcısına tüm yetkiler verin

### 4. Kurulum Sihirbazını Çalıştırın
1. Web tarayıcınızdan `http://yourdomain.com/video-platform/install.php` adresine gidin
2. Veritabanı bilgilerini girin:
   - **Sunucu:** localhost (genellikle)
   - **Veritabanı Adı:** Oluşturduğunuz veritabanı adı
   - **Kullanıcı Adı:** MySQL kullanıcı adı
   - **Şifre:** MySQL şifresi

3. Admin hesabı oluşturun:
   - **E-posta:** Admin e-posta adresi
   - **Şifre:** Güçlü bir şifre seçin

4. Site ayarları:
   - **Site Adı:** DOBİEN Video Platform
   - **Site URL:** Sitenizin tam URL'si

5. **"Kurulumu Başlat"** butonuna tıklayın

### 5. Kurulum Tamamlandı! 🎉
- Ana sayfa: `http://yourdomain.com/video-platform/`
- Admin paneli: `http://yourdomain.com/video-platform/admin/`

---

## 🎯 Özellikler

### 🏠 Ana Site
- **18+ Yaş Doğrulama** popupı
- **3 Üyelik Tipi:** Kullanıcı (720p), VIP (1080p), Premium (4K)
- **Video Kalite Kısıtlamaları** üyelik tipine göre
- **Gelişmiş Arama** ve filtreleme
- **Kategori Sistemi**
- **Favori ve Beğeni** sistemi
- **Responsive Tasarım** (mobil uyumlu)
- **Karanlık Tema**

### 🛠️ Admin Paneli
- **Modern Dashboard** ile istatistikler
- **Video Yönetimi:** Upload, düzenleme, kalite seçimi
- **Kullanıcı Yönetimi:** Üyelik yükseltme, kullanıcı kontrolü
- **Kategori Yönetimi:** Kategori ekleme/düzenleme
- **Site Ayarları:** Logo, başlık, açıklama, analytics kodları
- **Yaş Doğrulama Kontrolü**
- **Güvenlik Logları**

---

## 📧 Demo Hesaplar

### Admin Hesabı
Kurulum sırasında oluşturduğunuz hesap ile giriş yapabilirsiniz.

### Test Kullanıcısı (Otomatik oluşturulur)
- **E-posta:** demo@dobien.com
- **Şifre:** demo123
- **Üyelik:** Premium

---

## 🔧 Yapılandırma

### Video Upload Ayarları
1. **Admin Panel → Site Ayarları** bölümünden:
   - Maksimum dosya boyutu (varsayılan: 500MB)
   - İzin verilen formatlar (mp4, avi, mov, wmv)
   - Varsayılan video kalitesi

### E-posta Ayarları
1. **Admin Panel → Site Ayarları → E-posta** bölümünden:
   - SMTP sunucu bilgileri
   - Sistem e-posta adresi
   - E-posta şablonları

### Güvenlik Ayarları
- Şifreler bcrypt ile hashlenir
- CSRF koruması aktif
- SQL injection koruması
- XSS koruması

---

## 🎨 Özelleştirme

### Logo ve Favicon
- **Admin Panel → Site Ayarları** bölümünden yükleyebilirsiniz
- Logo: PNG/JPG formatında, önerilen boyut: 200x60px
- Favicon: ICO formatında, 32x32px

### Renkler ve Tema
- `/assets/css/style.css` dosyasından özelleştirebilirsiniz
- CSS değişkenleri ile kolay renk değişimi

### Yaş Doğrulama
- **Admin Panel → Site Ayarları** bölümünden:
  - Aktif/Pasif yapabilirsiniz
  - Popup başlığını değiştirebilirsiniz
  - Uyarı mesajını özelleştirebilirsiniz

---

## 🆘 Sorun Giderme

### Kurulum Sonrası Sorunlar

#### 1. Sürekli install.php'ye yönlendiriyor
✅ **ÇÖZÜLDÜ:** Config klasörü artık otomatik oluşturuluyor.

#### 2. Admin paneline giriş yapamıyorum
- E-posta ve şifrenizi doğru girdiğinizden emin olun
- Veritabanında `admin_kullanicilar` tablosunu kontrol edin

#### 3. Videolar yüklenmiyor
- `uploads/videos/` klasörünün yazma izinlerini kontrol edin
- PHP upload_max_filesize ayarını kontrol edin

#### 4. Database bağlantı hatası
- `config/config.php` dosyasındaki veritabanı bilgilerini kontrol edin
- MySQL servisinin çalıştığından emin olun

### PHP Ayarları
```ini
# php.ini dosyasında bu ayarları kontrol edin:
upload_max_filesize = 500M
post_max_size = 500M
max_execution_time = 300
memory_limit = 512M
```

---

## 📁 Klasör Yapısı

```
video-platform/
├── 📁 admin/                 # Admin paneli
│   ├── 📁 includes/          # Header, footer, sidebar
│   ├── 📁 assets/            # Admin CSS/JS
│   ├── 📄 index.php          # Dashboard
│   ├── 📄 giris.php          # Admin giriş
│   ├── 📄 videolar.php       # Video yönetimi
│   ├── 📄 kullanicilar.php   # Kullanıcı yönetimi
│   ├── 📄 kategoriler.php    # Kategori yönetimi
│   └── 📄 site-ayarlari.php  # Site ayarları
├── 📁 includes/              # Ortak dosyalar
│   ├── 📄 config.php         # Ana config
│   ├── 📄 header.php         # Site header
│   └── 📄 footer.php         # Site footer
├── 📁 assets/                # CSS, JS, resimler
├── 📁 uploads/               # Yüklenen dosyalar
│   ├── 📁 videos/            # Video dosyaları
│   ├── 📁 thumbnails/        # Video kapak resimleri
│   ├── 📁 categories/        # Kategori resimleri
│   └── 📁 avatars/           # Kullanıcı profil resimleri
├── 📁 api/                   # AJAX API'lar
├── 📄 install.php            # Kurulum sihirbazı
├── 📄 index.php              # Ana sayfa
├── 📄 video.php              # Video oynatıcı
├── 📄 giris.php              # Kullanıcı girişi
├── 📄 kayit.php              # Kullanıcı kaydı
└── 📄 database.sql           # Veritabanı yapısı
```

---

## 🎉 Başarılı Kurulum!

Tüm adımları tamamladıysanız DOBİEN Video Platform artık çalışır durumda!

### 🔗 Yararlı Linkler
- **Ana Sayfa:** `http://yourdomain.com/video-platform/`
- **Admin Panel:** `http://yourdomain.com/video-platform/admin/`
- **GitHub Repository:** https://github.com/Berocan025/Vdeo

### 📞 Destek
Herhangi bir sorun yaşarsanız:
1. Bu README dosyasını tekrar okuyun
2. GitHub'da issue açın
3. Log dosyalarını kontrol edin

---

**🎬 DOBİEN Video Platform - Professional Video Sharing Solution**  
*Geliştirici: DOBİEN* | *Tüm Hakları Saklıdır © 2024*