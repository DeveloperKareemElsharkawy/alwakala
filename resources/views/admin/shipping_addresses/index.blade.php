<?php
    $lang = app()->getLocale();
?>
@extends('admin.layout.master')
@section('pageTitle', 'عناوين الاستلام')
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
    <div class="modal fade" id="exampleModalgrid" data-bs-backdrop="static" tabindex="-1"
         aria-labelledby="exampleModalgridLabel" aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ri-close-line"></i>
                </button>
                <div class="modal-header d-block text-center">
                    <h5 class="modal-title" id="exampleModalgridLab">اضافة عنوان توصيل جديد</h5>
                </div>
                <div class="modal-body px-5">
                    <form class=" px-5" method="post" action="{{ url('admin_panel/new_shipping_address') }}">
                        {{ csrf_field() }}
                        <div class="row g-3">
                            <div class="col-md-12 col-12">
                                <div class="select-div">
                                    <label for="state_id" class="form-label">المحافظة</label>
                                    <select class="form-select select-modal" id="state_id" required="">
                                        <option selected="" disabled="" value="" hidden></option>
                                        @foreach($states as $state)
                                            <option value="{{ $state['id'] }}">{{ $state['name_'.$lang] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div><!--end col-->
                            <div class="col-md-12 col-12">
                                <div class="select-div">
                                    <label for="city_id" class="form-label">المدينة</label>
                                    <select class="form-select select-modal" name="city_id" id="city_id" required="">
                                        <option selected="" disabled="" value="" hidden>اختر المحافظة اولا</option>
                                    </select>
                                </div>
                            </div><!--end col-->
                            <div class="col-md-12 col-12">
                                <label for="name1" class="form-label">تكلفة الشحن</label>
                                <div class="input-group location_price">
                                    <span class="input-group-text fw-bold">LE</span>
                                    <input name="fees" type="number" class="form-control">
                                    <input name="store_id" value="{{ $store['id'] }}" hidden="">
                                </div>
                            </div><!--end col-->

                            <div class="col-lg-12">
                                <div class="hstack gap-2 mt-5 justify-content-center">
                                    <button type="submit" class="btn btn-gradient">حفظ</button>
                                </div>
                            </div><!--end col-->
                        </div><!--end row-->
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Grids in modals -->
    <div class="modal fade" id="exampleModalgridedit" data-bs-backdrop="static" tabindex="-1"
         aria-labelledby="exampleModalgridLabeledit" aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content" id="exampleModalgrideditform">

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
                                                <a href="#" data-bs-toggle="modal"
                                                   data-bs-target="#exampleModalgrid"
                                                   class="btn btn-warning role-add">
                                                    <i class=" bx bx-plus"></i>
                                                    اضافة عنوان جديد
                                                </a>
                                            </div>
                                            <div class="">
                                                <table id="vendor_places" class="dataTable row-border geo-table"
                                                       style="width:100%">
                                                    <thead class="d-none">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>#</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($addresses as $key => $address)
                                                    <tr class="image_class{{ $address['id'] }}">
                                                        <td class="d-none">
                                                            {{ $key + 1 }}
                                                        </td>
                                                        <td>
                                                            <div class="card border-solid border">
                                                                <div class="card-body p-2">
                                                                    <div class="user-row justify-content-between">
                                                                        <div class="d-flex">
                                                                            <img width="40" height="40" class="geo_image"
                                                                                 src="{{ asset('admin') }}/assets/images/geo.jpg"/>
                                                                            <div class="user-data">
                                                                                <h5>{{ $address['city']['name_'.$lang] }}</h5>
                                                                            </div>
                                                                        </div>
                                                                        <div class="btn-group">
                                                                            <button type="button"
                                                                                    class="btn dropdown-toggle"
                                                                                    data-bs-toggle="dropdown"
                                                                                    aria-haspopup="true"
                                                                                    aria-expanded="false">
                                                                                <i class="bx bx-dots-horizontal-rounded"></i>
                                                                            </button>
                                                                            <div class="dropdown-menu">
                                                                                <a address="{{ $address['id'] }}" class="dropdown-item btn edit_user text-primary fw-bold">EDIT</a>
                                                                                <a class="dropdown-item text-danger btn fw-bold sa-warning" delete_url="delete_address/"
                                                                                   object_id="{{ $address['id'] }}" data-bs-toggle="tooltip"
                                                                                   data-bs-placement="top" button_type="delete">DELETE</a>
                                                                            </div>
                                                                        </div><!-- /btn-group -->
                                                                    </div>
                                                                    <ul class=" mb-2 delivery-info">
                                                                        <li>
                                                                            <strong>تكلفة الشحن : </strong>
                                                                            {{ $address['fees'] }} LE
                                                                        </li>
                                                                    </ul>

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
    <script>
        $(".edit_user").on("click", function (e) {
            var address = $(this).attr('address');
            $('#exampleModalgrideditform').html('<div class="col-md-12 col-xl-12 text-center loading"><i class="mdi mdi-loading fa-spin"></i></div>');
            var link = '<?php echo url('/'); ?>';
            $('#exampleModalgridedit').modal('show');
            $.ajax({
                type: "GET",
                url: link + "/admin_panel/shipping_address_info/" + address,
                success: function (data) {
                    $('#exampleModalgrideditform').html(data);
                }
            });
        });

    </script>
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
                });h
            });
        });
    </script>
@endsection
