<footer class="main-footer">
    <strong>Copyright &copy; {{now()->year }} - {{now()->year + 1}} <a href="{{url('admin/dashboard')}}">QuickFluence</a>.</strong>
    All rights reserved.
</footer>

<script src="{{ asset('public/plugins/jquery/jquery.min.js') }}"></script>

<script src="{{ asset('public/plugins/jquery-ui/jquery-ui.min.js') }}"></script>

<script src="{{ asset('public/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<script src="{{ asset('public/plugins/chart.js/Chart.min.js') }}"></script>

<script src="{{ asset('public/plugins/sparklines/sparkline.js') }}"></script>

<script src="{{ asset('public/plugins/jqvmap/jquery.vmap.min.js') }}"></script>

<script src="{{ asset('public/plugins/jqvmap/maps/jquery.vmap.world.js') }}"></script>

<script src="{{ asset('public/plugins/jquery-knob/jquery.knob.min.js') }}"></script>

<script src="{{ asset('public/plugins/moment/moment.min.js') }}"></script>

<script src="{{ asset('public/plugins/daterangepicker/daterangepicker.js') }}"></script>

<script src="{{ asset('public/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>

<script src="{{ asset('public/plugins/summernote/summernote-bs4.min.js') }}"></script>

<script src="{{ asset('public/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>

<script src="{{ asset('public/dist/js/adminlte.js') }}"></script>

<script src="{{ asset('public/plugins/toastr/toastr.min.js') }}"></script>

<script src="{{ asset('public/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>

<script src="{{ asset('public/plugins/datatables-bootstrap-4/js/jquery.dataTables.min.js') }}"></script>

<script src="{{ asset('public/plugins/datatables-bootstrap-4/js/dataTables.checkboxes.min.js') }}"></script>

<script src="{{ asset('public/plugins/datatables-bootstrap-4/js/dataTables.responsive.min.js') }}"></script>

<script src="{{ asset('public/plugins/datatables-bootstrap-4/js/dataTables.bootstrap4.min.js') }}"></script>

<script src="{{ asset('public/plugins/jquery-validation/jquery.validate.min.js') }}"></script>

<script src="{{ asset('public/plugins/jquery-validation/additional-methods.min.js') }}"></script>
{{-- Coustom JS --}}

<script>
    const routeUrl = '{{ url('') }}';
</script>
@stack('page_scripts')
<script src="{{ asset('public/admin/custom/custom.js') }}"></script>

