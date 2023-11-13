<style>
    .my_formm .wrapper .upload-options label.error{
        background: transparent!important;
        height: 10%!important;
        top: 0;
        position: absolute;
    }
</style>
<div class="card-body form-steps">
    <form method="post"
          action="{{ url('admin_panel/product_attr_save') }}"
          class="my_formm" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input name="product_id" value="{{ $product['id'] }}" hidden="" readonly/>
        <input name="store_id" value="{{ $store['id'] }}" hidden="" readonly/>
        <div class="tab-pane fade active show" role="tabpanel"
             aria-labelledby="steparrow-description-info-tab">
            <div class="row">
                <div class="col-md-12">
                    <p class="mb-0 text-center">
                        خصائص المنتج {{ $product['name'] }}
                    </p>
                </div>
            </div>
            <div class="wrapper">
                <div class="row">
                    <div class="col-md-6">
                        <div class="col-md-12 col-12">
                            <div class="property-options">
                                <div class="property-label">
                                    <label class="form-label">الالوان</label>
                                </div>
                                <div class="radio_div">
                                    @foreach($colors as $color_key => $color)
                                        <div>
                                            <input type="radio" name="color_id" value="{{ $color['id'] }}" id="cb_{{ $color_key }}" required/>
                                            <label class="label" for="cb_{{ $color_key }}">
                                                <div class="d-flex justify-content-between">
                                                    <div style="background: {{ $color['hex'] }}" class="color rounded-circle"></div>
                                                    <div class="color-code">{{ $color['name_' . $lang] }}</div>
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div><!--end col-->
                        </div>
                        <div class="col-md-12 col-12">
                            <div class="property-options">
                                <div class="property-label">
                                    <label
                                        class="form-label">
                                        الكمية
                                        <small class="text-dark shadow-none">(لكل مقاس)</small>
                                    </label>
                                </div>
                                <div class="add_div size_div" id="second">
                                    @foreach($maincategory['sizes'] as $size_key => $size)
                                        <div>
                                            <div class="gradient-box"><input
                                                    type="text" name="size_ids[{{ $size_key }}]" value="{{ $size['size'] }}" readonly required>
                                            </div>
                                            <input type="number" name="size_counts[{{ $size_key }}]" min="0" value="0" required>
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                        </div><!--end col-->
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="box">
                                    <div class="image-text">
                                    </div>
                                    <div class="upload-options">
                                        <label>
                                            <input name="image[0]" type="file" class="image-upload"
                                                   accept="image/*" required/>
                                        </label>
                                    </div>
                                    <div class="js--image-preview mt-3"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="box">
                                    <div class="image-text">
                                    </div>
                                    <div class="upload-options">
                                        <label>
                                            <input name="image[1]" type="file" class="image-upload"
                                                   accept="image/*" required/>
                                        </label>
                                    </div>
                                    <div class="js--image-preview mt-3"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="box">
                                    <div class="image-text">
                                    </div>
                                    <div class="upload-options">
                                        <label>
                                            <input name="image[2]" type="file" class="image-upload"
                                                   accept="image/*" required/>
                                        </label>
                                    </div>
                                    <div class="js--image-preview mt-3"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="hstack gap-2 mt-5 justify-content-center">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                اغلاق
                            </button>
                            <button type="button" class="btn btn-gradient finish">حفظ
                            </button>
                            <button type="button" class="btn btn-gradient continue">حفظ واضافة اخرى
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end tab pane -->
        <!-- end tab content -->
    </form>
</div>
<script src="{{ asset('admin') }}/assets/js/jquery.min.js"></script>
<script src="{{ asset('admin') }}/assets/js/custom.js"></script>
<script src="{{ asset('admin') }}/assets/js/jquery.validate.min.js"></script>
<script>
    $('.my_formm').validate({
        rules: {
            'color_id': "required",
            'image[0]': "required",
            'image[1]': "required",
            'image[2]': "required",
        },
        messages: {
            'color_id': "اللون مطلوب",
            'image[0]': "الصورة مطلوبة",
            'image[1]': "الصورة مطلوبة",
            'image[2]': "الصورة مطلوبة",
        }
    });
    $('[name^="size_counts"]').each(function() {
        $(this).rules('add', {
            required: true,
            messages: {
                required: "من فضلك ادخل قيمة",
            }
        })
    });
    $('.label').click(function (){
       $(this).parent().find('label.error').remove();
    });
    var continue_link = '{{ url('admin_panel/product_attr_save?type=add_new') }}';
    var finish_link = '{{ url('admin_panel/product_attr_save') }}';
    $('.continue').click(function(){
        $('.my_formm').attr('action', continue_link);
        $('.my_formm').submit();
    });
    $('.finish').click(function(){
        $('.my_formm').attr('action', finish_link);
        $('.my_formm').submit();
    });
</script>
