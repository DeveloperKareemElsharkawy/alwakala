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
                        <div class="card-body pt-1 pb-0">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="card">
                                                <div class="card-body pt-1 pb-0">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="card store-places inventory_card">
                                                                <div class="card-body p-0">
                                                                    <div class="">
                                                                        <table id="vendor_places"
                                                                               class="dataTable row-border inventory_table"
                                                                               style="width:100%">
                                                                            <thead class="d-none">
                                                                            <tr>
                                                                                <th>#</th>
                                                                                <th>#</th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody id="portfolio">
                                                                            @foreach($products as $key => $product)
                                                                                    <?php
                                                                                    if ($product->activation == true && $product->reviewed == true) {
                                                                                        $kind = 'accepted';
                                                                                        $badge = 'تمت الموافقة';
                                                                                    }
                                                                                    if ($product->activation == true && $product->reviewed == false) {
                                                                                        $kind = 'pending';
                                                                                        $badge = 'تحت المراجعة';
                                                                                    }
                                                                                    if ($product->activation == false && $product->reviewed == false) {
                                                                                        $kind = 'rejected';
                                                                                        $badge = 'تم الرفض';
                                                                                    }
                                                                                    $product_store = \App\Models\ProductStore::where('product_id', $product->id)->pluck('id');
                                                                                    $price = \App\Models\ProductStore::where('product_id', $product->id)->first();
                                                                                    $color_ids = \App\Models\ProductStoreStock::whereIn('product_store_id', $product_store)->pluck('color_id');
                                                                                    $colors = \App\Models\Color::whereIn('id', $color_ids)->get();
                                                                                    ?>
                                                                                <tr class="tile scale-anm {{ $kind }} all image_class{{ $product->id }}">
                                                                                    <td class="d-none">
                                                                                        {{ $key + 1 }}
                                                                                    </td>
                                                                                    <td>
                                                                                        <div class="card border-0">
                                                                                            <div class="card-body p-2">
                                                                                                <div
                                                                                                    class="card_border">
                                                                                                    <!-- /btn-group -->
                                                                                                    <div class="offer">
                                                                                                        {{ $badge }}
                                                                                                    </div>
                                                                                                    <!-- /btn-group -->

                                                                                                    <div
                                                                                                        class="created_at">
                                                                                                        تم النشر :
                                                                                                        {{ $product->created_at->format('d M Y') }}
                                                                                                    </div>
                                                                                                    <div
                                                                                                        class="align-items-center image_info">
                                                                                                        <img
                                                                                                            class="map_image"
                                                                                                            src="{{ $product['image_url'] }}"/>
                                                                                                        <h6 style="max-width: 100%;overflow: hidden"> {{ $product->name }}</h6>
                                                                                                        <p style="width: 100%">{{ $product->category['name_'.$lang] }}</p>
                                                                                                        <ul class="color-list">
                                                                                                            @foreach($colors as $color)
                                                                                                                <li style="background: {{ $color['hex'] }};"></li>
                                                                                                            @endforeach
                                                                                                        </ul>
                                                                                                    </div>
                                                                                                    <ul class="delivery-info">
                                                                                                        @if(isset($price['net_price']) || $price['net_price'] != 0)
                                                                                                            <li>
                                                                                                                <strong>{{ $price['net_price'] }}
                                                                                                                    جنية</strong>
                                                                                                                <span
                                                                                                                    class="badge bg-danger-subtle"> {{ $price['discount_type'] == '2' ? $price['discount'] . ' % ' : $price['discount'] . ' جنية ' }}</span>
                                                                                                                <br>
                                                                                                                <span
                                                                                                                    class="old_price">
                                                                                        {{ $price['price'] }} جنية
                                                                                    </span>
                                                                                                            </li>
                                                                                                        @else
                                                                                                            <li>
                                                                                                                <strong>{{ $price['price'] }}
                                                                                                                    جنية</strong>
                                                                                                            </li>
                                                                                                        @endif
                                                                                                        <li>
                                                                                                            <i class="ph-chats-bold"></i>
                                                                                                            محادثات : 24
                                                                                                            الف
                                                                                                        </li>
                                                                                                        <li>
                                                                                                            <i class="ph-heart-fill"></i>
                                                                                                            مفضلة
                                                                                                            : {{ count($product->favourites) }}
                                                                                                        </li>
                                                                                                        <li>
                                                                                                            <i class="bi bi-bar-chart"></i>
                                                                                                            مبيعات
                                                                                                            : {{ count($product->orderProducts) }}
                                                                                                        </li>
                                                                                                        <li>
                                                                                                            <i class="ph-eye-thin"></i>
                                                                                                            مراجعات
                                                                                                            : {{ count($product->SellerRate) }}
                                                                                                        </li>
                                                                                                    </ul>

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
    <script>
        $('.nexttab').click(function () {
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

    @if(Session::get('type') == 'add_new')
        <script>
            $(document).ready(function () {
                var store_id = {{ Session::get('store_id') }};
                var product_id = {{ Session::get('product_id') }};
                $('#exampleModalgridattr').modal('show');
                $('.attr_body').html('<div class="col-md-12 col-xl-12 text-center loading"><i class="mdi mdi-loading fa-spin"></i></div>');
                var link = '<?php echo url('/'); ?>';
                $.ajax({
                    type: "GET",
                    url: link + "/admin_panel/product_attr/" + product_id + "/" + store_id,
                    success: function (data) {
                        $('.attr_body').html(data);
                    }
                });
            });
        </script>
    @endif
    <script>

        $(".add_attribute").on("click", function (e) {
            var store_id = $(this).attr('store_id');
            var product_id = $(this).attr('product_id');
            $('.attr_body').html('<div class="col-md-12 col-xl-12 text-center loading"><i class="mdi mdi-loading fa-spin"></i></div>');
            var link = '<?php echo url('/'); ?>';
            $.ajax({
                type: "GET",
                url: link + "/admin_panel/product_attr/" + product_id + "/" + store_id,
                success: function (data) {
                    $('.attr_body').html(data);
                }
            });
        });

    </script>

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

@endsection
