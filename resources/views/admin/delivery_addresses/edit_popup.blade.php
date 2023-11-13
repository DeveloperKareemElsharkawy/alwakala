<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
    <i class="ri-close-line"></i>
</button>
<div class="modal-header d-block text-center">
    <h5 class="modal-title" id="exampleModalgridLabeldit">تعديل عنوان التوصيل {{ $address['name']}} </h5>
</div>
<div class="modal-body px-5">
    <form class="myy_form px-5" method="post" action="{{ url('admin_panel/edit_delivery_address' , $address['id']) }}">
        {{ csrf_field() }}
        {{ method_field('PATCH') }}
        <div class="row g-3">
            <div class="col-md-12 col-12">
                <label for="name1" class="form-label">اسم العنوان</label>
                <div class="input-group">
                    <input name="name" value="{{ $address['name'] }}" type="text" class="form-control" required>
                </div>
            </div><!--end col-->
            <div class="col-md-12 col-12">
                <div class="select-div">
                    <label for="state_id_edit" class="form-label">المحافظة</label>
                    <select class="form-select select-modal" name="state_id" id="state_id_edit" required="">
                        @foreach($states as $state)
                            <option {{ $address['city']['state_id'] == $state['id'] ? 'selected' : '' }} value="{{ $state['id'] }}">{{ $state['name_'.$lang] }}</option>
                        @endforeach
                    </select>
                </div>
            </div><!--end col-->
            <div class="col-md-12 col-12">
                <div class="select-div">
                    <label for="city_id_edit" class="form-label">المدينة</label>
                    <select class="form-select select-modal" name="city_id" id="city_id_edit" required="">
                        @foreach($old_state->cities as $city)
                            <option {{ $address['city']['id'] == $city['id'] ? 'selected' : '' }} value="{{ $city['id'] }}">{{ $city['name_'.$lang] }}</option>
                        @endforeach
                    </select>
                </div>
            </div><!--end col-->

            <div class="col-md-12 col-12">
                <label for="name1" class="form-label">المنطقة</label>
                <div class="input-group">
                    <input name="main_street" value="{{ $address['main_street'] }}" type="text" class="form-control" required>
                    <input name="user_id" value="{{ $address['user_id'] }}" hidden="">
                </div>
            </div><!--end col-->

            <div class="col-md-12 col-12">
                <label for="address" class="form-label">العنوان</label>
                <div class="input-group">
                    <input name="address" value="{{ $address['address'] }}" type="text" class="form-control" required>
                </div>
            </div><!--end col-->
            <div class="col-md-12 col-12">
                <label for="name1" class="form-label">الهاتف</label>
                <div class="input-group">
                    <input name="mobile" value="{{ $address['mobile'] }}" type="number" class="form-control" required>
                </div>
            </div><!--end col-->
            <div class="col-md-12 col-12">
                <div class="select-div">
                    <label for="is_default" class="form-label">عنوان افتراضي</label>
                    <select class="form-select select-modal" name="is_default" id="is_default" required="">
                        <option {{ $address['is_default'] == true ? 'selected' : '' }} value="true">نعم</option>
                        <option {{ $address['is_default'] == false ? 'selected' : '' }} value="false">لا</option>
                    </select>
                </div>
            </div><!--end col-->
            <div class="col-lg-12">
                <div class="hstack gap-2 mt-5 justify-content-center">
                    <button type="submit" class="btn btn-gradient">حفظ</button>
                </div>
            </div><!--end col-->
        </div><!--end row-->
    </form>
</div>

<script src="{{ asset('admin') }}/assets/js/custom.js"></script>
<script>
    $('#state_id_edit').on('change', function (e) {
        console.log(e);
        var state_id = e.target.value;
        var server = '<?php echo \Request::root(); ?>';
        $.get(server + '/city_ajax?state_id=' + state_id, function (data) {
            $('#city_id_edit').empty();
            $('#city_id_edit').append('<option value="" selected hidden disabled>Select City</option>');
            $.each(data, function (index, subcatObj) {
                $('#city_id_edit').append('<option value="' + subcatObj.id + '">' + subcatObj.name + '</option>');
            });h
        });
    });
</script>
<script src="{{ asset('admin') }}/assets/js/jquery.validate.min.js"></script>
<script>
    $(document).ready(function() {
        $(".myy_form").validate({
            rules: {
                state_id: "required",
                city_id: "required",
                mobile: "required",
                name: "required",
                address: "required",
                main_street: "required",
            },
            messages: {
                state_id: "الحقل مطلوب",
                mobile: "الحقل مطلوب",
                name: "الحقل مطلوب",
                address: "الحقل مطلوب",
                main_street: "الحقل مطلوب",
            }
        });
    });
</script>

