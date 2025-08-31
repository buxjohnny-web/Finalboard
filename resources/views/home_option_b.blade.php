@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="page-title mb-0">{{ __('messages.home_option_b_title') }}</h4>
            </div>
        </div>
    </div>

    <div class="d-grid gap-3">
        <a href="#" class="btn btn-primary btn-lg d-flex align-items-center justify-content-center gap-2"
            aria-label="{{ __('messages.drivers') }}">
            <i class="mdi mdi-account-multiple-outline"></i>
            <span>{{ __('messages.drivers') }}</span>
        </a>
        <a href="#" class="btn btn-success btn-lg d-flex align-items-center justify-content-center gap-2"
            aria-label="{{ __('messages.pays') }}">
            <i class="mdi mdi-cash-multiple"></i>
            <span>{{ __('messages.pays') }}</span>
        </a>
        <a href="#" class="btn btn-info btn-lg d-flex align-items-center justify-content-center gap-2"
            aria-label="{{ __('messages.stats') }}">
            <i class="mdi mdi-chart-line"></i>
            <span>{{ __('messages.stats') }}</span>
        </a>
        <a href="#" class="btn btn-secondary btn-lg d-flex align-items-center justify-content-center gap-2"
            aria-label="{{ __('messages.settings') }}">
            <i class="mdi mdi-cog-outline"></i>
            <span>{{ __('messages.settings') }}</span>
        </a>
    </div>
@endsection
