@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="page-title mb-0">{{ __('messages.home_option_c_title') }}</h4>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Hero tile -->
        <div class="col-12">
            <a href="#" class="card text-white bg-primary text-decoration-none">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div class="d-flex align-items-center gap-3">
                        <i class="mdi mdi-account-multiple-outline display-5"></i>
                        <h3 class="mb-0 text-white">{{ __('messages.drivers') }}</h3>
                    </div>
                    <i class="mdi mdi-chevron-right-circle-outline display-6 text-white"></i>
                </div>
            </a>
        </div>

        <!-- Smaller tiles -->
        <div class="col-md-4">
            <a href="#" class="card text-decoration-none h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-cash-multiple fs-2"></i>
                        <h5 class="mb-0">{{ __('messages.pays') }}</h5>
                    </div>
                    <i class="mdi mdi-chevron-right-circle-outline fs-3"></i>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="#" class="card text-decoration-none h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-chart-line fs-2"></i>
                        <h5 class="mb-0">{{ __('messages.stats') }}</h5>
                    </div>
                    <i class="mdi mdi-chevron-right-circle-outline fs-3"></i>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="#" class="card text-decoration-none h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-cog-outline fs-2"></i>
                        <h5 class="mb-0">{{ __('messages.settings') }}</h5>
                    </div>
                    <i class="mdi mdi-chevron-right-circle-outline fs-3"></i>
                </div>
            </a>
        </div>
    </div>
@endsection
