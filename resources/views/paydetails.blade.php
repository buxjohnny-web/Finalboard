@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('drivers.show', $driver->id) }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left me-1"></i> {{ __('messages.back') }}
                    </a>
                </div>
                <h4 class="page-title">{{ __('messages.payment_details') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white p-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-1 fw-bold">{{ __('messages.driver') }} #{{ $driver->driver_id }} -
                                {{ $driver->full_name }}</h4>
                            @php
                                $currentYear = Carbon\Carbon::now()->year;
                                $firstDayOfYear = Carbon\Carbon::create($currentYear, 1, 1);
                                // Find first Monday of the year
                                $firstMonday = $firstDayOfYear->copy();
                                if ($firstMonday->dayOfWeek !== Carbon\Carbon::MONDAY) {
                                    $firstMonday = $firstMonday->next(Carbon\Carbon::MONDAY);
                                }

                                $weekStart = $firstMonday->copy()->addWeeks($week - 1);
                                $weekEnd = $weekStart->copy()->addDays(6);
                                $dateRange = $weekStart->format('M d') . ' - ' . $weekEnd->format('M d');
                            @endphp
                            <p class="text-white mb-0">{{ __('messages.week') }} {{ $week }}: {{ $dateRange }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <style>
                        .card-header-section {
                            height: 60px;
                            display: flex;
                            flex-direction: column;
                            justify-content: center;
                        }

                        .card-value-section {
                            height: 40px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                        }
                    </style>
                    <div class="row g-2">
                        <div class="col">
                            <div class="card border-0 rounded-3 shadow-sm h-100">
                                <div class="card-body text-center p-2">
                                    <i class="mdi mdi-calendar-week text-primary font-22"></i>
                                    <div class="card-header-section">
                                        <h6 class="text-muted text-uppercase font-12 fw-semibold">
                                            {{ __('messages.daysworked') }}</h6>
                                    </div>
                                    <div class="card-value-section">
                                        <h2 class="mb-0 font-22">{{ $calculation->parcel_rows_count }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card border-0 rounded-3 shadow-sm h-100">
                                <div class="card-body text-center p-2">
                                    <i class="mdi mdi-file-document-outline text-primary font-22"></i>
                                    <div class="card-header-section">
                                        <h6 class="text-muted text-uppercase font-12 fw-semibold">
                                            {{ __('messages.total_invoice') }}</h6>
                                    </div>
                                    <div class="card-value-section">
                                        <h2 class="mb-0 font-22">${{ sprintf('%.2f', $calculation->total_invoice) }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card border-0 rounded-3 shadow-sm h-100">
                                <div class="card-body text-center p-2">
                                    <i class="mdi mdi-star text-warning font-22"></i>
                                    <div class="card-header-section">
                                        <h6 class="text-muted text-uppercase font-12 fw-semibold">
                                            {{ __('messages.bonus') }}</h6>
                                    </div>
                                    <div class="card-value-section">
                                        <h2 class="mb-0 font-22">${{ number_format($calculation->bonus, 2) }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card border-0 rounded-3 shadow-sm h-100">
                                <div class="card-body text-center p-2">
                                    <i class="mdi mdi-cash text-danger font-22"></i>
                                    <div class="card-header-section">
                                        <h6 class="text-muted text-uppercase font-12 fw-semibold">
                                            {{ __('messages.cash_advance') }}</h6>
                                    </div>
                                    <div class="card-value-section">
                                        <h2 class="mb-0 text-danger font-22">
                                            ${{ number_format($calculation->cash_advance, 2) }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card border-0 rounded-3 shadow bg-primary h-100">
                                <div class="card-body text-center p-2">
                                    <i class="mdi mdi-cash-multiple text-white font-22"></i>
                                    <div class="card-header-section">
                                        <h6 class="text-white text-uppercase font-12 fw-semibold">
                                            {{ __('messages.finalamount') }}</h6>
                                    </div>
                                    <div class="card-value-section">
                                        <h2 class="mb-0 text-white font-22">
                                            ${{ sprintf('%.2f', $calculation->final_amount) }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card border-0 rounded-3 shadow-sm h-100">
                                <div class="card-body text-center p-2">
                                    <i class="mdi mdi-car text-info font-22"></i>
                                    <div class="card-header-section">
                                        <h6 class="text-muted text-uppercase font-12 fw-semibold">
                                            {{ __('messages.vehicle_cost') }}</h6>
                                    </div>
                                    <div class="card-value-section">
                                        @php
                                            $vehicleCost =
                                                $calculation->vehicule_rental_price * $calculation->parcel_rows_count;
                                        @endphp
                                        <h2 class="mb-0 font-22">${{ $vehicleCost }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card border-0 rounded-3 shadow bg-success h-100">
                                <div class="card-body text-center p-2">
                                    <i class="mdi mdi-chart-line text-white font-22"></i>
                                    <div class="card-header-section">
                                        <h6 class="text-white text-uppercase font-12 fw-semibold">
                                            {{ __('messages.benefit') }}</h6>
                                    </div>
                                    <div class="card-value-section">
                                        @php
                                            $benefit = $calculation->total_invoice - $calculation->final_amount;
                                        @endphp
                                        <h2 class="mb-0 text-white font-22">${{ sprintf('%.2f', $benefit) }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($calculation->details)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm rounded-3">
                                    <div class="card-header bg-light p-2">
                                        <h5 class="card-title mb-0 font-16">{{ __('messages.additional_details') }}</h5>
                                    </div>
                                    <div class="card-body p-2">
                                        <p class="mb-0">{{ $calculation->details }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
