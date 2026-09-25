// assets/js/main.js
// SMK Bangun Nusa Bangsa - Frontend Scripts

document.addEventListener('DOMContentLoaded', function() {
    // 1. Comment Form - Anonymous Toggle Logic
    const isAnonymousCheckbox = document.getElementById('is_anonymous');
    const nameInputGroup = document.getElementById('name_input_group');
    const emailInputGroup = document.getElementById('email_input_group');
    const nameInput = document.getElementById('comment_name');
    const emailInput = document.getElementById('comment_email');

    if (isAnonymousCheckbox) {
        function toggleAnonymousMode() {
            if (isAnonymousCheckbox.checked) {
                if (nameInputGroup) nameInputGroup.style.opacity = '0.4';
                if (emailInputGroup) emailInputGroup.style.opacity = '0.4';
                if (nameInput) {
                    nameInput.disabled = true;
                    nameInput.required = false;
                    nameInput.value = 'Anonim (Disembunyikan)';
                }
                if (emailInput) {
                    emailInput.disabled = true;
                    emailInput.required = false;
                    emailInput.value = '';
                }
            } else {
                if (nameInputGroup) nameInputGroup.style.opacity = '1';
                if (emailInputGroup) emailInputGroup.style.opacity = '1';
                if (nameInput) {
                    nameInput.disabled = false;
                    nameInput.required = true;
                    if (nameInput.value === 'Anonim (Disembunyikan)') {
                        nameInput.value = '';
                    }
                }
                if (emailInput) {
                    emailInput.disabled = false;
                    emailInput.required = true;
                }
            }
        }

        isAnonymousCheckbox.addEventListener('change', toggleAnonymousMode);
        // Initial run
        toggleAnonymousMode();
    }

    // 2. AJAX Comment Submission
    const commentForm = document.getElementById('commentForm');
    const commentAlertBox = document.getElementById('commentAlertBox');
    const commentListContainer = document.getElementById('commentsList');

    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = document.getElementById('btnSubmitComment');
            const originalBtnText = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim komentar...';
            }

            const formData = new FormData(commentForm);

            fetch('post-comment.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }

                if (data.status === 'success') {
                    if (commentAlertBox) {
                        commentAlertBox.innerHTML = `
                            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                                <div>${data.message}</div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                    }
                    
                    // Prepend new comment if instant approval or reload
                    if (data.comment_html && commentListContainer) {
                        const noCommentAlert = document.getElementById('noCommentAlert');
                        if (noCommentAlert) noCommentAlert.remove();
                        commentListContainer.insertAdjacentHTML('afterbegin', data.comment_html);
                    }

                    commentForm.reset();
                    if (isAnonymousCheckbox) {
                        isAnonymousCheckbox.checked = false;
                        if (nameInputGroup) nameInputGroup.style.opacity = '1';
                        if (emailInputGroup) emailInputGroup.style.opacity = '1';
                        if (nameInput) {
                            nameInput.disabled = false;
                            nameInput.value = '';
                        }
                        if (emailInput) {
                            emailInput.disabled = false;
                            emailInput.value = '';
                        }
                    }
                } else {
                    if (commentAlertBox) {
                        commentAlertBox.innerHTML = `
                            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                                <div>${data.message || 'Terjadi kesalahan. Silakan coba lagi.'}</div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                    }
                }
            })
            .catch(err => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
                if (commentAlertBox) {
                    commentAlertBox.innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            Gagal terhubung ke server. Silakan periksa koneksi Anda.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                }
            });
        });
    }

    // 3. Navbar scroll effect
    const navbar = document.querySelector('.navbar-custom');
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 40) {
                navbar.classList.add('scrolled', 'shadow-sm');
            } else {
                navbar.classList.remove('scrolled', 'shadow-sm');
            }
        });
    }
});
