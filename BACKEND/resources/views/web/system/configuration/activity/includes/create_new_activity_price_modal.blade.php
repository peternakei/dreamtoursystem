<div id="create-activity-price-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Create <span
                        class="text-primary">{!! $activity->name !!}</span>`s Activity Price</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form action="{{ route('activities.create_price', $activity->uuid) }}" id="createActivityPriceForm"
                name="createActivityPriceForm" method="POST">
                @csrf
                <div class="modal-body">
                    <h5 style="font-weight: 500;">Fill the form to create activity price</h5>
                    <div class="row mt-3">
                        <div class="col-md-12 col-lg-12">
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <div class="mb-2">
                                        <label for="age" class="form-label">Age</label>
                                        <select class="form-control select2" id="age" name="age"
                                            data-toggle="select2">
                                            <optgroup label="age">
                                                <option>Select age</option>
                                                @foreach ($ageGroups as $age)
                                                    <option value="{{ $age['id'] }}">
                                                        {{ $age['name'] }}</option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                        <small class="text-danger" id="error_age"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-2">
                                        <label for="duration" class="form-label">Duration</label>
                                        <input type="number" min="1" id="duration" name="duration"
                                            placeholder="Enter duration" class="form-control" required>
                                        <small class="text-danger" id="error_duration"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-2">
                                        <label for="type" class="form-label">Type</label>
                                        <select class="form-control select2" id="type" name="type"
                                            data-toggle="select2">
                                            <optgroup label="Duration Types">
                                                <option>Select type</option>
                                                @foreach ($durationTypes as $type)
                                                    <option value="{{ $type['id'] }}">
                                                        {{ $type['name'] }}</option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                        <small class="text-danger" id="error_type"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-2">
                                        <label for="price" class="form-label">Price</label>
                                        <input type="number" min="1" id="price" name="price"
                                            placeholder="Enter price" class="form-control" required>
                                        <small class="text-danger" id="error_price"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-2">
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
                    <button type="button" onclick="submitCreateForm('createActivityPriceForm')"
                        class="btn btn-success saveBtn">Create Price</button>
                    <button class="btn btn-success btnLoading" type="button" style="display: none" disabled>
                        <span class="spinner-grow spinner-grow-sm me-1" role="activity" aria-hidden="true"></span>
                        Loading...
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
