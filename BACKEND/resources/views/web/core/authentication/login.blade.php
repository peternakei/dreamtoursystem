@extends('layouts.login')
@section('content')
    <div class="auth-fluid"
        style="background: url('{{ asset('assets/images/background.jpg') }}'); background-size:cover; background-position: center;">
        <!--Auth fluid left content -->
        <div class="auth-fluid-form-box" style="background-color: #288479;">
            <div class="card-body d-flex flex-column h-100 gap-3">
                <!-- Logo -->
                <div class="auth-brand text-center text-lg-start">
                    <a href="index.html" class="logo-dark">
                        <span><img src="{{ asset('images/dream-logo.png') }}" alt="dark logo" height="100"></span>
                    </a>
                    <a href="index.html" class="logo-light">
                        <span><img src="{{ asset('images/dream-logo.png') }}" alt="logo" height="20"></span>
                    </a>
                </div>
                <div class="my-auto">
                    <!-- title-->
                    <h4 class="mt-0 text-white">Sign In</h4>
                    <p class="mb-4 text-white">Enter your email address and password to login.</p>
                    <!-- form -->
                    <form action="{{ route('authenticate-user') }}" id="userLoginForm" name="userLoginForm" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="username" class="form-label text-white">Email address</label>
                            <input class="form-control" type="email" name="username" id="username" required
                                placeholder="Enter your email">
                            <small class="text-danger" id="error_username"
                                style="display: none; font-weight: 600; font-size: 0.85em;"><i
                                    class="uil-exclamation-triangle"></i> <span id="username_message"></span></small>
                        </div>
                        <div class="mb-3">
                            <a href="{{ url('forgot-password') }}" class="float-end text-white"><small>Forgot your
                                    password?</small></a>
                            <label for="password" class="form-label text-white">Password</label>
                            <div class="input-group">
                                <input class="form-control" type="password" required name="password" id="password"
                                    placeholder="Enter your password">
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
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="remember" value="true"
                                    id="checkbox-signin">
                                <label class="form-check-label text-white" for="checkbox-signin">Remember me</label>
                            </div>
                        </div>
                        <div class="d-grid mb-0 text-center">
                            <button class="btn btn-primary saveBtn" onclick="submitCreateLoginForm('userLoginForm')"
                                type="button"><i class="mdi mdi-login"></i>
                                <span>Log In</span> </button>
                            <button class="btn btn-success btnLoading" type="button" style="display: none" disabled>
                                <span class="spinner-grow spinner-grow-sm me-1" role="" aria-hidden="true"></span>
                                Please wait...
                            </button>
                        </div>
                    </form>
                </div>
                <footer class="footer footer-alt text-white">
                    © 2025 -
                    <script>
                        document.write(new Date().getFullYear())
                    </script> | Dream Travel and Tours
                </footer>
            </div>
        </div>
    </div>
@endsection
