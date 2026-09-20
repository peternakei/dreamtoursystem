<script>
    jQuery(document).ready(function() {
        jQuery('#season,#class,#currency').select2({
            dropdownParent: $("#create-budget-modal")
        });
    });

    function getEditDetails(budget) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('budgets.edit', ':id') }}";
        url = urlB.replace(':id', budget);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: budget
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('budgets.update', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);

                    jQuery('#edit-budget-modal').modal('show');
                    jQuery('#budget_price').val(result.data.price);
                    jQuery('#budget_quantity').val(result.data.quantity);
                    jQuery('#hidden_input').val(result.html);

                    let selectedTrip = result.data.trip_id;
                    let dataArrTrip = result.trips;
                    if (dataArrTrip.length) {
                        $.each(result.trips, function(index, value) {

                            let trip = value.trip_code+' / '+value.name;
                            let tripID = value.id;

                            if (jQuery('#budget_trip').find("option[value='" + value
                                    .id + "']")
                                .length) {

                            } else {
                                jQuery('#budget_trip').append(
                                    new Option(trip, tripID)
                                );

                                jQuery('#budget_trip').find("option[value='" + selectedTrip +
                                    "']").prop("selected", true)
                            }
                        });
                    } else {
                        jQuery('#budget_trip').find('option')
                            .remove()
                            .end().append(new Option('No trip', ''));
                    }

                    let selectedSeason = result.data.season_id;
                    let dataArrSeason = result.seasons;
                    if (dataArrSeason.length) {
                        $.each(result.seasons, function(index, value) {

                            let season = value.name;
                            let seasonID = value.id;

                            if (jQuery('#budget_season').find("option[value='" + value
                                    .id + "']")
                                .length) {

                            } else {
                                jQuery('#budget_season').append(
                                    new Option(season, seasonID)
                                );

                                jQuery('#budget_season').find("option[value='" + selectedSeason +
                                    "']").prop("selected", true)
                            }
                        });
                    } else {
                        jQuery('#budget_season').find('option')
                            .remove()
                            .end().append(new Option('No season', ''));
                    }

                    let selectedClasss = result.data.service_class_id;
                    let dataArrClasss = result.classes;
                    if (dataArrClasss.length) {
                        $.each(dataArrClasss, function(index, value) {

                            let serviceClass = value.name;
                            let classID = value.id;

                            if (jQuery('#budget_class').find("option[value='" + value
                                    .id + "']")
                                .length) {

                            } else {
                                jQuery('#budget_class').append(
                                    new Option(serviceClass, classID)
                                );

                                jQuery('#budget_class').find("option[value='" + selectedClasss +
                                    "']").prop("selected", true)
                            }
                        });
                    } else {
                        jQuery('#budget_class').find('option')
                            .remove()
                            .end().append(new Option('No class', ''));
                    }

                    let selectedCurrency = result.data.currency_id;
                    let dataArrCurrency = result.currencies;
                    if (dataArrCurrency.length) {
                        $.each(result.currencies, function(index, value) {

                            let currency = value.short_name;
                            let currencyID = value.id;

                            if (jQuery('#budget_currency').find("option[value='" + value
                                    .id + "']")
                                .length) {

                            } else {
                                jQuery('#budget_currency').append(
                                    new Option(currency, currencyID)
                                );

                                jQuery('#budget_currency').find("option[value='" +
                                    selectedCurrency + "']").prop("selected", true)
                            }
                        });
                    } else {
                        jQuery('#budget_currency').find('option')
                            .remove()
                            .end().append(new Option('No currency', ''));
                    }

                    document.getElementById('editBudgetForm').setAttribute("action", url);
                }
            }
        });
    }

    function getDeleteDetails(budget) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('budgets.edit', ':id') }}";
        url = urlB.replace(':id', budget);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: budget
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('budgets.destroy', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);

                    jQuery('#delete-budget-modal').modal('show');
                    jQuery('#delete_budget_name').val(result.season_name);
                    jQuery('#hidden_input').val(result.html);

                    document.getElementById('deleteBudgetForm').setAttribute("action", url);
                }
            }
        });
    }
</script>
