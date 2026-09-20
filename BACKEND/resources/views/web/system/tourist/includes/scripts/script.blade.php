<script>
    jQuery(document).ready(function() {
        jQuery('#gender,#country').select2({dropdownParent: $("#create-tourist-modal")});
        jQuery('#tourist_gender,#tourist_country').select2({dropdownParent: $("#edit-tourist-modal")});
    });

    function getEditDetails(tourist) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('tourists.edit', ':id') }}";
        url = urlB.replace(':id', tourist);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: tourist
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('tourists.update', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);

                    jQuery('#edit-tourist-modal').modal('show');
                    jQuery('#tourist_name').val(result.data.name);
                    jQuery('#tourist_phone').val(result.data.phone);
                    jQuery('#tourist_email').val(result.data.email);
                    jQuery('#tourist_address').val(result.data.address);
                    jQuery('#hidden_input').val(result.html);

                    let selectedCountry = result.data.country_id;
                    let dataArrCountry = result.countries;
                    if (dataArrCountry.length) {
                        $.each(result.countries, function(index, value) {

                            let country = value.name;
                            let countryID = value.id;

                            if (jQuery('#tourist_country').find("option[value='" + value
                                    .id + "']")
                                .length) {

                            } else {
                                jQuery('#tourist_country').append(
                                    new Option(country, countryID)
                                );

                                jQuery('#tourist_country').find("option[value='" + selectedCountry + "']").prop("selected",true)
                            }
                        });
                    } else {
                        jQuery('#tourist_country').find('option')
                            .remove()
                            .end().append(new Option('No country', ''));
                    }

                    let selectedGender = result.data.gender_id;
                    let dataArrGender = result.genders;
                    if (dataArrGender.length) {
                        $.each(result.genders, function(index, value) {

                            let gender = value.name;
                            let genderID = value.id;

                            if (jQuery('#tourist_gender').find("option[value='" + value
                                    .id + "']")
                                .length) {

                            } else {
                                jQuery('#tourist_gender').append(
                                    new Option(gender, genderID)
                                );

                                jQuery('#tourist_gender').find("option[value='" + selectedGender + "']").prop("selected",true)
                            }
                        });
                    } else {
                        jQuery('#tourist_gender').find('option')
                            .remove()
                            .end().append(new Option('No gender', ''));
                    }

                    document.getElementById('editTouristDetailsForm').setAttribute("action", url);
                }
            }
        });
    }

    function getDeleteDetails(tourist) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('tourists.edit', ':id') }}";
        url = urlB.replace(':id', tourist);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: tourist
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('tourists.destroy', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);

                    jQuery('#delete-tourist-modal').modal('show');
                    jQuery('#delete_tourist_name').val(result.data.name);
                    jQuery('#hidden_input').val(result.html);

                    document.getElementById('deleteTouristForm').setAttribute("action", url);
                }
            }
        });
    }
</script>
