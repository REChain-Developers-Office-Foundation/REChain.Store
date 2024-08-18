@if(env('MAIL_USERNAME') == null || env('MAIL_PASSWORD') == null || env('MAIL_ENCRYPTION') == null || env('MAIL_FROM_ADDRESS') == null || env('MAIL_FROM_NAME') == null)

<div class="callout env-warning">
    <h6 class="text-bold">Warning</h6>
    <p>In order for the script to work properly, the email settings in the .env file must be configured.</p>
</div>

@endif