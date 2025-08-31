@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right"></div>
            <h4 class="page-title">
                {{ __('messages.calculation_for', ['driver' => $driver->full_name ?? $driver->name, 'week' => $week]) }}
            </h4>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">

        <!-- File Upload (hidden after upload) -->
        <form action="{{ route('calculate.upload') }}" method="post" class="dropzone" id="myAwesomeDropzone"
              data-plugin="dropzone" data-previews-container="#file-previews"
              data-upload-preview-template="#uploadPreviewTemplate" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="driver_id" value="{{ $driver->id }}">
            <input type="hidden" name="week" value="{{ $week }}">
            <div class="fallback">
                <input name="file" type="file" multiple />
            </div>

            <div class="dz-message needsclick">
                <i class="h1 text-muted dripicons-cloud-upload"></i>
                <h3>{{ __('messages.drop_file_here') }}</h3>
                <span class="text-muted font-13">{{ __('messages.pdf_file_requirements') }}</span>
            </div>
        </form>
        <div class="dropzone-previews mt-3" id="file-previews"></div>
        <div class="d-none" id="uploadPreviewTemplate">
            <div class="card mt-1 mb-0 shadow-none border">
                <div class="p-2">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <img data-dz-thumbnail src="#" class="avatar-sm rounded bg-light" alt="">
                        </div>
                        <div class="col ps-0">
                            <a href="javascript:void(0);" class="text-muted fw-bold" data-dz-name></a>
                            <p class="mb-0" data-dz-size></p>
                        </div>
                        <div class="col-auto">
                            <a href="" class="btn btn-link btn-lg text-muted" data-dz-remove>
                                <i class="dripicons-cross"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Spinner -->
        <div id="spinner" style="display:none;" class="text-center my-4">
            <div class="spinner-border text-info" role="status"></div>
        </div>

        <!-- Calculation Form -->
        <form id="calculation-form" class="mt-4" style="display:none;">
            <div class="mb-3">
                <label for="vehicule-rental-price" class="form-label">{{ __('messages.vehicle_rental_price') }}</label>
                <input type="number" step="0.01" min="0" class="form-control" id="vehicule-rental-price">
            </div>
            <div class="mb-3">
                <label for="percentage" class="form-label">{{ __('messages.percentage') }}</label>
                <input type="number" step="0.01" min="0" max="100" class="form-control" id="percentage" required placeholder="{{ __('messages.enter_broker_percentage') }}">
            </div>
            <div class="mb-3">
                <label for="bonus" class="form-label">{{ __('messages.add_bonus') }}</label>
                <input type="number" step="0.01" min="0" class="form-control" id="bonus" value="0">
            </div>
            <div class="mb-3">
                <label for="cash-advance" class="form-label">{{ __('messages.deduct_cash_advance') }}</label>
                <input type="number" step="0.01" min="0" class="form-control" id="cash-advance" value="0">
            </div>
            <button type="button" class="btn btn-info" id="start-btn">
                <i class="mdi mdi-transfer-right"></i> <span>{{ __('messages.start') }}</span>
            </button>
        </form>

        <!-- Result -->
        <div id="result-alert" style="display:none;">
            <div class="alert alert-primary alert-dismissible bg-primary text-white border-0 fade show mt-4" role="alert">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                <strong>
                    {{ __('messages.final_amount_save_prompt') }} <span id="final-amount"></span>, {{ __('messages.wannasavit') }}
                </strong>
            </div>
            <button type="button" class="btn btn-info" id="save-btn">
                <i class="mdi mdi-content-save"></i> <span>{{ __('messages.save') }}</span>
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/vendor/dropzone.min.js') }}"></script>
<script>
    let totalInvoice = 0;
    let parcelRowsCount = 0;
    let finalAmount = 0;

    Dropzone.options.myAwesomeDropzone = {
        paramName: "file",
        maxFiles: 1,
        acceptedFiles: ".pdf",
        init: function() {
            this.on("sending", function(file, xhr, formData) {
                document.getElementById('spinner').style.display = '';
                document.getElementById('myAwesomeDropzone').style.display = 'none';
                document.getElementById('calculation-form').style.display = 'none';
                document.getElementById('result-alert').style.display = 'none';

                formData.append('driver_id', '{{ $driver->id }}');
                formData.append('week', '{{ $week }}');
            });
        },
        success: function (file, response) {
            totalInvoice    = parseFloat(String(response.total_invoice ?? '0').replace(/,/g, '')) || 0;
            parcelRowsCount = parseInt(response.parcel_rows_count ?? 0);

            document.getElementById('spinner').style.display = 'none';

            if (totalInvoice > 0 && parcelRowsCount > 0) {
                document.getElementById('calculation-form').style.display = '';
            } else {
                alert('{{ __("messages.pdf_extraction_failed") }}');
                document.getElementById('myAwesomeDropzone').style.display = '';
            }
        },
        error: function () {
            document.getElementById('spinner').style.display = 'none';
            alert('{{ __("messages.file_upload_failed") }}');
            document.getElementById('myAwesomeDropzone').style.display = '';
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        const startBtn        = document.getElementById('start-btn');
        const calculationForm = document.getElementById('calculation-form');
        const resultAlert     = document.getElementById('result-alert');
        const saveBtn         = document.getElementById('save-btn');

        let vehiculeRental = 0, percentage = 0, bonus = 0, cashAdvance = 0;

        if (startBtn) {
            startBtn.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('spinner').style.display = '';
                calculationForm.style.display = 'none';

                setTimeout(function() {
                    vehiculeRental = parseFloat(document.getElementById('vehicule-rental-price').value) || 0;
                    percentage     = parseFloat(document.getElementById('percentage').value) || 0;
                    bonus          = parseFloat(document.getElementById('bonus').value) || 0;
                    cashAdvance    = parseFloat(document.getElementById('cash-advance').value) || 0;

                    if (percentage > 100) percentage = 100;
                    if (percentage < 0)   percentage = 0;

                    const driverPercentage = (100 - percentage) / 100;
                    const left  = totalInvoice * driverPercentage;
                    const right = vehiculeRental * parcelRowsCount;

                    finalAmount = left - right + bonus - cashAdvance;

                    document.getElementById('spinner').style.display = 'none';
                    document.getElementById('final-amount').innerText = '$' + finalAmount.toFixed(2);
                    resultAlert.style.display = '';
                    startBtn.style.display = 'none';
                }, 500);
            });
        }

        if (saveBtn) {
            saveBtn.addEventListener('click', async function(e) {
                e.preventDefault();
                document.getElementById('spinner').style.display = '';
                
                try {
                    const res = await fetch('{{ route('calculate.save') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            driver_id: {{ $driver->id }},
                            week: "{{ $week }}",
                            vehicule_rental_price: parseFloat(document.getElementById('vehicule-rental-price').value) || 0,
                            broker_percentage: parseFloat(document.getElementById('percentage').value) || 0,
                            bonus: parseFloat(document.getElementById('bonus').value) || 0,
                            cash_advance: parseFloat(document.getElementById('cash-advance').value) || 0
                        })
                    });

                    if (!res.ok) {
                        const err = await res.json().catch(() => ({}));
                        throw new Error(err.message || '{{ __("messages.save_failed") }}');
                    }

                    // Redirect to driver page instead of showing alert
                    window.location.href = "{{ route('drivers.show', $driver->id) }}";
                    
                } catch (err) {
                    document.getElementById('spinner').style.display = 'none';
                    alert(err.message || '{{ __("messages.error_saving_calculation") }}');
                }
            });
        }
    });
</script>
@endpush