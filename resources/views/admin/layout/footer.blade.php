<!--start back-to-top-->
<button class="btn btn-dark btn-icon" id="back-to-top">
    <i class="bi bi-caret-up fs-3xl"></i>
</button>
<!--end back-to-top-->

<!-- JAVASCRIPT -->
<script src="{{ asset('admin') }}/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('admin') }}/assets/js/jquery.min.js"></script>

<script src="{{ asset('admin') }}/assets/libs/simplebar/simplebar.min.js"></script>
<script src="{{ asset('admin') }}/assets/libs/datatable/jquery.dataTables.min.js"></script>
<script src="{{ asset('admin') }}/assets/libs/datatable/dataTables.buttons.min.js"></script>
<script src="{{ asset('admin') }}/assets/libs/datatable/excel.dataTables.min.css"></script>
<script src="{{ asset('admin') }}/assets/libs/datatable/buttons.html5.min.js"></script>
<script src="{{ asset('admin') }}/assets/libs/datatable/buttons.print.min.js"></script>
<script src="{{ asset('admin') }}/assets/js/pages/password-addon.init.js"></script>


@section('backend-footer')
@show
@if (session('status'))
    <script>
        {{--Swal.fire("{{ session('status') }}");--}}
        Swal.fire({
            text: "{{ session('status') }}",
            icon: "success",
            confirmButtonClass: "btn btn-gradient w-xs me-2 mt-2",
            confirmButtonText: "تم",
            buttonsStyling: !1
        })
    </script>
@endif
@if (session('error'))
    <script>
        {{--Swal.fire("{{ session('status') }}");--}}
        Swal.fire({
            text: "{{ session('error') }}",
            icon: "error",
            confirmButtonClass: "btn btn-gradient w-xs me-2 mt-2",
            confirmButtonText: "تم",
            buttonsStyling: !1
        })
    </script>
@endif
<!-- nouisliderribute js -->
<script src="{{ asset('admin') }}/assets/libs/nouislider/nouislider.min.js"></script>
<script src="{{ asset('admin') }}/assets/libs/wnumb/wNumb.min.js"></script>
<!-- range slider init -->
<script src="{{ asset('admin') }}/assets/js/pages/range-sliders.init.js"></script>

<script src="{{ asset('admin') }}/assets/js/plugins.js"></script>
<script src="{{ asset('admin') }}/assets/js/table.js"></script>
<!-- App js -->
<script src="{{ asset('admin') }}/assets/js/app.js"></script>
<script src="{{ asset('admin') }}/assets/js/custom.js"></script>
