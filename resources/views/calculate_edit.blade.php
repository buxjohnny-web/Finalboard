@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="page-title mb-0">
                    {{ __('messages.edit_calculation_title') }}
                </h4>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @php
                // Expecting: $driver, $week (string as received), $calculation
                $weekDisplay = $week;
            @endphp

            <div class="mb-3">
                <div class="d-flex flex-wrap gap-3">
                    <div><strong>{{ __('messages.driver') }}:</strong> #{{ $driver->driver_id }} - {{ $driver->full_name }}
                    </div>
                    <div><strong>{{ __('messages.weekno') }}:</strong> {{ $weekDisplay }}</div>
                </div>
            </div>

            <form id="calc-edit-form" method="POST"
                action="{{ route('calculate.update', ['driver' => $driver->id, 'week' => $week]) }}" novalidate>
                @csrf
                @method('PUT')

                <input type="hidden" name="driver_id" value="{{ $driver->id }}">
                <input type="hidden" name="week" value="{{ $week }}">
                <input type="hidden" id="bonus_static"
                    value="{{ number_format((float) ($calculation->bonus ?? 0), 2, '.', '') }}">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" for="total_invoice">{{ __('messages.total_invoice') }}</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="total_invoice"
                            name="total_invoice"
                            value="{{ number_format((float) ($calculation->total_invoice ?? 0), 2, '.', '') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label" for="parcel_rows_count">{{ __('messages.daysworked') }}</label>
                        <input type="number" step="1" min="0" class="form-control" id="parcel_rows_count"
                            name="parcel_rows_count" value="{{ (int) ($calculation->parcel_rows_count ?? 0) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label"
                            for="vehicule_rental_price">{{ __('messages.vehicule_rental_price') }}</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="vehicule_rental_price"
                            name="vehicule_rental_price"
                            value="{{ number_format((float) ($calculation->vehicule_rental_price ?? 0), 2, '.', '') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label" for="broker_percentage">{{ __('messages.broker_percentage') }}</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" max="100" class="form-control"
                                id="broker_percentage" name="broker_percentage"
                                value="{{ number_format((float) ($calculation->broker_percentage ?? 0), 2, '.', '') }}">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label" for="cash_advance">{{ __('messages.cash_advance') }}</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="cash_advance"
                            name="cash_advance"
                            value="{{ number_format((float) ($calculation->cash_advance ?? 0), 2, '.', '') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label" for="final_amount">{{ __('messages.finalamount') }}</label>
                        <input type="text" class="form-control" id="final_amount" name="final_amount_display"
                            value="${{ number_format((float) ($calculation->final_amount ?? 0), 2, '.', '') }}" disabled>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
                    <a href="{{ route('drivers.show', ['id' => $driver->id]) }}" class="btn btn-light">
                        {{ __('messages.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const elTotal = document.getElementById('total_invoice');
            const elDays = document.getElementById('parcel_rows_count');
            const elRent = document.getElementById('vehicule_rental_price');
            const elPct = document.getElementById('broker_percentage');
            const elCash = document.getElementById('cash_advance');
            const elFinal = document.getElementById('final_amount');
            const elBonus = document.getElementById('bonus_static');

            function num(v) {
                const n = parseFloat(v);
                return isNaN(n) ? 0 : n;
            }

            function recompute() {
                const total = num(elTotal.value);
                const pct = Math.min(100, Math.max(0, num(elPct.value)));
                const days = Math.max(0, Math.floor(num(elDays.value)));
                const rent = num(elRent.value);
                const cash = num(elCash.value);
                const bonus = num(elBonus.value);

                const driverShare = (100 - pct) / 100;
                const left = total * driverShare;
                const right = rent * days;
                const final = (left - right + bonus - cash);

                elFinal.value = '$' + final.toFixed(2);
            }

            [elTotal, elDays, elRent, elPct, elCash].forEach(i => {
                i.addEventListener('input', recompute);
                i.addEventListener('change', recompute);
            });
            recompute();
        });
    </script>
@endpush
