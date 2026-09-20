<div id="edit-budget-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Edit Budget </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form id="editBudgetForm" name="editBudgetForm" method="POST">
                @method('PUT')
                @csrf
                <div class="modal-body">
                    <h5 style="font-weight: 500;">Fill the form to edit budget </h5>
                    <div class="row mt-3">
                        <div class="col-md-12 col-lg-12">
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <div class="mb-3">
                                        <label for="trip" class="form-label">Trip</label>
                                        <select class="form-control select2" id="budget_trip" name="trip"
                                            data-toggle="select2">
                                            <optgroup label="Trips">
                                                <option style="font-weight: bold;">Select trip
                                                </option>
                                            </optgroup>
                                        </select>
                                        <small class="text-danger" id="error_trip"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="season" class="form-label">Season</label>
                                        <select class="form-control select2" id="budget_season" name="season"
                                            data-toggle="select2">
                                            <optgroup label="Seasons">
                                                <option style="font-weight: bold;">Select season
                                                </option>
                                            </optgroup>
                                        </select>
                                        <small class="text-danger" id="error_season"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="class" class="form-label">Class</label>
                                        <select class="form-control select2" id="budget_class" name="class"
                                            data-toggle="select2">
                                            <optgroup label="Classes">
                                                <option style="font-weight: bold;">Select class
                                                </option>
                                            </optgroup>
                                        </select>
                                        <small class="text-danger" id="error_class"></small>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <div class="mb-3">
                                        <label for="quantity" class="form-label">Quantity</label>
                                        <input type="number" id="budget_quantity" name="quantity"
                                            placeholder="Enter quantity" class="form-control" required>
                                        <small class="text-danger" id="error_quantity"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price</label>
                                        <input type="number" id="budget_price" name="price" placeholder="Enter price"
                                            class="form-control" required>
                                        <small class="text-danger" id="error_price"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="currency" class="form-label">Currency</label>
                                        <select class="form-control select2" id="budget_currency" name="currency"
                                            data-toggle="select2">
                                            <optgroup label="Currencies">
                                                <option style="font-weight: bold;">Select currency
                                                </option>
                                            </optgroup>
                                        </select>
                                        <small class="text-danger" id="error_currency"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="input-hidden" id="hidden_input"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="submitCreateForm('editBudgetForm')"
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
