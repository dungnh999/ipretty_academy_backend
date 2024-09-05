<!-- BEGIN: Vendor JS-->
<script src="{{ asset(mix('assets/vendor/libs/jquery/jquery.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/popper/popper.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/js/bootstrap.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/js/menu.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/dataTable/dataTables.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/dataTable/dataTables.bootstrap5.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/dataTable/buttons/js/dataTables.buttons.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/dataTable/buttons/js/buttons.bootstrap5.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/dataTable/responsive/js/dataTables.responsive.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/select2/js/select2.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/axios/axios.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/nouislider/js/nouislider.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/sweetalert2/js/sweetalert2.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/bs-stepper/js/bs-stepper.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/quill/js/quill.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/sortablejs/Sortable.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/select2/js/select2.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/jkanban/js/jkanban.js'))}}"></script>
<script src="{{ asset(mix('assets/vendor/libs/plyr/js/plyr.js'))}}"></script>

<script src="{{ asset('assets/template/axios.js') }}"></script>
<script src="{{ asset('assets/template/datatable.js') }}"></script>
<script src="{{ asset('assets/template/swal.js') }}"></script>
<script src="{{ asset('assets/template/validate.js') }}"></script>
<script src="{{ asset('assets/template/editor.js') }}"></script>
<script src="{{ asset('assets/template/local_storage.js') }}"></script>
<script src="{{ asset('assets/template/upload.js') }}"></script>


@yield('vendor-script')
<!-- END: Page Vendor JS-->
<!-- BEGIN: Theme JS-->
<script src="{{ asset(mix('assets/js/main.js')) }}"></script>

<!-- END: Theme JS-->
<!-- Pricing Modal JS-->
@stack('pricing-script')
<!-- END: Pricing Modal JS-->
<!-- BEGIN: Page JS-->
@yield('page-script')
<!-- END: Page JS-->
