<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
    <i class="ri-close-line"></i>
</button>
<div class="modal-header d-block text-center">
    <h5 class="modal-title" id="exampleModalgridLab">اضافة منتج جديد</h5>
</div>
<div class="modal-body px-5">
    <div class="card-body form-steps">
        <form id="wizard" method="post" action="{{ url('admin_panel/products/'. $product->id .'/edit') }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            {{ method_field('PATCH') }}
            <div class="step-arrow-nav mb-4 d-none">
                <ul class="nav nav-pills custom-nav nav-justified" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="steparrow-gen-info-tab"
                                data-bs-toggle="pill"
                                data-bs-target="#steparrow-gen-info" type="button" role="tab"
                                aria-controls="steparrow-gen-info" aria-selected="true"
                                data-position="0">
                            General
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="steparrow-description-info-tab"
                                data-bs-toggle="pill"
                                data-bs-target="#steparrow-description-info" type="button" role="tab"
                                aria-controls="steparrow-description-info" aria-selected="false"
                                data-position="1" tabindex="-1">Description
                        </button>
                    </li>

                </ul>
            </div>

            <div class="tab-content">
                <div class="tab-pane fade active show" id="steparrow-gen-info" role="tabpanel"
                     aria-labelledby="steparrow-gen-info-tab">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <p class="mb-0 text-center">
                                Step 1
                            </p>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12 col-12">
                                    <div>
                                        <label for="name1" class="form-label">الاسم للمنتج</label>
                                        <input type="text" name="name" value="{{ $product['name'] }}" class="form-control" id="name1"
                                               placeholder="">

                                        <input hidden="" name="store_id" value="{{ $store['id'] }}">
                                    </div>
                                </div><!--end col-->
                                <div class="col-md-12 col-12">
                                    <div>
                                        <label for="desc" class="form-label">نبذه عن المنتج </label>
                                        <textarea rows="1" name="description"  class="form-control" id="desc"
                                                  placeholder="">{{ $product['description'] }}</textarea>
                                    </div>
                                </div><!--end col-->
                                <div class="col-md-12 col-12">
                                    <div>
                                        <label for="date" class="form-label">تاريخ
                                            النشر</label>
                                        <input type="date" name="publish_app_at" class="form-control"
                                               id="date" value="{{ $product_store['publish_app_at'] }}" placeholder="">
                                    </div>
                                </div><!--end col-->
                                <div class="col-md-12 col-12">
                                    <div>
                                        <label for="youtube" class="form-label">رابط ال
                                            youtube</label>
                                        <input type="url" class="form-control"
                                               id="youtube" value="{{ $product['youtube_link'] }}" name="youtube_link" placeholder="">
                                    </div>
                                </div><!--end col-->
                                <div class="col-md-6 col-12">
                                    <div>
                                        <label for="wholesale_price" class="form-label">سعر
                                            الجملة</label>
                                        <input type="number" class="form-control"
                                               id="wholesale_price" value="{{ $product_store['price'] }}" name="price" placeholder="">
                                    </div>
                                </div><!--end col-->
                                <div class="col-md-6 col-12">
                                    <div>
                                        <label for="discount" class="form-label">قيمة الخصم
                                            الجملة</label>
                                        <input type="number" value="{{ $product_store['discount'] }}" class="form-control"
                                               id="discount" name="discount" placeholder="">
                                    </div>
                                </div><!--end col-->
                                <div class="col-md-12 col-12">
                                    <div class="select-div custom-check">
                                        <label class="form-label">نوع الخصم للجملة</label>
                                        <div class="inputs_cutom">
                                            <div class="me-1">
                                                <input hidden="" {{ $product_store['discount_type'] == 2 ? 'checked' : '' }} name="discount_type" value="2" type="radio" id="myCheckbper2"/>
                                                <label class="custom-label" for="myCheckbper2">
                                                    مئوي
                                                </label>
                                            </div>
                                            <div class="me-1">
                                                <input hidden="" {{ $product_store['discount_type'] == 1 ? 'checked' : '' }} name="discount_type" value="1" type="radio" id="myCheckbper22"/>
                                                <label class="custom-label" for="myCheckbper22">
                                                    رقم صحيح
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div><!--end col-->
                                <div class="col-md-6 col-12">
                                    <div>
                                        <label for="consumer_old_price" class="form-label">سعر
                                            القطاعي</label>
                                        <input type="number" class="form-control"
                                               id="consumer_old_price" value="{{ $product_store['consumer_old_price'] }}" name="consumer_old_price" placeholder="">
                                    </div>
                                </div><!--end col-->
                                <div class="col-md-6 col-12">
                                    <div>
                                        <label for="discount" class="form-label">قيمة الخصم
                                            القطاعي</label>
                                        <input type="number" class="form-control"
                                               id="consumer_price_discount" value="{{ $product_store['consumer_price_discount'] }}" name="discount" placeholder="">
                                    </div>
                                </div><!--end col-->
                                <div class="col-md-12 col-12">
                                    <div class="select-div custom-check">
                                        <label class="form-label">نوع الخصم للقطاعي</label>
                                        <div class="inputs_cutom">
                                            <div class="me-1">
                                                <input {{ $product_store['consumer_price_discount_type'] == 2 ? 'checked' : '' }} hidden="" name="consumer_price_discount_type" value="2" type="radio" id="myCheckbperr2"/>
                                                <label class="custom-label" for="myCheckbperr2">
                                                    مئوي
                                                </label>
                                            </div>
                                            <div class="me-1">
                                                <input {{ $product_store['consumer_price_discount_type'] == 1 ? 'checked' : '' }} hidden="" name="consumer_price_discount_type" value="1" type="radio" id="myCheckbperr22"/>
                                                <label class="custom-label" for="myCheckbperr22">
                                                    رقم صحيح
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div><!--end col-->
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12 col-12">
                                    <div class="select-div">
                                        <label for="category_id" class="form-label">التصنيف</label>
                                        <select name="category_id" class="form-select select-modal"
                                                id="category_id" required="">
                                            @foreach($categories as $category)
                                                <option {{ $product['category_id'] == $category['id'] ? 'selected' : '' }} value="{{ $category['id'] }}">{{ $category['name_'.$lang] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div><!--end col-->

                                <div class="col-md-12 col-12">
                                    <div class="select-div">
                                        <label for="brand_id" class="form-label">الماركة</label>
                                        <select name="brand_id" class="form-select select-modal"
                                                id="brand_id" required="">
                                            <option selected="" disabled="" value=""
                                                    hidden></option>
                                            @foreach($brands as $brand)
                                                <option {{ $product['brand_id'] == $brand['id'] ? 'selected' : '' }} value="{{ $brand['id'] }}">{{ $brand['name_'.$lang] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div><!--end col-->
                                <div class="col-md-12 col-12">
                                    <div class="select-div custom-check">
                                        <label for="subsubcategory_id"
                                               class="form-label">الخامات</label>
                                        <div class="inputs_cutom">
                                            @foreach($materials as $key => $material)
                                                <div class="me-1">
                                                    <input hidden="" {{ $product['material_id'] == $material['id'] ? 'checked' : '' }} name="material_id" value="{{ $material['id'] }}" type="radio" id="myCheckbox{{ $key }}"/>
                                                    <label class="custom-label" for="myCheckbox{{ $key }}">
                                                        {{ $material['name_'.$lang] }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div><!--end col-->

                                <div class="col-md-12 col-12">
                                    <div class="select-div custom-check">
                                        <label for="subsubcategory_id"
                                               class="form-label">طرق الشحن</label>
                                        <div class="inputs_cutom">
                                            @foreach($shippings as $ship_key => $shipping)
                                                <div class="me-1">
                                                    <input hidden="" {{ $product['shipping_method_id'] == $shipping['id'] ? 'selected' : '' }} name="shipping_method_id" value="{{ $shipping['id'] }}" type="radio" id="myCheckbo{{ $ship_key }}"/>
                                                    <label class="custom-label" for="myCheckbo{{ $ship_key }}">
                                                        {{ $shipping['name_'.$lang] }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div><!--end col-->
                                <div class="col-md-12 col-12">
                                    <div class="select-div custom-check">
                                        <label class="form-label">سياسات</label>
                                        <div class="inputs_cutom">
                                            @foreach($policies as $policy_key => $policy)
                                                <div class="me-1">
                                                    <input hidden="" {{ $product['policy_id'] == $policy['id'] ? 'selected' : '' }} name="policy_id" value="{{ $policy['id'] }}" type="radio" id="myCheckb{{ $policy_key }}"/>
                                                    <label class="custom-label" for="myCheckb{{ $policy_key }}">
                                                        {{ $policy['name_'.$lang] }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div><!--end col-->

                                <div class="col-md-12 col-12">
                                    <div class="select-div custom-check">
                                        <label class="form-label">شحن مجاني</label>
                                        <div class="inputs_cutom">
                                            <div class="me-1">
                                                <input hidden="" {{ $product_store['free_shipping'] == true ? 'checked' : '' }} name="free_shipping" value="true" type="radio" id="myCheckbsh1"/>
                                                <label class="custom-label" for="myCheckbsh1">
                                                    نعم
                                                </label>
                                            </div>
                                            <div class="me-1">
                                                <input hidden="" {{ $product_store['free_shipping'] == false ? 'checked' : '' }} name="free_shipping" value="false" type="radio" id="myCheckbsh2"/>
                                                <label class="custom-label" for="myCheckbsh2">
                                                    لا
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div><!--end col-->
                            </div>
                        </div>
                    </div><!--end row-->
                    <div class="hstack gap-2 mt-5 justify-content-center">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">اغلاق
                        </button>
                        <button type="button" class="btn btn-gradient nexttab nexttab"
                                data-nexttab="steparrow-description-info-tab">التالي
                        </button>
                    </div>
                </div>
                <!-- end tab pane -->

                <div class="tab-pane fade" id="steparrow-description-info" role="tabpanel"
                     aria-labelledby="steparrow-description-info-tab">
                    <div class="row">
                        <div class="col-md-12">
                            <p class="mb-0 text-center">
                                Step 2 ( Store data )
                            </p>
                        </div>
                    </div>
                    <div class="wrapper">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-md-6">
                                        <div class="box">
                                            <div class="image-text">
                                                <label for="barcode" class="form-label">BARCODE</label>
                                            </div>
                                            <div class="upload-options">
                                                <label>
                                                    <input type="file" name="barcode" class="image-upload"
                                                           accept="image/*"/>
                                                </label>
                                            </div>
                                            <div @if(isset($product['barcode_url'])) style="background-image:url({{ $product_store['barcode_url'] }})" @endif class="js--image-preview mt-3"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-12 col-12">
                                    <div>
                                        <label for="barcode" class="form-label">BARCODE</label>
                                        <input type="text" name="barcode_text" value="{{ $product_store['barcode_text'] }}" class="form-control" id="barcode"
                                               placeholder="">
                                    </div>
                                </div><!--end col-->
                            </div>

                            <div class="col-md-12">
                                <div class="hstack gap-2 mt-5 justify-content-center">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                        اغلاق
                                    </button>
                                    <button type="button"
                                            class="btn btn-light btn-label previestab  d-none"
                                            data-previous="steparrow-description-info-tab"><i
                                            class="ri-arrow-left-line label-icon align-middle fs-lg me-2"></i>
                                        الرجوع
                                    </button>
                                    <button id="finish" type="submit" class="btn btn-gradient">حفظ
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end tab pane -->

            </div>
            <!-- end tab content -->
        </form>
    </div>
</div>


