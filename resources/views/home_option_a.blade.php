@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="page-title mb-0">{{ __('messages.home_option_a_title') }}</h4>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-3">
        <div class="col">
            <div class="card h-100">
                <div class="card-body text-center py-5">
                    <i class="mdi mdi-account-multiple-outline display-5 mb-2"></i>
                    <h5 class="card-title mb-0">{{ __('messages.drivers') }}</h5>
                </div>
                <a href="#" class="stretched-link" aria-label="{{ __('messages.drivers') }}"></a>
            </div>
        </div>

        <div class="col">
            <div class="card h-100">
                <div class="card-body text-center py-5">
                    <i class="mdi mdi-cash-multiple display-5 mb-2"></i>
                    <h5 class="card-title mb-0">{{ __('messages.pays') }}</h5>
                </div>
                <a href="#" class="stretched-link" aria-label="{{ __('messages.pays') }}"></a>
            </div>
        </div>

        <div class="col">
            <div class="card h-100">
                <div class="card-body text-center py-5">
                    <i class="mdi mdi-chart-line display-5 mb-2"></i>
                    <h5 class="card-title mb-0">{{ __('messages.stats') }}</h5>
                </div>
                <a href="#" class="stretched-link" aria-label="{{ __('messages.stats') }}"></a>
            </div>
        </div>

        <div class="col">
            <div class="card h-100">
                <div class="card-body text-center py-5">
                    <i class="mdi mdi-cog-outline display-5 mb-2"></i>
                    <h5 class="card-title mb-0">{{ __('messages.settings') }}</h5>
                </div>
                <a href="#" class="stretched-link" aria-label="{{ __('messages.settings') }}"></a>
            </div>
        </div>
    </div>
@endsection
