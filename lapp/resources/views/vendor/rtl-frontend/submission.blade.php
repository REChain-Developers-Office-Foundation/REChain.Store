@extends('rtl-frontend::page')

@section('content')

{!! $breadcrumb_schema_data->toScript() !!}

<!-- Container -->
<div class="container my-3">

    <!-- Grid row -->
    <div class="row">

        <!-- Left column -->
        <div class="col-md-9 ps-md-2">

            <!-- Search Box -->
            @include('rtl-frontend::inc.partials', ['type' => '2'])
            <!-- /Search Box -->

            @if (!is_null($ad[5]))<div class="mb-3">{!! $ad[5] !!}</div>@endif
            <h1 class="h1-title mb-3">@lang('general.submit_your_app')</h1>

            <div class="shadow-sm p-2 bg-white rounded mb-3 pb-1">
                <div class="m-1">

                    <div class="submission-box" id="submission-section" data-error="@lang('general.error')" data-recaptcha-error="@lang('general.recaptcha_error')" data-fill-all-fields="@lang('general.fill_all_fields')">

                        <form id="submission-form" enctype="multipart/form-data">

                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="name" class="form-label">@lang('general.your_name'): <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="email" class="form-label">@lang('general.your_email'): <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                    <small id="emailHelp" class="form-text text-muted">@lang('general.email_notification')</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="title" class="form-label">@lang('general.title'): <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="description" class="form-label">@lang('general.description'): <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="description" name="description" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="category" class="form-label">@lang('general.category'): <span class="text-danger">*</span></label>
                                    <select id="category" name="category" class="form-control">
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}">
                                            {{ $category->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="platform" class="form-label">@lang('general.platform'): <span class="text-danger">*</span></label>
                                    <select id="platform" name="platform" class="form-control">
                                        @foreach($platforms as $platform)
                                        <option value="{{ $platform->id }}">
                                            {{ $platform->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="developer" class="form-label">@lang('general.developer'): <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="developer" name="developer" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="url" class="form-label">@lang('general.download_page_url'): <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="url" name="url" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="version" class="form-label">@lang('general.version'): <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="version" name="version">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="file_size" class="form-label">@lang('general.file_size'):</label>
                                    <input type="text" class="form-control" id="file_size" name="file_size">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="license" class="form-label">@lang('general.license'): </label>
                                    <input type="text" class="form-control" id="license" name="license">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="detailed_description" class="form-label">@lang('general.detailed_description'):</label>
                                    <textarea class="form-control" rows="5" id="detailed_description" name="detailed_description"></textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="image" class="form-label">@lang('general.image'): <span class="text-danger">*</span></label>
                                    <input type="file" accept="image/*" class="form-control mb-1" id="image" name="image">
                                </div>
                            </div>

                            @if ($settings['enable_google_recaptcha'] == '1')
                            <div class="g-recaptcha mb-2" data-sitekey="{{ $settings['google_recaptcha_site_key'] }}"></div>
                            @endif

                        </form>

                        <button type="submit" class="btn text-white comment-button mb-1" onclick="submission_form_control()">@lang('general.submit')</button>

                        <div id="submission-result"></div>

                    </div>

                </div>
            </div>

            @if (!is_null($ad[6]))<div class="mt-3">{!! $ad[6] !!}</div>@endif

        </div>
        <!-- /Left column -->

        <!-- Right column -->
        <div class="col-md-3 pe-md-2 mt-md-0 mt-3">
            @include('rtl-frontend::inc.partials', ['type' => '0'])
            @include('rtl-frontend::inc.partials', ['type' => '1'])
            @include('rtl-frontend::inc.partials', ['type' => '3'])
        </div>
        <!-- /Right column -->

    </div>
    <!-- /Grid row -->

</div>
<!-- /Container -->

@endsection
