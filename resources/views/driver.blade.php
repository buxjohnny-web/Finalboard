@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">
                    {{ __('messages.driver') }} : #{{ $driver->driver_id }} - {{ $driver->full_name }}
                </h4>
            </div>
        </div>
    </div>

    <!-- Search form (simple client-side filter like Drivers page) -->
    <div class="mb-3">
        <div class="input-group">
            <input type="text" id="table-search-input" class="form-control"
                placeholder="{{ __('messages.search_placeholder') }}" aria-label="{{ __('messages.search') }}">
            <button class="btn btn-primary" id="table-search-button">
                {{ __('messages.search') }}
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-dt-wrapper">
                <div class="table-responsive">
                    <table id="weeks-table" class="table table-centered table-bordered w-100 mb-0">
                        <thead class="table-dark align-middle text-center">
                            <tr>
                                <th style="width: 120px;" class="text-center">
                                    {{ __('messages.weekno') }}
                                </th>
                                <th style="width: 90px;" class="text-center">
                                    {{ __('messages.daysworked') }}
                                </th>
                                <th style="width: 100px;" class="text-center">
                                    {{ __('messages.total_invoice') }}
                                </th>
                                <th style="width: 80px;" class="text-center">
                                    {{ __('messages.bonus') }}
                                </th>
                                <th style="width: 90px;" class="text-center">
                                    {{ __('messages.cash_advance') }}
                                </th>
                                <th style="width: 100px;" class="text-center">
                                    {{ __('messages.finalamount') }}
                                </th>
                                <th style="width: 80px;" class="text-center">
                                    {{ __('messages.calculate') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                use Carbon\Carbon;
                                use App\Models\Calculation;

                                $currentYear = Carbon::now()->year;
                                $firstDayOfYear = Carbon::create($currentYear, 1, 1);
                                // Find first Monday of the year
                                $firstMonday = $firstDayOfYear->copy();
                                if ($firstMonday->dayOfWeek !== Carbon::MONDAY) {
                                    $firstMonday = $firstMonday->next(Carbon::MONDAY);
                                }

                                $today = Carbon::today();
                                $week = 1;
                                $current = $firstMonday->copy();
                                $locale = app()->getLocale();
                            @endphp
                            @while ($current->lessThanOrEqualTo($today))
                                @php
                                    $weekLabel = $locale == 'fr' ? 'S' . $week : 'W' . $week;

                                    // Calculate start date (Monday) and end date (Sunday)
                                    $weekStart = $current->copy();
                                    $weekEnd = $current->copy()->addDays(6);
                                    $dateRange = $weekStart->format('M d') . ' - ' . $weekEnd->format('M d');
                                    $fullWeekLabel = $week . ' | ' . $dateRange;

                                    $calculation = Calculation::where('driver_id', $driver->id)
                                        ->where('week_number', $week)
                                        ->first();

                                    $parcelRowsCount = $calculation ? $calculation->parcel_rows_count : 0;
                                    $totalInvoice = $calculation ? $calculation->total_invoice : 0;
                                    $bonus = $calculation ? $calculation->bonus : 0;
                                    $cashAdvance = $calculation ? $calculation->cash_advance : 0;
                                    $finalAmount = $calculation ? $calculation->final_amount : 0;
                                @endphp
                                <tr class="clickable-row"
                                    data-href="{{ route('paydetails.show', ['driver' => $driver->id, 'week' => $week]) }}">
                                    <td class="fw-bold text-nowrap text-center align-middle">
                                        <span class="d-none d-md-inline">{{ $fullWeekLabel }}</span>
                                        <span class="d-inline d-md-none">{{ $weekLabel }}</span>
                                    </td>
                                    <td class="text-nowrap text-center align-middle">
                                        {{ $parcelRowsCount }}
                                    </td>
                                    <td class="text-nowrap text-center align-middle">
                                        ${{ sprintf('%.2f', $totalInvoice) }}
                                    </td>
                                    <td class="text-nowrap text-center align-middle">
                                        ${{ number_format($bonus, 2) }}
                                    </td>
                                    <td class="text-nowrap text-center align-middle">
                                        ${{ number_format($cashAdvance, 2) }}
                                    </td>
                                    <td class="text-nowrap text-center align-middle">
                                        ${{ sprintf('%.2f', $finalAmount) }}
                                    </td>
                                    <td class="text-center align-middle">
                                        <a href="{{ route('calculate.show', ['driver' => $driver->id, 'week' => $week]) }}"
                                            class="btn btn-info btn-sm">
                                            <i class="mdi mdi-calculator d-md-none" style="font-size: 1.25rem;"></i>
                                            <span class="d-none d-md-inline">{{ __('messages.calculate') }}</span>
                                        </a>
                                    </td>
                                </tr>
                                @php
                                    $week++;
                                    $current->addWeek();
                                @endphp
                            @endwhile
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Simple text filter (like Drivers page)
        function filterTable() {
            const term = document.getElementById('table-search-input').value.toLowerCase();
            document.querySelectorAll('#weeks-table tbody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
            });
        }

        document.getElementById('table-search-input').addEventListener('input', filterTable);
        document.getElementById('table-search-button').addEventListener('click', filterTable);

        // Make rows clickable (preserve clicks on links/buttons)
        document.querySelectorAll('.clickable-row').forEach(row => {
            row.addEventListener('click', function(e) {
                if (e.target.closest('a, button')) return;
                const href = this.dataset.href;
                if (href) window.location.assign(href);
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        /* Keep same compact, single-line table style as Drivers (no DataTables) */
        #weeks-table th,
        #weeks-table td {
            white-space: nowrap;
            vertical-align: middle;
        }

        .clickable-row {
            cursor: pointer;
        }

        .clickable-row:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        /* Optional: on very small screens, allow horizontal scroll (already handled by .table-responsive) */
    </style>
@endpush
