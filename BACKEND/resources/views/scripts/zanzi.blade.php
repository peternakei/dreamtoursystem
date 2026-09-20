<script>
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

        if (jQuery('#snow-editor').length > 0) {
            var quillEditor = document.querySelector('#snow-editor');
            var html = quillEditor.children[0].innerHTML;

            jQuery('#trip_description').val(html);
            formData.append('trip_description', jQuery('#trip_description').val());
        }else{
             if (jQuery('#edit-trip-description-editor').length > 0) {
            var quillEditor = document.querySelector('#edit-trip-description-editor');
            var html = quillEditor.children[0].innerHTML;

            jQuery('#trip_description_edit').val(html);
            formData.append('trip_description', jQuery('#trip_description_edit').val());
        }
        }

       

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
                    setInterval(() => {
                        jQuery('.saveBtn').css("display", "none");
                        jQuery('.btnLoading').css("display", "block");
                        window.location.href = '/' + result.redirect;
                    }, 2000);

                } else {
                    toastr.error(result.message);
                    setInterval(() => {
                        jQuery('.saveBtn').css("display", "block");
                        jQuery('.btnLoading').css("display", "none");
                        // window.location.href = '/' + result.redirect;
                    }, 2000);

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

    function submitCreateFormUpload(formName) {

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

        if (jQuery('#snow-editor').length > 0) {
            var quillEditor = document.querySelector('#snow-editor');
            var html = quillEditor.children[0].innerHTML;

            jQuery('#ticket_description').val(html);
            formData.append('ticket_description', jQuery('#ticket_description').val());
        }

        $.ajax({
            url: jQuery('form[name="' + formName + '"]').attr("action"),
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            cache: false,
            success: function(result) {
                if (result.status === true) {
                    setInterval(() => {
                        toastr.success(result.message);
                        jQuery('.saveBtn').css("display", "block");
                        jQuery('.btnLoading').css("display", "none");
                        window.location.href = '/' + result.redirect;
                    }, 2000);

                } else {
                    toastr.error(result.message);
                    jQuery('.saveBtn').css("display", "block");
                    jQuery('.btnLoading').css("display", "none");
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

    jQuery(document).ready(function() {
        //Control next and back buttons
        jQuery('#nextBtn').click(function(e) {
            jQuery('.section_one').css('display', 'none');
            jQuery('.section_two').css('display', 'block');
        });

        //Control next and back buttons
        jQuery('#backBtn').click(function(e) {
            jQuery('.section_one').css('display', 'block');
            jQuery('.section_two').css('display', 'none');
        });

        //Get the selected permission type
        jQuery('#permission_type').on('change', function() {
            let permissionType = jQuery('#permission_type').val();
            if (permissionType == 2) {
                //show menu form
                jQuery('#menu_section').css('display', 'block');
            } else {
                //hide menu form
                jQuery('#menu_section').css('display', 'none');
            }
        });

        jQuery('.modal').on('hidden.bs.modal', function() {
            location.reload();
        });
    });
</script>
