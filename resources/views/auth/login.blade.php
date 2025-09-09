<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title>{{ __('messages.login_title') }} | Intelboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Admin login page" name="description" />
    <meta content="Intelboard" name="author" />
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />
    <style>
        .divider-text {
            position: relative;
            top: -12px;
            background: white;
            padding: 0 15px;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>

<body class="loading authentication-bg" data-layout-config='{"darkMode":false}'>

    @php
        $flagImages = ['en' => 'us.jpg', 'fr' => 'france.jpg'];
        $currentLocale = app()->getLocale();
    @endphp

    <div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-4 col-lg-5">

                    <div class="card">

                        {{-- <div class="card-header pt-2 pb-2 text-center bg-primary">
                            <a href="{{ url('/') }}">
                                <h3 class="text-white">Intelboard</h3>
                            </a>
                        </div> --}}
                        <div class="card-header pt-4 pb-4 text-center bg-primary">
                            <a href="index.html">
                                <svg width="228" height="36" viewBox="0 0 228 36" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <!-- Left logo: overlapping circles -->
                                    <g>
                                        <!-- Left blue circle -->
                                        <circle cx="18" cy="18" r="15" fill="#6AC0D7" />
                                        <!-- Right white circle, overlapping the blue one -->
                                        <circle cx="27" cy="18" r="15" fill="white" />
                                    </g>
                                    <!-- INTELBOARD text -->
                                    <text x="48" y="27" fill="white" font-family="Arial, Helvetica, sans-serif"
                                        font-size="24" font-weight="bold" letter-spacing="0.04em">INTELBOARD</text>
                                </svg>
                            </a>
                        </div>

                        <div class="card-body p-4">
                            <div class="mt-6 mb-3 text-end" id="langswitcher">
                                <div class="dropdown">
                                    <a class="nav-link dropdown-toggle text-muted p-0 arrow-none"
                                        data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false"
                                        aria-expanded="false">
                                        <img src="{{ asset('assets/images/flags/' . $flagImages[$currentLocale]) }}"
                                            alt="user-image" class="me-1" height="12">
                                        <span
                                            class="align-middle">{{ $currentLocale === 'fr' ? 'Français' : 'English' }}</span>
                                        <i class="mdi mdi-chevron-down align-middle"></i>
                                    </a>
                                    <!-- 🔹 align dropdown menu to the right -->
                                    <div
                                        class="dropdown-menu dropdown-menu-end dropdown-menu-animated topbar-dropdown-menu">
                                        @foreach (config('app.available_locales') as $locale)
                                            <a href="{{ route('lang.switch', ['locale' => $locale]) }}"
                                                class="dropdown-item notify-item">
                                                <img src="{{ asset('assets/images/flags/' . $flagImages[$locale]) }}"
                                                    alt="{{ strtoupper($locale) }}" class="me-1" height="12">
                                                <span
                                                    class="align-middle">{{ $locale === 'fr' ? 'Français' : 'English' }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <strong>{{ $errors->first() }}</strong>
                                </div>
                            @endif

                            <form action="{{ route('login') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label for="emailaddress"
                                        class="form-label">{{ __('messages.login_email_label') }}</label>
                                    <input class="form-control" type="email" id="emailaddress" name="email" required
                                        placeholder="{{ __('messages.login_email_placeholder') }}"
                                        value="{{ old('email') }}">
                                </div>

                                <div class="mb-3">
                                    <a href="#"
                                        class="text-muted float-end"><small>{{ __('messages.login_forgot_password') }}</small></a>
                                    <label for="password"
                                        class="form-label">{{ __('messages.login_password_label') }}</label>
                                    <div class="input-group input-group-merge">
                                        <input type="password" id="password" name="password" class="form-control"
                                            required placeholder="{{ __('messages.login_password_placeholder') }}">
                                        <div class="input-group-text" data-password="false">
                                            <span class="password-eye"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="checkbox-signin"
                                            name="remember" checked>
                                        <label class="form-check-label"
                                            for="checkbox-signin">{{ __('messages.login_remember_me') }}</label>
                                    </div>
                                </div>

                                <div class="mb-3 text-center">
                                    <button class="btn btn-primary"
                                        type="submit">{{ __('messages.login_button') }}</button>
                                </div>
                                
                                <!-- Google Login Button -->
                                <div class="mb-3 text-center">
                                    <div class="position-relative">
                                        <hr class="bg-light">
                                        <div class="divider-text">{{ __('messages.login_or') }}</div>
                                    </div>
                                </div>
                                
                                <div class="mb-3 text-center">
                                    <a href="{{ route('auth.google.redirect') }}" class="btn btn-outline-danger btn-block">
                                        <svg width="18" height="18" viewBox="0 0 18 18" class="me-2">
                                            <path fill="#4285F4" d="M16.51 8H8.98v3h4.3c-.18 1-.74 1.48-1.6 2.04v2.01h2.6a7.8 7.8 0 0 0 2.38-5.88c0-.57-.05-.66-.15-1.18Z"/>
                                            <path fill="#34A853" d="M8.98 17c2.16 0 3.97-.72 5.3-1.94l-2.6-2.04a4.8 4.8 0 0 1-2.7.75c-2.08 0-3.84-1.4-4.48-3.29H1.96v2.09A7.86 7.86 0 0 0 8.98 17Z"/>
                                            <path fill="#FBBC05" d="M4.5 10.48A4.59 4.59 0 0 1 4.25 9c0-.51.09-1.02.25-1.48V5.43H1.96a7.86 7.86 0 0 0 0 7.14l2.54-2.09Z"/>
                                            <path fill="#EB4335" d="M8.98 3.77c1.17 0 2.23.4 3.06 1.2l2.3-2.3A7.55 7.55 0 0 0 8.98 1a7.86 7.86 0 0 0-7.02 4.43l2.54 2.09c.64-1.89 2.4-3.29 4.48-3.29Z"/>
                                        </svg>
                                        {{ __('messages.login_google') }}
                                    </a>
                                </div>
                                
                                <div class="mb-3 text-center">
                                    {{-- <a href="{{ route('register') }}" --}}
                                    <a href="{{ route('register') }}"
                                        class="text-dark-50 text-center pb-0 fw-bold text-muted">{{ __('messages.login_no_account') }}</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer footer-alt">
        2024 -
        <script>
            document.write(new Date().getFullYear())
        </script> © Intelboard
    </footer>

    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.min.js') }}"></script>
</body>

</html>
