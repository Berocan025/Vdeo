</main>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <div class="footer-logo">
                    <h3><span class="text-gradient">DOBİEN</span></h3>
                    <p>Modern Video Platform</p>
                    <p class="footer-desc">En kaliteli video içerikleri ve premium üyelik avantajları ile video izleme deneyiminizi bir üst seviyeye taşıyoruz.</p>
                </div>
                <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-discord"></i></a>
                </div>
            </div>

            <div class="footer-section">
                <h4>Hızlı Linkler</h4>
                <ul class="footer-links">
                    <li><a href="<?php echo siteUrl(); ?>">Ana Sayfa</a></li>
                    <li><a href="<?php echo siteUrl('kategoriler.php'); ?>">Kategoriler</a></li>
                    <li><a href="<?php echo siteUrl('populer.php'); ?>">Popüler Videolar</a></li>
                    <li><a href="<?php echo siteUrl('yeni-videolar.php'); ?>">Yeni Videolar</a></li>
                    <li><a href="<?php echo siteUrl('en-cok-izlenen.php'); ?>">En Çok İzlenen</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Üyelik</h4>
                <ul class="footer-links">
                    <li><a href="<?php echo siteUrl('uyelik-yukselt.php'); ?>">Üyelik Paketleri</a></li>
                    <li><a href="<?php echo siteUrl('premium-avantajlar.php'); ?>">Premium Avantajları</a></li>
                    <li><a href="<?php echo siteUrl('vip-avantajlar.php'); ?>">VIP Avantajları</a></li>
                    <li><a href="<?php echo siteUrl('ucretsiz-deneme.php'); ?>">Ücretsiz Deneme</a></li>
                    <li><a href="<?php echo siteUrl('hediye-kod.php'); ?>">Hediye Kodu</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Destek</h4>
                <ul class="footer-links">
                    <li><a href="<?php echo siteUrl('hakkimizda.php'); ?>">Hakkımızda</a></li>
                    <li><a href="<?php echo siteUrl('iletisim.php'); ?>">İletişim</a></li>
                    <li><a href="<?php echo siteUrl('sss.php'); ?>">Sık Sorulan Sorular</a></li>
                    <li><a href="<?php echo siteUrl('yardim.php'); ?>">Yardım Merkezi</a></li>
                    <li><a href="<?php echo siteUrl('canlı-destek.php'); ?>">Canlı Destek</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Yasal</h4>
                <ul class="footer-links">
                    <li><a href="<?php echo siteUrl('kullanim-kosullari.php'); ?>">Kullanım Koşulları</a></li>
                    <li><a href="<?php echo siteUrl('gizlilik-politikasi.php'); ?>">Gizlilik Politikası</a></li>
                    <li><a href="<?php echo siteUrl('cerez-politikasi.php'); ?>">Çerez Politikası</a></li>
                    <li><a href="<?php echo siteUrl('telif-hakki.php'); ?>">Telif Hakkı</a></li>
                    <li><a href="<?php echo siteUrl('dmca.php'); ?>">DMCA</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <div class="copyright">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo safeOutput($site_settings['site_adi']); ?>. Tüm hakları saklıdır.</p>
                    <p class="developer-credit">
                        <i class="fas fa-code"></i> 
                        <strong>Geliştirici: DOBİEN</strong> - Modern Video Platform Teknolojisi
                    </p>
                </div>
                <div class="footer-badges">
                    <div class="quality-badges">
                        <span class="badge">4K Ultra HD</span>
                        <span class="badge">Dolby Atmos</span>
                        <span class="badge">HDR</span>
                        <span class="badge">60 FPS</span>
                    </div>
                    <div class="secure-badges">
                        <i class="fas fa-shield-alt"></i>
                        <span>SSL Güvenli</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top -->
<button id="backToTop" class="back-to-top">
    <i class="fas fa-chevron-up"></i>
</button>

<!-- Scripts -->
<script src="<?php echo siteUrl('assets/js/main.js?v=' . time()); ?>"></script>

<!-- Dark Mode Toggle -->
<div class="theme-toggle">
    <button id="themeToggle" class="theme-btn">
        <i class="fas fa-moon"></i>
    </button>
</div>

<!-- Video Player Modal -->
<div id="videoModal" class="video-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalVideoTitle"></h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <video id="modalVideo" controls>
                <source src="" type="video/mp4">
                Tarayıcınız video oynatmayı desteklemiyor.
            </video>
            <div class="video-info">
                <div class="video-stats">
                    <span class="views"><i class="fas fa-eye"></i> <span id="modalViews">0</span> görüntülenme</span>
                    <span class="duration"><i class="fas fa-clock"></i> <span id="modalDuration">0:00</span></span>
                </div>
                <div class="video-actions">
                    <button class="action-btn like-btn" data-video-id="">
                        <i class="fas fa-thumbs-up"></i> 
                        <span class="like-count">0</span>
                    </button>
                    <button class="action-btn dislike-btn" data-video-id="">
                        <i class="fas fa-thumbs-down"></i>
                        <span class="dislike-count">0</span>
                    </button>
                    <button class="action-btn favorite-btn" data-video-id="">
                        <i class="fas fa-heart"></i> Favorilere Ekle
                    </button>
                    <button class="action-btn playlist-btn" data-video-id="">
                        <i class="fas fa-bookmark"></i> Listeye Ekle
                    </button>
                    <button class="action-btn share-btn" data-video-id="">
                        <i class="fas fa-share"></i> Paylaş
                    </button>
                </div>
                <div class="video-description">
                    <p id="modalDescription"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notification Toast -->
<div id="toast" class="toast">
    <div class="toast-content">
        <i class="toast-icon"></i>
        <span class="toast-message"></span>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="loading-overlay">
    <div class="loading-spinner">
        <div class="spinner"></div>
        <p>Yükleniyor...</p>
    </div>
</div>

</body>
</html>