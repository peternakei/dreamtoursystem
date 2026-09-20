<div id="create-destination-fact-modal" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Create <span
                        class="text-primary">{!! $destination->name !!}</span>`s Destination Fact</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form action="{{ route('destinations.create_fact', $destination->uuid) }}"
                id="createDestinationFactForm" name="createDestinationFactForm" method="POST">
                @csrf
                <div class="modal-body">
                    <h5 style="font-weight: 500;">Fill the form to create destination fact</h5>
                    <div class="row mt-3">
                        <div class="col-md-12 col-lg-12">
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="fact" class="form-label">Fact</label>
                                        <input type="text" id="fact" name="fact"
                                            placeholder="Enter destination fact" class="form-control" required>
                                        <small class="text-danger" id="error_fact"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="sub_fact" class="form-label">Sub Fact</label>
                                        <input type="text" id="sub_fact" name="sub_fact"
                                            placeholder="Enter destination sub fact" class="form-control" required>
                                        <small class="text-danger" id="error_sub_fact"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-2">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea rows="5" id="description" name="description" placeholder="Enter description" class="form-control" required></textarea>
                                        <small class="text-danger" id="error_description"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="submitCreateForm('createDestinationFactForm')"
                        class="btn btn-success saveBtn">Create Fact</button>
                    <button class="btn btn-success btnLoading" type="button" style="display: none" disabled>
                        <span class="spinner-grow spinner-grow-sm me-1" role="fact" aria-hidden="true"></span>
                        Loading...
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
