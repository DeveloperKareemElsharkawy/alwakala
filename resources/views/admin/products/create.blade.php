<?php
$lang = app()->getLocale();
?>
@extends('admin.layout.master')
@section('pageTitle', 'المنتجات')
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
        .my_form .wrapper .upload-options label.error {
            background: transparent !important;
            height: 10% !important;
            top: 0;
            position: absolute;
        }
    </style>
@endsection
@section('backend-main')
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
                                    <p class="text-muted mb-0">{{ count($store->followers) . ' ' . trans('admin.followers') }}
                                        - {{ count($store->views) . ' ' . trans('admin.views') }}</p>
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
                                        <div class="col-lg-12">
                                            <div class="card">
                                                <div class="card-body pt-1 pb-0">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="card store-places inventory_card">
                                                                <div class="product_new_modal">
                                                                    <div class="card-body form-steps">
                                                                        <form method="post" class="my_form"
                                                                              action="{{ url('admin_panel/products/'. $store->id .'/create') }}"
                                                                              enctype="multipart/form-data">
                                                                            {{ csrf_field() }}
                                                                            <div class="row">
                                                                                <div class="col-md-12 col-12">
                                                                                    <div>
                                                                                        <label for="name1" class="form-label">الاسم للمنتج</label>
                                                                                        <input type="text" name="name" class="form-control" id="name1"
                                                                                               placeholder="">
                                                                                    </div>
                                                                                </div><!--end col-->
                                                                                <div class="col-md-12 col-12">
                                                                                    <div>
                                                                                        <label for="desc" class="form-label">نبذه عن المنتج </label>
                                                                                        <textarea rows="1" name="description" class="form-control"
                                                                                                  id="desc"
                                                                                                  placeholder=""></textarea>
                                                                                    </div>
                                                                                </div><!--end col-->
                                                                                <div class="col-md-12 col-12">
                                                                                    <div>
                                                                                        <label for="date" class="form-label">تاريخ
                                                                                            النشر</label>
                                                                                        <input onchange="TDate()" type="date" name="publish_app_at" class="form-control"
                                                                                               id="date" placeholder="">
                                                                                    </div>
                                                                                </div><!--end col-->
                                                                                <div class="col-md-12 col-12">
                                                                                    <div>
                                                                                        <label for="youtube" class="form-label">رابط ال
                                                                                            youtube</label>
                                                                                        <input type="url" class="form-control"
                                                                                               id="youtube" name="youtube_link" placeholder="">
                                                                                    </div>
                                                                                </div><!--end col-->
                                                                                <div class="col-md-6 col-12">
                                                                                    <div>
                                                                                        <label for="wholesale_price" class="form-label">سعر
                                                                                            الجملة</label>
                                                                                        <input type="number" class="form-control"
                                                                                               id="wholesale_price" name="price" placeholder="" required>
                                                                                    </div>
                                                                                </div><!--end col-->
                                                                                <div class="col-md-6 col-12">
                                                                                    <div>
                                                                                        <label for="discount" class="form-label">قيمة الخصم
                                                                                            الجملة</label>
                                                                                        <input type="number" class="form-control"
                                                                                               id="discount" name="discount" placeholder="">
                                                                                    </div>
                                                                                </div><!--end col-->
                                                                                <div class="col-md-12 col-12">
                                                                                    <div class="select-div custom-check">
                                                                                        <label class="form-label">نوع الخصم للجملة</label>
                                                                                        <div class="inputs_cutom">
                                                                                            <div class="me-1">
                                                                                                <input hidden="" name="discount_type" value="2"
                                                                                                       type="radio" id="myCheckbper2"/>
                                                                                                <label class="custom-label" for="myCheckbper2">
                                                                                                    مئوي
                                                                                                </label>
                                                                                            </div>
                                                                                            <div class="me-1">
                                                                                                <input hidden="" name="discount_type" value="1"
                                                                                                       type="radio" id="myCheckbper22"/>
                                                                                                <label class="custom-label" for="myCheckbper22">
                                                                                                    رقم صحيح
                                                                                                </label>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div><!--end col-->
                                                                                <div class="col-md-6 col-12">
                                                                                    <div>
                                                                                        <label for="consumer_old_price" class="form-label">سعر
                                                                                            القطاعي</label>
                                                                                        <input type="number" class="form-control"
                                                                                               id="consumer_old_price" name="consumer_old_price"
                                                                                               placeholder="" required>
                                                                                    </div>
                                                                                </div><!--end col-->
                                                                                <div class="col-md-6 col-12">
                                                                                    <div>
                                                                                        <label for="discount" class="form-label">قيمة الخصم
                                                                                            القطاعي</label>
                                                                                        <input type="number" class="form-control"
                                                                                               id="discount" name="discount" placeholder="">
                                                                                    </div>
                                                                                </div><!--end col-->
                                                                                <div class="col-md-12 col-12">
                                                                                    <div class="select-div custom-check">
                                                                                        <label class="form-label">نوع الخصم للقطاعي</label>
                                                                                        <div class="inputs_cutom">
                                                                                            <div class="me-1">
                                                                                                <input hidden="" name="consumer_price_discount_type"
                                                                                                       value="2" type="radio" id="myCheckbperr2"/>
                                                                                                <label class="custom-label" for="myCheckbperr2">
                                                                                                    مئوي
                                                                                                </label>
                                                                                            </div>
                                                                                            <div class="me-1">
                                                                                                <input hidden="" name="consumer_price_discount_type"
                                                                                                       value="1" type="radio" id="myCheckbperr22"/>
                                                                                                <label class="custom-label" for="myCheckbperr22">
                                                                                                    رقم صحيح
                                                                                                </label>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div><!--end col-->
                                                                                <div class="col-md-12">
                                                                                    <div class="row">
                                                                                        <div class="col-md-12 col-12">
                                                                                            <div class="select-div">
                                                                                                <label for="category_id" class="form-label">التصنيف</label>
                                                                                                <select name="category_id" class="form-select select-modal"
                                                                                                        id="category_id" required="">
                                                                                                    <option selected="" disabled="" value=""
                                                                                                            hidden></option>
                                                                                                    @foreach($categories as $category)
                                                                                                        <option
                                                                                                            value="{{ $category['id'] }}">{{ $category['name_'.$lang] }}</option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </div>
                                                                                        </div><!--end col-->

                                                                                        <div class="col-md-12 col-12">
                                                                                            <div class="select-div">
                                                                                                <label for="subcategory_id" class="form-label">التصنيف الفرعي </label>
                                                                                                <select class="form-select select-modal" name="subcategory_id"
                                                                                                        id="subcategory_id">
                                                                                                    <option
                                                                                                        value="" disabled selected>اختر التصنيف الرئيسي اولا</option>
                                                                                                </select>
                                                                                                @error('subcategory_id')
                                                                                                <span class="text-danger">{{ $message }}</span>
                                                                                                @enderror
                                                                                            </div>
                                                                                        </div><!--end col-->

                                                                                        <div class="col-md-12 col-12">
                                                                                            <div class="select-div">
                                                                                                <label for="subsubcategory_id" class="form-label">التصنيف الفرعي الثاني</label>
                                                                                                <select class="form-select select-modal" name="subsubcategory_id"
                                                                                                        id="subsubcategory_id">
                                                                                                    <option
                                                                                                        value="" disabled selected>اختر التصنيف الفرعي اولا</option>
                                                                                                </select>
                                                                                                @error('subsubcategory_id')
                                                                                                <span class="text-danger">{{ $message }}</span>
                                                                                                @enderror
                                                                                            </div>
                                                                                        </div><!--end col-->

                                                                                        <div class="col-md-12 col-12">
                                                                                            <div class="select-div">
                                                                                                <label for="brand_id" class="form-label">الماركة</label>
                                                                                                <select name="brand_id" class="form-select select-modal"
                                                                                                        id="brand_id" required="">
                                                                                                    <option selected="" disabled="" value=""
                                                                                                            hidden></option>
                                                                                                    @foreach($brands as $brand)
                                                                                                        <option
                                                                                                            value="{{ $brand['id'] }}">{{ $brand['name_'.$lang] }}</option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                                @error('brand_id')
                                                                                                <span class="text-danger">{{ $message }}</span>
                                                                                                @enderror
                                                                                            </div>
                                                                                        </div><!--end col-->
                                                                                        <div class="col-md-12 col-12">
                                                                                            <div class="select-div custom-check">
                                                                                                <label for="material_id"
                                                                                                       class="form-label">الخامات</label>
                                                                                                <div class="inputs_cutom">
                                                                                                    @foreach($materials as $key => $material)
                                                                                                        <div class="me-1">
                                                                                                            <input hidden="" name="material_id"
                                                                                                                   value="{{ $material['id'] }}" type="radio"
                                                                                                                   id="myCheckbox{{ $key }}"/>
                                                                                                            <label class="custom-label"
                                                                                                                   for="myCheckbox{{ $key }}">
                                                                                                                {{ $material['name_'.$lang] }}
                                                                                                            </label>
                                                                                                        </div>
                                                                                                    @endforeach
                                                                                                </div>
                                                                                            </div>
                                                                                        </div><!--end col-->

                                                                                        <div class="col-md-12 col-12">
                                                                                            <div class="select-div custom-check">
                                                                                                <label for="subsubcategory_id"
                                                                                                       class="form-label">طرق الشحن</label>
                                                                                                <div class="inputs_cutom">
                                                                                                    @foreach($shippings as $ship_key => $shipping)
                                                                                                        <div class="me-1">
                                                                                                            <input hidden="" name="shipping_method_id"
                                                                                                                   value="{{ $shipping['id'] }}" type="radio"
                                                                                                                   id="myCheckbo{{ $ship_key }}"/>
                                                                                                            <label class="custom-label"
                                                                                                                   for="myCheckbo{{ $ship_key }}">
                                                                                                                {{ $shipping['name_'.$lang] }}
                                                                                                            </label>
                                                                                                        </div>
                                                                                                    @endforeach
                                                                                                </div>
                                                                                            </div>
                                                                                        </div><!--end col-->
                                                                                        <div class="col-md-12 col-12">
                                                                                            <div class="select-div custom-check">
                                                                                                <label class="form-label">سياسات</label>
                                                                                                <div class="inputs_cutom">
                                                                                                    @foreach($policies as $policy_key => $policy)
                                                                                                        <div class="me-1">
                                                                                                            <input hidden="" name="policy_id"
                                                                                                                   value="{{ $policy['id'] }}" type="radio"
                                                                                                                   id="myCheckb{{ $policy_key }}"/>
                                                                                                            <label class="custom-label"
                                                                                                                   for="myCheckb{{ $policy_key }}">
                                                                                                                {{ $policy['name_'.$lang] }}
                                                                                                            </label>
                                                                                                        </div>
                                                                                                    @endforeach
                                                                                                </div>
                                                                                            </div>
                                                                                        </div><!--end col-->

                                                                                        <div class="col-md-12 col-12">
                                                                                            <div class="select-div custom-check">
                                                                                                <label class="form-label">شحن مجاني</label>
                                                                                                <div class="inputs_cutom">
                                                                                                    <div class="me-1">
                                                                                                        <input hidden="" name="free_shipping" value="true"
                                                                                                               type="radio" id="myCheckbsh1"/>
                                                                                                        <label class="custom-label" for="myCheckbsh1">
                                                                                                            نعم
                                                                                                        </label>
                                                                                                    </div>
                                                                                                    <div class="me-1">
                                                                                                        <input hidden="" name="free_shipping" value="false"
                                                                                                               type="radio" id="myCheckbsh2"/>
                                                                                                        <label class="custom-label" for="myCheckbsh2">
                                                                                                            لا
                                                                                                        </label>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div><!--end col-->
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="wrapper">
                                                                                <div class="row">
                                                                                    <div class="col-md-12">
                                                                                        <div class="row d-flex justify-content-center">
                                                                                            <div class="col-md-6">
                                                                                                <div class="box">
                                                                                                    <div class="image-text">
                                                                                                        <label for="barcode" class="form-label">BARCODE</label>
                                                                                                    </div>
                                                                                                    <div class="upload-options">
                                                                                                        <label>
                                                                                                            <input type="file" name="barcode"
                                                                                                                   class="image-upload"
                                                                                                                   accept="image/*"/>
                                                                                                        </label>
                                                                                                    </div>
                                                                                                    <div class="js--image-preview mt-3"></div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-12">
                                                                                        <div class="col-md-12 col-12">
                                                                                            <div>
                                                                                                <label for="barcode" class="form-label">BARCODE</label>
                                                                                                <input type="text" class="form-control" id="barcode" name="barcode_text"
                                                                                                       placeholder="">
                                                                                            </div>
                                                                                        </div><!--end col-->
                                                                                    </div>

                                                                                    <div class="col-md-12">
                                                                                        <div class="hstack gap-2 mt-5 justify-content-center">
                                                                                            <button id="finish" type="submit" class="btn btn-gradient">حفظ
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <!-- end tab content -->
                                                                        </form>
                                                                    </div>
                                                                </div><!-- end card-body -->
                                                            </div><!-- end card -->
                                                        </div><!--end col-->

                                                    </div>
                                                </div><!-- end card-body -->
                                            </div><!-- end card -->
                                        </div>
                                        <!-- end col -->
                                    </div>
                                    <!-- end row -->
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
    <!-- google maps api -->
    <script src="https://maps.google.com/maps/api/js?key=AIzaSyCtSAR45TFgZjOs4nBFFZnII-6mMHLfSYI"></script>
    <!-- form wizard init -->
    <script src="{{ asset('admin') }}/assets/js/pages/form-wizard.init.js"></script>
    <!-- gmaps plugins -->
    <script src="{{ asset('admin') }}/assets/libs/gmaps/gmaps.min.js"></script>
    <!-- gmaps init -->
    <script src="{{ asset('admin') }}/assets/js/pages/gmaps.init.js"></script>

    <script>
        var link = '<?php echo url('/'); ?>';
        $('#category_id').on('change', function () {
            var category_id = $(this).val();
            $.get(link + '/ajax_subcatgeories?category_id=' + category_id, function (data) {
                $('#subcategory_id').empty();
                $('#subcategory_id').append('<option value="" disabled selected>قم بإختيار  </option>');
                $.each(data, function (index, subcatObj) {
                    $('#subcategory_id').append('<option value="' + subcatObj.id + '">' + subcatObj.name + '</option>');
                });
            });
        });
    </script>
    <script>
        $('#subcategory_id').on('change', function () {
            var subcategory_id = $(this).val();
            $.get(link + '/ajax_subcatgeories?category_id=' + subcategory_id, function (data) {
                $('#subsubcategory_id').empty();
                $('#subsubcategory_id').append('<option value="" disabled selected>قم بإختيار  </option>');
                $.each(data, function (index, subcatObj) {
                    $('#subsubcategory_id').append('<option value="' + subcatObj.id + '">' + subcatObj.name + '</option>');
                });
            });
        });
    </script>
    <script src="{{ asset('admin') }}/assets/js/additional-methods.min.js"></script>
    <script src="{{ asset('admin') }}/assets/js/jquery.validate.min.js"></script>
    @if(isset($product_store['barcode_url']))
        <script>
            $(document).ready(function() {
                $(".my_form").validate({
                    rules: {
                        name: "required",
                        description: "required",
                        publish_app_at: "required",
                        price: "required",
                        consumer_old_price: "required",
                        brand_id: "required",
                        material_id: "required",
                        shipping_method_id: "required",
                        policy_id: "required",
                        free_shipping: "required",
                        barcode_text: "required",
                    },
                    messages: {
                        name: "مطلوب",
                        description: "مطلوب",
                        publish_app_at: "مطلوب",
                        price: "مطلوب",
                        consumer_old_price: "مطلوب",
                        brand_id: "مطلوب",
                        material_id: "مطلوب",
                        shipping_method_id: "مطلوب",
                        policy_id: "مطلوب",
                        free_shipping: "مطلوب",
                        barcode_text: "مطلوب",
                    }
                });
            });
        </script>
    @else
        <script>
            $(document).ready(function() {
                $(".my_form").validate({
                    rules: {
                        name: "required",
                        description: "required",
                        publish_app_at: "required",
                        price: "required",
                        consumer_old_price: "required",
                        brand_id: "required",
                        material_id: "required",
                        shipping_method_id: "required",
                        policy_id: "required",
                        free_shipping: "required",
                        barcode: "required",
                        barcode_text: "required",
                    },
                    messages: {
                        name: "مطلوب",
                        description: "مطلوب",
                        publish_app_at: "مطلوب",
                        price: "مطلوب",
                        consumer_old_price: "مطلوب",
                        brand_id: "مطلوب",
                        material_id: "مطلوب",
                        shipping_method_id: "مطلوب",
                        policy_id: "مطلوب",
                        free_shipping: "مطلوب",
                        barcode: "مطلوب",
                        barcode_text: "مطلوب",
                    }
                });
            });
        </script>
    @endif
    <script>
        function TDate() {
            var UserDate = document.getElementById("userdate").value;
            var ToDate = new Date();

            if (new Date(UserDate).getTime() <= ToDate.getTime()) {
                alert("The Date must be smaller or Equal to today date");
                return false;
            }
            return true;
        }
    </script>
@endsection
