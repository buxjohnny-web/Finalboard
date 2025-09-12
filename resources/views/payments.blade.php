@extends('layouts.master')

@push('styles')
    <link href="https://cdn.datatables.net/v/bs5/dt-2.0.8/r-3.0.2/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    {{-- Moved inline CSS to public/assets/css/custom.css --}}
@endpush

@section('content')
    <div id="main-content">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3 mb-3 page-title-box">
            <h4 class="page-title m-0">{{ __('messages.payments_page_title') }}</h4>
        </div>

        <div id="upload-status-alert-placeholder" class="mt-3"></div>

        <div id="loader-div" class="alert alert-dark bg-dark text-light border-0 p-5 text-center">
            <div class="d-flex align-items-center justify-content-center gap-3">
                <strong class="fs-5">{{ __('messages.payments_processing_uploads') }}</strong>
                <div class="spinner-border" role="status" aria-hidden="true"></div>
            </div>
            <div class="small mt-2">{{ __('messages.payments_processing_wait') }}</div>
        </div>

        <div class="card mb-4" id="upload-card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('messages.batch_upload') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('payments.upload') }}" method="post" class="dropzone" id="batch-upload-dropzone"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="dz-message needsclick">
                        <i class="h1 text-muted dripicons-cloud-upload"></i>
                        <h3>{{ __('messages.drop_files_here') }}</h3>
                        <span class="text-muted font-13">{{ __('messages.pdf_only_max') }}</span>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results -->
        <div class="card d-none" id="results-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('messages.uploaded_invoices_summary') }}</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3" id="summary-line"></p>
                <div class="table-responsive">
                    <table id="uploads-table" class="table table-bordered table-nowrap w-100 align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th class="control"></th>
                                <th class="select-col"><input type="checkbox" id="select-all" /></th>
                                <th>{{ __('messages.id_name') }}</th>
                                <th>{{ __('messages.total_invoice') }}</th>
                                <th>{{ __('messages.daysworked') }}</th>
                                <th>{{ __('messages.total_parcels') }}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/v/bs5/dt-2.0.8/r-3.0.2/datatables.min.js"></script>
    <script>
        Dropzone.autoDiscover = false;

        const uploadCard = document.getElementById("upload-card");
        const resultsCard = document.getElementById("results-card");
        const summaryLine = document.getElementById("summary-line");
        const loaderDiv = document.getElementById("loader-div");
        const alertPlaceholder = document.getElementById("upload-status-alert-placeholder");
        const selectAll = document.getElementById('select-all');

        loaderDiv.style.display = "none";
        loaderDiv.style.opacity = 0;

        function fadeOut(el, duration = 250, cb) {
            el.style.transition = `opacity ${duration}ms`;
            el.style.opacity = 0;
            setTimeout(() => {
                el.classList.add('d-none');
                cb && cb();
            }, duration);
        }

        function fadeIn(el, duration = 250) {
            el.classList.remove('d-none');
            el.style.opacity = 0;
            el.style.transition = `opacity ${duration}ms`;
            requestAnimationFrame(() => el.style.opacity = 1);
        }

        const driverMap = new Map();
        let dt = null;

        function ensureDataTable() {
            if (dt) return;
            dt = new DataTable('#uploads-table', {
                paging: true,
                searching: true,
                info: true,
                pageLength: 50,
                lengthMenu: [
                    [50, 10, 25, 100, -1],
                    [50, 10, 25, 100, 'All']
                ],
                order: [
                    [2, 'asc']
                ],
                dom: '<"dt-top d-flex align-items-center"l f>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                language: {
                    search: '',
                    searchPlaceholder: 'Search',
                    lengthMenu: '_MENU_'
                },
                responsive: {
                    breakpoints: [{
                            name: 'mobile',
                            width: 0
                        },
                        {
                            name: 'tablet',
                            width: 576
                        },
                        {
                            name: 'desktop',
                            width: 992
                        }
                    ],
                    details: {
                        type: 'column',
                        target: 'td.control',
                        renderer: function(api, rowIdx, columns) {
                            const hidden = columns
                                .filter(c => c.hidden)
                                .map(c => `<div><strong>${c.title}:</strong> ${c.data}</div>`).join('');
                            return hidden ? `<div class="dt-row-details">${hidden}</div>` : '';
                        }
                    }
                },
                columnDefs: [{
                        targets: 0,
                        className: 'control dtr-control all',
                        orderable: false
                    },
                    {
                        targets: 1,
                        className: 'select-col all',
                        orderable: false,
                        width: '46px'
                    },
                    {
                        targets: 2,
                        className: 'all'
                    },
                    {
                        targets: 3,
                        className: 'min-tablet text-end'
                    },
                    {
                        targets: 4,
                        className: 'min-tablet text-end'
                    },
                    {
                        targets: 5,
                        className: 'min-tablet text-end'
                    }
                ]
            });

            // Post-init DOM tweaks (ensure placeholder + remove residual text)
            const filterLabel = document.querySelector('#uploads-table_filter label');
            if (filterLabel) {
                const input = filterLabel.querySelector('input');
                if (input) input.setAttribute('placeholder', 'Search');
                // Remove any stray text nodes (like "Search:")
                [...filterLabel.childNodes].forEach(n => {
                    if (n.nodeType === 3) n.textContent = '';
                });
            }
            const lengthLabel = document.querySelector('.dataTables_length label');
            if (lengthLabel) {
                // Remove any text around the select (like "entries per page")
                [...lengthLabel.childNodes].forEach(n => {
                    if (n.nodeType === 3) n.textContent = '';
                });
            }
        }

        function formatMoney(val) {
            if (val === 'N/A' || val == null || val === '') return 'N/A';
            const num = parseFloat(String(val).replace(/,/g, ''));
            return isNaN(num) ? 'N/A' : '$' + num.toFixed(2);
        }

        function sumMoney(a, b) {
            const pa = parseFloat(String(a).replace(/,/g, ''));
            const pb = parseFloat(String(b).replace(/,/g, ''));
            if (isNaN(pa) && isNaN(pb)) return 'N/A';
            if (isNaN(pa)) return pb.toFixed ? pb.toFixed(2) : pb;
            if (isNaN(pb)) return pa.toFixed ? pa.toFixed(2) : pa;
            return (pa + pb).toFixed(2);
        }

        function sumInt(a, b) {
            return (parseInt(a) || 0) + (parseInt(b) || 0);
        }

        function upsertRow(d) {
            ensureDataTable();
            const ex = driverMap.get(d.driver_id);
            if (ex) {
                ex.invoice_value = sumMoney(ex.invoice_value, d.invoice_value);
                ex.days_worked += d.days_worked;
                ex.total_parcels = sumInt(ex.total_parcels, d.total_parcels);
                ex.found = ex.found || d.found;
                if (d.driver_name && !ex.driver_name) ex.driver_name = d.driver_name;
            } else {
                driverMap.set(d.driver_id, {
                    ...d
                });
            }
            redrawTable();
        }

        function getFoundCount() {
            let c = 0;
            driverMap.forEach(v => v.found && c++);
            return c;
        }

        function redrawTable() {
            ensureDataTable();
            dt.clear();
            [...driverMap.values()].sort((a, b) => a.driver_id.localeCompare(b.driver_id))
                .forEach(r => {
                    const label = r.found ? `${r.driver_id} - ${r.driver_name}` : `${r.driver_id} (Not Found)`;
                    dt.row.add([
                        '',
                        `<input type="checkbox" class="row-select" value="${r.driver_id}">`,
                        label,
                        formatMoney(r.invoice_value),
                        r.days_worked,
                        r.total_parcels
                    ]);
                });
            dt.draw(false);
            updateSummary();
        }

        const completedTpl = @json(__('messages.payments_completed_valid_drivers', ['count' => ':count']));
        const summaryPattern = @json(__('messages.payments_summary_pattern'));

        function updateSummary() {
            const found = getFoundCount();
            summaryLine.textContent = summaryPattern
                .replace(':drivers', driverMap.size)
                .replace(':found', found)
                .replace(':not_found', driverMap.size - found);
        }

        // Re-apply select column class after each draw
        document.addEventListener('DOMContentLoaded', () => {
            ensureDataTable();
            dt.on('draw', () => {
                document.querySelectorAll('#uploads-table tbody tr').forEach(tr => {
                    const td = tr.querySelector('td:nth-child(2)');
                    if (td) td.classList.add('select-col');
                });
            });
        });

        document.addEventListener('change', e => {
            if (e.target === selectAll) {
                document.querySelectorAll('#uploads-table tbody .row-select')
                    .forEach(cb => cb.checked = selectAll.checked);
            }
        });

        let loaderShown = false;
        let uploadStartTime = null;
        const MIN_LOADER_MS = 5000;

        const dz = new Dropzone("#batch-upload-dropzone", {
            url: "{{ route('payments.upload') }}",
            paramName: "files",
            maxFilesize: 5,
            acceptedFiles: ".pdf",
            addRemoveLinks: true,
            parallelUploads: 3,
            autoProcessQueue: true,
            init: function() {
                this.on("sending", () => {
                    if (!loaderShown) {
                        loaderShown = true;
                        uploadStartTime = Date.now();
                        fadeOut(uploadCard, 150, () => {
                            loaderDiv.classList.remove('d-none');
                            loaderDiv.style.display = 'block';
                            requestAnimationFrame(() => loaderDiv.style.opacity = 1);
                        });
                    }
                });

                this.on("success", (file, response) => {
                    if (response?.success && response?.uploaded?.driver_id) {
                        const up = response.uploaded;
                        upsertRow({
                            driver_id: up.driver_id,
                            driver_name: up.driver?.full_name || '',
                            invoice_value: up.invoice_value,
                            days_worked: parseInt(up.days_worked ?? up.parcels_qty_count) || 0,
                            total_parcels: up.total_parcels === 'N/A' ? 'N/A' : parseInt(up
                                .total_parcels ?? up.parcels_qty_total) || 0,
                            found: !!up.driver
                        });
                    }
                });

                this.on("error", (file, err) => console.warn("Upload error:", file.name, err));

                this.on("queuecomplete", () => {
                    const elapsed = Date.now() - uploadStartTime;
                    const remaining = Math.max(0, MIN_LOADER_MS - elapsed);
                    setTimeout(() => {
                        loaderDiv.style.opacity = 0;
                        setTimeout(() => {
                            loaderDiv.style.display = "none";
                            fadeIn(resultsCard, 250);
                            const valid = getFoundCount();
                            const msg = completedTpl.replace(':count', valid);
                            alertPlaceholder.innerHTML = `
                              <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                                <strong>${msg}</strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                              </div>`;
                        }, 250);
                    }, remaining);
                });
            }
        });
    </script>
@endpush
