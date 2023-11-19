<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
    <i class="ri-close-line"></i>
</button>
<div class="modal-header d-block text-center">
    <h5 class="modal-title" id="exampleModalgridLabeldit">تعديل الحي {{ $material['name_'.$lang] }} </h5>
</div>
<div class="modal-body px-5">
    <form class="my_form px-5" method="post" action="{{route('cities.update' , $material->id)}}" autocomplete="off">
        {{ csrf_field() }}
        {{ method_field('PATCH') }}
        <div class="row g-3">
            <div class="col-md-12 col-12">
                <div>
                    <label for="name_ar" class="form-label">الاسم بالعربية</label>
                    <input type="text" name="name_ar" class="form-control" id="name_ar" value="{{ $material['name_ar'] }}" placeholder="">
                    @error('name_ar')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div><!--end col-->

            <div class="col-md-12 col-12">
                <div>
                    <label for="name_en" class="form-label">الاسم بالانجليزية</label>
                    <input type="text" name="name_en" class="form-control" id="name_en" value="{{ $material['name_en'] }}" placeholder="">
                    @error('name_en')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div><!--end col-->
            <div class="col-md-12 col-12">
                <div class="select-div">
                    <label for="country_id" class="form-label">الدولة </label>
                    <select class="form-select select-modal" name="country_id"
                            id="country_id">
                        @foreach($countries as $country_key => $country)
                            <option {{ $country['id'] == $old_country['id'] ? 'selected' : '' }}
                                value="{{ $country['id'] }}">{{ $country['name_'.$lang] }}</option>
                        @endforeach
                    </select>
                    @error('country_id')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div><!--end col-->

            <div class="col-md-12 col-12">
                <div class="select-div">
                    <label for="region_id" class="form-label">المحافظة </label>
                    <select class="form-select select-modal" name="region_id"
                            id="region_id">
                        @foreach($regions as $region_key => $country)
                            <option {{ $country['id'] == $material['country_id'] ? 'selected' : '' }}
                                value="{{ $country['id'] }}">{{ $country['name_'.$lang] }}</option>
                        @endforeach
                    </select>
                    @error('region_id')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div><!--end col-->
            <div class="col-md-12 col-12">
                <div class="select-div">
                    <label for="state_id" class="form-label">المدينة </label>
                    <select class="form-select select-modal" name="state_id"
                            id="state_id">
                        @foreach($states as $state_key => $country)
                            <option {{ $country['id'] == $material['state_id'] ? 'selected' : '' }}
                                value="{{ $country['id'] }}">{{ $country['name_'.$lang] }}</option>
                        @endforeach
                    </select>
                    @error('state_id')
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
                country_id: "required",
                region_id: "required",
                state_id: "required",
            },
            messages: {
                name_ar: "اسم الدولة بالعربية مطلوب",
                name_en: "اسم الدولة بالانجليزية مطلوب",
                country_id: "الدولة مطلوبة",
                region_id: "المحافظة مطلوبة",
                state_id: "الولاية مطلوبة",
            }
        });
    });
</script>
<script>
    $('#country_id').on('change', function() {
        var country_id = $(this).val();
        $.get(link + '/ajax_regions?country_id=' + country_id, function (data) {
            $('#region_id').empty();
            $.each(data, function (index, subcatObj) {
                $('#region_id').append('<option value="' + subcatObj.id + '">' + subcatObj.name + '</option>');
            });
        });
    });
</script>
<script>
    $('#region_id').on('change', function() {
        var region_id = $(this).val();
        $.get(link + '/state_ajax?region_id=' + region_id, function (data) {
            $('#state_id').empty();
            $.each(data, function (index, subcatObj) {
                $('#state_id').append('<option value="' + subcatObj.id + '">' + subcatObj.name + '</option>');
            });
        });
    });
</script>

