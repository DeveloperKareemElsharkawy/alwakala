@extends('admin.layout.master')
@section('pageTitle', 'التصنيفات ')

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
        .error {
            color: red
        }
    </style>
@endsection
@section('backend-main')
    <div class="page-content show_vendor sidebar_settings">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="row">
                                <div class="col-md-3">
                                    @include('admin.settings.sidebar')
                                </div>
                                <div class="col-md-9">
                                    <div class="card-header">
                                        <div class="card-title">
                                            شجرة التصنيفات
                                        </div>
                                    </div>
                                    <ul class="wtree">
                                        @foreach($categories as $category)
                                            <li class="{{ count($category->childrenCategories) > 0 ? 'has' : 'not-has' }}">
                                                <?php
                                                    $category_ids = \App\Models\Category::where('category_id' , $category['id'])->pluck('id');
                                                    $subcategory_ids = \App\Models\Category::whereIn('category_id',$category_ids)->pluck('id');
                                                    $products = \App\Models\Product::whereIn('category_id' , $subcategory_ids)->orderBy('id' , 'desc')->get();
                                                    ?>
                                                <span>
                                                    {{ $category['name_'.$lang] }}
                                                    <div>
                                                        <a class="badge badge-gradient-danger" href="{{ url('admin_panel/settings/category_products/' . $category['id']) }}">Products : {{ count($products) }}</a>
                                                        <a class="badge badge-gradient-primary" href="{{ url('admin_panel/settings/category_stores/' . $category['id']) }}">Stores : {{ count($category['stores']) }}</a>
                                                    </div>
                                                </span>
                                                <ul>
                                                    @foreach($category['childrenCategories'] as $subcategory)
                                                        <?php
                                                            $category_ids = \App\Models\Category::where('category_id' , $subcategory['id'])->pluck('id');
                                                            $products = \App\Models\Product::whereIn('category_id' , $category_ids)->orderBy('id' , 'desc')->get();
                                                            ?>
                                                        <li class="{{ count($subcategory->childrenCategories) > 0 ? 'has' : 'not-has' }}">
                                                <span>
                                                    {{ $subcategory['name_'.$lang] }}
                                                    <div>
                                                        <a class="badge badge-gradient-danger" href="{{ url('admin_panel/settings/category_products/' . $subcategory['id']) }}">Products : {{ count($products) }}</a>
                                                        <a class="badge badge-gradient-primary" href="{{ url('admin_panel/settings/category_stores/' . $subcategory['id']) }}">Stores : {{ count($subcategory['stores']) }}</a>
                                                    </div>
                                                </span>
                                                            <ul>
                                                                @foreach($subcategory['childrenCategories'] as $subsubcategory)
                                                                        <?php
                                                                        $products = \App\Models\Product::where('category_id' , $subsubcategory['id'])->orderBy('id' , 'desc')->get();
                                                                        ?>
                                                                    <li class="{{ count($subsubcategory->childrenCategories) > 0 ? 'has' : 'not-has' }}">
                                                <span>
                                                    {{ $subsubcategory['name_'.$lang] }}
                                                    <div>
                                                        <a class="badge badge-gradient-danger" href="{{ url('admin_panel/settings/category_products/' . $subsubcategory['id']) }}">Products : {{ count($products) }}</a>
                                                        <a class="badge badge-gradient-primary" href="{{ url('admin_panel/settings/category_stores/' . $subsubcategory['id']) }}">Stores : {{ count($subsubcategory['stores']) }}</a>
                                                    </div>
                                                </span>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @endforeach
                                    </ul>

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
    <script>
        $('.wtree li span').click(function () {
            $(this).parent().find('ul').slideToggle();
        });
    </script>
    <!-- select2 js -->
    <script src="{{ asset('admin') }}/assets/libs/select2/select2.min.js"></script>
    <script src="{{ asset('admin') }}/assets/js/additional-methods.min.js"></script>
    <script src="{{ asset('admin') }}/assets/js/jquery.validate.min.js"></script>
    <!-- Sweet Alerts js -->
    <script src="{{ asset('admin') }}/assets/libs/sweetalert2/sweetalert2.min.js"></script>
@endsection
