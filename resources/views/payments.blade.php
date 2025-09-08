@extends('layouts.master')

@push('styles')
    <link href="https://cdn.datatables.net/v/bs5/dt-2.0.8/r-3.0.2/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Full-screen overlay for progress only (no extra results table) */
        #upload-container {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            top: 0;
            background: rgba(255, 255, 255, 0.97);
            z-index: 99999;
            overflow-y: auto;
            padding-top: 40px;
            padding-bottom: 40px;
            display: none;
            /* JS toggles .show */
            align-items: flex-start;
            justify-content: center;
            pointer-events: auto;
        }

        #upload-container.show {
            display: flex !important;
        }

        #upload-container .upload-card {
            width: 100%;
            max-width: 98vw;
            background: transparent;
            border: none;
            box-shadow: none;
            padding: 0;
        }

        .upload-table-responsive {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 3px 16px rgba(30, 34, 90, 0.07), 0 1.5px 4px rgba(30, 34, 90, 0.05);
            padding: 0;
            margin: 0 auto;
            max-width: 97%;
        }

        body.dz-uploading {
            overflow: hidden;
        }

        @media (max-width: 991.98px) {
            #upload-container {
                padding-left: 0;
                padding-right: 0;
            }

            .upload-table-responsive {
                border-radius: 0;
                max-width: 100%;
            }
        }

        @media (max-width: 575.98px) {
            #upload-container {
                padding-top: 10px;
                padding-bottom: 10px;
            }

            .upload-table-responsive {
                max-width: 100vw;
                border-radius: 0;
            }
        }

        /* MOBILE: on small screens, show only 2nd and 5th columns (1-based) */
        @media (max-width: 575.98px) {

            #products-table thead th:not(:nth-child(2)):not(:nth-child(5)),
            #products-table tbody td:not(:nth-child(2)):not(:nth-child(5)) {
                display: none !important;
            }

            #products-table thead th.dtr-control,
            #products-table tbody td.dtr-control {
                display: none !important;
            }
        }

        #upload-status {
            margin-bottom: 1rem;
        }

        thead.table-dark th {
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <div id="main-content">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3 mb-3 page-title-box">
            <h4 class="page-title m-0">Payments</h4>
        </div>

        <div id="upload-status-alert-placeholder"></div>

        <div class="mb-3 search-bar">
            <div class="input-group">
                <input type="text" id="table-search-input" class="form-control" placeholder="Search by name, phone or ID"
                    aria-label="Search">
                <button class="btn btn-info" id="table-search-button">Search</button>
            </div>
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
                <h5 class="card-title">Batch invoice Upload</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('payments.upload') }}" method="post" class="dropzone" id="batch-upload-dropzone"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="dz-message needsclick">
                        <i class="h1 text-muted dripicons-cloud-upload"></i>
                        <h3>Drop files here</h3>
                        <span class="text-muted font-13">PDFs only. Max 5MB each.</span>
                    </div>
                </form>
            </div>
        </div>

        <div class="card products-table-card">
            <div class="card-body">
                <table class="table table-centered table-striped dt-responsive nowrap w-100" id="products-table">
                    <thead class="table-dark">
                        <tr>
                            <th id="col-expand"></th>
                            <th id="col-status" class="text-center">Status</th>
                            <th>Driver</th>
                            <th>Phone</th>
                            <th>Added By</th>
                            <th class="text-center">#</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($drivers as $driver)
                            @php
                                $firstName = $driver->first_name ?? explode(' ', $driver->full_name)[0];
                                $addedByUser = \App\Models\User::find($driver->added_by);
                            @endphp
                            <tr>
                                <td class="col-expand"></td>
                                <td class="text-center col-status">
                                    @if ($driver->active == 1)
                                        <i class="mdi mdi-circle text-success" aria-label="status"></i>
                                    @else
                                        <i class="mdi mdi-circle text-danger" aria-label="status"></i>
                                    @endif
                                </td>
                                <td>{{ $driver->driver_id }} - {{ $firstName }}</td>
                                <td>{{ $driver->phone_number }}</td>
                                <td>{{ $addedByUser ? $addedByUser->full_name : '' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('drivers.show', $driver->id) }}" class="action-icon" aria-label="View"
                                        title="View">
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

    <!-- Progress overlay (no extra results table) -->
    {{-- <div id="upload-container" aria-hidden="true" role="dialog" aria-modal="true">
        <div class="upload-card">
            <div class="upload-table-responsive">
                <div class="container p-3">
                    <div id="upload-status" class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong id="upload-status-title">Uploading files...</strong>
                                <div id="upload-status-message" class="small text-muted">Please wait</div>
                            </div>
                            <div>
                                <button id="upload-cancel-button" class="btn btn-light btn-sm"
                                    type="button">Cancel</button>
                                <button id="upload-close-overlay" class="btn btn-primary btn-sm d-none"
                                    type="button">Close</button>
                            </div>
                        </div>
                        <div class="progress mt-2" style="height:8px;">
                            <div id="upload-progress-bar" class="progress-bar" role="progressbar" style="width:0%">0%</div>
                        </div>
                    </div>

                    <div class="small text-muted">
                        Results will replace the main table when upload completes.
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
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
            maxFilesize: 5, // Limit each file size to 5 MB
            acceptedFiles: ".pdf",
            addRemoveLinks: true,
            parallelUploads: 999, // Increase parallel uploads
            uploadMultiple: false, // Upload files individually
            maxFiles: 999, // Allow up to 999 files in a single upload
            autoProcessQueue: true,
            timeout: 0, // Disable timeout
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Include CSRF token
            },
            dictDefaultMessage: "<h3>Drop files here</h3><br><span>Click to upload</span>",
            init: function() {
                this.on("processing", function(file) {
                    // Hide the upload card immediately when the upload starts
                    $('#upload-card').hide();
                });

                this.on("success", function(file, response) {
                    console.log("File uploaded successfully: ", file.name);
                });

                this.on("queuecomplete", function() {
                    console.log("All files have been uploaded.");

                    // Show alert summarizing the upload results
                    const totalUploaded = myDropzone.getAcceptedFiles().length; // Count accepted files
                    $('#upload-status-alert-placeholder').html(`
                <div class="alert alert-primary alert-dismissible bg-primary text-white border-0 fade show" role="alert">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                    <strong>Uploading done : </strong> files uploaded : ${totalUploaded}
                </div>
            `);
                });

                this.on("error", function(file, response) {
                    console.error("Error uploading file: ", file.name, response);
                    alert("❌ Upload failed. Please try again.");
                });
            }
        });
    </script>
@endpush
