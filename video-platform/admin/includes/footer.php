            </div> <!-- .main-content -->
        </div> <!-- .admin-content -->
    </div> <!-- .admin-wrapper -->

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Admin JS -->
    <script src="assets/js/admin.js"></script>
    
    <script>
        $(document).ready(function() {
            // DataTables başlat
            if ($('.data-table').length) {
                $('.data-table').DataTable({
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/tr.json"
                    },
                    "pageLength": 25,
                    "order": [[ 0, "desc" ]]
                });
            }
            
            // Select2 başlat
            if ($('.select2').length) {
                $('.select2').select2({
                    theme: 'bootstrap-5'
                });
            }
            
            // Sidebar menü toggle
            $('.has-submenu > a').click(function(e) {
                e.preventDefault();
                $(this).parent().toggleClass('open');
            });
            
            // Form validasyonu
            $('.needs-validation').on('submit', function(e) {
                if (!this.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                $(this).addClass('was-validated');
            });
            
            // Success/Error messages
            <?php if (isset($_SESSION['success'])): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Başarılı!',
                    text: '<?php echo $_SESSION['success']; unset($_SESSION['success']); ?>',
                    timer: 3000,
                    showConfirmButton: false
                });
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: '<?php echo $_SESSION['error']; unset($_SESSION['error']); ?>',
                    timer: 3000,
                    showConfirmButton: false
                });
            <?php endif; ?>
            
            // Delete confirmation
            $('.delete-btn').click(function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                
                Swal.fire({
                    title: 'Emin misiniz?',
                    text: "Bu işlem geri alınamaz!",
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
            
            // Auto-hide alerts
            setTimeout(function() {
                $('.alert').fadeOut();
            }, 5000);
        });
        
        // Dosya yükleme önizleme
        function previewImage(input, target) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $(target).attr('src', e.target.result).show();
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        // Chart.js varsayılan ayarları
        Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
        Chart.defaults.color = '#666';
    </script>
    
    <!-- DOBİEN Developer Signature -->
    <div style="position: fixed; bottom: 10px; right: 10px; z-index: 1000; background: rgba(0,0,0,0.8); color: white; padding: 5px 10px; border-radius: 5px; font-size: 12px;">
        <i class="fas fa-code"></i> DOBİEN
    </div>
</body>
</html>