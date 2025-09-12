<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Intelboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Intelboard" name="description" />
    <meta content="Intelboard" name="author" />
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    <link href="{{ asset('assets/css/vendor/jquery-jvectormap-1.2.2.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Datatables css -->
    <link href="{{ asset('assets/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/responsive.bootstrap5.css') }}" rel="stylesheet" type="text/css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Copilot reminder:
     Never write CSS inside Blade files.
     Always use class names only.
     CSS belongs in: public/assets/css/custom.css
    --}}
</head>

<body class="loading" data-layout-color="light" data-leftbar-theme="dark" data-layout-mode="fluid"
    data-rightbar-onstart="true">
    <div class="wrapper">
