<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title>{{ __('messages.register_phone_title') }} | Intelboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Finish registration" name="description" />
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
                                <svg width="228" height="36" viewBox="0 0 228 36" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g>
                                        <circle cx="18" cy="18" r="15" fill="#6AC0D7" />
                                        <circle cx="27" cy="18" r="15" fill="white" />
                                    </g>
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

                            <div class="text-center">
                                <h4 class="mt-0">
                                    {{ __('messages.register_phone_welcome', ['name' => session('google_user.name', 'Guest')]) }}
                                </h4>
                                <p class="text-muted mb-4">{{ __('messages.register_phone_prompt') }}</p>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <strong>{{ $errors->first() }}</strong>
                                </div>
                            @endif

                            <form action="{{ route('register.phone.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="phone_number" class="form-label">
                                        {{ __('messages.register_phone_label') }}
                                    </label>
                                    <input class="form-control" type="tel" id="phone_number" name="phone_number"
                                        required value="{{ old('phone_number') }}"
                                        placeholder="{{ __('messages.register_phone_placeholder') }}" maxlength="10"
                                        inputmode="numeric">
                                    <div class="invalid-feedback">
                                        {{ __('messages.register_phone_error') ?? 'Please enter a valid 10-digit phone number.' }}
                                    </div>
                                </div>

                                <div class="mb-3 text-center">
                                    <button class="btn btn-primary"
                                        type="submit">{{ __('messages.register_phone_button') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer footer-alt">
        {{ date('Y') }} © Intelboard
    </footer>

    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.min.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const phone = document.getElementById("phone_number");

            phone.addEventListener("input", function() {
                // Keep only digits
                this.value = this.value.replace(/\D/g, "");

                // Validate length (10 digits only)
                if (/^\d{10}$/.test(this.value)) {
                    this.classList.remove("is-invalid");
                    this.classList.add("is-valid");
                } else {
                    this.classList.remove("is-valid");
                    this.classList.add("is-invalid");
                }
            });
        });
    </script>


</body>

</html>
