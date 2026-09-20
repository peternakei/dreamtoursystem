<!-- ========== Left Sidebar Start ========== -->
<div class="leftside-menu">

    <!-- Brand Logo Light -->
    <a href="javascript:void(0);" class="logo logo-light">
        <span class="logo-lg">
            <img src="{{ asset('images/dream-logo.png') }}" alt="logo">
        </span>
        <span class="logo-sm">
            <img src="{{ asset('assets/images/logo-sm.png') }}" alt="small logo">
        </span>
    </a>

    <!-- Sidebar Hover Menu Toggle Button -->
    <div class="button-sm-hover" data-bs-toggle="tooltip" data-bs-placement="right" title="Show Full Sidebar">
        <i class="ri-checkbox-blank-circle-line align-middle"></i>
    </div>

    <!-- Full Sidebar Menu Close Button -->
    <div class="button-close-fullsidebar">
        <i class="ri-close-fill align-middle"></i>
    </div>

    <!-- Sidebar -left -->
    <div class="h-100" id="leftside-menu-container" data-simplebar>
        <!-- Leftbar User -->
        <div class="leftbar-user">
            <a href="pages-profile.html">
                <img src="{{ asset('assets/images/users/avatar-1.jpg') }}" alt="user-image" height="42"
                    class="rounded-circle shadow-sm">
                <span class="leftbar-user-name mt-2">{{ Auth::user()->name }}</span>
            </a>
        </div>
        <!--- Sidemenu -->
        <ul class="side-nav">
            @if (count($menus) <= 0)
                <li class="side-nav-item">
                    <a href="{{ route('dashboard.user') }}" class="side-nav-link">
                        <i class="uil-apps"></i>
                        <span> Dashboard </span>
                    </a>
                </li>
            @else
                <li class="side-nav-item">
                    <a href="{{ route('dashboard.user') }}" class="side-nav-link">
                        <i class="uil-apps"></i>
                        <span> Dashboard </span>
                    </a>
                </li>
                @foreach ($menus as $menu)
                    @if (count($menu->childs) <= 0 && $menu->url != '#')
                        <li class="side-nav-item">
                            <a href="{{ route($menu->url) }}" class="side-nav-link">
                                <i class="{{ $menu->icon }}"></i>
                                <span> {{ $menu->title }} </span>
                            </a>
                        </li>
                    @else
                        <!-- resources/views/components/system/side-bar-menu.blade.php -->
                        <li class="side-nav-item">
                            <a data-bs-toggle="collapse"
                                href="{{ '#' . str_replace(' ', '_', $menu->title) . '_dropdown' }}"
                                aria-expanded="false"
                                aria-controls="{{ str_replace(' ', '_', $menu->title) . '_dropdown' }}"
                                class="side-nav-link">
                                <i class="{{ $menu->icon }}"></i>
                                <span>{{ $menu->title }}</span>
                                @if ($menu->childs->isNotEmpty())
                                    <span class="menu-arrow"></span>
                                @endif
                            </a>
                            @if ($menu->childs->isNotEmpty())
                                <div class="collapse" id="{{ str_replace(' ', '_', $menu->title) . '_dropdown' }}">
                                    <ul class="side-nav-second-level">
                                        @foreach ($menu->childs as $child)
                                            <li class="{{ $child->childs->isNotEmpty() ? 'side-nav-item' : '' }}">
                                                @if ($child->childs->isNotEmpty())
                                                    <a data-bs-toggle="collapse"
                                                        href="{{ '#' . str_replace(' ', '_', $child->title) . '_subdropdown' }}"
                                                        aria-expanded="false"
                                                        aria-controls="{{ str_replace(' ', '_', $child->title) . '_subdropdown' }}"
                                                        class="side-nav-link">
                                                        <span>{{ $child->title }}</span>
                                                        <span class="menu-arrow"></span>
                                                    </a>
                                                    <div class="collapse"
                                                        id="{{ str_replace(' ', '_', $child->title) . '_subdropdown' }}">
                                                        <ul class="side-nav-third-level">
                                                            @foreach ($child->childs as $subChild)
                                                                <li>
                                                                    <a
                                                                        href="{{ $subChild->url != '#' ? route($subChild->url) : '#' }}">{{ $subChild->title }}</a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @else
                                                    <a
                                                        href="{{ $child->url != '#' ? route($child->url) : '#' }}">{{ $child->title }}</a>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </li>
                    @endif
                @endforeach
            @endif
        </ul>
        <!--- End Sidemenu -->
        <div class="clearfix"></div>
    </div>
</div>
<!-- ========== Left Sidebar End ========== -->
