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
    <div class="page-content show_vendor purchases_page">
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
                                        <div class="col-md-12">
                                            <div class="card user-info">
                                                <div class="card-header d-flex justify-content-between">
                                                    <h6 class="card-title mb-0">معلومات الطلب</h6>
                                                    <?php
                                                    $product_orders_status = \App\Models\OrderProduct::where('order_id', $order['id'])->first();

                                                    if ($product_orders_status['status_id'] == 1) {
                                                        $color = 'bg-warning';
                                                    } elseif ($product_orders_status['status_id'] == 2) {
                                                        $color = 'bg-primary';
                                                    } elseif ($product_orders_status['status_id'] == 3) {
                                                        $color = 'bg-info';
                                                    } elseif ($product_orders_status['status_id'] == 4) {
                                                        $color = 'bg-warning';
                                                    } elseif ($product_orders_status['status_id'] == 5) {
                                                        $color = 'bg-danger';
                                                    } elseif ($product_orders_status['status_id'] == 6) {
                                                        $color = 'bg-success';
                                                    }

                                                    $statue = \App\Models\OrderStatus::find($product_orders_status['status_id']);
                                                    ?>
                                                    <div class="badge {{ $color }}">
                                                        {{ $statue['status_'.$lang] }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
                                                                <label for="order_number" class="form-label">رقم
                                                                    الطلب</label>
                                                                <div class="input-group location_price">
                                                                       <span class="input-group-text copy">
                                                                           <i class="mdi mdi-content-copy"></i>
                                                                       </span>
                                                                    <input type="text" class="form-control"
                                                                           id="order_number"
                                                                           value="{{ $order['number'] }}"
                                                                           readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-12">
                                                            <div>
                                                                <label for="invoice" class="form-label">رقم
                                                                    الفاتورة</label>
                                                                <input type="text" class="form-control"
                                                                       id="invoice" value="{{ $order['number'] }}"
                                                                       readonly>
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
                                                                        $product_count = \App\Models\OrderProduct::where('order_id', $order['id'])->get()->sum('quantity');
                                                                        $store_count = \App\Models\OrderProduct::where('order_id', $order['id'])->get()->unique('store_id');
                                                                        ?>
                                                                        <p>
                                                                            {{ $product_count }} منتجات من
                                                                            {{ count($store_count) }} متاجر
                                                                        </p>
                                                                    </div>
                                                                    <ul>
                                                                        @foreach($order['items'] as $product)
                                                                            <li>
                                                                                {{ $product['quantity'] }}  {{ $product['productt']['name'] }}
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </li>
                                                                <li>
                                                                </li>
                                                            </ul>
                                                        </div><!--end col-->
                                                    </div>
                                                </div>

                                                <div class="card-footer text-dark text-end">
                                                    قيمة الطلب : {{ $order['total_price'] }} LE
                                                    @if($product_orders_status->status_id != 6 && $product_orders_status->status_id != 5 && $product_orders_status->status_id != 4)
                                                        <?php
                                                            $iddd = \App\Models\OrderProduct::where('order_id', $order['id'])->whereHas('order', function ($query) use($store) {
                                                                 $query->where('user_id', $store['user_id']);
                                                            })->first();
                                                            ?>
                                                        <form method="post" action="{{ url('admin_panel/purchase_status/'.$order['id'] .'/'.$iddd['store_id']) }}">
                                                            {{ csrf_field() }}
                                                            <div style="text-align: start" class="col-md-12 col-12">
                                                                <div class="select-div">
                                                                    <label for="status_id" class="form-label">حالة الطلب</label>
                                                                    <select name="status_id" class="form-select select-modal"
                                                                            id="status_id" required="">
                                                                        @foreach($status as $brand)
                                                                            <option {{ $statue['id'] == $brand['id'] ? 'selected' : '' }} value="{{ $brand['id'] }}">{{ $brand['status_'.$lang] }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div><!--end col-->
                                                            <button type="submit" class="btn btn-gradient w-100 mt-3">
                                                                حفظ
                                                            </button>
                                                        </form>
                                                    @endif
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
                                                    $product_orders = \App\Models\OrderProduct::where('order_id', $order['id'])->get()->unique('store_id');
                                                    $user = $order['user'];

                                                    ?>
                                                    @foreach($product_orders as $key => $product_order)
                                                            <?php
                                                            $user_store = \App\Models\Store::where('id' , $product_order['store_id'])->first();
                                                            ?>
                                                        <div class="card store_card">
                                                            <div
                                                                class="card-header d-md-flex justify-content-between align-items-center">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-start">
                                                                    <div class="">
                                                                        <div class="d-flex align-items-center">
                                                                            <div
                                                                                class="store-input pe-1 from-check-success">
                                                                                <input class="form-check-input"
                                                                                       type="checkbox" value="">
                                                                            </div>
                                                                            <div
                                                                                class="border rounded-circle flex-shrink-0 position-relative">
                                                                                <img
                                                                                    src="{{ isset($user_store) ? $user_store['image_url'] : '' }}"
                                                                                    alt=""
                                                                                    class="avatar-sm rounded-circle">
                                                                            </div>
                                                                            <div
                                                                                class="flex-grow-1 ms-2 text-start store-info">
                                                                                <h5 class="d-flex align-items-center mb-0">
                                                                                    {{ isset($user_store) ? $user_store['name'] : '' }}
                                                                                    <i class="ri-checkbox-circle-fill px-2"></i>
                                                                                </h5>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @if(isset($user_store))
                                                                <div class="">
                                                                    <p class="text-muted mb-0"> {{ count($user_store['followers']) }}
                                                                        الف متابع -
                                                                        {{ count($user_store['views']) }}
                                                                        زيارة </p>
                                                                </div>
                                                                @endif
                                                            </div>
                                                            <div class="card-body py-2 pe-1 collapse show"
                                                                 id="collapseExample{{ $key }}">
                                                                <ul class="store-products">
                                                                        <?php
                                                                        $products = \App\Models\OrderProduct::where('store_id', $product_order['store_id'])->where('order_id', $order->id)->get();
                                                                        $products_price = \App\Models\OrderProduct::where('store_id', $product_order['store_id'])->where('order_id', $order->id)->sum('total_price');
                                                                        ?>
                                                                    @foreach($products as $product)
                                                                            <?php
                                                                            $products_info = \App\Models\OrderProduct::where('product_id', $product['productt']['id'])->where('store_id', $product_order['store_id'])->where('order_id', $order->id)->first();
                                                                            ?>
                                                                        <li>
                                                                            <div class="product-input pe-1">
                                                                                <input class="form-check-input"
                                                                                       type="checkbox" value="">
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-md-4">
                                                                                    <img
                                                                                        src="{{ $product['productt']['image_url'] }}"
                                                                                        alt=""/>
                                                                                </div>
                                                                                <div class="col-md-8">
                                                                                    <h5 class="product-name">
                                                                                        {{ $product['productt']['name'] }}
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
                                                                    السعر الكلي ({{ $products->sum('purchased_item_count') }}
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
