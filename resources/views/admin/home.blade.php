@extends('admin.layout.master')
@section('backend-head')
@endsection
@section('pageTitle', trans('admin.home'))
@section('backend-main')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card main-card">
                        <div class="card-header border-0 p-2">
                            <p class="card-title mb-0 px-2 pt-2 flex-grow-1">
                                Statistics & Reports
                            </p>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row">
                                <div class="col-md-12">
                                    <ul class="nav nav-tabs nav-justified mb-3 mt-0 settings_tabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" href="index.html">
                                                <i class="ri-store-2-line text-warning"></i>
                                                متاجر الجملة
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="index_2.html">
                                                <i class="ri-store-2-line text-danger"></i>
                                                متاجر قطاعي
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#">
                                                <i class=" ph-users text-black"></i>
                                                مشترين
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="index_4.html">
                                                <i class="bi bi-boxes text-warning"></i>
                                                طلبات
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#">
                                                <i class="bi bi-wallet2 text-danger"></i>
                                                مبيعات
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="index_6.html">
                                                <i class="bi bi-globe text-primary"></i>
                                                جغرافيا
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#">
                                                <i class="bi bi-grid text-black"></i>
                                                اخرى
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-12 col-xxl-8">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="card">
                                                <div class="card-header border-0 p-2">
                                                    <!--                                                        <p style="font-weight: 500;" class="card-title mb-0 px-2 pt-2 flex-grow-1">-->
                                                    <!--                                                            Top <span class="text-red">5</span> Stores-->
                                                    <!--                                                            <small>Total 10.5k Stores</small>-->
                                                    <!--                                                        </p>-->
                                                    <p style="font-weight: 500;"
                                                       class="card-title mb-0 px-2 pt-2 flex-grow-1">
                                                        افضل <span class="text-red">5</span> متاجر
                                                        <small>15 الف متجر</small>
                                                    </p>
                                                    <a href="#">
                                                        رؤية المزيد
                                                    </a>
                                                </div>
                                                <div class="card-body statics-home m-0 pt-0">
                                                    <table class="table table-bordered mb-0">
                                                        <thead>
                                                        <tr>
                                                            <!--                                                                <th scope="col">#</th>-->
                                                            <!--                                                                <th scope="col">STORE NAME</th>-->
                                                            <!--                                                                <th scope="col">PHONE NO.</th>-->
                                                            <!--                                                                <th scope="col">TOTAL ORDERS</th>-->
                                                            <!--                                                                <th scope="col">TOTAL PROFITS</th>-->
                                                            <th scope="col">#</th>
                                                            <th scope="col">اسم المتجر</th>
                                                            <th scope="col">رقم الهاتف</th>
                                                            <th scope="col">مجمل الطلبات</th>
                                                            <th scope="col">مجمل الدخل</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <th scope="row">1</th>
                                                            <td>
                                                                <a href="#">
                                                                    <img class="avatar-xxs rounded-circle me-2 border"
                                                                         src="{{ asset('admin') }}/assets/images/brands/max.png"/> LC
                                                                    Waikiki Store
                                                                </a>
                                                            </td>
                                                            <td class="phone">0109 - 626 - 9579</td>
                                                            <td class="order">@200K Order</td>
                                                            <td class="price">368 K LE</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">2</th>
                                                            <td>
                                                                <a href="#">
                                                                    <img class="avatar-xxs rounded-circle me-2 border"
                                                                         src="{{ asset('admin') }}/assets/images/brands/max.png"/> LC
                                                                    Waikiki Store
                                                                </a>
                                                            </td>
                                                            <td class="phone">0109 - 626 - 9579</td>
                                                            <td class="order">@200K Order</td>
                                                            <td class="price">368 K LE</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">3</th>
                                                            <td>
                                                                <a href="#">
                                                                    <img class="avatar-xxs rounded-circle me-2 border"
                                                                         src="{{ asset('admin') }}/assets/images/brands/max.png"/> LC
                                                                    Waikiki Store
                                                                </a>
                                                            </td>
                                                            <td class="phone">0109 - 626 - 9579</td>
                                                            <td class="order">@200K Order</td>
                                                            <td class="price">368 K LE</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">4</th>
                                                            <td>
                                                                <a href="#">
                                                                    <img class="avatar-xxs rounded-circle me-2 border"
                                                                         src="{{ asset('admin') }}/assets/images/brands/max.png"/> LC
                                                                    Waikiki Store
                                                                </a>
                                                            </td>
                                                            <td class="phone">0109 - 626 - 9579</td>
                                                            <td class="order">@200K Order</td>
                                                            <td class="price">368 K LE</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">5</th>
                                                            <td>
                                                                <a href="#">
                                                                    <img class="avatar-xxs rounded-circle me-2 border"
                                                                         src="{{ asset('admin') }}/assets/images/brands/max.png"/> LC
                                                                    Waikiki Store
                                                                </a>
                                                            </td>
                                                            <td class="phone">0109 - 626 - 9579</td>
                                                            <td class="order">@200K Order</td>
                                                            <td class="price">368 K LE</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-header border-0 p-2">
                                                    <p style="font-weight: 500;"
                                                       class="card-title mb-0 px-2 pt-2 flex-grow-1">
                                                        النسبة المئوية للمبيعات بالنسبه للمتاجر
                                                        <small>
                                                            Lorem ipsum dolor sit amet, consetetur.
                                                        </small>
                                                    </p>
                                                </div>
                                                <div class="card-body statics-home m-0 pt-0">
                                                    <div class="row">
                                                        <div class="col-md-6 d-flex align-items-center">
                                                            Lorem ipsum dolor sit amet, consetetur sadipscing elitr,
                                                            sed diam nonumy eirmod tempor invidunt ut.
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div id="multiple_radialbar"
                                                                 data-colors='["#F46200", "#F46200", "#F46200"]'
                                                                 class="apex-charts" dir="ltr"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="card">
                                                <div class="card-header border-0 p-2">
                                                    <p style="font-weight: 500;"
                                                       class="card-title mb-0 px-2 pt-2 flex-grow-1">
                                                        الزائرين لحاليين
                                                        <small>15 الف متجر</small>
                                                    </p>
                                                    <a href="#">
                                                        رؤية المزيد
                                                    </a>
                                                </div>
                                                <div class="card-body statics-home m-0 pt-0">
                                                    <table class="table table-bordered mb-0">
                                                        <thead>
                                                        <tr>
                                                            <!--                                                                <th scope="col">#</th>-->
                                                            <!--                                                                <th scope="col">STORE NAME</th>-->
                                                            <!--                                                                <th scope="col">PHONE NO.</th>-->
                                                            <!--                                                                <th scope="col">TOTAL ORDERS</th>-->
                                                            <!--                                                                <th scope="col">TOTAL PROFITS</th>-->
                                                            <th scope="col">#</th>
                                                            <th scope="col">اسم المتجر</th>
                                                            <th scope="col">رقم الهاتف</th>
                                                            <th scope="col">اخر وقت للزيارة</th>
                                                            <th scope="col">الوقت</th>
                                                            <th scope="col">المتصفح</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <th scope="row">1</th>
                                                            <td>
                                                                <a href="#">
                                                                    LC Waikiki Store
                                                                </a>
                                                            </td>
                                                            <td class="phone">0109 - 626 - 9579</td>
                                                            <td class="phone">19 - 7 - 2023</td>
                                                            <td class="phone">12 : 30 AM</td>
                                                            <td class="browser">Browser Name</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">2</th>
                                                            <td>
                                                                <a href="#">
                                                                    LC Waikiki Store
                                                                </a>
                                                            </td>
                                                            <td class="phone">0109 - 626 - 9579</td>
                                                            <td class="phone">19 - 7 - 2023</td>
                                                            <td class="phone">12 : 30 AM</td>
                                                            <td class="browser">Browser Name</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">3</th>
                                                            <td>
                                                                <a href="#">
                                                                    LC Waikiki Store
                                                                </a>
                                                            </td>
                                                            <td class="phone">0109 - 626 - 9579</td>
                                                            <td class="phone">19 - 7 - 2023</td>
                                                            <td class="phone">12 : 30 AM</td>
                                                            <td class="browser">Browser Name</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">4</th>
                                                            <td>
                                                                <a href="#">
                                                                    LC Waikiki Store
                                                                </a>
                                                            </td>
                                                            <td class="phone">0109 - 626 - 9579</td>
                                                            <td class="phone">19 - 7 - 2023</td>
                                                            <td class="phone">12 : 30 AM</td>
                                                            <td class="browser">Browser Name</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">5</th>
                                                            <td>
                                                                <a href="#">
                                                                    LC Waikiki Store
                                                                </a>
                                                            </td>
                                                            <td class="phone">0109 - 626 - 9579</td>
                                                            <td class="phone">19 - 7 - 2023</td>
                                                            <td class="phone">12 : 30 AM</td>
                                                            <td class="browser">Browser Name</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="card">
                                                <div class="card-header border-0 p-2">
                                                    <p style="font-weight: 500;"
                                                       class="card-title mb-0 px-2 pt-2 flex-grow-1">
                                                        افضل المبيعات
                                                        <small>15 الف متجر</small>
                                                    </p>
                                                    <a href="#">
                                                        رؤية المزيد
                                                    </a>
                                                </div>
                                                <div class="card-body statics-home m-0 pt-0">
                                                    <table class="table table-bordered mb-0">
                                                        <thead>
                                                        <tr>
                                                            <!--                                                                <th scope="col">#</th>-->
                                                            <!--                                                                <th scope="col">STORE NAME</th>-->
                                                            <!--                                                                <th scope="col">PHONE NO.</th>-->
                                                            <!--                                                                <th scope="col">TOTAL ORDERS</th>-->
                                                            <!--                                                                <th scope="col">TOTAL PROFITS</th>-->
                                                            <th scope="col">#</th>
                                                            <th scope="col">اسم المتجر</th>
                                                            <th scope="col">التصنيف</th>
                                                            <th scope="col">الماركة</th>
                                                            <th scope="col">اللون</th>
                                                            <th scope="col">السعر</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <th scope="row">1</th>
                                                            <td>
                                                                <a href="#">
                                                                    LC Waikiki Store
                                                                </a>
                                                            </td>
                                                            <td class="phone">ملابس رجالي</td>
                                                            <td class="phone">Max</td>
                                                            <td class="phone">اسود</td>
                                                            <td class="browser">200 LE</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">2</th>
                                                            <td>
                                                                <a href="#">
                                                                    LC Waikiki Store
                                                                </a>
                                                            </td>
                                                            <td class="phone">ملابس رجالي</td>
                                                            <td class="phone">Max</td>
                                                            <td class="phone">اسود</td>
                                                            <td class="browser">200 LE</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">3</th>
                                                            <td>
                                                                <a href="#">
                                                                    LC Waikiki Store
                                                                </a>
                                                            </td>
                                                            <td class="phone">ملابس رجالي</td>
                                                            <td class="phone">Max</td>
                                                            <td class="phone">اسود</td>
                                                            <td class="browser">200 LE</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">4</th>
                                                            <td>
                                                                <a href="#">
                                                                    LC Waikiki Store
                                                                </a>
                                                            </td>
                                                            <td class="phone">ملابس رجالي</td>
                                                            <td class="phone">Max</td>
                                                            <td class="phone">اسود</td>
                                                            <td class="browser">200 LE</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">5</th>
                                                            <td>
                                                                <a href="#">
                                                                    LC Waikiki Store
                                                                </a>
                                                            </td>
                                                            <td class="phone">ملابس رجالي</td>
                                                            <td class="phone">Max</td>
                                                            <td class="phone">اسود</td>
                                                            <td class="browser">200 LE</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="card">
                                                        <div class="card-header border-0 p-2">
                                                            <p style="font-weight: 500;"
                                                               class="card-title mb-0 px-2 pt-2 flex-grow-1">
                                                                اكثر <span class="text-red"> 5 </span> منتجات مبيعا
                                                                <small>15 الف متجر</small>
                                                            </p>
                                                        </div>
                                                        <div class="card-body px-0">
                                                            <div data-simplebar>
                                                                <div class="card rounded-0 border-0 shadow-none mb-0">
                                                                    <div class="card-body pt-0 d-flex align-items-center justify-content-between">
                                                                        <div class="d-flex gap-2">
                                                                            <div class="flex-shrink-0">
                                                                                <img src="{{ asset('admin') }}/assets/images/products/img-1.png"
                                                                                     alt=""
                                                                                     class="avatar-sm rounded">
                                                                            </div>
                                                                            <div class="flex-grow-1">
                                                                                <p class="text-black mb-0 fw-bold">
                                                                                    <a href="#" class="text-muted">Handbag
                                                                                        Tied Scarf</a></p>
                                                                                <p style="opacity: 0.7"
                                                                                   class="text-muted mb-0 fs-xs"><a
                                                                                        href="#" class="text-muted">Textured
                                                                                        Leather Handbag</a></p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="price fw-bold text-red">
                                                                            20.03 LE
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="card rounded-0 border-0 shadow-none mb-0">
                                                                    <div class="card-body pt-0 d-flex align-items-center justify-content-between">
                                                                        <div class="d-flex gap-2">
                                                                            <div class="flex-shrink-0">
                                                                                <img src="{{ asset('admin') }}/assets/images/products/img-1.png"
                                                                                     alt=""
                                                                                     class="avatar-sm rounded">
                                                                            </div>
                                                                            <div class="flex-grow-1">
                                                                                <p class="text-black mb-0 fw-bold">
                                                                                    <a href="#" class="text-muted">Handbag
                                                                                        Tied Scarf</a></p>
                                                                                <p style="opacity: 0.7"
                                                                                   class="text-muted mb-0 fs-xs"><a
                                                                                        href="#" class="text-muted">Textured
                                                                                        Leather Handbag</a></p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="price fw-bold text-red">
                                                                            20.03 LE
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="card rounded-0 border-0 shadow-none mb-0">
                                                                    <div class="card-body pt-0 d-flex align-items-center justify-content-between">
                                                                        <div class="d-flex gap-2">
                                                                            <div class="flex-shrink-0">
                                                                                <img src="{{ asset('admin') }}/assets/images/products/img-1.png"
                                                                                     alt=""
                                                                                     class="avatar-sm rounded">
                                                                            </div>
                                                                            <div class="flex-grow-1">
                                                                                <p class="text-black mb-0 fw-bold">
                                                                                    <a href="#" class="text-muted">Handbag
                                                                                        Tied Scarf</a></p>
                                                                                <p style="opacity: 0.7"
                                                                                   class="text-muted mb-0 fs-xs"><a
                                                                                        href="#" class="text-muted">Textured
                                                                                        Leather Handbag</a></p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="price fw-bold text-red">
                                                                            20.03 LE
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="card rounded-0 border-0 shadow-none mb-0">
                                                                    <div class="card-body pt-0 d-flex align-items-center justify-content-between">
                                                                        <div class="d-flex gap-2">
                                                                            <div class="flex-shrink-0">
                                                                                <img src="{{ asset('admin') }}/assets/images/products/img-1.png"
                                                                                     alt=""
                                                                                     class="avatar-sm rounded">
                                                                            </div>
                                                                            <div class="flex-grow-1">
                                                                                <p class="text-black mb-0 fw-bold">
                                                                                    <a href="#" class="text-muted">Handbag
                                                                                        Tied Scarf</a></p>
                                                                                <p style="opacity: 0.7"
                                                                                   class="text-muted mb-0 fs-xs"><a
                                                                                        href="#" class="text-muted">Textured
                                                                                        Leather Handbag</a></p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="price fw-bold text-red">
                                                                            20.03 LE
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="card rounded-0 border-0 shadow-none mb-0">
                                                                    <div class="card-body pt-0 d-flex align-items-center justify-content-between">
                                                                        <div class="d-flex gap-2">
                                                                            <div class="flex-shrink-0">
                                                                                <img src="{{ asset('admin') }}/assets/images/products/img-1.png"
                                                                                     alt=""
                                                                                     class="avatar-sm rounded">
                                                                            </div>
                                                                            <div class="flex-grow-1">
                                                                                <p class="text-black mb-0 fw-bold">
                                                                                    <a href="#" class="text-muted">Handbag
                                                                                        Tied Scarf</a></p>
                                                                                <p style="opacity: 0.7"
                                                                                   class="text-muted mb-0 fs-xs"><a
                                                                                        href="#" class="text-muted">Textured
                                                                                        Leather Handbag</a></p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="price fw-bold text-red">
                                                                            20.03 LE
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div><!--end card-->
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="card">
                                                        <div class="card-header border-0 p-2">
                                                            <p style="font-weight: 500;"
                                                               class="card-title mb-0 px-2 pt-2 flex-grow-1">
                                                                اكثر <span class="text-red"> 5 </span> منتجات ارجاعا
                                                                <small>15 الف متجر</small>
                                                            </p>
                                                        </div>
                                                        <div class="card-body px-0">
                                                            <div data-simplebar>
                                                                <div class="card rounded-0 border-0 shadow-none mb-0">
                                                                    <div class="card-body pt-0 d-flex align-items-center justify-content-between">
                                                                        <div class="d-flex gap-2">
                                                                            <div class="flex-shrink-0">
                                                                                <img src="{{ asset('admin') }}/assets/images/products/img-1.png"
                                                                                     alt=""
                                                                                     class="avatar-sm rounded">
                                                                            </div>
                                                                            <div class="flex-grow-1">
                                                                                <p class="text-black mb-0 fw-bold">
                                                                                    <a href="#" class="text-muted">Handbag
                                                                                        Tied Scarf</a></p>
                                                                                <p style="opacity: 0.7"
                                                                                   class="text-muted mb-0 fs-xs"><a
                                                                                        href="#" class="text-muted">Textured
                                                                                        Leather Handbag</a></p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="price fw-bold text-red">
                                                                            20.03 LE
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="card rounded-0 border-0 shadow-none mb-0">
                                                                    <div class="card-body pt-0 d-flex align-items-center justify-content-between">
                                                                        <div class="d-flex gap-2">
                                                                            <div class="flex-shrink-0">
                                                                                <img src="{{ asset('admin') }}/assets/images/products/img-1.png"
                                                                                     alt=""
                                                                                     class="avatar-sm rounded">
                                                                            </div>
                                                                            <div class="flex-grow-1">
                                                                                <p class="text-black mb-0 fw-bold">
                                                                                    <a href="#" class="text-muted">Handbag
                                                                                        Tied Scarf</a></p>
                                                                                <p style="opacity: 0.7"
                                                                                   class="text-muted mb-0 fs-xs"><a
                                                                                        href="#" class="text-muted">Textured
                                                                                        Leather Handbag</a></p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="price fw-bold text-red">
                                                                            20.03 LE
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="card rounded-0 border-0 shadow-none mb-0">
                                                                    <div class="card-body pt-0 d-flex align-items-center justify-content-between">
                                                                        <div class="d-flex gap-2">
                                                                            <div class="flex-shrink-0">
                                                                                <img src="{{ asset('admin') }}/assets/images/products/img-1.png"
                                                                                     alt=""
                                                                                     class="avatar-sm rounded">
                                                                            </div>
                                                                            <div class="flex-grow-1">
                                                                                <p class="text-black mb-0 fw-bold">
                                                                                    <a href="#" class="text-muted">Handbag
                                                                                        Tied Scarf</a></p>
                                                                                <p style="opacity: 0.7"
                                                                                   class="text-muted mb-0 fs-xs"><a
                                                                                        href="#" class="text-muted">Textured
                                                                                        Leather Handbag</a></p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="price fw-bold text-red">
                                                                            20.03 LE
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="card rounded-0 border-0 shadow-none mb-0">
                                                                    <div class="card-body pt-0 d-flex align-items-center justify-content-between">
                                                                        <div class="d-flex gap-2">
                                                                            <div class="flex-shrink-0">
                                                                                <img src="{{ asset('admin') }}/assets/images/products/img-1.png"
                                                                                     alt=""
                                                                                     class="avatar-sm rounded">
                                                                            </div>
                                                                            <div class="flex-grow-1">
                                                                                <p class="text-black mb-0 fw-bold">
                                                                                    <a href="#" class="text-muted">Handbag
                                                                                        Tied Scarf</a></p>
                                                                                <p style="opacity: 0.7"
                                                                                   class="text-muted mb-0 fs-xs"><a
                                                                                        href="#" class="text-muted">Textured
                                                                                        Leather Handbag</a></p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="price fw-bold text-red">
                                                                            20.03 LE
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="card rounded-0 border-0 shadow-none mb-0">
                                                                    <div class="card-body pt-0 d-flex align-items-center justify-content-between">
                                                                        <div class="d-flex gap-2">
                                                                            <div class="flex-shrink-0">
                                                                                <img src="{{ asset('admin') }}/assets/images/products/img-1.png"
                                                                                     alt=""
                                                                                     class="avatar-sm rounded">
                                                                            </div>
                                                                            <div class="flex-grow-1">
                                                                                <p class="text-black mb-0 fw-bold">
                                                                                    <a href="#" class="text-muted">Handbag
                                                                                        Tied Scarf</a></p>
                                                                                <p style="opacity: 0.7"
                                                                                   class="text-muted mb-0 fs-xs"><a
                                                                                        href="#" class="text-muted">Textured
                                                                                        Leather Handbag</a></p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="price fw-bold text-red">
                                                                            20.03 LE
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div><!--end card-->
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="card">
                                                        <div class="card-header border-0 p-2">
                                                            <p style="font-weight: 500;"
                                                               class="card-title mb-0 px-2 pt-2 flex-grow-1">
                                                                طرق الدفع
                                                            </p>
                                                        </div>
                                                        <div class="card-body px-0">
                                                            <div data-simplebar>
                                                                <div class="card rounded-0 border-0 shadow-none mb-0">
                                                                    <div class="card-body pt-0 d-flex align-items-center justify-content-between">
                                                                        <div class="d-flex gap-2">
                                                                            <div class="flex-shrink-0">
                                                                                <img src="{{ asset('admin') }}/assets/images/companies/img-1.png"
                                                                                     alt=""
                                                                                     class="avatar-sm rounded-circle border">
                                                                            </div>
                                                                            <div class="flex-grow-1 align-items-center d-flex">
                                                                                <p style="opacity: 0.7"
                                                                                   class="text-muted mb-0 fs-xs"><a
                                                                                        href="#" class="text-muted">The
                                                                                        Order Account Was Paid <br/>
                                                                                        Through The Payment Method</a>
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="price fw-bold text-red">
                                                                            <div id="basic_radialbar"
                                                                                 data-colors='["--tb-success"]'
                                                                                 class="apex-charts"
                                                                                 dir="ltr"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="card rounded-0 border-0 shadow-none mb-0">
                                                                    <div class="card-body pt-0 d-flex align-items-center justify-content-between">
                                                                        <div class="d-flex gap-2">
                                                                            <div class="flex-shrink-0">
                                                                                <img src="{{ asset('admin') }}/assets/images/companies/img-1.png"
                                                                                     alt=""
                                                                                     class="avatar-sm rounded-circle border">
                                                                            </div>
                                                                            <div class="flex-grow-1 align-items-center d-flex">
                                                                                <p style="opacity: 0.7"
                                                                                   class="text-muted mb-0 fs-xs"><a
                                                                                        href="#" class="text-muted">The
                                                                                        Order Account Was Paid <br/>
                                                                                        Through The Payment Method</a>
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="price fw-bold text-red">
                                                                            <div id="basic_radialbar"
                                                                                 data-colors='["--tb-success"]'
                                                                                 class="apex-charts"
                                                                                 dir="ltr"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="card rounded-0 border-0 shadow-none mb-0">
                                                                    <div class="card-body pt-0 d-flex align-items-center justify-content-between">
                                                                        <div class="d-flex gap-2">
                                                                            <div class="flex-shrink-0">
                                                                                <img src="{{ asset('admin') }}/assets/images/companies/img-1.png"
                                                                                     alt=""
                                                                                     class="avatar-sm rounded-circle border">
                                                                            </div>
                                                                            <div class="flex-grow-1 align-items-center d-flex">
                                                                                <p style="opacity: 0.7"
                                                                                   class="text-muted mb-0 fs-xs"><a
                                                                                        href="#" class="text-muted">The
                                                                                        Order Account Was Paid <br/>
                                                                                        Through The Payment Method</a>
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="price fw-bold text-red">
                                                                            <div id="basic_radialbar"
                                                                                 data-colors='["--tb-success"]'
                                                                                 class="apex-charts"
                                                                                 dir="ltr"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="card rounded-0 border-0 shadow-none mb-0">
                                                                    <div class="card-body pt-0 d-flex align-items-center justify-content-between">
                                                                        <div class="d-flex gap-2">
                                                                            <div class="flex-shrink-0">
                                                                                <img src="{{ asset('admin') }}/assets/images/companies/img-1.png"
                                                                                     alt=""
                                                                                     class="avatar-sm rounded-circle border">
                                                                            </div>
                                                                            <div class="flex-grow-1 align-items-center d-flex">
                                                                                <p style="opacity: 0.7"
                                                                                   class="text-muted mb-0 fs-xs"><a
                                                                                        href="#" class="text-muted">The
                                                                                        Order Account Was Paid <br/>
                                                                                        Through The Payment Method</a>
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="price fw-bold text-red">
                                                                            <div id="basic_radialbar"
                                                                                 data-colors='["--tb-success"]'
                                                                                 class="apex-charts"
                                                                                 dir="ltr"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div><!--end card-->
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="card">
                                                <div class="card-header border-0 p-2">
                                                    <p style="font-weight: 500;"
                                                       class="card-title mb-0 px-2 pt-2 flex-grow-1">
                                                        افضل المبيعات
                                                        <small>15 الف متجر</small>
                                                    </p>
                                                    <a href="#">
                                                        رؤية المزيد
                                                    </a>
                                                </div>
                                                <div class="card-body statics-home m-0 pt-0">
                                                    <table class="table table-bordered mb-0">
                                                        <thead>
                                                        <tr>
                                                            <!--                                                                <th scope="col">#</th>-->
                                                            <!--                                                                <th scope="col">STORE NAME</th>-->
                                                            <!--                                                                <th scope="col">PHONE NO.</th>-->
                                                            <!--                                                                <th scope="col">TOTAL ORDERS</th>-->
                                                            <!--                                                                <th scope="col">TOTAL PROFITS</th>-->
                                                            <th scope="col">#</th>
                                                            <th scope="col">اسم المتجر</th>
                                                            <th scope="col">التصنيف</th>
                                                            <th scope="col">الماركة</th>
                                                            <th scope="col">اللون</th>
                                                            <th scope="col">السعر</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <th scope="row">1</th>
                                                            <td>
                                                                <a href="#">
                                                                    LC Waikiki Store
                                                                </a>
                                                            </td>
                                                            <td class="phone">ملابس رجالي</td>
                                                            <td class="phone">Max</td>
                                                            <td class="phone">اسود</td>
                                                            <td class="browser">200 LE</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">2</th>
                                                            <td>
                                                                <a href="#">
                                                                    LC Waikiki Store
                                                                </a>
                                                            </td>
                                                            <td class="phone">ملابس رجالي</td>
                                                            <td class="phone">Max</td>
                                                            <td class="phone">اسود</td>
                                                            <td class="browser">200 LE</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">3</th>
                                                            <td>
                                                                <a href="#">
                                                                    LC Waikiki Store
                                                                </a>
                                                            </td>
                                                            <td class="phone">ملابس رجالي</td>
                                                            <td class="phone">Max</td>
                                                            <td class="phone">اسود</td>
                                                            <td class="browser">200 LE</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">4</th>
                                                            <td>
                                                                <a href="#">
                                                                    LC Waikiki Store
                                                                </a>
                                                            </td>
                                                            <td class="phone">ملابس رجالي</td>
                                                            <td class="phone">Max</td>
                                                            <td class="phone">اسود</td>
                                                            <td class="browser">200 LE</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">5</th>
                                                            <td>
                                                                <a href="#">
                                                                    LC Waikiki Store
                                                                </a>
                                                            </td>
                                                            <td class="phone">ملابس رجالي</td>
                                                            <td class="phone">Max</td>
                                                            <td class="phone">اسود</td>
                                                            <td class="browser">200 LE</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="card">
                                                <div class="card-header border-0 p-2">
                                                    <p style="font-weight: 500;"
                                                       class="card-title mb-0 px-2 pt-2 flex-grow-1">
                                                        افضل المبيعات
                                                        <small>15 الف متجر</small>
                                                    </p>
                                                    <a href="#">
                                                        رؤية المزيد
                                                    </a>
                                                </div>
                                                <div class="card-body statics-home m-0 pt-0">
                                                    <table class="table table-bordered mb-0">
                                                        <thead>
                                                        <tr>
                                                            <!--                                                                <th scope="col">#</th>-->
                                                            <!--                                                                <th scope="col">STORE NAME</th>-->
                                                            <!--                                                                <th scope="col">PHONE NO.</th>-->
                                                            <!--                                                                <th scope="col">TOTAL ORDERS</th>-->
                                                            <!--                                                                <th scope="col">TOTAL PROFITS</th>-->
                                                            <th scope="col">#</th>
                                                            <th scope="col">اسم المتجر</th>
                                                            <th scope="col">التصنيف</th>
                                                            <th scope="col">الماركة</th>
                                                            <th scope="col">اللون</th>
                                                            <th scope="col">السعر</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <th scope="row">1</th>
                                                            <td>
                                                                <a href="#">
                                                                    LC Waikiki Store
                                                                </a>
                                                            </td>
                                                            <td class="phone">ملابس رجالي</td>
                                                            <td class="phone">Max</td>
                                                            <td class="phone">اسود</td>
                                                            <td class="browser">200 LE</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">2</th>
                                                            <td>
                                                                <a href="#">
                                                                    LC Waikiki Store
                                                                </a>
                                                            </td>
                                                            <td class="phone">ملابس رجالي</td>
                                                            <td class="phone">Max</td>
                                                            <td class="phone">اسود</td>
                                                            <td class="browser">200 LE</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">3</th>
                                                            <td>
                                                                <a href="#">
                                                                    LC Waikiki Store
                                                                </a>
                                                            </td>
                                                            <td class="phone">ملابس رجالي</td>
                                                            <td class="phone">Max</td>
                                                            <td class="phone">اسود</td>
                                                            <td class="browser">200 LE</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">4</th>
                                                            <td>
                                                                <a href="#">
                                                                    LC Waikiki Store
                                                                </a>
                                                            </td>
                                                            <td class="phone">ملابس رجالي</td>
                                                            <td class="phone">Max</td>
                                                            <td class="phone">اسود</td>
                                                            <td class="browser">200 LE</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">5</th>
                                                            <td>
                                                                <a href="#">
                                                                    LC Waikiki Store
                                                                </a>
                                                            </td>
                                                            <td class="phone">ملابس رجالي</td>
                                                            <td class="phone">Max</td>
                                                            <td class="phone">اسود</td>
                                                            <td class="browser">200 LE</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xxl-4 col-md-12">
                                    <div class="row">
                                        <div class="col-xxl-12 col-md-12">
                                            <div class="card">
                                                <div class="card-header border-0 p-2">
                                                    <p style="font-weight: 500;"
                                                       class="card-title mb-0 px-2 pt-4 flex-grow-1">
                                                        افضل <span class="text-red"> 10 </span> تصنيفات
                                                    </p>
                                                </div>
                                                <div class="card-body statics-home m-0 pt-0 ">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <a class="text-muted d-flex align-items-center" href="#">
                                                            <i class="ph-t-shirt-fill text-warning me-2"></i>
                                                            LC Waikiki Store
                                                        </a>
                                                        <div class="text-muted">
                                                            Woman`s Fashion & Accessories
                                                        </div>
                                                        <div class="slider-number">
                                                            Percentage Of Orders <span class="text-orange">80%</span>
                                                        </div>
                                                        <div style="width: 25%">
                                                            <div data-rangeslider class="orange-slder"></div>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <a class="text-muted d-flex align-items-center" href="#">
                                                            <i class="ph-t-shirt-fill text-warning me-2"></i>
                                                            LC Waikiki Store
                                                        </a>
                                                        <div class="text-muted">
                                                            Woman`s Fashion & Accessories
                                                        </div>
                                                        <div class="slider-number">
                                                            Percentage Of Orders <span class="text-orange">80%</span>
                                                        </div>
                                                        <div style="width: 25%">
                                                            <div data-rangeslider class="orange-slder"></div>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <a class="text-muted d-flex align-items-center" href="#">
                                                            <i class="ph-t-shirt-fill text-warning me-2"></i>
                                                            LC Waikiki Store
                                                        </a>
                                                        <div class="text-muted">
                                                            Woman`s Fashion & Accessories
                                                        </div>
                                                        <div class="slider-number">
                                                            Percentage Of Orders <span class="text-orange">80%</span>
                                                        </div>
                                                        <div style="width: 25%">
                                                            <div data-rangeslider class="orange-slder"></div>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <a class="text-muted d-flex align-items-center" href="#">
                                                            <i class="ph-t-shirt-fill text-warning me-2"></i>
                                                            LC Waikiki Store
                                                        </a>
                                                        <div class="text-muted">
                                                            Woman`s Fashion & Accessories
                                                        </div>
                                                        <div class="slider-number">
                                                            Percentage Of Orders <span class="text-orange">80%</span>
                                                        </div>
                                                        <div style="width: 25%">
                                                            <div data-rangeslider class="orange-slder"></div>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <a class="text-muted d-flex align-items-center" href="#">
                                                            <i class="ph-t-shirt-fill text-warning me-2"></i>
                                                            LC Waikiki Store
                                                        </a>
                                                        <div class="text-muted">
                                                            Woman`s Fashion & Accessories
                                                        </div>
                                                        <div class="slider-number">
                                                            Percentage Of Orders <span class="text-orange">80%</span>
                                                        </div>
                                                        <div style="width: 25%">
                                                            <div data-rangeslider class="orange-slder"></div>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <a class="text-muted d-flex align-items-center" href="#">
                                                            <i class="ph-t-shirt-fill text-warning me-2"></i>
                                                            LC Waikiki Store
                                                        </a>
                                                        <div class="text-muted">
                                                            Woman`s Fashion & Accessories
                                                        </div>
                                                        <div class="slider-number">
                                                            Percentage Of Orders <span class="text-orange">80%</span>
                                                        </div>
                                                        <div style="width: 25%">
                                                            <div data-rangeslider class="orange-slder"></div>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <a class="text-muted d-flex align-items-center" href="#">
                                                            <i class="ph-t-shirt-fill text-warning me-2"></i>
                                                            LC Waikiki Store
                                                        </a>
                                                        <div class="text-muted">
                                                            Woman`s Fashion & Accessories
                                                        </div>
                                                        <div class="slider-number">
                                                            Percentage Of Orders <span class="text-orange">80%</span>
                                                        </div>
                                                        <div style="width: 25%">
                                                            <div data-rangeslider class="orange-slder"></div>
                                                        </div>
                                                    </div>


                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <a class="text-muted d-flex align-items-center" href="#">
                                                            <i class="ph-t-shirt-fill text-warning me-2"></i>
                                                            LC Waikiki Store
                                                        </a>
                                                        <div class="text-muted">
                                                            Woman`s Fashion & Accessories
                                                        </div>
                                                        <div class="slider-number">
                                                            Percentage Of Orders <span class="text-orange">80%</span>
                                                        </div>
                                                        <div style="width: 25%">
                                                            <div data-rangeslider class="orange-slder"></div>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <a class="text-muted d-flex align-items-center" href="#">
                                                            <i class="ph-t-shirt-fill text-warning me-2"></i>
                                                            LC Waikiki Store
                                                        </a>
                                                        <div class="text-muted">
                                                            Woman`s Fashion & Accessories
                                                        </div>
                                                        <div class="slider-number">
                                                            Percentage Of Orders <span class="text-orange">80%</span>
                                                        </div>
                                                        <div style="width: 25%">
                                                            <div data-rangeslider class="orange-slder"></div>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <a class="text-muted d-flex align-items-center" href="#">
                                                            <i class="ph-t-shirt-fill text-warning me-2"></i>
                                                            LC Waikiki Store
                                                        </a>
                                                        <div class="text-muted">
                                                            Woman`s Fashion & Accessories
                                                        </div>
                                                        <div class="slider-number">
                                                            Percentage Of Orders <span class="text-orange">80%</span>
                                                        </div>
                                                        <div style="width: 25%">
                                                            <div data-rangeslider class="orange-slder"></div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xxl-12">
                                            <div class="card">
                                                <div class="card-header border-0 p-2">
                                                    <p style="font-weight: 500;"
                                                       class="card-title mb-0 px-2 pt-2 flex-grow-1">
                                                        اكثر <span class="text-red"> 5 </span> منتجات مبيعا
                                                        <small>15 الف متجر</small>
                                                    </p>
                                                </div>
                                                <div class="card-body px-0">
                                                    <div data-simplebar>
                                                        <div class="card rounded-0 border-0 shadow-none mb-0">
                                                            <div class="card-body pt-0 d-flex align-items-center justify-content-between">
                                                                <div class="d-flex gap-2">
                                                                    <div class="flex-shrink-0">
                                                                        <img src="{{ asset('admin') }}/assets/images/brands/max.png"
                                                                             alt=""
                                                                             class="avatar-sm rounded-circle border shadow-lg">
                                                                    </div>
                                                                </div>
                                                                <div style="width: 80%" class="price fw-bold text-red">
                                                                    <div id="total_income" data-colors='["--tb-success"]'
                                                                         class="apex-charts total_income" dir="ltr"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!--end card-->
                                        </div>
                                        <div class="col-md-6 col-xxl-12">
                                            <div class="card">
                                                <div class="card-header border-0 p-2">
                                                    <p style="font-weight: 500;"
                                                       class="card-title mb-0 px-2 pt-2 flex-grow-1">
                                                        نسبة الخصومات
                                                        <small>15 الف متجر</small>
                                                    </p>
                                                </div>
                                                <div class="card-body px-0">
                                                    <div data-simplebar>
                                                        <div class="card rounded-0 border-0 shadow-none mb-0">
                                                            <div class="card-body pt-0 d-flex align-items-center justify-content-between">
                                                                <div id="pattern_chart" data-colors='["--tb-primary", "--tb-success", "--tb-warning", "--tb-danger", "--tb-info"]' class="apex-charts" dir="ltr"></div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!--end card-->
                                        </div>
                                        <div class="col-md-12">
                                            <div class="card" id="propertyList">
                                                <div class="card-header align-items-center d-flex">
                                                    <h4 class="card-title mb-0 flex-grow-1 d-flex align-items-center">
                                                        <i class=" ph-fire-light text-danger"></i>
                                                        Trending Products
                                                    </h4>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="user_example" class="dataTable row-border"
                                                               style="width:100%">
                                                            <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>اسم المنتج</th>
                                                                <th>اسم البراند</th>
                                                                <th>اللون</th>
                                                                <th>سعر المنتج</th>
                                                                <th>عدد الزيارات</th>
                                                                <th>عدد مرات الشراء</th>

                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <tr>
                                                                <td>1</td>
                                                                <td>اسم المنتج</td>
                                                                <td>سم البراند</td>
                                                                <td>
                                                                    <div style="width: 20px;height: 20px;background: red;border-radius: 50px;display:inline-block"></div>
                                                                </td>
                                                                <td>22 LE</td>
                                                                <td>2000</td>
                                                                <td>200</td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!--end col-->
                                    </div>
                                </div><!--end col-->



                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- container-fluid -->
    </div>
    <!-- End Page-content -->
    <!-- End Page-content -->
    <footer class="footer d-none">
    </footer>
@endsection
@section('backend-footer')
    <!-- echarts js -->
    <script src="{{ asset('admin') }}/assets/libs/echarts/echarts.min.js"></script>

    <!-- apexcharts -->
    <script src="{{ asset('admin') }}/assets/libs/apexcharts/apexcharts.min.js"></script>


    <!-- linecharts init -->
    <script src="{{ asset('admin') }}/assets/js/pages/apexcharts-line.init.js"></script>
    <!-- radialbar charts init -->
    <!--<script src="{{ asset('admin') }}/assets/js/pages/apexcharts-radialbar.init.js"></script>-->

    <script src="{{ asset('admin') }}/assets/js/pages/dashboard-real-estate.init.js"></script>
    <script src="{{ asset('admin') }}/assets/js/pages/apexcharts-pie.init.js"></script>
    <!-- piecharts init -->
    <!--<script src="{{ asset('admin') }}/assets/js/pages/apexcharts-pie.init.js"></script>-->
    <script src="{{ asset('admin') }}/assets/js/pages/apexcharts-radialbar.init.js"></script>


@endsection
