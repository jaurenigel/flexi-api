@component('mail::message')
# This Is A Test 12

Testing if email is working

@component('mail::button', ['url' => ''])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
