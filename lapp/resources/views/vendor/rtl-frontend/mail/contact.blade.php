@component('mail::message')
# @lang('general.contact_message_received')

<b>@lang('general.name'):</b> {{ $name }}<br>
<b>@lang('general.email'):</b> {{ $email }}<br>
<b>@lang('general.subject'):</b> {{ $form_subject }}<br>
<b>@lang('general.message'):</b> {!! $form_message !!}<br>
<b>@lang('general.ip_address'):</b> {{ $ip_address }}

@lang('general.regards_message'),<br>
{{ $site_title }}
@endcomponent
