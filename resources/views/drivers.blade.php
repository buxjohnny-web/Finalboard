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
            <table class="table table-centered table-nowrap w-100" id="products-table">
                <thead class="table-dark">
                    <tr>
                        <th class="control"></th>
                        <th class="status-col">
                            <span class="d-inline d-md-none">{{ __('messages.active') }}</span>
                            <span class="d-none d-md-inline">#</span>
                        </th>
                        <th>{{ __('messages.driver') }}</th>
                        <th>{{ __('messages.phone') }}</th>
                        <th>{{ __('messages.added_by') }}</th>
                        <th>{{ __('messages.created_on') }}</th>
                        <th class="text-center" style="width:120px;">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($drivers as $driver)
                        @php
                            $addedByUser = \App\Models\User::find($driver->added_by);
                            $firstName = explode(' ', trim($driver->full_name))[0] ?? $driver->full_name;
                        @endphp
                        <tr>
                            <td></td>
                            <td class="text-center">
                                <div>
                                    <input type="checkbox" id="switch-driver-{{ $driver->id }}"
                                        class="driver-active-toggle" data-id="{{ $driver->id }}"
                                        data-url="{{ route('drivers.toggleActive', $driver->id) }}" data-switch="success"
                                        {{ $driver->active ? 'checked' : '' }} />
                                    <label for="switch-driver-{{ $driver->id }}"
                                        data-on-label="{{ __('messages.active') }}"
                                        data-off-label="{{ __('messages.inactive') ?: 'Inactive' }}"
                                        class="mb-0 d-block driver-switch-label"></label>
                                </div>
                            </td>
                            <td>
                                {{ $driver->driver_id }} -
                                <span class="d-inline d-md-none">{{ $firstName }}</span>
                                <span class="d-none d-md-inline">{{ $driver->full_name }}</span>
                            </td>
                            <td>{{ $driver->phone_number }}</td>
                            <td>{{ $addedByUser ? $addedByUser->full_name : '' }}</td>
                            <td>{{ \Carbon\Carbon::parse($driver->created_at)->format('d-m-y') }}</td>
                            <td class="text-center">
                                <a href="{{ route('drivers.show', $driver->id) }}" class="action-icon"
                                    title="{{ __('messages.view') }}"><i class="mdi mdi-eye"></i></a>
                                <a href="{{ route('drivers.edit', $driver->id) }}" class="action-icon"
                                    title="{{ __('messages.edit') }}"><i class="mdi mdi-pencil"></i></a>
                                <form action="{{ route('drivers.delete', $driver->id) }}" method="POST"
                                    class="d-inline m-0 p-0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-icon text-danger border-0 bg-transparent p-0"
                                        onclick="return confirm('{{ __('messages.confirm_delete_driver') }}')"
                                        title="{{ __('messages.delete') }}"><i class="mdi mdi-trash-can"></i></button>
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
        $(function() {
            const activeTitle = @json(__('messages.active'));
            const activeWord = (function(w) {
                return (w && w !== 'messages.active') ? w : 'Active';
            })(@json(__('messages.active')));
            const inactiveWord = (function(w) {
                return (w && w !== 'messages.inactive') ? w : 'Inactive';
            })(@json(__('messages.inactive')));

            function isMobile() {
                return window.matchMedia("(max-width: 576px)").matches;
            }

            const dt = $('#products-table').DataTable({
                paging: true,
                pageLength: 50,
                lengthChange: false,
                autoWidth: false,
                order: [
                    [2, 'asc']
                ],
                responsive: {
                    details: {
                        type: 'column',
                        target: 'td.control',
                        renderer: function(api, rowIdx, columns) {
                            const rows = columns
                                .filter(c => c.hidden)
                                .map(c => {
                                    if (c.title === activeTitle) {
                                        // Always output without static text label; desktop label not needed anymore.
                                        return `<div class="detail-active-line" data-active-field="1">${c.data}</div>`;
                                    }
                                    return `<div class="py-1"><strong>${c.title}:</strong> ${c.data}</div>`;
                                })
                                .join('');
                            return rows ? `<div class="p-2 small dt-row-details">${rows}</div>` : false;
                        }
                    }
                },
                columnDefs: [{
                        targets: 0,
                        className: 'control dtr-control',
                        orderable: false
                    },
                    {
                        targets: 1,
                        responsivePriority: 10000,
                        orderable: false
                    },
                    {
                        targets: 2,
                        responsivePriority: 1
                    },
                    {
                        targets: 3,
                        responsivePriority: 10002,
                        orderable: false
                    },
                    {
                        targets: 4,
                        responsivePriority: 10003,
                        orderable: false
                    },
                    {
                        targets: 5,
                        responsivePriority: 10004,
                        orderable: false
                    },
                    {
                        targets: 6,
                        responsivePriority: 2,
                        orderable: false
                    }
                ],
                dom: "<'row'<'col-12'tr>>" + "<'row'<'col-12'p>>",
                language: {
                    paginate: {
                        previous: '&lt;',
                        next: '&gt;'
                    }
                }
            });

            function normalizeSwitchLabels() {
                const mobile = isMobile();
                document.querySelectorAll('input.driver-active-toggle + label').forEach(label => {
                    if (mobile) {
                        // Remove text on mobile
                        label.setAttribute('data-on-label', '');
                        label.setAttribute('data-off-label', '');
                        label.classList.add('switch-compact');
                    } else {
                        label.setAttribute('data-on-label', activeWord);
                        label.setAttribute('data-off-label', inactiveWord);
                        label.classList.remove('switch-compact');
                    }
                });
            }

            // Initial
            normalizeSwitchLabels();

            // Re-apply when responsive rows open/close
            $('#products-table').on('responsive-display.dt', function() {
                normalizeSwitchLabels();
            });

            // Resize listener
            let resizeTimer;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(normalizeSwitchLabels, 120);
            });

            // Search
            $('#table-search-input').on('input', function() {
                dt.search(this.value).draw();
            });
            $('#table-search-button').on('click', function() {
                dt.search($('#table-search-input').val()).draw();
            });
        });

        // AJAX toggle
        document.addEventListener('change', e => {
            if (e.target.classList.contains('driver-active-toggle')) {
                const cb = e.target;
                fetch(cb.dataset.url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        active: cb.checked ? 1 : 0
                    })
                }).catch(err => console.error('Status update failed', err));
            }
        });
    </script>
@endpush
