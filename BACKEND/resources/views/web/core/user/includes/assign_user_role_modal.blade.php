<div id="assign-user-role-modal" class="modal fade" tabindex="-1" user="dialog" aria-labelledby="standard-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Assign User Role</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form action="{{ route('users.assign_role',$user->uuid) }}" id="assignUserRoleForm" name="assignUserRoleForm" method="POST">
                @csrf
                <div class="modal-body">
                    <h5 style="font-weight: 500;">Fill the form to assign user role</h5>
                    <div class="row mt-3">
                        <div class="col-md-12 col-lg-12">
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <div class="mb-3">
                                        <label for="role" class="form-label">Role</label>
                                        <select class="form-control select2" id="role" name="role"
                                            data-toggle="select2">
                                            <optgroup label="Role">
                                                <option>Select role</option>
                                                @foreach ($roles as $role)
                                                @if (!in_array($role['id'],($user->roles->pluck('role_id'))->toArray()))
                                                    <option value="{{ $role['id'] }}">
                                                        {{ $role['name'] }}</option>
                                                @endif
                                                @endforeach
                                            </optgroup>
                                        </select>
                                        <small class="text-danger" id="error_role"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="submitCreateForm('assignUserRoleForm')"
                        class="btn btn-success saveBtn">Save details</button>
                    <button class="btn btn-success btnLoading" type="button" style="display: none" disabled>
                        <span class="spinner-grow spinner-grow-sm me-1" user="" aria-hidden="true"></span>
                        Loading...
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
