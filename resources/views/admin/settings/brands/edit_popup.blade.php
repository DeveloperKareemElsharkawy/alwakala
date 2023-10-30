<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
    <i class="ri-close-line"></i>
</button>
<div class="modal-header d-block text-center">
    <h5 class="modal-title" id="exampleModalgridLabeldit">تعديل البراند {{ $brand['name_'.$lang] }} </h5>
</div>
<div class="modal-body px-5">
    <form class="my_form px-5" method="post" action="{{route('brands.update' , $brand->id)}}" enctype="multipart/form-data" autocomplete="off">
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
                             style="background-image: url({{ $brand['image_url'] }})">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-12">
                <div class="select-div">
                    <label for="validationDefault02" class="form-label">التصنيفات </label>
                    <select class="form-select select-modal" name="category_ids[]" id="validationDefault02" multiple>
                        @foreach($categories as $cat_key => $category)
                            <?php
                                $found = \App\Models\BrandCategory::where('brand_id' , $brand['id'])->where('category_id' ,$category['id'])->first();
                                ?>
                            <option {{ $found ? 'selected' : '' }} value="{{ $category['id'] }}">{{ $category['name_'.$lang] }}</option>
                        @endforeach
                    </select>
                    @error('category_ids')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div><!--end col-->
            <div class="col-md-12 col-12">
                <div>
                    <label for="name_ar" class="form-label">الاسم بالعربية</label>
                    <input type="text" name="name_ar" class="form-control" id="name_ar" value="{{ $brand['name_ar'] }}" placeholder="">
                    @error('name_ar')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div><!--end col-->

            <div class="col-md-12 col-12">
                <div>
                    <label for="name_en" class="form-label">الاسم بالانجليزية</label>
                    <input type="text" name="name_en" class="form-control" id="name_en" value="{{ $brand['name_en'] }}" placeholder="">
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

<script src="{{ asset('admin') }}/assets/js/custom.js"></script>
<script src="{{ asset('admin') }}/assets/js/additional-methods.min.js"></script>
<script src="{{ asset('admin') }}/assets/js/jquery.validate.min.js"></script>
<script>
    $(document).ready(function() {
        $(".my_form").validate({
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

