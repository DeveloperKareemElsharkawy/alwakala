<style>
    .my_formm .wrapper .upload-options label.error {
        background: transparent !important;
        height: 10% !important;
        top: 0;
        position: absolute;
    }
</style>

<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
    <i class="ri-close-line"></i>
</button>
<div class="modal-header d-block text-center">
    <h5 class="modal-title" id="exampleModalgridLabeldit">تعديل خاصية للمنتج {{ $product['name'] }} </h5>
</div>
<div class="card-body form-steps">
    <form method="post"
          action="{{ url('admin_panel/attributess/1/edit') }}"
          class="my_formm" enctype="multipart/form-data">
        {{ csrf_field() }}
        {{ method_field('PATCH') }}
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
                                    <div>
                                        <input type="radio" name="color_id" value="{{ $color['id'] }}" id="cb" checked/>
                                        <label class="label" for="cb">
                                            <div class="d-flex justify-content-between">
                                                <div style="background: {{ $color['hex'] }}"
                                                     class="color rounded-circle"></div>
                                                <div class="color-code">{{ $color['name_' . $lang] }}</div>
                                            </div>
                                        </label>
                                    </div>
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
                                    @foreach($category['sizes'] as $size_key => $size)
                                            <?php
                                            $selected_color = \App\Models\ProductStoreStock::where('color_id', $color['id'])->where('product_store_id', $attribute['product_store_id'])->whereHas('size', function ($q) use ($size) {
                                                $q->where('size', $size['size']);
                                            })->orderBy('id', 'desc')->first();
                                            ?>
                                        <div>
                                            <div class="gradient-box"><input
                                                    type="text" name="size_ids[{{ $size_key }}]"
                                                    value="{{ $size['size'] }}" readonly required>
                                            </div>
                                            <input type="number" name="size_counts[{{ $size_key }}]" min="0"
                                                   value="{{ $selected_color['stock'] }}" required>
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                        </div><!--end col-->
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <?php
                            $images = \App\Models\ProductImage::where('product_id', $product['id'])->where('color_id', $color['id'])->orderBy('id','desc')->paginate(3);
                            ?>
                            @foreach($images as $image_key => $image)
                                <div class="col-md-6">
                                    <div class="box">
                                        <div class="image-text">
                                        </div>
                                        <div class="upload-options">
                                            <label>
                                                <input name="image[{{ $image_key }}]" type="file" class="image-upload"
                                                       accept="image/*"/>
                                            </label>
                                        </div>
                                        <div style="background-image:url({{ $image['image_url'] }}) "
                                             class="js--image-preview mt-3"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="hstack gap-2 mt-5 justify-content-center">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                اغلاق
                            </button>
                            <button type="submit" class="btn btn-gradient finish">حفظ
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
        },
        messages: {
            'color_id': "اللون مطلوب",
        }
    });
    $('[name^="size_counts"]').each(function () {
        $(this).rules('add', {
            required: true,
            messages: {
                required: "من فضلك ادخل قيمة",
            }
        })
    });
    $('.label').click(function () {
        $(this).parent().find('label.error').remove();
    });
</script>


