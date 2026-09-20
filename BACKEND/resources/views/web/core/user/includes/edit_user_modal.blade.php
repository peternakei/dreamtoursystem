<div id="edit-user-modal" class="modal fade" tabindex="-1" user="dialog" aria-labelledby="standard-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Edit User </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form id="editUserForm" name="editUserForm" method="POST">
                @method('PUT')
                @csrf
                <div class="modal-body">
                    <h5 style="font-weight: 500;">Fill the form to edit new user </h5>
                    <div class="row mt-3">
                        <div class="col-md-12 col-lg-12">
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" id="user_name" name="name"
                                            placeholder="Enter user name" class="form-control" required>
                                        <small class="text-danger" id="error_name"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Mobile</label>
                                        <input type="number" id="user_phone" name="phone"
                                            placeholder="Enter user phone" class="form-control" required>
                                        <small class="text-danger" id="error_phone"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" id="user_email" name="email"
                                            placeholder="Enter user email" class="form-control" required>
                                        <small class="text-danger" id="error_email"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="submitCreateForm('editUserForm')"
                        class="btn btn-success saveBtn">Update details</button>
                    <button class="btn btn-success btnLoading" type="button" style="display: none" disabled>
                        <span class="spinner-grow spinner-grow-sm me-1" user="" aria-hidden="true"></span>
                        Loading...
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
