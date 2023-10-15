<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
    <i class="ri-close-line"></i>
</button>
<div class="modal-header d-block text-center">
    <h5 class="modal-title" id="exampleModalgridLabeldit">تعديل المستخدم {{ $user['name'] }} </h5>
</div>
<div class="modal-body px-5">
    <form class=" px-5" method="post" action="{{route('users.update' , $user->id)}}" enctype="multipart/form-data" autocomplete="off">
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
                             style="background-image: url({{ $user['image_url'] }})">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-12">
                <div>
                    <label for="name" class="form-label">الاسم كامل</label>
                    <input type="text" value="{{ $user['name'] }}" name="name" class="form-control" id="name" placeholder="">
                </div>
            </div><!--end col-->
            <div class="col-md-6 col-12">
                <div>
                    <label for="emaill" class="form-label">البريد الالكتروني</label>
                    <input type="email" value="{{ $user['email'] }}" name="email" class="form-control" id="emaill"
                           placeholder="">
                </div>
            </div><!--end col-->

            <div class="col-md-6 col-12">
                <div>
                    <label for="phonee" class="form-label">رقم الهاتف</label>
                    <input type="tel" minlength="11" maxlength="11" name="mobile" class="form-control  password-input"
                           id="phonee" value="{{ $user['mobile'] }}" placeholder=""
                           oninput="this.value = this.value.replace(/[^0-9+()]/g, '');" pattern=".{11,11}"
                           required>
                </div>
            </div><!--end col-->
            <div class="col-md-6 col-12">
                <div class="select-div">
                    <label for="validationDefault04" class="form-label">دور العضوية</label>
                    <select class="form-select select-modal" name="type" id="validationDefault04" required="">
                        <option {{ $user['type_id'] == 1 ? 'selected' : '' }} value="ADMIN">ادمن</option>
                        <option {{ $user['type_id'] == 3 ? 'selected' : '' }} value="CONSUMER">مستهلك</option>
                        <option {{ $user['type_id'] == 2 ? 'selected' : '' }} value="SELLER">بائع</option>
                    </select>
                </div>
            </div><!--end col-->
            <div class="col-md-6 col-12">
                <div>
                    <label class="form-label" for="password-inputt">رقم المرور (في حاله التغيير فقط)</label>
                    <div class="position-relative auth-pass-inputgroup mb-3">
                        <input type="password" name="password" class="form-control pe-6 password-input "
                               placeholder="" id="password-inputt" autoComplete="new-password">
                        <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon"
                                type="button" id="password-addonn"><i
                                class="ri-eye-fill align-middle"></i></button>
                    </div>
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

