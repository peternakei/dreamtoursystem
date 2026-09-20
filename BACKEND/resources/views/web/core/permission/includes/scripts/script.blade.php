<script>
    function getEditDetails(permission) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('permissions.edit', ':id') }}";
        url = urlB.replace(':id', permission);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: permission
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('permissions.update', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);
                    console.log(url)

                    jQuery('#edit-permission-modal').modal('show');
                    jQuery('#permission_name').val(result.data.name);
                    jQuery('#hidden_input').val(result.html);

                    //build expenses report summary
                    var permissionRoleData = result.roles
                    let i = 0;

                    var selectedRoles = result.selectedRoles;
                    jQuery.each(permissionRoleData, function(key,
                        value) {
                        i++;
                        var rol = value.id;
                        let isChecked = $.inArray(rol, selectedRoles) !== -1 ?
                            'checked' : '';
                        jQuery('#permissionRoleSection').append('<tr id="row' + i +
                            '"><td>' + value.name +
                            '</td><td><div class="form-check form-checkbox-info text-center"><input type="checkbox" class="form-check-input" id="role_' +
                            i + '" name="role[]" value="' + value.id +
                            '" ' + isChecked + '></div></td></tr>'
                        );
                    });

                    document.getElementById('editPermissionForm').setAttribute("action", url);
                }
            }
        });
    }

    function getDeleteDetails(permission) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('permissions.edit', ':id') }}";
        url = urlB.replace(':id', permission);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: permission
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('permissions.destroy', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);

                    jQuery('#delete-permission-modal').modal('show');
                    jQuery('#delete_permission_name').val(result.data.name);
                    jQuery('#hidden_input').val(result.html);
                    document.getElementById('deletePermissionForm').setAttribute("action", url);
                }
            }
        });
    }
</script>
