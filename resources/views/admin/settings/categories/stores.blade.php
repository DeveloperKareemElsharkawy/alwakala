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
    <style>
        .wrapper .upload-options label{
            display: block;
        }
    </style>
@endsection
@section('backend-main')
    <?php
    $lang = app()->getLocale();
    ?>

    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-head">
                        <h5>
                            {{ __('admin.vendors') }}
                        </h5>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header p-3 border-0 align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">{{ __('admin.vendors') }}</h4>
                        </div><!-- end card header -->

                        <div class="card-body pt-1">
                            <div class="">
                                <table id="vendor_example" class="dataTable row-border" style="width:100%">
                                    <thead class="d-none">
                                    <tr>
                                        <th>#</th>
                                        <th>#</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($stores as $key => $store)
                                        <tr>
                                            <td class="d-none">
                                                1
                                            </td>
                                            <td>
                                                <div class="card border-solid border">
                                                    <div class="card-body">
                                                        <div class="float-end created_at">
                                                            {{ __('admin.joined') }} {{ $store->created_at->format('M Y') }}
                                                        </div>
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
                                                                <p class="text-muted mb-0">{{ count($store->followers) . ' ' . trans('admin.followers') }}  -  {{ count($store->views) . ' ' . trans('admin.views') }}</p>
                                                                {!! $store->getBadge() !!}
                                                            </div>
                                                        </div>
                                                        <ul class="mt-4 mb-2 store-counters">
                                                            <li class="border-solid rounded border">
                                                                <div class="icon-text">
                                                                    <i class="bi bi-boxes"></i>
                                                                    منتجات
                                                                </div>
                                                                <div class="counter">
                                                                    {{ count($store->allProducts) }}
                                                                </div>
                                                            </li>
                                                            <li class="border-solid rounded border">
                                                                <div class="icon-text">
                                                                    <i class="mdi mdi-account-group"></i>
                                                                    زبائن
                                                                </div>
                                                                <div class="counter">
                                                                    325
                                                                </div>
                                                            </li>
                                                            <li class="border-solid rounded border">
                                                                <div class="icon-text">
                                                                    <i class="ph-files-fill"></i>
                                                                    طلبات
                                                                </div>
                                                                <div class="counter">
                                                                    {{ count($store->owner->orders) }}
                                                                </div>
                                                            </li>
                                                            <li class="border-solid rounded border">
                                                                <div class="icon-text">
                                                                    <i class="bi bi-box-seam"></i>
                                                                    ارجاع
                                                                </div>
                                                                <div class="counter">
                                                                    325
                                                                </div>
                                                            </li>
                                                            <li class="border-solid rounded border">
                                                                <div class="icon-text">
                                                                    <i class="bx bxs-user-pin"></i>
                                                                    متابعين
                                                                </div>
                                                                <div class="counter">
                                                                    {{ count($store->followers) }}
                                                                </div>
                                                            </li>
                                                            <li class="border-solid rounded border">
                                                                <div class="icon-text">
                                                                    <i class="bi bi-heart"></i>
                                                                    المفضلة
                                                                </div>
                                                                <div class="counter">
                                                                    {{ count($store->SellerFavorite) }}
                                                                </div>
                                                            </li>
                                                        </ul>
                                                        <div class="m-0 p-0 store-link text-end">
                                                            <a href="{{ route('vendors.show' , $store->id) }}">
                                                                زيارة ال{{ __('admin.vendor_s') }}
                                                                <i class="bx bx-arrow-back"></i>
                                                            </a>
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
    <script src="{{ asset('admin') }}/assets/js/pages/form-wizard.init.js"></script>

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
    <script src="{{ asset('admin') }}/assets/js/additional-methods.min.js"></script>
    <script src="{{ asset('admin') }}/assets/js/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".my_form").validate({
                rules: {
                    cover: {
                        required: {{ $store->cover ? 'false' : 'true' }},
                    },
                    image: {
                        required: {{ $store->user->image ? 'false' : 'true' }},
                    },
                    logo: {
                        required: {{ $store->logo ? 'false' : 'true' }},
                    },
                    store_name: "required",
                    store_address: "required",
                    city_id: "required",
                    category_id: "required",
                    user_name: "required",
                    email: "required",
                    user_phone: "required",
                    text_card: {
                        required: {{ $store->text_card ? 'false' : 'true' }},
                    },
                    licence: {
                        required: {{ $store->licence ? 'false' : 'true' }},
                    },
                    identity: {
                        required: {{ $store->identity ? 'false' : 'true' }},
                    },
                },
                messages: {
                    cover: {
                        required: " صورة الكفر مطلوبة",
                    },
                    image: {
                        required: " صورة  مطلوبة",
                    },
                    logo: {
                        required: "لوجو الشركة مطلوب",
                    },
                    store_name: "اسم المتجر مطلوب",
                    store_address: "العنوان للمتجر مطلوب",
                    city_id: "الولاية مطلوبة",
                    category_id: "التصنيف مطلوب",
                    user_name: "اسم المستخدم كامل مطلوب",
                    email: "البريد مطلوب",
                    user_phone: "رقم هاتف المستخدم مطلوب",
                    text_card: {
                        required: "البطاقة النصية مطلوبة",
                    },
                    licence: {
                        required: "السجل الضريبي مطلوب",
                    },
                    identity: {
                        required: "الهوية الوطنية مطلوبة",
                    },
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $(".my_formm").validate({
                rules: {
                    name_ar: "required",
                    name_en: "required",
                    image: "required",
                    "category[]": "required"
                },
                messages: {
                    name_ar: "اسم البراند بالعربية مطلوب",
                    name_en: "اسم البراند بالانجليزية مطلوب",
                    image: "صورة البراند مطلوبة",
                    "category[]": "من فضلك اختر تصنيف"
                }
            });
        });
    </script>
@endsection
