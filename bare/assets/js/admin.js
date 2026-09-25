// assets/js/admin.js
// SMK Bangun Nusa Bangsa - Admin Scripts

document.addEventListener('DOMContentLoaded', function() {
    // 1. Sidebar Toggle Mobile
    const toggleSidebarBtn = document.getElementById('toggleSidebar');
    const adminSidebar = document.querySelector('.admin-sidebar');
    if (toggleSidebarBtn && adminSidebar) {
        toggleSidebarBtn.addEventListener('click', function() {
            adminSidebar.classList.toggle('show');
        });
    }

    // 2. Auto Slug Generator on Article Add/Edit
    const titleInput = document.getElementById('article_title');
    const slugInput = document.getElementById('article_slug');
    if (titleInput && slugInput && !slugInput.readOnly) {
        titleInput.addEventListener('input', function() {
            if (slugInput.dataset.manual !== 'true') {
                let slug = titleInput.value.toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/[\s-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                slugInput.value = slug;
            }
        });

        slugInput.addEventListener('input', function() {
            slugInput.dataset.manual = 'true';
        });
    }

    // 3. Image File Upload Preview
    const thumbnailInput = document.getElementById('thumbnail_input');
    const thumbnailPreview = document.getElementById('thumbnail_preview');
    if (thumbnailInput && thumbnailPreview) {
        thumbnailInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    thumbnailPreview.src = e.target.result;
                    thumbnailPreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // 4. Delete Confirmation
    const deleteButtons = document.querySelectorAll('.btn-confirm-delete');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            const itemName = this.dataset.name || 'item ini';
            if (!confirm(`Apakah Anda yakin ingin menghapus ${itemName}? Tindakan ini tidak dapat dibatalkan.`)) {
                e.preventDefault();
            }
        });
    });
});
