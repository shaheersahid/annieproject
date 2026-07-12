$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let formTouched = false;
    $("#ajaxForm, .ajaxForm").on("submit", function (e) {
        e.preventDefault();
        if (formTouched === false) {
            const formData = new FormData(this);
            formTouched = true;
            $.ajax({
                url: $(this).attr('action'),
                type: "post",
                dataType: "json",
                success: function success(e) {
                    formTouched = true;

                    if (e.success && e.redirect) {
                        toastr.success(e.message || 'Product saved successfully!');
                        setTimeout(function () {
                            window.location.href = e.redirect;
                        }, 1500);
                    } else if (e.showTastrMsg) {
                        toastr.success(e.message);
                        setTimeout(function () {
                            window.location.reload(true);
                        }, 1500);
                    } else {
                        window.location.reload(true);
                    }
                },
                error: function error(e) {
                    var resp = $.parseJSON(e.responseText);
                    formTouched = false;
                    if (e.status === 422) {
                        var errors = resp.errors;
                        $.each(errors, function (x, y) {
                            if (y != null && y !== "") {
                                toastr.error(y);
                            }
                        });
                    } else if (resp.message) {
                        toastr.error(resp.message);
                    } else {
                        toastr.error("Something went wrong. Please try again.");
                    }
                },
                // Form data
                data: formData,
                processData: false,
                cache: false,
                contentType: false,
            });
        }
    });

    // generate slug
    $("#product_name, #category_name").on("input", function () {
        const title = $(this).val();
        const slug = title.toLowerCase().replace(/[^a-z0-9]+/g, "-").replace(/^-|-$/g, "");
        const isProductInp = $(this).attr("id") === "product_name";
        $(isProductInp ? "#product_slug" : "#category_slug").val(slug);

        if (!isProductInp) $("#category_title").val(title);
    });
})


