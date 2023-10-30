<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
    <i class="ri-close-line"></i>
</button>
<div class="modal-header d-block text-center">
    <h5 class="modal-title" id="exampleModalgridLabeldit">تعديل التصنيف {{ $category['name_'.$lang] }} </h5>
</div>
<div class="modal-body px-5">
    <form class="my_form px-5" method="post" action="{{route('categories.update' , $category->id)}}" enctype="multipart/form-data" autocomplete="off">
        {{ csrf_field() }}
        {{ method_field('PATCH') }}
        <div class="row g-3">
            <div class="col-md-12">
                <div class="avatar-upload">
                    <div class="avatar-edit">
                        <input type='file' id="imageUpload2" name="image" class="imageUpload"
                               accept=".png, .jpg, .jpeg"/>
                        <label for="imageUpload2">
                            <i class="bx bxs-plus-circle"></i>
                        </label>
                    </div>
                    <div class="avatar-preview">
                        <div id="imagePreview2"
                             style="background-image: url({{ $category['image_url'] }})">
                        </div>
                    </div>
                </div>
            </div>
            @if($url == url('admin_panel/settings/subcategories') || $url == url('admin_panel/settings/subsubcategories'))
                <div class="col-md-12 col-12">
                    <div class="select-div">
                        <label for="category_id" class="form-label">التصنيف الرئيسي </label>
                        <select class="form-select select-modal" name="category_id"
                                id="category_id">
                            @if(Request::is('admin_panel/settings/subcategories'))
                            @foreach($main_categories as $cat_key => $categoryy)
                                <option {{ $categoryy['id'] == $category->category_id ? 'selected' : '' }}
                                    value="{{ $categoryy['id'] }}">{{ $categoryy['name_'.$lang] }}</option>
                            @endforeach
                            @endif
                            @if($url == url('admin_panel/settings/subsubcategories'))
                            @foreach($main_categories as $cat_key => $categoryy)
                                <option {{ $categoryy['id'] == $category->parent->id ? 'selected' : '' }}
                                    value="{{ $categoryy['id'] }}">{{ $categoryy['name_'.$lang] }}</option>
                            @endforeach
                            @endif
                        </select>
                        @error('category_id')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div><!--end col-->
            @endif
            @if($url == url('admin_panel/settings/subsubcategories'))
                <div class="col-md-12 col-12">
                    <div class="select-div">
                        <label for="subcategory_id" class="form-label">التصنيف الفرعي </label>
                        <?php
                           $subca = \App\Models\Category::where('id' , $category['category_id'])->first();
                           $subcats = \App\Models\Category::where('category_id' , $subca['category_id'])->get();
                        ?>
                        <select class="form-select select-modal" name="subcategory_id"
                                id="subcategory_id">
                            @foreach($subcats as $subcat)
                                <option {{ $subcat['id'] == $category['category_id'] ? 'selected' : '' }}
                                    value="{{ $subcat['id'] }}">{{ $subcat['name_'.$lang] }}</option>
                            @endforeach
                        </select>
                        @error('subcategory_id')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div><!--end col-->
            @endif
            <div class="col-md-12 col-12">
                <div>
                    <label for="name_ar" class="form-label">الاسم بالعربية</label>
                    <input type="text" name="name_ar" class="form-control" id="name_ar" value="{{ $category['name_ar'] }}" placeholder="">
                    @error('name_ar')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div><!--end col-->

            <div class="col-md-12 col-12">
                <div>
                    <label for="name_en" class="form-label">الاسم بالانجليزية</label>
                    <input type="text" name="name_en" class="form-control" id="name_en" value="{{ $category['name_en'] }}" placeholder="">
                    @error('name_en')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div><!--end col-->

            <div class="col-lg-12">
                <div class="hstack gap-2 mt-5 justify-content-center">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">اغلاق</button>
                    <button type="submit" class="btn btn-gradient">حفظ</button>
                </div>
            </div><!--end col-->
        </div><!--end row-->
    </form>
</div>
<script src="{{ asset('admin') }}/assets/libs/select2/select2.min.js"></script>
<script src="{{ asset('admin') }}/assets/js/custom.js"></script>
<script src="{{ asset('admin') }}/assets/js/additional-methods.min.js"></script>
<script src="{{ asset('admin') }}/assets/js/jquery.validate.min.js"></script>
@if(Request::is('admin_panel/settings/categories'))
    <script>
        $(document).ready(function () {
            $(".my_form").validate({
                rules: {
                    name_ar: "required",
                    name_en: "required",
                    image: "required",
                },
                messages: {
                    name_ar: "اسم البراند بالعربية مطلوب",
                    name_en: "اسم البراند بالانجليزية مطلوب",
                    image: "صورة البراند مطلوبة",
                }
            });
        });
    </script>
@endif
@if(Request::is('admin_panel/settings/subcategories'))
    <script>
        $(document).ready(function () {
            $(".my_form").validate({
                rules: {
                    name_ar: "required",
                    name_en: "required",
                    image: "required",
                    category_id: "required"
                },
                messages: {
                    name_ar: "اسم البراند بالعربية مطلوب",
                    name_en: "اسم البراند بالانجليزية مطلوب",
                    image: "صورة البراند مطلوبة",
                    category_id: "من فضلك اختر تصنيف"
                }
            });
        });
    </script>
@endif
@if(Request::is('admin_panel/settings/subsubcategories'))
    <script>
        $(document).ready(function () {
            $(".my_form").validate({
                rules: {
                    name_ar: "required",
                    name_en: "required",
                    image: "required",
                    category_id: "required",
                    subcategory_id: "required"
                },
                messages: {
                    name_ar: "اسم البراند بالعربية مطلوب",
                    name_en: "اسم البراند بالانجليزية مطلوب",
                    image: "صورة البراند مطلوبة",
                    category_id: "من فضلك اختر تصنيف",
                    subcategory_id: "من فضلك اختر تصنيف فرعي"
                }
            });
        });
    </script>
@endif
<script>
    $('#category_id').on('change', function() {
        var category_id = $(this).val();
        $.get(link + '/ajax_subcatgeories?category_id=' + category_id, function (data) {
            $('#subcategory_id').empty();
            $.each(data, function (index, subcatObj) {
                $('#subcategory_id').append('<option value="' + subcatObj.id + '">' + subcatObj.name + '</option>');
            });
        });
    });
</script>

