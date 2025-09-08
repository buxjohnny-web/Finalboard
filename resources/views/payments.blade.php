@extends('layouts.master')

@push('styles')
    <link href="https://cdn.datatables.net/v/bs5/dt-2.0.8/r-3.0.2/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        #upload-container {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            background: #fff;
            z-index: 2000;
            box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.1);
            border-top: 1px solid #eee;
        }

        #upload-container .progress {
            max-width: 500px;
            margin: 0 auto;
        }

        #upload-message {
            font-size: 1.1em;
        }

        .upload-error-message {
            color: #b02a37;
        }

        .error-details {
            font-size: 0.95em;
            color: #a94442;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 6px;
            margin: 15px auto 0 auto;
            padding: 10px 14px;
            max-width: 600px;
            word-break: break-all;
            text-align: left;
        }

        .error-details ul {
            margin: 0 0 0 20px;
            padding: 0;
            list-style-type: disc;
        }
    </style>
@endpush

@section('content')
    <div id="main-content">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3 mb-3 page-title-box">
            <h4 class="page-title m-0">{{ __('messages.payments_page_title') }}</h4>
        </div>

        <div class="mb-3 search-bar">
            <div class="input-group">
                <input type="text" id="table-search-input" class="form-control"
                    placeholder="{{ __('messages.search_placeholder') }}" aria-label="{{ __('messages.search') }}">
                <button class="btn btn-info" id="table-search-button">
                    {{ __('messages.search') }}
                </button>
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

        <div class="card mb-4 pdf-dropzone-card">
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

        <div class="card products-table-card">
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
                                        <i class="mdi mdi-circle text-success"
                                            aria-label="{{ __('messages.status') }}"></i>
                                    @else
                                        <i class="mdi mdi-circle text-danger" aria-label="{{ __('messages.status') }}"></i>
                                    @endif
                                </td>
                                <td>
                                    {{ $driver->driver_id }} - {{ $firstName }}
                                </td>
                                <td>{{ $driver->phone_number }}</td>
                                <td>{{ $addedByUser ? $addedByUser->full_name : '' }}</td>
                                <td class="text-center"> <a href="{{ route('drivers.show', $driver->id) }}"
                                        class="action-icon" aria-label="{{ __('messages.view') }}"
                                        title="{{ __('messages.view') }}">
                                        <i class="mdi mdi-arrow-right-bold-box text-info"></i>
                                    </a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="upload-container" class="d-none text-center p-5">
        <h3 class="mb-3">{{ __('messages.uploading_files') }}</h3>
        <div class="progress mb-3" style="height: 25px;">
            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0"
                aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
        </div>
        <div id="upload-message" class="mt-4 fw-bold"></div>
        <div id="upload-error-details" class="error-details d-none"></div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/dt-2.0.8/r-3.0.2/datatables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js" crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>

    <script>
        $(document).ready(function() {
            var dt = $('#products-table').DataTable({
                responsive: true,
                autoWidth: false,
                dom: "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12'p>>",
                lengthChange: false,
                order: [
                    [2, 'asc']
                ],
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
                    },
                    {
                        responsivePriority: 2,
                        targets: 2
                    },
                    {
                        responsivePriority: 3,
                        targets: -1
                    }
                ]
            });

            $('#table-search-input').on('input', function() {
                dt.search(this.value).draw();
            });
            $('#table-search-button').on('click', function() {
                dt.search($('#table-search-input').val()).draw();
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
                    $('.products-table-card').hide();
                    $('.search-bar').hide();
                    $('.pdf-dropzone-card').hide();
                    $('.page-title-box').hide();
                    $('#upload-container').removeClass('d-none').show();
                    $('#upload-container .progress').show();
                    $('#upload-container .progress-bar').css('width', '0%').attr('aria-valuenow', 0);
                    $('#upload-message').html('');
                    $('#upload-error-details').addClass('d-none').empty();
                });

                this.on("successmultiple", function(files, response) {
                    $('#upload-container .progress').hide();
                    var message = `Done: ${response.uploaded.length} files uploaded.`;
                    if (response.uploaded && response.uploaded.length) {
                        message += "<ul>";
                        response.uploaded.forEach(function(f) {
                            // Display Driver ID
                            message += `<li>${f.driver_id}</li>`;
                        });
                        message += "</ul>";
                    }
                    if (response.failed && response.failed.length) {
                        message += `<br>Failed: ${response.failed.length} files.`;
                        message += "<ul>";
                        response.failed.forEach(function(f) {
                            message += `<li>${f.name}: ${f.error}</li>`;
                        });
                        message += "</ul>";
                    }
                    $('#upload-message').removeClass('upload-error-message').html(message);
                    $('#upload-error-details').addClass('d-none').empty();
                });

                this.on("totaluploadprogress", function(progress) {
                    $('#upload-container .progress-bar').css('width', progress + '%').attr(
                        'aria-valuenow', progress);
                });

                this.on("errormultiple", function(files, response, xhr) {
                    $('#upload-container .progress').hide();
                    let errorText =
                        '{{ __('messages.uploading_files') }}<br>{{ __('An error occurred during upload. Please try again.') }}';
                    let details = '';
                    if (xhr && xhr.responseText) {
                        try {
                            let json = JSON.parse(xhr.responseText);
                            if (json && json.errors) {
                                details = '<ul>';
                                for (let field in json.errors) {
                                    if (Array.isArray(json.errors[field])) {
                                        json.errors[field].forEach(function(msg) {
                                            details += `<li>${msg}</li>`;
                                        });
                                    } else {
                                        details += `<li>${json.errors[field]}</li>`;
                                    }
                                }
                                details += '</ul>';
                            } else if (json && json.message) {
                                details = `<div>${json.message}</div>`;
                            } else if (xhr.responseText.length < 2000) {
                                details = `<div>${xhr.responseText}</div>`;
                            }
                        } catch (e) {
                            if (xhr.responseText.length < 2000) {
                                details = `<div>${xhr.responseText}</div>`;
                            }
                        }
                    }
                    if (!details && response && typeof response === 'string') {
                        details = `<div>${response}</div>`;
                    }
                    $('#upload-message').addClass('upload-error-message').html(errorText);
                    if (details) {
                        $('#upload-error-details').removeClass('d-none').html(details);
                    } else {
                        $('#upload-error-details').addClass('d-none').empty();
                    }
                    $('.products-table-card').hide();
                    $('.search-bar').hide();
                    $('.pdf-dropzone-card').hide();
                    $('.page-title-box').hide();
                });
            }
        });
    </script>
@endpush
