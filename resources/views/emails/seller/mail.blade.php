@component('mail::message')

# {{$data['subject']}}

Dear {{$data['user_name']}},

{{$data['message']}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
