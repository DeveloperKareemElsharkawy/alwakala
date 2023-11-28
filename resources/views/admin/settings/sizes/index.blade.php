@extends('admin.layout.master')
@section('pageTitle', 'المقاسات')
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
                    <h5 class="modal-title" id="exampleModalgridLab">اضافة مقاس جديد</h5>
                </div>
                <div class="modal-body px-5">
                    <form class="my_form px-5" method="post" action="{{ url('admin_panel/settings/sizes') }}">
                        {{ csrf_field() }}
                        <div class="row g-3">
                            <div class="col-md-12 col-12">
                                <div class="select-div">
                                    <label for="validationDefault02" class="form-label">التصنيفات </label>
                                    <select class="form-select select-modal" name="category_ids[]" id="validationDefault02" multiple>
                                        @foreach($categories as $cat_key => $category)
                                            <option value="{{ $category['id'] }}">{{ $category['name_'.$lang] }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_ids')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div><!--end col-->
                            <div class="col-md-12 col-12">
                                <div>
                                    <label for="size" class="form-label">المقاسات</label>
                                    <input type="text" name="size" class="form-control" id="size" placeholder="">
                                    @error('size')
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
                                                                        اضافة مقاس جديد
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
                                                                    <th>المقاس</th>
                                                                    <th>التصنيفات</th>
                                                                    <th>عدد المنتجات</th>
                                                                    <th> فعال</th>
                                                                    <th>الاجراءات</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @foreach($sizes as $key => $brand)
                                                                    <tr class="image_class{{ $brand['id'] }}">
                                                                        <td>{{ $key + 1 }}</td>
                                                                        <td>{{ $brand['size'] }}</td>
                                                                            <?php
                                                                            $founds = \App\Models\CategorySize::where('size_id' , $brand['id'])->get();
                                                                            ?>
                                                                        <td>@foreach($founds as $brand_category) {{ $brand_category['category']['name_'.$lang] }} <br> @endforeach</td>
                                                                        <td>{{ count($brand->product_store_stocks) }}</td>
                                                                        <td class="colors">
                                                                            <div class="form-check form-switch form-switch-success d-inline-block">
                                                                                <input class="form-check-input active" model_type="Size" model_id="{{ $brand['id'] }}" type="checkbox" role="switch" id="SwitchCheck3" {{ $brand['activation'] == true ? 'checked' : '' }}>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <ul class="actions-list">
                                                                                <li>
                                                                                    <a title="quick edit"
                                                                                       data-bs-placement="top"
                                                                                       class="edit-btn" model_id="{{ $brand['id'] }}" href="#">
                                                                                        <i class="bi bi-pencil"></i>
                                                                                    </a>
                                                                                </li>
                                                                                <li>
                                                                                    @if(request()->type == 'archived')
                                                                                        <a title="delete" delete_url="settings/sizes/"
                                                                                           object_id="{{ $brand['id'] }}" data-bs-toggle="tooltip"
                                                                                           data-bs-placement="top" button_type="delete"
                                                                                           class="archive-btn sa-warning" href="#">
                                                                                            <i class="ph-trash"></i>
                                                                                        </a>
                                                                                        <a title="restore" delete_url="settings/sizes/archive/"
                                                                                           object_id="{{ $brand['id'] }}" data-bs-toggle="tooltip"
                                                                                           data-bs-placement="top" button_type="restore"
                                                                                           class="archive-btn sa-warning" href="#">
                                                                                            <i class="ri-restart-line"></i>
                                                                                        </a>
                                                                                    @else
                                                                                        <a title="archive" delete_url="settings/sizes/archive/"
                                                                                           object_id="{{ $brand['id'] }}" data-bs-toggle="tooltip"
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
                    size: "required",
                    "category[]": "required"
                },
                messages: {
                    size: "اسم البراند بالعربية مطلوب",
                    "category[]": "من فضلك اختر تصنيف"
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
                url: link + "/admin_panel/settings/sizes/" + model_id,
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
                url: link + "/admin_panel/settings/sizes/" + modell_id +"/edit?model_type=" + model_type,
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
@endsection
