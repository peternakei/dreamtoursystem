<script>
    jQuery(document).ready(function() {
        jQuery('#age,#currency,#type').select2({dropdownParent: $("#create-activity-price-modal")});
    });

    function getEditDetails(activity) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('activities.edit', ':id') }}";
        url = urlB.replace(':id', activity);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: activity
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('activities.update', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);

                    jQuery('#edit-activity-modal').modal('show');
                    jQuery('#activity_name').val(result.data.name);
                    jQuery('#activity_description').val(result.data.description);
                    jQuery('#hidden_input').val(result.html);

                    document.getElementById('editActivityForm').setAttribute("action", url);
                }
            }
        });
    }

    function getDeleteDetails(activity) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('activities.edit', ':id') }}";
        url = urlB.replace(':id', activity);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: activity
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('activities.destroy', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);

                    jQuery('#delete-activity-modal').modal('show');
                    jQuery('#delete_activity_name').val(result.data.name);
                    jQuery('#hidden_input').val(result.html);

                    document.getElementById('deleteActivityForm').setAttribute("action", url);
                }
            }
        });
    }
</script>
