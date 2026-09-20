<script>
    function getEditDetails(role) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('roles.edit', ':id') }}";
        url = urlB.replace(':id', role);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: role
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('roles.update', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);
                    console.log(url)

                    jQuery('#edit-role-modal').modal('show');
                    jQuery('#role_name').val(result.data.name);
                    jQuery('#hidden_input').val(result.html);

                    //build expenses report summary
                    var rolePermissionsData = result.permissions
                    let i = 0;

                    var selectedPermissions = result.selectedPermissions;
                    jQuery.each(rolePermissionsData, function(key,
                        value) {
                        i++;
                        var perm = value.id;
                        let isChecked = $.inArray(perm, selectedPermissions) !== -1 ?
                            'checked' : '';
                        jQuery('#rolePermissionSection').append('<tr id="row' + i +
                            '"><td>' + value.name +
                            '</td><td><div class="form-check form-checkbox-info text-center"><input type="checkbox" class="form-check-input" id="permission_' +
                            i + '" name="permission[]" value="' + value.id +
                            '" ' + isChecked + '></div></td></tr>'
                        );
                    });

                    document.getElementById('editRoleForm').setAttribute("action", url);
                }
            }
        });
    }

    function getDeleteDetails(role) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('roles.edit', ':id') }}";
        url = urlB.replace(':id', role);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: role
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('roles.destroy', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);

                    jQuery('#delete-role-modal').modal('show');
                    jQuery('#delete_role_name').val(result.data.name);
                    jQuery('#hidden_input').val(result.html);
                    document.getElementById('deleteRoleForm').setAttribute("action", url);
                }
            }
        });
    }
</script>
