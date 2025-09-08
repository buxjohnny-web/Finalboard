<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                2024 -
                <script>
                    document.write(new Date().getFullYear())
                </script> © Intelboard.ca
            </div>
        </div>
    </div>
</footer>

</div> <!-- End content-page -->
</div> <!-- End wrapper -->

{{-- Place all vendor/app scripts here if needed for all pages --}}
<script src="{{ asset('assets/js/vendor.min.js') }}"></script>
<script src="{{ asset('assets/js/app.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>

{{-- Stack for page-specific scripts --}}
@stack('scripts')
</body>

</html>
