-- --------------------------------------------------------
-- DOBİEN Video Platform Database Structure
-- Geliştirici: DOBİEN
-- Modern Video Paylaşım Platformu Veritabanı
-- Tüm Hakları Saklıdır © DOBİEN
-- --------------------------------------------------------

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Site Ayarları Tablosu
-- --------------------------------------------------------

CREATE TABLE `site_ayarlari` (
  `id` int(11) NOT NULL,
  `anahtar` varchar(100) NOT NULL,
  `deger` text,
  `aciklama` text,
  `guncelleme_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `site_ayarlari` (`anahtar`, `deger`, `aciklama`) VALUES
('site_baslik', 'DOBİEN Video Platform', 'Site başlığı'),
('site_aciklama', 'Premium video içeriklerini keşfedin. 4K kalite, VIP üyelik avantajları ve sınırsız izleme deneyimi.', 'Site açıklaması'),
('site_anahtar_kelimeler', 'video platform, premium videolar, 4k video, vip üyelik, DOBİEN', 'SEO anahtar kelimeleri'),
('site_logo', 'logo.png', 'Site logosu'),
('site_favicon', 'favicon.ico', 'Site favicon'),
('yas_dogrulama_aktif', '1', 'Yaş doğrulama popup aktif/pasif'),
('yas_dogrulama_baslik', 'Yaş Doğrulama Gerekli', 'Yaş doğrulama popup başlığı'),
('yas_dogrulama_mesaj', 'Bu siteye erişebilmeniz için 18 yaşından büyük olmanız gerekmektedir.', 'Yaş doğrulama mesajı'),
('footer_metin', '© 2024 DOBİEN Video Platform. Tüm hakları saklıdır.', 'Footer metni'),
('google_analytics', '', 'Google Analytics kodu'),
('facebook_pixel', '', 'Facebook Pixel kodu'),
('smtp_host', '', 'SMTP sunucu adresi'),
('smtp_port', '587', 'SMTP port'),
('smtp_kullanici', '', 'SMTP kullanıcı adı'),
('smtp_sifre', '', 'SMTP şifresi'),
('sistem_email', 'noreply@dobien.com', 'Sistem e-posta adresi'),
('max_video_boyut', '500', 'Maksimum video boyutu (MB)'),
('izin_verilen_formatlar', 'mp4,avi,mov,wmv', 'İzin verilen video formatları'),
('varsayilan_video_kalite', '720p', 'Varsayılan video kalitesi');

-- --------------------------------------------------------
-- Kullanıcılar Tablosu
-- --------------------------------------------------------

CREATE TABLE `kullanicilar` (
  `id` int(11) NOT NULL,
  `ad` varchar(50) NOT NULL,
  `soyad` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `sifre` varchar(255) NOT NULL,
  `telefon` varchar(20) DEFAULT NULL,
  `dogum_tarihi` date DEFAULT NULL,
  `cinsiyet` enum('erkek','kadın','belirtmek_istemiyorum') DEFAULT NULL,
  `profil_resmi` varchar(255) DEFAULT NULL,
  `uyelik_tipi` enum('kullanici','vip','premium') NOT NULL DEFAULT 'kullanici',
  `uyelik_baslangic` timestamp NULL DEFAULT NULL,
  `uyelik_bitis` timestamp NULL DEFAULT NULL,
  `durum` enum('aktif','pasif','beklemede','yasakli') NOT NULL DEFAULT 'beklemede',
  `aktivasyon_kodu` varchar(100) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `newsletter_izni` tinyint(1) NOT NULL DEFAULT '0',
  `son_giris_tarihi` timestamp NULL DEFAULT NULL,
  `son_giris_ip` varchar(45) DEFAULT NULL,
  `kayit_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `guncelleme_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Demo kullanıcı (şifre: demo123)
INSERT INTO `kullanicilar` (`ad`, `soyad`, `email`, `sifre`, `uyelik_tipi`, `durum`) VALUES
('Demo', 'Kullanıcı', 'demo@dobien.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'premium', 'aktif');

-- --------------------------------------------------------
-- Admin Kullanıcılar Tablosu
-- --------------------------------------------------------

CREATE TABLE `admin_kullanicilar` (
  `id` int(11) NOT NULL,
  `ad` varchar(50) NOT NULL,
  `soyad` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `sifre` varchar(255) NOT NULL,
  `yetki_seviyesi` enum('super_admin','admin','moderator') NOT NULL DEFAULT 'admin',
  `profil_resmi` varchar(255) DEFAULT NULL,
  `durum` enum('aktif','pasif') NOT NULL DEFAULT 'aktif',
  `son_giris_tarihi` timestamp NULL DEFAULT NULL,
  `son_giris_ip` varchar(45) DEFAULT NULL,
  `olusturma_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `guncelleme_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Demo admin (şifre: admin123)
INSERT INTO `admin_kullanicilar` (`ad`, `soyad`, `email`, `sifre`, `yetki_seviyesi`) VALUES
('DOBİEN', 'Admin', 'admin@dobien.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin');

-- --------------------------------------------------------
-- Kategoriler Tablosu
-- --------------------------------------------------------

CREATE TABLE `kategoriler` (
  `id` int(11) NOT NULL,
  `kategori_adi` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `aciklama` text,
  `resim` varchar(255) DEFAULT NULL,
  `siralama` int(11) NOT NULL DEFAULT '0',
  `durum` enum('aktif','pasif') NOT NULL DEFAULT 'aktif',
  `olusturma_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `guncelleme_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `kategoriler` (`kategori_adi`, `slug`, `aciklama`, `siralama`) VALUES
('Aksiyon', 'aksiyon', 'Heyecan dolu aksiyon videoları', 1),
('Drama', 'drama', 'Duygusal drama içerikleri', 2),
('Komedi', 'komedi', 'Eğlenceli komedi videoları', 3),
('Korku', 'korku', 'Gerilim dolu korku filmleri', 4),
('Romantik', 'romantik', 'Aşk temalı romantik içerikler', 5),
('Bilim Kurgu', 'bilim-kurgu', 'Gelecek ve teknoloji temalı yapımlar', 6),
('Belgesel', 'belgesel', 'Eğitici belgesel videoları', 7),
('Müzik', 'muzik', 'Müzik videoları ve konserler', 8);

-- --------------------------------------------------------
-- Videolar Tablosu
-- --------------------------------------------------------

CREATE TABLE `videolar` (
  `id` int(11) NOT NULL,
  `baslik` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `aciklama` text,
  `kategori_id` int(11) DEFAULT NULL,
  `kapak_resmi` varchar(255) DEFAULT NULL,
  `video_dosyasi_720p` varchar(255) DEFAULT NULL,
  `video_dosyasi_1080p` varchar(255) DEFAULT NULL,
  `video_dosyasi_4k` varchar(255) DEFAULT NULL,
  `sure` time DEFAULT NULL,
  `dosya_boyutu` bigint(20) DEFAULT NULL,
  `goruntulenme_yetkisi` enum('herkes','vip','premium') NOT NULL DEFAULT 'herkes',
  `ozellik` enum('normal','populer','editor_secimi','yeni') DEFAULT 'normal',
  `etiketler` text,
  `izlenme_sayisi` bigint(20) NOT NULL DEFAULT '0',
  `begeni_sayisi` bigint(20) NOT NULL DEFAULT '0',
  `begenme_sayisi` bigint(20) NOT NULL DEFAULT '0',
  `favori_sayisi` bigint(20) NOT NULL DEFAULT '0',
  `sikayet_sayisi` int(11) NOT NULL DEFAULT '0',
  `durum` enum('aktif','pasif','beklemede','silinmis') NOT NULL DEFAULT 'beklemede',
  `ekleme_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `guncelleme_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Video Beğeniler Tablosu
-- --------------------------------------------------------

CREATE TABLE `video_begeniler` (
  `id` int(11) NOT NULL,
  `kullanici_id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `tur` enum('begeni','begenme') NOT NULL,
  `tarih` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Favoriler Tablosu
-- --------------------------------------------------------

CREATE TABLE `favoriler` (
  `id` int(11) NOT NULL,
  `kullanici_id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `ekleme_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- İzleme Geçmişi Tablosu
-- --------------------------------------------------------

CREATE TABLE `izleme_gecmisi` (
  `id` int(11) NOT NULL,
  `kullanici_id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `izleme_suresi` time DEFAULT NULL,
  `izleme_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Video Şikayetler Tablosu
-- --------------------------------------------------------

CREATE TABLE `video_sikayetler` (
  `id` int(11) NOT NULL,
  `kullanici_id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `sikayet_sebebi` text NOT NULL,
  `durum` enum('beklemede','inceleniyor','kabul_edildi','reddedildi') NOT NULL DEFAULT 'beklemede',
  `admin_notu` text,
  `ip_adresi` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `sikayet_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `islem_tarihi` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Slider Tablosu
-- --------------------------------------------------------

CREATE TABLE `slider` (
  `id` int(11) NOT NULL,
  `baslik` varchar(255) NOT NULL,
  `aciklama` text,
  `resim` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `buton_metni` varchar(50) DEFAULT NULL,
  `siralama` int(11) NOT NULL DEFAULT '0',
  `durum` enum('aktif','pasif') NOT NULL DEFAULT 'aktif',
  `olusturma_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `guncelleme_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `slider` (`baslik`, `aciklama`, `resim`, `link`, `buton_metni`, `siralama`, `durum`) VALUES
('DOBİEN Video Platform''a Hoş Geldiniz', 'Premium video deneyimi için üyeliğinizi yükseltin. 4K kalite, sınırsız izleme ve özel içerikler.', 'slide1.jpg', '/uyelik-yukselt.php', 'Üyeliği Yükselt', 1, 'aktif'),
('VIP Üyelik Avantajları', '1080p kalitede videolar izleyin. VIP üyelerimize özel içerikler ve avantajlar.', 'slide2.jpg', '/vip.php', 'VIP Ol', 2, 'aktif'),
('Premium 4K İçerikler', 'En yüksek kalitede video deneyimi. Premium üyelerimize özel 4K Ultra HD videolar.', 'slide3.jpg', '/premium.php', 'Premium Ol', 3, 'aktif'),
('Popüler Videolar', 'En çok izlenen ve beğenilen videolarımızı keşfedin.', 'slide4.jpg', '/populer.php', 'Keşfet', 4, 'aktif'),
('Yeni Eklenen İçerikler', 'Her gün yeni videolar ekliyoruz. Güncel içerikleri kaçırmayın.', 'slide5.jpg', '/yeni-videolar.php', 'İncele', 5, 'aktif');

-- --------------------------------------------------------
-- Menü Tablosu
-- --------------------------------------------------------

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `menu_adi` varchar(100) NOT NULL,
  `link` varchar(255) NOT NULL,
  `ust_menu_id` int(11) DEFAULT NULL,
  `siralama` int(11) NOT NULL DEFAULT '0',
  `durum` enum('aktif','pasif') NOT NULL DEFAULT 'aktif',
  `olusturma_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `menu` (`menu_adi`, `link`, `siralama`) VALUES
('Ana Sayfa', '/', 1),
('Videolar', '/videolar.php', 2),
('Kategoriler', '/kategoriler.php', 3),
('Popüler', '/populer.php', 4),
('VIP', '/vip.php', 5),
('Premium', '/premium.php', 6),
('İletişim', '/iletisim.php', 7);

-- --------------------------------------------------------
-- Sayfalar Tablosu
-- --------------------------------------------------------

CREATE TABLE `sayfalar` (
  `id` int(11) NOT NULL,
  `sayfa_adi` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `icerik` longtext,
  `meta_baslik` varchar(255) DEFAULT NULL,
  `meta_aciklama` text,
  `meta_anahtar_kelimeler` text,
  `durum` enum('aktif','pasif') NOT NULL DEFAULT 'aktif',
  `olusturma_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `guncelleme_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sayfalar` (`sayfa_adi`, `slug`, `icerik`, `meta_baslik`, `meta_aciklama`) VALUES
('Hakkımızda', 'hakkimizda', '<h1>DOBİEN Video Platform Hakkında</h1><p>DOBİEN Video Platform, kullanıcılarına en kaliteli video deneyimini sunmak için geliştirilmiş modern bir video paylaşım platformudur.</p>', 'Hakkımızda - DOBİEN', 'DOBİEN Video Platform hakkında bilgi edinin'),
('Kullanım Şartları', 'kullanim-sartlari', '<h1>Kullanım Şartları</h1><p>Bu platformu kullanarak aşağıdaki şartları kabul etmiş sayılırsınız...</p>', 'Kullanım Şartları', 'Platform kullanım şartları ve kuralları'),
('Gizlilik Politikası', 'gizlilik-politikasi', '<h1>Gizlilik Politikası</h1><p>Kişisel verilerinizin güvenliği bizim için önemlidir...</p>', 'Gizlilik Politikası', 'Kişisel verilerin korunması ve gizlilik politikası'),
('İletişim', 'iletisim', '<h1>İletişim</h1><p>Bizimle iletişime geçin:</p><p>E-posta: info@dobien.com</p>', 'İletişim', 'DOBİEN ile iletişime geçin');

-- --------------------------------------------------------
-- Admin Bildirimler Tablosu
-- --------------------------------------------------------

CREATE TABLE `admin_bildirimler` (
  `id` int(11) NOT NULL,
  `tip` enum('bilgi','uyari','hata','basari','sikayet') NOT NULL DEFAULT 'bilgi',
  `baslik` varchar(255) NOT NULL,
  `mesaj` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `durum` enum('okunmamis','okunmus') NOT NULL DEFAULT 'okunmamis',
  `olusturma_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `okunma_tarihi` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Ödeme Geçmişi Tablosu
-- --------------------------------------------------------

CREATE TABLE `odeme_gecmisi` (
  `id` int(11) NOT NULL,
  `kullanici_id` int(11) NOT NULL,
  `odeme_turu` enum('uyelik_yukseltme','yenileme') NOT NULL,
  `eski_uyelik` enum('kullanici','vip','premium') NOT NULL,
  `yeni_uyelik` enum('vip','premium') NOT NULL,
  `tutar` decimal(10,2) NOT NULL,
  `para_birimi` varchar(3) NOT NULL DEFAULT 'TRY',
  `odeme_yontemi` varchar(50) DEFAULT NULL,
  `islem_no` varchar(100) DEFAULT NULL,
  `durum` enum('beklemede','tamamlandi','iptal_edildi','hata') NOT NULL DEFAULT 'beklemede',
  `odeme_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `onay_tarihi` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- E-posta Şablonları Tablosu
-- --------------------------------------------------------

CREATE TABLE `email_sablonlari` (
  `id` int(11) NOT NULL,
  `sablon_adi` varchar(100) NOT NULL,
  `konu` varchar(255) NOT NULL,
  `icerik` longtext NOT NULL,
  `degiskenler` text,
  `durum` enum('aktif','pasif') NOT NULL DEFAULT 'aktif',
  `olusturma_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `guncelleme_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `email_sablonlari` (`sablon_adi`, `konu`, `icerik`, `degiskenler`) VALUES
('hesap_aktivasyon', 'Hesap Aktivasyonu - DOBİEN', '<h1>Hoş Geldiniz!</h1><p>Hesabınızı aktive etmek için <a href="{aktivasyon_link}">buraya tıklayın</a>.</p>', '{ad_soyad}, {aktivasyon_link}'),
('sifre_sifirlama', 'Şifre Sıfırlama - DOBİEN', '<h1>Şifre Sıfırlama</h1><p>Şifrenizi sıfırlamak için <a href="{sifirlama_link}">buraya tıklayın</a>.</p>', '{ad_soyad}, {sifirlama_link}'),
('uyelik_yukseltme', 'Üyelik Yükseltme Onayı - DOBİEN', '<h1>Üyelik Yükseltme</h1><p>Üyeliğiniz başarıyla {yeni_uyelik} olarak yükseltildi.</p>', '{ad_soyad}, {yeni_uyelik}');

-- --------------------------------------------------------
-- İstatistikler Tablosu
-- --------------------------------------------------------

CREATE TABLE `istatistikler` (
  `id` int(11) NOT NULL,
  `tarih` date NOT NULL,
  `toplam_kullanici` int(11) NOT NULL DEFAULT '0',
  `aktif_kullanici` int(11) NOT NULL DEFAULT '0',
  `yeni_kullanici` int(11) NOT NULL DEFAULT '0',
  `toplam_video` int(11) NOT NULL DEFAULT '0',
  `yeni_video` int(11) NOT NULL DEFAULT '0',
  `toplam_izlenme` bigint(20) NOT NULL DEFAULT '0',
  `gunluk_izlenme` bigint(20) NOT NULL DEFAULT '0',
  `vip_kullanici` int(11) NOT NULL DEFAULT '0',
  `premium_kullanici` int(11) NOT NULL DEFAULT '0',
  `gunluk_gelir` decimal(10,2) NOT NULL DEFAULT '0.00',
  `olusturma_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Log Tablosu
-- --------------------------------------------------------

CREATE TABLE `sistem_loglari` (
  `id` int(11) NOT NULL,
  `kullanici_id` int(11) DEFAULT NULL,
  `kullanici_tipi` enum('admin','kullanici','misafir') NOT NULL DEFAULT 'misafir',
  `islem` varchar(100) NOT NULL,
  `detaylar` text,
  `ip_adresi` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `tarih` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- İndeksler ve Primary Key'ler
-- --------------------------------------------------------

ALTER TABLE `site_ayarlari`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `anahtar` (`anahtar`);

ALTER TABLE `kullanicilar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `uyelik_tipi` (`uyelik_tipi`),
  ADD KEY `durum` (`durum`);

ALTER TABLE `admin_kullanicilar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `kategoriler`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `durum` (`durum`);

ALTER TABLE `videolar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `kategori_id` (`kategori_id`),
  ADD KEY `goruntulenme_yetkisi` (`goruntulenme_yetkisi`),
  ADD KEY `durum` (`durum`),
  ADD KEY `ozellik` (`ozellik`);

ALTER TABLE `video_begeniler`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kullanici_video` (`kullanici_id`,`video_id`),
  ADD KEY `video_id` (`video_id`);

ALTER TABLE `favoriler`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kullanici_video` (`kullanici_id`,`video_id`),
  ADD KEY `video_id` (`video_id`);

ALTER TABLE `izleme_gecmisi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kullanici_id` (`kullanici_id`),
  ADD KEY `video_id` (`video_id`);

ALTER TABLE `video_sikayetler`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kullanici_id` (`kullanici_id`),
  ADD KEY `video_id` (`video_id`),
  ADD KEY `durum` (`durum`);

ALTER TABLE `slider`
  ADD PRIMARY KEY (`id`),
  ADD KEY `durum` (`durum`);

ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ust_menu_id` (`ust_menu_id`),
  ADD KEY `durum` (`durum`);

ALTER TABLE `sayfalar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `durum` (`durum`);

ALTER TABLE `admin_bildirimler`
  ADD PRIMARY KEY (`id`),
  ADD KEY `durum` (`durum`),
  ADD KEY `tip` (`tip`);

ALTER TABLE `odeme_gecmisi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kullanici_id` (`kullanici_id`),
  ADD KEY `durum` (`durum`);

ALTER TABLE `email_sablonlari`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sablon_adi` (`sablon_adi`);

ALTER TABLE `istatistikler`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tarih` (`tarih`);

ALTER TABLE `sistem_loglari`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kullanici_id` (`kullanici_id`),
  ADD KEY `kullanici_tipi` (`kullanici_tipi`);

-- --------------------------------------------------------
-- AUTO_INCREMENT Değerleri
-- --------------------------------------------------------

ALTER TABLE `site_ayarlari`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

ALTER TABLE `kullanicilar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `admin_kullanicilar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `kategoriler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `videolar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `video_begeniler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `favoriler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `izleme_gecmisi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `video_sikayetler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `slider`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `sayfalar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `admin_bildirimler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `odeme_gecmisi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `email_sablonlari`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `istatistikler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `sistem_loglari`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

-- --------------------------------------------------------
-- Foreign Key Constraints
-- --------------------------------------------------------

ALTER TABLE `videolar`
  ADD CONSTRAINT `fk_videolar_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategoriler` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `video_begeniler`
  ADD CONSTRAINT `fk_video_begeniler_kullanici` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_video_begeniler_video` FOREIGN KEY (`video_id`) REFERENCES `videolar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `favoriler`
  ADD CONSTRAINT `fk_favoriler_kullanici` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_favoriler_video` FOREIGN KEY (`video_id`) REFERENCES `videolar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `izleme_gecmisi`
  ADD CONSTRAINT `fk_izleme_gecmisi_kullanici` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_izleme_gecmisi_video` FOREIGN KEY (`video_id`) REFERENCES `videolar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `video_sikayetler`
  ADD CONSTRAINT `fk_video_sikayetler_kullanici` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_video_sikayetler_video` FOREIGN KEY (`video_id`) REFERENCES `videolar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `odeme_gecmisi`
  ADD CONSTRAINT `fk_odeme_gecmisi_kullanici` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

COMMIT;

-- --------------------------------------------------------
-- Son Not: DOBİEN Video Platform
-- Bu veritabanı yapısı DOBİEN tarafından geliştirilmiştir.
-- Modern video paylaşım platformu için optimize edilmiştir.
-- Tüm hakları saklıdır © DOBİEN
-- --------------------------------------------------------