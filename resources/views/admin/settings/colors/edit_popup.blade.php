<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
    <i class="ri-close-line"></i>
</button>
<div class="modal-header d-block text-center">
    <h5 class="modal-title" id="exampleModalgridLabeldit">تعديل اللون {{ $color['name_'.$lang] }} </h5>
</div>
<div class="modal-body px-5">
    <form class="my_form px-5" method="post" action="{{route('colors.update' , $color->id)}}" autocomplete="off">
        {{ csrf_field() }}
        {{ method_field('PATCH') }}
        <div class="row g-3">
            <div class="col-md-12 col-12">
                <div>
                    <label for="name_ar" class="form-label">الاسم بالعربية</label>
                    <input type="text" name="name_ar" class="form-control" id="name_ar" value="{{ $color['name_ar'] }}" placeholder="">
                    @error('name_ar')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div><!--end col-->

            <div class="col-md-12 col-12">
                <div>
                    <label for="name_en" class="form-label">الاسم بالانجليزية</label>
                    <input type="text" name="name_en" class="form-control" id="name_en" value="{{ $color['name_en'] }}" placeholder="">
                    @error('name_en')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div><!--end col-->

            <div class="col-md-12 col-12">
                <div>
                    <label class="form-label">درجة اللون</label>
                    <input type="text" class="form-control" id="" value="{{ $color['hex'] }}" placeholder="" readonly>
                </div>
            </div><!--end col-->

            <div class="col-md-12 col-12">
                <div>
                    <label for="colorPicker" class="form-label"> اللون</label>
                    <input type="color" class="form-control form-control-color w-100" name="hex" id="colorPicker" value="{{ $color['hex'] }}" placeholder="">
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
                hex: "required",
            },
            messages: {
                name_ar: "اسم البراند بالعربية مطلوب",
                name_en: "اسم البراند بالانجليزية مطلوب",
                hex: "درجة اللون مطلوبة",
            }
        });
    });
</script>

