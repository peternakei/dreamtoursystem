<div id="edit-menu-modal" class="modal fade" tabindex="-1" menu="dialog" aria-labelledby="standard-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Edit Menu </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form id="editMenuForm" name="editMenuForm" method="POST">
                @method('PUT')
                @csrf
                <div class="modal-body">
                    <h5 style="font-weight: 500;">Fill the form to edit new menu </h5>
                    <div class="row mt-3">
                        <div class="col-md-12 col-lg-12">
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" id="menu_name" name="name"
                                            placeholder="Enter menu name" class="form-control" required>
                                        <small class="text-danger" id="error_name"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Title</label>
                                        <input type="text" id="menu_title" name="title"
                                            placeholder="Enter menu title" class="form-control" required>
                                        <small class="text-danger" id="error_title"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="url" class="form-label">Url</label>
                                        <input type="text" id="menu_url" name="url" placeholder="Enter menu url"
                                            class="form-control" required>
                                        <small class="text-danger" id="error_url"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="icon" class="form-label">Icon</label>
                                        <input type="text" id="menu_icon" name="icon"
                                            placeholder="Enter menu icon" class="form-control" required>
                                        <small class="text-danger" id="error_icon"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="ordering" class="form-label">Ordering</label>
                                        <input type="number" id="menu_ordering" name="ordering"
                                            placeholder="Enter menu ordering" class="form-control" required>
                                        <small class="text-danger" id="error_ordering"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="parent" class="form-label">Parent Menu</label>
                                        <select class="form-control select2" id="parent" name="parent"
                                            data-toggle="select2">
                                            <optgroup label="Parent">
                                                <option>Select parent menu</option>
                                                @foreach ($menus as $menu)
                                                    <option value="{{ $menu['id'] }}">
                                                        {{ $menu['name'] }}</option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                        <small class="text-danger" id="error_parent"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="submitCreateForm('editMenuForm')"
                        class="btn btn-success saveBtn">Update details</button>
                    <button class="btn btn-success btnLoading" type="button" style="display: none" disabled>
                        <span class="spinner-grow spinner-grow-sm me-1" menu="" aria-hidden="true"></span>
                        Loading...
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
