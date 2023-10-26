<?php
$lang = app()->getLocale();
?>
@extends('admin.layout.master')
@section('pageTitle', $product->name)
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
                                    <div class="card">
                                        <div class="card-header form-header py-2">
                                            <h5 class="fs-md d-flex align-items-center">
                                                تفاصيل المخزون
                                            </h5>
                                        </div>
                                        <div class="card-body p-0">
                                            <form class="product-from px-3" action="#">
                                                <div class="row g-3">
                                                    <div class="col-md-6 col-12">
                                                        <div class="row">
                                                            <div class="col-md-12 col-12">
                                                                <div>
                                                                    <label for="name" class="form-label">اسم
                                                                        المنتج</label>
                                                                    <input type="text" class="form-control"
                                                                           id="name" placeholder="" value="{{ $product['name'] }}" readonly>
                                                                </div>
                                                            </div><!--end col-->
                                                            <div class="col-md-12 col-12">
                                                                <div>
                                                                    <label for="desc" class="form-label">نبذة عن
                                                                        المنتج</label>
                                                                    <textarea rows="1" class="form-control"
                                                                              id="desc" placeholder="" readonly>{{ $product['description'] }}</textarea>
                                                                </div>
                                                            </div><!--end col-->
                                                            <div class="col-md-12 col-12">
                                                                <div>
                                                                    <label for="date" class="form-label">تاريخ
                                                                        النشر</label>
                                                                    <input type="date" class="form-control"
                                                                           id="date" placeholder="" readonly value="{{ $product['created_at']->format('Y-m-d') }}">
                                                                </div>
                                                            </div><!--end col-->
                                                            <div class="col-md-12 col-12">
                                                                <div>
                                                                    <label for="youtube" class="form-label">رابط ال
                                                                        youtube</label>
                                                                    <input type="url" class="form-control"
                                                                           id="youtube" placeholder="" value="{{ $product['youtube_link'] }}" readonly>
                                                                </div>
                                                            </div><!--end col-->
                                                            <div class="col-md-6 col-12">
                                                                <div>
                                                                    <label for="wholesale_price" class="form-label">سعر
                                                                        الجملة</label>
                                                                    <input type="text" class="form-control"
                                                                           id="wholesale_price" value="{{ $product_store['price'] }} جنية" readonly>
                                                                </div>
                                                            </div><!--end col-->
                                                            <div class="col-md-6 col-12">
                                                                <div>
                                                                    <label for="consumer_price" class="form-label">سعر
                                                                        القطاعي</label>
                                                                    <input type="text" class="form-control"
                                                                           id="consumer_price" value="{{ $product_store['consumer_price'] }} جنية" readonly>
                                                                </div>
                                                            </div><!--end col-->
                                                            <div class="col-md-12 col-12">
                                                                <div>
                                                                    <label for="discount" class="form-label">قيمة
                                                                         الخصم للقطاعي</label>
                                                                    <input type="text" class="form-control"
                                                                           id="discount" value="{{ $product_store['consumer_price_discount'] }} {{ $product_store['consumer_price_discount_type'] == '2' ? ' % ' : ' جنية ' }}" readonly>
                                                                </div>
                                                            </div><!--end col-->
                                                            <div class="col-md-6 col-12">
                                                                <div>
                                                                    <label for="wholesale_discount"
                                                                           class="form-label">قيمة الخصم للجملة</label>
                                                                    <input type="text" class="form-control"
                                                                           id="discount" value="{{ $product_store['discount'] }} {{ $product_store['discount_type'] == '2' ? ' % ' : ' جنية ' }}" readonly>
                                                                </div>
                                                            </div><!--end col-->
                                                            <div class="col-md-6 col-12">
                                                                <div>
                                                                    <label for="consumer_discount"
                                                                           class="form-label">سعر الجملة بعد
                                                                        الخصم</label>
                                                                    <input type="text" class="form-control"
                                                                           id="consumer_discount" value="{{ $product_store['net_price'] }} جنية" readonly>
                                                                </div>
                                                            </div><!--end col-->
                                                            <div class="col-md-6 col-12">
                                                                <div>
                                                                    <label for="consumer_discount"
                                                                           class="form-label">سعر القطاعي بعد
                                                                        الخصم</label>
                                                                    <input type="text" class="form-control"
                                                                           id="consumer_discount" value="{{ $product_store['consumer_price'] }} جنية" readonly>
                                                                </div>
                                                            </div><!--end col-->
                                                            <div class="col-md-12 col-12">

                                                                <div class="select-div">
                                                                    <label for="category_id" class="form-label">التصنيف
                                                                        الرئيسي</label>
                                                                    <input type="text" class="form-control"
                                                                           id="consumer_discount" @if(isset($product->category->parent)) value="{{ $product->category->parent['name_' . $lang] }}" @else value="{{ $product->category['name_' . $lang] }}" @endif readonly>
                                                                </div>
                                                            </div><!--end col-->
                                                            @if(isset($product->category->parent))
                                                            <div class="col-md-12 col-12">
                                                                <div class="select-div">
                                                                    <label for="subcategory_id" class="form-label">التصنيف
                                                                        الفرعي</label>
                                                                    <input type="text" class="form-control"
                                                                           id="consumer_discount" value="{{ $product->category['name_' . $lang] }}" readonly>
                                                                </div>
                                                            </div><!--end col-->
                                                            @endif
                                                            <div class="col-md-12 col-12">
                                                                <div>
                                                                    <label for="brand"
                                                                           class="form-label">البراند</label>
                                                                    <input type="text" class="form-control"
                                                                           id="brand" placeholder="" value="{{ $product->brand['name_' . $lang] }}" readonly>
                                                                </div>
                                                            </div><!--end col-->
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-md-6 col-12">
                                                        <div class="row mt-3">
                                                            @foreach($product->images as $image)
                                                                <div class="col-md-6 col-12 px-md-1 d-md-flex justify-content-end">
                                                                    <img style="width: 100%;height: 200px;object-fit: contain" src="{{ config('filesystems.aws_base_url') . $image->image }}" alt=""/>
                                                                </div><!--end col-->
                                                            @endforeach
                                                            <div class="col-md-12 col-12">
                                                                <br>
                                                            </div>
                                                            <div class="col-md-6 col-12">
                                                                <?php
                                                                    $stock = \App\Models\ProductStoreStock::where('product_store_id' , $product_store->id)->sum('stock');
                                                                    $returned = \App\Models\ProductStoreStock::where('product_store_id' , $product_store->id)->sum('returned');
                                                                    $now_stock = \App\Models\ProductStoreStock::where('product_store_id' , $product_store->id)->sum('available_stock');
                                                                $color_ids = \App\Models\ProductStoreStock::where('product_store_id' , $product_store->id)->pluck('color_id');
                                                                $colors = \App\Models\Color::whereIn('id',$color_ids)->get();

                                                                $size_ids = \App\Models\ProductStoreStock::where('product_store_id' , $product_store->id)->pluck('size_id');
                                                                $sizes = \App\Models\Size::whereIn('id',$size_ids)->get();
                                                                ?>
                                                                <div>
                                                                    <label for="total_count" class="form-label">العدد
                                                                        الكلي </label>
                                                                    <input type="number" class="form-control"
                                                                           id="total_count" value="{{ $stock }}" readonly>
                                                                </div>
                                                            </div><!--end col-->
                                                            <div class="col-md-6 col-12">
                                                                <div>
                                                                    <label for="returned" class="form-label">العدد
                                                                        المعاد</label>
                                                                    <input type="number" class="form-control"
                                                                           id="returned" value="{{ $returned }}" readonly>
                                                                </div>
                                                            </div><!--end col-->
                                                            <div class="col-md-6 col-12">
                                                                <div>
                                                                    <label for="sold" class="form-label">تم
                                                                        بيعه</label>
                                                                    <input type="number" class="form-control"
                                                                           id="sold" value="{{ $stock - $now_stock }}" readonly>
                                                                </div>
                                                            </div><!--end col-->
                                                            <div class="col-md-6 col-12">
                                                                <div>
                                                                    <label for="remaining" class="form-label">المنتجات
                                                                        المتبقية</label>
                                                                    <input type="number" class="form-control"
                                                                           id="remaining" value="{{ $now_stock }}" readonly>
                                                                </div>
                                                            </div><!--end col-->
                                                            <div class="col-md-12 col-12">
                                                                <div>
                                                                    <label for="material"
                                                                           class="form-label">الخامة</label>
                                                                    <input type="text" class="form-control"
                                                                           id="material" value="{{ $product->material['name_'.$lang] }}" readonly>
                                                                </div>
                                                            </div><!--end col-->
                                                            <div class="col-md-12 col-12">
                                                                <div class="property-options">
                                                                    <div class="property-label">
                                                                        <label for="material" class="form-label">الالوان</label>
                                                                    </div>
                                                                    <div class="add_div" id="first">
                                                                        @foreach($colors as $color)
                                                                            <?php
                                                                                $stockk = \App\Models\ProductStoreStock::where('product_store_id' , $product_store->id)->where('color_id' , $color['id'])->first();
                                                                            ?>
                                                                        <div>
                                                                            <input class="colorSelector"
                                                                                   type="color" value="{{ $color['hex'] }}" readonly>
                                                                            <div class="result"></div>
                                                                            <input type="number" value="{{ $stockk['available_stock'] }}" readonly>
                                                                        </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div><!--end col-->
                                                            </div>
                                                            <div class="col-md-12 col-12">
                                                                <div class="property-options">
                                                                    <div class="property-label">
                                                                        <label for="material"
                                                                               class="form-label">
                                                                            الكمية
                                                                            <small class="text-dark shadow-none">(لكل مقاس)</small>
                                                                        </label>
                                                                    </div>
                                                                    <div class="add_div size_div" id="second">
                                                                        @foreach($sizes as $size)
                                                                                <?php
                                                                                $stockkk = \App\Models\ProductStoreStock::where('product_store_id' , $product_store->id)->where('size_id' , $size['id'])->first();
                                                                                ?>
                                                                        <div>
                                                                            <div class="gradient-box"><input
                                                                                    type="text" style="font-size: 10px" value="{{ $size['size'] }}" disabled>
                                                                            </div>
                                                                            <div class="result"></div>
                                                                            <input type="text" style="font-size: 10px" value="{{ $stockkk['available_stock'] }}" readonly disabled>
                                                                        </div>
                                                                        @endforeach
                                                                    </div>

                                                                </div>
                                                            </div><!--end col-->
                                                        </div><!--end col-->
                                                    </div><!--end row-->
                                                </div>
                                                <input type="submit"
                                                       style="position: absolute; display: none; width: 1px; height: 1px;"
                                                       tabindex="-1" />
                                            </form>
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
    <!-- End Page-content -->

    <footer class="footer d-none">
    </footer>

@endsection
@section('backend-footer')

@endsection
