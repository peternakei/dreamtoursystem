<div class="navbar-custom">
    <div class="topbar container-fluid">
        <div class="d-flex align-items-center gap-lg-2 gap-1">
            <!-- Sidebar Menu Toggle Button -->
            <button class="button-toggle-menu" type="button" aria-label="Toggle sidebar">
                <i class="mdi mdi-menu"></i>
            </button>

            <!-- Horizontal Menu Toggle Button -->
            <button class="navbar-toggle" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                <div class="lines">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </button>
        </div>
        <ul class="topbar-menu d-flex align-items-center gap-3">
            <li class="dropdown">
                <a class="nav-link dropdown-toggle arrow-none nav-user px-2" data-bs-toggle="dropdown" href="#"
                    role="button" aria-haspopup="false" aria-expanded="false">
                    <span class="account-user-avatar">
                        <img src="{{ asset('assets/images/users/avatar-1.jpg') }}" alt="user-image" width="32"
                            class="rounded-circle">
                    </span>
                    <span class="d-lg-flex flex-column gap-1 d-none">
                        <h5 class="my-0">{{ Auth::user()->userProfile->name }}</h5>
                        <h6 class="my-0 fw-normal">{{ Auth::user()->roles->pluck('name')->first() }}</h6>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated profile-dropdown">
                    <!-- item-->
                    <a href="{{ route('users.profile', Auth::user()->uuid) }}" class="dropdown-item">
                        <i class="mdi mdi-account-edit me-1"></i>
                        <span>Settings</span>
                    </a>

                    <!-- item-->
                    <form action="{{ route('logout') }}" id="userLogoutForm" name="userLogoutForm" method="POST">
                        @csrf
                        <a href="javascript:void(0);" class="dropdown-item"
                            onclick="submitCreateForm('userLogoutForm');">
                            <i class="mdi mdi-logout me-1"></i>
                            <span>Logout</span>
                        </a>
                    </form>

                </div>
            </li>
        </ul>
    </div>
</div>
