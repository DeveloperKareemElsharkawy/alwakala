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
                                    <div class="row">
                                        <div class="col-lg-12 statics">
                                            <div class="row">
                                                <div class="col-xxl-2 col-md-4">
                                                    <div class="card text-center">
                                                        <div class="card-body text-center">

                                                            <h4 class="mb-4"><span class="counter-value" data-target="{{ count($store->allProducts) }}">{{ count($store->allProducts) }}</span> </h4>

                                                            <p class="fw-medium text-uppercase mb-0">عدد المنتجات</p>
                                                        </div>
                                                    </div>
                                                </div><!--end col-->
                                                <div class="col-xxl-2 col-md-4">
                                                    <div class="card text-center">
                                                        <div class="card-body text-center">

                                                            <h4 class="mb-4"><span class="counter-value" data-target="{{ count($store->owner->orders) }}">{{ count($store->owner->orders) }}</span>  </h4>

                                                            <p class="fw-medium text-uppercase mb-0">عدد الطلبات</p>
                                                        </div>
                                                    </div>
                                                </div><!--end col-->
                                                <div class="col-xxl-2 col-md-4">
                                                    <div class="card text-center">
                                                        <div class="card-body text-center">
                                                            <?php $orders = \App\Models\OrderProduct::where('store_id',$store['id'])->get()->unique('order_id'); ?>
                                                            <h4 class="mb-4"><span class="counter-value" data-target="{{ count($orders) }}">{{ count($orders) }}</span> </h4>

                                                            <p class="fw-medium text-uppercase mb-0">عدد المبيعات</p>
                                                        </div>
                                                    </div>
                                                </div><!--end col-->
                                                <div class="col-xxl-2 col-md-4">
                                                    <div class="card text-center">
                                                        <div class="card-body text-center">
                                                            <h4 class="mb-4"><span class="counter-value" data-target="258">258</span> الف </h4>

                                                            <p class="fw-medium text-uppercase mb-0">عمليات ارجاع</p>
                                                        </div>
                                                    </div>
                                                </div><!--end col-->
                                                <div class="col-xxl-2 col-md-4">
                                                    <div class="card text-center">
                                                        <div class="card-body text-center">

                                                            <h4 class="mb-4"><span class="counter-value" data-target="258">258</span> الف </h4>

                                                            <p class="fw-medium text-uppercase mb-0">الارباح</p>
                                                        </div>
                                                    </div>
                                                </div><!--end col-->
                                                <div class="col-xxl-2 col-md-4">
                                                    <div class="card text-center">
                                                        <div class="card-body text-center">

                                                            <h4 class="mb-4"><span class="counter-value" data-target="258">258</span> الف </h4>

                                                            <p class="fw-medium text-uppercase mb-0">عدد المدخلات</p>
                                                        </div>
                                                    </div>
                                                </div><!--end col-->
                                            </div>
                                        </div><!--end col-->
                                        <form method="post" action="{{ route('vendors.update' , $store->id) }}" enctype="multipart/form-data">
                                            {{ csrf_field() }}
                                            {{ method_field('PATCH') }}
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="card user-info">
                                                                <div class="card-header">
                                                                    <h6 class="card-title mb-0">معلومات المتجر</h6>
                                                                </div>
                                                                <div class="card-body pt-1 px-0">
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
                                                                                         style="background-image: url({{ $store->cover_url }});">
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
                                                                                         style="background-image: url({{ $store->image_url }});">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row px-3">
                                                                            <div class="mb-2 col-md-12 col-12">
                                                                                <div>
                                                                                    <label for="store_name" class="form-label">اسم المتجر</label>
                                                                                    <input type="text" class="form-control" id="store_name" name="store_name" value="{{ $store->name }}" placeholder="">
                                                                                </div>
                                                                            </div><!--end col-->
                                                                            <div class="mb-2 col-md-12 col-12">
                                                                                <div>
                                                                                    <label for="address" class="form-label">العنوان</label>
                                                                                    <input type="text" class="form-control" id="address" name="store_address" value="{{ $store->address }}" placeholder="">
                                                                                </div>
                                                                            </div><!--end col-->
                                                                            <div class="mb-2 col-md-12 col-12">
                                                                                <div>
                                                                                    <label for="phonee" class="form-label">رقم الهاتف</label>
                                                                                    <input type="tel" minlength="11" maxlength="11" name="store_phone" value="{{ $store->mobile }}" class="form-control  password-input"
                                                                                           id="phonee" placeholder="" oninput="this.value = this.value.replace(/[^0-9+()]/g, '');" pattern=".{11,11}" required>
                                                                                </div>
                                                                            </div><!--end col-->
                                                                            <div class="mb-2 col-md-12 col-12">
                                                                                <div class="input-group has-validation select-div">
                                                                                    <label for="estate_id" class="form-label">الولاية</label>
                                                                                    <select class="form-select p-2 select" id="estate_id" name="city_id" required="">
                                                                                        <option selected="" disabled="" value=""></option>
                                                                                        @foreach($cities as $city)
                                                                                            <option {{ $city->id == $store->city_id ? 'selected' : '' }} value="{{ $city->id }}">{{ $city['name_'.$lang] }}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="mb-2 col-md-12 col-12">
                                                                                <div class="input-group has-validation select-div">
                                                                                    <label for="category_id" class="form-label">التصنيف</label>
                                                                                    <?php
                                                                                        $categoryy = \App\Models\CategoryStore::where('store_id', $store->id)->first();
                                                                                    ?>
                                                                                    <select class="form-select p-2 select" id="category_id" name="category_id" required="">
                                                                                        <option selected="" disabled="" value=""></option>
                                                                                        @foreach($categories as $category)
                                                                                            <option {{ $category->id == $categoryy->category_id ? 'selected' : '' }} value="{{ $category->id }}">{{ $category['name_'.$lang] }}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            <div class="mb-2 col-md-12 col-12">
                                                                                <div class="input-group has-validation select-div">
                                                                                    <label for="category_id" class="form-label">الموقع على الخريطة</label>

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div style="height: 200px" id="gmaps-markers" class="gmaps"></div>
                                                                </div>
                                                            </div>
                                                        </div><!--end col-->
                                                    </div><!--end row-->

                                                </div>
                                                <div class="col-md-8">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="card user-info">
                                                                <div class="card-header">
                                                                    <h6 class="card-title mb-0">معلومات المالك</h6>
                                                                </div>
                                                                <div class="card-body pt-1">
                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            <div style="display: flex;justify-content: center" class="avatar-upload">
                                                                                <div class="avatar-edit">
                                                                                    <input type='file' id="imageUpload" name="image" class="imageUpload" accept=".png, .jpg, .jpeg"/>
                                                                                    <label for="imageUpload">
                                                                                        <i class="bx bxs-plus-circle"></i>
                                                                                    </label>
                                                                                </div>
                                                                                <div style="width: 100px;height: 100px" class="avatar-preview">
                                                                                    <div class="imagePreview" id="imagePreview"
                                                                                         style="background-image: url({{ $store->user->image_url }});">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-8 col-12">
                                                                            <div class="mt-md-5">
                                                                                <label for="name1" class="form-label">الاسم كامل</label>
                                                                                <input type="text" name="user_name" value="{{ $store->user->name }}" class="form-control" id="name1" placeholder="">
                                                                            </div>
                                                                        </div><!--end col-->
                                                                        <div class="col-md-6 col-12">
                                                                            <div>
                                                                                <label for="email" class="form-label">البريد الالكتروني</label>
                                                                                <input type="email" name="user_email" value="{{ $store->user->email }}" class="form-control" id="email" placeholder="">
                                                                            </div>
                                                                        </div><!--end col-->
                                                                        <div class="col-md-6 col-12">
                                                                            <div>
                                                                                <label for="phone" class="form-label">رقم الهاتف</label>
                                                                                <input type="tel" name="user_phone" minlength="11" value="{{ $store->user->mobile }}" maxlength="11" class="form-control  password-input"
                                                                                       id="phone" placeholder=""
                                                                                       oninput="this.value = this.value.replace(/[^0-9+()]/g, '');" pattern=".{11,11}"
                                                                                       required>
                                                                            </div>
                                                                        </div><!--end col-->
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div><!--end col-->
                                                        <div class="col-lg-12">
                                                            <div class="card user-info documents">
                                                                <div class="card-header">
                                                                    <h6 class="card-title mb-0">الوثائق</h6>
                                                                </div>
                                                                <div class="card-body pt-1">
                                                                    <div class="wrapper">
                                                                        <div class="row">
                                                                            <div class="col-md-4">
                                                                                <div class="box">
                                                                                    <div class="image-text">
                                                                                        <h6>الهوية الوطنية</h6>
                                                                                        <p>
                                                                                            يجب ان تكون الصورة ذو امتداد (.jpg - .png - .jpeg)
                                                                                        </p>
                                                                                    </div>
                                                                                    <div class="upload-options">
                                                                                        <label>
                                                                                            <p><i class="bx bxs-plus-circle"></i> </p>
                                                                                            <input type="file" class="image-upload" accept="image/*" />
                                                                                        </label>
                                                                                    </div>
                                                                                    <div class="js--image-preview mt-3"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <div class="box">
                                                                                    <div class="image-text">
                                                                                        <div class="image-text">
                                                                                            <h6> بطاقه نصية</h6>
                                                                                            <p>
                                                                                                يجب ان تكون الصورة ذو امتداد (.jpg - .png - .jpeg)
                                                                                            </p>
                                                                                        </div>

                                                                                    </div>
                                                                                    <div class="upload-options">
                                                                                        <label>
                                                                                            <p><i class="bx bxs-plus-circle"></i> </p>
                                                                                            <input type="file" class="image-upload" accept="image/*" />
                                                                                        </label>
                                                                                    </div>
                                                                                    <div class="js--image-preview mt-3"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <div class="box">
                                                                                    <div class="image-text">
                                                                                        <div class="image-text">
                                                                                            <h6> السجل الضريبي</h6>
                                                                                            <p>
                                                                                                يجب ان تكون الصورة ذو امتداد (.jpg - .png - .jpeg)
                                                                                            </p>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="upload-options">
                                                                                        <label>
                                                                                            <p><i class="bx bxs-plus-circle"></i> </p>
                                                                                            <input type="file" class="image-upload" accept="image/*" />
                                                                                        </label>
                                                                                    </div>
                                                                                    <div class="js--image-preview mt-3"></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div><!--end col-->
                                                    </div><!--end row-->
                                                </div>
                                            </div>
                                            <input type="submit" hidden />
                                        </form>
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
@endsection
