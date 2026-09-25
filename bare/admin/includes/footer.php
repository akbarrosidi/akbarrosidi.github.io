<?php
// admin/includes/footer.php
// SMK Bangun Nusa Bangsa - Admin Footer
?>
        </div> <!-- End .admin-content -->
    </div> <!-- End .admin-main -->
</div> <!-- End .admin-wrapper -->

    <!-- jQuery & Bootstrap 5 Bundle (Required for Summernote) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Summernote WYSIWYG JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <!-- Admin JS -->
    <script src="<?= SITE_URL ?>/assets/js/admin.js"></script>
    <script>
        $(document).ready(function() {
            if ($('#summernote_editor').length) {
                $('#summernote_editor').summernote({
                    placeholder: 'Tuliskan isi konten artikel secara lengkap di sini...',
                    tabsize: 2,
                    height: 350,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'underline', 'clear']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture', 'video']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ]
                });
            }
        });
    </script>
</body>
</html>
