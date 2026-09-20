<div id="edit-trip-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Edit Trip </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form id="editTripDetailsForm" name="editTripDetailsForm" method="POST">
                @method('PUT')
                @csrf
                <div class="modal-body">
                    <h5 style="font-weight: 500;">Fill the form to edit trip </h5>
                    <div class="row mt-3">
                        <div class="col-md-12 col-lg-12">
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" id="trip_name" name="name"
                                            placeholder="Enter trip name" class="form-control" required>
                                        <small class="text-danger" id="error_name"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-2">
                                        <label for="from_date" class="form-label">From Date</label>
                                        <input type="date" id="trip_from_date" min="<?php echo date('Y-m-d'); ?>" name="from_date" placeholder="Enter from date"
                                            class="form-control" required>
                                        <small class="text-danger" id="error_from_date"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-2">
                                        <label for="to_date" class="form-label">To Date</label>
                                        <input type="date" id="trip_to_date" min="<?php echo date('Y-m-d'); ?>" name="to_date" placeholder="Enter to date"
                                            class="form-control" required>
                                        <small class="text-danger" id="error_to_date"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="type" class="form-label">Type</label>
                                        <select class="form-control select2" id="trip_type" name="type"
                                            data-toggle="select2">
                                            <optgroup label="Trip Types">
                                                <option>Select type</option>
                                            </optgroup>
                                        </select>
                                        <small class="text-danger" id="error_type"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="source" class="form-label">Source</label>
                                        <select class="form-control select2" id="trip_source" name="source"
                                            data-toggle="select2">
                                            <optgroup label="Trip Sources">
                                                <option>Select source</option>
                                            </optgroup>
                                        </select>
                                        <small class="text-danger" id="error_source"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-2">
                                        <label for="last_booking_date" class="form-label">Last Booking Date</label>
                                        <input type="date" id="trip_last_booking_date" min="<?php echo date('Y-m-d'); ?>"  name="last_booking_date"
                                            placeholder="Enter last booking date" class="form-control" required>
                                        <small class="text-danger" id="error_last_booking_date"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-2">
                                        <label for="last_payment_date" class="form-label">Last Payment Date</label>
                                        <input type="date" id="trip_last_payment_date" min="<?php echo date('Y-m-d'); ?>" name="last_payment_date"
                                            placeholder="Enter last payment date" class="form-control" required>
                                        <small class="text-danger" id="error_last_payment_date"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-2">
                                        <label for="description" class="form-label">Description</label>
                                        <div id="edit-trip-description-editor" style="height: 170px;"></div>
                                        <input type="hidden" name="description" id="trip_description_edit">
                                        <small class="text-danger" id="error_description"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="submitCreateForm('editTripDetailsForm')"
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
