@extends('layouts.master')

@section('content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3 mb-3">
    <h4 class="page-title m-0">{{ __('messages.add_driver') }}</h4>
    <button type="button" class="btn btn-success ms-auto" onclick="window.location='{{ route('drivers') }}'">
        <i class="mdi mdi-arrow-left me-1"></i> <span>{{ __('messages.back_to_drivers') }}</span>
    </button>
</div>

<div class="mb-3">
    <form action="{{ route('drivers.store') }}" method="POST">
        @csrf
        <div class="form-group mt-2">
            <input type="text" name="full_name" placeholder="{{ __('messages.full_name') }}" class="form-control" required>
        </div>
        <div class="form-group mt-2">
            <input type="text" name="phone_number" placeholder="{{ __('messages.phone_number') }}" class="form-control" required>
        </div>
        <div class="form-group mt-2">
            <input type="text" name="driver_id" placeholder="{{ __('messages.driver_id') }}" class="form-control" required>
        </div>
        <div class="form-group mt-2">
            <input type="text" name="license_number" placeholder="{{ __('messages.license_number') }}" class="form-control" required>
        </div>
        <div class="form-group mt-2">
            <input type="text" name="ssn" placeholder="{{ __('messages.ssn') }}" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary mt-3">
            <i class="mdi mdi-plus me-1"></i> <span>{{ __('messages.add_driver_btn') }}</span>
        </button>
    </form>
</div>
@endsection