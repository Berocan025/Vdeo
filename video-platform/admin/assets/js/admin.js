/**
 * DOBİEN Video Platform - Admin Panel JavaScript
 * Geliştirici: DOBİEN
 */

$(document).ready(function() {
    
    // Sidebar toggle for mobile
    $('.sidebar-toggle').click(function() {
        $('.admin-sidebar').toggleClass('show');
    });
    
    // Close sidebar when clicking outside on mobile
    $(document).click(function(e) {
        if ($(window).width() <= 768) {
            if (!$(e.target).closest('.admin-sidebar, .sidebar-toggle').length) {
                $('.admin-sidebar').removeClass('show');
            }
        }
    });
    
    // Submenu toggle
    $('.has-submenu > a').click(function(e) {
        e.preventDefault();
        const parent = $(this).parent();
        parent.toggleClass('open');
        parent.siblings('.has-submenu').removeClass('open');
    });
    
    // DataTables Türkçe
    if ($.fn.DataTable) {
        $.extend(true, $.fn.dataTable.defaults, {
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/tr.json"
            },
            "pageLength": 25,
            "responsive": true,
            "order": [[ 0, "desc" ]]
        });
    }
    
    // Select2 global settings
    if ($.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    }
    
    // Form validation
    $('.needs-validation').on('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        $(this).addClass('was-validated');
    });
    
    // Auto-hide alerts
    $('.alert').each(function() {
        const alert = $(this);
        setTimeout(function() {
            alert.fadeOut();
        }, 5000);
    });
    
    // Confirm delete buttons
    $('.delete-btn').click(function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        const text = $(this).data('text') || 'Bu işlem geri alınamaz!';
        
        Swal.fire({
            title: 'Emin misiniz?',
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Evet, sil!',
            cancelButtonText: 'İptal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });
    
    // File upload preview
    $('input[type="file"]').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            const preview = $(this).siblings('.file-preview');
            
            reader.onload = function(e) {
                if (file.type.startsWith('image/')) {
                    preview.html('<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 200px;">');
                } else {
                    preview.html('<p>Dosya seçildi: ' + file.name + '</p>');
                }
            };
            
            reader.readAsDataURL(file);
        }
    });
    
    // Drag & Drop file upload
    $('.file-upload').on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('dragover');
    }).on('dragleave', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
    }).on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
        
        const files = e.originalEvent.dataTransfer.files;
        const input = $(this).find('input[type="file"]')[0];
        input.files = files;
        $(input).trigger('change');
    });
    
    // Chart.js global defaults
    if (typeof Chart !== 'undefined') {
        Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
        Chart.defaults.color = '#858796';
        Chart.defaults.plugins.legend.labels.usePointStyle = true;
        Chart.defaults.plugins.legend.labels.padding = 20;
    }
    
    // Auto-refresh notifications
    if ($('.notification-count').length) {
        setInterval(function() {
            $.get('get_notification_count.php', function(data) {
                if (data.count > 0) {
                    $('.notification-count').text(data.count).show();
                } else {
                    $('.notification-count').hide();
                }
            }, 'json');
        }, 30000); // Her 30 saniyede bir kontrol et
    }
    
    // Auto-save forms
    $('.auto-save').on('change', function() {
        const form = $(this).closest('form');
        const formData = form.serialize();
        
        $.post(form.attr('action') || window.location.href, formData, function(response) {
            if (response.success) {
                showToast('Ayarlar otomatik olarak kaydedildi.', 'success');
            }
        }, 'json');
    });
    
    // Copy to clipboard
    $('.copy-btn').click(function() {
        const text = $(this).data('text');
        navigator.clipboard.writeText(text).then(function() {
            showToast('Panoya kopyalandı!', 'success');
        });
    });
    
    // Toggle switches
    $('.form-switch input').change(function() {
        const isChecked = $(this).is(':checked');
        const label = $(this).siblings('label');
        
        if (isChecked) {
            label.removeClass('text-muted').addClass('text-success');
        } else {
            label.removeClass('text-success').addClass('text-muted');
        }
    });
    
    // Search functionality
    $('.search-input').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        const target = $(this).data('target');
        
        $(target + ' tr').each(function() {
            const text = $(this).text().toLowerCase();
            if (text.indexOf(searchTerm) === -1) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
    });
    
    // Sortable lists
    if ($.fn.sortable) {
        $('.sortable').sortable({
            handle: '.sort-handle',
            update: function(event, ui) {
                const order = $(this).sortable('toArray', {attribute: 'data-id'});
                const url = $(this).data('url');
                
                if (url) {
                    $.post(url, {order: order}, function(response) {
                        if (response.success) {
                            showToast('Sıralama güncellendi.', 'success');
                        }
                    }, 'json');
                }
            }
        });
    }
    
    // Real-time stats update
    if ($('.stats-card').length) {
        setInterval(function() {
            $.get('get_stats.php', function(data) {
                $.each(data, function(key, value) {
                    $('.stats-' + key).text(value);
                });
            }, 'json');
        }, 60000); // Her dakika güncelle
    }
    
    // Progress bars animation
    $('.progress-bar').each(function() {
        const progress = $(this);
        const value = progress.attr('aria-valuenow');
        progress.css('width', '0%');
        
        setTimeout(function() {
            progress.animate({
                width: value + '%'
            }, 1000);
        }, 500);
    });
    
    // Tooltip initialization
    if ($.fn.tooltip) {
        $('[data-bs-toggle="tooltip"]').tooltip();
    }
    
    // Popover initialization
    if ($.fn.popover) {
        $('[data-bs-toggle="popover"]').popover();
    }
    
});

// Utility Functions
function showToast(message, type = 'info') {
    const toast = $(`
        <div class="toast align-items-center text-white bg-${type} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `);
    
    $('.toast-container').append(toast);
    new bootstrap.Toast(toast[0]).show();
    
    setTimeout(function() {
        toast.remove();
    }, 5000);
}

function formatNumber(num) {
    return new Intl.NumberFormat('tr-TR').format(num);
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('tr-TR', {
        style: 'currency',
        currency: 'TRY'
    }).format(amount);
}

function formatDate(date) {
    return new Intl.DateTimeFormat('tr-TR').format(new Date(date));
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Image preview function
function previewImage(input, target) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $(target).attr('src', e.target.result).show();
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Video preview function
function previewVideo(input, target) {
    if (input.files && input.files[0]) {
        const url = URL.createObjectURL(input.files[0]);
        $(target).attr('src', url).show();
    }
}

// AJAX form submission
function submitForm(formElement, callback) {
    const form = $(formElement);
    const formData = new FormData(form[0]);
    
    $.ajax({
        url: form.attr('action') || window.location.href,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function() {
            form.find('button[type="submit"]').prop('disabled', true);
        },
        success: function(response) {
            if (callback) {
                callback(response);
            }
        },
        error: function() {
            showToast('Bir hata oluştu!', 'danger');
        },
        complete: function() {
            form.find('button[type="submit"]').prop('disabled', false);
        }
    });
}

// Lazy loading for images
function lazyLoadImages() {
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
}

// Initialize lazy loading
document.addEventListener('DOMContentLoaded', lazyLoadImages);

// Keyboard shortcuts
$(document).keydown(function(e) {
    // Ctrl + S to save
    if (e.ctrlKey && e.which === 83) {
        e.preventDefault();
        const form = $('form:visible:first');
        if (form.length) {
            form.submit();
        }
    }
    
    // Escape to close modals
    if (e.which === 27) {
        $('.modal:visible').modal('hide');
    }
});

// Export functions
window.DOBIENAdmin = {
    showToast: showToast,
    formatNumber: formatNumber,
    formatCurrency: formatCurrency,
    formatDate: formatDate,
    formatFileSize: formatFileSize,
    previewImage: previewImage,
    previewVideo: previewVideo,
    submitForm: submitForm
};