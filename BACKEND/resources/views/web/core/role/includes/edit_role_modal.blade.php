<div id="edit-role-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Edit Role </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form id="editRoleForm" name="editRoleForm" method="POST">
                @method('PUT')
                @csrf
                <div class="modal-body">
                    <h5 style="font-weight: 500;">Fill the form to edit new role </h5>
                    <div class="row mt-3">
                        <div class="col-md-12 col-lg-12">
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" id="role_name" name="name"
                                            placeholder="Enter role name" class="form-control" required>
                                        <small class="text-danger" id="error_name"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <div class="mb-3">
                                        <label for="permission" class="form-label">Permissions</label>
                                        <div class="table-section py-0" data-simplebar
                                            style="min-height: 280px; max-height: 350px;">
                                            <table class="table table-bordered table-hover table-centered mb-0" id="rolePermissionTable">
                                                <thead class="pt-2" style="background-color: #e9ecef;">
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="rolePermissionSection">

                                                </tbody>
                                            </table>
                                        </div>
                                        <small class="text-danger" id="error_permission"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="submitCreateForm('editRoleForm')"
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
