<script type="text/javascript">
    function submitCreateLoginForm(formName) {

        jQuery('.saveBtn').css("display", "none");
        jQuery('.btnLoading').css("display", "block");

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        let formId = ':id';
        let formID = formId.replace(':id', formName);
        let form = document.getElementById(formID);

        let formData = new FormData(form);

        $.ajax({
            url: jQuery('form[name="' + formName + '"]').attr("action"),
            method: 'POST',
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            success: function(result) {
                if (result.status === true) {
                    toastr.success(result.message);
                    jQuery('.saveBtn').css("display", "none");
                    jQuery('.btnLoading').css("display", "block");
                    setInterval(() => {
                        if (result.redirect) {
                            window.location.href = '/' + result.redirect;
                        } else {
                            window.location.href = "{{ route('dashboard.user') }}";
                        }
                    }, 3500);

                } else {
                    toastr.error(result.message);
                    $('#error_password').css('display', 'block')
                    $('#password_message').html('Incorrect password')
                    $('#error_username').css('display', 'block')
                    $('#username_message').html('Incorrect username')
                    jQuery('.saveBtn').css("display", "block");
                    jQuery('.btnLoading').css("display", "none");
                    setInterval(() => {

                        window.location.reload();
                    }, 3500);

                }
            },
            error: function(error) {
                let errors = error.responseJSON.errors;
                if (jQuery.isEmptyObject(errors) == false) {
                    jQuery.each(errors, function(key, value) {
                        jQuery('#error_' + key).html(value);
                    });
                }
                jQuery('.saveBtn').css("display", "block");
                jQuery('.btnLoading').css("display", "none");
            }
        });
    }

    function submitCreateForm(formName) {

        jQuery('.saveBtn').css("display", "none");
        jQuery('.btnLoading').css("display", "block");

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        let formId = ':id';
        let formID = formId.replace(':id', formName);
        let form = document.getElementById(formID);

        let formData = new FormData(form);

        $.ajax({
            url: jQuery('form[name="' + formName + '"]').attr("action"),
            method: 'POST',
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            success: function(result) {
                if (result.status === true) {
                    setInterval(() => {
                        toastr.success(result.message);
                        jQuery('.saveBtn').css("display", "none");
                        jQuery('.btnLoading').css("display", "block");
                        if (result.redirect) {
                            window.location.href = '/' + result.redirect;
                        } else {
                            location.reload();
                        }
                    }, 3500);

                } else {
                    toastr.error(result.message);
                    jQuery('.saveBtn').css("display", "block");
                    jQuery('.btnLoading').css("display", "none");
                    location.reload();
                }
            },
            error: function(error) {
                let errors = error.responseJSON.errors;
                if (jQuery.isEmptyObject(errors) == false) {
                    jQuery.each(errors, function(key, value) {
                        jQuery('#error_' + key).html(value);
                    });
                }
                jQuery('.saveBtn').css("display", "block");
                jQuery('.btnLoading').css("display", "none");
            }
        });
    }
</script>
