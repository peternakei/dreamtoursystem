<div id="create-trip-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Create Trip </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form action="{{ route('trips.store') }}" id="createTripForm" name="createTripForm" method="POST">
                @csrf
                <div class="modal-body">
                    <h5 style="font-weight: 500;">Fill the form to create new trip </h5>
                    <div class="row mt-3">
                        <div class="col-md-12 col-lg-12">
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" id="name" name="name"
                                            placeholder="Enter trip name" class="form-control" required>
                                        <small class="text-danger" id="error_name"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-2">
                                        <label for="from_date" class="form-label">From Date</label>
                                        <input type="date" id="from_date" min="<?php echo date('Y-m-d'); ?>" name="from_date"
                                            placeholder="Enter from date" class="form-control" required>
                                        <small class="text-danger" id="error_from_date"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-2">
                                        <label for="to_date" class="form-label">To Date</label>
                                        <input type="date" id="to_date" min="<?php echo date('Y-m-d'); ?>" name="to_date"
                                            placeholder="Enter to date" class="form-control" required>
                                        <small class="text-danger" id="error_to_date"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="type" class="form-label">Type</label>
                                        <select class="form-control select2" id="type" name="type"
                                            data-toggle="select2">
                                            <optgroup label="Trip Types">
                                                <option>Select type</option>
                                                @foreach ($tripTypes as $type)
                                                    <option value="{{ $type['id'] }}">
                                                        {{ $type['name'] }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                        <small class="text-danger" id="error_type"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="source" class="form-label">Source</label>
                                        <select class="form-control select2" id="source" name="source"
                                            data-toggle="select2">
                                            <optgroup label="Trip Sources">
                                                <option>Select source</option>
                                                @foreach ($tripSources as $source)
                                                    <option value="{{ $source['id'] }}">
                                                        {{ $source['name'] }}
                                                    </option>
                                                @endforeach
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
                                        <input type="date" id="last_booking_date" min="<?php echo date('Y-m-d'); ?>"
                                            name="last_booking_date" placeholder="Enter last booking date"
                                            class="form-control" required>
                                        <small class="text-danger" id="error_last_booking_date"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-2">
                                        <label for="last_payment_date" class="form-label">Last Payment Date</label>
                                        <input type="date" id="last_payment_date" min="<?php echo date('Y-m-d'); ?>"
                                            name="last_payment_date" placeholder="Enter last payment date"
                                            class="form-control" required>
                                        <small class="text-danger" id="error_last_payment_date"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-2">
                                        <label for="description" class="form-label">Description</label>
                                        <div id="snow-editor" style="height: 170px;"></div>
                                        <input type="hidden" name="description" id="trip_description">
                                        <small class="text-danger" id="error_description"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="submitCreateForm('createTripForm')"
                        class="btn btn-success saveBtn">Save details</button>
                    <button class="btn btn-success btnLoading" type="button" style="display: none" disabled>
                        <span class="spinner-grow spinner-grow-sm me-1" role="" aria-hidden="true"></span>
                        Loading...
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let quillEditor = null;

        // Initialize Quill editor when modal is shown
        $('#create-trip-modal').on('shown.bs.modal', function() {
            if (!quillEditor) {
                quillEditor = new Quill("#snow-editor", {
                    theme: "snow",
                    modules: {
                        toolbar: [
                            [{
                                font: []
                            }, {
                                size: []
                            }],
                            ["bold", "italic", "underline", "strike"],
                            [{
                                color: []
                            }, {
                                background: []
                            }],
                            [{
                                script: "super"
                            }, {
                                script: "sub"
                            }],
                            [{
                                header: [false, 1, 2, 3, 4, 5, 6]
                            }, "blockquote", "code-block"],
                            [{
                                list: "ordered"
                            }, {
                                list: "bullet"
                            }, {
                                indent: "-1"
                            }, {
                                indent: "+1"
                            }],
                            ["direction", {
                                align: []
                            }],
                            ["link", "image", "video"],
                            ["clean"]
                        ]
                    }
                });
            }
        });

        // Clear Quill editor when modal is hidden
        $('#create-trip-modal').on('hidden.bs.modal', function() {
            if (quillEditor) {
                quillEditor.setContents([]);
            }
        });
    });
</script>
