<div id="create-system-config-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Create Configuration </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form action="{{ route('system_configurations.store') }}" id="createSystemConfigForm"
                name="createSystemConfigForm" method="POST">
                @csrf
                <div class="modal-body">
                    <h5 style="font-weight: 500;">Fill the form to create new system configuration </h5>
                    <div class="row mt-3">
                        <div class="col-md-12 col-lg-12">
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="config_type" class="form-label">Configuration Type</label>
                                        <select class="form-control select2" id="config_type" name="config_type"
                                            data-toggle="select2">
                                            <optgroup label="Configuration Types">
                                                <option>Select config type</option>
                                                @foreach ($configTypes as $type)
                                                    <option value="{{ $type['id'] }}">
                                                        {{ $type['name'] }}</option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                        <small class="text-danger" id="error_config_type"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="config_value" class="form-label">Value</label>
                                        <input type="text" id="config_value" name="config_value"
                                            placeholder="Enter configuration value" class="form-control" required>
                                        <small class="text-danger" id="error_config_value"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="submitCreateForm('createSystemConfigForm')"
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
