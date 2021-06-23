

@component('mail::message')
# Hello : {{$user->name}}

please verify your new email. you can click the link below :


@component('mail::button', ['url' => route('verify',$user->verification_token)])
Verify Account
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent