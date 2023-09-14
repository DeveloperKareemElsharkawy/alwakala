<!doctype html>
<html lang="en" data-layout="vertical" data-sidebar="dark" data-sidebar-size="lg" data-preloader="disable"
      data-theme="modern" data-topbar="light" data-bs-theme="light" dir="rtl">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ trans('admin.wekala') }} | @yield('pageTitle')</title>
    @include('admin.layout.head')
</head>

<body data-sidebar="dark">

<input type="hidden" value="{{URL::to('/')}}" id="base_url">
<div id="layout-wrapper">
    <!-- Begin page -->
    <div id="layout-wrapper">
        <!-- Sidebar Menu -->
        @include('admin.layout.navbar')
        <!-- Sidebar End -->
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>
        @include('admin.layout.header')

        <!-- ============================================================== -->
        <!-- Start Content -->
        <!-- ============================================================== -->
        <div class="main-content {{ Route::currentRouteName() == 'adminHome'  ? 'index-home' : ''}}">
            @include('common.done')
            @section('backend-main')
            @show
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->
</div>
@include('admin.layout.footer')
</body>
</html>
