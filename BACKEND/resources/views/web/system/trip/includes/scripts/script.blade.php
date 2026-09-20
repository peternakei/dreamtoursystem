<script>
    jQuery(document).ready(function() {
        jQuery('#type,#source').select2({
            dropdownParent: $("#create-trip-modal")
        });
        jQuery('#trip_type,#trip_source').select2({
            dropdownParent: $("#edit-trip-modal")
        });
        function formatTripAddonOption(state) {
            if (!state.id) {
                return state.text;
            }
            var include = jQuery(state.element).data('include');
            if (typeof include === 'undefined' || include === null || include === '') {
                return state.text;
            }
            var badge = parseInt(include) === 1
                ? '<span class="badge bg-success ms-1" style="background-color:#28a745;color:#fff;">Included</span>'
                : '<span class="badge bg-danger ms-1" style="background-color:#dc3545;color:#fff;">Not Included</span>';
            return jQuery('<span>' + jQuery('<div/>').text(state.text).html() + ' ' + badge + '</span>');
        }

        jQuery('#addon_0').select2({
            dropdownParent: $("#create-trip-addon-modal"),
            templateResult: formatTripAddonOption,
            templateSelection: formatTripAddonOption,
            escapeMarkup: function(m) { return m; }
        });
        jQuery('#category_0').select2({
            dropdownParent: $("#create-trip-category-modal")
        });
        jQuery('#destination_0').select2({
            dropdownParent: $("#create-trip-destination-modal")
        });
        jQuery('#activity_0').select2({
            dropdownParent: $("#create-trip-category-activities-modal")
        });
        jQuery('#age,#group,#currency').select2({
            dropdownParent: $("#create-trip-price-modal")
        });
        jQuery('#trip_budget_currency').select2({
            dropdownParent: $("#manage-trip-budgets-modal")
        });
        jQuery('#trip_category').select2({
            dropdownParent: $("#create-trip-category-activities-modal")
        });

        var j = 0;

        //add row buttons
        jQuery('.assignTripAddonItemsRow').click(function(e) {
            j++;
            e.stopImmediatePropagation();
            jQuery('#assignTripAddonItemsTable').append('<tr id="row' + j +
                '"><td><select class="form-control select2 row-input trip-addon-select" id="addon_' +
                j +
                '" name="addon[]" data-toggle="select2"><optgroup label="Addons"><option style="font-weight: bold;">Select addon </option> @foreach ($addons as $addon) <option value="{{ $addon->id }}" data-include="{{ $addon->is_include ? 1 : 0 }}"> {{ $addon['name'] }}</option> @endforeach </optgroup></select><small class="text-danger" id="error_addon_' +
                j + '"></small></td><td><a href="#" id="' + j +
                '" name="remove" class="action-icon text-danger removeTripAddonItemRow"> <i class="uil uil-trash"></i></a></td></td></tr>'
            );

            jQuery('#addon_' + j + '').select2({
                dropdownParent: $("#create-trip-addon-modal"),
                templateResult: formatTripAddonOption,
                templateSelection: formatTripAddonOption,
                escapeMarkup: function(m) { return m; }
            });
        });

        //remove row buttons
        jQuery(document).on('click', '.removeTripAddonItemRow', function(e) {
            e.stopImmediatePropagation();
            let button_id = jQuery(this).attr("id");
            jQuery('#row' + button_id + '').remove();
        });

        var f = 0;

        //add row buttons
        jQuery('.assignTripCategoryItemsRow').click(function(e) {
            f++;
            e.stopImmediatePropagation();
            jQuery('#assignTripCategoryItemsTable').append('<tr id="row' + f +
                '"><td><select class="form-control select2 row-input" id="category_' +
                f +
                '" name="category[]" data-toggle="select2"><optgroup label="Categories"><option style="font-weight: bold;">Select category </option> @foreach ($categories as $category) <option value="{{ $category->id }}"> {{ $category['name'] }}</option> @endforeach </optgroup></select><small class="text-danger" id="error_category_' +
                f + '"></small></td><td><a href="#" id="' + f +
                '" name="remove" class="action-icon text-danger removeTripCategoryItemRow"> <i class="uil uil-trash"></i></a></td></td></tr>'
            );

            jQuery('#category_' + f + '').select2({
                dropdownParent: $("#create-trip-category-modal")
            });
        });

        //remove row buttons
        jQuery(document).on('click', '.removeTripCategoryItemRow', function(e) {
            e.stopImmediatePropagation();
            let button_id = jQuery(this).attr("id");
            jQuery('#row' + button_id + '').remove();
        });

        var z = 0;

        //add row buttons
        jQuery('.assignTripDestinationItemsRow').click(function(e) {
            z++;
            e.stopImmediatePropagation();
            jQuery('#assignTripDestinationItemsTable').append('<tr id="row' + z +
                '"><td><select class="form-control select2 row-input" id="destination_' +
                z +
                '" name="destination[]" data-toggle="select2"><optgroup label="Destinations"><option style="font-weight: bold;">Select destination </option> @foreach ($destinations as $destination) <option value="{{ $destination->id }}"> {{ $destination['name'] }}</option> @endforeach </optgroup></select><small class="text-danger" id="error_destination_' +
                z +
                '"></small></td><td><div><div class="col-md-12 col-lg-12"><div class="mb-0"><input type="text" id="description_' +
                z +
                '" name="description[]" placeholder="description" class="form-control row-input"><small class="text-danger" id="error_description_' +
                z + '"></small></div></div></div></td><td><a href="#" id="' + z +
                '" name="remove" class="action-icon text-danger removeTripDestinationItemRow"> <i class="uil uil-trash"></i></a></td></td></tr>'
            );

            jQuery('#destination_' + z + '').select2({
                dropdownParent: $("#create-trip-destination-modal")
            });
        });

        //remove row buttons
        jQuery(document).on('click', '.removeTripDestinationItemRow', function(e) {
            e.stopImmediatePropagation();
            let button_id = jQuery(this).attr("id");
            jQuery('#row' + button_id + '').remove();
        });

        var l = 0;

        //add row buttons
        jQuery('.assignTripCategoryActivityItemsRow').click(function(e) {
            l++;
            e.stopImmediatePropagation();
            jQuery('#assignTripCategoryActivityItemsTable').append('<tr id="row' + l +
                '"><td><select class="form-control select2 row-input" id="activity_' +
                l +
                '" name="activity[]" data-toggle="select2"><optgroup label="Activities"><option style="font-weight: bold;">Select activity </option> @foreach ($activities as $activity) <option value="{{ $activity->id }}"> {{ $activity['name'] }}</option> @endforeach </optgroup></select><small class="text-danger" id="error_activity_' +
                l +
                '"></small></td><td><div><div class="col-md-12 col-lg-12"><div class="mb-0"><input type="text" id="description_' +
                z +
                '" name="description[]" placeholder="description" class="form-control row-input"><small class="text-danger" id="error_description_' +
                l + '"></small></div></div></div></td><td><a href="#" id="' + l +
                '" name="remove" class="action-icon text-danger removeTripCategoryActivityItemRow"> <i class="uil uil-trash"></i></a></td></td></tr>'
            );

            jQuery('#activity_' + l + '').select2({
                dropdownParent: $("#create-trip-category-activities-modal")
            });
        });

        //remove row buttons
        jQuery(document).on('click', '.removeTripCategoryActivityItemRow', function(e) {
            e.stopImmediatePropagation();
            let button_id = jQuery(this).attr("id");
            jQuery('#row' + button_id + '').remove();
        });

        var s = 0;

        //add row buttons
        jQuery('.assignTripPointItemsRow').click(function(e) {
            s++;
            e.stopImmediatePropagation();
            jQuery('#assignTripPointItemsTable').append('<tr id="row' + s +
                '"><td><div><div class="col-md-12 col-lg-12"><div class="mb-0"><input type="text" id="title_' +
                s +
                '" name="title[]" placeholder="title" class="form-control row-input"><small class="text-danger" id="error_title_' +
                s +
                '"></small></div></div></div></td><td><div><div class="col-md-12 col-lg-12"><div class="mb-0"><textarea id="description_' +
                s +
                '" name="description[]" placeholder="description" class="form-control row-input" rows="10"></textarea><small class="text-danger" id="error_description_' +
                s + '"></small></div></div></div></td><td><a href="#" id="' + s +
                '" name="remove" class="action-icon text-danger removeTripPointItemRow"> <i class="uil uil-trash"></i></a></td></td></tr>'
            );
        });

        //remove row buttons
        jQuery(document).on('click', '.removeTripPointItemRow', function(e) {
            e.stopImmediatePropagation();
            let button_id = jQuery(this).attr("id");
            jQuery('#row' + button_id + '').remove();
        });
    });

    function getEditDetails(trip) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('trips.edit', ':id') }}";
        url = urlB.replace(':id', trip);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: trip
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('trips.update', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);

                    jQuery('#edit-trip-modal').modal('show');
                    jQuery('#trip_name').val(result.data.name);
                    jQuery('#trip_from_date').val(result.data.from_date);
                    jQuery('#trip_to_date').val(result.data.to_date);
                    jQuery('#trip_last_booking_date').val(result.data.last_booking_date);
                    jQuery('#trip_last_payment_date').val(result.data.last_payment_date);
                    jQuery('#hidden_input').val(result.html);

                    var quill = new Quill("#edit-trip-description-editor", {
                        theme: "snow",
                        modules: {
                            toolbar: [
                                [{
                                    font: []
                                }, {
                                    size: []
                                }],
                                ["bold", "italic", "underline", "strike"],
                                [{
                                    color: []
                                }, {
                                    background: []
                                }],
                                [{
                                    script: "super"
                                }, {
                                    script: "sub"
                                }],
                                [{
                                    header: [!1, 1, 2, 3, 4, 5, 6]
                                }, "blockquote", "code-block"],
                                [{
                                    list: "ordered"
                                }, {
                                    list: "bullet"
                                }, {
                                    indent: "-1"
                                }, {
                                    indent: "+1"
                                }],
                                ["direction", {
                                    align: []
                                }],
                                ["link", "image", "video"],
                                ["clean"]
                            ]
                        }
                    });

                    var description = result.data.description;
                    if (description) {
                        quill.root.innerHTML = description;
                    }

                    let selectedType = result.data.trip_type_id;
                    let dataArrType = result.tripTypes;
                    if (dataArrType.length) {
                        $.each(result.tripTypes, function(index, value) {

                            let type = value.name;
                            let typeID = value.id;

                            if (jQuery('#trip_type').find("option[value='" + value
                                    .id + "']")
                                .length) {

                            } else {
                                jQuery('#trip_type').append(
                                    new Option(type, typeID)
                                );

                                jQuery('#trip_type').find("option[value='" + selectedType + "']")
                                    .prop("selected", true)
                            }
                        });
                    } else {
                        jQuery('#trip_type').find('option')
                            .remove()
                            .end().append(new Option('No type', ''));
                    }

                    let selectedSource = result.data.trip_source_id;
                    let dataArrSource = result.tripSources;
                    if (dataArrSource.length) {
                        $.each(result.tripSources, function(index, value) {

                            let source = value.name;
                            let sourceID = value.id;

                            if (jQuery('#trip_source').find("option[value='" + value
                                    .id + "']")
                                .length) {

                            } else {
                                jQuery('#trip_source').append(
                                    new Option(source, sourceID)
                                );

                                jQuery('#trip_source').find("option[value='" + selectedSource +
                                    "']").prop("selected", true)
                            }
                        });
                    } else {
                        jQuery('#trip_source').find('option')
                            .remove()
                            .end().append(new Option('No source', ''));
                    }

                    document.getElementById('editTripDetailsForm').setAttribute("action", url);
                }
            }
        });
    }

    function getDeleteDetails(trip) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('trips.edit', ':id') }}";
        url = urlB.replace(':id', trip);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: trip
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('trips.destroy', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);

                    jQuery('#delete-trip-modal').modal('show');
                    jQuery('#delete_trip_name').val(result.data.name);
                    jQuery('#hidden_input').val(result.html);

                    document.getElementById('deleteTripForm').setAttribute("action", url);
                }
            }
        });
    }

    function getPointEditDetails(point) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('trips.edit_point', ':id') }}";
        url = urlB.replace(':id', point);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: point
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('trips.update_point', ['id' => ':id', 'trip' => ':trip']) }}";
                    var url = urlB.replace(':id', result.data.uuid).replace(':trip', result.trip);

                    jQuery('#edit-trip-point-modal').modal('show');

                    jQuery('#trip_title').val(result.data.title);
                    jQuery('#trip_description').val(result.data.description);

                    jQuery('#hidden_input').val(result.html);

                    document.getElementById('editTripPointDetailsForm').setAttribute("action", url);
                }
            }
        });
    }

    function deleteTripPoint(pointId) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('trips.get_point', ':id') }}";
        url = urlB.replace(':id', pointId);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: pointId
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('trips.delete_point', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);

                    jQuery('#delete-trip-point-modal').modal('show');
                    jQuery('#delete_trip_point_title').val(result.data.title);
                    jQuery('#delete_trip_point_description').val(result.data.description);
                    jQuery('#hidden_input').val(result.html);

                    document.getElementById('deleteTripPointForm').setAttribute("action", url);
                }
            }
        });
    }

    function deleteTripAddon(addonId) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('trips.get_addon', ':id') }}";
        url = urlB.replace(':id', addonId);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: addonId
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('trips.delete_addon', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);

                    jQuery('#delete-trip-addon-modal').modal('show');
                    jQuery('#delete_trip_addon_name').val(result.data.addon.name);
                    jQuery('#delete_trip_addon_status').val(result.data.is_active ? 'Active' : 'Inactive');
                    jQuery('#hidden_input').val(result.html);

                    document.getElementById('deleteTripAddonForm').setAttribute("action", url);
                }
            }
        });
    }
</script>
