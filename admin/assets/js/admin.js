/**
 * DOBİEN Video Platform - Admin Panel JavaScript
 * Geliştirici: DOBİEN
 * Modern Video Paylaşım Platformu - Admin Panel JS
 * Tüm Hakları Saklıdır © DOBİEN
 */

class DOBIENAdminPanel {
    constructor() {
        this.init();
    }

    init() {
        console.log('DOBİEN Admin Panel başlatılıyor...');
        
        // DOM yüklendikten sonra çalıştır
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.initializeComponents();
            });
        } else {
            this.initializeComponents();
        }
    }

    initializeComponents() {
        this.initializeSidebar();
        this.initializeDropdowns();
        this.initializeTooltips();
        this.initializeModals();
        this.initializeForms();
        this.initializeDataTables();
        this.initializeCharts();
        this.initializeFileUploads();
        this.initializeNotifications();
        this.initializeSearchFunctionality();
        this.initializeAnimations();
        
        console.log('DOBİEN Admin Panel başlatıldı!');
    }

    // Sidebar işlevleri
    initializeSidebar() {
        const sidebar = document.getElementById('adminSidebar');
        const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
        const sidebarToggle = document.getElementById('sidebarToggle');

        // Mobile sidebar toggle
        if (mobileSidebarToggle) {
            mobileSidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('mobile-open');
                this.addOverlay();
            });
        }

        // Desktop sidebar toggle
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
            });
        }

        // Submenu toggle
        document.querySelectorAll('.has-submenu').forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleSubmenu(item);
            });
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(e.target) && !mobileSidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('mobile-open');
                    this.removeOverlay();
                }
            }
        });
    }

    toggleSubmenu(item) {
        const submenuId = item.getAttribute('data-submenu');
        const submenu = document.getElementById(`submenu-${submenuId}`);
        const arrow = item.querySelector('.submenu-arrow');

        // Close other submenus
        document.querySelectorAll('.submenu').forEach(menu => {
            if (menu !== submenu) {
                menu.classList.remove('active');
            }
        });

        document.querySelectorAll('.has-submenu').forEach(menuItem => {
            if (menuItem !== item) {
                menuItem.classList.remove('active');
            }
        });

        // Toggle current submenu
        submenu.classList.toggle('active');
        item.classList.toggle('active');
    }

    addOverlay() {
        const overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;
        document.body.appendChild(overlay);
        
        setTimeout(() => {
            overlay.style.opacity = '1';
        }, 10);

        overlay.addEventListener('click', () => {
            document.getElementById('adminSidebar').classList.remove('mobile-open');
            this.removeOverlay();
        });
    }

    removeOverlay() {
        const overlay = document.querySelector('.sidebar-overlay');
        if (overlay) {
            overlay.style.opacity = '0';
            setTimeout(() => {
                overlay.remove();
            }, 300);
        }
    }

    // Dropdown işlevleri
    initializeDropdowns() {
        document.querySelectorAll('.dropdown').forEach(dropdown => {
            const toggle = dropdown.querySelector('button, .dropdown-toggle');
            const menu = dropdown.querySelector('.dropdown-menu');

            if (toggle && menu) {
                toggle.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.toggleDropdown(dropdown);
                });
            }
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', () => {
            this.closeAllDropdowns();
        });

        // Prevent dropdown from closing when clicking inside
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        });
    }

    toggleDropdown(dropdown) {
        const menu = dropdown.querySelector('.dropdown-menu');
        
        // Close other dropdowns
        this.closeAllDropdowns();
        
        // Toggle current dropdown
        menu.classList.toggle('show');
    }

    closeAllDropdowns() {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.classList.remove('show');
        });
    }

    // Tooltip işlevleri
    initializeTooltips() {
        document.querySelectorAll('[title]').forEach(element => {
            element.addEventListener('mouseenter', (e) => {
                this.showTooltip(e.target, e.target.getAttribute('title'));
            });

            element.addEventListener('mouseleave', () => {
                this.hideTooltip();
            });
        });
    }

    showTooltip(element, text) {
        const tooltip = document.createElement('div');
        tooltip.className = 'admin-tooltip';
        tooltip.textContent = text;
        tooltip.style.cssText = `
            position: absolute;
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.8rem;
            z-index: 10000;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.2s ease;
            white-space: nowrap;
        `;

        document.body.appendChild(tooltip);

        const rect = element.getBoundingClientRect();
        tooltip.style.top = `${rect.bottom + 5}px`;
        tooltip.style.left = `${rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2)}px`;

        setTimeout(() => {
            tooltip.style.opacity = '1';
        }, 10);
    }

    hideTooltip() {
        const tooltip = document.querySelector('.admin-tooltip');
        if (tooltip) {
            tooltip.remove();
        }
    }

    // Modal işlevleri
    initializeModals() {
        document.querySelectorAll('[data-modal]').forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                const modalId = trigger.getAttribute('data-modal');
                this.openModal(modalId);
            });
        });

        document.querySelectorAll('.modal-close, .modal-overlay').forEach(closeBtn => {
            closeBtn.addEventListener('click', () => {
                this.closeModal();
            });
        });

        // ESC key to close modal
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeModal();
            }
        });
    }

    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }

    closeModal() {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.classList.remove('show');
        });
        document.body.style.overflow = '';
    }

    // Form işlevleri
    initializeForms() {
        // Form validation
        document.querySelectorAll('form[data-validate]').forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                }
            });
        });

        // Auto-save forms
        document.querySelectorAll('form[data-autosave]').forEach(form => {
            form.addEventListener('input', () => {
                this.autoSaveForm(form);
            });
        });

        // File input preview
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', (e) => {
                this.previewFile(e.target);
            });
        });
    }

    validateForm(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');

        inputs.forEach(input => {
            if (!input.value.trim()) {
                this.showFieldError(input, 'Bu alan zorunludur');
                isValid = false;
            } else {
                this.clearFieldError(input);
            }
        });

        return isValid;
    }

    showFieldError(input, message) {
        this.clearFieldError(input);
        
        const error = document.createElement('div');
        error.className = 'field-error';
        error.textContent = message;
        error.style.cssText = `
            color: #ef4444;
            font-size: 0.8rem;
            margin-top: 0.25rem;
        `;
        
        input.parentNode.appendChild(error);
        input.style.borderColor = '#ef4444';
    }

    clearFieldError(input) {
        const error = input.parentNode.querySelector('.field-error');
        if (error) {
            error.remove();
        }
        input.style.borderColor = '';
    }

    autoSaveForm(form) {
        // Implement auto-save functionality
        console.log('Auto-saving form...');
    }

    previewFile(input) {
        const file = input.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const preview = input.parentNode.querySelector('.file-preview');
                if (preview) {
                    preview.src = e.target.result;
                }
            };
            reader.readAsDataURL(file);
        }
    }

    // Data Tables
    initializeDataTables() {
        if (typeof DataTable !== 'undefined') {
            document.querySelectorAll('.admin-datatable').forEach(table => {
                new DataTable(table, {
                    responsive: true,
                    pageLength: 25,
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/tr.json'
                    }
                });
            });
        }
    }

    // Charts
    initializeCharts() {
        if (typeof Chart !== 'undefined') {
            this.createDashboardCharts();
        }
    }

    createDashboardCharts() {
        // User Statistics Chart
        const userChartCtx = document.getElementById('userChart');
        if (userChartCtx) {
            new Chart(userChartCtx, {
                type: 'line',
                data: {
                    labels: ['Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz'],
                    datasets: [{
                        label: 'Kullanıcılar',
                        data: [12, 19, 3, 5, 2, 3],
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Revenue Chart
        const revenueChartCtx = document.getElementById('revenueChart');
        if (revenueChartCtx) {
            new Chart(revenueChartCtx, {
                type: 'bar',
                data: {
                    labels: ['Premium', 'VIP', 'Ücretsiz'],
                    datasets: [{
                        label: 'Gelir',
                        data: [300, 50, 100],
                        backgroundColor: [
                            '#6366f1',
                            '#f59e0b',
                            '#10b981'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
    }

    // File Upload
    initializeFileUploads() {
        document.querySelectorAll('.file-upload-area').forEach(area => {
            const input = area.querySelector('input[type="file"]');
            
            if (input) {
                // Drag and drop
                area.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    area.classList.add('dragover');
                });

                area.addEventListener('dragleave', () => {
                    area.classList.remove('dragover');
                });

                area.addEventListener('drop', (e) => {
                    e.preventDefault();
                    area.classList.remove('dragover');
                    
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        input.files = files;
                        this.handleFileUpload(input, files[0]);
                    }
                });

                // Click to upload
                area.addEventListener('click', () => {
                    input.click();
                });

                input.addEventListener('change', (e) => {
                    if (e.target.files.length > 0) {
                        this.handleFileUpload(input, e.target.files[0]);
                    }
                });
            }
        });
    }

    handleFileUpload(input, file) {
        // Show file info
        const info = input.parentNode.querySelector('.file-info');
        if (info) {
            info.textContent = `${file.name} (${this.formatFileSize(file.size)})`;
        }

        // Preview if image
        if (file.type.startsWith('image/')) {
            this.previewFile(input);
        }
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Notifications
    initializeNotifications() {
        // Mark notifications as read
        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', () => {
                item.classList.add('read');
            });
        });
    }

    // Search Functionality
    initializeSearchFunctionality() {
        const searchInputs = document.querySelectorAll('[data-search]');
        
        searchInputs.forEach(input => {
            const target = input.getAttribute('data-search');
            const targetElements = document.querySelectorAll(target);
            
            input.addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase();
                
                targetElements.forEach(element => {
                    const text = element.textContent.toLowerCase();
                    if (text.includes(query)) {
                        element.style.display = '';
                    } else {
                        element.style.display = 'none';
                    }
                });
            });
        });
    }

    // Animations
    initializeAnimations() {
        // Fade in animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                }
            });
        });

        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });

        // Loading states
        document.querySelectorAll('[data-loading]').forEach(btn => {
            btn.addEventListener('click', () => {
                this.showLoading(btn);
            });
        });
    }

    showLoading(element) {
        element.classList.add('loading');
        element.disabled = true;
        
        const originalText = element.textContent;
        element.innerHTML = '<i class="loading-spinner"></i> Yükleniyor...';
        
        // Simulate loading
        setTimeout(() => {
            this.hideLoading(element, originalText);
        }, 2000);
    }

    hideLoading(element, originalText) {
        element.classList.remove('loading');
        element.disabled = false;
        element.textContent = originalText;
    }

    // Utility functions
    showAlert(message, type = 'info') {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.innerHTML = `
            <i class="fas fa-info-circle"></i>
            ${message}
            <button class="alert-close">&times;</button>
        `;
        
        alert.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            max-width: 300px;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
        `;
        
        document.body.appendChild(alert);
        
        setTimeout(() => {
            alert.style.opacity = '1';
            alert.style.transform = 'translateX(0)';
        }, 10);
        
        // Auto close
        setTimeout(() => {
            this.hideAlert(alert);
        }, 5000);
        
        // Close button
        alert.querySelector('.alert-close').addEventListener('click', () => {
            this.hideAlert(alert);
        });
    }

    hideAlert(alert) {
        alert.style.opacity = '0';
        alert.style.transform = 'translateX(100%)';
        setTimeout(() => {
            alert.remove();
        }, 300);
    }

    // AJAX helper
    async ajax(url, options = {}) {
        const defaultOptions = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };
        
        const config = { ...defaultOptions, ...options };
        
        try {
            const response = await fetch(url, config);
            return await response.json();
        } catch (error) {
            console.error('AJAX Error:', error);
            this.showAlert('Bir hata oluştu', 'error');
            throw error;
        }
    }

    // Format date
    formatDate(date, format = 'dd.mm.yyyy') {
        const d = new Date(date);
        const day = String(d.getDate()).padStart(2, '0');
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const year = d.getFullYear();
        
        return format
            .replace('dd', day)
            .replace('mm', month)
            .replace('yyyy', year);
    }

    // Copy to clipboard
    copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            this.showAlert('Panoya kopyalandı', 'success');
        }).catch(() => {
            this.showAlert('Kopyalama başarısız', 'error');
        });
    }
}

// Initialize admin panel
const adminPanel = new DOBIENAdminPanel();

// Global helper functions
window.DOBIENAdmin = {
    showAlert: (message, type) => adminPanel.showAlert(message, type),
    ajax: (url, options) => adminPanel.ajax(url, options),
    formatDate: (date, format) => adminPanel.formatDate(date, format),
    copyToClipboard: (text) => adminPanel.copyToClipboard(text)
};

// Console message
console.log('%c🚀 DOBİEN Video Platform Admin Panel', 'color: #6366f1; font-size: 16px; font-weight: bold;');
console.log('%cGeliştirici: DOBİEN', 'color: #10b981; font-size: 12px;');
console.log('%cTüm Hakları Saklıdır © DOBİEN', 'color: #6b7280; font-size: 10px;');