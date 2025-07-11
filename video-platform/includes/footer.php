</main>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <!-- Footer Logo ve Açıklama -->
            <div class="footer-section">
                <div class="footer-logo">
                    <h3><span class="text-gradient">DOBİEN</span></h3>
                    <p class="footer-subtitle">Video Platform</p>
                </div>
                <p class="footer-description">
                    Modern video paylaşım platformu. Premium kalitede içerikler, 4K video deneyimi ve sınırsız izleme imkanı.
                </p>
                <div class="social-links">
                    <a href="#" class="social-link" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-link" title="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-link" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="social-link" title="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>

            <!-- Hızlı Linkler -->
            <div class="footer-section">
                <h4>Hızlı Linkler</h4>
                <ul class="footer-links">
                    <li><a href="<?php echo siteUrl(); ?>">Ana Sayfa</a></li>
                    <li><a href="<?php echo siteUrl('kategoriler.php'); ?>">Kategoriler</a></li>
                    <li><a href="<?php echo siteUrl('populer.php'); ?>">Popüler Videolar</a></li>
                    <li><a href="<?php echo siteUrl('yeni-videolar.php'); ?>">Yeni Videolar</a></li>
                    <li><a href="<?php echo siteUrl('arama.php'); ?>">Video Arama</a></li>
                </ul>
            </div>

            <!-- Üyelik -->
            <div class="footer-section">
                <h4>Üyelik</h4>
                <ul class="footer-links">
                    <?php if (!$current_user): ?>
                    <li><a href="<?php echo siteUrl('giris.php'); ?>">Giriş Yap</a></li>
                    <li><a href="<?php echo siteUrl('kayit.php'); ?>">Kayıt Ol</a></li>
                    <?php else: ?>
                    <li><a href="<?php echo siteUrl('profil.php'); ?>">Profilim</a></li>
                    <li><a href="<?php echo siteUrl('favoriler.php'); ?>">Favorilerim</a></li>
                    <li><a href="<?php echo siteUrl('izleme-gecmisi.php'); ?>">İzleme Geçmişi</a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo siteUrl('uyelik-yukselt.php'); ?>">Üyeliği Yükselt</a></li>
                    <li><a href="<?php echo siteUrl('premium-avantajlar.php'); ?>">Premium Avantajlar</a></li>
                </ul>
            </div>

            <!-- Destek -->
            <div class="footer-section">
                <h4>Destek</h4>
                <ul class="footer-links">
                    <li><a href="<?php echo siteUrl('hakkimizda.php'); ?>">Hakkımızda</a></li>
                    <li><a href="<?php echo siteUrl('iletisim.php'); ?>">İletişim</a></li>
                    <li><a href="<?php echo siteUrl('sss.php'); ?>">Sıkça Sorulan Sorular</a></li>
                    <li><a href="<?php echo siteUrl('gizlilik-politikasi.php'); ?>">Gizlilik Politikası</a></li>
                    <li><a href="<?php echo siteUrl('kullanim-sartlari.php'); ?>">Kullanım Şartları</a></li>
                </ul>
            </div>
        </div>

        <!-- Footer Alt Kısım -->
        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <div class="copyright">
                    <p>&copy; <?php echo date('Y'); ?> DOBİEN Video Platform. Tüm hakları saklıdır.</p>
                    <p class="developer-credit">
                        <i class="fas fa-code"></i> 
                        <strong>DOBİEN</strong> tarafından geliştirilmiştir
                    </p>
                </div>
                <div class="footer-features">
                    <span class="feature-badge">
                        <i class="fas fa-hd-video"></i> HD Kalite
                    </span>
                    <span class="feature-badge">
                        <i class="fas fa-mobile-alt"></i> Mobil Uyumlu
                    </span>
                    <span class="feature-badge">
                        <i class="fas fa-shield-alt"></i> Güvenli
                    </span>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
/* Footer Stilleri */
.footer {
    background: var(--bg-secondary);
    color: var(--text-secondary);
    margin-top: 4rem;
    border-top: 1px solid var(--border-color);
}

.footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    padding: 3rem 0;
}

.footer-section h4 {
    color: var(--text-primary);
    margin-bottom: 1rem;
    font-size: 1.1rem;
    font-weight: 600;
}

.footer-logo h3 {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.footer-subtitle {
    color: var(--text-muted);
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    margin-bottom: 1rem;
}

.footer-description {
    color: var(--text-muted);
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.social-links {
    display: flex;
    gap: 1rem;
}

.social-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: var(--bg-tertiary);
    color: var(--text-muted);
    border-radius: 50%;
    text-decoration: none;
    transition: var(--transition);
}

.social-link:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-2px);
}

.footer-links {
    list-style: none;
}

.footer-links li {
    margin-bottom: 0.5rem;
}

.footer-links a {
    color: var(--text-muted);
    text-decoration: none;
    transition: var(--transition);
    font-size: 0.9rem;
}

.footer-links a:hover {
    color: var(--primary-color);
}

.footer-bottom {
    border-top: 1px solid var(--border-color);
    padding: 1.5rem 0;
}

.footer-bottom-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.copyright p {
    color: var(--text-muted);
    font-size: 0.85rem;
    margin-bottom: 0.25rem;
}

.developer-credit {
    color: var(--text-muted);
    font-size: 0.8rem;
}

.developer-credit i {
    color: var(--primary-color);
    margin-right: 0.25rem;
}

.footer-features {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.feature-badge {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    background: var(--bg-tertiary);
    padding: 0.25rem 0.75rem;
    border-radius: var(--radius-md);
    font-size: 0.75rem;
    color: var(--text-muted);
    border: 1px solid var(--border-color);
}

.feature-badge i {
    color: var(--primary-color);
}

@media (max-width: 768px) {
    .footer-content {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .footer-bottom-content {
        flex-direction: column;
        text-align: center;
    }
    
    .social-links {
        justify-content: center;
    }
    
    .footer-features {
        justify-content: center;
    }
}
</style>

<!-- Scroll to Top Button -->
<button id="scrollToTop" class="scroll-to-top" title="Yukarı Çık">
    <i class="fas fa-chevron-up"></i>
</button>

<style>
.scroll-to-top {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    width: 50px;
    height: 50px;
    background: var(--gradient-primary);
    color: white;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: none;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    z-index: 1000;
    transition: var(--transition);
    box-shadow: var(--shadow-lg);
}

.scroll-to-top:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-xl);
}

.scroll-to-top.show {
    display: flex;
}
</style>

<script>
// Scroll to Top Functionality
const scrollToTopBtn = document.getElementById('scrollToTop');

window.addEventListener('scroll', () => {
    if (window.pageYOffset > 300) {
        scrollToTopBtn.classList.add('show');
    } else {
        scrollToTopBtn.classList.remove('show');
    }
});

scrollToTopBtn.addEventListener('click', () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

// Footer Copyright Year Update
document.addEventListener('DOMContentLoaded', function() {
    const currentYear = new Date().getFullYear();
    const copyrightElements = document.querySelectorAll('.copyright p');
    copyrightElements.forEach(element => {
        if (element.textContent.includes('©')) {
            element.textContent = element.textContent.replace(/© \d{4}/, `© ${currentYear}`);
        }
    });
});
</script>

</body>
</html>