<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
    <i class="ri-close-line"></i>
</button>
<div class="modal-header d-block text-center">
    <h5 class="modal-title" id="exampleModalgridLabeldit">تعديل عنوان الشحن في {{ $address['city']['name_'. $lang] }} </h5>
</div>
<div class="modal-body px-5">
    <form class=" px-5" method="post" action="{{ url('admin_panel/new_shipping_address' , $address['id']) }}">
        {{ csrf_field() }}
        {{ method_field('PATCH') }}
        <div class="row g-3">
            <div class="col-md-12 col-12">
                <div class="select-div">
                    <label for="state_id_edit" class="form-label">المحافظة</label>
                    <select class="form-select select-modal" id="state_id_edit" required="">
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
                <label for="name1" class="form-label">تكلفة الشحن</label>
                <div class="input-group location_price">
                    <span class="input-group-text fw-bold">LE</span>
                    <input name="fees" value="{{ $address['fees'] }}" type="number" class="form-control">
                    <input name="store_id" value="{{ $address['store_id'] }}" hidden="">
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

