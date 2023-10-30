<?php
    $active = '';
    if(Request::is('admin_panel/settings/brands')){
        $active = 'brands';
    }
    if(Request::is('admin_panel/settings/colors')){
        $active = 'colors';
    }
    if(Request::is('admin_panel/settings/sizes')){
        $active = 'sizes';
    }
    if(Request::is('admin_panel/settings/categories')){
        $active = 'categories';
    }
    if(Request::is('admin_panel/settings/subcategories')){
        $active = 'subcategories';
    }
    if(Request::is('admin_panel/settings/subsubcategories')){
        $active = 'subsubcategories';
    }
    if(Request::is('admin_panel/settings/categories_trees')){
        $active = 'categories_trees';
    }
    if(Request::is('admin_panel/settings/materials')){
        $active = 'materials';
    }
?>
<div id="dashboard_sidebar">
    <div class="container-fluid p-0">
        <ul class="navbar-nav">
{{--            <li class="nav-item">--}}
{{--                <a href="#ll" class="nav-link menu-link"--}}
{{--                   data-bs-toggle="collapse"--}}
{{--                   role="button" aria-expanded="false" aria-controls="users">--}}
{{--                    <i class="ph-record-fill"></i> <span data-key="t-layouts">معلومات رئيسية</span>--}}
{{--                    <i class="ri-arrow-left-s-fill fs-5"></i>--}}
{{--                </a>--}}
{{--                <div class="collapse menu-dropdown" id="ll">--}}
{{--                    <ul class="nav nav-sm flex-column">--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="settings-lang.html" class="nav-link "--}}
{{--                               data-key="t-products">--}}
{{--                                <i class="bx bxs-circle"></i>--}}
{{--                                معلومات لوحة التحكم--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="settings-country.html" class="nav-link "--}}
{{--                               data-key="t-products-grid">--}}
{{--                                <i class="bx bxs-circle"></i>--}}
{{--                                اعدادات الموقع الجغرافي--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="settings-policy.html" class="nav-link"--}}
{{--                               data-key="t-products-grid">--}}
{{--                                <i class="bx bxs-circle"></i>--}}
{{--                                سياسة الخصوصية--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="settings-category.html" class="nav-link"--}}
{{--                               data-key="t-products-grid">--}}
{{--                                <i class="bx bxs-circle"></i>--}}
{{--                                اعدادات التصنيفات--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="slider.html" class="nav-link"--}}
{{--                               data-key="t-products-grid">--}}
{{--                                <i class="bx bxs-circle"></i>--}}
{{--                                اعدادات سلايدر الصور--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    </ul>--}}
{{--                </div>--}}
{{--            </li>--}}
{{--            <li class="nav-item">--}}
{{--                <a href="settings-contacts.html" class="nav-link menu-link collapsed">--}}
{{--                    <i class="ph-record-fill"></i> <span data-key="t-layouts">الدعم الفني</span>--}}
{{--                </a>--}}
{{--            </li>--}}

{{--            <li class="nav-item">--}}
{{--                <a href="#purchase" class="nav-link menu-link collapsed"--}}
{{--                   data-bs-toggle="collapse"--}}
{{--                   role="button" aria-expanded="false" aria-controls="purchase">--}}
{{--                    <i class="ph-record-fill"></i> <span data-key="t-layouts">المنتجات</span>--}}
{{--                    <i class="ri-arrow-left-s-fill fs-5"></i>--}}
{{--                </a>--}}
{{--                <div class="collapse menu-dropdown" id="purchase">--}}
{{--                    <ul class="nav nav-sm flex-column">--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="settings-color.html" class="nav-link" data-key="t-products">--}}
{{--                                <i class="bx bx-circle"></i>--}}
{{--                                الوان المنتج--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="settings-packing.html" class="nav-link" data-key="t-products">--}}
{{--                                <i class="bx bx-circle"></i>--}}
{{--                                انواع تغليف المنتج--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    </ul>--}}
{{--                </div>--}}
{{--            </li>--}}
            <li class="nav-item">
                <a href="#brands" class="nav-link menu-link {{ $active == 'brands' ? '' : 'collapsed' }}"
                   data-bs-toggle="collapse"
                   role="button" aria-expanded="false" aria-controls="purchase">
                    <i class="ph-record-fill"></i> <span data-key="t-layouts">البراندات</span>
                    <i class="ri-arrow-left-s-fill fs-5"></i>
                </a>
                <div class="collapse menu-dropdown {{ $active == 'brands' ? 'show' : '' }}" id="brands">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a href="{{ url('admin_panel/settings/brands') }}" class="nav-link {{ $active == 'brands' && request()->type != 'archived' ? 'active' : '' }}" data-key="t-brands">
                                <i class="bx bx-circle"></i>
                                البراندات
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('admin_panel/settings/brands?type=archived') }}" class="nav-link {{ $active == 'brands' && request()->type == 'archived' ? 'active' : '' }}" data-key="t-brands">
                                <i class="bx bx-circle"></i>
                                البراندات المؤرشفة
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a href="#materilas" class="nav-link menu-link {{ $active == 'materials' ? '' : 'collapsed' }}"
                   data-bs-toggle="collapse"
                   role="button" aria-expanded="false" aria-controls="purchase">
                    <i class="ph-record-fill"></i> <span data-key="t-layouts">الخامات</span>
                    <i class="ri-arrow-left-s-fill fs-5"></i>
                </a>
                <div class="collapse menu-dropdown {{ $active == 'materials' ? 'show' : '' }}" id="materilas">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a href="{{ url('admin_panel/settings/materials') }}" class="nav-link {{ $active == 'materials' && request()->type != 'archived' ? 'active' : '' }}" data-key="t-materilas">
                                <i class="bx bx-circle"></i>
                                الخامات
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('admin_panel/settings/materials?type=archived') }}" class="nav-link {{ $active == 'materials' && request()->type == 'archived' ? 'active' : '' }}" data-key="t-materilas">
                                <i class="bx bx-circle"></i>
                                الخامات المؤرشفة
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a href="#colors" class="nav-link menu-link {{ $active == 'colors' ? '' : 'collapsed' }}"
                   data-bs-toggle="collapse"
                   role="button" aria-expanded="false" aria-controls="purchase">
                    <i class="ph-record-fill"></i> <span data-key="t-layouts">الالوان</span>
                    <i class="ri-arrow-left-s-fill fs-5"></i>
                </a>
                <div class="collapse menu-dropdown {{ $active == 'colors' ? 'show' : '' }}" id="colors">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a href="{{ url('admin_panel/settings/colors') }}" class="nav-link {{ $active == 'colors' && request()->type != 'archived' ? 'active' : '' }}" data-key="t-brands">
                                <i class="bx bx-circle"></i>
                                الالوان
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('admin_panel/settings/colors?type=archived') }}" class="nav-link {{ $active == 'colors' && request()->type == 'archived' ? 'active' : '' }}" data-key="t-brands">
                                <i class="bx bx-circle"></i>
                                الالوان المؤرشفة
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a href="#sizes" class="nav-link menu-link {{ $active == 'sizes' ? '' : 'collapsed' }}"
                   data-bs-toggle="collapse"
                   role="button" aria-expanded="false" aria-controls="purchase">
                    <i class="ph-record-fill"></i> <span data-key="t-layouts">المقاسات</span>
                    <i class="ri-arrow-left-s-fill fs-5"></i>
                </a>
                <div class="collapse menu-dropdown {{ $active == 'sizes' ? 'show' : '' }}" id="sizes">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a href="{{ url('admin_panel/settings/sizes') }}" class="nav-link {{ $active == 'sizes' && request()->type != 'archived' ? 'active' : '' }}" data-key="t-sizes">
                                <i class="bx bx-circle"></i>
                                المقاسات
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('admin_panel/settings/sizes?type=archived') }}" class="nav-link {{ $active == 'sizes' && request()->type == 'archived' ? 'active' : '' }}" data-key="t-sizes">
                                <i class="bx bx-circle"></i>
                                المقاسات المؤرشفة
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
{{--            <li class="nav-item">--}}
{{--                <a href="#categories" class="nav-link menu-link {{ $active == 'sizes' ? '' : 'collapsed' }}"--}}
{{--                   data-bs-toggle="collapse"--}}
{{--                   role="button" aria-expanded="false" aria-controls="purchase">--}}
{{--                    <i class="ph-record-fill"></i> <span data-key="t-layouts">التصنيفات</span>--}}
{{--                    <i class="ri-arrow-left-s-fill fs-5"></i>--}}
{{--                </a>--}}
{{--                <div class="collapse menu-dropdown {{ $active == 'categories' || $active == 'subcategories' || $active == 'subsubcategories' || $active == 'subsubcategories' ? 'show' : '' }}" id="categories">--}}
{{--                    <ul class="nav nav-sm flex-column">--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="{{ url('admin_panel/settings/categories') }}" class="nav-link {{ $active == 'categories' && request()->type != 'archived' ? 'active' : '' }}" data-key="t-categories">--}}
{{--                                <i class="bx bx-circle"></i>--}}
{{--                                التصنيفات الرئيسية--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="{{ url('admin_panel/settings/subcategories?type=archived') }}" class="nav-link {{ $active == 'subcategories' && request()->type == 'archived' ? 'active' : '' }}" data-key="t-categories">--}}
{{--                                <i class="bx bx-circle"></i>--}}
{{--                                التصنيفات الفرعية--}}
{{--                            </a>--}}
{{--                        </li><li class="nav-item">--}}
{{--                            <a href="{{ url('admin_panel/settings/subsubcategories?type=archived') }}" class="nav-link {{ $active == 'subsubcategories' && request()->type == 'archived' ? 'active' : '' }}" data-key="t-categories">--}}
{{--                                <i class="bx bx-circle"></i>--}}
{{--                                التصنيفات الفرعية الثانية--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="{{ url('admin_panel/settings/categories_trees') }}" class="nav-link {{ $active == 'categories_trees' && request()->type == 'archived' ? 'active' : '' }}" data-key="t-categories">--}}
{{--                                <i class="bx bx-circle"></i>--}}
{{--                                شجره التصنيفات--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    </ul>--}}
{{--                </div>--}}
{{--            </li>--}}
{{--            <li class="nav-item">--}}
{{--                <a href="#sales" class="nav-link menu-link collapsed"--}}
{{--                   data-bs-toggle="collapse"--}}
{{--                   role="button" aria-expanded="false" aria-controls="sales">--}}
{{--                    <i class="ph-record-fill"></i> <span data-key="t-layouts">النقاط</span>--}}
{{--                    <i class="ri-arrow-left-s-fill fs-5"></i>--}}
{{--                </a>--}}
{{--                <div class="collapse menu-dropdown" id="sales">--}}
{{--                    <ul class="nav nav-sm flex-column">--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="settings-points.html" class="nav-link" data-key="t-products">--}}
{{--                                <i class="bx bx-circle"></i>--}}
{{--                                نقاط الشراء--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="settings-brand-points.html" class="nav-link" data-key="t-products">--}}
{{--                                <i class="bx bx-circle"></i>--}}
{{--                                نقاط البرندات--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    </ul>--}}
{{--                </div>--}}
{{--            </li>--}}
{{--            <li class="nav-item">--}}
{{--                <a href="#messages" class="nav-link menu-link collapsed"--}}
{{--                   data-bs-toggle="collapse"--}}
{{--                   role="button" aria-expanded="false" aria-controls="messages">--}}
{{--                    <i class="ph-record-fill"></i> <span data-key="t-layouts">البيع </span>--}}
{{--                    <i class="ri-arrow-left-s-fill fs-5"></i>--}}
{{--                </a>--}}
{{--                <div class="collapse menu-dropdown" id="messages">--}}
{{--                    <ul class="nav nav-sm flex-column">--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="offline-order.html" class="nav-link" data-key="t-products">--}}
{{--                                <i class="bx bx-circle"></i>--}}
{{--                                البيع اونلاين--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="offline-order.html" class="nav-link" data-key="t-products">--}}
{{--                                <i class="bx bx-circle"></i>--}}
{{--                                البيع اوفلاين--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    </ul>--}}
{{--                </div>--}}
{{--            </li>--}}
{{--            <li class="nav-item">--}}
{{--                <a href="#payment_methods" class="nav-link menu-link collapsed"--}}
{{--                   data-bs-toggle="collapse"--}}
{{--                   role="button" aria-expanded="false" aria-controls="payment_methods">--}}
{{--                    <i class="ph-record-fill"></i> <span data-key="t-layouts">وسائل الدفع </span>--}}
{{--                    <i class="ri-arrow-left-s-fill fs-5"></i>--}}
{{--                </a>--}}
{{--                <div class="collapse menu-dropdown" id="payment_methods">--}}
{{--                    <ul class="nav nav-sm flex-column">--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="settings-payment-methods.html" class="nav-link" data-key="t-products">--}}
{{--                                <i class="bx bx-circle"></i>--}}
{{--                                وسائل الدفع--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    </ul>--}}
{{--                </div>--}}
{{--            </li>--}}
{{--            <li class="nav-item">--}}
{{--                <a href="#sms" class="nav-link menu-link collapsed"--}}
{{--                   data-bs-toggle="collapse"--}}
{{--                   role="button" aria-expanded="false" aria-controls="sms">--}}
{{--                    <i class="ph-record-fill"></i> <span data-key="t-layouts">البريد و الرسائل القصيرة</span>--}}
{{--                    <i class="ri-arrow-left-s-fill fs-5"></i>--}}
{{--                </a>--}}
{{--                <div class="collapse menu-dropdown" id="sms">--}}
{{--                    <ul class="nav nav-sm flex-column">--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="#" class="nav-link" data-key="t-products">--}}
{{--                                <i class="bx bx-circle"></i>--}}
{{--                                البريد الاكتروني--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="#" class="nav-link"--}}
{{--                               data-key="t-products-grid">--}}
{{--                                <i class="bx bx-circle"></i>--}}
{{--                                بوابات الرسائل القصيرة--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    </ul>--}}
{{--                </div>--}}
{{--            </li>--}}

{{--            <li class="nav-item">--}}
{{--                <a href="settings-backup.html" class="nav-link menu-link collapsed">--}}
{{--                    <i class="ph-record-fill"></i> <span data-key="t-layouts">حفظ نسخة احتياطية</span>--}}
{{--                </a>--}}
{{--            </li>--}}

{{--            <li class="nav-item">--}}
{{--                <a href="#google" class="nav-link menu-link collapsed"--}}
{{--                   data-bs-toggle="collapse"--}}
{{--                   role="button" aria-expanded="false" aria-controls="google">--}}
{{--                    <i class="ph-record-fill"></i> <span data-key="t-layouts">خدمات جوجل</span>--}}
{{--                    <i class="ri-arrow-left-s-fill fs-5"></i>--}}
{{--                </a>--}}
{{--                <div class="collapse menu-dropdown" id="google">--}}
{{--                    <ul class="nav nav-sm flex-column">--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="#" class="nav-link" data-key="t-products">--}}
{{--                                <i class="bx bx-circle"></i>--}}
{{--                                خريطة جوجل--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="#" class="nav-link"--}}
{{--                               data-key="t-products-grid">--}}
{{--                                <i class="bx bx-circle"></i>--}}
{{--                                اشعارات--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    </ul>--}}
{{--                </div>--}}
{{--            </li>--}}


        </ul>
    </div>
    <!-- Sidebar -->
</div>
