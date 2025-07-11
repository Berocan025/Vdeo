-- DOBİEN Video Platform - Complete Database Schema
-- Bu dosya tüm gerekli tabloları oluşturur
-- Geliştirici: DOBİEN

SET FOREIGN_KEY_CHECKS = 0;

-- Ayarlar tablosu
CREATE TABLE IF NOT EXISTS `ayarlar` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `site_adi` VARCHAR(255) DEFAULT 'DOBİEN Video Platform',
    `site_url` VARCHAR(255) DEFAULT 'http://localhost',
    `site_aciklama` TEXT DEFAULT 'Modern Video Paylaşım Platformu',
    `meta_anahtar` TEXT,
    `email` VARCHAR(255),
    `telefon` VARCHAR(50),
    `adres` TEXT,
    `footer_metin` TEXT DEFAULT 'DOBİEN tarafından geliştirildi. Tüm hakları saklıdır.',
    `logo` VARCHAR(255),
    `favicon` VARCHAR(255),
    `sosyal_facebook` VARCHAR(255),
    `sosyal_twitter` VARCHAR(255),
    `sosyal_instagram` VARCHAR(255),
    `sosyal_youtube` VARCHAR(255),
    `analytics_kod` TEXT,
    `kayit_durumu` TINYINT DEFAULT 1,
    `email_dogrulama` TINYINT DEFAULT 0,
    `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `guncelleme_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin kullanıcıları tablosu
CREATE TABLE IF NOT EXISTS `admin_kullanicilar` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `kullanici_adi` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `sifre` VARCHAR(255) NOT NULL,
    `ad` VARCHAR(50),
    `soyad` VARCHAR(50),
    `telefon` VARCHAR(20),
    `avatar` VARCHAR(255),
    `rol` ENUM('super_admin', 'admin', 'editor', 'moderator') DEFAULT 'admin',
    `durum` TINYINT DEFAULT 1,
    `son_giris` TIMESTAMP NULL,
    `son_ip` VARCHAR(45),
    `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `guncelleme_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_kullanici_adi` (`kullanici_adi`),
    INDEX `idx_email` (`email`),
    INDEX `idx_rol` (`rol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Eski adminler tablosu (backward compatibility)
CREATE TABLE IF NOT EXISTS `adminler` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `kullanici_adi` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `sifre` VARCHAR(255) NOT NULL,
    `ad` VARCHAR(50),
    `soyad` VARCHAR(50),
    `telefon` VARCHAR(20),
    `avatar` VARCHAR(255),
    `rol` ENUM('super_admin', 'admin', 'editor', 'moderator') DEFAULT 'admin',
    `durum` TINYINT DEFAULT 1,
    `son_giris` TIMESTAMP NULL,
    `son_ip` VARCHAR(45),
    `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `guncelleme_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kullanıcılar tablosu
CREATE TABLE IF NOT EXISTS `kullanicilar` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `kullanici_adi` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `sifre` VARCHAR(255) NOT NULL,
    `ad` VARCHAR(50),
    `soyad` VARCHAR(50),
    `telefon` VARCHAR(20),
    `avatar` VARCHAR(255),
    `cinsiyet` ENUM('erkek', 'kadin', 'belirtmek_istemiyorum') DEFAULT 'belirtmek_istemiyorum',
    `dogum_tarihi` DATE,
    `ulke` VARCHAR(50),
    `sehir` VARCHAR(50),
    `hakkinda` TEXT,
    `durum` TINYINT DEFAULT 1,
    `email_dogrulandi` TINYINT DEFAULT 0,
    `email_dogrulama_kodu` VARCHAR(100),
    `sifre_sifirlama_kodu` VARCHAR(100),
    `premium_bitis` TIMESTAMP NULL,
    `vip_bitis` TIMESTAMP NULL,
    `son_giris` TIMESTAMP NULL,
    `son_ip` VARCHAR(45),
    `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `guncelleme_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_kullanici_adi` (`kullanici_adi`),
    INDEX `idx_email` (`email`),
    INDEX `idx_durum` (`durum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kategoriler tablosu
CREATE TABLE IF NOT EXISTS `kategoriler` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ad` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `aciklama` TEXT,
    `resim` VARCHAR(255),
    `renk` VARCHAR(7) DEFAULT '#6366f1',
    `sira` INT DEFAULT 0,
    `durum` TINYINT DEFAULT 1,
    `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `guncelleme_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_durum` (`durum`),
    INDEX `idx_sira` (`sira`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Etiketler tablosu
CREATE TABLE IF NOT EXISTS `etiketler` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ad` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `renk` VARCHAR(7) DEFAULT '#6366f1',
    `kullanim_sayisi` INT DEFAULT 0,
    `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_kullanim_sayisi` (`kullanim_sayisi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Videolar tablosu
CREATE TABLE IF NOT EXISTS `videolar` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `baslik` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `aciklama` TEXT,
    `video_url` VARCHAR(500) NOT NULL,
    `kapak_resmi` VARCHAR(255),
    `sure` TIME,
    `kategori_id` INT,
    `yukleyen_id` INT,
    `goruntulenme` INT DEFAULT 0,
    `begeni` INT DEFAULT 0,
    `begenmeme` INT DEFAULT 0,
    `durum` ENUM('aktif', 'beklemede', 'reddedildi', 'silindi') DEFAULT 'aktif',
    `premium` TINYINT DEFAULT 0,
    `vip` TINYINT DEFAULT 0,
    `yas_siniri` TINYINT DEFAULT 0,
    `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `guncelleme_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_kategori` (`kategori_id`),
    INDEX `idx_yukleyen` (`yukleyen_id`),
    INDEX `idx_durum` (`durum`),
    INDEX `idx_premium` (`premium`),
    INDEX `idx_vip` (`vip`),
    INDEX `idx_goruntulenme` (`goruntulenme`),
    FOREIGN KEY (`kategori_id`) REFERENCES `kategoriler`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`yukleyen_id`) REFERENCES `kullanicilar`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Video etiketleri tablosu
CREATE TABLE IF NOT EXISTS `video_etiketler` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `video_id` INT NOT NULL,
    `etiket_id` INT NOT NULL,
    `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `video_etiket` (`video_id`, `etiket_id`),
    FOREIGN KEY (`video_id`) REFERENCES `videolar`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`etiket_id`) REFERENCES `etiketler`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Yorumlar tablosu
CREATE TABLE IF NOT EXISTS `yorumlar` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `video_id` INT NOT NULL,
    `kullanici_id` INT NOT NULL,
    `yorum` TEXT NOT NULL,
    `ust_yorum_id` INT NULL,
    `begeni` INT DEFAULT 0,
    `begenmeme` INT DEFAULT 0,
    `durum` ENUM('aktif', 'beklemede', 'reddedildi', 'silindi') DEFAULT 'aktif',
    `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `guncelleme_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_video` (`video_id`),
    INDEX `idx_kullanici` (`kullanici_id`),
    INDEX `idx_ust_yorum` (`ust_yorum_id`),
    INDEX `idx_durum` (`durum`),
    FOREIGN KEY (`video_id`) REFERENCES `videolar`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`ust_yorum_id`) REFERENCES `yorumlar`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Beğeniler tablosu
CREATE TABLE IF NOT EXISTS `begeniler` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `video_id` INT NOT NULL,
    `kullanici_id` INT NOT NULL,
    `tur` ENUM('begeni', 'begenmeme') NOT NULL,
    `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `video_kullanici` (`video_id`, `kullanici_id`),
    FOREIGN KEY (`video_id`) REFERENCES `videolar`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Favoriler tablosu
CREATE TABLE IF NOT EXISTS `favoriler` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `video_id` INT NOT NULL,
    `kullanici_id` INT NOT NULL,
    `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `video_kullanici` (`video_id`, `kullanici_id`),
    FOREIGN KEY (`video_id`) REFERENCES `videolar`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- İzleme geçmişi tablosu
CREATE TABLE IF NOT EXISTS `izleme_gecmisi` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `video_id` INT NOT NULL,
    `kullanici_id` INT NOT NULL,
    `izleme_suresi` INT DEFAULT 0,
    `tamamlandi` TINYINT DEFAULT 0,
    `son_pozisyon` INT DEFAULT 0,
    `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `guncelleme_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `video_kullanici` (`video_id`, `kullanici_id`),
    INDEX `idx_kullanici` (`kullanici_id`),
    FOREIGN KEY (`video_id`) REFERENCES `videolar`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- İzleme listesi tablosu
CREATE TABLE IF NOT EXISTS `izleme_listesi` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `video_id` INT NOT NULL,
    `kullanici_id` INT NOT NULL,
    `sira` INT DEFAULT 0,
    `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `video_kullanici` (`video_id`, `kullanici_id`),
    INDEX `idx_kullanici` (`kullanici_id`),
    INDEX `idx_sira` (`sira`),
    FOREIGN KEY (`video_id`) REFERENCES `videolar`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Slider tablosu
CREATE TABLE IF NOT EXISTS `slider` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `baslik` VARCHAR(255) NOT NULL,
    `aciklama` TEXT,
    `resim` VARCHAR(255) NOT NULL,
    `link` VARCHAR(500),
    `buton_metni` VARCHAR(50),
    `sira` INT DEFAULT 0,
    `durum` TINYINT DEFAULT 1,
    `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `guncelleme_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_sira` (`sira`),
    INDEX `idx_durum` (`durum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sayfalar tablosu
CREATE TABLE IF NOT EXISTS `sayfalar` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `baslik` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `icerik` LONGTEXT NOT NULL,
    `meta_aciklama` TEXT,
    `meta_anahtar` TEXT,
    `durum` TINYINT DEFAULT 1,
    `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `guncelleme_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_durum` (`durum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Yaş uyarısı ayarları tablosu
CREATE TABLE IF NOT EXISTS `yas_uyarisi_ayarlari` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `aktif` TINYINT DEFAULT 1,
    `baslik` VARCHAR(255) DEFAULT 'DOBİEN',
    `alt_baslik` VARCHAR(255) DEFAULT 'Video Platform',
    `uyari_baslik` VARCHAR(255) DEFAULT 'Yaş Doğrulama Gerekli',
    `uyari_metni` TEXT DEFAULT 'Bu siteye erişebilmeniz için 18 yaşından büyük olmanız gerekmektedir.',
    `onay_butonu` VARCHAR(100) DEFAULT '18 yaşından büyüğüm',
    `red_butonu` VARCHAR(100) DEFAULT '18 yaşında değilim',
    `red_mesaji` TEXT DEFAULT 'Üzgünüz, sitemiz sizin için uygun değildir.',
    `gelistirici_notu` TEXT DEFAULT 'Bu sistem DOBİEN tarafından geliştirilmiştir.',
    `guncelleme_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Üyelik paketleri tablosu
CREATE TABLE IF NOT EXISTS `uyelik_paketleri` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ad` VARCHAR(100) NOT NULL,
    `aciklama` TEXT,
    `fiyat` DECIMAL(10,2) NOT NULL,
    `sure_gun` INT NOT NULL,
    `ozellikler` JSON,
    `durum` TINYINT DEFAULT 1,
    `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `guncelleme_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_durum` (`durum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ödemeler tablosu
CREATE TABLE IF NOT EXISTS `odemeler` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `kullanici_id` INT NOT NULL,
    `paket_id` INT NOT NULL,
    `tutar` DECIMAL(10,2) NOT NULL,
    `odeme_yontemi` VARCHAR(50) NOT NULL,
    `odeme_durumu` ENUM('beklemede', 'tamamlandi', 'iptal', 'iade') DEFAULT 'beklemede',
    `islem_id` VARCHAR(100),
    `odeme_tarihi` TIMESTAMP NULL,
    `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `guncelleme_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_kullanici` (`kullanici_id`),
    INDEX `idx_paket` (`paket_id`),
    INDEX `idx_durum` (`odeme_durumu`),
    FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`paket_id`) REFERENCES `uyelik_paketleri`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Mesajlar tablosu
CREATE TABLE IF NOT EXISTS `mesajlar` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `gonderen_id` INT NOT NULL,
    `alici_id` INT NOT NULL,
    `konu` VARCHAR(255) NOT NULL,
    `mesaj` TEXT NOT NULL,
    `okundu` TINYINT DEFAULT 0,
    `okunma_tarihi` TIMESTAMP NULL,
    `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_gonderen` (`gonderen_id`),
    INDEX `idx_alici` (`alici_id`),
    INDEX `idx_okundu` (`okundu`),
    FOREIGN KEY (`gonderen_id`) REFERENCES `kullanicilar`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`alici_id`) REFERENCES `kullanicilar`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sistem logları tablosu
CREATE TABLE IF NOT EXISTS `sistem_loglari` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `kullanici_id` INT NULL,
    `islem` VARCHAR(255) NOT NULL,
    `aciklama` TEXT,
    `ip_adresi` VARCHAR(45),
    `user_agent` TEXT,
    `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_kullanici` (`kullanici_id`),
    INDEX `idx_tarih` (`olusturma_tarihi`),
    FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- İstatistikler tablosu
CREATE TABLE IF NOT EXISTS `istatistikler` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tarih` DATE NOT NULL,
    `toplam_goruntulenme` INT DEFAULT 0,
    `yeni_uyeler` INT DEFAULT 0,
    `yeni_videolar` INT DEFAULT 0,
    `toplam_sure` INT DEFAULT 0,
    `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `guncelleme_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `tarih` (`tarih`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Varsayılan veriler ekleme
INSERT IGNORE INTO `ayarlar` (`id`, `site_adi`, `site_url`, `site_aciklama`, `footer_metin`) VALUES
(1, 'DOBİEN Video Platform', 'http://localhost', 'Modern Video Paylaşım Platformu', 'DOBİEN tarafından geliştirildi. Tüm hakları saklıdır.');

INSERT IGNORE INTO `yas_uyarisi_ayarlari` (`id`, `aktif`, `baslik`, `alt_baslik`, `uyari_baslik`, `uyari_metni`, `onay_butonu`, `red_butonu`, `red_mesaji`, `gelistirici_notu`) VALUES
(1, 1, 'DOBİEN', 'Video Platform', 'Yaş Doğrulama Gerekli', 'Bu siteye erişebilmeniz için 18 yaşından büyük olmanız gerekmektedir. Sitemiz yetişkin içerikler barındırmaktadır ve yalnızca reşit kullanıcılar için uygundur.', '18 yaşından büyüğüm', '18 yaşında değilim', 'Üzgünüz, sitemiz sizin için uygun değildir. 18 yaş altındaki kullanıcılar siteye erişemez.', 'Bu sistem DOBİEN tarafından geliştirilmiştir ve kullanıcı güvenliği için tasarlanmıştır.');

-- Varsayılan admin kullanıcı (şifre: admin123)
INSERT IGNORE INTO `admin_kullanicilar` (`id`, `kullanici_adi`, `email`, `sifre`, `ad`, `soyad`, `rol`) VALUES
(1, 'admin', 'admin@dobien.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'DOBİEN', 'Admin', 'super_admin');

-- Backward compatibility için adminler tablosuna da ekle
INSERT IGNORE INTO `adminler` (`id`, `kullanici_adi`, `email`, `sifre`, `ad`, `soyad`, `rol`) VALUES
(1, 'admin', 'admin@dobien.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'DOBİEN', 'Admin', 'super_admin');

-- Varsayılan kategoriler
INSERT IGNORE INTO `kategoriler` (`id`, `ad`, `slug`, `aciklama`, `renk`, `sira`) VALUES
(1, 'Genel', 'genel', 'Genel videolar', '#6366f1', 1),
(2, 'Eğlence', 'eglence', 'Eğlence videoları', '#10b981', 2),
(3, 'Spor', 'spor', 'Spor videoları', '#f59e0b', 3),
(4, 'Müzik', 'muzik', 'Müzik videoları', '#ef4444', 4),
(5, 'Teknoloji', 'teknoloji', 'Teknoloji videoları', '#8b5cf6', 5);

-- Varsayılan slider
INSERT IGNORE INTO `slider` (`id`, `baslik`, `aciklama`, `resim`, `link`, `buton_metni`, `sira`) VALUES
(1, 'DOBİEN Video Platform\'a Hoş Geldiniz', 'Modern ve güçlü video paylaşım platformu', 'slider1.jpg', '#', 'Keşfet', 1),
(2, 'Premium İçerikler', 'Premium üyelik ile özel içeriklere erişin', 'slider2.jpg', 'premium.php', 'Premium Ol', 2),
(3, 'VIP Deneyimi', 'VIP üyelik ile en iyi deneyimi yaşayın', 'slider3.jpg', 'vip.php', 'VIP Ol', 3);

-- Varsayılan sayfalar
INSERT IGNORE INTO `sayfalar` (`id`, `baslik`, `slug`, `icerik`) VALUES
(1, 'Hakkımızda', 'hakkimizda', '<h1>Hakkımızda</h1><p>DOBİEN Video Platform, modern ve güvenli video paylaşım deneyimi sunan bir platformdur.</p>'),
(2, 'Gizlilik Politikası', 'gizlilik-politikasi', '<h1>Gizlilik Politikası</h1><p>Kişisel verilerinizin korunması bizim için önemlidir.</p>'),
(3, 'Kullanım Koşulları', 'kullanim-kosullari', '<h1>Kullanım Koşulları</h1><p>Platformumuzu kullanırken uymanız gereken kurallar.</p>'),
(4, 'İletişim', 'iletisim', '<h1>İletişim</h1><p>Bizimle iletişime geçmek için aşağıdaki bilgileri kullanabilirsiniz.</p>');

-- Varsayılan üyelik paketleri
INSERT IGNORE INTO `uyelik_paketleri` (`id`, `ad`, `aciklama`, `fiyat`, `sure_gun`, `ozellikler`) VALUES
(1, 'Premium', 'Premium üyelik paketi', 29.99, 30, '["Reklamsız izleme", "HD kalite", "Öncelikli destek"]'),
(2, 'VIP', 'VIP üyelik paketi', 49.99, 30, '["Tüm premium özellikler", "4K kalite", "Özel içerikler", "Öncelikli yükleme"]');

SET FOREIGN_KEY_CHECKS = 1;

-- Veritabanı optimizasyonu
OPTIMIZE TABLE `ayarlar`;
OPTIMIZE TABLE `admin_kullanicilar`;
OPTIMIZE TABLE `adminler`;
OPTIMIZE TABLE `kullanicilar`;
OPTIMIZE TABLE `kategoriler`;
OPTIMIZE TABLE `etiketler`;
OPTIMIZE TABLE `videolar`;
OPTIMIZE TABLE `video_etiketler`;
OPTIMIZE TABLE `yorumlar`;
OPTIMIZE TABLE `begeniler`;
OPTIMIZE TABLE `favoriler`;
OPTIMIZE TABLE `izleme_gecmisi`;
OPTIMIZE TABLE `izleme_listesi`;
OPTIMIZE TABLE `slider`;
OPTIMIZE TABLE `sayfalar`;
OPTIMIZE TABLE `yas_uyarisi_ayarlari`;
OPTIMIZE TABLE `uyelik_paketleri`;
OPTIMIZE TABLE `odemeler`;
OPTIMIZE TABLE `mesajlar`;
OPTIMIZE TABLE `sistem_loglari`;
OPTIMIZE TABLE `istatistikler`;

-- Başarılı mesajı
SELECT 'DOBİEN Video Platform veritabanı başarıyla oluşturuldu!' as Mesaj;