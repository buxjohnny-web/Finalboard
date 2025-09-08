@extends('layouts.master')

@push('styles')
    <link href="https://cdn.datatables.net/v/bs5/dt-2.0.8/r-3.0.2/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Table styling */
        .products-table-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 3px 16px rgba(30, 34, 90, 0.07), 0 1.5px 4px rgba(30, 34, 90, 0.05);
            padding: 0;
        }

        .text-success {
            color: #1cc88a !important;
        }

        .text-danger {
            color: #e74a3b !important;
        }

        /* On mobile hide status column */
        @media (max-width: 575.98px) {

            #drivers-table th:nth-child(1),
            #drivers-table td:nth-child(1) {
                display: none;
            }
        }
    </style>
@endpush

@section('content')
    <div id="main-content">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3 mb-3 page-title-box">
            <h4 class="page-title m-0">{{ __('messages.payments_page_title') }}</h4>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card mb-4 pdf-dropzone-card" id="upload-card">
            <div class="card-header">
                <h5 class="card-title">{{ __('messages.batch_upload') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('payments.upload') }}" method="post" class="dropzone" id="batch-upload-dropzone"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="dz-message needsclick">
                        <i class="h1 text-muted dripicons-cloud-upload"></i>
                        <h3>{{ __('messages.drop_files_here') }}</h3>
                        <span class="text-muted font-13">{{ __('messages.pdf_file_requirements') }}</span>
                    </div>
                </form>
            </div>
        </div>

        <!-- Upload status placeholder -->
        <div id="upload-status" class="d-none mt-3"></div>

        <div class="card products-table-card mt-4">
            <div class="card-body">
                <table class="table table-centered table-striped dt-responsive nowrap w-100" id="drivers-table">
                    <thead class="table-dark">
                        <tr>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.driver') }}</th>
                            <th>{{ __('messages.total') }}</th>
                            <th>{{ __('messages.days_worked') }}</th>
                            <th class="text-center">#</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($drivers as $driver)
                            @php
                                $firstName = $driver->first_name ?? explode(' ', $driver->full_name)[0];
                            @endphp
                            <tr>
                                <td>
                                    @if ($driver->active == 1)
                                        <i class="mdi mdi-circle text-success"></i>
                                    @else
                                        <i class="mdi mdi-circle text-danger"></i>
                                    @endif
                                </td>
                                <td>{{ $driver->driver_id }} - {{ $firstName }}</td>
                                <td>0</td>
                                <td>0</td>
                                <td class="text-center">
                                    <a href="{{ route('drivers.show', $driver->id) }}" class="action-icon"
                                        aria-label="{{ __('messages.view') }}" title="{{ __('messages.view') }}">
                                        <i class="mdi mdi-arrow-right-bold-box text-info"></i>
                                    </a>
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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/dt-2.0.8/r-3.0.2/datatables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js" crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>

    <script>
        $(document).ready(function() {
            var dt = $('#drivers-table').DataTable({
                responsive: true,
                autoWidth: false,
                paging: false, // no pagination
                searching: true,
                ordering: true,
                info: false,
                order: [
                    [1, 'asc']
                ],
                language: {
                    url: "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
                }
            });
        });

        Dropzone.autoDiscover = false;
        var myDropzone = new Dropzone("#batch-upload-dropzone", {
            url: "{{ route('payments.upload') }}",
            paramName: "files",
            maxFilesize: 5,
            acceptedFiles: ".pdf",
            addRemoveLinks: true,
            parallelUploads: 9999,
            uploadMultiple: true,
            autoProcessQueue: true,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dictDefaultMessage: "<h3>{{ __('messages.drop_files_here') }}</h3><br><span>{{ __('messages.click_to_upload') }}</span>",
            init: function() {
                this.on("processingmultiple", function(files) {
                    $('#upload-status')
                        .removeClass('d-none')
                        .html('<div class="alert alert-info">⏳ Uploading files...</div>');
                });

                this.on("successmultiple", function(files, response) {
                    $('#upload-card').hide(); // hide upload card

                    var tbody = $('#drivers-table tbody');
                    tbody.empty();

                    let driverCount = 0;

                    // Success
                    if (response.uploaded && response.uploaded.length) {
                        response.uploaded.forEach(function(file) {
                            const driverId = file.driver_id ?? '';
                            const driverName = file.full_name ?? '';
                            const driverLink = file.driver_id_db ?
                                `{{ url('drivers') }}/${file.driver_id_db}` : '#';
                            const statusIcon = `<i class="mdi mdi-circle text-success"></i>`;
                            const row = `
                                <tr>
                                    <td>${statusIcon}</td>
                                    <td>${driverId} - ${driverName}</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td class="text-center">
                                        <a href="${driverLink}" class="action-icon" aria-label="{{ __('messages.view') }}" title="{{ __('messages.view') }}">
                                            <i class="mdi mdi-arrow-right-bold-box text-info"></i>
                                        </a>
                                    </td>
                                </tr>`;
                            tbody.append(row);
                            driverCount++;
                        });
                    }

                    // Failed
                    if (response.failed && response.failed.length) {
                        response.failed.forEach(function(file) {
                            const statusIcon = `<i class="mdi mdi-circle text-danger"></i>`;
                            const row = `
                                <tr class="table-danger">
                                    <td>${statusIcon}</td>
                                    <td>${file.name} - Not Found</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td></td>
                                </tr>`;
                            tbody.append(row);
                        });
                    }

                    // Replace upload status with card
                    $('#upload-status').html(`
                        <div class="card bg-info">
                            <div class="card-body profile-user-box">
                                <div class="row">
                                    <div class="col-sm-8">
                                        <h4 class="mt-1 mb-1 text-white">${driverCount} drivers found</h4>
                                        <p class="font-13 text-white-50">Select a week and calculate ?</p>
                                        <select class="form-select w-auto" id="example-select">
                                            <option>1</option>
                                            <option>2</option>
                                            <option>3</option>
                                            <option>4</option>
                                            <option>5</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="text-center mt-sm-0 mt-3 text-sm-end">
                                            <button type="button" class="btn btn-light">
                                                <i class="mdi mdi-account-edit me-1"></i> Edit Profile
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                });

                this.on("errormultiple", function(files, response) {
                    $('#upload-status')
                        .removeClass('d-none')
                        .html(
                            '<div class="alert alert-danger">❌ Upload failed. Please try again.</div>');
                });
            }
        });
    </script>
@endpush
