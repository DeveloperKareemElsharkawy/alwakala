<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    <a href="#" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="{{ asset('admin') }}/assets/images/logo-sm.png" alt="" height="22">
                                </span>
                        <span class="logo-lg">
                                    <img src="{{ asset('admin') }}/assets/images/logo-dark.png" alt="" height="22">
                                </span>
                    </a>

                    <a href="#" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="{{ asset('admin') }}/assets/images/logo-sm.png" alt="" height="22">
                                </span>
                        <span class="logo-lg">
                                    <img src="{{ asset('admin') }}/assets/images/logo-light.png" alt="" height="22">
                                </span>
                    </a>
                </div>

                <button type="button"
                        class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger shadow-none"
                        id="topnav-hamburger-icon">
                            <span class="hamburger-icon">
                                <span></span>
                                <span></span>
                                <span></span>
                            </span>
                </button>

                <form class="app-search d-none d-md-inline-flex">
                    <div class="position-relative">
                        <input type="text" class="form-control" placeholder="كلمة البحث" autocomplete="off"
                               id="search-options" value="">
                        <span class="ri-search-line search-widget-icon"></span>
                        <span class="mdi mdi-close-circle search-widget-icon search-widget-icon-close d-none"
                              id="search-close-options"></span>
                    </div>
                </form>
            </div>

            <div class="d-flex align-items-center">


                <div class="dropdown ms-1 topbar-head-dropdown header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img id="header-lang-img" src="{{ asset('admin') }}/assets/images/flags/egy.svg" alt="Header Language"
                             height="20"
                             class="rounded">
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">

                        <!-- item-->
                        <a href="en/add-subscription.html" class="dropdown-item notify-item language py-2" data-lang="en"
                           title="English">
                            <img src="{{ asset('admin') }}/assets/images/flags/us.svg" alt="user-image" class="me-2 rounded" height="18">
                            <span class="align-middle">English</span>
                        </a>
                        <!-- item-->
                        <a href="#" class="dropdown-item notify-item language" data-lang="ar"
                           title="Arabic">
                            <img src="{{ asset('admin') }}/assets/images/flags/egy.svg" alt="user-image" class="me-2 rounded"
                                 height="18">
                            <span class="align-middle">عربي</span>
                        </a>
                    </div>
                </div>

                <div class="ms-1 header-item d-none d-sm-flex">
                    <a href="#" class="btn btn-icon btn-topbar btn-ghost-dark rounded-circle">
                        <i class='bi bi-chat-dots fs-3xl'></i>
                    </a>
                </div>

                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-dark rounded-circle"
                            data-toggle="fullscreen">
                        <i class='bi bi-arrows-fullscreen fs-lg'></i>
                    </button>
                </div>

                <div class="ms-1 header-item d-none d-sm-flex">
                    <a title="payment methods" href="payment-methods.html" class="btn btn-icon btn-topbar btn-ghost-dark rounded-circle">
                        <i class='bx bx-credit-card fs-3xl'></i>
                    </a>
                </div>

                <div class="dropdown topbar-head-dropdown ms-1 header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-dark rounded-circle mode-layout" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-sun align-middle fs-3xl"></i>
                    </button>
                    <div class="dropdown-menu p-2 dropdown-menu-end" id="light-dark-mode">
                        <a href="#!" class="dropdown-item" data-mode="light"><i class="bi bi-sun align-middle me-2"></i> light mode</a>
                        <a href="#!" class="dropdown-item" data-mode="dark"><i class="bi bi-moon align-middle me-2"></i> Dark</a>
                    </div>
                </div>

                <div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationDropdown">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-dark rounded-circle"
                            id="page-header-notifications-dropdown" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                        <i class='bi bi-bell fs-2xl'></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                         aria-labelledby="page-header-notifications-dropdown">

                        <div class="dropdown-head rounded-top">
                            <div class="p-3 border-bottom border-bottom-dashed">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="mb-0 fs-lg fw-semibold"> الاشعارات <span
                                                class="badge bg-danger-subtle text-danger fs-sm notification-badge"> 4</span>
                                        </h6>
                                        <p class="fs-md text-muted mt-1 mb-0">لديك عدد <span
                                                class="fw-semibold notification-unread">3</span> غير مقروؤة </p>
                                    </div>
                                    <div class="col-auto dropdown">
                                        <a href="javascript:void(0);" data-bs-toggle="dropdown"
                                           class="link-secondary fs-md"><i
                                                class="bi bi-three-dots-vertical"></i></a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#">حذف الكل</a></li>
                                            <li><a class="dropdown-item" href="#">قراءة الكل</a></li>
                                            <li><a class="dropdown-item" href="#">ارشفة الكل</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="py-2 ps-2" id="notificationItemsTabContent">
                            <div data-simplebar style="max-height: 300px;" class="pe-2">
                                <h6 class="text-overflow text-muted fs-sm my-2 text-uppercase notification-title">
                                    جديد</h6>

                                <div class="text-reset notification-item d-block dropdown-item position-relative unread-message">
                                    <div class="d-flex">
                                        <div class="position-relative me-3 flex-shrink-0">
                                            <img src="{{ asset('admin') }}/assets/images/users/32/avatar-1.jpg"
                                                 class="rounded-circle avatar-xs" alt="user-pic">
                                            <span class="active-badge position-absolute start-100 translate-middle p-1 bg-success rounded-circle">
                                                        <span class="visually-hidden"></span>
                                                    </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <a href="#!" class="stretched-link">
                                                <h6 class="mt-0 mb-1 fs-md fw-semibold">اسم المستخدم</h6>
                                            </a>
                                            <div class="fs-sm text-muted">
                                                <p class="mb-1">عملية شراء جديدة</p>
                                            </div>
                                            <p class="mb-0 fs-2xs fw-medium text-uppercase text-muted">
                                                <span><i class="mdi mdi-clock-outline"></i> 48 منذ</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-reset notification-item d-block dropdown-item position-relative unread-message">
                                    <div class="d-flex">
                                        <div class="position-relative me-3 flex-shrink-0">
                                            <img src="{{ asset('admin') }}/assets/images/users/32/avatar-1.jpg"
                                                 class="rounded-circle avatar-xs" alt="user-pic">
                                            <span class="active-badge position-absolute start-100 translate-middle p-1 bg-success rounded-circle">
                                                        <span class="visually-hidden"></span>
                                                    </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <a href="#!" class="stretched-link">
                                                <h6 class="mt-0 mb-1 fs-md fw-semibold">اسم المستخدم</h6>
                                            </a>
                                            <div class="fs-sm text-muted">
                                                <p class="mb-1">عملية شراء جديدة</p>
                                            </div>
                                            <p class="mb-0 fs-2xs fw-medium text-uppercase text-muted">
                                                <span><i class="mdi mdi-clock-outline"></i> 48 منذ</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-reset notification-item d-block dropdown-item position-relative unread-message">
                                    <div class="d-flex">
                                        <div class="position-relative me-3 flex-shrink-0">
                                            <img src="{{ asset('admin') }}/assets/images/users/32/avatar-1.jpg"
                                                 class="rounded-circle avatar-xs" alt="user-pic">
                                            <span class="active-badge position-absolute start-100 translate-middle p-1 bg-success rounded-circle">
                                                        <span class="visually-hidden"></span>
                                                    </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <a href="#!" class="stretched-link">
                                                <h6 class="mt-0 mb-1 fs-md fw-semibold">اسم المستخدم</h6>
                                            </a>
                                            <div class="fs-sm text-muted">
                                                <p class="mb-1">عملية شراء جديدة</p>
                                            </div>
                                            <p class="mb-0 fs-2xs fw-medium text-uppercase text-muted">
                                                <span><i class="mdi mdi-clock-outline"></i> 48 منذ</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button type="button" class="btn shadow-none" id="page-header-user-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="d-flex align-items-center">
                                    <img class="rounded-circle header-profile-user"
                                         src="{{ asset('admin') }}/assets/images/users/32/avatar-1.jpg" alt="Header Avatar">
                                    <span class="text-start ms-xl-2">
                                        <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">Toqa Elbauomy</span>
                                        <i class=" ri-arrow-down-s-line"></i>
                                    </span>
                                </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <h6 class="dropdown-header">مرحبا Toqa</h6>
                        <a class="dropdown-item" href="#"><i
                                class="mdi mdi-account-circle text-muted fs-lg align-middle me-1"></i> <span
                                class="align-middle">الصفحة الشخصية</span></a>
                        <a class="dropdown-item" href="#"><i
                                class="mdi mdi-cog-outline text-muted fs-lg align-middle me-1"></i> <span
                                class="align-middle">الاعدادات</span></a>
                        <a class="dropdown-item" href="auth-signin.html"><i
                                class="mdi mdi-logout text-muted fs-lg align-middle me-1"></i> <span
                                class="align-middle" data-key="t-logout">تسجيل خروج</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
