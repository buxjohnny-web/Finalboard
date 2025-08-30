@extends('layouts.master')

@section('content')
<p></p>
    <div class="card">
        <div class="card-body">
            <div class="table-dt-wrapper">
                <table id="weeks-datatable" class="table table-centered table-bordered table-hover w-100 mb-0">
                    <thead class="table-dark align-middle text-center">
<tr>
    <th style="width: 60px;" class="text-center">
        {{ app()->getLocale() == 'fr' ? __('messages.s_week') : __('messages.w_week') }}
    </th>
    <th style="width: 90px;" class="text-center">
        {{ __('messages.intelcom_invoice') }}
    </th>
    <th style="width: 80px;" class="text-center">
        <span class="d-none d-md-inline">{{ __('messages.calculate') }}</span>
        <span class="d-inline d-md-none">{{ __('messages.calculate') }}</span>
    </th>
</tr>
</thead>
<tbody>
@php
    use Carbon\Carbon;
    $startOfYear = Carbon::create(null, 1, 1);
    $today = Carbon::today();
    $week = 1;
    $current = $startOfYear->copy()->startOfWeek(Carbon::MONDAY);
    $locale = app()->getLocale();
@endphp
@while ($current->lessThanOrEqualTo($today))
    @php
        $weekLabel = $locale == 'fr' ? 'S'.$week : 'W'.$week;
    @endphp
    <tr>
        <td class="fw-bold text-nowrap text-center align-middle">{{ $weekLabel }}</td>
        <td class="text-nowrap text-center align-middle">
            <span class="badge bg-primary rounded-pill py-1 px-2 fs-7">#INV-{{ $week }}</span>
        </td>
        <td class="text-center align-middle">
            <a href="{{ route('calculate.show', ['driver' => $driver->id, 'week' => $week]) }}"
               class="btn btn-info btn-sm d-inline d-md-none d-flex justify-content-center align-items-center p-0 mx-auto"
               style="width: 40px; height: 40px;">
                <i class="mdi mdi-calculator" style="font-size: 1.25rem; margin: 0;"></i>
            </a>
            <a href="{{ route('calculate.show', ['driver' => $driver->id, 'week' => $week]) }}"
               class="btn btn-info btn-sm px-2 py-1 fw-semibold d-none d-md-inline"
               style="font-size: 0.9rem;">
                {{ __('messages.calculate') }}
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
                language: {
                    url: "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
                },
                dom: "<'row mb-2'<'col-md-6'l><'col-md-6'f>>" +
                    "<'table-responsive'tr>" +
                    "<'row mt-2'<'col-md-5'i><'col-md-7'p>>"
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
            .dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate {
                text-align: left !important;
            }
            .dataTables_filter {
                margin-top: 1rem;
            }
            #weeks-datatable th:nth-child(3) span.d-inline.d-md-none {
                display: inline !important;
            }
            #weeks-datatable td:nth-child(3) .btn.d-inline.d-md-none i.bi-calculator {
                display: inline !important;
            }
        }
    </style>
@endpush