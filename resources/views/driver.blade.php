@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">{{ __('messages.driver') }} : #{{ $driver->driver_id }} - {{ $driver->full_name }}
                </h4>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-dt-wrapper">
                <table id="weeks-datatable" class="table table-centered table-bordered w-100 mb-0">
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
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/vendor/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/dataTables.bootstrap5.js') }}"></script>
    <script>
        $(function() {
            $('#weeks-datatable').DataTable({
                paging: true,
                searching: true,
                ordering: false,
                info: false,
                autoWidth: false,
                responsive: {
                    details: false
                },
                columnDefs: [{
                        responsivePriority: 1,
                        targets: 0
                    }, // Week #
                    {
                        responsivePriority: 2,
                        targets: 5
                    }, // Final Amount
                    {
                        responsivePriority: 3,
                        targets: 6
                    }, // Calculate button
                    {
                        responsivePriority: 4,
                        targets: 1
                    },
                    {
                        responsivePriority: 5,
                        targets: 2
                    },
                    {
                        responsivePriority: 6,
                        targets: 3
                    },
                    {
                        responsivePriority: 7,
                        targets: 4
                    }
                ],
                language: {
                    url: "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
                },
                dom: "<'row mb-2'<'col-md-6'l><'col-md-6'f>>" +
                    "<'table-responsive'tr>" +
                    "<'row mt-2'<'col-md-5'i><'col-md-7'p>>"
            });

            // Custom function to handle responsive behavior
            function updateColumnVisibility() {
                if (window.innerWidth < 768) {
                    // Hide columns 1, 2, 3, 4 on mobile (indices 1-4)
                    for (let i = 1; i <= 4; i++) {
                        $('#weeks-datatable').DataTable().column(i).visible(false);
                    }
                } else {
                    // Show all columns on desktop
                    for (let i = 1; i <= 4; i++) {
                        $('#weeks-datatable').DataTable().column(i).visible(true);
                    }
                }
            }

            // Initial call on document ready
            updateColumnVisibility();

            // Call on window resize
            $(window).resize(function() {
                updateColumnVisibility();
            });

            // Make rows clickable
            $('.clickable-row').on('click', function(e) {
                // Don't navigate if clicking on a button or link
                if (!$(e.target).closest('a, button').length) {
                    window.location.href = $(this).data('href');
                }
            });
        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .dataTables_length label,
        .dataTables_filter label {
            width: 100%;
            font-weight: 500;
        }

        .dataTables_length select,
        .dataTables_filter input {
            width: auto !important;
            display: inline-block;
            margin-left: .5rem;
        }

        @media (max-width: 767.98px) {

            .dataTables_length,
            .dataTables_filter,
            .dataTables_info,
            .dataTables_paginate {
                text-align: left !important;
            }

            .dataTables_filter {
                margin-top: 1rem;
            }
        }

        .clickable-row {
            cursor: pointer;
        }

        .clickable-row:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }
    </style>
@endpush
