@extends('layouts.login')
@section('content')
    <div class="auth-fluid"
        style="background: url({{ asset('assets/images/background.jpg') }}); background-size:cover; background-position: center;">
        <!--Auth fluid left content -->
        <div class="auth-fluid-form-box" style="background-color: rgba(12, 149, 212,1);">
            <div class="card-body d-flex flex-column h-100 gap-3">
                <!-- Logo -->
                <div class="auth-brand text-center text-lg-start">
                    <a href="index.html" class="logo-dark">
                        <span><img src="{{ asset('images/dream-logo.png') }}" alt="dark logo" height="60"></span>
                    </a>
                    <a href="index.html" class="logo-light">
                        <span><img src="{{ asset('images/dream-logo.png') }}" alt="logo" height="50"></span>
                    </a>
                </div>
                <div class="my-auto">
                    <!-- title-->
                    <h4 class="mt-0 text-white">Reset Password</h4>
                    <p class="mb-4 text-white">Fill the form to reset your password.</p>
                    <!-- form -->
                    <form action="{{ route('post_reset_password') }}" id="postResetPasswordForm"
                        name="postResetPasswordForm" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label text-white">Email address</label>
                            <input class="form-control" type="email" name="email" id="email" required
                                placeholder="Enter your email" value="{{ $email }}" readonly>
                            <small class="text-danger" id="email_error"></small>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label text-white">Password</label>
                            <div class="input-group">
                                <input class="form-control" type="password" name="password" id="password"
                                    placeholder="Enter your password" autocomplete="new-password" required>
                                <button class="btn btn-light password-toggle-btn" type="button"
                                    data-password-toggle="password" aria-label="Show password">
                                    <i class="mdi mdi-eye-outline"></i>
                                </button>
                            </div>
                            <small class="text-danger" id="error_password"
                                style="display: none; font-weight: 600; font-size: 0.85em;"><i class="uil-lock"></i> <span
                                    id="password_message"></span></small>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label text-white">Confirm Password</label>
                            <div class="input-group">
                                <input class="form-control" type="password" name="password_confirmation"
                                    id="password_confirmation" placeholder="Confirm your password"
                                    autocomplete="new-password" required>
                                <button class="btn btn-light password-toggle-btn" type="button"
                                    data-password-toggle="password_confirmation" aria-label="Show password">
                                    <i class="mdi mdi-eye-outline"></i>
                                </button>
                            </div>
                            <small class="text-danger" id="error_password_confirmation"
                                style="display: none; font-weight: 600; font-size: 0.85em;"><i class="uil-lock"></i> <span
                                    id="password_confirmation_message"></span></small>
                        </div>
                        <input type="hidden" name="reset_token" value="{{ $token }}">
                        <div class="d-grid mb-0 text-center">
                            <button class="btn btn-primary saveBtn" onclick="submitCreateForm('postResetPasswordForm')"
                                type="button"><i class="mdi mdi-login"></i>
                                <span>Reset Password</span> </button>
                            <button class="btn btn-success btnLoading" type="button" style="display: none" disabled>
                                <span class="spinner-grow spinner-grow-sm me-1" role="" aria-hidden="true"></span>
                                Please wait...
                            </button>
                        </div>
                    </form>
                    <!-- end form-->
                </div>
                <!-- Footer-->
                <footer class="footer footer-alt text-white">
                    © 2025 -
                    <script>
                        document.write(new Date().getFullYear())
                    </script> | Dream Travel and Tours
                </footer>

            </div> <!-- end .card-body -->
        </div>
        <!-- end auth-fluid-form-box-->
    </div>
@endsection
