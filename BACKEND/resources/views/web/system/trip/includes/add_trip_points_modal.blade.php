<div id="create-trip-point-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Create <span
                        class="text-primary">{!! $trip->name !!}</span>`s Trip Point</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form action="{{ route('trips.create_point', $trip->uuid) }}" id="createTripPointForm"
                name="createTripPointForm" method="POST">
                @csrf
                <div class="modal-body">
                    <h5 style="font-weight: 500;">Fill the form to create trip point</h5>
                    <div class="row mt-3">
                        <div class="col-md-12 col-lg-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-primary btn-sm assignTripPointItemsRow">
                                            <i class="uil uil-plus-circle"></i> Add</button>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <table class="table table-bordered table-centered mb-0"
                                        id="assignTripPointItemsTable">
                                        <thead>
                                            <tr>
                                                <th>Point</th>
                                                <th>Description</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div>
                                                        <div class="col-md-12 col-lg-12">
                                                            <div class="mb-0">
                                                                <input type="text" id="title_0" name="title[]"
                                                                    placeholder="title" class="form-control row-input">
                                                                <small class="text-danger" id="error_title"></small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <div class="col-md-12 col-lg-12">
                                                            <div class="mb-0">
                                                                <textarea id="description_0" name="description[]" placeholder="description" class="form-control row-input"
                                                                    rows="10"></textarea>
                                                                <small class="text-danger"
                                                                    id="error_description"></small>
                                                            </div>
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
                    <button type="button" onclick="submitCreateForm('createTripPointForm')"
                        class="btn btn-success saveBtn">Create Point</button>
                    <button class="btn btn-success btnLoading" type="button" style="display: none" disabled>
                        <span class="spinner-grow spinner-grow-sm me-1" role="point" aria-hidden="true"></span>
                        Loading...
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
