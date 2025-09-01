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

                        <div class="card-header pt-4 pb-4 text-center bg-primary">
                            <a href="{{ url('/') }}">
                                <h3 class="text-white">Intelboard</h3>
                            </a>
                        </div>

                        <div class="card-body p-4">

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

                                <div class="mt-3 text-start">
                                    <div class="dropdown">
                                        <a class="nav-link dropdown-toggle text-muted p-0 arrow-none"
                                            data-bs-toggle="dropdown" href="#" role="button"
                                            aria-haspopup="false" aria-expanded="false">
                                            <img src="{{ asset('assets/images/flags/' . $flagImages[$currentLocale]) }}"
                                                alt="user-image" class="me-1" height="12">
                                            <span
                                                class="align-middle">{{ $currentLocale === 'fr' ? 'Français' : 'English' }}</span>
                                            <i class="mdi mdi-chevron-down align-middle"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-animated topbar-dropdown-menu">
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
