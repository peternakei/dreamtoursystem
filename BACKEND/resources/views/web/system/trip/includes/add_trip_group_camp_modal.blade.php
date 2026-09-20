<div id="create-trip-group-camp-modal" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Create <span
                        class="text-primary">{!! $trip->name !!}</span>`s Trip Group Camp</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form action="{{ route('trips.create_group_camp', $trip->uuid) }}"
                id="createTripGroupCampForm" name="createTripGroupCampForm" method="POST">
                @csrf
                <div class="modal-body">
                    <h5 style="font-weight: 500;">Fill the form to create trip group camp</h5>
                    <div class="row mt-3">
                        <div class="col-md-12 col-lg-12">
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <div class="mb-3">
                                        <label for="group" class="form-label">Group</label>
                                        <select class="form-control select2" id="group" name="group"
                                            data-toggle="select2">
                                            <optgroup label="Groups">
                                                <option>Select group</option>
                                                @foreach ($tripGroups as $group)
                                                @if (in_array($group['id'],$trip->groups()->pluck('id')->toArray()))
                                                    <option value="{{ $group['id'] }}">
                                                        {{ $group->trip->trip_code }}/{{ $group['group'] }}</option>
                                                @endif
                                                @endforeach
                                            </optgroup>
                                        </select>
                                        <small class="text-danger" id="error_group"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="camp" class="form-label">Camp</label>
                                        <input type="text" id="camp" name="camp"
                                            placeholder="Enter group camp" class="form-control" required>
                                        <small class="text-danger" id="error_camp"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="day" class="form-label">Day</label>
                                        <input type="number" id="day" name="day"
                                            placeholder="Enter day" class="form-control" required>
                                        <small class="text-danger" id="error_day"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-2">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea rows="5" id="description" name="description" placeholder="Enter description" class="form-control" required></textarea>
                                        <small class="text-danger" id="error_description"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="submitCreateForm('createTripGroupCampForm')"
                        class="btn btn-success saveBtn">Create Group</button>
                    <button class="btn btn-success btnLoading" type="button" style="display: none" disabled>
                        <span class="spinner-grow spinner-grow-sm me-1" role="group" aria-hidden="true"></span>
                        Loading...
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
