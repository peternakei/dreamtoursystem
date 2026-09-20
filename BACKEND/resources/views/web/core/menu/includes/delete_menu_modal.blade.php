<div id="delete-menu-modal" class="modal fade" tabindex="-1" menu="dialog" aria-labelledby="standard-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Delete Menu </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form id="deleteMenuForm" name="deleteMenuForm" method="POST">
                @method('DELETE')
                @csrf
                <div class="modal-body">
                    <h5 style="font-weight: 500;">Are you sure you want to delete menu </h5>
                    <div class="row mt-3">
                        <div class="col-md-12 col-lg-12">
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" id="delete_menu_name" name="name"
                                            placeholder="Enter menu name" class="form-control" disabled>
                                        <small class="text-danger" id="error_name"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="input-hidden" id="hidden_input"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="submitCreateForm('deleteMenuForm')"
                        class="btn btn-danger saveBtn">Delete details</button>
                    <button class="btn btn-success btnLoading" type="button" style="display: none" disabled>
                        <span class="spinner-grow spinner-grow-sm me-1" menu="" aria-hidden="true"></span>
                        Loading...
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
