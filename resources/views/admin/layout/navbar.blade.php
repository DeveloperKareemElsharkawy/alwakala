<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="#" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset('admin') }}/assets/images/logo-sm.png" alt="" height="20">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('admin') }}/assets/images/logo-dark.png" alt="" height="70">
            </span>
        </a>
        <a href="#" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ asset('admin') }}/assets/images/logo-sm.png" alt="" height="20">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('admin') }}/assets/images/logo-light.png" alt="" height="70">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-3xl header-item float-end btn-vertical-sm-hover"
                id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
{{--                <li class="nav-item">--}}
{{--                    <a class="nav-link menu-link" href="index.html">--}}
{{--                        <i class="bx bxs-home"></i> <span data-key="t-dashboards">لوحة التحكم</span>--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--                <li class="nav-item">--}}
{{--                    <a href="#roles" class="nav-link menu-link collapsed" data-bs-toggle="collapse"--}}
{{--                       role="button" aria-expanded="false" aria-controls="users">--}}
{{--                        <i class="bx bxs-badge-check"></i> <span data-key="t-layouts">الصلاحيات والادوار</span>--}}
{{--                    </a>--}}
{{--                    <div class="collapse menu-dropdown" id="roles">--}}
{{--                        <ul class="nav nav-sm flex-column">--}}
{{--                            <li class="nav-item">--}}
{{--                                <a href="new-role-pop.html" class="nav-link" data-key="t-products">اضافة دور جديد</a>--}}
{{--                            </li>--}}
{{--                            <li class="nav-item">--}}
{{--                                <a href="all-roles.html" class="nav-link"--}}
{{--                                   data-key="t-products-grid">كل الادوار</a>--}}
{{--                            </li>--}}
{{--                            <li class="nav-item">--}}
{{--                                <a href="archived-role.html" class="nav-link"--}}
{{--                                   data-key="t-product-Details">الادوار المؤرشفة</a>--}}
{{--                            </li>--}}
{{--                        </ul>--}}
{{--                    </div>--}}
{{--                </li>--}}

{{--                <li class="nav-item">--}}
{{--                    <a href="slider.html" class="nav-link menu-link collapsed">--}}
{{--                        <i class="mdi mdi-image-multiple-outline"></i> <span data-key="t-ecommerce">سلايدر الصور</span>--}}
{{--                    </a>--}}
{{--                </li>--}}

                <li class="nav-item">
                    <a href="#users" class="nav-link menu-link collapsed {{ Request::is('admin_panel/users') || Request::is('admin_panel/users/*') ? 'active' : '' }}" data-bs-toggle="collapse"
                       role="button" aria-expanded="{{ Request::is('admin_panel/users') || Request::is('admin_panel/users/*') ? 'true' : 'false' }}" aria-controls="users">
                        <i class="bx bxs-group"></i> <span data-key="t-ecommerce">المستخدمين</span>
                    </a>
                    <div class="collapse menu-dropdown {{ Request::is('admin_panel/users') || Request::is('admin_panel/users/*') ? 'show' : '' }}" id="users">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ url('admin_panel/users?add_new=true') }}" class="nav-link {{Request::is('admin_panel/users') && request()->add_new == 'true' ? 'active' : '' }}" data-key="t-products">اضافة مستخدم جديد</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('admin_panel/users?type=true') }}" class="nav-link {{ (Request::is('admin_panel/users') && request()->type != 'false' && request()->add_new != 'true') ||  (Request::is('admin_panel/users') && request()->type == 'true' ) ? 'active' : '' }}"
                                   data-key="t-products-grid">كل المستخدمين المفعلين</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('admin_panel/users?type=false') }}" class="nav-link {{Request::is('admin_panel/users') && request()->type == 'false' ? 'active' : '' }}"
                                   data-key="t-product-Details">ارشيف المستخدمين</a>
                            </li>
                        </ul>
                    </div>
                </li>
{{--                <li class="nav-item">--}}
{{--                    <a href="#consumers" class="nav-link menu-link collapsed" data-bs-toggle="collapse"--}}
{{--                       role="button" aria-expanded="false" aria-controls="consumers">--}}
{{--                        <i class="bx bxs-group"></i> <span data-key="t-ecommerce">المستهلكين</span>--}}
{{--                    </a>--}}
{{--                    <div class="collapse menu-dropdown" id="consumers">--}}
{{--                        <ul class="nav nav-sm flex-column">--}}
{{--                            <li class="nav-item">--}}
{{--                                <a href="all-consumers-card-pop.html" class="nav-link" data-key="t-products">اضافة مستهلك جديد</a>--}}
{{--                            </li>--}}
{{--                            <li class="nav-item">--}}
{{--                                <a href="all-consumers-card.html" class="nav-link"--}}
{{--                                   data-key="t-products-grid">كل المستهلكين</a>--}}
{{--                            </li>--}}
{{--                        </ul>--}}
{{--                    </div>--}}
{{--                </li>--}}

                <li class="nav-item">
                    <a href="#suppliers" class="nav-link menu-link collapsed " data-bs-toggle="collapse"
                       role="button" aria-expanded="false" aria-controls="suppliers">
                        <i class="bi bi-box"></i> <span data-key="t-ecommerce">متاجر الجملة / براندات</span>
                    </a>
                    <div class="collapse  menu-dropdown" id="suppliers">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ url('admin_panel/vendors?add_new=true&type=supplier&active=true') }}" class="nav-link" data-key="t-products">اضافة متجر جديد</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('admin_panel/vendors?type=supplier&active=true&verified=false') }}" class="nav-link"
                                   data-key="t-products-grid">متاجر في انتظار الموافقة</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('admin_panel/vendors?type=supplier&active=true') }}" class="nav-link"
                                   data-key="t-product-Details">متاجر مفعلة</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('admin_panel/vendors?type=supplier&active=false&verified=false') }}" class="nav-link"
                                   data-key="t-product-Details">متاجر معطلة</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a href="#suppliersss" class="nav-link menu-link collapsed " data-bs-toggle="collapse"
                       role="button" aria-expanded="false" aria-controls="suppliersss">
                        <i class="bi bi-box"></i> <span data-key="t-ecommerce">متاجر القطاعي</span>
                    </a>
                    <div class="collapse  menu-dropdown" id="suppliersss">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ url('admin_panel/vendors?add_new=true&type=retail&active=true') }}" class="nav-link" data-key="t-products">اضافة متجر جديد</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('admin_panel/vendors?type=retail&active=true&verified=false') }}" class="nav-link"
                                   data-key="t-products-grid">متاجر في انتظار الموافقة</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('admin_panel/vendors?type=retail&active=true') }}" class="nav-link"
                                   data-key="t-product-Details">متاجر مفعلة</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('admin_panel/vendors?type=retail&active=false&verified=false') }}" class="nav-link"
                                   data-key="t-product-Details">متاجر معطلة</a>
                            </li>
                        </ul>
                    </div>
                </li>

{{--                <li class="nav-item">--}}
{{--                    <a href="#orders" class="nav-link menu-link collapsed " data-bs-toggle="collapse"--}}
{{--                       role="button" aria-expanded="false" aria-controls="orders">--}}
{{--                        <i class="bi bi-lock-fill"></i> <span data-key="t-ecommerce">الطلبات</span>--}}
{{--                    </a>--}}
{{--                    <div class="collapse  menu-dropdown" id="orders">--}}
{{--                        <ul class="nav nav-sm flex-column">--}}
{{--                            <li class="nav-item">--}}
{{--                                <a href="purchases-orders.html" class="nav-link" data-key="t-products">طلبات الشراء </a>--}}
{{--                            </li>--}}
{{--                            <li class="nav-item">--}}
{{--                                <a href="purchases-orders-seller.html" class="nav-link"--}}
{{--                                   data-key="t-products-grid">طلبات البيع</a>--}}
{{--                            </li>--}}
{{--                        </ul>--}}
{{--                    </div>--}}
{{--                </li>--}}

{{--                <li class="nav-item">--}}
{{--                    <a href="#ads" class="nav-link menu-link collapsed " data-bs-toggle="collapse"--}}
{{--                       role="button" aria-expanded="false" aria-controls="ads">--}}
{{--                        <i class="ri-advertisement-fill"></i> <span data-key="t-ecommerce">الاعلانات</span>--}}
{{--                    </a>--}}
{{--                    <div class="collapse  menu-dropdown" id="ads">--}}
{{--                        <ul class="nav nav-sm flex-column">--}}
{{--                            <li class="nav-item">--}}
{{--                                <a href="b2b-ads-system.html" class="nav-link" data-key="t-products">اعلانات تجار الجملة </a>--}}
{{--                            </li>--}}
{{--                            <li class="nav-item">--}}
{{--                                <a href="all-ads.html" class="nav-link"--}}
{{--                                   data-key="t-products-grid">اعلانات تجار القطاعي </a>--}}
{{--                            </li>--}}
{{--                        </ul>--}}
{{--                    </div>--}}
{{--                </li>--}}

{{--                <li class="nav-item">--}}
{{--                    <a href="#subscription" class="nav-link menu-link collapsed " data-bs-toggle="collapse"--}}
{{--                       role="button" aria-expanded="false" aria-controls="subscription">--}}
{{--                        <i class="bx bx-package"></i> <span data-key="t-ecommerce">الاشتراكات</span>--}}
{{--                    </a>--}}
{{--                    <div class="collapse  menu-dropdown" id="subscription">--}}
{{--                        <ul class="nav nav-sm flex-column">--}}
{{--                            <li class="nav-item">--}}
{{--                                <a href="all-subscriptions.html" class="nav-link" data-key="t-products">اشتراكات تجار الجملة </a>--}}
{{--                            </li>--}}
{{--                            <li class="nav-item">--}}
{{--                                <a href="all-subscriptions.html" class="nav-link"--}}
{{--                                   data-key="t-products-grid">اشتراكات تجار القطاعي </a>--}}
{{--                            </li>--}}
{{--                        </ul>--}}
{{--                    </div>--}}
{{--                </li>--}}

{{--                <li class="nav-item">--}}
{{--                    <a href="transactions.html" class="nav-link menu-link collapsed ">--}}
{{--                        <i class="bx bx-transfer-alt"></i> <span data-key="t-ecommerce">التحويلات</span>--}}
{{--                    </a>--}}
{{--                </li>--}}

{{--                <li class="nav-item">--}}
{{--                    <a href="settings-w-offers.html" class="nav-link menu-link collapsed">--}}
{{--                        <i class=" bx bxs-discount"></i> <span data-key="t-ecommerce">العروض</span>--}}
{{--                    </a>--}}
{{--                </li>--}}


{{--                <li class="nav-item">--}}
{{--                    <a href="refunds-orders.html" class="nav-link menu-link collapsed">--}}
{{--                        <i class="mdi mdi-archive-refresh-outline"></i> <span data-key="t-ecommerce">المرتجعات</span>--}}
{{--                    </a>--}}
{{--                </li>--}}

{{--                <li class="nav-item">--}}
{{--                    <a href="all-faqs.html" class="nav-link menu-link collapsed">--}}
{{--                        <i class="mdi mdi-account-question-outline"></i> <span data-key="t-ecommerce">الاسئلة الشائعة</span>--}}
{{--                    </a>--}}
{{--                </li>--}}

{{--                <li class="nav-item">--}}
{{--                    <a href="#settings" class="nav-link menu-link collapsed " data-bs-toggle="collapse"--}}
{{--                       role="button" aria-expanded="false" aria-controls="subscription">--}}
{{--                        <i class="ri-settings-5-fill"></i> <span data-key="t-ecommerce">الاعدادات</span>--}}
{{--                    </a>--}}
{{--                    <div class="collapse  menu-dropdown" id="settings">--}}
{{--                        <ul class="nav nav-sm flex-column">--}}
{{--                            <li class="nav-item">--}}
{{--                                <a href="settings-lang.html" class="nav-link" data-key="t-products">اعدادات رئيسية </a>--}}
{{--                            </li>--}}
{{--                            <li class="nav-item">--}}
{{--                                <a href="settings-w-coupons.html" class="nav-link"--}}
{{--                                   data-key="t-products-grid">اعدادات نظام الوكالة </a>--}}
{{--                            </li>--}}
{{--                        </ul>--}}
{{--                    </div>--}}
{{--                </li>--}}

            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
