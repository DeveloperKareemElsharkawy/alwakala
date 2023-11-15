@extends('admin.layout.master')
@section('pageTitle', 'المدن')
@section('backend-head')
    <!-- datatable css -->
    <link href="{{ asset('admin') }}/assets/libs/datatable/datatables.min.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('admin') }}/assets/libs/datatable/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('admin') }}/assets/libs/datatable/buttons.dataTables.min.css" rel="stylesheet" type="text/css">
    <!-- Layout config Js -->
    <script src="{{ asset('admin') }}/assets/js/layout.js"></script>
    <!-- Sweet Alert css-->
    <link href="{{ asset('admin') }}/assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
    <!-- select2 css -->
    <link href="{{ asset('admin') }}/assets/libs/select2/select2.min.css" rel="stylesheet" type="text/css">
    <style>
        .error{
            color:red
        }
    </style>
@endsection
@section('backend-main')
    @if(request()->type != 'archived')
        <div class="modal fade" id="exampleModalgrid" data-bs-backdrop="static" tabindex="-1"
             aria-labelledby="exampleModalgridLabel" aria-modal="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ri-close-line"></i>
                    </button>
                    <div class="modal-header d-block text-center">
                        <h5 class="modal-title" id="exampleModalgridLab">اضافة ولاية جديد</h5>
                    </div>
                    <div class="modal-body px-5">
                        <form class="my_form px-5" method="post" action="{{ url('admin_panel/settings/cities') }}">
                            {{ csrf_field() }}
                            <div class="row g-3">
                                <div class="col-md-12 col-12">
                                    <div>
                                        <label for="name_ar" class="form-label">الاسم بالعربية</label>
                                        <input type="text" name="name_ar" class="form-control" id="name_ar" placeholder="">
                                        @error('name_ar')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div><!--end col-->

                                <div class="col-md-12 col-12">
                                    <div>
                                        <label for="name_en" class="form-label">الاسم بالانجليزية</label>
                                        <input type="text" name="name_en" class="form-control" id="name_en" placeholder="">
                                        @error('name_en')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div><!--end col-->
                                <div class="col-md-12 col-12">
                                    <div class="select-div">
                                        <label for="country_id" class="form-label">الدولة </label>
                                        <select class="form-select select-modal" name="country_id"
                                                id="country_id">
                                            <option
                                                value="" selected disabled hidden="">اختر الدولة </option>
                                            @foreach($countries as $country_key => $country)
                                                <option
                                                    value="{{ $country['id'] }}">{{ $country['name_'.$lang] }}</option>
                                            @endforeach
                                        </select>
                                        @error('country_id')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div><!--end col-->

                                <div class="col-md-12 col-12">
                                    <div class="select-div">
                                        <label for="region_id" class="form-label">المحافظة </label>
                                        <select class="form-select select-modal" name="region_id"
                                                id="region_id">
                                            <option
                                                value="" selected disabled hidden="">اختر الدولة اولا</option>
                                        </select>
                                        @error('region_id')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div><!--end col-->

                                <div class="col-md-12 col-12">
                                    <div class="select-div">
                                        <label for="state_id" class="form-label">الولاية </label>
                                        <select class="form-select select-modal" name="state_id"
                                                id="state_id">
                                            <option
                                                value="" selected disabled hidden="">اختر المحافظة اولا</option>
                                        </select>
                                        @error('state_id')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div><!--end col-->
                                <div class="col-lg-12">
                                    <div class="hstack gap-2 mt-5 justify-content-center">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">اغلاق</button>
                                        <button type="submit" class="btn btn-gradient">حفظ</button>
                                    </div>
                                </div><!--end col-->
                            </div><!--end row-->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <!-- Grids in modals -->
    <div class="modal fade" id="exampleModalgridedit" data-bs-backdrop="static" tabindex="-1"
         aria-labelledby="exampleModalgridLabeledit" aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content" id="exampleModalgrideditform">

            </div>
        </div>
    </div>

    <div class="page-content show_vendor sidebar_settings">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="row">
                                <div class="col-md-3">
                                    @include('admin.settings.sidebar')
                                </div>
                                <div class="col-md-9">
                                    <ul class="nav nav-tabs nav-justified mb-1 settings_tabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ url('admin_panel/settings/countries') }}">
                                                الدولة
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ url('admin_panel/settings/regions') }}">
                                                المحافظة
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link " href="{{ url('admin_panel/settings/states') }}">
                                                الولاية
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link active" href="{{ url('admin_panel/settings/cities') }}">
                                                المدينة
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="setting-body">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="card user-info">
                                                    <div class="card-body pt-0">
                                                        @if(request()->type != 'archived')
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="add-btns mb-0 justify-content-end mb-2">
                                                                        <a href="#" data-bs-toggle="modal"
                                                                           data-bs-target="#exampleModalgrid"
                                                                           class="btn btn-warning role-add">
                                                                            <i class=" bx bx-plus"></i>
                                                                            اضافة مدينة جديدة
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        <div class="table-responsive">
                                                            <table id="user_example" class="dataTable row-border"
                                                                   style="width:100%">
                                                                <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>المدينة</th>
                                                                    <th>الولاية</th>
                                                                    <th>المحافظة</th>
                                                                    <th>الدولة</th>
                                                                    <th> فعال</th>
                                                                    <th>الاجراءات</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @foreach($cities as $key => $color)
                                                                    <tr class="image_class{{ $color['id'] }}">
                                                                        <td>{{ $key + 1 }}</td>
                                                                        <td>{{ $color['name_'.$lang] }}</td>
                                                                        <td>{{ $color['state']['name_'.$lang] }}</td>
                                                                        <td>{{ $color['state']['region']['name_'.$lang] }}</td>
                                                                        <td>{{ $color['state']['region']['country']['name_'.$lang] }}</td>
                                                                        <td class="materials">
                                                                            <div class="form-check form-switch form-switch-success d-inline-block">
                                                                                <input class="form-check-input active" model_type="City" model_id="{{ $color['id'] }}" type="checkbox" role="switch" id="SwitchCheck3" {{ $color['activation'] == true ? 'checked' : '' }}>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <ul class="actions-list">
                                                                                <li>
                                                                                    <a title="quick edit"
                                                                                       data-bs-placement="top"
                                                                                       class="edit-btn" model_id="{{ $color['id'] }}" href="#">
                                                                                        <i class="bi bi-pencil"></i>
                                                                                    </a>
                                                                                </li>
                                                                                <li>
                                                                                    @if(request()->type == 'archived')
                                                                                        <a title="delete" delete_url="settings/cities/"
                                                                                           object_id="{{ $color['id'] }}" data-bs-toggle="tooltip"
                                                                                           data-bs-placement="top" button_type="delete"
                                                                                           class="archive-btn sa-warning" href="#">
                                                                                            <i class="ph-trash"></i>
                                                                                        </a>
                                                                                        <a title="restore" delete_url="settings/cities/archive/"
                                                                                           object_id="{{ $color['id'] }}" data-bs-toggle="tooltip"
                                                                                           data-bs-placement="top" button_type="restore"
                                                                                           class="archive-btn sa-warning" href="#">
                                                                                            <i class="ri-restart-line"></i>
                                                                                        </a>
                                                                                    @else
                                                                                        <a title="archive" delete_url="settings/cities/archive/"
                                                                                           object_id="{{ $color['id'] }}" data-bs-toggle="tooltip"
                                                                                           data-bs-placement="top" button_type="archive"
                                                                                           class="archive-btn sa-warning" href="#">
                                                                                            <i class="ph-archive-box"></i>
                                                                                        </a>
                                                                                    @endif
                                                                                </li>
                                                                            </ul>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                    </div><!-- end card-body -->
                                                </div>
                                            </div><!--end col-->
                                        </div>
                                    </div>
                                </div><!--end col-->

                            </div>
                        </div><!-- end card-body -->
                    </div><!-- end card -->
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->

        </div>
        <!-- container-fluid -->
    </div>
    <!-- End Page-content -->

    <footer class="footer d-none">
    </footer>

    <div class="offcanvas offcanvas-end border-0 theme-settings-offcanvas" tabindex="-1" id="theme-settings-offcanvas">
        <div class="d-flex align-items-center p-3 offcanvas-header">
            <button type="button" class="btn-close btn-close-grey ms-auto" id="customizerclose-btn"
                    data-bs-dismiss="offcanvas" aria-label="Close">

            </button>
        </div>
        <div class="offcanvas-body p-0">
            <div data-simplebar id="collapse_body" class="h-100">

            </div>


        </div>
    </div>
@endsection
@section('backend-footer')
    <!-- select2 js -->
    <script src="{{ asset('admin') }}/assets/libs/select2/select2.min.js"></script>
    <script src="{{ asset('admin') }}/assets/js/additional-methods.min.js"></script>
    <script src="{{ asset('admin') }}/assets/js/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".my_form").validate({
                rules: {
                    name_ar: "required",
                    name_en: "required",
                    country_id: "required",
                    region_id: "required",
                    state_id: "required",
                },
                messages: {
                    name_ar: "اسم الدولة بالعربية مطلوب",
                    name_en: "اسم الدولة بالانجليزية مطلوب",
                    country_id: "الدولة مطلوبة",
                    region_id: "المحافظة مطلوبة",
                    state_id: "الولاية مطلوبة",
                }
            });
        });
    </script>
    <!-- Sweet Alerts js -->
    <script src="{{ asset('admin') }}/assets/libs/sweetalert2/sweetalert2.min.js"></script>
    <script>
        var link = '<?php echo url('/'); ?>';
        $("body").on("click", ".edit-btn", function (e) {
            var model_id = $(this).attr('model_id');
            $('#exampleModalgrideditform').html('<div class="col-md-12 col-xl-12 text-center loading"><i class="mdi mdi-loading fa-spin"></i></div>');

            $('#exampleModalgridedit').modal('show');
            $.ajax({
                type: "GET",
                url: link + "/admin_panel/settings/cities/" + model_id,
                success: function (data) {
                    $('#exampleModalgrideditform').html(data);
                }
            });
        });
    </script>

    <script>
        $(".active").change(function (e) {
            var modell_id = $(this).attr('model_id');
            var model_type = $(this).attr('model_type');
            $.ajax({
                type: "GET",
                url: link + "/admin_panel/settings/cities/" + modell_id +"/edit?model_type=" + model_type,
                success: function (data) {
                    Swal.fire({
                        title: data.title,
                        text: data.msg,
                        icon: '',
                        confirmButtonClass: "btn btn-gradient w-xs me-2 mt-2",
                        confirmButtonText: "اغلاق",
                        buttonsStyling: !1
                    });
                }
            });
        });
    </script>
    <script src="{{ asset('admin') }}/assets/js/sweetalert_ar.js"></script>
    <script>
        $('#country_id').on('change', function() {
            var country_id = $(this).val();
            $.get(link + '/ajax_regions?country_id=' + country_id, function (data) {
                $('#region_id').empty();
                $.each(data, function (index, subcatObj) {
                    $('#region_id').append('<option value="' + subcatObj.id + '">' + subcatObj.name + '</option>');
                });
            });
        });
    </script>
    <script>
        $('#region_id').on('change', function() {
            var region_id = $(this).val();
            $.get(link + '/state_ajax?region_id=' + region_id, function (data) {
                $('#state_id').empty();
                $.each(data, function (index, subcatObj) {
                    $('#state_id').append('<option value="' + subcatObj.id + '">' + subcatObj.name + '</option>');
                });
            });
        });
    </script>
@endsection
