@component('mail::message')
# Request Rest Password

Dear {{$data['user_name']}},

You Receiving this mail cause you request reset password and your reset code is: <b>{{$data['reset_code']}}</b>


Thanks,<br>
{{ config('app.name') }}
@endcomponent
