<div id="delete-trip-addon-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Delete Trip Addon</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form id="deleteTripAddonForm" name="deleteTripAddonForm" method="POST">
                @method('DELETE')
                @csrf
                <div class="modal-body">
                    <h5 style="font-weight: 500;">Are you sure you want to delete this trip addon?</h5>
                    <div class="row mt-3">
                        <div class="col-md-12 col-lg-12">
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <div class="mb-3">
                                        <label for="addon_name" class="form-label">Addon Name</label>
                                        <input type="text" id="delete_trip_addon_name" name="addon_name"
                                            placeholder="Enter addon name" class="form-control" disabled>
                                        <small class="text-danger" id="error_addon_name"></small>
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-12">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <input type="text" id="delete_trip_addon_status" name="status"
                                            placeholder="Enter addon status" class="form-control" disabled>
                                        <small class="text-danger" id="error_status"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="input-hidden" id="hidden_input"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="submitCreateForm('deleteTripAddonForm')"
                        class="btn btn-danger saveBtn">Delete Addon</button>
                    <button class="btn btn-danger btnLoading" type="button" style="display: none" disabled>
                        <span class="spinner-grow spinner-grow-sm me-1" role="" aria-hidden="true"></span>
                        Loading...
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
