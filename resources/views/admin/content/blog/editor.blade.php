@push('admin-styles')
    <style>
        .blog-editor-page .card { border-radius: 6px; }
        .blog-editor-page .form-control,
        .blog-editor-page .form-select,
        .blog-editor-page .input-group-text { border-radius: 4px; }
        .blog-editor-page .tox-tinymce { border: 1px solid #d8dee8; border-radius: 4px; }
        .blog-sidebar { position: sticky; top: 84px; }
        .blog-image-preview { aspect-ratio: 16 / 9; overflow: hidden; border: 1px solid #e4e8ee; border-radius: 4px; background: #f5f7fa; }
        .blog-image-preview img { width: 100%; height: 100%; object-fit: cover; display: block; }
        @media (max-width: 1199.98px) { .blog-sidebar { position: static; } }
    </style>
@endpush

@push('admin-scripts')
    <script src="{{ asset('admin/assets/libs/tinymce/tinymce.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var title = document.getElementById('blog-title');
            var slug = document.getElementById('blog-slug');
            var excerpt = document.getElementById('blog-excerpt');
            var excerptCount = document.getElementById('excerptCount');
            var form = document.getElementById('blogForm');
            var saveButton = document.getElementById('savePostButton');
            var slugEdited = slug.value.trim() !== '';

            function slugify(value) {
                return value.toLowerCase().trim()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/[\s-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }

            function updateExcerptCount() {
                excerptCount.textContent = excerpt.value.length + ' / 500';
            }

            title.addEventListener('input', function () {
                if (!slugEdited) slug.value = slugify(title.value);
            });
            slug.addEventListener('input', function () {
                slugEdited = slug.value.trim() !== '';
            });
            excerpt.addEventListener('input', updateExcerptCount);
            updateExcerptCount();

            if (typeof tinymce !== 'undefined') {
                tinymce.init({
                    selector: '#blog-content',
                    license_key: 'gpl',
                    height: 560,
                    menubar: 'edit view insert format tools table help',
                    branding: false,
                    promotion: false,
                    plugins: 'advlist autolink lists link image media table charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime codesample wordcount autoresize',
                    toolbar: 'undo redo | blocks | bold italic underline | forecolor | alignleft aligncenter alignright | bullist numlist | blockquote link image media table | codesample | removeformat preview fullscreen code',
                    toolbar_mode: 'sliding',
                    content_style: 'body { font-family: Arial, sans-serif; font-size: 16px; line-height: 1.7; color: #2f3542; padding: 16px; } h2, h3 { color: #1f2937; } img { max-width: 100%; height: auto; }',
                    browser_spellcheck: true,
                    contextmenu: false,
                    autoresize_bottom_margin: 24,
                    setup: function (editor) {
                        editor.on('change input undo redo', function () {
                            editor.save();
                        });
                    }
                });
            }

            document.getElementById('featuredImageInput').addEventListener('change', function () {
                var file = this.files[0];
                if (!file) return;

                document.getElementById('previewImg').src = URL.createObjectURL(file);
                document.getElementById('imagePreview').classList.remove('d-none');
            });

            var originalButtonHtml = saveButton.innerHTML;

            function resetSubmitButton() {
                form.dataset.submitting = '';
                saveButton.disabled = false;
                saveButton.innerHTML = originalButtonHtml;
            }

            function showError(message) {
                if (typeof toastr !== 'undefined') {
                    toastr.error(message);
                } else {
                    window.alert(message);
                }
            }

            form.addEventListener('submit', function (event) {
                event.preventDefault();
                if (form.dataset.submitting === '1') return;

                var imageInput = document.getElementById('featuredImageInput');
                var image = imageInput.files[0];
                var allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'avif'];

                if (image) {
                    var extension = image.name.split('.').pop().toLowerCase();
                    if (!allowedExtensions.includes(extension) || image.size > 5 * 1024 * 1024) {
                        showError('Featured image must be JPG, PNG, WebP, or AVIF and no larger than 5 MB.');
                        return;
                    }
                }

                if (typeof tinymce !== 'undefined') tinymce.triggerSave();

                form.dataset.submitting = '1';
                saveButton.disabled = true;
                saveButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';

                var request = new XMLHttpRequest();
                request.open('POST', form.action, true);
                request.timeout = 90000;
                request.setRequestHeader('Accept', 'application/json');
                request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                request.upload.addEventListener('progress', function (progressEvent) {
                    if (!progressEvent.lengthComputable || !image) return;
                    var percent = Math.round((progressEvent.loaded / progressEvent.total) * 100);
                    saveButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Uploading ' + percent + '%';
                });

                request.addEventListener('load', function () {
                    var payload = {};
                    try {
                        payload = JSON.parse(request.responseText || '{}');
                    } catch (error) {
                        payload = {};
                    }

                    if (request.status >= 200 && request.status < 300 && payload.redirect) {
                        window.location.assign(payload.redirect);
                        return;
                    }

                    if (request.status === 422 && payload.errors) {
                        var messages = Object.values(payload.errors).flat();
                        showError(messages[0] || 'Please check form fields.');
                    } else {
                        showError(payload.message || 'Blog post could not be saved. Please try again.');
                    }
                    resetSubmitButton();
                });

                request.addEventListener('error', function () {
                    showError('Network error. Blog post was not saved.');
                    resetSubmitButton();
                });

                request.addEventListener('timeout', function () {
                    showError('Upload timed out. Use a smaller image or check server upload limits.');
                    resetSubmitButton();
                });

                request.send(new FormData(form));
            });
        });
    </script>
@endpush
