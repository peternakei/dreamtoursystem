@extends('layouts.app')
@section('content')
    <div class="row mt-3">
        <div class="col-md-12 col-lg-12">
            <div class="back-button">
                <a href="{{ route('users.index') }}" class="text-muted"><span style="font-size: 1.5em;"><i
                            class="uil uil-arrow-circle-left"></i></span></a>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="page-title" style="font-size: 1.5em;">Setting</h4>
            </div>
            <div class="row mt-3">
                <div class="col-md-8 col-lg-8">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <ul class="nav nav-tabs nav-justified nav-bordered mb-3">
                                    <li class="nav-item">
                                        <a href="#profile" data-bs-toggle="tab" aria-expanded="true"
                                            class="nav-link active">
                                            <i class="mdi mdi-account-circle d-md-none d-block"></i>
                                            <span class="d-none d-md-block">Profile</span>
                                        </a>
                                    <li class="nav-item">
                                        <a href="#password" data-bs-toggle="tab" aria-expanded="true" class="nav-link">
                                            <i class="mdi mdi-account-circle d-md-none d-block"></i>
                                            <span class="d-none d-md-block">Change Password</span>
                                        </a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane show active" id="profile">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-12" data-simplebar style="max-height: 350px;">
                                                <form action="{{ route('users.update', $user->uuid) }}"
                                                    id="updateUserProfileForm" name="updateUserProfileForm" method="post">
                                                    @method('PUT')
                                                    @csrf
                                                    <div class="modal-body">
                                                        <h5 style="font-weight: 500;">Fill the form to update profile </h5>
                                                        <div class="row mt-3">
                                                            <div class="col-md-12 col-lg-12">
                                                                <div class="row">
                                                                    <div class="col-md-12 col-lg-12">
                                                                        <div class="mb-3">
                                                                            <label for="name"
                                                                                class="form-label">Name</label>
                                                                            <input type="text" id="user_name"
                                                                                name="name" value="{{ $user->name }}"
                                                                                placeholder="Enter user name"
                                                                                class="form-control" required>
                                                                            <small class="text-danger"
                                                                                id="error_name"></small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6 col-lg-6">
                                                                        <div class="mb-3">
                                                                            <label for="phone"
                                                                                class="form-label">Phone</label>
                                                                            <input type="number"
                                                                                value="{{ $user->phone }}" id="user_phone"
                                                                                name="phone"
                                                                                placeholder="Enter user phone"
                                                                                class="form-control" required>
                                                                            <small class="text-danger"
                                                                                id="error_phone"></small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6 col-lg-6">
                                                                        <div class="mb-3">
                                                                            <label for="email"
                                                                                class="form-label">Email</label>
                                                                            <input type="email"
                                                                                value="{{ $user->email }}" id="user_email"
                                                                                name="email"
                                                                                placeholder="Enter user email"
                                                                                class="form-control" required>
                                                                            <small class="text-danger"
                                                                                id="error_email"></small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button type="button"
                                                        onclick="submitCreateForm('updateUserProfileForm')"
                                                        class="btn btn-success saveBtn">Update details</button>
                                                    <button class="btn btn-success btnLoading" type="button"
                                                        style="display: none" disabled>
                                                        <span class="spinner-grow spinner-grow-sm me-1" user=""
                                                            aria-hidden="true"></span>
                                                        Loading...
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane show" id="password">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-12" data-simplebar style="max-height: 350px;">
                                                <form action="{{ route('users.update_password', $user->uuid) }}"
                                                    id="changeUserPasswordForm" name="changeUserPasswordForm" method="post">

                                                    @csrf
                                                    <div class="modal-body">
                                                        <h5 style="font-weight: 500;">Fill the form to change password </h5>
                                                        <div class="row mt-3">
                                                            <div class="col-md-12 col-lg-12">
                                                                <div class="row">
                                                                    <div class="col-md-6 col-lg-6">
                                                                        <div class="mb-3">
                                                                            <label for="password"
                                                                                class="form-label">Password</label>
                                                                            <div class="input-group">
                                                                                <input type="password" id="password"
                                                                                    name="password"
                                                                                    placeholder="Enter new password"
                                                                                    class="form-control" required>
                                                                                <button
                                                                                    class="btn btn-light password-toggle-btn"
                                                                                    type="button"
                                                                                    data-password-toggle="password"
                                                                                    aria-label="Show password">
                                                                                    <i class="mdi mdi-eye-outline"></i>
                                                                                </button>
                                                                            </div>
                                                                            <small class="text-danger"
                                                                                id="error_password"></small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6 col-lg-6">
                                                                        <div class="mb-3">
                                                                            <label for="password_confirm"
                                                                                class="form-label">Confirm Password</label>
                                                                            <div class="input-group">
                                                                                <input type="password"
                                                                                    id="password_confirm"
                                                                                    name="password_confirm"
                                                                                    placeholder="Enter confirm password"
                                                                                    class="form-control" required>
                                                                                <button
                                                                                    class="btn btn-light password-toggle-btn"
                                                                                    type="button"
                                                                                    data-password-toggle="password_confirm"
                                                                                    aria-label="Show password">
                                                                                    <i class="mdi mdi-eye-outline"></i>
                                                                                </button>
                                                                            </div>
                                                                            <small class="text-danger"
                                                                                id="error_password_confirm"></small>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button type="button"
                                                        onclick="submitCreateForm('changeUserPasswordForm')"
                                                        class="btn btn-success saveBtn">Change Password</button>
                                                    <button class="btn btn-success btnLoading" type="button"
                                                        style="display: none" disabled>
                                                        <span class="spinner-grow spinner-grow-sm me-1" user=""
                                                            aria-hidden="true"></span>
                                                        Loading...
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
