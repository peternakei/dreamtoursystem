<script>
    function getEditDetails(user) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('users.edit', ':id') }}";
        url = urlB.replace(':id', user);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: user
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('users.update', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);console.log(url)

                    jQuery('#edit-user-modal').modal('show');
                    jQuery('#user_name').val(result.data.name);
                    jQuery('#user_phone').val(result.data.phone);
                    jQuery('#user_email').val(result.data.email);
                    jQuery('#hidden_input').val(result.html);

                    document.getElementById('editUserForm').setAttribute("action", url);
                }
            }
        });
    }

    function getDeleteDetails(user) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('users.edit', ':id') }}";
        url = urlB.replace(':id', user);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: user
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('users.destroy', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);

                    jQuery('#delete-user-modal').modal('show');
                    jQuery('#delete_user_name').val(result.data.name);
                    jQuery('#hidden_input').val(result.html);
                    document.getElementById('deleteUserForm').setAttribute("action", url);
                }
            }
        });
    }

    function revokeUserRole(role, id) {
        jQuery('#revoke-user-role-modal').modal('show');
        jQuery('#role_id').val(id);
        jQuery('#role_name').html(role);
    }
</script>
