<div id="revoke-user-role-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Revoke <span
                        class="text-primary">{!! $user->name !!}</span> User Role</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form action="{{ route('users.revoke_role',$user->uuid) }}"
                id="revokeUserRoleForm" name="revokeUserRoleForm" method="post">
                @csrf
                <div class="modal-body">
                    <h5 style="font-weight: 500;">Are you sure you want to revoke <span class="text-danger" id="role_name"></span> role?</h5>
                    <div class="row mt-3">
                        <div class="col-md-12 col-lg-12">
                            <input type="hidden" name="role_id" id="role_id">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" onclick="submitCreateForm('revokeUserRoleForm')"
                        class="btn btn-danger saveBtn">Revoke Role</button>
                    <button class="btn btn-success btnLoading" type="button" style="display: none" disabled>
                        <span class="spinner-grow spinner-grow-sm me-1" role="status" aria-hidden="true"></span>
                        Loading...
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
