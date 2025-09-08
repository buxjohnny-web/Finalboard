<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title>{{ __('messages.register_title') }} | Intelboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Admin registration page" name="description" />
    <meta content="Intelboard" name="author" />
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" type="text/css" />
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

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="vstack gap-2">
                                <a href="{{ route('auth.google.redirect') }}" class="btn btn-light social-btn">
                                    <svg viewBox="0 0 48 48">
                                        <path fill="#FFC107"
                                            d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24s8.955,20,20,20s20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z">
                                        </path>
                                        <path fill="#FF3D00"
                                            d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z">
                                        </path>
                                        <path fill="#4CAF50"
                                            d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z">
                                        </path>
                                        <path fill="#1976D2"
                                            d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.574l6.19,5.238C42.022,35.545,44,30.035,44,24C44,22.659,43.862,21.35,43.611,20.083z">
                                        </path>
                                    </svg>
                                    {{ __('messages.register_with_google') }}
                                </a>
                                <a href="#" class="btn btn-dark social-btn" id="apple-login-btn"
                                    style="display: none;">
                                    <svg viewBox="0 0 24 24">
                                        <path fill="currentColor"
                                            d="M13.6,1.2H14c1.2,0,2.1,0.5,2.6,1c0.5,0.5,0.9,1.3,0.9,2.4c0,1.2-0.5,2.1-1.3,2.6c-0.8,0.5-1.9,0.7-3.1,0.7 c-0.8,0-1.6-0.1-2.2-0.4c-0.6-0.2-1.2-0.5-1.6-0.9C8.8,6.8,8.5,6.1,8.5,5.2c0-1.5,0.8-2.6,2.3-3.1C11.6,1.8,12.5,1.6,13.6,1.2z M17.6,10.6c-1.1,1.8-2,3.3-3.1,4.5c-0.9,1.1-1.8,2-2.8,2.8c-1.1,0.9-2.1,1.3-3.2,1.3c-0.8,0-1.6-0.2-2.2-0.6 c-0.6-0.4-1.1-1-1.3-1.8c-0.2-0.8,0-1.6,0.5-2.4c0.5-0.8,1.2-1.5,2.1-2.2c0.9-0.7,1.8-1.3,2.8-1.9c0.9-0.6,1.8-1.2,2.5-1.9 c-1.3-0.9-2.2-1.8-2.6-2.6C9.6,7,9.5,6.2,9.5,5.4c0-1.2,0.4-2.1,1.2-2.6c0.8-0.6,1.8-0.8,2.8-0.8c0.8,0,1.6,0.2,2.2,0.6 c0.6,0.4,1.1,1,1.3,1.8c0.2,0.8-0.1,1.6-0.5,2.4c-0.5,0.8-1.2,1.5-2.1,2.2C13.8,9.7,14,9.9,14,10.1c0,0.2,0,0.4,0.1,0.5 c0.5,0.4,0.9,0.8,1.3,1.3C15.8,12.7,16.8,11.8,17.6,10.6z">
                                        </path>
                                    </svg>
                                    {{ __('messages.register_with_apple') }}
                                </a>
                            </div>

                            <div class="separator">
                                {{ __('messages.or_use_email') }}
                            </div>

                            <form action="{{ route('register') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="full_name"
                                        class="form-label">{{ __('messages.register_full_name_label') }}</label>
                                    <input class="form-control" type="text" id="full_name" name="full_name" required
                                        value="{{ old('full_name') }}"
                                        placeholder="{{ __('messages.register_full_name_placeholder') }}">
                                </div>

                                <div class="mb-3">
                                    <label for="email"
                                        class="form-label">{{ __('messages.register_email_label') }}</label>
                                    <input class="form-control" type="email" id="email" name="email"
                                        required value="{{ old('email') }}"
                                        placeholder="{{ __('messages.register_email_placeholder') }}">
                                    <div class="invalid-feedback">
                                        {{ __('messages.register_email_invalid') ?? 'Please enter a valid email address.' }}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="phone"
                                        class="form-label">{{ __('messages.register_phone_label') }}</label>
                                    <input class="form-control" type="tel" id="phone" name="phone"
                                        required value="{{ old('phone') }}"
                                        placeholder="{{ __('messages.register_phone_placeholder') }}" maxlength="10"
                                        inputmode="numeric">
                                    <div class="invalid-feedback">
                                        {{ __('messages.register_phone_invalid') ?? 'Phone must be exactly 10 digits.' }}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="password"
                                        class="form-label">{{ __('messages.register_password_label') }}</label>
                                    <div class="input-group input-group-merge">
                                        <input type="password" id="password" name="password" class="form-control"
                                            required placeholder="{{ __('messages.register_password_placeholder') }}">
                                        <div class="input-group-text" data-password="false">
                                            <span class="password-eye"></span>
                                        </div>
                                        <div class="invalid-feedback">
                                            {{ __('messages.register_password_invalid') ?? 'Must be at least 8 chars, include 1 letter, 1 number, and 1 special character.' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="password_confirmation"
                                        class="form-label">{{ __('messages.register_password_confirm_label') }}</label>
                                    <input class="form-control" type="password" id="password_confirmation"
                                        name="password_confirmation" required
                                        placeholder="{{ __('messages.register_password_confirm_placeholder') }}">
                                </div>

                                <div class="mb-3 text-center">
                                    <button class="btn btn-primary"
                                        type="submit">{{ __('messages.register_button') }}</button>
                                </div>
                            </form>

                            <div class="row mt-3">
                                <div class="col-12 text-center">
                                    <p class="text-muted">{{ __('messages.register_already_have_account') }} <a
                                            href="{{ route('login') }}"
                                            class="text-muted ms-1"><b>{{ __('messages.login_button') }}</b></a></p>
                                </div>
                            </div>
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
        // Conditional Apple Login Button
        document.addEventListener("DOMContentLoaded", function() {
            var appleBtn = document.getElementById('apple-login-btn');
            if (navigator.platform.indexOf('iPhone') !== -1) {
                appleBtn.style.display = 'flex';
            }
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Phone validation
            const phone = document.getElementById("phone");
            phone.addEventListener("input", function() {
                this.value = this.value.replace(/\D/g, ""); // remove non-digits
                if (this.value.length === 10) {
                    this.classList.remove("is-invalid");
                    this.classList.add("is-valid");
                } else {
                    this.classList.remove("is-valid");
                    this.classList.add("is-invalid");
                }
            });

            // Email validation
            const email = document.getElementById("email");
            email.addEventListener("input", function() {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (emailPattern.test(this.value)) {
                    this.classList.remove("is-invalid");
                    this.classList.add("is-valid");
                } else {
                    this.classList.remove("is-valid");
                    this.classList.add("is-invalid");
                }
            });

            // Password validation
            const password = document.getElementById("password");
            password.addEventListener("input", function() {
                const passPattern = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}$/;
                if (passPattern.test(this.value)) {
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
