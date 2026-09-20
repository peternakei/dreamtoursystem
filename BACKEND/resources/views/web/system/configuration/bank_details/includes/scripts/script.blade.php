<script>
    jQuery(document).ready(function() {
        jQuery('#bank,#currency').select2({dropdownParent: $("#create-bank-account-details-modal")});
    });

    function getEditDetails(bank) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('bank_details.edit', ':id') }}";
        url = urlB.replace(':id', bank);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: bank
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('bank_details.update', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);

                    jQuery('#edit-bank-details-modal').modal('show');
                    jQuery('#account_name_edit').val(result.data.account_name);
                    jQuery('#account_number_edit').val(result.data.account_number);
                    jQuery('#hidden_input').val(result.html);

                    let selectedBank = result.data.bank_id;
                    let dataArr = result.banks;
                    if (dataArr.length) {
                        $.each(result.banks, function(index, value) {

                            let bank = value.name;
                            let bankID = value.id;

                            if (jQuery('#bank_details_bank').find("option[value='" + value
                                    .id + "']")
                                .length) {

                            } else {
                                jQuery('#bank_details_bank').append(
                                    new Option(bank, bankID)
                                );

                                jQuery('#bank_details_bank').find("option[value='" + selectedBank + "']").prop("selected",true)
                            }
                        });
                    } else {
                        jQuery('#bank_details_bank').find('option')
                            .remove()
                            .end().append(new Option('No bank', ''));
                    }

                    let selectedCurrency = result.data.currency_id;
                    let dataArrCurrency = result.currencies;
                    if (dataArrCurrency.length) {
                        $.each(dataArrCurrency, function(index, value) {

                            let currency = value.short_name;
                            let currencyID = value.id;

                            if (jQuery('#bank_details_currency').find("option[value='" + value
                                    .id + "']")
                                .length) {

                            } else {
                                jQuery('#bank_details_currency').append(
                                    new Option(currency, currencyID)
                                );

                                jQuery('#bank_details_currency').find("option[value='" + selectedCurrency + "']").prop("selected",true)
                            }
                        });
                    } else {
                        jQuery('#bank_details_currency').find('option')
                            .remove()
                            .end().append(new Option('No currency', ''));
                    }

                    document.getElementById('editBankAccountDetailsForm').setAttribute("action", url);
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

        var urlB = "{{ route('bank_details.edit', ':id') }}";
        url = urlB.replace(':id', bank);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: bank
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('bank_details.destroy', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);

                    jQuery('#delete-bank-details-modal').modal('show');
                    jQuery('#delete_bank_details_name').val(result.bank.name+'/'+result.data.account_name+'/'+result.data.account_number);
                    jQuery('#hidden_input').val(result.html);

                    document.getElementById('deleteBankDetailsForm').setAttribute("action", url);
                }
            }
        });
    }
</script>
