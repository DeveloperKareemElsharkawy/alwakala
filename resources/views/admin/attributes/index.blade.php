<?php
$lang = app()->getLocale();
?>
@extends('admin.layout.master')
@section('pageTitle', 'خصائص المنتج')
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
    <!-- Grids in modals -->
    <div class="modal fade product_new_modal" id="exampleModalgridattr" data-bs-backdrop="static" tabindex="-1"
         aria-labelledby="exampleModalgridLabb" aria-modal="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ri-close-line"></i>
                </button>
                <div class="modal-header d-block text-center">
                    <h5 class="modal-title" id="exampleModalgridLabb">اضافة خصائص جديدة</h5>
                </div>
                <div class="modal-body attr_body px-5">

                </div>
            </div>
        </div>
    </div>

    <!-- Grids in modals -->
    <div class="modal fade product_new_modal" id="exampleModalgridedit" data-bs-backdrop="static" tabindex="-1"
         aria-labelledby="exampleModalgridLabeledit" aria-modal="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ri-close-line"></i>
                </button>
                <div class="modal-header d-block text-center">
                    <h5 class="modal-title" id="exampleModalgridLabeledit">تعديل خاصية </h5>
                </div>
                <div id="exampleModalgrideditform" class="modal-body px-5">

                </div>
            </div>
        </div>
    </div>


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
                                    <div class="card store-places">
                                        <div class="card-body p-0">
                                            <div class="add-btns mb-2 justify-content-end">
                                                <a href="#" product_id="{{ $product['id'] }}" data-bs-toggle="modal"
                                                   data-bs-target="#exampleModalgridattr"
                                                   store_id="{{ $store['id'] }}" class="btn btn-warning role-add add_attribute">
                                                    <i class=" bx bx-plus"></i>
                                                    اضافة خاصية جديدة
                                                </a>
                                            </div>
                                            <div class="">
                                                <table id="user_example" class="dataTable row-border"
                                                       style="width:100%">
                                                    <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>اللون</th>
                                                        <th>الحجم</th>
                                                        <th>المخزون الكلي</th>
                                                        <th>ما تم بيعة</th>
                                                        <th>المتبقي</th>
                                                        <th>الاجراءات</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($attributess as $key => $attribute)
                                                        <?php $attributs = \App\Models\ProductStoreStock::where('product_store_id' , $product_store['id'])->where('color_id' , $attribute['color']['id'])->orderBy('id' , 'desc')->get(); ?>
                                                        <tr class="image_class{{ $attribute->id }}">
                                                            <td>{{ $key + 1 }}</td>
                                                            <td style="justify-content: start" class="user-row">
                                                                {{ $attribute['color']['name_'.$lang] }}
                                                                 <span style="width: 20px;height: 20px;background: {{ $attribute['color']['hex'] }};border-radius: 50px;margin:0 3px"></span>
                                                            </td>
                                                            <td>@foreach($attributs as $sizo) {{ $sizo->size ? $sizo->size['size'] : '---' }} <br> @endforeach</td>
                                                            <td>
                                                                {{ $attributs->sum('stock') }}
                                                            </td>
                                                            <td>{{ $attributs->sum('sold') }}</td>
                                                            <td>
                                                                {{ $attributs->sum('stock') - $attributs->sum('sold') }}
                                                            </td>
                                                            <td>
                                                                <ul class="actions-list">
                                                                    <li>
                                                                        <a title="quick edit" attribute="{{ $attribute['id'] }}"
                                                                           class="edit-btn edit_user" href="#" data-bs-toggle="modal"
                                                                           data-bs-target="#exampleModalgridedit">
                                                                            <i class="bi bi-pencil"></i>
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a title="archive" delete_url="attributes/"
                                                                           object_id="{{ $attribute['id'] }}"
                                                                           data-bs-toggle="tooltip"
                                                                           data-bs-placement="top"
                                                                           button_type="delete"
                                                                           class="archive-btn sa-warning" href="#">
                                                                            <i class="ph-trash"></i>
                                                                        </a>
                                                                    </li>
                                                                </ul>
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

    @if(Session::get('type') == 'add_new')
        <script>
            $( document ).ready(function() {
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
    <!-- gmaps plugins -->
    <script src="{{ asset('admin') }}/assets/libs/gmaps/gmaps.min.js"></script>
    <!-- gmaps init -->
    <script src="{{ asset('admin') }}/assets/js/pages/gmaps.init.js"></script>

    <script>
        $('#state_id').on('change', function (e) {
            console.log(e);
            var state_id = e.target.value;
            var server = '<?php echo \Request::root(); ?>';
            $.get(server + '/city_ajax?state_id=' + state_id, function (data) {
                $('#city_id').empty();
                $('#city_id').append('<option value="" selected hidden disabled>Select City</option>');
                $.each(data, function (index, subcatObj) {
                    $('#city_id').append('<option value="' + subcatObj.id + '">' + subcatObj.name + '</option>');
                });
                h
            });
        });
    </script>

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
    <script>
        $(".edit_user").on("click", function (e) {
            var attribute = $(this).attr('attribute');
            $('#exampleModalgrideditform').html('<div class="col-md-12 col-xl-12 text-center loading"><i class="mdi mdi-loading fa-spin"></i></div>');
            var link = '<?php echo url('/'); ?>';
            $.ajax({
                type: "GET",
                url: link + "/admin_panel/attributes/" + attribute +"/show",
                success: function (data) {
                    $('#exampleModalgrideditform').html(data);
                }
            });
        });

    </script>
@endsection
