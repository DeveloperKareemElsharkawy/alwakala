@extends('admin.layout.master')
@section('pageTitle', 'طلبات غير مكتملة')
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
                                    <div class="card store-places inventory_card">
                                        <div class="card-body p-0">
                                            <div class="">
                                                <div class="tab-content  text-muted">
                                                    <div class="tab-pane active" id="base-justified-home"
                                                         role="tabpanel">

                                                        <div class="">
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
                                                                @foreach($carts as $order_key => $order)
                                                                    @if(count($order->items) > 0)
                                                                        <tr>
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
                                                                                                   href="{{ url('admin_panel/carts/' . $order['id']) }}">زيارة</a>
                                                                                            </li>
                                                                                        </ul>
                                                                                    </div><!-- /btn-group -->
                                                                                    <div class="card-header">
                                                                                        <div class="purchase_info">
                                                                                            <h4>
                                                                                                صاحب الطلب
                                                                                                : {{ isset($order['user']['store']) ? $order['user']['store']['name'] : $order['user']['name'] }}
                                                                                            </h4>
                                                                                            <p>
                                                                                                تاريخ الطلب
                                                                                                : {{ $order['created_at']->format('d M Y') }}
                                                                                            </p>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="card-body p-2">
                                                                                        <div class="row">
                                                                                            <div class="col-md-8">
                                                                                                <?php
                                                                                                    $items = \App\Models\CartItem::where('cart_id' , $order['id'])->where('user_id' , $order['user_id'])->get()->unique('store_id');
                                                                                                    ?>
                                                                                                @foreach($items as $product_order)
                                                                                                        <?php
                                                                                                        $pro_total = \App\Models\CartItem::where('cart_id' , $order['id'])->where('user_id' , $order['user_id'])->where('store_id',$product_order['store_id'])->pluck('product_store_id');
                                                                                                        $total = \App\Models\ProductStore::whereIn('id' , $pro_total)->sum('price');
                                                                                                        $count = \App\Models\CartItem::where('cart_id' , $order['id'])->where('user_id' , $order['user_id'])->where('store_id',$product_order['store_id'])->sum('quantity');
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
                                                                                                                        src="{{ isset($product_order['store']) ? $product_order['store']['image_url'] : null }}"
                                                                                                                        alt=""
                                                                                                                        class="avatar-sm rounded-circle">
                                                                                                                </div>
                                                                                                                <div
                                                                                                                    class="flex-grow-1 ms-2 text-start store-info">
                                                                                                                    <h5 class="d-flex align-items-center">
                                                                                                                        {{ isset($product_order['store']) ? $product_order['store']['name'] : null }}
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
                                                                                                    $proo_total = \App\Models\CartItem::where('cart_id' , $order['id'])->where('user_id' , $order['user_id'])->pluck('product_store_id');
                                                                                                    $totall = \App\Models\ProductStore::whereIn('id' , $pro_total)->sum('price');
                                                                                                    $countt = \App\Models\CartItem::where('cart_id' , $order['id'])->where('user_id' , $order['user_id'])->sum('quantity');
                                                                                                    ?>
                                                                                                <div
                                                                                                    class="total-price-items">
                                                                                                    <h5 class="item-text">
                                                                                                        المجموع الكلي
                                                                                                        (قطعة):
                                                                                                    </h5>
                                                                                                    <h5 class="count">
                                                                                                        {{ $countt }}
                                                                                                        pieces
                                                                                                    </h5>
                                                                                                    <hr>
                                                                                                    <h5 class="price">
                                                                                                        المجموع الكلي :
                                                                                                        {{ $totall }}
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
