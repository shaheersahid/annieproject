const ProductForm = (function () {
    let config = {
        specIndex: 0,
        currentStep: 1,
        totalSteps: 3,
        maxImages: 9,
        maxImageBytes: 5 * 1024 * 1024,
        maxVideoBytes: 30 * 1024 * 1024,
        maxUploadBytes: 32 * 1024 * 1024
    };

    function init(userConfig) {
        config = { ...config, ...userConfig };
        bindEvents();
        initEditors();
        initDatePickers();

        // Restore step from hidden field (populated by old() on validation redirect)
        const savedStep = parseInt($('#current_step').val()) || 1;
        config.currentStep = savedStep;
        updateStep(config.currentStep);

        updateGalleryCount();
    }

    function initDatePickers() {
        if (typeof flatpickr !== 'undefined') {
            flatpickr(".datetime-picker", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                time_24hr: true,
                allowInput: true
            });
        }
    }

    function initEditors() {
        if (typeof tinymce !== 'undefined') {
            tinymce.init({
                selector: '#description, #short_description',
                min_height: 250,
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount', 'autoresize'
                ],
                toolbar: 'undo redo | blocks | ' +
                    'bold italic backcolor | alignleft aligncenter ' +
                    'alignright alignjustify | bullist numlist outdent indent | ' +
                    'removeformat | help',
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
                autoresize_bottom_margin: 20,
                setup: function (editor) {
                    editor.on('change', function () {
                        editor.save();
                    });
                }
            });
        }
    }

    function bindEvents() {
        // Add specification row
        $(document).on('click', '#add-spec', function () {
            const template = $('#spec-template').html();
            if (!template) return;

            const html = template.replace(/__INDEX__/g, config.specIndex);
            $('#specs-container').append(html);
            config.specIndex++;
        });

        // Remove specification row
        $(document).on('click', '.remove-spec', function () {
            $(this).closest('.spec-row').remove();
        });

        // --- Multi-step Navigation ---
        $('#next-btn').on('click', function () {
            if (validateStep(config.currentStep)) {
                config.currentStep++;
                updateStep(config.currentStep);
            }
        });

        $('#prev-btn').on('click', function () {
            config.currentStep--;
            updateStep(config.currentStep);
        });

        $('#draft-btn').on('click', function (e) {
            e.preventDefault();
            submitProduct(true);
        });

        $('#submit-btn').on('click', function (e) {
            e.preventDefault();
            submitProduct(false);
        });

        $('#video').on('change', function () {
            if (!validateMedia()) {
                $(this).val('');
            }
        });

        $('.step-item').on('click keydown', function (event) {
            if (event.type === 'keydown' && event.key !== 'Enter' && event.key !== ' ') {
                return;
            }

            event.preventDefault();
            const targetStep = Number($(this).data('step'));

            if (targetStep === config.currentStep) {
                return;
            }

            if (targetStep > config.currentStep && !validateStep(config.currentStep)) {
                return;
            }

            config.currentStep = targetStep;
            updateStep(config.currentStep);
        });
    }

    function submitProduct(isDraft) {
        if (!validateMedia()) return;

        if (typeof tinymce !== 'undefined') {
            tinymce.triggerSave();
        }

        $('#is_draft').val(isDraft ? 1 : 0);
        $('#current_step').val(config.currentStep);
        $('#draft-btn, #submit-btn').prop('disabled', true);
        $('#product-form')[0].submit();
    }

    function updateStep(step) {
        // Sync hidden field so the step survives form redirects
        $('#current_step').val(step);

        $('.form-step').removeClass('active');
        $(`#step-${step}`).addClass('active');

        $('.step-item').removeClass('active completed');
        $('.step-item').each(function () {
            const stepNum = $(this).data('step');
            if (stepNum === step) {
                $(this).addClass('active');
            } else if (stepNum < step) {
                $(this).addClass('completed');
            }
        });

        if (step === 1) {
            $('#prev-btn').hide();
        } else {
            $('#prev-btn').show();
        }

        if (step === config.totalSteps) {
            $('#next-btn').hide();
            $('#draft-btn').show();
            $('#submit-btn').show();
        } else {
            $('#next-btn').show();
            $('#draft-btn').hide();
            $('#submit-btn').hide();
        }

        if ($(".step-indicator").length) {
            $('html, body').animate({
                scrollTop: $(".step-indicator").offset().top - 100
            }, 200);
        }
    }

    function validateStep(step) {
        if (typeof tinymce !== 'undefined') {
            tinymce.triggerSave();
        }
        let isValid = true;
        const $currentStepEl = $(`#step-${step}`);

        $currentStepEl.find('.is-invalid').removeClass('is-invalid');
        $currentStepEl.find('.invalid-feedback').remove();

        if (step === 1) {
            const name = ($('#name').val() || '').trim();
            if (!name) {
                $('#name').addClass('is-invalid');
                $('#name').after('<div class="invalid-feedback">Product title is required.</div>');
                isValid = false;
            }

            const $catSelect = $('#category_ids');
            if ($catSelect.length) {
                const valArr = $catSelect.val();
                const hasVal = Array.isArray(valArr) && valArr.length > 0;
                const hasOpt = $catSelect.find('option:selected').length > 0;
                const hasUi = $catSelect.next('.select2-container').find('.select2-selection__choice').length > 0;
                if (!hasVal && !hasOpt && !hasUi) {
                    $catSelect.addClass('is-invalid');
                    $catSelect.next('.select2-container').after('<div class="invalid-feedback d-block">Please select at least one category.</div>');
                    isValid = false;
                }
            }
        }

        if (!isValid) {
            if (typeof toastr !== 'undefined') {
                toastr.error('Please fill in all required fields.');
            }
        }

        return isValid;
    }

    // --- Image Upload & Preview Functions ---
    function handleThumbnailSelect(input) {
        if (input.files && input.files[0]) {
            if (!isValidImage(input.files[0]) || input.files[0].size > config.maxImageBytes) {
                showMediaError('Main image must be JPG, PNG, WebP, or AVIF and no larger than 5 MB.');
                $(input).val('');
                removeThumbnail();
                return;
            }

            if (!validateMedia()) {
                $(input).val('');
                removeThumbnail();
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                $('#thumbnail-preview').attr('src', e.target.result);
                $('#thumbnail-preview-container').show();
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeThumbnail() {
        $('#thumbnail').val('');
        $('#thumbnail-preview').attr('src', '');
        $('#thumbnail-preview-container').hide();
    }

    function handleGallerySelect(input) {
        if (input.files && input.files.length > 0) {
            const container = $('#gallery-preview');
            // Remove any newly added preview cards before adding new ones
            container.find('.new-gallery-image').remove();

            const existingCount = container.find('.existing-image-card').length;
            const newCount = input.files.length;
            const totalCount = existingCount + newCount;

            const invalidFile = Array.from(input.files).find(file =>
                !isValidImage(file) || file.size > config.maxImageBytes
            );

            if (invalidFile) {
                showMediaError('Gallery images must be JPG, PNG, WebP, or AVIF and no larger than 5 MB each.');
                $(input).val('');
                updateGalleryCount();
                return;
            }

            if (totalCount > config.maxImages) {
                const maxAllowed = config.maxImages - existingCount;
                if (typeof toastr !== 'undefined') {
                    toastr.warning(`You can select a maximum of ${maxAllowed} more image(s). Total limit: ${config.maxImages}`);
                }
                $(input).val('');
                updateGalleryCount();
                return;
            }

            if (!validateMedia()) {
                $(input).val('');
                updateGalleryCount();
                return;
            }

            Array.from(input.files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const html = `
                        <div class="col-md-3 col-6 mb-3 new-gallery-image">
                            <div class="card h-100 border position-relative">
                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1" onclick="ProductForm.removeSelectedGalleryImage(this, ${index})">
                                    <i class="fa fa-trash"></i>
                                </button>
                                <img src="${e.target.result}" class="card-img-top" alt="Product Image" style="height: 100px; object-fit: cover;">
                            </div>
                        </div>
                    `;
                    container.append(html);
                }
                reader.readAsDataURL(file);
            });

            updateGalleryCount();
        }
    }

    function removeSelectedGalleryImage(btn, index) {
        const input = document.getElementById('images');
        const transfer = new DataTransfer();

        Array.from(input.files).forEach((file, fileIndex) => {
            if (fileIndex !== index) transfer.items.add(file);
        });

        input.files = transfer.files;
        $(btn).closest('.new-gallery-image').remove();
        handleGallerySelect(input);
    }

    function removeExistingImage(path, btn) {
        $('#deleted-images-container').append(`<input type="hidden" name="deleted_images[]" value="${path}">`);
        $(btn).closest('.existing-image-card').remove();
        updateGalleryCount();
    }

    function updateGalleryCount() {
        const total = $('#gallery-preview .existing-image-card, #gallery-preview .new-gallery-image').length;
        $('#gallery-count').text(`${total}/${config.maxImages}`);
    }

    function isValidImage(file) {
        const extension = file.name.split('.').pop().toLowerCase();
        return ['jpg', 'jpeg', 'png', 'webp', 'avif'].includes(extension);
    }

    function validateMedia() {
        const thumbnail = document.getElementById('thumbnail')?.files?.[0];
        const images = Array.from(document.getElementById('images')?.files || []);
        const video = document.getElementById('video')?.files?.[0];

        if (thumbnail && (!isValidImage(thumbnail) || thumbnail.size > config.maxImageBytes)) {
            showMediaError('Main image must be JPG, PNG, WebP, or AVIF and no larger than 5 MB.');
            return false;
        }

        if (images.some(file => !isValidImage(file) || file.size > config.maxImageBytes)) {
            showMediaError('Gallery images must be JPG, PNG, WebP, or AVIF and no larger than 5 MB each.');
            return false;
        }

        if (video && video.size > config.maxVideoBytes) {
            showMediaError('Video must not exceed 30 MB.');
            return false;
        }

        const totalBytes = (thumbnail?.size || 0)
            + images.reduce((total, file) => total + file.size, 0)
            + (video?.size || 0);

        if (totalBytes > config.maxUploadBytes) {
            showMediaError('Combined media upload must not exceed 32 MB.');
            return false;
        }

        return true;
    }

    function showMediaError(message) {
        if (typeof toastr !== 'undefined') {
            toastr.error(message);
        } else {
            window.alert(message);
        }
    }

    return {
        init: init,
        handleThumbnailSelect: handleThumbnailSelect,
        removeThumbnail: removeThumbnail,
        handleGallerySelect: handleGallerySelect,
        removeSelectedGalleryImage: removeSelectedGalleryImage,
        removeExistingImage: removeExistingImage
    };
})();
