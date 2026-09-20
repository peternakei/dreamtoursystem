@extends('layouts.login')
@section('content')
    <div class="auth-fluid"
        style="background: url('{{ asset('assets/images/background.jpg') }}'); background-size:cover; background-position: center;">
        <div class="auth-fluid-form-box" style="background-color: var(--brand-accent-deep);">
            <div class="card-body d-flex flex-column h-100 gap-3">
                <div class="auth-brand text-center text-lg-start">
                    <a href="{{ route('dashboard.user') }}" class="logo-dark">
                        <span><img src="{{ asset('images/dream-logo.png') }}" alt="Dream Travel and Tours" height="100"></span>
                    </a>
                    <a href="{{ route('dashboard.user') }}" class="logo-light">
                        <span><img src="{{ asset('images/dream-logo.png') }}" alt="Dream Travel and Tours" height="50"></span>
                    </a>
                </div>

                <div class="my-auto">
                    <h4 class="mt-0 text-white">Change Password</h4>
                    <p class="mb-4 text-white">For your first sign in, set a private password before continuing.</p>

                    <form action="{{ route('password.force.update') }}" id="forcePasswordChangeForm"
                        name="forcePasswordChangeForm" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="password" class="form-label text-white">New Password</label>
                            <div class="input-group">
                                <input class="form-control" type="password" name="password" id="password"
                                    placeholder="Enter a new password" autocomplete="new-password" required>
                                <button class="btn btn-light password-toggle-btn" type="button"
                                    data-password-toggle="password" aria-label="Show password">
                                    <i class="mdi mdi-eye-outline"></i>
                                </button>
                            </div>
                            <small class="text-danger" id="error_password"></small>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirm" class="form-label text-white">Confirm Password</label>
                            <div class="input-group">
                                <input class="form-control" type="password" name="password_confirm" id="password_confirm"
                                    placeholder="Confirm your new password" autocomplete="new-password" required>
                                <button class="btn btn-light password-toggle-btn" type="button"
                                    data-password-toggle="password_confirm" aria-label="Show password">
                                    <i class="mdi mdi-eye-outline"></i>
                                </button>
                            </div>
                            <small class="text-danger" id="error_password_confirm"></small>
                        </div>
                        <div class="d-grid mb-0 text-center">
                            <button class="btn btn-primary saveBtn" onclick="submitCreateForm('forcePasswordChangeForm')"
                                type="button"><i class="mdi mdi-lock-reset"></i>
                                <span>Update Password</span></button>
                            <button class="btn btn-success btnLoading" type="button" style="display: none" disabled>
                                <span class="spinner-grow spinner-grow-sm me-1" aria-hidden="true"></span>
                                Please wait...
                            </button>
                        </div>
                    </form>
                </div>

                <footer class="footer footer-alt text-white">
                    &copy; 2025 -
                    <script>
                        document.write(new Date().getFullYear())
                    </script> | Dream Travel and Tours
                </footer>
            </div>
        </div>
    </div>
@endsection
