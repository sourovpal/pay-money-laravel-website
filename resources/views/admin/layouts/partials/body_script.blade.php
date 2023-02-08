<script src="{{ asset('public/backend/bootstrap/dist/js/bootstrap-js/popper.min.js') }}" type="text/javascript"></script>
<!-- Bootstrap 5.0.2 -->
<script src="{{ asset('public/backend/bootstrap/dist/js/bootstrap-js/bootstrap.min.js') }}" type="text/javascript"></script>
<!-- Select2 -->
<script src="{{ asset('public/backend/select2/select2.full.min.js') }}" type="text/javascript"></script>
<!-- moment -->
<script src="{{ asset('public/backend/moment/moment.js') }}" type="text/javascript"></script>
<!-- AdminLTE App -->
<script src="{{ asset('public/dist/js/app.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    "use strict";
    var url = "{{ url('change-lang') }}";
    var token = "{{ csrf_token() }}";
 </script>
<script src="{{ asset('public/admin_dashboard/js/common/body_script.min.js') }}"></script>
@yield('body_script')
