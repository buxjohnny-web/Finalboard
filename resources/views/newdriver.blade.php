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
                <label for="full_name" class="form-label"><small>{{ __('messages.full_name') }}</small></label>
                <input id="full_name" type="text" name="full_name" placeholder="{{ __('messages.full_name') }}"
                    class="form-control" required>
            </div>

            <div class="form-group mt-2">
                <label for="phone_number" class="form-label"><small>{{ __('messages.phone_number') }}</small></label>
                <input id="phone_number" type="text" name="phone_number" placeholder="{{ __('messages.phone_number') }}"
                    class="form-control" required>
            </div>

            <div class="form-group mt-2">
                <label for="driver_id" class="form-label"><small>{{ __('messages.driver_id') }}</small></label>
                <input id="driver_id" type="text" name="driver_id" placeholder="{{ __('messages.driver_id') }}"
                    class="form-control" required>
            </div>

            <div class="form-group mt-2">
                <label for="license_number" class="form-label"><small>{{ __('messages.license_number') }}</small></label>
                <input id="license_number" type="text" name="license_number"
                    placeholder="{{ __('messages.license_number') }}" class="form-control" required>
            </div>

            <div class="form-group mt-2">
                <label for="ssn" class="form-label"><small>{{ __('messages.ssn') }}</small></label>
                <input id="ssn" type="text" name="ssn" placeholder="{{ __('messages.ssn') }}"
                    class="form-control">
            </div>

            <!-- New fields -->
            <div class="form-group mt-2">
                <label for="default_percentage"
                    class="form-label"><small>{{ __('messages.default_percentage') }}</small></label>
                <input id="default_percentage" type="number" name="default_percentage" class="form-control"
                    placeholder="{{ __('messages.default_percentage') }}" min="0" max="100" step="0.01"
                    inputmode="decimal">
            </div>

            <div class="form-group mt-2">
                <label for="default_rental_price"
                    class="form-label"><small>{{ __('messages.default_rental_price') }}</small></label>
                <input id="default_rental_price" type="number" name="default_rental_price" class="form-control"
                    placeholder="{{ __('messages.default_rental_price') }}" min="0" step="0.01"
                    inputmode="decimal">
            </div>

            <button type="submit" class="btn btn-primary mt-3">
                <i class="mdi mdi-plus me-1"></i> <span>{{ __('messages.add_driver_btn') }}</span>
            </button>
        </form>
    </div>
@endsection
