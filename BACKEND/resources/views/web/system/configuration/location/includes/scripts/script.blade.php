<script>
    function getEditDetails(location) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('locations.edit', ':id') }}";
        url = urlB.replace(':id', location);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: location
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('locations.update', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);

                    jQuery('#edit-location-modal').modal('show');
                    jQuery('#location_name').val(result.data.name);
                    jQuery('#hidden_input').val(result.html);

                    document.getElementById('editLocationForm').setAttribute("action", url);
                }
            }
        });
    }

    function getDeleteDetails(location) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('locations.edit', ':id') }}";
        url = urlB.replace(':id', location);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: location
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('locations.destroy', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);

                    jQuery('#delete-location-modal').modal('show');
                    jQuery('#delete_location_name').val(result.data.name);
                    jQuery('#hidden_input').val(result.html);

                    document.getElementById('deleteLocationForm').setAttribute("action", url);
                }
            }
        });
    }
</script>
