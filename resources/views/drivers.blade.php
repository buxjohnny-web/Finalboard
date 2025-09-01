@extends('layouts.master')

@push('styles')
    <link href="https://cdn.datatables.net/v/bs5/dt-2.0.8/r-3.0.2/datatables.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3 mb-3">
        <h4 class="page-title m-0">{{ __('messages.drivers') }}</h4>
        <button type="button" class="btn btn-info ms-auto" onclick="window.location='{{ route('newdriver') }}'">
            <i class="mdi mdi-plus me-1"></i> <span>{{ __('messages.add_driver_btn') }}</span>
        </button>
    </div>

    <div class="mb-3">
        <div class="input-group">
            <input type="text" id="table-search-input" class="form-control"
                placeholder="{{ __('messages.search_placeholder') }}" aria-label="{{ __('messages.search') }}">
            <button class="btn btn-info" id="table-search-button">
                {{ __('messages.search') }}
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-centered table-striped dt-responsive nowrap w-100" id="products-table">
                <thead>
                    <tr>
                        <th id="col-expand"></th>
                        <th id="col-status" class="text-center">
                            <span class="d-inline d-md-none" aria-hidden="true">
                                <i class="mdi mdi-circle text-secondary"></i>
                            </span>
                            <span class="d-none d-md-inline">{{ __('messages.status') }}</span>
                            <span class="visually-hidden">{{ __('messages.status') }}</span>
                        </th>
                        <th>{{ __('messages.driver') }}</th>
                        <th>{{ __('messages.phone') }}</th>
                        <th>{{ __('messages.added_by') }}</th>
                        <th>{{ __('messages.created_on') }}</th>
                        <th class="text-center" style="width: 120px;">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($drivers as $driver)
                        @php
                            $firstName = $driver->first_name ?? explode(' ', $driver->full_name)[0];
                            $addedByUser = \App\Models\User::find($driver->added_by);
                        @endphp
                        <tr data-href="{{ route('drivers.show', $driver->id) }}">
                            <td class="col-expand"></td>
                            <td class="text-center col-status">
                                @if ($driver->active == 1)
                                    <i class="mdi mdi-circle text-success" aria-label="{{ __('messages.status') }}"></i>
                                @else
                                    <i class="mdi mdi-circle text-danger" aria-label="{{ __('messages.status') }}"></i>
                                @endif
                            </td>
                            <td>
                                {{ $driver->driver_id }} - {{ $firstName }}
                            </td>
                            <td>{{ $driver->phone_number }}</td>
                            <td>{{ $addedByUser ? $addedByUser->full_name : '' }}</td>
                            <td>{{ \Carbon\Carbon::parse($driver->created_at)->format('d-m-y') }}</td>
                            <td class="text-center">
                                <a href="{{ route('drivers.show', $driver->id) }}" class="action-icon"
                                    aria-label="{{ __('messages.view') }}" title="{{ __('messages.view') }}">
                                    <i class="mdi mdi-eye"></i>
                                </a>
                                <a href="{{ route('drivers.edit', $driver->id) }}" class="action-icon"
                                    aria-label="{{ __('messages.edit') }}" title="{{ __('messages.edit') }}">
                                    <i class="mdi mdi-pencil"></i>
                                </a>
                                <form action="{{ route('drivers.delete', $driver->id) }}" method="POST"
                                    class="d-inline m-0 p-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-icon text-danger border-0 bg-transparent p-0"
                                        onclick="return confirm('{{ __('messages.confirm_delete_driver') }}')"
                                        aria-label="{{ __('messages.delete') }}" title="{{ __('messages.delete') }}">
                                        <i class="mdi mdi-trash-can"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/dt-2.0.8/r-3.0.2/datatables.min.js"></script>
    <script>
        $(document).ready(function() {
            var dt = $('#products-table').DataTable({
                responsive: true,
                autoWidth: false,
                // UPDATED: This dom gives pagination a full-width row to be centered in.
                dom: "<'row'<'col-sm-12'tr>>" + // The table
                    "<'row'<'col-sm-12'p>>", // The pagination in a full-width column
                lengthChange: false,
                order: [
                    [2, 'asc']
                ], // Order by ID-Name column
                language: {
                    url: "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}",
                    paginate: {
                        previous: "&lt;",
                        next: "&gt;"
                    }
                },
                columnDefs: [{
                        orderable: false,
                        searchable: false,
                        className: 'dtr-control',
                        targets: 0
                    },
                    {
                        responsivePriority: 1,
                        targets: 1
                    }, // Status
                    {
                        responsivePriority: 2,
                        targets: 2
                    }, // ID-Name
                    {
                        responsivePriority: 3,
                        targets: -1
                    } // Actions
                ]
            });

            $('#table-search-input').on('input', function() {
                dt.search(this.value).draw();
            });
            $('#table-search-button').on('click', function() {
                dt.search($('#table-search-input').val()).draw();
            });
        });
    </script>
@endpush
