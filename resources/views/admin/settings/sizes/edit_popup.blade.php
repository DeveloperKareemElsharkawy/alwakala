<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
    <i class="ri-close-line"></i>
</button>
<div class="modal-header d-block text-center">
    <h5 class="modal-title" id="exampleModalgridLabeldit">تعديل اللون {{ $size['size'] }} </h5>
</div>
<div class="modal-body px-5">
    <form class="my_form px-5" method="post" action="{{route('sizes.update' , $size->id)}}" autocomplete="off">
        {{ csrf_field() }}
        {{ method_field('PATCH') }}
        <div class="row g-3">
            <div class="col-md-12 col-12">
                <div class="select-div">
                    <label for="validationDefault02" class="form-label">التصنيفات </label>
                    <select class="form-select select-modal" name="category_ids[]" id="validationDefault02" multiple>
                        @foreach($categories as $cat_key => $category)
                                <?php
                                $found = \App\Models\CategorySize::where('size_id' , $size['id'])->where('category_id' ,$category['id'])->first();
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
                    <label for="size" class="form-label">المقاس</label>
                    <input type="text" name="size" class="form-control" id="size" value="{{ $size['size'] }}" placeholder="">
                    @error('size')
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
                size: "required",
                "category[]": "required"
            },
            messages: {
                size: "اسم البراند بالعربية مطلوب",
                "category[]": "من فضلك اختر تصنيف"
            }
        });
    });
</script>

