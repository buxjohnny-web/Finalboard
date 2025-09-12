@extends('layouts.master')

@section('content')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3 mb-3">
        {{-- <h4 class="page-title m-0">{{ __('messages.edit_driver') }}</h4> --}}
        <button type="button" class="btn btn-success ms-auto" onclick="window.location='{{ route('drivers') }}'">
            <i class="mdi mdi-arrow-left me-1"></i> <span>{{ __('messages.back_to_drivers') }}</span>
        </button>
    </div>

    <div class="mb-3">
        <form action="{{ route('drivers.update', $driver->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group mt-2">
                <input type="text" name="full_name" value="{{ $driver->full_name }}"
                    placeholder="{{ __('messages.full_name') }}" class="form-control" required>
            </div>
            <div class="form-group mt-2">
                <input type="text" name="phone_number" value="{{ $driver->phone_number }}"
                    placeholder="{{ __('messages.phone_number') }}" class="form-control" required>
            </div>
            <div class="form-group mt-2">
                <input type="text" name="driver_id" value="{{ $driver->driver_id }}"
                    placeholder="{{ __('messages.driver_id') }}" class="form-control" required>
            </div>
            <div class="form-group mt-2">
                <input type="text" name="license_number" value="{{ $driver->license_number }}"
                    placeholder="{{ __('messages.license_number') }}" class="form-control" required>
            </div>
            <div class="form-group mt-2">
                <input type="text" name="ssn" value="{{ $driver->ssn }}" placeholder="{{ __('messages.ssn') }}"
                    class="form-control">
            </div>

            <!-- New fields -->
            <div class="form-group mt-2">
                <input type="number" name="default_percentage" value="{{ $driver->default_percentage }}"
                    placeholder="{{ __('messages.default_percentage') }}" class="form-control" min="0"
                    max="100" step="0.01" inputmode="decimal">
            </div>
            <div class="form-group mt-2">
                <input type="number" name="default_rental_price" value="{{ $driver->default_rental_price }}"
                    placeholder="{{ __('messages.default_rental_price') }}" class="form-control" min="0"
                    step="0.01" inputmode="decimal">
            </div>

            <button type="submit" class="btn btn-primary mt-3">
                <i class="mdi mdi-content-save me-1"></i> <span>{{ __('messages.save_driver_btn') }}</span>
            </button>
        </form>
    </div>
@endsection
