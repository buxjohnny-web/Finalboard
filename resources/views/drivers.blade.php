@extends('layouts.master')

@push('styles')
    <style>
        /* Mobile-specific row click affordance */
        @media (max-width: 767.98px) {
            .clickable-mobile-row {
                cursor: pointer;
            }

            .clickable-mobile-row:hover {
                background-color: #f8f9fa;
            }
        }
    </style>
@endpush

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
                placeholder="{{ __('messages.search_placeholder') }}" aria-label="{{ __('messages.search') }}">
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
                            <!-- Mobile: only a grey circle icon; Desktop: translated label -->
                            <th class="text-center" style="width: 40px;">
                                <span class="d-inline d-md-none" aria-hidden="true">
                                    <i class="mdi mdi-circle text-secondary"></i>
                                </span>
                                <span class="d-none d-md-inline">{{ __('messages.status') }}</span>
                                <span class="visually-hidden">{{ __('messages.status') }}</span>
                            </th>
                            <th style="width: 60px;">{{ __('messages.id') }}</th>
                            <th>{{ __('messages.name') }}</th>

                            <!-- Hidden on mobile -->
                            <th class="d-none d-md-table-cell">{{ __('messages.phone') }}</th>
                            <th class="d-none d-md-table-cell">{{ __('messages.added_by') }}</th>
                            <th class="d-none d-md-table-cell">{{ __('messages.created_on') }}</th>

                            <th class="text-center" style="width: 120px;">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($drivers as $driver)
                            @php
                                $fullName = trim((string) $driver->full_name);
                                $parts = preg_split('/\s+/', $fullName);
                                $first = $parts[0] ?? '';
                                $lastInitial = isset($parts[1]) ? mb_substr($parts[1], 0, 1) . '.' : '';
                                $mobileName = trim($first . ' ' . $lastInitial); // Mobile: First L.
                            @endphp
                            <tr class="clickable-mobile-row" data-href="{{ route('drivers.show', $driver->id) }}">
                                <td class="text-center">
                                    @if ($driver->active == 1)
                                        <i class="mdi mdi-circle text-success"
                                            aria-label="{{ __('messages.status') }}"></i>
                                    @else
                                        <i class="mdi mdi-circle text-danger" aria-label="{{ __('messages.status') }}"></i>
                                    @endif
                                </td>
                                <td>{{ $driver->driver_id }}</td>
                                <td>
                                    <span class="d-inline d-md-none">{{ $mobileName }}</span>
                                    <span class="d-none d-md-inline">{{ $driver->full_name }}</span>
                                </td>

                                <!-- Hidden on mobile -->
                                <td class="d-none d-md-table-cell">{{ $driver->phone_number }}</td>
                                <td class="d-none d-md-table-cell">{{ $driver->addedBy->name ?? '' }}</td>
                                <td class="d-none d-md-table-cell">
                                    {{ \Carbon\Carbon::parse($driver->created_at)->format('d-m-y') }}</td>

                                <td class="text-center">
                                    <!-- Hide the "view" icon on mobile; whole row is clickable there -->
                                    <a href="{{ route('drivers.show', $driver->id) }}"
                                        class="action-icon d-none d-md-inline" aria-label="{{ __('messages.view') }}"
                                        title="{{ __('messages.view') }}">
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

        document.getElementById('table-search-input').addEventListener('input', filterTable);
        document.getElementById('table-search-button').addEventListener('click', filterTable);

        // Make entire row clickable on mobile ONLY, excluding action controls
        function isMobile() {
            return window.matchMedia('(max-width: 767.98px)').matches;
        }

        document.querySelectorAll('.clickable-mobile-row').forEach(row => {
            row.addEventListener('click', function(e) {
                if (!isMobile()) return;

                if (e.target.closest('.action-icon') || e.target.closest('button') || e.target.closest(
                    'a')) {
                    return;
                }

                const href = this.dataset.href;
                if (href) {
                    window.location.assign(href);
                }
            });
        });
    </script>
@endpush
