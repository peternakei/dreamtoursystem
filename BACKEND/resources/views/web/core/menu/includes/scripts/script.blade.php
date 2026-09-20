<script>
    function getEditDetails(menu) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('menus.edit', ':id') }}";
        url = urlB.replace(':id', menu);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: menu
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('menus.update', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);console.log(url)

                    jQuery('#edit-menu-modal').modal('show');
                    jQuery('#menu_name').val(result.data.name);
                    jQuery('#menu_title').val(result.data.title);
                    jQuery('#menu_icon').val(result.data.icon);
                    jQuery('#menu_url').val(result.data.url);
                    jQuery('#menu_ordering').val(result.data.ordering);
                    jQuery('#hidden_input').val(result.html);
                    document.getElementById('editMenuForm').setAttribute("action", url);
                }
            }
        });
    }

    function getDeleteDetails(menu) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('menus.edit', ':id') }}";
        url = urlB.replace(':id', menu);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: menu
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('menus.destroy', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);

                    jQuery('#delete-menu-modal').modal('show');
                    jQuery('#delete_menu_name').val(result.data.name);
                    jQuery('#hidden_input').val(result.html);
                    document.getElementById('deleteMenuForm').setAttribute("action", url);
                }
            }
        });
    }
</script>
