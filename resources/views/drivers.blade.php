@extends('layouts.master')

@section('content')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3 mb-3">
        <h4 class="page-title m-0">{{ __('messages.drivers') }}</h4>
        <button type="button" class="btn btn-info ms-auto" onclick="window.location='{{ route('newdriver') }}'">
            <i class="mdi mdi-plus me-1"></i> <span>{{ __('messages.add_driver_btn') }}</span>
        </button>
    </div>

    <!-- Search form -->
    <div class="mb-3">
        <div class="input-group">
            <input type="text" id="table-search-input" class="form-control"
                placeholder="{{ __('messages.search_placeholder') }}">
            <button class="btn btn-primary" id="table-search-button">
                {{ __('messages.search') }}
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-centered table-striped w-100" id="products-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">{{ __('messages.status') }}</th>
                            <th style="width: 60px;">{{ __('messages.id') }}</th>
                            <th>{{ __('messages.name') }}</th>
                            <th>{{ __('messages.phone') }}</th>
                            <th>{{ __('messages.added_by') }}</th>
                            <th>{{ __('messages.created_on') }}</th>
                            <th class="text-center" style="width: 120px;">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($drivers as $driver)
                            <tr>
                                <td class="text-center">
                                    @if ($driver->active == 1)
                                        <i class="mdi mdi-circle text-success"></i>
                                    @else
                                        <i class="mdi mdi-circle text-danger"></i>
                                    @endif
                                </td>
                                <td>{{ $driver->driver_id }}</td>
                                <td>{{ $driver->full_name }}</td>
                                <td>{{ $driver->phone_number }}</td>
                                <td>{{ $driver->addedBy->name ?? '' }}</td>
                                <td>{{ \Carbon\Carbon::parse($driver->created_at)->format('d-m-y') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('drivers.show', $driver->id) }}" class="action-icon">
                                        <i class="mdi mdi-eye"></i>
                                    </a>
                                    <a href="{{ route('drivers.edit', $driver->id) }}" class="action-icon">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <form action="{{ route('drivers.delete', $driver->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-icon text-danger border-0 bg-transparent p-0"
                                            onclick="return confirm('{{ __('messages.confirm_delete_driver') }}')">
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
    </div>
@endsection

@push('scripts')
    <script>
        function filterTable() {
            const term = document.getElementById('table-search-input').value.toLowerCase();
            document.querySelectorAll('#products-table tbody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
            });
        }

        // Instant search on every keystroke
        document.getElementById('table-search-input')
            .addEventListener('input', filterTable);

        // Keep button support if needed
        document.getElementById('table-search-button')
            .addEventListener('click', filterTable);
    </script>
@endpush
