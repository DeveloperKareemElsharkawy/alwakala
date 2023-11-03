@extends('admin.layout.master')
@section('pageTitle', trans('admin.vendors'))
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
    <?php
    $lang = app()->getLocale();
    ?>

        <!-- Grids in modals -->
    <div class="modal fade margin_label" id="exampleModalgrid" data-bs-backdrop="static" tabindex="-1"
         aria-labelledby="exampleModalgridLabel" aria-modal="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ri-close-line"></i>
                </button>
                <div class="modal-header d-block text-center">
                    <h5 class="modal-title" id="exampleModalgridLab">اضافة فرع جديد</h5>
                </div>
                <div class="modal-body px-5">

                    <div class="card-body form-steps new_branch_modal">
                        <form class="my_form" id="wizard" method="post" action="{{ url('admin_panel/branch') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="step-arrow-nav mb-4 d-none">
                                <ul class="nav nav-pills custom-nav nav-justified" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="steparrow-gen-info-tab" data-bs-toggle="pill" data-bs-target="#steparrow-gen-info" type="button" role="tab" aria-controls="steparrow-gen-info" aria-selected="true" data-position="0">General</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="steparrow-description-info-tab" data-bs-toggle="pill" data-bs-target="#steparrow-description-info" type="button" role="tab" aria-controls="steparrow-description-info" aria-selected="false" data-position="1" tabindex="-1">Description</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="pills-experience-tab" data-bs-toggle="pill" data-bs-target="#pills-experience" type="button" role="tab" aria-controls="pills-experience" aria-selected="false" data-position="2" tabindex="-1">Finish</button>
                                    </li>
                                </ul>
                            </div>

                            <div class="tab-content">
                                <div class="tab-pane fade active show" id="steparrow-gen-info" role="tabpanel" aria-labelledby="steparrow-gen-info-tab">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p class="mb-0 text-center">
                                                Step 1
                                            </p>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="mb-5 position-relative">
                                            <div class="avatar-upload cover">
                                                <div class="avatar-edit ">
                                                    <input type='file' id="imageUpload2" name="cover" class="imageUpload" accept=".png, .jpg, .jpeg"/>
                                                    <label for="imageUpload2">
                                                    <span>
                                                        اضافه بانر
                                                    </span>
                                                        <i class="bx bxs-plus-circle"></i>
                                                    </label>
                                                </div>
                                                <div class="avatar-preview">
                                                    <div class="imagePreview" id="imagePreview2"
                                                         style="background-image: url({{ asset('admin') }}/assets/images/users/48/empty.png);">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="avatar-upload store-avatar">
                                                <div class="avatar-edit ">
                                                    <input type='file' id="imageUpload3" name="logo" class="imageUpload" accept=".png, .jpg, .jpeg"/>
                                                    <label for="imageUpload3">
                                                        <i class="bx bxs-plus-circle"></i>
                                                    </label>
                                                </div>
                                                <div class="avatar-preview">
                                                    <div class="imagePreview" id="imagePreview3"
                                                         style="background-image: url({{asset('admin')}}/assets/images/users/48/empty.png);">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="mb-2 col-md-6 col-12">
                                                <div>
                                                    <label for="store_name" class="form-label">اسم المتجر</label>
                                                    <input type="text" class="form-control" name="store_name" id="store_name" placeholder="">
                                                    <input name="store_id" id="store_id" value="{{ $store['id'] }}" hidden="">
                                                </div>
                                            </div><!--end col-->
                                            <div class="mb-2 col-md-6 col-12">
                                                <div>
                                                    <label for="address" class="form-label">العنوان</label>
                                                    <input type="text" class="form-control" name="store_address" id="address" placeholder="">
                                                </div>
                                            </div><!--end col-->
                                            <div class="mb-2 col-md-6 col-12">
                                                <div>
                                                    <label for="phonee" class="form-label">رقم الهاتف</label>
                                                    <input type="tel" minlength="11" maxlength="11" class="form-control  password-input"
                                                           id="phonee" placeholder="" name="store_phone" oninput="this.value = this.value.replace(/[^0-9+()]/g, '');" pattern=".{11,11}" required>
                                                </div>
                                            </div><!--end col-->
                                            <div class="mb-2 col-md-6 col-12">
                                                <div>
                                                    <label for="landing_number" class="form-label">رقم الهاتف الارضي</label>
                                                    <input type="tel" class="form-control  password-input"
                                                           id="landing_number" placeholder="" name="landing_number" required>
                                                </div>
                                            </div><!--end col-->
                                            <div class="mb-2 col-md-6 col-12">
                                                <div class="input-group has-validation select-div">
                                                    <label for="city_id" class="form-label">الولاية</label>
                                                    <select name="city_id" class="form-select p-2 select-modal" id="city_id" required="">
                                                        @foreach($cities as $city)
                                                            <option {{ $city->id == $store->city_id ? 'selected' : '' }} value="{{ $city->id }}">{{ $city['name_'.$lang] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="mb-2 col-md-6 col-12">
                                                <?php
                                                $categoryy = \App\Models\CategoryStore::where('store_id', $store->id)->first();
                                                ?>
                                                <div class="input-group has-validation select-div">
                                                    <label for="category_id" class="form-label">التصنيف</label>
                                                    <select class="form-select p-2 select-modal" name="category_id" id="category_id" required="">
                                                        <option selected="" disabled="" value=""></option>
                                                        @foreach($categories as $category)
                                                            <option {{ $category->id == $categoryy->category_id ? 'selected' : '' }} value="{{ $category->id }}">{{ $category['name_'.$lang] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="hstack gap-2 mt-5 justify-content-center">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">اغلاق</button>
                                        <button type="button" class="btn btn-gradient nexttab nexttab" data-nexttab="steparrow-description-info-tab">التالي</button>
                                    </div>
                                </div>
                                <!-- end tab pane -->

                                <div class="tab-pane fade" id="steparrow-description-info" role="tabpanel" aria-labelledby="steparrow-description-info-tab">
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <p class="mb-0 text-center">
                                                Step 2
                                            </p>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div>
                                                        <label for="name1" class="form-label">الاسم مدير الفرع كامل</label>
                                                        <input type="text" name="user_name" value="{{ $store->user->name }}" class="form-control" id="name1" placeholder="">
                                                    </div>
                                                    <div>
                                                        <label for="email" class="form-label">البريد الالكتروني</label>
                                                        <input type="email" name="user_email" value="{{ $store->user->email }}" class="form-control" id="email" placeholder="">
                                                    </div>
                                                    <div>
                                                        <label for="phone" class="form-label">رقم الهاتف</label>
                                                        <input name="user_phone" type="tel" minlength="11" value="{{ $store->user->mobile }}" maxlength="11" class="form-control  password-input"
                                                               id="phone" placeholder=""
                                                               oninput="this.value = this.value.replace(/[^0-9+()]/g, '');" pattern=".{11,11}"
                                                               required>
                                                    </div>
                                                    <div>
                                                        <label for="password" class="form-label">الرقم السري</label>
                                                        <input name="password" type="password" value="" class="form-control password-input"
                                                               id="password" placeholder=""
                                                               required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 text-center">
                                                    <div class="avatar-upload branch_manager">
                                                        <div class="avatar-edit">
                                                            <input type='file' id="imageUpload" name="image" class="imageUpload" accept=".png, .jpg, .jpeg"/>
                                                            <label for="imageUpload">
                                                             <span>
                                                        اضافه صورة
                                                    </span>
                                                                <i class="bx bxs-plus-circle"></i>
                                                            </label>
                                                        </div>
                                                        <div class="avatar-preview">
                                                            <div class="imagePreview" id="imagePreview"
                                                                 style="background-image: url({{ $store->user->image_url }});">
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <label class="form-label mt-2">صورة مدير الفرع</label>
                                                </div>
                                            </div>

                                        </div>

                                    </div><!--end row-->
                                    <div class="hstack gap-2 mt-5 justify-content-center">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">اغلاق</button>
                                        <button type="button" class="btn btn-light btn-label previestab " data-previous="steparrow-gen-info-tab"><i class="ri-arrow-left-line label-icon align-middle fs-lg me-2"></i> الرجوع</button>
                                        <button type="button" class="btn btn-gradient nexttab nexttab" data-nexttab="pills-experience-tab">التالي</button>

                                    </div>


                                </div>
                                <!-- end tab pane -->

                                <div class="tab-pane fade" id="pills-experience" role="tabpanel" aria-labelledby="pills-experience-tab">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p class="mb-4 text-center">
                                                Step 3
                                            </p>
                                        </div>
                                    </div>
                                    <div class="wrapper">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="box">
                                                    <div class="text-center image-text">

                                                    </div>
                                                    <div class="upload-options">
                                                        <label>
                                                            <p>
                                                           <span>
                                                        اضافه صورة
                                                    </span>
                                                                <i class="bx bxs-plus-circle"></i>
                                                            </p>
                                                            <input type="file" name="identity" class="image-upload" accept="image/*" />
                                                        </label>
                                                    </div>
                                                    <div class="js--image-preview mt-3"></div>
                                                    <div class="text-center image-text">
                                                        A Copy Of The Branch Manager's Id
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="box">
                                                    <div class="text-center image-text">

                                                    </div>
                                                    <div class="upload-options">
                                                        <label>
                                                            <p>
                                                            <span>
                                                        اضافه صورة
                                                    </span>
                                                                <i class="bx bxs-plus-circle"></i>
                                                            </p>
                                                            <input type="file" name="text_card" class="image-upload" accept="image/*" />
                                                        </label>
                                                    </div>
                                                    <div class="js--image-preview mt-3"></div>
                                                    <div class="text-center image-text">
                                                        Copy Of The Commercial Register
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="box">
                                                    <div class="text-center image-text">

                                                    </div>
                                                    <div class="upload-options">
                                                        <label>
                                                            <p>
                                                            <span>
                                                        اضافه صورة
                                                    </span>
                                                                <i class="bx bxs-plus-circle"></i>
                                                            </p>
                                                            <input type="file" name="licence" class="image-upload" accept="image/*" />
                                                        </label>
                                                    </div>
                                                    <div class="js--image-preview mt-3"></div>
                                                    <div class="text-center image-text">
                                                        A Copy Of The Tax Record
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="hstack gap-2 mt-5 justify-content-center">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">اغلاق</button>
                                                    <button type="button" class="btn btn-light btn-label previestab  d-none" data-previous="steparrow-description-info-tab"><i class="ri-arrow-left-line label-icon align-middle fs-lg me-2"></i> الرجوع</button>
                                                    <button id="finish" type="submit" class="btn btn-gradient">حفظ</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- end tab pane -->
                            </div>
                            <!-- end tab content -->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grids in modals -->
    <div class="modal fade" id="exampleModalgridedit" data-bs-backdrop="static"
         aria-labelledby="exampleModalgridLabeledit" aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ri-close-line"></i>
                </button>
                <div class="modal-body px-5 py-3">
                    <div id="gmaps-markers" class="gmaps"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content show_vendor">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header vendor_header">

                            <div class="d-flex align-items-center">
                                <div class="border rounded-circle flex-shrink-0 position-relative">
                                    <img src="{{ $store->image_url }}" alt=""
                                         class="avatar-sm rounded-circle">
                                </div>
                                <div class="flex-grow-1 ms-2 text-start store-info">
                                    <h5 class="fs-md d-flex align-items-center">
                                        {{ $store->name }}
                                        <i class="ri-checkbox-circle-fill px-2"></i>
                                    </h5>
                                    <p class="text-muted mb-0">{{ count($store->followers) . ' ' . trans('admin.followers') }}  -  {{ count($store->views) . ' ' . trans('admin.views') }}</p>
                                </div>
                            </div>
                            <div class="float-end created_at">
                                {!! $store->getBadge() !!}
                            </div>
                        </div>
                        <div class="card-body pt-1 pb-0">
                            <div class="row">
                                <div class="col-md-3">
                                    <div id="vendor_sidebar">
                                        <div class="container-fluid p-0">
                                            @include('admin.vendors.store_sidebar')
                                        </div>
                                        <!-- Sidebar -->
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="card store-places">
                                        <div class="card-body p-0">
                                            <div class="add-btns mb-2 justify-content-end">
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#exampleModalgrid"
                                                   class="btn btn-warning role-add">
                                                    <i class=" bx bx-plus"></i>
                                                    اضافة فرع جديد
                                                </a>
                                            </div>
                                            <div class="">
                                                <table id="vendor_places" class="dataTable row-border branches-table" style="width:100%">
                                                    <thead class="d-none">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>#</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($store->branches as $key => $branch)
                                                        <tr class="image_class{{ $branch['id'] }}">
                                                            <td class="d-none">
                                                                {{ $key + 1 }}
                                                            </td>
                                                            <td>
                                                                <div class="card border-solid border">
                                                                    <div class="card-body pt-0 p-2">
                                                                        <div class="branch_header">
                                                                            <div class="btn-group">
                                                                                <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                                    <i class="bx bx-dots-horizontal-rounded"></i>
                                                                                </button>
                                                                                <div class="dropdown-menu">
                                                                                    <a title="archive" delete_url="archive/store/"
                                                                                       object_id="{{ $branch['id'] }}" data-bs-toggle="tooltip"
                                                                                       data-bs-placement="top" button_type="archive" class="dropdown-item text-danger fw-bold sa-warning" href="#">ارشفة</a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="align-items-center border border-solid rounded overflow-hidden">
                                                                            <img class="map_image" src="{{ $branch->cover_url }}"/>
                                                                        </div>
                                                                        <div class="branch_data">
                                                                            <a class="branch_name" href="#">
                                                                                <span>{{ $branch->name }}</span>
                                                                            </a>
                                                                            {!! $branch->getBadge() !!}
                                                                        </div>
                                                                        <ul class=" mb-2 delivery-info">
                                                                            <li>
                                                                                <strong>المحافظة : </strong>
                                                                                {{ $branch->city['name_'.$lang] }}
                                                                            </li>
                                                                            <li>
                                                                                <strong>رقم الهاتف : </strong>
                                                                                {{ $branch->mobile }}
                                                                            </li>
                                                                            @if($branch->land_number)
                                                                            <li>
                                                                                <strong>الخط الساخن : </strong>
                                                                                {{ $branch->land_number }}
                                                                            </li>
                                                                            @endif
{{--                                                                            <li>--}}
{{--                                                                                <div class="map_input">--}}
{{--                                                                                    <a class="show_location" data-bs-toggle="modal"--}}
{{--                                                                                       data-bs-target="#exampleModalgridedit" href="#">--}}
{{--                                                                                        <span>View Location On Map</span>--}}
{{--                                                                                        <i class="ri-map-pin-fill"></i>--}}
{{--                                                                                    </a>--}}
{{--                                                                                </div>--}}
{{--                                                                            </li>--}}
                                                                        </ul>
                                                                        <div class="branch_footer">
                                                                            <span class="map_default">
                                                                                مدير الفرع :
                                                                                <span>{{ $branch->user->name }}</span>
                                                                            </span>
                                                                            <a href="{{ url('admin_panel/vendors/' . $branch->id . '?type=branch') }}">
                                                                                زيارة المورد
                                                                                <i class="bx bx-arrow-back"></i>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div><!-- end card-body -->
                                    </div><!-- end card -->
                                </div>
                                <!--end col-->

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

    <footer class="footer d-none">
    </footer>

@endsection
@section('backend-footer')
    <script src="{{ asset('admin') }}/assets/js/pages/form-wizard.init.js"></script>
    <!-- google maps api -->
    <script src="https://maps.google.com/maps/api/js?key=AIzaSyCtSAR45TFgZjOs4nBFFZnII-6mMHLfSYI"></script>
    <!-- gmaps plugins -->
    <script src="{{ asset('admin') }}/assets/libs/gmaps/gmaps.min.js"></script>

    <!-- gmaps init -->
    <script src="{{ asset('admin') }}/assets/js/pages/gmaps.init.js"></script>
    <!-- select2 js -->
    <script src="{{ asset('admin') }}/assets/libs/select2/select2.min.js"></script>
    <!-- Sweet Alerts js -->
    <script src="{{ asset('admin') }}/assets/libs/sweetalert2/sweetalert2.min.js"></script>
    @if(request()->add_new == 'true')
        <script type="text/javascript">
            window.onload = () => {
                $('#exampleModalgrid').modal('show');
            }
        </script>
    @endif
    <script src="{{ asset('admin') }}/assets/js/sweetalert_ar.js"></script>
    <script src="{{ asset('admin') }}/assets/js/additional-methods.min.js"></script>
    <script src="{{ asset('admin') }}/assets/js/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".my_form").validate({
                rules: {
                    cover: {
                        required: {{ $store->cover ? 'false' : 'true' }},
                    },
                    image: {
                        required: {{ $store->user->image ? 'false' : 'true' }},
                    },
                    logo: {
                        required: {{ $store->logo ? 'false' : 'true' }},
                    },
                    store_name: "required",
                    store_address: "required",
                    city_id: "required",
                    category_id: "required",
                    user_name: "required",
                    email: "required",
                    user_phone: "required",
                    text_card: {
                        required: {{ $store->text_card ? 'false' : 'true' }},
                    },
                    licence: {
                        required: {{ $store->licence ? 'false' : 'true' }},
                    },
                    identity: {
                        required: {{ $store->identity ? 'false' : 'true' }},
                    },
                },
                messages: {
                    cover: {
                        required: " صورة الكفر مطلوبة",
                    },
                    image: {
                        required: " صورة  مطلوبة",
                    },
                    logo: {
                        required: "لوجو الشركة مطلوب",
                    },
                    store_name: "اسم المتجر مطلوب",
                    store_address: "العنوان للمتجر مطلوب",
                    city_id: "الولاية مطلوبة",
                    category_id: "التصنيف مطلوب",
                    user_name: "اسم المستخدم كامل مطلوب",
                    email: "البريد مطلوب",
                    user_phone: "رقم هاتف المستخدم مطلوب",
                    text_card: {
                        required: "البطاقة النصية مطلوبة",
                    },
                    licence: {
                        required: "السجل الضريبي مطلوب",
                    },
                    identity: {
                        required: "الهوية الوطنية مطلوبة",
                    },
                }
            });
        });
    </script>
@endsection
