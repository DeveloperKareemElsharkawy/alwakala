@extends('admin.layout.master')
@if(Request::is('admin_panel/settings/categories'))
    @section('pageTitle', 'التصنيفات الرئيسية')
@endif
@if(Request::is('admin_panel/settings/subcategories'))
    @section('pageTitle', 'التصنيفات الفرعية')
@endif
@if(Request::is('admin_panel/settings/subsubcategories'))
    @section('pageTitle', 'التصنيفات الفرعية الثانية')
@endif
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
        .error {
            color: red
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
                        <h5 class="modal-title" id="exampleModalgridLab">اضافة تصنيف جديد</h5>
                    </div>
                    <div class="modal-body px-5">
                        <form class="my_form px-5" method="post" action="{{ url('admin_panel/settings/categories') }}"
                              enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="avatar-upload">
                                        <div class="avatar-edit">
                                            <input type='file' id="imageUpload" name="image"
                                                   accept=".png, .jpg, .jpeg" required/>
                                            <label for="imageUpload">
                                                <i class="bx bxs-plus-circle"></i>
                                            </label>
                                        </div>
                                        <div class="avatar-preview">
                                            <div id="imagePreview"
                                                 style="background-image: url({{ asset('admin') }}/assets/images/upload.png);">
                                            </div>
                                        </div>
                                        @error('image')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                @if(Request::is('admin_panel/settings/subcategories') || Request::is('admin_panel/settings/subsubcategories'))
                                    <div class="col-md-12 col-12">
                                        <div class="select-div">
                                            <label for="category_id" class="form-label">التصنيف الرئيسي </label>
                                            <select class="form-select select-modal" name="category_id"
                                                    id="category_id">
                                                @foreach($main_categories as $cat_key => $categoryy)
                                                    <option
                                                        value="{{ $categoryy['id'] }}">{{ $categoryy['name_'.$lang] }}</option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div><!--end col-->
                                @endif
                                @if(Request::is('admin_panel/settings/subsubcategories'))
                                    <div class="col-md-12 col-12">
                                        <div class="select-div">
                                            <label for="subcategory_id" class="form-label">التصنيف الفرعي </label>
                                            <select class="form-select select-modal" name="subcategory_id"
                                                    id="subcategory_id">
                                                    <option
                                                        value="" disabled selected>اختر التصنيف الفرعي اولا</option>
                                            </select>
                                            @error('subcategory_id')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div><!--end col-->
                                @endif
                                <div class="col-md-12 col-12">
                                    <div>
                                        <label for="name_ar" class="form-label">الاسم بالعربية</label>
                                        <input type="text" name="name_ar" class="form-control" id="name_ar"
                                               placeholder="">
                                        @error('name_ar')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div><!--end col-->

                                <div class="col-md-12 col-12">
                                    <div>
                                        <label for="name_en" class="form-label">الاسم بالانجليزية</label>
                                        <input type="text" name="name_en" class="form-control" id="name_en"
                                               placeholder="">
                                        @error('name_en')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div><!--end col-->
                                <div class="col-lg-12">
                                    <div class="hstack gap-2 mt-5 justify-content-center">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">اغلاق
                                        </button>
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
                                                                    <div
                                                                        class="add-btns mb-0 justify-content-between mb-2">
                                                                        <a href="#" data-bs-toggle="modal"
                                                                           data-bs-target="#exampleModalgrid"
                                                                           class="btn btn-warning role-add">
                                                                            <i class=" bx bx-plus"></i>
                                                                            اضافة جديد
                                                                        </a>
                                                                        <a href="{{ url()->current().'?type=archived' }}"
                                                                           class="btn btn-danger">
                                                                            <i class="ph-archive-box"></i>
                                                                            الارشيف
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
                                                                    <th>الصورة</th>
                                                                    <th>الاسم</th>
                                                                    @if(Request::is('admin_panel/settings/subcategories'))
                                                                        <th>التصنيف الرئيسي</th>
                                                                    @endif
                                                                    @if(Request::is('admin_panel/settings/subsubcategories'))
                                                                        <th>التصنيف الفرعي</th>
                                                                    @endif
                                                                    <th>عدد المتاجر</th>
                                                                    <th>عدد منتجات</th>
                                                                    <th> فعال</th>
                                                                    <th>الاجراءات</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @foreach($categories as $key => $category)
                                                                    <tr class="image_class{{ $category['id'] }}">
                                                                        <td>{{ $key + 1 }}</td>
                                                                        <td>
                                                                            @if(isset($category['image_url']))
                                                                                <img width="40" height="40"
                                                                                     class="user-avatar object-fit-contain rounded-circle"
                                                                                     src="{{ $category['image_url'] }}">
                                                                            @endif
                                                                        </td>
                                                                        <td>{{ $category['name_'.$lang] }}</td>
                                                                        @if(Request::is('admin_panel/settings/subcategories'))
                                                                            <td>{{ $category['parent']['name_'.$lang] }}</td>
                                                                        @endif
                                                                        @if(Request::is('admin_panel/settings/subsubcategories'))
                                                                            <td>{{ $category['parent']['name_'.$lang] }}</td>
                                                                        @endif
                                                                        <td>{{ count($category->stores) }}</td>
                                                                        <td>{{ count($category->Products) }}</td>
                                                                        <td class="colors">
                                                                            <div
                                                                                class="form-check form-switch form-switch-success d-inline-block">
                                                                                <input class="form-check-input active"
                                                                                       model_type="Category"
                                                                                       model_id="{{ $category['id'] }}"
                                                                                       type="checkbox" role="switch"
                                                                                       id="SwitchCheck3" {{ $category['activation'] == true ? 'checked' : '' }}>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <ul class="actions-list">
                                                                                <li>
                                                                                    <a title="quick edit"
                                                                                       data-bs-placement="top"
                                                                                       class="edit-btn"
                                                                                       model_id="{{ $category['id'] }}"
                                                                                       href="#">
                                                                                        <i class="bi bi-pencil"></i>
                                                                                    </a>
                                                                                </li>
                                                                                <li>
                                                                                    @if(request()->type == 'archived')
                                                                                        <a title="delete"
                                                                                           delete_url="settings/categories/"
                                                                                           object_id="{{ $category['id'] }}"
                                                                                           data-bs-toggle="tooltip"
                                                                                           data-bs-placement="top"
                                                                                           button_type="delete"
                                                                                           class="archive-btn sa-warning"
                                                                                           href="#">
                                                                                            <i class="ph-trash"></i>
                                                                                        </a>
                                                                                        <a title="restore"
                                                                                           delete_url="settings/categories/archive/"
                                                                                           object_id="{{ $category['id'] }}"
                                                                                           data-bs-toggle="tooltip"
                                                                                           data-bs-placement="top"
                                                                                           button_type="restore"
                                                                                           class="archive-btn sa-warning"
                                                                                           href="#">
                                                                                            <i class="ri-restart-line"></i>
                                                                                        </a>
                                                                                    @else
                                                                                        <a title="archive"
                                                                                           delete_url="settings/categories/archive/"
                                                                                           object_id="{{ $category['id'] }}"
                                                                                           data-bs-toggle="tooltip"
                                                                                           data-bs-placement="top"
                                                                                           button_type="archive"
                                                                                           class="archive-btn sa-warning"
                                                                                           href="#">
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
@endsection
@section('backend-footer')
    <!-- select2 js -->
    <script src="{{ asset('admin') }}/assets/libs/select2/select2.min.js"></script>
    <script src="{{ asset('admin') }}/assets/js/additional-methods.min.js"></script>
    <script src="{{ asset('admin') }}/assets/js/jquery.validate.min.js"></script>
    @if(Request::is('admin_panel/settings/categories'))
        <script>
            $(document).ready(function () {
                $(".my_form").validate({
                    rules: {
                        name_ar: "required",
                        name_en: "required",
                        image: "required",
                    },
                    messages: {
                        name_ar: "اسم البراند بالعربية مطلوب",
                        name_en: "اسم البراند بالانجليزية مطلوب",
                        image: "صورة البراند مطلوبة",
                    }
                });
            });
        </script>
    @endif
    @if(Request::is('admin_panel/settings/subcategories'))
        <script>
            $(document).ready(function () {
                $(".my_form").validate({
                    rules: {
                        name_ar: "required",
                        name_en: "required",
                        image: "required",
                        category_id: "required"
                    },
                    messages: {
                        name_ar: "اسم البراند بالعربية مطلوب",
                        name_en: "اسم البراند بالانجليزية مطلوب",
                        image: "صورة البراند مطلوبة",
                        category_id: "من فضلك اختر تصنيف"
                    }
                });
            });
        </script>
    @endif
    @if(Request::is('admin_panel/settings/subsubcategories'))
        <script>
            $(document).ready(function () {
                $(".my_form").validate({
                    rules: {
                        name_ar: "required",
                        name_en: "required",
                        image: "required",
                        category_id: "required",
                        subcategory_id: "required"
                    },
                    messages: {
                        name_ar: "اسم البراند بالعربية مطلوب",
                        name_en: "اسم البراند بالانجليزية مطلوب",
                        image: "صورة البراند مطلوبة",
                        category_id: "من فضلك اختر تصنيف",
                        subcategory_id: "من فضلك اختر تصنيف فرعي"
                    }
                });
            });
        </script>
    @endif
    <!-- Sweet Alerts js -->
    <script src="{{ asset('admin') }}/assets/libs/sweetalert2/sweetalert2.min.js"></script>
    <script>
        var link = '<?php echo url('/'); ?>';
        $("body").on("click", ".edit-btn", function (e) {
            var model_id = $(this).attr('model_id');
            var urll = '<?php echo url()->current(); ?>';
            $('#exampleModalgrideditform').html('<div class="col-md-12 col-xl-12 text-center loading"><i class="mdi mdi-loading fa-spin"></i></div>');

            $('#exampleModalgridedit').modal('show');
            $.ajax({
                type: "GET",
                url: link + "/admin_panel/settings/categories/" + model_id + "?url=" + urll,
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
                url: link + "/admin_panel/settings/categories/" + modell_id + "/edit?model_type=" + model_type,
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
        $('#category_id').on('change', function() {
            var category_id = $(this).val();
            $.get(link + '/ajax_subcatgeories?category_id=' + category_id, function (data) {
                $('#subcategory_id').empty();
                $.each(data, function (index, subcatObj) {
                    $('#subcategory_id').append('<option value="' + subcatObj.id + '">' + subcatObj.name + '</option>');
                });
            });
        });
    </script>
@endsection
