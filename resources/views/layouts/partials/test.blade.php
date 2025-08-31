@php
    $currentLocale = app()->getLocale();
@endphp

<div class="navbar-custom topnav-navbar-dark">
    {{-- <ul class="list-unstyled topbar-menu float-end mb-0">
        <li class="dropdown notification-list topbar-dropdown">
            <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button">
                @if ($currentLocale === 'fr')
                    <img src="{{ asset('assets/images/flags/france.jpg') }}" alt="flag" height="12">
                    <span class="align-middle d-none d-sm-inline-block">Français</span>
                @else
                    <img src="{{ asset('assets/images/flags/us.jpg') }}" alt="flag" height="12">
                    <span class="align-middle d-none d-sm-inline-block">English</span>
                @endif
                <i class="mdi mdi-chevron-down d-none d-sm-inline-block align-middle"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated topbar-dropdown-menu">
                @foreach (config('app.available_locales') as $locale)
                    <a href="{{ route('lang.switch', ['locale' => $locale]) }}" class="dropdown-item notify-item">
                        <img src="{{ asset("assets/images/flags/$locale.jpg") }}" alt="{{ strtoupper($locale) }}"
                            class="me-1" height="12">
                        @if ($locale === 'fr')
                            Français
                        @else
                            English
                        @endif
                    </a>
                @endforeach
            </div>
        </li>

        <li class="dropdown notification-list">
            <a class="nav-link dropdown-toggle nav-user arrow-none me-0" data-bs-toggle="dropdown" href="#"
                role="button">
                <span class="account-user-avatar avatar-initials">
                    {{ strtoupper(substr(Auth::user()->full_name, 0, 1)) }}{{ strtoupper(substr(explode(' ', Auth::user()->full_name)[1] ?? '', 0, 1)) }}
                </span>
                <span>
                    <span class="account-user-name">{{ Auth::user()->full_name }}</span>
                    <span class="account-position">{{ strtoupper(Auth::user()->role) }}</span>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated topbar-dropdown-menu profile-dropdown">
                <a href="javascript:void(0);" class="dropdown-item notify-item">
                    <i class="mdi mdi-account-edit me-1"></i> Settings
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>

                <a href="#" class="dropdown-item notify-item"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="mdi mdi-logout me-1"></i> Logout
                </a>
            </div>
        </li>
    </ul> --}}
    <button class="button-menu-mobile open-left text-white">
        <i class="mdi mdi-menu"></i>
    </button>
</div>
