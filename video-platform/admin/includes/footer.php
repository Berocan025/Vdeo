        </div> <!-- main-content kapatma -->
    </div> <!-- admin-layout kapatma -->

    <!-- Modal Container -->
    <div id="modalContainer"></div>

    <!-- JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <!-- Admin JavaScript -->
    <script>
        // Sidebar toggle için mobil
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('open');
        }

        // Modal fonksiyonları
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }

        // Tablo sıralama
        function sortTable(columnIndex, tableId) {
            const table = document.getElementById(tableId);
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            const isNumeric = !isNaN(parseFloat(rows[0].cells[columnIndex].textContent));
            
            rows.sort((a, b) => {
                const aVal = a.cells[columnIndex].textContent.trim();
                const bVal = b.cells[columnIndex].textContent.trim();
                
                if (isNumeric) {
                    return parseFloat(aVal) - parseFloat(bVal);
                } else {
                    return aVal.localeCompare(bVal, 'tr');
                }
            });
            
            rows.forEach(row => tbody.appendChild(row));
        }

        // Toplu işlemler
        function toggleAllCheckboxes(masterCheckbox) {
            const checkboxes = document.querySelectorAll('input[name="selected_items[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = masterCheckbox.checked;
            });
        }

        // Silme onayı
        function confirmDelete(message = 'Bu işlemi gerçekleştirmek istediğinizden emin misiniz?') {
            return confirm(message);
        }

        // AJAX işlemleri için yardımcı fonksiyon
        function ajaxRequest(url, data, callback) {
            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(response) {
                    if (callback) callback(response);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert('Bir hata oluştu. Lütfen tekrar deneyin.');
                }
            });
        }

        // Sayfa yüklendiğinde
        $(document).ready(function() {
            // Auto-hide alerts
            setTimeout(function() {
                $('.alert').fadeOut();
            }, 5000);

            // DataTable benzeri özellikler
            $('.data-table').each(function() {
                const table = $(this);
                
                // Arama kutusu varsa
                const searchInput = table.siblings('.table-search').find('input');
                if (searchInput.length) {
                    searchInput.on('keyup', function() {
                        const value = $(this).val().toLowerCase();
                        table.find('tbody tr').filter(function() {
                            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                        });
                    });
                }
            });
        });

        // Gerçek zamanlı validasyon
        function validateForm(formId) {
            const form = document.getElementById(formId);
            const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
            let isValid = true;

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.style.borderColor = '#ef4444';
                    isValid = false;
                } else {
                    input.style.borderColor = '#22c55e';
                }
            });

            return isValid;
        }

        // Dosya yükleme önizlemesi
        function previewImage(input, previewId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById(previewId);
                    if (preview) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

    <!-- Özel sayfa JavaScript'leri -->
    <?php if (isset($page_scripts)): ?>
        <?php foreach ($page_scripts as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Inline JavaScript -->
    <?php if (isset($inline_js)): ?>
        <script>
            <?php echo $inline_js; ?>
        </script>
    <?php endif; ?>

</body>
</html>