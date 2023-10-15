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
                    <form class=" px-5" action="#">
                        <div class="row g-3">
                            <div class="col-md-12 col-12">
                                <div class="select-div">
                                    <label for="validationDefault04" class="form-label">المحافظة</label>
                                    <select class="form-select select-modal" id="validationDefault04" required="">
                                        <option selected="" disabled="" value="" hidden></option>
                                        <option>القاهرة</option>
                                        <option>الدقهلية</option>
                                        <option>الشرقية</option>
                                        <option>دمياط</option>
                                        <option>جنوب سيناء</option>
                                        <option>السويس</option>
                                    </select>
                                </div>
                            </div><!--end col-->
                            <div class="col-md-12 col-12">
                                <div class="select-div">
                                    <label for="validationDefault01" class="form-label">المدينة</label>
                                    <select class="form-select select-modal" id="validationDefault01" required="">
                                        <option selected="" disabled="" value="" hidden></option>
                                        <option>اسم المدينة</option>
                                        <option>اسم المدينة</option>
                                        <option>اسم المدينة</option>
                                        <option>اسم المدينة</option>
                                    </select>
                                </div>
                            </div><!--end col-->
                            <div class="col-md-12 col-12">
                                <div>
                                    <label for="name1" class="form-label">المنطقة</label>
                                    <input type="text" class="form-control" id="name1" placeholder="">
                                    <div class="map_input">
                                        <a class="add_location" data-bs-toggle="modal"
                                           data-bs-target="#exampleModalgridedit" href="#">
                                            <span>اضافة موقع من الخريطة</span>
                                            <i class="ri-map-pin-fill"></i>
                                        </a>
                                        <a class="show_location" data-bs-toggle="modal"
                                           data-bs-target="#exampleModalgridedit" href="#">
                                            <span>تصفح موقع من الخريطة</span>
                                            <i class="ri-map-pin-fill"></i>
                                        </a>
                                    </div>
                                </div>
                            </div><!--end col-->
                            <div class="col-md-12 col-12">
                                <label for="name1" class="form-label">تكلفة الشحن</label>
                                <div class="input-group location_price">
                                    <span class="input-group-text fw-bold">LE</span>
                                    <input type="number" class="form-control">
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
    <div class="modal fade" id="exampleModalgrideditt" data-bs-backdrop="static" tabindex="-1"
         aria-labelledby="exampleModalgridLabeleditt" aria-modal="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content" id="exampleModalgrideditform">

            </div>
        </div>
    </div>

    <!-- Grids in modals -->
    <div class="modal fade" id="exampleModalgridedit" data-bs-backdrop="static"
         aria-labelledby="exampleModalgridLabeledit" aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ri-close-line"></i>
                </button>
                <div class="modal-body px-5 py-3">
                    <div id="gmaps-markers" class="gmaps"></div>
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
                                                    <tr>
                                                        <td class="d-none">
                                                            1
                                                        </td>
                                                        <td>
                                                            <div class="card border-solid border default">
                                                                <div class="card-body p-2">
                                                                    <div class="user-row">
                                                                        <img width="40" height="40" class="geo_image"
                                                                             src="{{ asset('admin') }}/assets/images/geo.jpg"/>
                                                                        <div class="user-data">
                                                                            <h5>Mansoura, Dakhalia, Egypt, 35 Elgash St
                                                                                35 Elgash St,</h5>
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
                                                                                <a class="dropdown-item text-primary fw-bold"
                                                                                   data-bs-toggle="modal"
                                                                                   data-bs-target="#exampleModalgrid"
                                                                                   href="#">EDIT</a>
                                                                                <a class="dropdown-item text-danger fw-bold sa-warning"
                                                                                   href="#">DELETE</a>
                                                                            </div>
                                                                        </div><!-- /btn-group -->
                                                                    </div>
                                                                    <ul class=" mb-2 delivery-info">
                                                                        <li>
                                                                            <strong>رقم الهاتف : </strong>
                                                                            01096269579
                                                                        </li>
                                                                    </ul>
                                                                    <div class="mb-2">
                                                                        <a class="map_popup" data-bs-toggle="modal"
                                                                           data-bs-target="#exampleModalgridedit"
                                                                           href="#">
                                                                            <span>تصفح موقع من الخريطة</span>
                                                                            <i class="ri-map-pin-fill"></i>
                                                                        </a>
                                                                    </div>

                                                                    <div class="text-end">
                                                                            <span class="map_default">
                                                                                عنوان الشحن والدفع الافتراضي
                                                                            </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="d-none">
                                                            1
                                                        </td>
                                                        <td>
                                                            <div class="card border-solid border">
                                                                <div class="card-body p-2">
                                                                    <div class="user-row">
                                                                        <img width="40" height="40" class="geo_image"
                                                                             src="assets/images/geo.jpg"/>
                                                                        <div class="user-data">
                                                                            <h5>Mansoura, Dakhalia, Egypt, 35 Elgash St
                                                                                35 Elgash St,</h5>
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
                                                                                <a class="dropdown-item text-primary fw-bold"
                                                                                   data-bs-toggle="modal"
                                                                                   data-bs-target="#exampleModalgrid"
                                                                                   data-bs-toggle="modal"
                                                                                   data-bs-target="#exampleModalgrid"
                                                                                   href="#">EDIT</a>
                                                                                <a class="dropdown-item text-danger fw-bold sa-warning"
                                                                                   href="#">DELETE</a>
                                                                            </div>
                                                                        </div><!-- /btn-group -->
                                                                    </div>
                                                                    <ul class=" mb-2 delivery-info">
                                                                        <li>
                                                                            <br>
                                                                        </li>
                                                                        <li>
                                                                            <strong>رقم الهاتف : </strong>
                                                                            01096269579
                                                                        </li>
                                                                    </ul>
                                                                    <div class="mb-2">
                                                                        <a class="map_popup" data-bs-toggle="modal"
                                                                           data-bs-target="#exampleModalgridedit"
                                                                           href="#">
                                                                            <span>تصفح موقع من الخريطة</span>
                                                                            <i class="ri-map-pin-fill"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
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
        $("body").on("click", ".edit_user", function (e) {
            var user = $(this).attr('user');
            $('#exampleModalgrideditform').html('<div class="col-md-12 col-xl-12 text-center loading"><i class="mdi mdi-loading fa-spin"></i></div>');
            var link = '<?php echo url('/'); ?>';
            $('#exampleModalgridedit').modal('show');
            $.ajax({
                type: "GET",
                url: link + "/admin_panel/user/" + user,
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
@endsection
