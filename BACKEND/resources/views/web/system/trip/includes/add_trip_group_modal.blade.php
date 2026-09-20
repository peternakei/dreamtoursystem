<div id="create-trip-group-modal" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Create <span
                        class="text-primary">{!! $trip->name !!}</span>`s Trip Group</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form action="{{ route('trips.create_group', $trip->uuid) }}"
                id="createTripGroupForm" name="createTripGroupForm" method="POST">
                @csrf
                <div class="modal-body">
                    <h5 style="font-weight: 500;">Fill the form to create trip group</h5>
                    <div class="row mt-3">
                        <div class="col-md-12 col-lg-12">
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="group" class="form-label">Group</label>
                                        <input type="text" id="group" name="group"
                                            placeholder="Enter trip group" class="form-control" required>
                                        <small class="text-danger" id="error_group"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="size" class="form-label">Size</label>
                                        <input type="number" id="size" name="size"
                                            placeholder="Enter trip group" class="form-control" required>
                                        <small class="text-danger" id="error_size"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="color" class="form-label">Color</label>
                                        <input type="color" id="color" name="color"
                                            placeholder="Enter trip group color" class="form-control" required>
                                        <small class="text-danger" id="error_color"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="day" class="form-label">Days</label>
                                        <input type="number" id="day" name="day"
                                            placeholder="Enter days" class="form-control" required>
                                        <small class="text-danger" id="error_day"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <div class="mb-2">
                                        <label for="departure_date" class="form-label">Departure Date</label>
                                        <input type="date" id="departure_date" min="<?php echo date('Y-m-d'); ?>"  name="departure_date"
                                            placeholder="Enter departure date" class="form-control" required>
                                        <small class="text-danger" id="error_departure_date"></small>
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
                    <button type="button" onclick="submitCreateForm('createTripGroupForm')"
                        class="btn btn-success saveBtn">Create Group</button>
                    <button class="btn btn-success btnLoading" type="button" style="display: none" disabled>
                        <span class="spinner-grow spinner-grow-sm me-1" role="group" aria-hidden="true"></span>
                        Loading...
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
