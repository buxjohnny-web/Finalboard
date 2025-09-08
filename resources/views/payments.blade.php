@extends('layouts.master')

@push('styles')
    <link href="https://cdn.datatables.net/v/bs5/dt-2.0.8/r-3.0.2/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush

@section('content')
    <div id="main-content">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3 mb-3 page-title-box">
            <h4 class="page-title m-0">Payments</h4>
        </div>


        <div id="upload-status-alert-placeholder" class="mt-3"></div>


        <!-- Batch Invoice Upload Section -->
        <div class="card mb-4" id="upload-card">
            <div class="card-header">
                <h5 class="card-title">Batch Invoice Upload</h5>
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

        <div class="card">
            <div class="card-body">
                <table class="table table-centered table-striped dt-responsive nowrap w-100" id="products-table">
                    <thead class="table-dark">
                        <tr>
                            <th>Driver</th>
                            <th>Phone</th>
                            <th>Added By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($drivers as $driver)
                            <tr>
                                <td>{{ $driver->driver_id }} - {{ $driver->full_name }}</td>
                                <td>{{ $driver->phone_number }}</td>
                                <td>{{ $driver->added_by ? \App\Models\User::find($driver->added_by)->full_name : '' }}</td>
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
        Dropzone.autoDiscover = false;

        if (!Dropzone.instances.some(instance => instance.element.id === "batch-upload-dropzone")) {
            let totalFilesUploaded = 0; // Total files uploaded
            let driversFound = 0; // Drivers found in DB
            let driversNotFound = []; // List of IDs not found in DB

            const updateAlertPlaceholder = (totalFiles, foundDrivers, notFoundDrivers) => {
                const notFoundDriversCount = notFoundDrivers.length; // Calculate unfound drivers count
                const notFoundDriverIds = notFoundDrivers.join(", "); // Convert IDs to comma-separated string

                const alertMessage = `
                <div class="alert alert-primary alert-dismissible bg-primary text-white border-0 fade show" role="alert">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                    Uploading done: files uploaded: ${totalFiles} | ${foundDrivers} drivers found in the DB - ${notFoundDriversCount} not found<br>
                    <strong>IDs not found:</strong> ${notFoundDriverIds}
                </div>`;
                document.getElementById("upload-status-alert-placeholder").innerHTML = alertMessage;
            };

            new Dropzone("#batch-upload-dropzone", {
                url: "{{ route('payments.upload') }}",
                paramName: "files",
                maxFilesize: 5,
                acceptedFiles: ".pdf",
                addRemoveLinks: true,
                init: function() {
                    // Ensure upload-card is hidden permanently
                    const hideUploadCard = () => {
                        const uploadCard = document.getElementById("upload-card");
                        if (uploadCard) {
                            uploadCard.style.display = "none";
                        }
                    };

                    // Hide upload-card on start of processing
                    this.on("processing", function() {
                        hideUploadCard();
                    });

                    // Hide upload-card on success
                    this.on("success", function(file, response) {
                        console.log("Dropzone 'success' response:", response);

                        totalFilesUploaded++;

                        if (response && response.success) {
                            const driverId = response.uploaded
                            .driver_id; // Extract driver ID from response
                            const driverFound = response.uploaded.driver && response.uploaded.driver
                                .driver_id; // Check if driver exists

                            if (driverFound) {
                                driversFound++;
                            } else {
                                driversNotFound.push(driverId); // Add ID to not found list
                            }

                            // Update alert placeholder with counts and IDs
                            updateAlertPlaceholder(totalFilesUploaded, driversFound, driversNotFound);

                            // Ensure upload-card stays hidden
                            hideUploadCard();
                        } else {
                            console.error("Error in response:", response.message);
                        }
                    });

                    // Hide upload-card on error
                    this.on("error", function(file, response) {
                        console.error("Error uploading file:", response);
                        const errorMessage = `
                        <div class="alert alert-danger alert-dismissible bg-danger text-white border-0 fade show" role="alert">
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                            <strong>Error:</strong> Validation failed for ${file.name}.
                        </div>`;
                        document.getElementById("upload-status-alert-placeholder").innerHTML =
                            errorMessage;

                        // Ensure upload-card stays hidden
                        hideUploadCard();
                    });
                }
            });
        }
    </script>
@endpush
