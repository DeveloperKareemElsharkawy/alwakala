<div class="p-4">
    <img width="70" height="70" class="side-info-img"
         src="{{ $user['image_url'] }}" alt=""/>
    <p class="text-muted fs-sm">{{ $user['name'] }}</p>
    <p class="text-muted fs-sm alert alert-dark d-inline-block border-0 user-type">
        @if($user['type_id'] == 1)
            ادمن
        @elseif($user['type_id'] == 2)
            تاجر
        @elseif($user['type_id'] == 3)
            مشتري
        @endif
    </p>
    <div class="row">
        <div style="margin-right: 3em;" class="col-md-12">
            <div class="form-check-group">
                <div class="checkbox-group">
{{--                    <div class="form-check form-check-left">--}}
{{--                        <input class="form-check-input" type="checkbox" value=""--}}
{{--                               id="permessionnpermessionnss3"--}}
{{--                               checked disabled>--}}
{{--                        <label class="form-check-label" for="permessionnpermessionnss3">--}}
{{--                            ادارة العضويات--}}
{{--                        </label>--}}
{{--                    </div>--}}
                </div>
            </div>
        </div><!--end col-->

        <div class="col-md-12">
            <ul class="user-list-info text-center">
                <li>
                    <i class="bx bx-calendar"></i>
                    تاريخ الانضمام : {{ $user->created_at->format('d M Y') }}
                </li>
                <li>
                    <i class="bx bx-envelope"></i>
                    {{ $user['email'] }}
                </li>
                <li>
                    <i class="bx bxs-phone"></i>
                    {{ $user['mobile'] }}
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="user-logs">
    <p class="alert alert-dark border-0 text-start">
        <i class="mdi mdi-file-chart-outline"></i> Logs
    </p>
    <ul class="logs-list px-4">
        <li>
            <p>
                Create New action
            </p>
            <p>
                25 Fed. 2022
            </p>
        </li>
        <li>
            <p>
                Create New action
            </p>
            <p>
                25 Fed. 2022
            </p>
        </li>
        <li>
            <p>
                Create New action
            </p>
            <p>
                25 Fed. 2022
            </p>
        </li>
    </ul>
</div>
