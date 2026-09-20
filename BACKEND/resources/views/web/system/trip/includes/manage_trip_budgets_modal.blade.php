<div id="manage-trip-budgets-modal" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Manage <span
                        class="text-primary">{!! $trip->name !!}</span>`s Budgets</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form action="{{ route('trips.create_budget_matrix', $trip->uuid) }}" id="manageTripBudgetsForm"
                name="manageTripBudgetsForm" method="POST">
                @csrf
                <div class="modal-body">
                    <h5 style="font-weight: 500;">Fill the budget matrix once instead of saving one season/class pair at a
                        time</h5>
                    <div class="alert alert-info mt-3 mb-3">
                        Use one quantity and one currency for this batch, then enter prices only in the season/class cells
                        you want to save.
                        Public base price currently reads from the active <strong>Low / Economy / Quantity 6</strong>
                        budget.
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <div class="mb-3">
                                <label for="trip_budget_currency" class="form-label">Currency</label>
                                <select class="form-control select2" id="trip_budget_currency" name="currency"
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
                        <div class="col-md-6 col-lg-6">
                            <div class="mb-3">
                                <label for="trip_budget_quantity" class="form-label">Quantity</label>
                                <input type="number" id="trip_budget_quantity" name="quantity"
                                    placeholder="Enter quantity" class="form-control" value="6" min="1" required>
                                <small class="text-danger" id="error_quantity"></small>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead style="background-color: #e9ecef;">
                                <tr>
                                    <th style="min-width: 180px;">Season</th>
                                    @foreach ($classes as $class)
                                        <th style="min-width: 180px;">{{ $class->name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($seasons as $season)
                                    <tr>
                                        <td style="font-weight: 600;">{{ $season->name }}</td>
                                        @foreach ($classes as $class)
                                            @php
                                                $existingBudget = $trip->budgets
                                                    ->where('is_active', true)
                                                    ->where('quantity', 6)
                                                    ->first(function ($budget) use ($season, $class) {
                                                        return (int) $budget->season_id === (int) $season->id
                                                            && (int) $budget->service_class_id === (int) $class->id;
                                                    });
                                            @endphp
                                            <td>
                                                <input type="number" step="0.01" min="0"
                                                    name="prices[{{ $season->id }}][{{ $class->id }}]"
                                                    class="form-control"
                                                    value="{{ $existingBudget?->price }}"
                                                    placeholder="Enter price">
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <small class="text-muted d-block mt-2">
                        Leaving a cell blank means no change for that season/class pair in this submission.
                    </small>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="submitCreateForm('manageTripBudgetsForm')"
                        class="btn btn-success saveBtn">Save Budget Matrix</button>
                    <button class="btn btn-success btnLoading" type="button" style="display: none" disabled>
                        <span class="spinner-grow spinner-grow-sm me-1" role="status" aria-hidden="true"></span>
                        Loading...
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
