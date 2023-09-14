@extends('admin.layout.master')
@section('pageTitle', trans('admin.users'))
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
@endsection
@section('backend-main')
    @if(request()->type != 'false')
        <!-- Grids in modals -->
        <div class="modal fade" id="exampleModalgrid" data-bs-backdrop="static" tabindex="-1"
             aria-labelledby="exampleModalgridLabel" aria-modal="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ri-close-line"></i>
                    </button>
                    <div class="modal-header d-block text-center">
                        <h5 class="modal-title" id="exampleModalgridLab">اضافة مستخدم جديد</h5>
                    </div>
                    <div class="modal-body px-5">
                        <form class=" px-5" method="post" action="{{ route('users.store') }}"
                              enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="avatar-upload">
                                        <div class="avatar-edit">
                                            <input type='file' id="imageUpload" name="image"
                                                   accept=".png, .jpg, .jpeg"/>
                                            <label for="imageUpload">
                                                <i class="bx bxs-plus-circle"></i>
                                            </label>
                                        </div>
                                        <div class="avatar-preview">
                                            <div id="imagePreview"
                                                 style="background-image: url({{ asset('admin') }}/assets/images/users/48/upload.jpg);">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div>
                                        <label for="name1" class="form-label">الاسم كامل</label>
                                        <input type="text" class="form-control" name="name" id="name1" placeholder="">
                                    </div>
                                </div><!--end col-->
                                <div class="col-md-6 col-12">
                                    <div>
                                        <label for="email" class="form-label">البريد الالكتروني</label>
                                        <input type="email" class="form-control" name="email" id="email" placeholder="">
                                    </div>
                                </div><!--end col-->

                                <div class="col-md-6 col-12">
                                    <div>
                                        <label for="phone" class="form-label">رقم الهاتف</label>
                                        <input type="tel" minlength="11" maxlength="11" name="mobile"
                                               class="form-control  password-input"
                                               id="phone" placeholder="" autocomplete="off"
                                               oninput="this.value = this.value.replace(/[^0-9+()]/g, '');"
                                               pattern=".{11,11}"
                                               required>
                                    </div>
                                </div><!--end col-->
                                <div class="col-md-6 col-12">
                                    <div class="select-div">
                                        <label for="validationDefault04" class="form-label">دور العضوية</label>
                                        <select class="form-select select-modal" name="type" id="validationDefault04" required="">
                                            <option selected="" disabled="" value="" hidden></option>
                                            <option value="ADMIN">ادمن</option>
                                            <option value="CONSUMER">مستهلك</option>
                                            <option value="SELLER">بائع</option>
                                        </select>
                                    </div>
                                </div><!--end col-->
                                <div class="col-md-6 col-12">
                                    <div>
                                        <label class="form-label" for="password-inputt">رقم المرور </label>
                                        <div class="position-relative auth-pass-inputgroup mb-3">
                                            <input type="password" name="password"
                                                   class="form-control pe-6 password-input "
                                                   placeholder="" id="password-inputt" required>
                                            <button
                                                class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon"
                                                type="button" id="password-addonn"><i
                                                    class="ri-eye-fill align-middle"></i></button>
                                        </div>
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

        <!-- Grids in modals -->
        <div class="modal fade" id="exampleModalgridedit" data-bs-backdrop="static" tabindex="-1"
             aria-labelledby="exampleModalgridLabeledit" aria-modal="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content" id="exampleModalgrideditform">

                </div>
            </div>
        </div>
    @endif

    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-head">
                        <h5>{{ request()->type == 'false' ? 'المستخدمين المؤرشفين' : 'المستخدمين المفعلين'}} </h5>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="filter-div">
                                        {{--                                        <form class="filter-form" action="#">--}}
                                        {{--                                            <div class="input-group has-validation">--}}
                                        {{--                                                <select class="form-select p-2 select" id="validationTooltipUsername"--}}
                                        {{--                                                        aria-describedby="validationTooltipUsernamePrepend" required="">--}}
                                        {{--                                                    <option selected="" disabled="" value="">دور العضوية</option>--}}
                                        {{--                                                    <option>دور</option>--}}
                                        {{--                                                    <option>دور</option>--}}
                                        {{--                                                    <option>دور</option>--}}
                                        {{--                                                </select>--}}
                                        {{--                                                <!--<span class="input-group-text p-0" id="validationTooltipUsernamePrepend"><button-->--}}
                                        {{--                                                <!--class="d-flex align-items-center btn m-0 py-0 px-2 btn-warning"><i-->--}}
                                        {{--                                                <!--class="bx bx-filter-alt fs-4xl"></i> </button></span>-->--}}
                                        {{--                                            </div>--}}
                                        {{--                                        </form>--}}
                                        <form class="filter-form form_date" action="{{ url('admin_panel/users?type='.$request.'&date='.request()->date.'') }}">
                                            <div class="input-group has-validation">
                                                <input type="text" class="form-control d-none" value="{{ $request }}" name="type" id="type">
                                                <input type="date" class="form-control" name="date" id="date"
                                                       placeholder="التاريخ" aria-describedby="exampleInputdatePrepend"
                                                       required="">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @if(request()->type != 'false')
                                <div class="col-md-6">
                                    <div class="add-btns mb-0 justify-content-end">
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#exampleModalgrid"
                                           class="btn btn-warning role-add">
                                            <i class=" bx bx-plus"></i>
                                            اضافة مستخدم جديد
                                        </a>
                                    </div>
                                </div>
                                    @endif
                            </div>
                            <div class="table-responsive">
                                <table id="user_example" class="dataTable row-border" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>اسم المستخدم</th>
                                        <th>الهاتف</th>
                                        <th>الدور</th>
                                        <th>تاريخ الانشاء</th>
                                        <th>الحالة</th>
                                        <th>الاجراءات</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($users as $key => $user)
                                        <tr class="image_class{{ $user->id }}">
                                            <td>{{ $key + 1 }}</td>
                                            <td style="justify-content: start" class="user-row">
                                                <img width="40" height="40"
                                                     class="user-avatar object-fit-contain rounded-circle"
                                                     src="{{ $user['image_url'] }}">
                                                <div class="user-data">
                                                    <h5>{{ $user->name ?: '---' }}</h5>
                                                    <p>{{ $user->email ?: '---' }}</p>
                                                </div>
                                            </td>
                                            <td>{{ $user->mobile ?: '---' }}</td>
                                            <td>
                                                @if($user['type_id'] == 1)
                                                    ادمن
                                                @elseif($user['type_id'] == 2)
                                                    تاجر
                                                @elseif($user['type_id'] == 3)
                                                    مشتري
                                                @endif
                                            </td>
                                            <td>{{ $user->created_at ? $user->created_at->toDateString() : '---' }}</td>
                                            <td class="status-row">
                                                <p class="alert {{ $user->activation == true ? 'alert-success' : 'alert-danger'}}">{{ $user->activation == true ? 'Active' : 'Unactive'}}</p>
                                            </td>
                                            <td>
                                                <ul class="actions-list">
                                                    <li>
                                                        <a user="{{ $user['id'] }}" data-bs-toggle="offcanvas"
                                                           data-bs-target=".theme-settings-offcanvas"
                                                           aria-controls="theme-settings-offcanvas"
                                                           class="show-btn" href="#">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        @if(request()->type == 'false')
                                                            <a title="Restore" delete_url="restore/user/"
                                                               object_id="{{ $user['id'] }}" data-bs-toggle="tooltip"
                                                               data-bs-placement="top" button_type="restore"
                                                               class="edit-btn sa-warning" href="#">
                                                                <i class="ri-restart-line"></i>
                                                            </a>
                                                        @else
                                                            <a title="quick edit" user="{{ $user['id'] }}"
                                                               class="edit-btn edit_user" href="#">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                        @endif
                                                    </li>
                                                    <li>
                                                        @if(request()->type == 'false')
                                                            <a title="archive" delete_url="users/"
                                                               object_id="{{ $user['id'] }}" data-bs-toggle="tooltip"
                                                               data-bs-placement="top" button_type="delete"
                                                               class="archive-btn sa-warning" href="#">
                                                                <i class="ph-trash"></i>
                                                            </a>
                                                        @else
                                                            <a title="archive" delete_url="archive/user/"
                                                               object_id="{{ $user['id'] }}" data-bs-toggle="tooltip"
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
    <!-- Sweet Alerts js -->
    <script src="{{ asset('admin') }}/assets/libs/sweetalert2/sweetalert2.min.js"></script>
    <script>
        $("body").on("click", ".edit_user", function (e) {
            var user = $(this).attr('user');
            $('#exampleModalgrideditform').html('<div class="col-md-12 col-xl-12 text-center loading"><i class="mdi mdi-loading fa-spin"></i></div>');
            var link = '<?php echo url('/'); ?>';
            $('#exampleModalgridedit').modal('show');
            $.ajax({
                type: "GET",
                url: link + "/admin_panel/user/" + user,
                success: function (data) {
                    $('#exampleModalgrideditform').html(data);
                }
            });
        });

        $("body").on("click", ".show-btn", function (e) {
            var user = $(this).attr('user');
            $('#collapse_body').html('<div class="col-md-12 col-xl-12 text-center loading"><i class="mdi mdi-loading fa-spin"></i></div>');
            var link = '<?php echo url('/'); ?>';
            $.ajax({
                type: "GET",
                url: link + "/admin_panel/user_show/" + user,
                success: function (data) {
                    $('#collapse_body').html(data);
                }
            });
        });

        $(document).ready(function() {
            $('#date').on('change', function() {
                $('.form_date').submit();
            });
        });
    </script>
    @if(request()->add_new == 'true')
    <script type="text/javascript">
        window.onload = () => {
            $('#exampleModalgrid').modal('show');
        }
    </script>
    @endif
    <script src="{{ asset('admin') }}/assets/js/sweetalert_ar.js"></script>
@endsection
