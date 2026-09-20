<div id="edit-addon-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Edit Addon </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form id="editAddonForm" name="editAddonForm" method="POST">
                @method('PUT')
                @csrf
                <div class="modal-body">
                    <h5 style="font-weight: 500;">Fill the form to edit addon </h5>
                    <div class="row mt-3">
                        <div class="col-md-12 col-lg-12">
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" id="addon_name" name="name"
                                            placeholder="Enter addon name" class="form-control" required>
                                        <small class="text-danger" id="error_name"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="is_include" class="form-label">Is Include?</label>
                                        <select class="form-control select2" id="addon_is_include" name="is_include"
                                            data-toggle="select2">
                                            <optgroup label="Is_includes">
                                                <option>Select type</option>
                                            </optgroup>
                                        </select>
                                        <small class="text-danger" id="error_is_include"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="input-hidden" id="hidden_input"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="submitCreateForm('editAddonForm')"
                        class="btn btn-success saveBtn">Update details</button>
                    <button class="btn btn-success btnLoading" type="button" style="display: none" disabled>
                        <span class="spinner-grow spinner-grow-sm me-1" role="" aria-hidden="true"></span>
                        Loading...
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
