<div id="edit-faq-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Edit Faq </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form id="editFaqDetailsForm" name="editFaqDetailsForm" method="POST">
                @method('PUT')
                @csrf
                <div class="modal-body">
                    <h5 style="font-weight: 500;">Fill the form to edit faq</h5>
                    <div class="row mt-3">
                        <div class="col-md-12 col-lg-12">
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="category" class="form-label">Category</label>
                                        <select class="form-control select2" id="faq_category" name="category"
                                            data-toggle="select2">
                                            <optgroup label="Categories">
                                                <option>Select category</option>
                                            </optgroup>
                                        </select>
                                        <small class="text-danger" id="error_category"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="order" class="form-label">Order</label>
                                        <input type="number" min="1" id="faq_order" name="order"
                                            placeholder="Enter faq order" class="form-control" required>
                                        <small class="text-danger" id="error_order"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-2">
                                        <label for="question" class="form-label">Question</label>
                                        <textarea rows="5" id="faq_question" name="question" placeholder="Enter question" class="form-control" required></textarea>
                                        <small class="text-danger" id="error_question"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-2">
                                        <label for="answer" class="form-label">Answer</label>
                                        <textarea rows="5" id="faq_answer" name="answer" placeholder="Enter answer" class="form-control" required></textarea>
                                        <small class="text-danger" id="error_answer"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="submitCreateForm('editFaqDetailsForm')"
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
