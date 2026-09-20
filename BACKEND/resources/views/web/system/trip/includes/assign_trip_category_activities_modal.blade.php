<div id="create-trip-category-activities-modal" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Create <span
                        class="text-primary">{!! $trip->name !!}</span>`s Trip Category Activity</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form action="{{ route('trips.create_category_activity', $trip->uuid) }}"
                id="createTripCategoryActivityForm" name="createTripCategoryActivityForm" method="POST">
                @csrf
                <div class="modal-body">
                    <h5 style="font-weight: 500;">Fill the form to create trip category activity</h5>
                    <div class="row mt-3">
                        <div class="col-md-12 col-lg-12">
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="trip_category" class="form-label">Category</label>
                                        <select class="form-control select2" id="trip_category" name="trip_category"
                                            data-toggle="select2">
                                            <optgroup label="Trip Categories">
                                                <option>Select category</option>
                                                @foreach ($categories as $category)
                                                    @if (in_array($category['id'], $trip->categories()->pluck('category_id')->toArray()))
                                                        <option value="{{ $category['id'] }}">
                                                            {{ $category['name'] }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </optgroup>
                                        </select>
                                        <small class="text-danger" id="error_trip_category"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-end">
                                        <button type="button"
                                            class="btn btn-primary btn-sm assignTripCategoryActivityItemsRow">
                                            <i class="uil uil-plus-circle"></i> Add</button>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <table class="table table-bordered table-centered mb-0"
                                        id="assignTripCategoryActivityItemsTable">
                                        <thead>
                                            <tr>
                                                <th>Activity</th>
                                                <th>Description</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div>
                                                        <div class="mb-0">
                                                            <select class="form-control select2" id="activity_0"
                                                                name="activity[]" data-toggle="select2">
                                                                <optgroup label="Activities">
                                                                    <option>Select activity</option>
                                                                    @foreach ($activities as $activity)
                                                                        <option value="{{ $activity['id'] }}">
                                                                            {{ $activity['name'] }}</option>
                                                                    @endforeach
                                                                </optgroup>
                                                            </select>
                                                            <small class="text-danger" id="error_activity_0"></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><div>
                                                        <div class="col-md-12 col-lg-12">
                                                            <div class="mb-0">
                                                                <input type="text" id="description_0"
                                                                    name="description[]" placeholder="description"
                                                                    class="form-control row-input">
                                                                <small class="text-danger"
                                                                    id="error_description"></small>
                                                            </div>
                                                        </div>
                                                    </div></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="submitCreateForm('createTripCategoryActivityForm')"
                        class="btn btn-success saveBtn">Create Category</button>
                    <button class="btn btn-success btnLoading" type="button" style="display: none" disabled>
                        <span class="spinner-grow spinner-grow-sm me-1" role="category" aria-hidden="true"></span>
                        Loading...
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
