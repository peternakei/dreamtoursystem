<div id="assign-destination-category-modal" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Assign <span
                        class="text-primary">{!! $destination->name !!}</span>`s Destination Category</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form action="{{ route('destinations.assign_category', $destination->uuid) }}"
                id="assignDestinationCategoryForm" name="assignDestinationCategoryForm" method="POST">
                @csrf
                <div class="modal-body">
                    <h5 style="font-weight: 500;">Fill the form to assign destination category</h5>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-primary btn-sm assignDestinationCategoryItemsRow">
                                            <i class="uil uil-plus-circle"></i> Add</button>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <table class="table table-bordered table-centered mb-0" id="assignDestinationCategoryItemsTable">
                                        <thead>
                                            <tr>
                                                <th>Category</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div>
                                                        <div class="mb-0">
                                                            <select class="form-control select2"
                                                                id="category_0" name="category[]"
                                                                data-toggle="select2">
                                                                <optgroup label="Categories">
                                                                    <option>Select category</option>
                                                                    @foreach ($categories as $category)
                                                                        <option value="{{ $category['id'] }}">
                                                                            {{ $category['name'] }}</option>
                                                                    @endforeach
                                                                </optgroup>
                                                            </select>
                                                            <small class="text-danger"
                                                                id="error_category_0"></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="submitCreateForm('assignDestinationCategoryForm')"
                        class="btn btn-success saveBtn">Assign</button>
                    <button class="btn btn-success btnLoading" type="button" style="display: none" disabled>
                        <span class="spinner-grow spinner-grow-sm me-1" role="status" aria-hidden="true"></span>
                        Loading...
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
