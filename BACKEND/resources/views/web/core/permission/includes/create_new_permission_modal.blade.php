<div id="create-permission-modal" class="modal fade" tabindex="-1" permission="dialog" aria-labelledby="standard-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Create Permission </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form action="{{ route('permissions.store') }}" id="createPermissionForm" name="createPermissionForm" method="POST">
                @csrf
                <div class="modal-body">
                    <h5 style="font-weight: 500;">Fill the form to create new permission </h5>
                    <div class="row mt-3">
                        <div class="col-md-12 col-lg-12">
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" id="name" name="name"
                                            placeholder="Enter permission name" class="form-control" required>
                                        <small class="text-danger" id="error_name"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <div class="mb-3">
                                        <label for="menu" class="form-label">Menu</label>
                                        <select class="form-control select2" id="menu" name="menu"
                                            data-toggle="select2">
                                            <optgroup label="Menu">
                                                <option>Select menu</option>
                                                @foreach ($menus as $menu)
                                                    <option value="{{ $menu['id'] }}">
                                                        {{ $menu['name'] }}</option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                        <small class="text-danger" id="error_menu"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <div class="mb-3">
                                        <label for="role" class="form-label">Roles</label>
                                        <div class="table-section py-0" data-simplebar
                                            style="min-height: 280px; max-height: 350px;">
                                            <table class="table table-bordered table-hover table-centered mb-0">
                                                <thead class="pt-2" style="background-color: #e9ecef;">
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($roles as $role)
                                                        <tr>
                                                            <td>{{ $role->name }}</td>
                                                            <td>
                                                                <div class="form-check form-checkbox-info text-center">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        id="role" name="role[]"
                                                                        value="{{ $role->id }}">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <small class="text-danger" id="error_role"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="submitCreateForm('createPermissionForm')"
                        class="btn btn-success saveBtn">Save details</button>
                    <button class="btn btn-success btnLoading" type="button" style="display: none" disabled>
                        <span class="spinner-grow spinner-grow-sm me-1" permission="" aria-hidden="true"></span>
                        Loading...
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
