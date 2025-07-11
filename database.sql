-- DOBİEN Video Platform Database
-- Geliştirici: DOBİEN
-- Tam entegre database yapısı - tüm sorunlar çözüldü
-- Tüm Hakları Saklıdır © DOBİEN

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Database: `dobien_video_platform`

-- --------------------------------------------------------
-- Admin Kullanıcıları Tablosu (Tüm alanlar mevcut)
-- --------------------------------------------------------

CREATE TABLE `admin_kullanicilar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad` varchar(50) NOT NULL,
  `soyad` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `kullanici_adi` varchar(100) NOT NULL,
  `sifre` varchar(255) NOT NULL,
  `rol` enum('admin','super_admin') NOT NULL DEFAULT 'admin',
  `yetki_seviyesi` enum('admin','super_admin') NOT NULL DEFAULT 'admin',
  `avatar` varchar(255) DEFAULT NULL,
  `profil_resmi` varchar(255) DEFAULT NULL,
  `durum` enum('aktif','pasif') NOT NULL DEFAULT 'aktif',
  `son_giris_tarihi` timestamp NULL DEFAULT NULL,
  `son_giris_ip` varchar(45) DEFAULT NULL,
  `olusturma_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `guncelleme_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `kullanici_adi` (`kullanici_adi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Varsayılan admin kullanıcı
INSERT INTO `admin_kullanicilar` (`ad`, `soyad`, `email`, `kullanici_adi`, `sifre`, `rol`, `yetki_seviyesi`, `durum`) VALUES
('DOBİEN', 'Admin', 'admin@dobien.com', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', 'super_admin', 'aktif');

-- --------------------------------------------------------
-- Site Ayarları Tablosu
-- --------------------------------------------------------

CREATE TABLE `site_ayarlari` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `anahtar` varchar(100) NOT NULL,
  `deger` text,
  `aciklama` text,
  `kategori` varchar(50) DEFAULT 'genel',
  `tip` enum('text','textarea','select','number','boolean','file') DEFAULT 'text',
  `guncelleme_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `anahtar` (`anahtar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Site ayarları
INSERT INTO `site_ayarlari` (`anahtar`, `deger`, `aciklama`, `kategori`, `tip`) VALUES
('site_baslik', 'DOBİEN Video Platform', 'Site başlığı', 'genel', 'text'),
('site_aciklama', 'Modern Video Paylaşım Platformu - Premium kalitede video deneyimi', 'Site açıklaması', 'genel', 'textarea'),
('site_anahtar_kelimeler', 'video platform, premium videolar, 4k video, vip üyelik, DOBİEN', 'SEO anahtar kelimeleri', 'seo', 'textarea'),
('site_logo', '', 'Site logosu', 'genel', 'file'),
('site_favicon', '', 'Site favicon', 'genel', 'file'),
('footer_metin', 'DOBİEN tarafından geliştirildi. Tüm hakları saklıdır.', 'Footer metni', 'genel', 'text'),
('yas_dogrulama_aktif', '1', 'Yaş doğrulama popup aktif/pasif', 'güvenlik', 'boolean'),
('yas_dogrulama_baslik', 'Yaş Doğrulama Gerekli', 'Yaş doğrulama popup başlığı', 'güvenlik', 'text'),
('yas_dogrulama_mesaj', 'Bu siteye erişebilmeniz için 18 yaşından büyük olmanız gerekmektedir.', 'Yaş doğrulama mesajı', 'güvenlik', 'textarea'),
('google_analytics', '', 'Google Analytics kodu', 'analitik', 'textarea'),
('meta_verification', '', 'Site doğrulama kodları', 'seo', 'textarea'),
('sistem_email', 'admin@dobien.com', 'Sistem e-posta adresi', 'sistem', 'text'),
('smtp_host', 'localhost', 'SMTP sunucu adresi', 'email', 'text'),
('smtp_port', '587', 'SMTP port', 'email', 'number'),
('smtp_username', '', 'SMTP kullanıcı adı', 'email', 'text'),
('smtp_password', '', 'SMTP şifresi', 'email', 'text'),
('max_video_boyut', '500', 'Maksimum video boyutu (MB)', 'upload', 'number'),
('izin_verilen_formatlar', 'mp4,avi,mov,wmv,flv', 'İzin verilen video formatları', 'upload', 'text'),
('varsayilan_video_kalite', '720p', 'Varsayılan video kalitesi', 'video', 'select'),
('cache_suresi', '3600', 'Cache süresi (saniye)', 'performans', 'number'),
('maintenance_mode', '0', 'Bakım modu', 'sistem', 'boolean'),
('user_registration', '1', 'Kullanıcı kayıt izni', 'kullanici', 'boolean'),
('comment_moderation', '1', 'Yorum moderasyonu', 'içerik', 'boolean');

-- --------------------------------------------------------
-- Kullanıcılar Tablosu
-- --------------------------------------------------------

CREATE TABLE `kullanicilar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad` varchar(50) NOT NULL,
  `soyad` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `sifre` varchar(255) NOT NULL,
  `telefon` varchar(20) DEFAULT NULL,
  `dogum_tarihi` date DEFAULT NULL,
  `cinsiyet` enum('erkek','kadın','belirtmek_istemiyorum') DEFAULT NULL,
  `profil_resmi` varchar(255) DEFAULT NULL,
  `kapak_resmi` varchar(255) DEFAULT NULL,
  `bio` text,
  `uyelik_tipi` enum('kullanici','vip','premium') NOT NULL DEFAULT 'kullanici',
  `uyelik_baslangic` timestamp NULL DEFAULT NULL,
  `vip_bitis` timestamp NULL DEFAULT NULL,
  `premium_bitis` timestamp NULL DEFAULT NULL,
  `durum` enum('aktif','pasif','beklemede','yasakli') NOT NULL DEFAULT 'beklemede',
  `aktivasyon_kodu` varchar(100) DEFAULT NULL,
  `sifre_sifirlama_token` varchar(100) DEFAULT NULL,
  `sifre_sifirlama_tarih` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `newsletter_izni` tinyint(1) NOT NULL DEFAULT '0',
  `bildirim_izni` tinyint(1) NOT NULL DEFAULT '1',
  `son_giris_tarihi` timestamp NULL DEFAULT NULL,
  `son_giris_ip` varchar(45) DEFAULT NULL,
  `toplam_izleme_suresi` bigint(20) DEFAULT '0',
  `toplam_begeni` int(11) DEFAULT '0',
  `toplam_yorum` int(11) DEFAULT '0',
  `kayit_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `guncelleme_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `uyelik_tipi` (`uyelik_tipi`),
  KEY `durum` (`durum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Kategoriler Tablosu
-- --------------------------------------------------------

CREATE TABLE `kategoriler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kategori_adi` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `aciklama` text,
  `resim` varchar(255) DEFAULT NULL,
  `banner_resmi` varchar(255) DEFAULT NULL,
  `renk` varchar(7) DEFAULT '#6c5ce7',
  `icon` varchar(50) DEFAULT 'fas fa-play',
  `siralama` int(11) NOT NULL DEFAULT '0',
  `durum` enum('aktif','pasif') NOT NULL DEFAULT 'aktif',
  `video_sayisi` int(11) DEFAULT '0',
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` text DEFAULT NULL,
  `seo_keywords` text DEFAULT NULL,
  `olusturma_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `guncelleme_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `durum` (`durum`),
  KEY `siralama` (`siralama`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Varsayılan kategoriler
INSERT INTO `kategoriler` (`kategori_adi`, `slug`, `aciklama`, `siralama`, `durum`, `renk`, `icon`) VALUES
('Genel', 'genel', 'Genel video içerikleri', 1, 'aktif', '#6c5ce7', 'fas fa-play'),
('Eğitim', 'egitim', 'Eğitici ve öğretici videolar', 2, 'aktif', '#00b894', 'fas fa-graduation-cap'),
('Eğlence', 'eglence', 'Eğlenceli video içerikleri', 3, 'aktif', '#e17055', 'fas fa-laugh'),
('Spor', 'spor', 'Spor videoları ve maçlar', 4, 'aktif', '#0984e3', 'fas fa-running'),
('Müzik', 'muzik', 'Müzik videoları ve konsérler', 5, 'aktif', '#a29bfe', 'fas fa-music'),
('Teknoloji', 'teknoloji', 'Teknoloji ve inovasyon', 6, 'aktif', '#636e72', 'fas fa-laptop');

-- --------------------------------------------------------
-- Videolar Tablosu
-- --------------------------------------------------------

CREATE TABLE `videolar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `baslik` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `aciklama` text,
  `kategori_id` int(11) DEFAULT NULL,
  `uploader_id` int(11) DEFAULT NULL,
  `kapak_resmi` varchar(255) DEFAULT NULL,
  `video_dosyasi` varchar(255) NOT NULL,
  `video_url` varchar(500) DEFAULT NULL,
  `video_dosyasi_720p` varchar(255) DEFAULT NULL,
  `video_dosyasi_1080p` varchar(255) DEFAULT NULL,
  `video_dosyasi_4k` varchar(255) DEFAULT NULL,
  `sure` int(11) DEFAULT NULL COMMENT 'Saniye cinsinden',
  `dosya_boyutu` bigint(20) DEFAULT NULL,
  `goruntulenme_yetkisi` enum('herkes','vip','premium') NOT NULL DEFAULT 'herkes',
  `ozellik` enum('normal','populer','editor_secimi','yeni','ozel') DEFAULT 'normal',
  `etiketler` text,
  `thumbnail_grid` text COMMENT 'Thumbnail ızgara pozisyonları',
  `izlenme_sayisi` bigint(20) NOT NULL DEFAULT '0',
  `begeni_sayisi` bigint(20) NOT NULL DEFAULT '0',
  `begenme_sayisi` bigint(20) NOT NULL DEFAULT '0',
  `favori_sayisi` bigint(20) NOT NULL DEFAULT '0',
  `yorum_sayisi` int(11) NOT NULL DEFAULT '0',
  `paylaşım_sayisi` int(11) NOT NULL DEFAULT '0',
  `sikayet_sayisi` int(11) NOT NULL DEFAULT '0',
  `kalite` enum('480p','720p','1080p','4k') DEFAULT '720p',
  `dil` varchar(10) DEFAULT 'tr',
  `altyazi_dosyasi` varchar(255) DEFAULT NULL,
  `durum` enum('aktif','pasif','beklemede','silinmis','gizli') NOT NULL DEFAULT 'beklemede',
  `moderasyon_notu` text,
  `featured_until` timestamp NULL DEFAULT NULL,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` text DEFAULT NULL,
  `seo_keywords` text DEFAULT NULL,
  `ekleme_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `guncelleme_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `yayin_tarihi` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `kategori_id` (`kategori_id`),
  KEY `uploader_id` (`uploader_id`),
  KEY `durum` (`durum`),
  KEY `goruntulenme_yetkisi` (`goruntulenme_yetkisi`),
  KEY `ozellik` (`ozellik`),
  KEY `izlenme_sayisi` (`izlenme_sayisi`),
  KEY `ekleme_tarihi` (`ekleme_tarihi`),
  CONSTRAINT `videolar_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategoriler` (`id`) ON DELETE SET NULL,
  CONSTRAINT `videolar_ibfk_2` FOREIGN KEY (`uploader_id`) REFERENCES `kullanicilar` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Slider Tablosu
-- --------------------------------------------------------

CREATE TABLE `slider` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `baslik` varchar(255) NOT NULL,
  `aciklama` text,
  `resim` varchar(255) NOT NULL,
  `mobil_resim` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `buton_metni` varchar(100) DEFAULT 'İzle',
  `video_id` int(11) DEFAULT NULL,
  `siralama` int(11) DEFAULT '0',
  `durum` enum('aktif','pasif') DEFAULT 'aktif',
  `baslangic_tarihi` timestamp NULL DEFAULT NULL,
  `bitis_tarihi` timestamp NULL DEFAULT NULL,
  `click_sayisi` int(11) DEFAULT '0',
  `olusturma_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `guncelleme_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `video_id` (`video_id`),
  KEY `durum` (`durum`),
  KEY `siralama` (`siralama`),
  CONSTRAINT `slider_ibfk_1` FOREIGN KEY (`video_id`) REFERENCES `videolar` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Ödeme Geçmişi Tablosu
-- --------------------------------------------------------

CREATE TABLE `odeme_gecmisi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kullanici_id` int(11) NOT NULL,
  `plan` enum('vip','premium') NOT NULL,
  `donem` enum('monthly','yearly') NOT NULL DEFAULT 'monthly',
  `tutar` decimal(10,2) NOT NULL,
  `para_birimi` varchar(3) DEFAULT 'TRY',
  `odeme_yontemi` enum('kredi_karti','banka_havalesi','paypal','bitcoin') DEFAULT 'kredi_karti',
  `transaction_id` varchar(100) DEFAULT NULL,
  `gateway_response` text,
  `durum` enum('beklemede','tamamlandi','iptal','geri_iade') NOT NULL DEFAULT 'beklemede',
  `baslangic_tarihi` timestamp NULL DEFAULT NULL,
  `bitis_tarihi` timestamp NULL DEFAULT NULL,
  `odeme_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `guncelleme_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `kullanici_id` (`kullanici_id`),
  KEY `durum` (`durum`),
  KEY `odeme_tarihi` (`odeme_tarihi`),
  CONSTRAINT `odeme_gecmisi_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Kullanıcı Video Etkileşimleri
-- --------------------------------------------------------

CREATE TABLE `kullanici_video_etkilesimleri` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kullanici_id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `etkilesim_tipi` enum('izleme','begeni','begenme','favori','paylaşım') NOT NULL,
  `etkilesim_suresi` int(11) DEFAULT NULL COMMENT 'İzleme süresi saniye',
  `cihaz_tipi` enum('desktop','mobile','tablet','tv') DEFAULT 'desktop',
  `ip_adresi` varchar(45) DEFAULT NULL,
  `olusturma_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_interaction` (`kullanici_id`,`video_id`,`etkilesim_tipi`),
  KEY `video_id` (`video_id`),
  KEY `etkilesim_tipi` (`etkilesim_tipi`),
  CONSTRAINT `kullanici_video_etkilesimleri_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kullanici_video_etkilesimleri_ibfk_2` FOREIGN KEY (`video_id`) REFERENCES `videolar` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Video Yorumları
-- --------------------------------------------------------

CREATE TABLE `video_yorumlari` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `video_id` int(11) NOT NULL,
  `kullanici_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL COMMENT 'Yanıt için üst yorum ID',
  `yorum_metni` text NOT NULL,
  `begeni_sayisi` int(11) DEFAULT '0',
  `begenme_sayisi` int(11) DEFAULT '0',
  `durum` enum('aktif','pasif','beklemede','silinmis') DEFAULT 'aktif',
  `moderasyon_notu` text,
  `ip_adresi` varchar(45) DEFAULT NULL,
  `olusturma_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `guncelleme_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `video_id` (`video_id`),
  KEY `kullanici_id` (`kullanici_id`),
  KEY `parent_id` (`parent_id`),
  KEY `durum` (`durum`),
  CONSTRAINT `video_yorumlari_ibfk_1` FOREIGN KEY (`video_id`) REFERENCES `videolar` (`id`) ON DELETE CASCADE,
  CONSTRAINT `video_yorumlari_ibfk_2` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE,
  CONSTRAINT `video_yorumlari_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `video_yorumlari` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- İstatistikler Tablosu
-- --------------------------------------------------------

CREATE TABLE `istatistikler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tarih` date NOT NULL,
  `toplam_izlenme` bigint(20) DEFAULT '0',
  `toplam_kullanici` int(11) DEFAULT '0',
  `yeni_kayitlar` int(11) DEFAULT '0',
  `premium_satislari` decimal(10,2) DEFAULT '0.00',
  `vip_satislari` decimal(10,2) DEFAULT '0.00',
  `aktif_kullanicilar` int(11) DEFAULT '0',
  `video_yuklemeleri` int(11) DEFAULT '0',
  `toplam_yorum` int(11) DEFAULT '0',
  `olusturma_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tarih` (`tarih`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Sistem Logları
-- --------------------------------------------------------

CREATE TABLE `sistem_loglari` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kullanici_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `aksiyon` varchar(100) NOT NULL,
  `tablo_adi` varchar(50) DEFAULT NULL,
  `kayit_id` int(11) DEFAULT NULL,
  `eski_deger` text,
  `yeni_deger` text,
  `ip_adresi` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `olusturma_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `kullanici_id` (`kullanici_id`),
  KEY `admin_id` (`admin_id`),
  KEY `aksiyon` (`aksiyon`),
  KEY `olusturma_tarihi` (`olusturma_tarihi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Bildirimler Tablosu  
-- --------------------------------------------------------

CREATE TABLE `bildirimler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kullanici_id` int(11) NOT NULL,
  `baslik` varchar(255) NOT NULL,
  `mesaj` text NOT NULL,
  `tip` enum('info','success','warning','error') DEFAULT 'info',
  `url` varchar(500) DEFAULT NULL,
  `okundu` tinyint(1) DEFAULT '0',
  `olusturma_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `kullanici_id` (`kullanici_id`),
  KEY `okundu` (`okundu`),
  CONSTRAINT `bildirimler_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- AUTO_INCREMENT değerleri
-- --------------------------------------------------------

ALTER TABLE `admin_kullanicilar` AUTO_INCREMENT=2;
ALTER TABLE `site_ayarlari` AUTO_INCREMENT=21;
ALTER TABLE `kullanicilar` AUTO_INCREMENT=1;
ALTER TABLE `kategoriler` AUTO_INCREMENT=7;
ALTER TABLE `videolar` AUTO_INCREMENT=1;
ALTER TABLE `slider` AUTO_INCREMENT=1;
ALTER TABLE `odeme_gecmisi` AUTO_INCREMENT=1;
ALTER TABLE `kullanici_video_etkilesimleri` AUTO_INCREMENT=1;
ALTER TABLE `video_yorumlari` AUTO_INCREMENT=1;
ALTER TABLE `istatistikler` AUTO_INCREMENT=1;
ALTER TABLE `sistem_loglari` AUTO_INCREMENT=1;
ALTER TABLE `bildirimler` AUTO_INCREMENT=1;

COMMIT;