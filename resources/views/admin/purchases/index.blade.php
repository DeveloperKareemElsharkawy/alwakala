@extends('admin.layout.master')
@section('pageTitle', 'المبيعات')
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
                                    <div class="card store-places inventory_card">
                                        <div class="card-body p-0">
                                            <div class="add-btns ms-2 mb-2 mt-md-1 mt-3 justify-content-between">
{{--                                                <form action="{{ url('admin_panel/purchases',$store['id']) }}" method="get"--}}
{{--                                                      class="products_type">--}}
{{--                                                    <div class="form-check form-radio-dark">--}}
{{--                                                        <input class="form-check-input" type="radio"--}}
{{--                                                               name="store_type" onchange="this.form.submit()"--}}
{{--                                                               id="formradioRight12"--}}
{{--                                                               {{ $store_type == 2 ? 'checked' : '' }} value="2">--}}
{{--                                                        <label class="form-check-label" for="formradioRight12">--}}
{{--                                                            بائع تجزئة--}}
{{--                                                        </label>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="form-check form-radio-dark">--}}
{{--                                                        <input class="form-check-input" type="radio"--}}
{{--                                                               {{ $store_type == 1 ? 'checked' : '' }} onchange="this.form.submit()"--}}
{{--                                                               name="store_type" id="formradioRight1" value="1">--}}
{{--                                                        <label class="form-check-label" for="formradioRight1">--}}
{{--                                                            بائع قطاعي--}}
{{--                                                        </label>--}}
{{--                                                    </div>--}}
{{--                                                </form>--}}
                                            </div>
                                            <div class="">
                                                <div class="tab-content  text-muted">
                                                    <div class="tab-pane active" id="base-justified-home"
                                                         role="tabpanel">

                                                        <div class="">
                                                            <div class="toolbar">
                                                                <button class="btn fil-cat selected me-1" href=""
                                                                        data-rel="all">
                                                                    عرض الجميع
                                                                </button>
                                                                @foreach($order_types as $key => $order_type)
                                                                        <?php
                                                                        if ($order_type['id'] == 1) {
                                                                            $color = 'text-warning';
                                                                        } elseif ($order_type['id'] == 2) {
                                                                            $color = 'text-primary';
                                                                        } elseif ($order_type['id'] == 3) {
                                                                            $color = 'text-info';
                                                                        } elseif ($order_type['id'] == 4) {
                                                                            $color = 'text-warning';
                                                                        } elseif ($order_type['id'] == 5) {
                                                                            $color = 'text-danger';
                                                                        } elseif ($order_type['id'] == 6) {
                                                                            $color = 'text-success';
                                                                        }
                                                                        ?>
                                                                    <button class="btn fil-cat {{ $color }} me-1"
                                                                            href=""
                                                                            data-rel="{{ $order_type['status_en']  }}">
                                                                        {{ $order_type['status_'.$lang]  }}
                                                                    </button>
                                                                @endforeach
                                                            </div>

                                                            <table
                                                                class="purchases_table dataTable row-border"
                                                                style="width:100%">
                                                                <thead class="d-none">
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>#</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody id="portfolio">
                                                                @foreach($orders as $order_key => $order)
                                                                        <?php
                                                                        $product_orders_status = \App\Models\OrderProduct::where('order_id', $order['id'])->first();

                                                                        $status = \App\Models\OrderStatus::find($product_orders_status['status_id']);
                                                                        ?>
                                                                    @if(count($order->items) > 0)
                                                                        <tr class="tile scale-anm {{ $status['status_en'] }} all">
                                                                            <td class="d-none">
                                                                                {{ $order_key + 1 }}
                                                                            </td>
                                                                            <td>
                                                                                <div
                                                                                    class="card border position-relative">
                                                                                    <div
                                                                                        class="btn-group position-absolute">
                                                                                        <button type="button"
                                                                                                class="btn dropdown-toggle"
                                                                                                data-bs-toggle="dropdown"
                                                                                                aria-haspopup="true"
                                                                                                aria-expanded="false">
                                                                                            <i class="bx bx-dots-horizontal-rounded"></i>
                                                                                        </button>
                                                                                        <ul class="dropdown-menu">
                                                                                            <li class="dropdown-item">
                                                                                                <a class="text-primary fw-bold"
                                                                                                   href="{{ url('admin_panel/purchase/' . $order['id'] .'/'.$store['id']) }}">زيارة</a>
                                                                                            </li>
                                                                                        </ul>
                                                                                    </div><!-- /btn-group -->
                                                                                    <div class="card-header">
                                                                                        <div class="purchase_info">
                                                                                            <h4>
                                                                                                رقم
                                                                                                الطلب: {{ $order['number'] }}
                                                                                            </h4>
                                                                                            <p>
                                                                                                تاريخ الطلب
                                                                                                : {{ $order['created_at']->format('d M Y') }}
                                                                                            </p>
                                                                                        </div>
                                                                                        <div class="purchase_statue">

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
                                                                                                $product_orders = \App\Models\OrderProduct::where('order_id', $order['id'])->where('store_id' , $store['id'])->get()->unique('store_id');

                                                                                                $status = \App\Models\OrderStatus::find($product_orders_status['status_id']);
                                                                                                ?>
                                                                                            <div
                                                                                                class="badge {{ $color }}">
                                                                                                {{ $status['status_'.$lang] }}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="card-body p-2">
                                                                                        <div class="row">
                                                                                            <div class="col-md-8">

                                                                                                @foreach($product_orders as $product_order)
                                                                                                        <?php
                                                                                                        $total = \App\Models\OrderProduct::where('order_id', $order['id'])->where('store_id', $product_order['store_id'])->sum('total_price');
                                                                                                        $count = \App\Models\OrderProduct::where('order_id', $order['id'])->where('store_id', $product_order['store_id'])->sum('quantity');
                                                                                                        $user = $order['user'];
                                                                                                        $user_store = \App\Models\Store::where('id' , $product_order['store_id'])->first();
                                                                                                        ?>
                                                                                                    <div
                                                                                                        class="store-purchase">
                                                                                                        <div
                                                                                                            class="store-info-purchase">
                                                                                                            <div
                                                                                                                class="d-flex align-items-center">
                                                                                                                <div
                                                                                                                    class="border rounded-circle flex-shrink-0 position-relative">
                                                                                                                    <img
                                                                                                                        src="{{ isset($user_store) ? $user_store['image_url'] : '' }}"
                                                                                                                        alt=""
                                                                                                                        class="avatar-sm rounded-circle">
                                                                                                                </div>
                                                                                                                <div
                                                                                                                    class="flex-grow-1 ms-2 text-start store-info">
                                                                                                                    <h5 class="d-flex align-items-center">
                                                                                                                        {{ $product_order['store']['name'] ?: '' }}
                                                                                                                        <i class="ri-checkbox-circle-fill px-2"></i>
                                                                                                                    </h5>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <div
                                                                                                                class="items-price">
                                                                                                                المجموع
                                                                                                                الكلي :
                                                                                                                {{ $total }}
                                                                                                                LE
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div
                                                                                                            class="total-info">
                                                                                                            المجموع
                                                                                                            الكلي (
                                                                                                            قطعة ) :
                                                                                                            {{ $count }}
                                                                                                            قطعة
                                                                                                        </div>
                                                                                                    </div>
                                                                                                @endforeach
                                                                                            </div>
                                                                                            <div class="col-md-4">
                                                                                                    <?php
                                                                                                    $all_total = \App\Models\OrderProduct::where('order_id', $order['id'])->get()->sum('total_price');
                                                                                                    $all_count = \App\Models\OrderProduct::where('order_id', $order['id'])->get()->sum('quantity');
                                                                                                    ?>
                                                                                                <div
                                                                                                    class="total-price-items">
                                                                                                    <h5 class="item-text">
                                                                                                        المجموع الكلي
                                                                                                        (قطعة):
                                                                                                    </h5>
                                                                                                    <h5 class="count">
                                                                                                        {{ $all_count }}
                                                                                                        pieces
                                                                                                    </h5>
                                                                                                    <hr>
                                                                                                    <h5 class="price">
                                                                                                        المجموع الكلي :
                                                                                                        {{ $all_total }}
                                                                                                        LE
                                                                                                    </h5>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                    </div>
                                                </div>
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
