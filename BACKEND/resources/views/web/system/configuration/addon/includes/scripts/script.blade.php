<script>
    function getEditDetails(addon) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('addons.edit', ':id') }}";
        url = urlB.replace(':id', addon);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: addon
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('addons.update', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);

                    jQuery('#edit-addon-modal').modal('show');
                    jQuery('#addon_name').val(result.data.name);
                    jQuery('#hidden_input').val(result.html);

                    let selectedType = result.data.is_include;
                    let dataArr = result.isIncludes;
                    if (dataArr.length) {
                        $.each(result.isIncludes, function(index, value) {

                            let type = value.name;
                            let typeID = value.id;

                            if (jQuery('#addon_is_include').find("option[value='" + value
                                    .id + "']")
                                .length) {

                            } else {
                                jQuery('#addon_is_include').append(
                                    new Option(type, typeID)
                                );

                                jQuery('#addon_is_include').find("option[value='" + selectedType +
                                    "']").prop("selected", true)
                            }
                        });
                    } else {
                        jQuery('#addon_is_include').find('option')
                            .remove()
                            .end().append(new Option('No type', ''));
                    }

                    document.getElementById('editAddonForm').setAttribute("action", url);
                }
            }
        });
    }

    function getDeleteDetails(addon) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('addons.edit', ':id') }}";
        url = urlB.replace(':id', addon);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: addon
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('addons.destroy', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);

                    jQuery('#delete-addon-modal').modal('show');
                    jQuery('#delete_addon_name').val(result.data.name);
                    jQuery('#hidden_input').val(result.html);

                    document.getElementById('deleteAddonForm').setAttribute("action", url);
                }
            }
        });
    }
</script>
