<div id="create-budget-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Create Budget </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form action="{{ route('budgets.store') }}"
                id="createBudgetForm" name="createBudgetForm" method="POST">
                @csrf
                <div class="modal-body">
                    <h5 style="font-weight: 500;">Fill the form to create new budget </h5>
                    <div class="row mt-3">
                        <div class="col-md-12 col-lg-12">
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <div class="mb-3">
                                        <label for="trip" class="form-label">Trip</label>
                                        <select class="form-control select2" id="trip" name="trip"
                                            data-toggle="select2">
                                            <optgroup label="Trips">
                                                <option>Select trip</option>
                                                @foreach ($trips as $trip)
                                                    <option value="{{ $trip['id'] }}">
                                                        {{ $trip['trip_code'] }}-{{ $trip['name'] }}</option>
                                                @endforeach
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
                                        <select class="form-control select2" id="season" name="season"
                                            data-toggle="select2">
                                            <optgroup label="Seasons">
                                                <option>Select season</option>
                                                @foreach ($seasons as $season)
                                                    <option value="{{ $season['id'] }}">
                                                        {{ $season['name'] }}</option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                        <small class="text-danger" id="error_season"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="class" class="form-label">Class</label>
                                        <select class="form-control select2" id="class" name="class"
                                            data-toggle="select2">
                                            <optgroup label="Classs">
                                                <option>Select class</option>
                                                @foreach ($classes as $class)
                                                    <option value="{{ $class['id'] }}">
                                                        {{ $class['name'] }}</option>
                                                @endforeach
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
                                        <input type="number" id="quantity" name="quantity"
                                            placeholder="Enter quantity" class="form-control" required>
                                        <small class="text-danger" id="error_quantity"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price</label>
                                        <input type="number" id="price" name="price"
                                            placeholder="Enter price" class="form-control" required>
                                        <small class="text-danger" id="error_price"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="currency" class="form-label">Currency</label>
                                        <select class="form-control select2" id="currency" name="currency"
                                            data-toggle="select2">
                                            <optgroup label="Currencies">
                                                <option>Select currency</option>
                                                @foreach ($currencies as $currency)
                                                    <option value="{{ $currency['id'] }}">
                                                        {{ $currency['short_name'] }}</option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                        <small class="text-danger" id="error_currency"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="submitCreateForm('createBudgetForm')"
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
