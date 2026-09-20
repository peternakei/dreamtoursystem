<script>
    function getEditDetails(country) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('countries.edit', ':id') }}";
        url = urlB.replace(':id', country);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: country
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('countries.update', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);

                    jQuery('#edit-country-modal').modal('show');
                    jQuery('#country_name').val(result.data.name);
                    jQuery('#country_code').val(result.data.code);
                    jQuery('#hidden_input').val(result.html);

                    document.getElementById('editCountryForm').setAttribute("action", url);
                }
            }
        });
    }

    function getDeleteDetails(country) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('countries.edit', ':id') }}";
        url = urlB.replace(':id', country);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: country
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('countries.destroy', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);

                    jQuery('#delete-country-modal').modal('show');
                    jQuery('#delete_country_name').val(result.data.name);
                    jQuery('#hidden_input').val(result.html);

                    document.getElementById('deleteCountryForm').setAttribute("action", url);
                }
            }
        });
    }
</script>
