<div id="change-inquiry-status-modal" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Change <span
                        class="text-primary">{!! $inquiry->inquiry_code !!}</span>`s Inquiry Status</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form action="{{ route('inquiries.change_status', $inquiry->uuid) }}"
                id="changeInquiriestatusForm" name="changeInquiriestatusForm" method="POST">
                @csrf
                <div class="modal-body">
                    <h5 style="font-weight: 500;">Fill the form to change inquiry status</h5>
                    <div class="row mt-3">
                        <div class="col-md-12 col-lg-12">
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-2">
                                        <label for="current_status" class="form-label">Current Status</label>
                                        <select class="form-control select2" id="current_status" name="current_status"
                                            data-toggle="select2">
                                            <optgroup label="Current Status">
                                                <option style="font-weight: bold;" disabled>Select current status
                                                </option>
                                                <option value="{{ $inquiry->is_approved }}" selected>
                                                    @if ($inquiry->is_approved) Approve
                                                    @else
                                                        Pending @endif
                                                </option>
                                            </optgroup>
                                        </select>
                                        <small class="text-danger" id="error_current_status"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-2">
                                        <label for="new_status" class="form-label">New status</label>
                                        <select class="form-control select2" id="new_status" name="new_status"
                                            data-toggle="select2">
                                            <optgroup label="Statuses">
                                                <option style="font-weight: bold;">Select new status
                                                </option>
                                                @if (!$inquiry->is_approved)
                                                    <option value="1">Approve</option>
                                                @else
                                                    <option value="2">Pending</option>
                                                @endif
                                            </optgroup>
                                        </select>
                                        <small class="text-danger" id="error_new_status"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-2">
                                        <label for="comments" class="form-label">Comments</label>
                                        <textarea rows="5" id="comments" name="comments" placeholder="Enter comments" class="form-control" required></textarea>
                                        <small class="text-danger" id="error_comments"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="submitCreateForm('changeInquiriestatusForm')"
                        class="btn btn-success saveBtn">Change Status</button>
                    <button class="btn btn-success btnLoading" type="button" style="display: none" disabled>
                        <span class="spinner-grow spinner-grow-sm me-1" role="status" aria-hidden="true"></span>
                        Loading...
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
