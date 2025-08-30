@extends('layouts.master')

@section('content')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3 mb-3">
        <h4 class="page-title m-0">{{ __('messages.drivers') }}</h4>
        <button type="button" class="btn btn-info ms-auto" onclick="window.location='{{ route('newdriver') }}'">
            <i class="mdi mdi-plus me-1"></i> <span>{{ __('messages.add_driver_btn') }}</span>
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="d-flex justify-content-center">
        <div class="card" style="min-width:380px; max-width:820px; width:100%;">
            <div class="card-body">
                <div class="table-dt-wrapper">
                    <table id="drivers-table" class="table table-centered table-bordered dt-responsive w-100 mb-0">
                        <thead class="table-dark align-middle">
                        <tr>
                            <th style="width: 80px;">{{ __('messages.id') }}</th>
                            <th style="width: 340px;">{{ __('messages.name') }}</th>
                            <th style="width: 140px;">{{ __('messages.actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($drivers as $driver)
                            <tr class="clickable-row" data-href="{{ route('drivers.show', $driver->id) }}" style="cursor:pointer;">
                                <td>{{ $driver->driver_id }}</td>
                                <td>
                                    {{ explode(' ', $driver->full_name)[0] }}
                                    {{ isset(explode(' ', $driver->full_name)[1]) ? strtoupper(substr(explode(' ', $driver->full_name)[1], 0, 1)) . '.' : '' }}
                                </td>
                                <td>
                                    <div class="d-flex flex-row gap-1">
                                        <a class="driver-action-btn btn btn-success btn-xs" href="{{ route('drivers.edit', $driver->id) }}" title="{{ __('messages.edit_driver') }}">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <form action="{{ route('drivers.delete', $driver->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button class="driver-action-btn btn btn-danger btn-xs" type="submit" title="{{ __('messages.delete_driver_btn') }}" onclick="return confirm('{{ __('messages.confirm_delete_driver') }}')">
                                                <i class="mdi mdi-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/vendor/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/responsive.bootstrap5.min.js') }}"></script>
    <script>
        $(function() {
            $('#drivers-table').DataTable({
                responsive: true,
                autoWidth: false,
                language: {
                    url: "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
                },
                ordering: false,
                // Put length and filter in a single flex row, always
                dom: "<'dt-top-controls d-flex flex-row align-items-center justify-content-start gap-2 mb-2'l f>" +
                    "<'table-responsive'tr>" +
                    "<'row mt-2'<'col-md-5'i><'col-md-7'p>>"
            });

            // Clickable rows, except action buttons
            document.querySelectorAll('.clickable-row').forEach(function(row) {
                row.addEventListener('click', function(e) {
                    if (e.target.closest('.driver-action-btn')) {
                        return;
                    }
                    window.location = row.getAttribute('data-href');
                });
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        /* Remove row hover effect for thead */
        #drivers-table thead tr:hover, #drivers-table thead th:hover {
            background-color: inherit !important;
            cursor: default !important;
        }
        /* Controls always in a row, even on mobile */
        .dt-top-controls {
            gap: .7rem !important;
            margin-bottom: 1rem;
        }
        .dt-top-controls .dataTables_length,
        .dt-top-controls .dataTables_filter {
            margin-bottom: 0 !important;
        }
        .dt-top-controls .dataTables_length label,
        .dt-top-controls .dataTables_filter label {
            width: auto;
            font-weight: 500;
            margin-bottom: 0;
            margin-right: 0.4rem;
        }
        .dt-top-controls .dataTables_length select,
        .dt-top-controls .dataTables_filter input {
            width: auto !important;
            min-width: 55px;
            display: inline-block;
            margin-left: .2rem;
            height: 2rem;
            font-size: 0.97rem;
            padding: 2px 6px;
        }

        /* Smaller pagination buttons */
        .dataTables_paginate .pagination {
            justify-content: center !important;
        }
        .dataTables_paginate .paginate_button {
            font-size: 0.98rem !important;
            padding: 0.13rem 1.1rem !important;
            margin: 0 2px !important;
            border-radius: 0.25rem !important;
            min-width: 90px;
        }
        .dataTables_paginate .paginate_button.current,
        .dataTables_paginate .paginate_button:active {
            font-size: 0.98rem !important;
            padding: 0.13rem 1.1rem !important;
            border-radius: 0.25rem !important;
            min-width: 90px;
        }

        /* Responsive tweaks for mobile */
        @media (max-width: 991.98px) {
            .card-body {
                padding-left: 2px !important;
                padding-right: 2px !important;
            }
            .table-dt-wrapper {
                overflow-x: auto;
            }
            #drivers-table {
                min-width: 320px;
                width: 100% !important;
                font-size: 0.98rem;
            }
            #drivers-table td, #drivers-table th {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
            .dt-top-controls {
                flex-direction: row !important;
                gap: 0.5rem !important;
                margin-bottom: 0.7rem !important;
            }
            .dt-top-controls .dataTables_length,
            .dt-top-controls .dataTables_filter {
                float: none;
                width: auto;
                display: inline-block;
                vertical-align: middle;
                margin-bottom: 0 !important;
            }
            .dataTables_paginate .paginate_button {
                min-width: 75px;
                font-size: 0.95rem !important;
                padding: 0.12rem 0.7rem !important;
            }
        }
    </style>
@endpush
