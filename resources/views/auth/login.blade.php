<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Log In | Intelboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Admin login page" name="description" />
    <meta content="Intelboard" name="author" />
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- App CSS -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style"/>
</head>

<body class="loading authentication-bg" data-layout-config='{"darkMode":false}'>
    <div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-4 col-lg-5">
                    <div class="card">

                        <!-- Logo -->
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
                                    <label for="emailaddress" class="form-label">Email address</label>
                                    <input class="form-control" type="email" id="emailaddress" name="email" required placeholder="Enter your email" value="{{ old('email') }}">
                                </div>

                                <div class="mb-3">
                                    <a href="#" class="text-muted float-end"><small>Forgot your password?</small></a>
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group input-group-merge">
                                        <input type="password" id="password" name="password" class="form-control" required placeholder="Enter your password">
                                        <div class="input-group-text" data-password="false">
                                            <span class="password-eye"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="checkbox-signin" name="remember" checked>
                                        <label class="form-check-label" for="checkbox-signin">Remember me</label>
                                    </div>
                                </div>

                                <div class="mb-3 text-center">
                                    <button class="btn btn-primary" type="submit">Log In</button>
                                </div>
                            </form>
                        </div> <!-- end card-body -->
                    </div> <!-- end card -->
                </div> <!-- end col -->
            </div> <!-- end row -->
        </div> <!-- end container -->
    </div> <!-- end page -->

    <footer class="footer footer-alt">
        2024 - <script>document.write(new Date().getFullYear())</script> © Intelboard
    </footer>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.min.js') }}"></script>
</body>
</html>
