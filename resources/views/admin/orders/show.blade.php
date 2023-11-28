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
                                                    if ($order['status_id'] == 1) {
                                                        $color = 'bg-warning';
                                                    } elseif ($order['status_id'] == 2) {
                                                        $color = 'bg-primary';
                                                    } elseif ($order['status_id'] == 3) {
                                                        $color = 'bg-info';
                                                    } elseif ($order['status_id'] == 4) {
                                                        $color = 'bg-warning';
                                                    } elseif ($order['status_id'] == 5) {
                                                        $color = 'bg-danger';
                                                    } elseif ($order['status_id'] == 6) {
                                                        $color = 'bg-success';
                                                    }elseif ($order['status_id'] == 7) {
                                                        $color = 'bg-warning';
                                                    }
                                                    $status = \App\Models\OrderStatus::find($order['status_id']);
                                                    ?>
                                                    <div class="badge {{ $color }}">
                                                        {{ $status['status_'.$lang] }}
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
                                                                    {{--                                                                    <div class="main_info">--}}
                                                                    {{--                                                                        <p>--}}
                                                                    {{--                                                                            مصاريف الشحن--}}
                                                                    {{--                                                                        </p>--}}
                                                                    {{--                                                                        <p>--}}
                                                                    {{--                                                                            28,100 LE--}}
                                                                    {{--                                                                        </p>--}}
                                                                    {{--                                                                    </div>--}}
                                                                    {{--                                                                    <ul>--}}
                                                                    {{--                                                                        <li>--}}
                                                                    {{--                                                                            2 منتج--}}
                                                                    {{--                                                                        </li>--}}
                                                                    {{--                                                                        <li>--}}
                                                                    {{--                                                                            2 منتج--}}
                                                                    {{--                                                                        </li>--}}
                                                                    {{--                                                                        <li>--}}
                                                                    {{--                                                                            2 منتج--}}
                                                                    {{--                                                                        </li>--}}
                                                                    {{--                                                                    </ul>--}}
                                                                </li>
                                                            </ul>
                                                        </div><!--end col-->
                                                    </div>
                                                </div>

                                                <div class="card-footer text-dark text-end">
                                                    قيمة الطلب : {{ $order['total_price'] }} LE
                                                    @if($order->status_id != 6 && $order->status_id != 3 && $order->status_id != 4)
                                                        <a href="{{ url('admin_panel/cancel_order' , $order->id) }}" class="btn btn-gradient w-100 mt-3">
                                                            الغاء الطلب
                                                        </a>
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
                                                    @if($order->status_id != 6 && $order->status_id != 3 && $order->status_id != 4)
                                                    <div class="btn-group position-absolute">
                                                        <button type="button"
                                                                class="btn dropdown-toggle"
                                                                data-bs-toggle="dropdown"
                                                                aria-haspopup="true"
                                                                aria-expanded="false">
                                                            <i class="bx bx-dots-horizontal-rounded"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li class="dropdown-item">
                                                                <a class="text-danger product_delete">حذف منتج</a>
                                                            </li>
                                                            <li class="dropdown-item">
                                                                <a class="text-danger store_delete">حذف متجر كامل</a>
                                                            </li>
                                                        </ul>
                                                    </div><!-- /btn-group -->
                                                    @endif
                                                </div>
                                                <div class="card-body pt-1 px-1">
                                                    <?php
                                                    $product_orders = \App\Models\OrderProduct::where('order_id', $order['id'])->get()->unique('store_id');
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
                                                                                class="store-input pe-1 from-check-success">
                                                                                <input class="form-check-input"
                                                                                       type="checkbox" value="">
                                                                            </div>
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
                                                                                    {{--                                                                                <ul class="product-tags">--}}
                                                                                    {{--                                                                                    <li>--}}
                                                                                    {{--                                                                                        5PCs/Package--}}
                                                                                    {{--                                                                                    </li>--}}
                                                                                    {{--                                                                                    <li>--}}
                                                                                    {{--                                                                                        5PCs/Package--}}
                                                                                    {{--                                                                                    </li>--}}
                                                                                    {{--                                                                                </ul>--}}
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
                                                                                <div class="col-md-12 reason">
                                                                                    <form
                                                                                        action="{{ url('admin_panel/delete_product_order' , $products_info) }}"
                                                                                        method="post">
                                                                                        {{ csrf_field() }}
                                                                                        <div>
                                                                                            <label for="reason"
                                                                                                   class="form-label">اذكر
                                                                                                سبب الحذف</label>
                                                                                            <input type="text"
                                                                                                   name="product_reason"
                                                                                                   class="form-control"
                                                                                                   id="reason">
                                                                                        </div>
                                                                                        <button type="submit"
                                                                                                class="btn btn-gradient float-end d-inline-block mt-1">
                                                                                            حذف
                                                                                        </button>
                                                                                    </form>
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
                                                            <div class="col-md-12 reason store_reason">
                                                                <form
                                                                    action="{{ url('admin_panel/delete_store_order' , $product_order['store_id']) }}"
                                                                    method="post">
                                                                    {{ csrf_field() }}
                                                                    <input type="number" hidden=""
                                                                           value="{{ $order['id'] }}" readonly
                                                                           name="order_id">
                                                                    <div>
                                                                        <label
                                                                            for="reason{{ $product_order['store_id'] }}"
                                                                            class="form-label">اذكر سبب الحذف
                                                                            للبائع</label>
                                                                        <input type="text" name="store_reason"
                                                                               class="form-control"
                                                                               id="reason{{ $product_order['store_id'] }}">
                                                                    </div>
                                                                    <button type="submit"
                                                                            class="btn btn-gradient float-end d-inline-block mt-1">
                                                                        حذف
                                                                    </button>
                                                                </form>
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
