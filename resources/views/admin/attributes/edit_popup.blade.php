<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
    <i class="ri-close-line"></i>
</button>
<div class="modal-header d-block text-center">
    <h5 class="modal-title" id="exampleModalgridLabeldit">تعديل خاصية للمنتج {{ $product['name'] }} </h5>
</div>
<div class="modal-body px-5">
    <form class=" px-5" method="post" action="{{ url('admin_panel/attributes/' . $attribute['id'].'/edit') }}">
        {{ csrf_field() }}
        {{ method_field('PATCH') }}
        <input name="product_store_id" value="{{ $attribute['product_store_id'] }}" hidden="">

        <div class="row g-3">
            <div class="col-md-12 col-12">
                <div class="select-div">
                    <label for="size_id" class="form-label">الحجم</label>
                    <select name="size_id" class="form-select select-modal" id="size_id" required="">
                        @foreach($sizes as $statee)
                            <option {{ $attribute['size_id'] == $statee['id'] ? 'selected' : '' }} value="{{ $statee['id'] }}">{{ $statee['size'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div><!--end col-->

            <div class="col-md-12 col-12">
                <div class="select-div">
                    <label for="color_id" class="form-label">اللون</label>
                    <select name="color_id" class="form-select select-modal" id="color_id" required="">
                        @foreach($colors as $state)
                            <option {{ $attribute['color_id'] == $state['id'] ? 'selected' : '' }} value="{{ $state['id'] }}">{{ $state['name_'.$lang] }}</option>
                        @endforeach
                    </select>
                </div>
            </div><!--end col-->
            <div class="col-md-12 col-12">
                <div class="select-div">
                    <label for="stock" class="form-label">المخزون الكلي</label>
                    <input name="stock" value="{{ $attribute['stock'] }}" type="number" class="form-control">
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


