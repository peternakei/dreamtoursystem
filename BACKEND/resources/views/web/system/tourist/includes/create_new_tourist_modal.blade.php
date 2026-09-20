<div id="create-tourist-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Create Tourist </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form action="{{ route('tourists.store') }}"
                id="createTouristForm" name="createTouristForm" method="POST">
                @csrf
                <div class="modal-body">
                    <h5 style="font-weight: 500;">Fill the form to create new tourist </h5>
                    <div class="row mt-3">
                        <div class="col-md-12 col-lg-12">
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" id="name"
                                            name="name" placeholder="Enter tourist name"
                                            class="form-control" required>
                                        <small class="text-danger" id="error_name"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="gender" class="form-label">Gender</label>
                                        <select class="form-control select2" id="gender" name="gender"
                                            data-toggle="select2">
                                            <optgroup label="Genders">
                                                <option>Select gender</option>
                                                @foreach ($genders as $gender)
                                                    <option value="{{ $gender['id'] }}">
                                                        {{ $gender['name'] }}</option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                        <small class="text-danger" id="error_gender"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone</label>
                                        <input type="text" id="phone" name="phone"
                                            placeholder="Enter tourist phone" class="form-control" required>
                                        <small class="text-danger" id="error_phone"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="text" id="email" name="email"
                                            placeholder="Enter tourist email" class="form-control" required>
                                        <small class="text-danger" id="error_email"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="country" class="form-label">Country</label>
                                        <select class="form-control select2" id="country" name="country"
                                            data-toggle="select2">
                                            <optgroup label="Countries">
                                                <option>Select country</option>
                                                @foreach ($countries as $country)
                                                    <option value="{{ $country['id'] }}">
                                                        {{ $country['name'] }}</option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                        <small class="text-danger" id="error_country"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <input type="text" id="address" name="address"
                                            placeholder="Enter tourist address" class="form-control" required>
                                        <small class="text-danger" id="error_address"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="submitCreateForm('createTouristForm')"
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
