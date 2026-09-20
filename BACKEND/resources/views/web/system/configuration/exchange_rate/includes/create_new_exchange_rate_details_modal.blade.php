<div id="create-exchange-rate-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Create Exchange Rate </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form action="{{ route('exchange_rates.store') }}" id="createExchangeRateDetailsForm" name="createExchangeRateDetailsForm"
                method="POST">
                @csrf
                <div class="modal-body">
                    <h5 style="font-weight: 500;">Fill the form to create new exchange rate</h5>
                    <div class="row mt-3">
                        <div class="col-md-12 col-lg-12">
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="rate" class="form-label">Rate</label>
                                        <input type="number" id="rate" name="rate"
                                            placeholder="Enter rate" class="form-control" required>
                                        <small class="text-danger" id="error_rate"></small>
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
                    <button type="button" onclick="submitCreateForm('createExchangeRateDetailsForm')"
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
