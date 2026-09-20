<div id="create-trip-addon-modal" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Create <span
                        class="text-primary">{!! $trip->name !!}</span>`s Trip Addon</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form action="{{ route('trips.create_addon', $trip->uuid) }}"
                id="createTripAddonForm" name="createTripAddonForm" method="POST">
                @csrf
                <div class="modal-body">
                    <h5 style="font-weight: 500;">Fill the form to create trip addon</h5>
                    <div class="row mt-3">
                        <div class="col-md-12 col-lg-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-primary btn-sm assignTripAddonItemsRow">
                                            <i class="uil uil-plus-circle"></i> Add</button>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <table class="table table-bordered table-centered mb-0" id="assignTripAddonItemsTable">
                                        <thead>
                                            <tr>
                                                <th>Addon</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div>
                                                        <div class="mb-0">
                                                            <select class="form-control select2 trip-addon-select"
                                                                id="addon_0" name="addon[]"
                                                                data-toggle="select2">
                                                                <optgroup label="Addons">
                                                                    <option>Select addon</option>
                                                                    @foreach ($addons as $addon)
                                                                        <option value="{{ $addon['id'] }}"
                                                                            data-include="{{ $addon['is_include'] ? 1 : 0 }}">
                                                                            {{ $addon['name'] }}</option>
                                                                    @endforeach
                                                                </optgroup>
                                                            </select>
                                                            <small class="text-danger"
                                                                id="error_addon_0"></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="submitCreateForm('createTripAddonForm')"
                        class="btn btn-success saveBtn">Create Addon</button>
                    <button class="btn btn-success btnLoading" type="button" style="display: none" disabled>
                        <span class="spinner-grow spinner-grow-sm me-1" role="addon" aria-hidden="true"></span>
                        Loading...
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
