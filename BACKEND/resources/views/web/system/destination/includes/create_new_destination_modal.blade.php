<div id="create-destination-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Create Destination </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form action="{{ route('destinations.store') }}" id="createDestinationForm" name="createDestinationForm"
                method="POST">
                @csrf
                <div class="modal-body">
                    <h5 style="font-weight: 500;">Fill the form to create new destination </h5>
                    <div class="row mt-3">
                        <div class="col-md-12 col-lg-12">
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" id="name" name="name"
                                            placeholder="Enter destination name" class="form-control" required>
                                        <small class="text-danger" id="error_name"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="location" class="form-label">Location</label>
                                        <select class="form-control select2" id="location" name="location"
                                            data-toggle="select2">
                                            <optgroup label="Locations">
                                                <option>Select location</option>
                                                @foreach ($locations as $location)
                                                    <option value="{{ $location['id'] }}">
                                                        {{ $location['name'] }}</option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                        <small class="text-danger" id="error_location"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="latitude" class="form-label">Latitude</label>
                                        <input type="number" step="any" id="latitude" name="latitude"
                                            placeholder="Enter destination latitude" class="form-control" required>
                                        <small class="text-danger" id="error_latitude"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="longitude" class="form-label">Longitude</label>
                                        <input type="number" step="any" id="longitude" name="longitude"
                                            placeholder="Enter destination longitude" class="form-control" required>
                                        <small class="text-danger" id="error_longitude"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Pick Coordinates From Map</label>
                                        <div class="d-flex flex-wrap gap-2 mb-2">
                                            <input type="text" id="create_destination_search" class="form-control"
                                                placeholder="Search location on map" style="flex: 1 1 260px;">
                                            <button type="button" class="btn btn-outline-primary"
                                                id="create_destination_search_btn">Search</button>
                                            <button type="button" class="btn btn-outline-secondary"
                                                id="create_destination_live_btn">Use Live Location</button>
                                        </div>
                                        <small class="text-muted d-block mb-2">
                                            Search a place, click anywhere on the map, or use your device location to fill
                                            latitude and longitude automatically.
                                        </small>
                                        <div id="create_destination_map"
                                            style="height: 320px; border-radius: 12px; overflow: hidden; border: 1px solid #d9e2ec;">
                                        </div>
                                        <small class="text-danger d-block mt-2" id="create_destination_map_error"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <div class="mb-3">
                                        <label for="region" class="form-label">Region</label>
                                        <select class="form-control select2" id="region" name="region"
                                            data-toggle="select2">
                                            <optgroup label="Regions">
                                                <option>Select region</option>
                                                @foreach ($regions as $region)
                                                    <option value="{{ $region['id'] }}">
                                                        {{ $region['name'] }}</option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                        <small class="text-danger" id="error_region"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-2">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea rows="5" id="description" name="description" placeholder="Enter description" class="form-control" required></textarea>
                                        <small class="text-danger" id="error_description"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="submitCreateForm('createDestinationForm')"
                        class="btn btn-success saveBtn">Save details</button>
                    <button class="btn btn-success btnLoading" type="button" style="display: none" disabled>
                        <span class="spinner-grow spinner-grow-sm me-1" role="" aria-hidden="true"></span>
                        Loading...
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
