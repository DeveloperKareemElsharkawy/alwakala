@component('mail::message')
    # Dear {{$data['user_name']}},

    {{$data['message']}}


    Thanks.
    {{ config('app.name') }}
@endcomponent
