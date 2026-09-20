<script>
    function getEditDetails(bank) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('banks.edit', ':id') }}";
        url = urlB.replace(':id', bank);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: bank
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('banks.update', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);

                    jQuery('#edit-bank-modal').modal('show');
                    jQuery('#bank_name').val(result.data.name);
                    jQuery('#hidden_input').val(result.html);

                    document.getElementById('editBankForm').setAttribute("action", url);
                }
            }
        });
    }

    function getDeleteDetails(bank) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('banks.edit', ':id') }}";
        url = urlB.replace(':id', bank);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: bank
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('banks.destroy', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);

                    jQuery('#delete-bank-modal').modal('show');
                    jQuery('#delete_bank_name').val(result.data.name);
                    jQuery('#hidden_input').val(result.html);

                    document.getElementById('deleteBankForm').setAttribute("action", url);
                }
            }
        });
    }
</script>
