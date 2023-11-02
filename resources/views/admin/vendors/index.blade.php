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
    <style>
        .wrapper .upload-options label{
            display: block;
        }
    </style>
@endsection
@section('backend-main')
    <?php
    $lang = app()->getLocale();
    ?>

    <div class="modal fade" id="exampleModalgridddd" data-bs-backdrop="static" tabindex="-1"
         aria-labelledby="exampleModalgridLabellll" aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ri-close-line"></i>
                </button>
                <div class="modal-header d-block text-center">
                    <h5 class="modal-title" id="exampleModalgridLabbbb">اضافة براند جديد</h5>
                </div>
                <div class="modal-body px-5">
                    <form class="my_formm px-5" method="post" action="{{ url('admin_panel/settings/brands') }}" enctype="multipart/form-data">
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

    <!-- Grids in modals -->
    <div class="modal fade" id="exampleModalgrid" data-bs-backdrop="static" tabindex="-1"
         aria-labelledby="exampleModalgridLabel" aria-modal="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ri-close-line"></i>
                </button>
                <div class="modal-header d-block text-center">
                    <h5 class="modal-title" id="exampleModalgridLab">اضافة مورد جديد</h5>
                </div>
                <div class="modal-body px-5">

                    <div class="card-body form-steps">
                        <form id="wizard" class="my_form" method="post" action="{{ route('vendors.store') }}" enctype="multipart/form-data">
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
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <p class="mb-0 text-center">
                                                Step 1 ( Merchant data )
                                            </p>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="avatar-upload">
                                                <div class="avatar-edit">
                                                    <input type='file' id="imageUpload" name="image" class="imageUpload" accept=".png, .jpg, .jpeg"/>
                                                    <label for="imageUpload">
                                                        <i class="bx bxs-plus-circle"></i>
                                                    </label>
                                                </div>
                                                <div class="avatar-preview">
                                                    <div class="imagePreview" id="imagePreview"
                                                         style="background-image: url({{ asset('admin') }}/assets/images/users/48/upload.jpg);">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-12">
                                            <div>
                                                <label for="name1" class="form-label">الاسم كامل</label>
                                                <input type="text" name="user_name" class="form-control" id="name1" placeholder="">
                                            </div>
                                        </div><!--end col-->
                                        <div class="col-md-12 col-12">
                                            <div>
                                                <label for="email" class="form-label">البريد الالكتروني</label>
                                                <input type="email" name="user_email" class="form-control" id="email" placeholder="">
                                            </div>
                                        </div><!--end col-->

                                        <div class="col-md-12 col-12">
                                            <div>
                                                <label for="phone" class="form-label">رقم الهاتف</label>
                                                <input type="tel" minlength="11" name="user_phone" maxlength="11" class="form-control  password-input"
                                                       id="phone" placeholder="">
                                            </div>
                                        </div><!--end col-->
                                    </div><!--end row-->
                                    <div class="hstack gap-2 mt-5 justify-content-center">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">اغلاق</button>
                                        <button type="button" class="btn btn-gradient nexttab nexttab" data-nexttab="steparrow-description-info-tab">التالي</button>
                                    </div>
                                </div>
                                <!-- end tab pane -->

                                <div class="tab-pane fade" id="steparrow-description-info" role="tabpanel" aria-labelledby="steparrow-description-info-tab">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p class="mb-0 text-center">
                                                Step 2 ( Store data )
                                            </p>
                                        </div>
                                    </div>
                                    <div>
                                        <input type="number" name="store_type_id" class="form-control" id="store_type_id" value="{{ $type ? $type : 1 }}" hidden="">
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
                                                         style="background-image: url({{ asset('admin') }}/assets/images/users/48/empty.png);">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="mb-2 col-md-6 col-12">
                                                <div>
                                                    <label for="store_name" class="form-label">اسم المتجر</label>
                                                    <input type="text" name="store_name" class="form-control" id="store_name" placeholder="">
                                                </div>
                                            </div><!--end col-->
                                            <div class="mb-2 col-md-6 col-12">
                                                <div>
                                                    <label for="address" class="form-label">العنوان</label>
                                                    <input type="text" name="store_address" class="form-control" id="address" placeholder="">
                                                </div>
                                            </div><!--end col-->
                                            <div class="mb-2 col-md-6 col-12">
                                                <div>
                                                    <label for="phonee" class="form-label">رقم الهاتف</label>
                                                    <input type="tel" minlength="11" maxlength="11" class="form-control"
                                                           id="phonee" placeholder="" name="store_phone">
                                                </div>
                                            </div><!--end col-->
                                            <?php
                                                $brands = \App\Models\Brand::where('archive' , false)->get();
                                            ?>
                                            <div class="mb-2 col-md-6 col-12">
                                                <label for="validationDefault03" class="form-label">البراندات </label>
                                                <div class="add-btns mb-0 justify-content-end mb-2">
                                                    <div style="width: 90%" class="select-div">
                                                        <select class="form-select select-modal" name="brand_ids[]" id="validationDefault03" multiple>
                                                            @foreach($brands as $cat_key => $category)
                                                                <option value="{{ $category['id'] }}">{{ $category['name_'.$lang] }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('brand_ids')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <a href="#" data-bs-toggle="modal"
                                                       data-bs-target="#exampleModalgridddd"
                                                       class="btn btn-warning role-add">
                                                        <i class=" bx bx-plus"></i>
                                                    </a>
                                                </div>

                                            </div><!--end col-->

                                            <div class="mb-2 col-md-6 col-12">
                                                <div class="input-group has-validation select-div">
                                                    <label for="estate_id" class="form-label">الولاية</label>
                                                    <select class="form-select p-2 select-modal" name="city_id" id="estate_id" required="">
                                                        <option selected="" disabled="" value=""></option>
                                                        @foreach($cities as $city)
                                                            <option value="{{ $city->id }}">{{ $city['name_' . $lang] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="mb-2 col-md-6 col-12">
                                                <div class="input-group has-validation select-div">
                                                    <label for="category_id" class="form-label">التصنيف</label>
                                                    <select class="form-select p-2 select-modal" id="category_id" name="category_id" required="">
                                                        <option selected="" disabled="" value=""></option>
                                                        @foreach($categories as $category)
                                                            <option value="{{ $category->id }}">{{ $category['name_' . $lang] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="hstack gap-2 mt-5 justify-content-center">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">اغلاق</button>
                                        <button type="button" class="btn btn-light btn-label previestab" data-previous="steparrow-gen-info-tab"><i class="ri-arrow-left-line label-icon align-middle fs-lg me-2"></i> الرجوع</button>
                                        <button type="button" class="btn btn-gradient nexttab nexttab" data-nexttab="pills-experience-tab">التالي</button>
                                    </div>
                                </div>
                                <!-- end tab pane -->

                                <div class="tab-pane fade" id="pills-experience" role="tabpanel" aria-labelledby="pills-experience-tab">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p class="mb-4 text-center">
                                                Step 3 ( Store documents )
                                            </p>
                                        </div>
                                    </div>
                                    <div class="wrapper">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="box">
                                                    <div class="image-text">
                                                        National ID
                                                    </div>
                                                    <div class="upload-options">
                                                        <label>
                                                            <p>click to upload <i class="mdi mdi-attachment"></i> </p>
                                                            <input type="file" name="identity" class="image-upload" accept="image/*" />
                                                        </label>
                                                    </div>
                                                    <div class="js--image-preview mt-3"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="box">
                                                    <div class="image-text">
                                                        Text Card
                                                    </div>
                                                    <div class="upload-options">
                                                        <label>
                                                            <p>click to upload <i class="mdi mdi-attachment"></i> </p>
                                                            <input type="file" name="text_card" class="image-upload" accept="image/*" />
                                                        </label>
                                                    </div>
                                                    <div class="js--image-preview mt-3"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="box">
                                                    <div class="image-text">
                                                        Tax Record
                                                    </div>
                                                    <div class="upload-options">
                                                        <label>
                                                            <p>click to upload <i class="mdi mdi-attachment"></i> </p>
                                                            <input type="file" name="licence" class="image-upload" accept="image/*" />
                                                        </label>
                                                    </div>
                                                    <div class="js--image-preview mt-3"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="hstack gap-2 mt-5 justify-content-center">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">اغلاق</button>
                                                    <button type="button" class="btn btn-light btn-label previestab" data-previous="steparrow-description-info-tab"><i class="ri-arrow-left-line label-icon align-middle fs-lg me-2"></i> الرجوع</button>
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



    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-head">
                        <h5>
                            {{ __('admin.vendors') }}
                        </h5>
                    </div>
                </div>
            </div>
            <div class="vendor-statics">
                <div class="row">
                    <div class="col-md-3 col-6">
                        <div class="p-3 mb-2 border border-solid rounded bg-white">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-xs flex-shrink-0">
                                    <div class="avatar-title bg-primary-subtle text-primary fs-lg rounded">
                                        <i class="ri-user-line fw-light"></i>
                                    </div>
                                </div>
                                <p class="fs-md mb-0">{{ __('admin.retail') }}</p>
                                <div style="margin-right: auto" class="fs-md mb-0">
                                    <strong>{{ $all_retail }}</strong>
                                    <small>{{ __('admin.vendor_s') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="p-3 mb-2 border border-solid rounded bg-white">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-xs flex-shrink-0">
                                    <div class="avatar-title bg-primary-subtle text-primary fs-lg rounded">
                                        <i class="ri-user-line fw-light"></i>
                                    </div>
                                </div>
                                <p class="fs-md mb-0">{{ __('admin.supplier') }}</p>
                                <div style="margin-right: auto" class="fs-md mb-0">
                                    <strong>{{ $all_supplier }}</strong>
                                    <small>{{ __('admin.vendor_s') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="p-3 mb-2 border border-solid rounded bg-white">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-xs flex-shrink-0">
                                    <div class="avatar-title bg-success-subtle text-success fs-lg rounded">
                                        <i class="ri-user-follow-line fw-light"></i>
                                    </div>
                                </div>
                                <p class="fs-md mb-0">{{ __('admin.active_retail') }}</p>
                                <div style="margin-right: auto" class="fs-md mb-0">
                                    <strong>{{ $active_retail }}</strong>
                                    <small>{{ __('admin.vendor_s') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="p-3 mb-2 border border-solid rounded bg-white">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-xs flex-shrink-0">
                                    <div class="avatar-title bg-success-subtle text-success fs-lg rounded">
                                        <i class="ri-user-follow-line fw-light"></i>
                                    </div>
                                </div>
                                <p class="fs-md mb-0">{{ __('admin.active_supplier') }}</p>
                                <div style="margin-right: auto" class="fs-md mb-0">
                                    <strong>{{ $active_supplier }}</strong>
                                    <small>{{ __('admin.vendor_s') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="p-3 mb-2 border border-solid rounded bg-white">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-xs flex-shrink-0">
                                    <div class="avatar-title bg-danger-subtle text-danger fs-lg rounded">
                                        <i class="ri-user-unfollow-line fw-light"></i>
                                    </div>
                                </div>
                                <p class="fs-md mb-0">{{ __('admin.inactive_retail') }}</p>
                                <div style="margin-right: auto" class="fs-md mb-0">
                                    <strong>{{ $inactive_retail }} </strong>
                                    <small>{{ __('admin.vendor_s') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="p-3 mb-2 border border-solid rounded bg-white">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-xs flex-shrink-0">
                                    <div class="avatar-title bg-danger-subtle text-danger fs-lg rounded">
                                        <i class="ri-user-unfollow-line fw-light"></i>
                                    </div>
                                </div>
                                <p class="fs-md mb-0">{{ __('admin.inactive_supplier') }} </p>
                                <div style="margin-right: auto" class="fs-md mb-0">
                                    <strong> {{ $inactive_supplier }}</strong>
                                    <small>{{ __('admin.vendor_s') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="p-3 mb-2 border border-solid rounded bg-white">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-xs flex-shrink-0">
                                    <div class="avatar-title bg-warning-subtle text-warning fs-lg rounded">
                                        <i class="ri-contacts-line fw-light"></i>
                                    </div>
                                </div>
                                <p class="fs-md mb-0">{{ __('admin.pending_retail') }}</p>
                                <div style="margin-right: auto" class="fs-md mb-0">
                                    <strong>{{ $pending_retail }} </strong>
                                    <small>{{ __('admin.vendor_s') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="p-3 mb-2 border border-solid rounded bg-white">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-xs flex-shrink-0">
                                    <div class="avatar-title bg-warning-subtle text-warning fs-lg rounded">
                                        <i class="ri-contacts-line fw-light"></i>
                                    </div>
                                </div>
                                <p class="fs-md mb-0">{{ __('admin.pending_supplier') }}</p>
                                <div style="margin-right: auto" class="fs-md mb-0">
                                    <strong>{{ $pending_supplier }} </strong>
                                    <small>{{ __('admin.vendor_s') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header p-3 border-0 align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">{{ __('admin.vendors') }}</h4>
                        </div><!-- end card header -->

                        <div class="card-body pt-1">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="filter-div">
                                        <form class="filter-form" action="{{ url('admin_panel/vendors?type='.$type.'&active='.$active.'&verified='.$verified.'&city_id='. $city_id .'&date='.$date.'') }}">
                                            <div class="input-group has-validation">
                                                <select class="form-select p-2 select" id="validationTooltipUsername"
                                                        aria-describedby="validationTooltipUsernamePrepend" required="" name="city_id" onchange="this.form.submit()">
                                                    <option {{ !$city_id ? 'selected' : '' }} value="">الولاية</option>
                                                    @foreach($cities as $city)
                                                        <option {{ $city_id == $city->id ? 'selected' : '' }} value="{{ $city->id }}">{{ $city['name_' . $lang] }}</option>
                                                    @endforeach
                                                </select>
                                                <!--<span class="input-group-text p-0" id="validationTooltipUsernamePrepend"><button-->
                                                <!--class="d-flex align-items-center btn m-0 py-0 px-2 btn-warning"><i-->
                                                <!--class="bx bx-filter-alt fs-4xl"></i> </button></span>-->
                                            </div>
                                        </form>
                                        <form class="filter-form" action="{{ url('admin_panel/vendors?type='.$type.'&active='.$active.'&verified='.$verified.'&city_id='. $city_id .'&date='.request()->date.'') }}">
                                            <div class="input-group has-validation">
                                                <input type="date" class="form-control" id="exampleInputdate"
                                                       placeholder="التاريخ" name="date" aria-describedby="exampleInputdatePrepend"
                                                       required="" value="{{ $date }}" onchange="this.form.submit()">
                                                <!--<span class="input-group-text p-0" id="exampleInputdatePrepend"><button-->
                                                <!--class="d-flex align-items-center btn m-0 py-0 px-2 btn-warning"><i-->
                                                <!--class="bx bx-filter-alt fs-4xl"></i> </button></span>-->
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    @if(request()->active != 'false' && request()->verified != 'false')
                                    <div class="add-btns mb-0 justify-content-end">
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#exampleModalgrid"
                                           class="btn btn-warning role-add">
                                            <i class=" bx bx-plus"></i>
                                            اضافة {{ __('admin.vendor_s') }} جديد
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="">
                                <table id="vendor_example" class="dataTable row-border" style="width:100%">
                                    <thead class="d-none">
                                    <tr>
                                        <th>#</th>
                                        <th>#</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($stores as $key => $store)
                                        <tr>
                                            <td class="d-none">
                                                1
                                            </td>
                                            <td>
                                                <div class="card border-solid border">
                                                    <div class="card-body">
                                                        <div class="float-end created_at">
                                                            {{ __('admin.joined') }} {{ $store->created_at->format('M Y') }}
                                                        </div>
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
                                                                {!! $store->getBadge() !!}
                                                            </div>
                                                        </div>
                                                        <ul class="mt-4 mb-2 store-counters">
                                                            <li class="border-solid rounded border">
                                                                <div class="icon-text">
                                                                    <i class="bi bi-boxes"></i>
                                                                    منتجات
                                                                </div>
                                                                <div class="counter">
                                                                    {{ count($store->allProducts) }}
                                                                </div>
                                                            </li>
                                                            <li class="border-solid rounded border">
                                                                <div class="icon-text">
                                                                    <i class="mdi mdi-account-group"></i>
                                                                    زبائن
                                                                </div>
                                                                <div class="counter">
                                                                    325
                                                                </div>
                                                            </li>
                                                            <li class="border-solid rounded border">
                                                                <div class="icon-text">
                                                                    <i class="ph-files-fill"></i>
                                                                    طلبات
                                                                </div>
                                                                <div class="counter">
                                                                    {{ count($store->owner->orders) }}
                                                                </div>
                                                            </li>
                                                            <li class="border-solid rounded border">
                                                                <div class="icon-text">
                                                                    <i class="bi bi-box-seam"></i>
                                                                    ارجاع
                                                                </div>
                                                                <div class="counter">
                                                                    325
                                                                </div>
                                                            </li>
                                                            <li class="border-solid rounded border">
                                                                <div class="icon-text">
                                                                    <i class="bx bxs-user-pin"></i>
                                                                    متابعين
                                                                </div>
                                                                <div class="counter">
                                                                    {{ count($store->followers) }}
                                                                </div>
                                                            </li>
                                                            <li class="border-solid rounded border">
                                                                <div class="icon-text">
                                                                    <i class="bi bi-heart"></i>
                                                                    المفضلة
                                                                </div>
                                                                <div class="counter">
                                                                    {{ count($store->SellerFavorite) }}
                                                                </div>
                                                            </li>
                                                        </ul>
                                                        <div class="m-0 p-0 store-link text-end">
                                                            <a href="{{ route('vendors.show' , $store->id) }}">
                                                                زيارة ال{{ __('admin.vendor_s') }}
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
    <script src="{{ asset('admin') }}/assets/js/pages/form-wizard.init.js"></script>

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
    <script>
        $(document).ready(function() {
            $(".my_formm").validate({
                rules: {
                    name_ar: "required",
                    name_en: "required",
                    image: "required",
                    "category[]": "required"
                },
                messages: {
                    name_ar: "اسم البراند بالعربية مطلوب",
                    name_en: "اسم البراند بالانجليزية مطلوب",
                    image: "صورة البراند مطلوبة",
                    "category[]": "من فضلك اختر تصنيف"
                }
            });
        });
    </script>
@endsection
