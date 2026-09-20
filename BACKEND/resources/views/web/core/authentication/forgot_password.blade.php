@extends('layouts.login')
@section('content')
    <div class="auth-fluid"
        style="background: url({{ asset('assets/images/background.jpg') }}); background-size:cover; background-position: center;">
        <!--Auth fluid left content -->
        <div class="auth-fluid-form-box" style="background-color: rgba(12, 149, 212,1);">
            <div class="card-body d-flex flex-column h-100 gap-3">
                <!-- Logo -->
                <div class="auth-brand text-center text-lg-start">
                    <a href="{{ url('/') }}" class="logo-dark">
                        <span><img src="{{ asset('images/dream-logo.png') }}" alt="dark logo" height="60"></span>
                    </a>
                    <a href="{{ url('/') }}" class="logo-light">
                        <span><img src="{{ asset('images/dream-logo.png') }}" alt="logo" height="50"></span>
                    </a>
                </div>
                <div class="my-auto">
                    <!-- title-->
                    <h4 class="mt-0 text-white">Reset Password</h4>
                    <p class="mb-4 text-white">Enter your email address to reset password.</p>
                    <!-- form -->
                    <form action="{{ route('forgot_password') }}" id="sendPasswordResetLinkForm"
                        name="sendPasswordResetLinkForm" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="username" class="form-label text-white">Email address</label>
                            <input class="form-control" type="email" name="username" id="username" required
                                placeholder="Enter your email">
                            <small class="text-danger" id="username_error"></small>
                        </div>
                        <div class="d-grid mb-0 text-center">
                            <button class="btn btn-primary saveBtn" onclick="submitCreateForm('sendPasswordResetLinkForm')"
                                type="button"><i class="mdi mdi-login"></i>
                                <span>Send Password Reset Link</span> </button>
                            <button class="btn btn-success btnLoading" type="button" style="display: none" disabled>
                                <span class="spinner-grow spinner-grow-sm me-1" role="" aria-hidden="true"></span>
                                Please wait...
                            </button>
                        </div>
                        <div class="text-center mt-4">
                            <a href="{{ route('login') }}" class=" float-center text-white">
                                <p>Back to Log In</p>
                            </a>
                        </div>
                    </form>
                    <!-- end form-->
                </div>
                <!-- Footer-->
                <footer class="footer footer-alt text-white">
                    © 2024-
                    <script>
                        document.write(new Date().getFullYear())
                    </script> | Dream Travel and Tours
                </footer>

            </div> <!-- end .card-body -->
        </div>
        <!-- end auth-fluid-form-box-->
    </div>
@endsection
