@extends('admin.layout.master')
@section('pageTitle', 'عرض العملية')
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
    <div class="page-content show_vendor purchases_page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body pt-1 pb-0">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card user-info">
                                                <div class="card-header">
                                                    <h6 class="card-title mb-0">
                                                        ملخص الطلب
                                                    </h6>
                                                </div>
                                                <div class="card-body pt-1">
                                                    <div class="row">
                                                        <div class="col-md-6 col-12">
                                                            <div>
                                                                <label for="order_number" class="form-label">
                                                                    صاحب الطلب</label>
                                                                <div class="input-group location_price">
                                                                       <span class="input-group-text copy">
                                                                           <i class="mdi mdi-content-copy"></i>
                                                                       </span>
                                                                    <input type="text" class="form-control"
                                                                           id="order_number"
                                                                           value="{{ isset($order['user']['store']) ? $order['user']['store']['name'] : $order['user']['name'] }}"
                                                                           readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12 col-12">
                                                            <ul class="singleorder-list">
                                                                <li>
                                                                    <div class="main_info">
                                                                        <p>وقت الطلب</p>
                                                                        <p style="direction: ltr">

                                                                            {{ $order['created_at']->format('d M Y') }}
                                                                            <span>
                                                                              {{ $order['created_at']->format('H:i:s') }}
                                                                              </span>
                                                                        </p>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <div class="main_info">
                                                                        <p>طريقة الدفع</p>
                                                                        <p>
                                                                            {{ isset($order['payment_method']) ? $order['payment_method']['nema_'.$lang] : '-----' }}
                                                                        </p>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <div class="main_info">
                                                                        <p>محتوى الطلب</p>
                                                                        <?php
                                                                        $product_count = \App\Models\CartItem::where('cart_id', $order['id'])->get()->sum('quantity');
                                                                        $store_count = \App\Models\CartItem::where('cart_id', $order['id'])->get()->unique('store_id');
                                                                        ?>
                                                                        <p>
                                                                            {{ $product_count }} منتجات من
                                                                            {{ count($store_count) }} متاجر
                                                                        </p>
                                                                    </div>
                                                                    <ul>
                                                                        @foreach($order['items'] as $product)
                                                                            <li>
                                                                                {{ $product['quantity'] }}  {{ $product['product']['name'] }}
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </li>
                                                            </ul>
                                                        </div><!--end col-->
                                                    </div>
                                                </div>

                                                <div class="card-footer text-dark text-end">
                                                    <?php
                                                    $proo_total = \App\Models\CartItem::where('cart_id' , $order['id'])->pluck('product_store_id');
                                                    $totall = \App\Models\ProductStore::whereIn('id' , $proo_total)->sum('price');
                                                    ?>
                                                    قيمة الطلب : {{ $totall }} LE
                                                    <a href="{{ url('admin_panel/carts_alert' , $order->id) }}" class="btn btn-gradient w-100 mt-3">
                                                        ارسال تنبية
                                                    </a>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card user-info order-card">
                                                <div class="card-header">
                                                    <h6 class="card-title mb-0">
                                                        محتويات الطلب
                                                    </h6>
                                                </div>
                                                <div class="card-body pt-1 px-1">
                                                    <?php
                                                    $product_orders = \App\Models\CartItem::where('cart_id', $order['id'])->get()->unique('store_id');
                                                    ?>
                                                    @foreach($product_orders as $key => $product_order)
                                                        <div class="card store_card">
                                                            <div
                                                                class="card-header d-md-flex justify-content-between align-items-center">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-start">
                                                                    <div class="">
                                                                        <div class="d-flex align-items-center">
                                                                            <div
                                                                                class="border rounded-circle flex-shrink-0 position-relative">
                                                                                <img
                                                                                    src="{{ $product_order['store']['image_url'] }}"
                                                                                    alt=""
                                                                                    class="avatar-sm rounded-circle">
                                                                            </div>
                                                                            <div
                                                                                class="flex-grow-1 ms-2 text-start store-info">
                                                                                <h5 class="d-flex align-items-center mb-0">
                                                                                    {{ $product_order['store']['name'] }}
                                                                                    <i class="ri-checkbox-circle-fill px-2"></i>
                                                                                </h5>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="flex-shrink-0">
                                                                        <ul class="list-inline card-toolbar-menu d-flex align-items-center mb-0">
                                                                            <li class="list-inline-item">
                                                                                <a class="align-middle minimize-card"
                                                                                   data-bs-toggle="collapse"
                                                                                   href="#collapseExample{{ $key }}"
                                                                                   role="button" aria-expanded="false"
                                                                                   aria-controls="collapseExample2">
                                                                                    <i class="ri-arrow-up-s-line align-middle plus"></i>
                                                                                    <i class="ri-arrow-down-s-line align-middle minus"></i>
                                                                                </a>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                                <div class="">
                                                                    <p class="text-muted mb-0"> {{ count($product_order['store']['followers']) }}
                                                                        الف متابع -
                                                                        {{ count($product_order['store']['views']) }}
                                                                        زيارة </p>
                                                                </div>
                                                            </div>
                                                            <div class="card-body py-2 pe-1 collapse show"
                                                                 id="collapseExample{{ $key }}">
                                                                <ul class="store-products">
                                                                        <?php
                                                                        $products = \App\Models\CartItem::where('store_id', $product_order['store_id'])->where('cart_id', $order->id)->get();
                                                                        $pro_total = \App\Models\CartItem::where('cart_id' , $order['id'])->where('user_id' , $order['user_id'])->where('store_id',$product_order['store_id'])->pluck('product_store_id');
                                                                        $products_price = \App\Models\ProductStore::whereIn('id' , $pro_total)->sum('price');
                                                                        ?>
                                                                    @foreach($products as $product)
                                                                            <?php
                                                                            $products_info = \App\Models\CartItem::where('product_id', $product['product']['id'])->where('store_id', $product_order['store_id'])->where('cart_id', $order->id)->first();
                                                                            ?>
                                                                        <li>
                                                                            <div class="product-input pe-1">
                                                                                <input class="form-check-input"
                                                                                       type="checkbox" value="">
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-md-4">
                                                                                    <img
                                                                                        src="{{ $product['product']['image_url'] }}"
                                                                                        alt=""/>
                                                                                </div>
                                                                                <div class="col-md-8">
                                                                                    <h5 class="product-name">
                                                                                        {{ $product['product']['name'] }}
                                                                                    </h5>

                                                                                    <div
                                                                                        class="product-qty-info float-end">
                                                                                        <div
                                                                                            class="pro-price">{{ $products_info['total_price'] }}
                                                                                            LE
                                                                                        </div>
                                                                                        <div class="pro-qty">كمية
                                                                                            : {{ $products_info['quantity'] }}</div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </li>
                                                                    @endforeach

                                                                </ul>
                                                            </div>
                                                            <div class="card-footer text-dark text-end">
                                                                <small>
                                                                    السعر الكلي ({{ $order['items']->sum('quantity') }}
                                                                    Item/s) :
                                                                </small>
                                                                {{ $products_price }} LE
                                                            </div>
                                                        </div>
                                                        <!--end store card-->
                                                    @endforeach
                                                </div>
                                            </div>
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

    <footer class="footer d-none">
    </footer>

@endsection
@section('backend-footer')
    <script src="{{ asset('admin') }}/assets/libs/simplebar/simplebar.min.js"></script>

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
