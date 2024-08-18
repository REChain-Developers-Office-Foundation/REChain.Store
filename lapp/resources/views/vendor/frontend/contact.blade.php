@extends('frontend::page')

@section('content')

{!! $breadcrumb_schema_data->toScript() !!}

<!-- Container -->
<div class="container my-3">

    <!-- Grid row -->
    <div class="row">

        <!-- Left column -->
        <div class="col-md-9 pe-md-2">

            <!-- Search Box -->
            @include('frontend::inc.partials', ['type' => '2'])
            <!-- /Search Box -->

            @if (!is_null($ad[5]))<div class="mb-3">{!! $ad[5] !!}</div>@endif

            <h1 class="h1-title mb-3">@lang('general.contact')</h1>

            <div id="contact-form-section" data-error="@lang('general.error')" data-recaptcha-error="@lang('general.recaptcha_error')">

                <div class="shadow-sm p-2 bg-white rounded">

                    <div class="my-1 mx-2">

                        <form id="contact-form">

                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="name" class="form-label">@lang('general.name') <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="email" class="form-label">@lang('general.email') <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="subject" class="form-label">@lang('general.subject') <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="subject" name="subject" required>
                                </div>
                            </div>

                            <div class="mb-2 pb-1">
                                <label for="message" class="form-label">@lang('general.message') <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="message" name="message" rows="3"></textarea>
                            </div>

                            @if ($settings['enable_google_recaptcha'] == '1')
                            <div class="g-recaptcha mb-2" data-sitekey="{{ $settings['google_recaptcha_site_key'] }}"></div>
                            @endif

                        </form>

                        <div id="contact-form-result"></div>

                        <button type="submit" class="btn text-white submit-button" onclick="contact_form()">@lang('general.submit')</button>

                    </div>

                </div>

            </div>

            @if (!is_null($ad[6]))<div class="mt-3">{!! $ad[6] !!}</div>@endif

        </div>
        <!-- /Left column -->

        <!-- Right column -->
        <div class="col-md-3 ps-md-2 mt-md-0 mt-3">
            @include('frontend::inc.partials', ['type' => '0'])
            @include('frontend::inc.partials', ['type' => '1'])
            @include('frontend::inc.partials', ['type' => '3'])
        </div>
        <!-- /Right column -->

    </div>
    <!-- /Grid row -->

</div>
<!-- /Container -->

@endsection
