<div id="create-trip-price-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Create <span
                        class="text-primary">{!! $trip->name !!}</span>`s Trip Price</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form action="{{ route('trips.create_price', $trip->uuid) }}" id="createTripPriceForm"
                name="createTripPriceForm" method="POST">
                @csrf
                <div class="modal-body">
                    <h5 style="font-weight: 500;">Fill the form to create trip price</h5>
                    <div class="row mt-3">
                        <div class="col-md-12 col-lg-12">
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="age" class="form-label">Age Group</label>
                                        <select class="form-control select2" id="age" name="age"
                                            data-toggle="select2">
                                            <optgroup label="Age Groups">
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
                                @if ($trip->trip_type_id == 2)
                                    <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="group" class="form-label">Trip Group</label>
                                        <select class="form-control select2" id="group" name="group"
                                            data-toggle="select2">
                                            <optgroup label="Group Groups">
                                                <option>Select group</option>
                                                @foreach ($tripGroups as $group)
                                                    @if (in_array($group['id'], $trip->groups()->pluck('id')->toArray()))
                                                        <option value="{{ $group['id'] }}">
                                                            {{ $group->trip->trip_code }}/{{ $group['group'] }}</option>
                                                    @endif
                                                @endforeach
                                            </optgroup>
                                        </select>
                                        <small class="text-danger" id="error_group"></small>
                                        <small class="text-danger"><i>Select group if you want to configure group
                                            price</i></small>
                                    </div>
                                </div>
                                @endif
                                
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price</label>
                                        <input type="number" id="price" name="price"
                                            placeholder="Enter trip price" class="form-control" required>
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
                    <button type="button" onclick="submitCreateForm('createTripPriceForm')"
                        class="btn btn-success saveBtn">Create Price</button>
                    <button class="btn btn-success btnLoading" type="button" style="display: none" disabled>
                        <span class="spinner-grow spinner-grow-sm me-1" role="price" aria-hidden="true"></span>
                        Loading...
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
