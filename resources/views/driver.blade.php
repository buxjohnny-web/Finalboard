@extends('layouts.master')
@section('content')
    <br>
    <div class="row text-center">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">
                    {{ __('messages.driver') }} : #{{ $driver->driver_id }} - {{ $driver->full_name }}
                </h4>
            </div>
        </div>
    </div>

    <!-- Search form (same as Drivers page) -->
    <div class="mb-3">
        <div class="input-group">
            <input type="text" id="table-search-input" class="form-control"
                placeholder="{{ __('messages.searchdriverpage') }}" aria-label="{{ __('messages.search') }}">
            <button class="btn btn-info" id="table-search-button">
                {{ __('messages.search') }}
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-dt-wrapper">
                <div class="table-responsive">
                    <!-- Match Drivers table style: striped, centered, single-line cells (no DataTables) -->
                    <table id="weeks-table" class="table table-centered table-striped iboard-table w-100 mb-0">
                        <thead class="align-middle text-center">
                            <tr>
                                <th style="width: 120px;" class="text-center">
                                    {{ __('messages.weekno') }}
                                </th>
                                <th style="width: 90px;" class="text-center">
                                    {{ __('messages.daysworked') }}
                                </th>
                                <th style="width: 100px;" class="text-center d-none d-md-table-cell">
                                    {{ __('messages.total_invoice') }}
                                </th>
                                <th style="width: 80px;" class="text-center d-none d-md-table-cell">
                                    {{ __('messages.bonus') }}
                                </th>
                                <th style="width: 90px;" class="text-center d-none d-md-table-cell">
                                    {{ __('messages.cash_advance') }}
                                </th>
                                <th style="width: 100px;" class="text-center">
                                    {{ __('messages.finalamount') }}
                                </th>
                                <th style="width: 80px;" class="text-center">
                                    {{ __('messages.calculatexx') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                use Carbon\Carbon;
                                use App\Models\Calculation;

                                $currentYear = Carbon::now()->year;
                                $firstDayOfYear = Carbon::create($currentYear, 1, 1);
                                // Find first Monday of the year (on/after Jan 1)
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

                                    $rowId = 'row-week-' . $week;
                                    $calcUrl = route('calculate.show', ['driver' => $driver->id, 'week' => $week]);
                                    $editUrl = route('calculate.edit', ['driver' => $driver->id, 'week' => $week]);
                                    $resetUrl = route('calculate.reset', ['driver' => $driver->id, 'week' => $week]);
                                @endphp
                                <tr id="{{ $rowId }}" class="clickable-row"
                                    data-href="{{ route('paydetails.show', ['driver' => $driver->id, 'week' => $week]) }}"
                                    data-calc-url="{{ $calcUrl }}">
                                    <td class="fw-bold text-nowrap text-center align-middle">
                                        <span class="d-none d-md-inline">{{ $fullWeekLabel }}</span>
                                        <span class="d-inline d-md-none">{{ $weekLabel }}</span>
                                    </td>
                                    <td class="text-nowrap text-center align-middle">
                                        {{ $parcelRowsCount }}
                                    </td>
                                    <td class="text-nowrap text-center align-middle d-none d-md-table-cell">
                                        ${{ sprintf('%.2f', $totalInvoice) }}
                                    </td>
                                    <td class="text-nowrap text-center align-middle d-none d-md-table-cell">
                                        ${{ number_format($bonus, 2) }}
                                    </td>
                                    <td class="text-nowrap text-center align-middle d-none d-md-table-cell">
                                        ${{ number_format($cashAdvance, 2) }}
                                    </td>
                                    <td class="text-nowrap text-center align-middle">
                                        ${{ sprintf('%.2f', $finalAmount) }}
                                    </td>
                                    <td class="text-center align-middle">
                                        @if ($totalInvoice > 0)
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                <!-- Edit calculation (Drivers actions style) -->
                                                <a href="{{ $editUrl }}" class="action-icon"
                                                    aria-label="{{ __('messages.edit') }}"
                                                    title="{{ __('messages.edit') }}">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <!-- Reset calculation (open modal, AJAX on confirm) -->
                                                <button type="button"
                                                    class="action-icon text-danger border-0 bg-transparent p-0 js-reset-btn"
                                                    data-reset-url="{{ $resetUrl }}"
                                                    data-row-selector="#{{ $rowId }}"
                                                    aria-label="{{ __('messages.reset') }}"
                                                    title="{{ __('messages.reset') }}">
                                                    <i class="mdi mdi-refresh"></i>
                                                </button>
                                            </div>
                                        @else
                                            <a href="{{ $calcUrl }}" class="btn btn-info btn-sm">
                                                <i class="mdi mdi-calculator d-md-none" style="font-size: 1.25rem;"></i>
                                                <span class="d-none d-md-inline">{{ __('messages.calculate') }}</span>
                                            </a>
                                        @endif
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

    <!-- Reset confirm modal -->
    <div class="modal fade" id="resetConfirmModal" tabindex="-1" role="dialog" aria-labelledby="resetConfirmModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="resetConfirmModalLabel">{{ __('messages.reset_calculation_title') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('messages.close') }}"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">{{ __('messages.confirm_reset_calculation') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.no') }}</button>
                    <button type="button" class="btn btn-danger" id="confirmResetBtn">{{ __('messages.yes') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // i18n strings for JS
        const i18nCalculate = @json(__('messages.calculate'));
        const i18nResetFailed = @json(__('messages.reset_failed'));

        // Simple text filter (like Drivers page)
        function filterTable() {
            const term = document.getElementById('table-search-input').value.toLowerCase();
            document.querySelectorAll('#weeks-table tbody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
            });
        }

        // Ensure handlers after DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('table-search-input').addEventListener('input', filterTable);
            document.getElementById('table-search-button').addEventListener('click', filterTable);

            // Row click: event delegation on tbody
            const tbody = document.querySelector('#weeks-table tbody');
            if (tbody) {
                tbody.addEventListener('click', function(e) {
                    // ignore clicks on links or buttons (and their children)
                    if (e.target.closest('a, button')) return;
                    const row = e.target.closest('tr.clickable-row');
                    if (!row) return;
                    const href = row.dataset.href;
                    if (href) window.location.assign(href);
                });
            }

            // Reset button: event delegation on document (covers any dynamic rows)
            let pendingReset = {
                url: null,
                rowSelector: null
            };

            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.js-reset-btn');
                if (!btn) return;

                e.preventDefault();
                e.stopPropagation();

                pendingReset.url = btn.dataset.resetUrl || '';
                pendingReset.rowSelector = btn.dataset.rowSelector || '';

                const modalEl = document.getElementById('resetConfirmModal');
                if (!modalEl) return;

                let modalInstance = null;
                if (window.bootstrap && bootstrap.Modal) {
                    // get or create instance
                    modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                }
                if (modalInstance) modalInstance.show();

                // Confirm button handler (bind once)
                const confirmBtn = document.getElementById('confirmResetBtn');
                if (!confirmBtn.dataset.bound) {
                    confirmBtn.dataset.bound = '1';
                    confirmBtn.addEventListener('click', async function() {
                        if (!pendingReset.url) {
                            if (modalInstance) modalInstance.hide();
                            return;
                        }
                        try {
                            const meta = document.querySelector('meta[name="csrf-token"]');
                            const csrf = meta ? meta.content : '';

                            const resp = await fetch(pendingReset.url, {
                                method: 'DELETE',
                                credentials: 'same-origin',
                                headers: {
                                    'X-CSRF-TOKEN': csrf,
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            });

                            // Be tolerant to non-JSON responses and log details
                            let ok = resp.ok;
                            let data = null;
                            const ct = resp.headers.get('content-type') || '';

                            try {
                                if (ct.includes('application/json')) {
                                    data = await resp.json();
                                    if (data && data.success === true) ok = true;
                                }
                            } catch (_) {}

                            if (!ok) {
                                const body = await resp.text().catch(() => '');
                                console.error('Reset failed', {
                                    status: resp.status,
                                    ct,
                                    body
                                });
                                throw new Error('reset_failed');
                            }

                            // Update row values in the table UI
                            const row = document.querySelector(pendingReset.rowSelector);
                            if (row) {
                                const tds = row.querySelectorAll('td');
                                // 0 week | 1 daysWorked | 2 totalInvoice | 3 bonus | 4 cashAdvance | 5 finalAmount | 6 action
                                if (tds[1]) tds[1].textContent = '0';
                                if (tds[2]) tds[2].textContent = '$0.00';
                                if (tds[3]) tds[3].textContent = '$0.00';
                                if (tds[4]) tds[4].textContent = '$0.00';
                                if (tds[5]) tds[5].textContent = '$0.00';

                                if (tds[6]) {
                                    const calcUrl = row.dataset.calcUrl || '#';
                                    tds[6].innerHTML =
                                        '<a href="' + calcUrl +
                                        '" class="btn btn-info btn-sm">' +
                                        '<i class="mdi mdi-calculator d-md-none" style="font-size: 1.25rem;"></i>' +
                                        '<span class="d-none d-md-inline">' + i18nCalculate +
                                        '</span>' +
                                        '</a>';
                                }
                            }
                        } catch (err) {
                            alert(i18nResetFailed);
                        } finally {
                            if (modalInstance) modalInstance.hide();
                            pendingReset = {
                                url: null,
                                rowSelector: null
                            };
                        }
                    });
                }
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        /* Same compact, single-line look as Drivers table */
        .iboard-table th,
        .iboard-table td {
            white-space: nowrap;
            vertical-align: middle;
        }

        .clickable-row {
            cursor: pointer;
        }

        .clickable-row:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        /* Action icon style like Drivers table */
        .action-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.6rem;
            height: 1.6rem;
            font-size: 1rem;
            color: inherit;
            text-decoration: none;
            cursor: pointer;
        }

        .action-icon:hover {
            opacity: .85;
        }
    </style>
@endpush
