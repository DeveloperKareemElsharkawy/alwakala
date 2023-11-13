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
                                                                        <form id="wizard" method="post" action="{{ url('admin_panel/products/'. $product->id .'/edit') }}" enctype="multipart/form-data">
                                                                            {{ csrf_field() }}
                                                                            {{ method_field('PATCH') }}
                                                                            <div class="step-arrow-nav mb-4 d-none">
                                                                                <ul class="nav nav-pills custom-nav nav-justified" role="tablist">
                                                                                    <li class="nav-item" role="presentation">
                                                                                        <button class="nav-link active" id="steparrow-gen-info-tab"
                                                                                                data-bs-toggle="pill"
                                                                                                data-bs-target="#steparrow-gen-info" type="button" role="tab"
                                                                                                aria-controls="steparrow-gen-info" aria-selected="true"
                                                                                                data-position="0">
                                                                                            General
                                                                                        </button>
                                                                                    </li>
                                                                                    <li class="nav-item" role="presentation">
                                                                                        <button class="nav-link" id="steparrow-description-info-tab"
                                                                                                data-bs-toggle="pill"
                                                                                                data-bs-target="#steparrow-description-info" type="button" role="tab"
                                                                                                aria-controls="steparrow-description-info" aria-selected="false"
                                                                                                data-position="1" tabindex="-1">Description
                                                                                        </button>
                                                                                    </li>

                                                                                </ul>
                                                                            </div>

                                                                            <div class="tab-content">
                                                                                <div class="tab-pane fade active show" id="steparrow-gen-info" role="tabpanel"
                                                                                     aria-labelledby="steparrow-gen-info-tab">
                                                                                    <div class="row g-3">
                                                                                        <div class="col-md-12">
                                                                                            <p class="mb-0 text-center">
                                                                                            </p>
                                                                                        </div>
                                                                                        <div class="col-md-6">
                                                                                            <div class="row">
                                                                                                <div class="col-md-12 col-12">
                                                                                                    <div>
                                                                                                        <label for="name1" class="form-label">الاسم للمنتج</label>
                                                                                                        <input type="text" name="name" value="{{ $product['name'] }}" class="form-control" id="name1"
                                                                                                               placeholder="">

                                                                                                        <input hidden="" name="store_id" value="{{ $store['id'] }}">
                                                                                                    </div>
                                                                                                </div><!--end col-->
                                                                                                <div class="col-md-12 col-12">
                                                                                                    <div>
                                                                                                        <label for="desc" class="form-label">نبذه عن المنتج </label>
                                                                                                        <textarea rows="1" name="description"  class="form-control" id="desc"
                                                                                                                  placeholder="">{{ $product['description'] }}</textarea>
                                                                                                    </div>
                                                                                                </div><!--end col-->
                                                                                                <div class="col-md-12 col-12">
                                                                                                    <div>
                                                                                                        <label for="date" class="form-label">تاريخ
                                                                                                            النشر</label>
                                                                                                        <input type="date" name="publish_app_at" class="form-control"
                                                                                                               id="date" value="{{ $product_store['publish_app_at'] }}" placeholder="">
                                                                                                    </div>
                                                                                                </div><!--end col-->
                                                                                                <div class="col-md-12 col-12">
                                                                                                    <div>
                                                                                                        <label for="youtube" class="form-label">رابط ال
                                                                                                            youtube</label>
                                                                                                        <input type="url" class="form-control"
                                                                                                               id="youtube" value="{{ $product['youtube_link'] }}" name="youtube_link" placeholder="">
                                                                                                    </div>
                                                                                                </div><!--end col-->
                                                                                                <div class="col-md-6 col-12">
                                                                                                    <div>
                                                                                                        <label for="wholesale_price" class="form-label">سعر
                                                                                                            الجملة</label>
                                                                                                        <input type="number" class="form-control"
                                                                                                               id="wholesale_price" value="{{ $product_store['price'] }}" name="price" placeholder="">
                                                                                                    </div>
                                                                                                </div><!--end col-->
                                                                                                <div class="col-md-6 col-12">
                                                                                                    <div>
                                                                                                        <label for="discount" class="form-label">قيمة الخصم
                                                                                                            الجملة</label>
                                                                                                        <input type="number" value="{{ $product_store['discount'] }}" class="form-control"
                                                                                                               id="discount" name="discount" placeholder="">
                                                                                                    </div>
                                                                                                </div><!--end col-->
                                                                                                <div class="col-md-12 col-12">
                                                                                                    <div class="select-div custom-check">
                                                                                                        <label class="form-label">نوع الخصم للجملة</label>
                                                                                                        <div class="inputs_cutom">
                                                                                                            <div class="me-1">
                                                                                                                <input hidden="" {{ $product_store['discount_type'] == 2 ? 'checked' : '' }} name="discount_type" value="2" type="radio" id="myCheckbper2"/>
                                                                                                                <label class="custom-label" for="myCheckbper2">
                                                                                                                    مئوي
                                                                                                                </label>
                                                                                                            </div>
                                                                                                            <div class="me-1">
                                                                                                                <input hidden="" {{ $product_store['discount_type'] == 1 ? 'checked' : '' }} name="discount_type" value="1" type="radio" id="myCheckbper22"/>
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
                                                                                                               id="consumer_old_price" value="{{ $product_store['consumer_old_price'] }}" name="consumer_old_price" placeholder="">
                                                                                                    </div>
                                                                                                </div><!--end col-->
                                                                                                <div class="col-md-6 col-12">
                                                                                                    <div>
                                                                                                        <label for="discount" class="form-label">قيمة الخصم
                                                                                                            القطاعي</label>
                                                                                                        <input type="number" class="form-control"
                                                                                                               id="consumer_price_discount" value="{{ $product_store['consumer_price_discount'] }}" name="discount" placeholder="">
                                                                                                    </div>
                                                                                                </div><!--end col-->
                                                                                                <div class="col-md-12 col-12">
                                                                                                    <div class="select-div custom-check">
                                                                                                        <label class="form-label">نوع الخصم للقطاعي</label>
                                                                                                        <div class="inputs_cutom">
                                                                                                            <div class="me-1">
                                                                                                                <input {{ $product_store['consumer_price_discount_type'] == 2 ? 'checked' : '' }} hidden="" name="consumer_price_discount_type" value="2" type="radio" id="myCheckbperr2"/>
                                                                                                                <label class="custom-label" for="myCheckbperr2">
                                                                                                                    مئوي
                                                                                                                </label>
                                                                                                            </div>
                                                                                                            <div class="me-1">
                                                                                                                <input {{ $product_store['consumer_price_discount_type'] == 1 ? 'checked' : '' }} hidden="" name="consumer_price_discount_type" value="1" type="radio" id="myCheckbperr22"/>
                                                                                                                <label class="custom-label" for="myCheckbperr22">
                                                                                                                    رقم صحيح
                                                                                                                </label>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div><!--end col-->
                                                                                            </div>
                                                                                        </div>
                                                                                        <?php
                                                                                        $subsubcategory = \App\Models\Category::find($product['category_id']);
                                                                                        $subcategory = \App\Models\Category::find($subsubcategory['category_id']);
                                                                                        $categoryy = \App\Models\Category::find($subcategory['category_id']);
                                                                                        ?>
                                                                                        <div class="col-md-6">
                                                                                            <div class="row">
                                                                                                <div class="col-md-12 col-12">
                                                                                                    <div class="select-div">
                                                                                                        <label for="category_id" class="form-label">التصنيف</label>
                                                                                                        <select name="subsubcategory_id" class="form-select select-modal"
                                                                                                                id="category_id" required="">
                                                                                                            <option selected="" disabled="" value=""
                                                                                                                    hidden></option>
                                                                                                            @foreach($categories as $category)
                                                                                                                <option {{ $categoryy->id == $category['id'] }}
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
                                                                                                            <option value="{{ $subcategory['id'] }}">{{ $subcategory['name_'.$lang] }}</option>
                                                                                                        </select>
                                                                                                        @error('subcategory_id')
                                                                                                        <span class="text-danger">{{ $message }}</span>
                                                                                                        @enderror
                                                                                                    </div>
                                                                                                </div><!--end col-->

                                                                                                <div class="col-md-12 col-12">
                                                                                                    <div class="select-div">
                                                                                                        <label for="subsubcategory_id" class="form-label">التصنيف الفرعي الثاني</label>
                                                                                                        <select class="form-select select-modal" name="category_id"
                                                                                                                id="subsubcategory_id">

                                                                                                            <option value="{{ $subsubcategory['id'] }}">{{ $subsubcategory['name_'.$lang] }}</option>
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
                                                                                                            @if(!isset($product['brand_id']))
                                                                                                            <option selected="" disabled="" value=""
                                                                                                                    hidden></option>
                                                                                                            @endif
                                                                                                            @foreach($brands as $brand)
                                                                                                                <option {{ $product['brand_id'] == $brand['id'] ? 'selected' : '' }} value="{{ $brand['id'] }}">{{ $brand['name_'.$lang] }}</option>
                                                                                                            @endforeach
                                                                                                        </select>
                                                                                                    </div>
                                                                                                </div><!--end col-->
                                                                                                <div class="col-md-12 col-12">
                                                                                                    <div class="select-div custom-check">
                                                                                                        <label for="subsubcategory_id"
                                                                                                               class="form-label">الخامات</label>
                                                                                                        <div class="inputs_cutom">
                                                                                                            @foreach($materials as $key => $material)
                                                                                                                <div class="me-1">
                                                                                                                    <input hidden="" {{ $product['material_id'] == $material['id'] ? 'checked' : '' }} name="material_id" value="{{ $material['id'] }}" type="radio" id="myCheckbox{{ $key }}"/>
                                                                                                                    <label class="custom-label" for="myCheckbox{{ $key }}">
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
                                                                                                                    <input hidden="" {{ $product['shipping_method_id'] == $shipping['id'] ? 'checked' : '' }} name="shipping_method_id" value="{{ $shipping['id'] }}" type="radio" id="myCheckbo{{ $ship_key }}"/>
                                                                                                                    <label class="custom-label" for="myCheckbo{{ $ship_key }}">
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
                                                                                                                    <input hidden="" {{ $product['policy_id'] == $policy['id'] ? 'checked' : '' }} name="policy_id" value="{{ $policy['id'] }}" type="radio" id="myCheckb{{ $policy_key }}"/>
                                                                                                                    <label class="custom-label" for="myCheckb{{ $policy_key }}">
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
                                                                                                                <input hidden="" {{ $product_store['free_shipping'] == true ? 'checked' : '' }} name="free_shipping" value="true" type="radio" id="myCheckbsh1"/>
                                                                                                                <label class="custom-label" for="myCheckbsh1">
                                                                                                                    نعم
                                                                                                                </label>
                                                                                                            </div>
                                                                                                            <div class="me-1">
                                                                                                                <input hidden="" {{ $product_store['free_shipping'] == false ? 'checked' : '' }} name="free_shipping" value="false" type="radio" id="myCheckbsh2"/>
                                                                                                                <label class="custom-label" for="myCheckbsh2">
                                                                                                                    لا
                                                                                                                </label>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div><!--end col-->
                                                                                            </div>
                                                                                        </div>
                                                                                    </div><!--end row-->
                                                                                    <div class="hstack gap-2 mt-5 justify-content-center">
                                                                                        <button type="button" class="btn btn-gradient nexttab nexttab"
                                                                                                data-nexttab="steparrow-description-info-tab">التالي
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                                <!-- end tab pane -->

                                                                                <div class="tab-pane fade" id="steparrow-description-info" role="tabpanel"
                                                                                     aria-labelledby="steparrow-description-info-tab">
                                                                                    <div class="row">
                                                                                        <div class="col-md-12">
                                                                                            <p class="mb-0 text-center">
                                                                                            </p>
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
                                                                                                                    <input type="file" name="barcode" class="image-upload"
                                                                                                                           accept="image/*"/>
                                                                                                                </label>
                                                                                                            </div>
                                                                                                            <div @if(isset($product_store['barcode_url'])) style="background-image:url({{ $product_store['barcode_url'] }})" @endif class="js--image-preview mt-3 border border-1"></div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-md-12">
                                                                                                <div class="col-md-12 col-12">
                                                                                                    <div>
                                                                                                        <label for="barcode" class="form-label">BARCODE</label>
                                                                                                        <input type="text" name="barcode_text" value="{{ $product_store['barcode_text'] }}" class="form-control" id="barcode"
                                                                                                               placeholder="">
                                                                                                    </div>
                                                                                                </div><!--end col-->
                                                                                            </div>

                                                                                            <div class="col-md-12">
                                                                                                <div class="hstack gap-2 mt-5 justify-content-center">
                                                                                                    <button type="button"
                                                                                                            class="btn btn-light btn-label previestab"
                                                                                                            data-previous="steparrow-description-info-tab"><i
                                                                                                            class="ri-arrow-left-line label-icon align-middle fs-lg me-2"></i>
                                                                                                        الرجوع
                                                                                                    </button>
                                                                                                    <button id="finish" type="submit" class="btn btn-gradient">حفظ
                                                                                                    </button>
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
@endsection
