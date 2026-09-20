<script>
    jQuery(document).ready(function() {
        jQuery('#location,#region').select2({dropdownParent: $("#create-destination-modal")});
        jQuery('#destination_location,#destination_region').select2({dropdownParent: $("#edit-destination-modal")});
        jQuery('#category_0').select2({dropdownParent: $("#assign-destination-category-modal")});
        jQuery('#activity_0').select2({dropdownParent: $("#assign-destination-activity-modal")});
        initializeDestinationMaps();

        var i = 0;

        //add row buttons
        jQuery('.uploadAddOrderItemsRow').click(function(e) {
            i++;
            e.stopImmediatePropagation();
            jQuery('#uploadOrderItemsTable').append('<tr id="row' + i +
                '"><td><input type="file" id="identity_image_' + i +
                '" name="identity_image[]" accept="image/*" class="form-control row-input"><small class="text-danger"id="error_identity_image_' +
                i +
                '"></small><small id="id_pdf_'+i+'" class="text-danger"><i>Upload image upto 2Mbs</i></small></td></td><td><a href="#" id="' + i +
                '" name="remove" class="action-icon text-danger removeOrderItemRow"> <i class="uil uil-trash"></i></a></td></td></tr>'
            );

        });

        //remove row buttons
        jQuery(document).on('click', '.removeOrderItemRow', function(e) {
            e.stopImmediatePropagation();
            let button_id = jQuery(this).attr("id");
            jQuery('#row' + button_id + '').remove();
        });

        var j = 0;

        //add row buttons
        jQuery('.assignDestinationActivityItemsRow').click(function(e) {
            j++;
            e.stopImmediatePropagation();
            jQuery('#assignDestinationActivityItemsTable').append('<tr id="row' + j +
                '"><td><select class="form-control select2 row-input" id="activity_' +
                j +
                '" name="activity[]" data-toggle="select2"><optgroup label="Activities"><option style="font-weight: bold;">Select activity </option> @foreach ($activities as $activity) <option value="{{ $activity->id }}"> {{ $activity['name'] }}</option> @endforeach </optgroup></select><small class="text-danger" id="error_activity_' +
                j + '"></small></td><td><input type="file" id="activity_image_' + j +
                '" name="activity_image[]" accept="images.*" class="form-control row-input"><small class="text-danger"id="error_activity_image_' +
                j +
                '"></small><small id="id_pdf_'+j+'" class="text-danger"><i>Upload image upto 2Mbs</i></small></td></td><td><a href="#" id="' + j +
                '" name="remove" class="action-icon text-danger removeDestinationActivityItemRow"> <i class="uil uil-trash"></i></a></td></td></tr>'
            );

            jQuery('#activity_' + j + '').select2({dropdownParent: $("#assign-destination-activity-modal")});
        });

        //remove row buttons
        jQuery(document).on('click', '.removeDestinationActivityItemRow', function(e) {
            e.stopImmediatePropagation();
            let button_id = jQuery(this).attr("id");
            jQuery('#row' + button_id + '').remove();
        });

        var k = 0;

        //add row buttons
        jQuery('.assignDestinationCategoryItemsRow').click(function(e) {
            k++;
            e.stopImmediatePropagation();
            jQuery('#assignDestinationCategoryItemsTable').append('<tr id="row' + k +
                '"><td><select class="form-control select2 row-input" id="category_' +
                k +
                '" name="category[]" data-toggle="select2"><optgroup label="Categories"><option style="font-weight: bold;">Select category </option> @foreach ($categories as $category) <option value="{{ $category->id }}"> {{ $category['name'] }}</option> @endforeach </optgroup></select><small class="text-danger" id="error_category_' +
                k + '"></small></td></td><td><a href="#" id="' + k +
                '" name="remove" class="action-icon text-danger removeDestinationCategoryItemRow"> <i class="uil uil-trash"></i></a></td></td></tr>'
            );

            jQuery('#category_' + k + '').select2({dropdownParent: $("#assign-destination-category-modal")});
        });

        //remove row buttons
        jQuery(document).on('click', '.removeDestinationCategoryItemRow', function(e) {
            e.stopImmediatePropagation();
            let button_id = jQuery(this).attr("id");
            jQuery('#row' + button_id + '').remove();
        });
    });

    const destinationMapConfigs = {
        create: {
            mapId: 'create_destination_map',
            latitudeId: 'latitude',
            longitudeId: 'longitude',
            searchInputId: 'create_destination_search',
            searchButtonId: 'create_destination_search_btn',
            liveButtonId: 'create_destination_live_btn',
            modalId: 'create-destination-modal',
            errorId: 'create_destination_map_error'
        },
        edit: {
            mapId: 'edit_destination_map',
            latitudeId: 'destination_latitude',
            longitudeId: 'destination_longitude',
            searchInputId: 'edit_destination_search',
            searchButtonId: 'edit_destination_search_btn',
            liveButtonId: 'edit_destination_live_btn',
            modalId: 'edit-destination-modal',
            errorId: 'edit_destination_map_error'
        }
    };

    const destinationMapInstances = {};

    function initializeDestinationMaps() {
        if (typeof L === 'undefined') {
            return;
        }

        Object.entries(destinationMapConfigs).forEach(([key, config]) => {
            const modal = document.getElementById(config.modalId);
            const searchButton = document.getElementById(config.searchButtonId);
            const liveButton = document.getElementById(config.liveButtonId);
            const searchInput = document.getElementById(config.searchInputId);
            const latitudeInput = document.getElementById(config.latitudeId);
            const longitudeInput = document.getElementById(config.longitudeId);

            if (modal) {
                modal.addEventListener('shown.bs.modal', function() {
                    ensureDestinationMap(key);
                    syncMapFromInputs(key);
                    setTimeout(() => destinationMapInstances[key]?.map.invalidateSize(), 150);
                });
            }

            if (searchButton) {
                searchButton.addEventListener('click', function() {
                    searchDestinationLocation(key);
                });
            }

            if (searchInput) {
                searchInput.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        searchDestinationLocation(key);
                    }
                });
            }

            if (liveButton) {
                liveButton.addEventListener('click', function() {
                    useLiveDestinationLocation(key);
                });
            }

            if (latitudeInput) {
                latitudeInput.addEventListener('change', function() {
                    syncMapFromInputs(key);
                });
            }

            if (longitudeInput) {
                longitudeInput.addEventListener('change', function() {
                    syncMapFromInputs(key);
                });
            }
        });
    }

    function ensureDestinationMap(key) {
        if (destinationMapInstances[key]) {
            return destinationMapInstances[key];
        }

        const config = destinationMapConfigs[key];
        const map = L.map(config.mapId, {
            scrollWheelZoom: false
        }).setView([-6.369, 34.8888], 5);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const marker = L.marker([-6.369, 34.8888], {
            draggable: true
        }).addTo(map);

        map.on('click', function(event) {
            updateDestinationCoordinates(key, event.latlng.lat, event.latlng.lng, true);
        });

        marker.on('dragend', function() {
            const position = marker.getLatLng();
            updateDestinationCoordinates(key, position.lat, position.lng, false);
        });

        destinationMapInstances[key] = {
            map,
            marker
        };

        return destinationMapInstances[key];
    }

    function updateDestinationCoordinates(key, latitude, longitude, recenterMap = true) {
        const config = destinationMapConfigs[key];
        const instance = ensureDestinationMap(key);
        const latValue = Number(latitude).toFixed(6);
        const lngValue = Number(longitude).toFixed(6);

        jQuery(`#${config.latitudeId}`).val(latValue);
        jQuery(`#${config.longitudeId}`).val(lngValue);
        jQuery(`#${config.errorId}`).text('');

        const point = [Number(latValue), Number(lngValue)];
        instance.marker.setLatLng(point);

        if (recenterMap) {
            instance.map.setView(point, 13);
        }
    }

    function syncMapFromInputs(key) {
        const config = destinationMapConfigs[key];
        const latitude = parseFloat(jQuery(`#${config.latitudeId}`).val());
        const longitude = parseFloat(jQuery(`#${config.longitudeId}`).val());

        if (!Number.isNaN(latitude) && !Number.isNaN(longitude)) {
            updateDestinationCoordinates(key, latitude, longitude, true);
        }
    }

    async function searchDestinationLocation(key) {
        const config = destinationMapConfigs[key];
        const query = jQuery(`#${config.searchInputId}`).val().trim();

        if (!query) {
            jQuery(`#${config.errorId}`).text('Enter a place name to search.');
            return;
        }

        jQuery(`#${config.errorId}`).text('');

        try {
            const response = await fetch(
                `https://nominatim.openstreetmap.org/search?format=json&limit=1&q=${encodeURIComponent(query)}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                }
            );
            const results = await response.json();

            if (!results.length) {
                jQuery(`#${config.errorId}`).text('No matching location found.');
                return;
            }

            updateDestinationCoordinates(key, results[0].lat, results[0].lon, true);
        } catch (error) {
            jQuery(`#${config.errorId}`).text('Search failed. Check your connection and try again.');
        }
    }

    function useLiveDestinationLocation(key) {
        const config = destinationMapConfigs[key];

        if (!navigator.geolocation) {
            jQuery(`#${config.errorId}`).text('Live location is not supported on this device/browser.');
            return;
        }

        jQuery(`#${config.errorId}`).text('Requesting device location...');

        navigator.geolocation.getCurrentPosition(
            function(position) {
                jQuery(`#${config.errorId}`).text('');
                updateDestinationCoordinates(
                    key,
                    position.coords.latitude,
                    position.coords.longitude,
                    true
                );
            },
            function() {
                jQuery(`#${config.errorId}`).text('Location access was denied or unavailable.');
            }, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    }

    function getEditDetails(destination) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('destinations.edit', ':id') }}";
        url = urlB.replace(':id', destination);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: destination
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('destinations.update', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);

                    jQuery('#edit-destination-modal').modal('show');
                    jQuery('#destination_name').val(result.data.name);
                    jQuery('#destination_latitude').val(result.data.latitude);
                    jQuery('#destination_longitude').val(result.data.longitude);
                    jQuery('#destination_description').val(result.data.description);
                    jQuery('#hidden_input').val(result.html);

                    let selectedLocation = result.data.location_id;
                    let dataArrLocation = result.locations;
                    if (dataArrLocation.length) {
                        $.each(result.locations, function(index, value) {

                            let location = value.name;
                            let locationID = value.id;

                            if (jQuery('#destination_location').find("option[value='" + value
                                    .id + "']")
                                .length) {

                            } else {
                                jQuery('#destination_location').append(
                                    new Option(location, locationID)
                                );

                                jQuery('#destination_location').find("option[value='" + selectedLocation + "']").prop("selected",true)
                            }
                        });
                    } else {
                        jQuery('#destination_location').find('option')
                            .remove()
                            .end().append(new Option('No location', ''));
                    }

                    let selectedRegion = result.data.region_id;
                    let dataArrRegion = result.regions;
                    if (dataArrRegion.length) {
                        $.each(result.regions, function(index, value) {

                            let region = value.name;
                            let regionID = value.id;

                            if (jQuery('#destination_region').find("option[value='" + value
                                    .id + "']")
                                .length) {

                            } else {
                                jQuery('#destination_region').append(
                                    new Option(region, regionID)
                                );

                                jQuery('#destination_region').find("option[value='" + selectedRegion + "']").prop("selected",true)
                            }
                        });
                    } else {
                        jQuery('#destination_region').find('option')
                            .remove()
                            .end().append(new Option('No region', ''));
                    }

                    document.getElementById('editDestinationDetailsForm').setAttribute("action", url);
                    syncMapFromInputs('edit');
                }
            }
        });
    }

    function getDeleteDetails(destination) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('destinations.edit', ':id') }}";
        url = urlB.replace(':id', destination);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: destination
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('destinations.destroy', ':id') }}";
                    url = urlB.replace(':id', result.data.uuid);

                    jQuery('#delete-destination-modal').modal('show');
                    jQuery('#delete_destination_name').val(result.data.name);
                    jQuery('#hidden_input').val(result.html);

                    document.getElementById('deleteDestinationForm').setAttribute("action", url);
                }
            }
        });
    }

    function getDestinationImage(image){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('destinations.image', ':id') }}";
        url = urlB.replace(':id', image);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: image
            },
            success: function(result) {
                if (result.status === true) {
                    var attach = "{{ asset('storage/uploads/:name') }}";
                    url = attach.replace(':name', result.data.image.name);
                    jQuery('#view-destination-image-modal').modal('show');

                    if (result.data.extension == 'Image') {
                        jQuery('#destination_image').css('display', 'block');
                        jQuery('#destination_pdf').css('display', 'none');
                        document.getElementById('destination_image').setAttribute("src", url);
                    }
                }
            }
        });
    }

    function getDeleteDestinationImage(destination,image){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('destinations.delete_image', ':id') }}";
        url = urlB.replace(':id', image);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: image
            },
            success: function(result) {
                if (result.status === true) {

                    var urlB = "{{ route('destinations.destroy_image', [':destination',':id']) }}";
                    url = urlB.replace(':destination', destination).replace(':id', result.data.image.uuid);

                    jQuery('#delete-destination-image-modal').modal('show');
                    jQuery('#delete_destination_image_name').val(result.data.image.name);
                    jQuery('#hidden_input').val(result.html);

                    document.getElementById('deleteDestinationImageForm').setAttribute("action", url);
                }
            }
        });
    }

    function getDestinationFact(destination,fact){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var urlB = "{{ route('destinations.fact', ':id') }}";
        url = urlB.replace(':id', fact);

        $.ajax({
            url: url,
            method: 'get',
            data: {
                id: fact
            },
            success: function(result) {
                if (result.status === true) {
                    var urlB = "{{ route('destinations.update_destination_fact', [':destination',':id']) }}";
                    url = urlB.replace(':destination',destination).replace(':id', result.data.fact.uuid);

                    jQuery('#edit-destination-fact-modal').modal('show');

                    jQuery('#destination_fact').val(result.data.fact.fact);
                    jQuery('#destination_sub_fact').val(result.data.fact.sub_fact);
                    jQuery('#destination_description').val(result.data.fact.description);
                    jQuery('#hidden_input').val(result.html);

                    document.getElementById('editDestinationFactDetailsForm').setAttribute("action", url);
                }
            }
        });
    }
</script>
