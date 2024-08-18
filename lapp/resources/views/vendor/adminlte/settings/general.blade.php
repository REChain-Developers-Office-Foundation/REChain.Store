@extends('adminlte::page')

@section('content')

@section('content_header', __('admin.general_settings'))

@include('adminlte::inc.messages')

<div class="card p-2">
    <div class="m-1">

        {!! Form::open(['url' => url(env('ADMIN_URL').'/general_settings'), 'method' => 'POST', 'files' => true]) !!}

        <span class="section-title mb-3">
            <i class="fas fa-sliders-h mr-1"></i> @lang('admin.general_settings')
        </span>

        <div class="row mb-3">
            <div class="col-md-3">
                <label for="languages">@lang('admin.site_language')</label>
                {!! Form::select('site_language', $languages, $settings['site_language'], ['class' => 'form-control selectpicker', 'data-live-search' => 'true', 'id' => 'site_language' ]) !!}
            </div>
             <div class="col-md-3">
                {{Form::label('admin_email', __('admin.admin_email') . " <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('admin_email', $settings['admin_email'], ['class' => 'form-control', 'placeholder' => __('admin.admin_email')])}}
            </div>
        </div>

        <span class="section-title mb-3">
            <i class="fas fa-sliders-h mr-1"></i> @lang('admin.social_media')
        </span>

        <div class="row mb-3">
            <div class="col-md-3">
                {{Form::label('twitter_account', __('admin.twitter_account'), [], false)}}
                {{Form::text('twitter_account', $settings['twitter_account'], ['class' => 'form-control', 'placeholder' => __('admin.twitter_account')])}}
            </div>
            <div class="col-md-3">
                {{Form::label('facebook_page', __('admin.facebook_page'), [], false)}}
                {{Form::text('facebook_page', $settings['facebook_page'], ['class' => 'form-control', 'placeholder' => __('admin.facebook_page')])}}
            </div>
            <div class="col-md-3">
                {{Form::label('telegram_page', __('admin.telegram_page'), [], false)}}
                {{Form::text('telegram_page', $settings['telegram_page'], ['class' => 'form-control', 'placeholder' => __('admin.telegram_page')])}}
            </div>
            <div class="col-md-3">
                {{Form::label('youtube_page', __('admin.youtube_page'), [], false)}}
                {{Form::text('youtube_page', $settings['youtube_page'], ['class' => 'form-control', 'placeholder' => __('admin.youtube_page')])}}
            </div>
        </div>

        <span class="section-title mb-3">
            <i class="fas fa-sliders-h mr-1"></i> @lang('admin.cronjobs')
        </span>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('auto_submission_cronjob_link_google', __('admin.auto_submission_cronjob_link_google') . " <a href=\"".asset(env('ADMIN_URL').'/general_settings')."?submission_cronjob_code_google\"><sup class=\"text-dark\"><u>".__('admin.generate_new_link')."</u></sup></a>", [], false)}}
                    <span class="d-block mb-2"><b>@lang('admin.last_run'):</b> {{ $settings['google_cronjob_last_run'] ? \Carbon\Carbon::parse($settings['google_cronjob_last_run'])->diffForHumans() : __('admin.never') }}</span>
                {{Form::text('auto_submission_cronjob_link_google', asset('/crawler'.'/'.$settings['submission_cronjob_code_google']), ['class' => 'form-control', 'readonly' => 'true'])}}
                <div class="callout callout-dark mt-3 mb-0 py-2 pl-2">
                    <p><i class="fas fa-info-circle"></i> <b>@lang('admin.sample_curl_command'):</b> curl --silent {{ asset('/crawler'.'/'.$settings['submission_cronjob_code_google']) }} >/dev/null 2>&1</p>
                </div>
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('auto_submission_cronjob_link_apple', __('admin.auto_submission_cronjob_link_apple') . " <a href=\"".asset(env('ADMIN_URL').'/general_settings')."?submission_cronjob_code_apple\"><sup class=\"text-dark\"><u>".__('admin.generate_new_link')."</u></sup></a>", [], false)}}
                    <span class="d-block mb-2"><b>@lang('admin.last_run'):</b> {{ $settings['apple_cronjob_last_run'] ? \Carbon\Carbon::parse($settings['apple_cronjob_last_run'])->diffForHumans() : __('admin.never') }}</span>
                {{Form::text('auto_submission_cronjob_link_apple', asset('/crawler'.'/'.$settings['submission_cronjob_code_apple']), ['class' => 'form-control', 'readonly' => 'true'])}}
                <div class="callout callout-dark mt-3 mb-0 py-2 pl-2">
                    <p><i class="fas fa-info-circle"></i> <b>@lang('admin.sample_curl_command'):</b> curl --silent {{ asset('/crawler'.'/'.$settings['submission_cronjob_code_apple']) }} >/dev/null 2>&1</p>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('cronjob_link', __('admin.cronjob_link') . " <a href=\"".asset(env('ADMIN_URL').'/general_settings')."?cronjob\"><sup class=\"text-dark\"><u>".__('admin.generate_new_link')."</u></sup></a>", [], false)}}
                    <span class="d-block mb-2"><b>@lang('admin.last_run'):</b> {{ $settings['system_cronjob_last_run'] ? \Carbon\Carbon::parse($settings['system_cronjob_last_run'])->diffForHumans() : __('admin.never') }}</span>
                {{Form::text('cronjob_link', asset('/cronjob'.'/'.$settings['cronjob_code']), ['class' => 'form-control', 'readonly' => 'true'])}}
                <div class="callout callout-dark mt-3 mb-0 py-2 pl-2">
                    <p><i class="fas fa-info-circle"></i> <b>@lang('admin.sample_curl_command'):</b> curl --silent {{ asset('/cronjob'.'/'.$settings['cronjob_code']) }} >/dev/null 2>&1</p>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('hourly_cronjob_link', __('admin.hourly_cronjob_link') . " <a href=\"".asset(env('ADMIN_URL').'/general_settings')."?hourly_cronjob\"><sup class=\"text-dark\"><u>".__('admin.generate_new_link')."</u></sup></a>", [], false)}}
                    <span class="d-block mb-2"><b>@lang('admin.last_run'):</b> {{ $settings['hourly_cronjob_last_run'] ? \Carbon\Carbon::parse($settings['hourly_cronjob_last_run'])->diffForHumans() : __('admin.never') }}</span>
                {{Form::text('hourly_cronjob_link', asset('/hourly-cronjob'.'/'.$settings['hourly_cronjob_code']), ['class' => 'form-control', 'readonly' => 'true'])}}
                <div class="callout callout-dark mt-3 mb-0 py-2 pl-2">
                    <p><i class="fas fa-info-circle"></i> <b>@lang('admin.sample_curl_command'):</b> curl --silent {{ asset('/hourly-cronjob'.'/'.$settings['hourly_cronjob_code']) }} >/dev/null 2>&1</p>
                </div>
            </div>
        </div>

        <span class="section-title mb-3">
            <i class="fas fa-sliders-h mr-1"></i> @lang('admin.image_settings')
        </span>

        <div class="row mb-3">

            <div class="col-md-3">
                <label>@lang('admin.site_logo') <sup><span class="right badge size-sup">600x140</span></sup></label>
                <div class="custom-file custom-file-logo">
                    {{Form::label('logo', __('admin.choose_image'), ['class' => 'custom-file-label'])}}
                    {{Form::file('logo', ['class' => 'custom-file-input'])}}
                </div>
                <a href="{{ s3_switch('logo.png') }}?r={{Str::random(40)}}" data-toggle="lightbox" class="preview d-table mt-1">@lang('admin.preview')</a>
            </div>

            <div class="col-md-3">
                <label>@lang('admin.favicon') <sup><span class="right badge size-sup">192x192</span></sup></label>
                <div class="custom-file custom-file-favicon">
                    {{Form::label('favicon', __('admin.choose_image'), ['class' => 'custom-file-label'])}}
                    {{Form::file('favicon', ['class' => 'custom-file-input'])}}
                </div>
                <a href="{{ s3_switch('favicon.png') }}?r={{Str::random(40)}}" data-toggle="lightbox" class="preview d-table mt-1">@lang('admin.preview')</a>
            </div>

            <div class="col-md-3">
                <label>@lang('admin.default_app_image') <sup><span class="right badge size-sup">200x200</span></sup></label>
                <div class="custom-file custom-file-share">
                    {{Form::label('default_app_image', __('admin.choose_image'), ['class' => 'custom-file-label'])}}
                    {{Form::file('default_app_image', ['class' => 'custom-file-input'])}}
                </div>
                <a href="{{ s3_switch('no_image.png') }}?r={{Str::random(40)}}" data-toggle="lightbox" class="preview d-table mt-1">@lang('admin.preview')</a>
            </div>

            <div class="col-md-3">
                <label>@lang('admin.default_share_image') <sup><span class="right badge size-sup">600x315</span></sup></label>
                <div class="custom-file custom-file-share">
                    {{Form::label('share', __('admin.choose_image'), ['class' => 'custom-file-label'])}}
                    {{Form::file('share', ['class' => 'custom-file-input'])}}
                </div>
                <a href="{{ s3_switch('default_share_image.png') }}?r={{Str::random(40)}}" data-toggle="lightbox" class="preview d-table mt-1">@lang('admin.preview')</a>
            </div>

        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('use_text_logo', null, $settings['use_text_logo'], ['id' => 'use_text_logo'])}}
                    {{Form::label('use_text_logo', __('admin.use_text_logo'))}}
                </div>
            </div>

            <div class="col-md-3">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('save_as_webp', null, $settings['save_as_webp'], ['id' => 'save_as_webp'])}}
                    {{Form::label('save_as_webp', __('admin.save_as_webp'))}}
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                {{Form::label('image_quality', __('admin.image_quality') . " <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('image_quality', $settings['image_quality'], ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('admin.image_quality')])}}
            </div>

        </div>

        <span class="section-title mb-3">
            <i class="fas fa-sliders-h mr-1"></i> @lang('admin.google_recaptcha')
        </span>

        <div class="row mb-3">
            <div class="col-md-4">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('enable_google_recaptcha', null, $settings['enable_google_recaptcha'], ['id' => 'enable_google_recaptcha'])}}
                    {{Form::label('enable_google_recaptcha', __('admin.enable_google_recaptcha'))}}
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('google_recaptcha_site_key', __('admin.google_recaptcha_site_key'), [], false)}}
                {{Form::text('google_recaptcha_site_key', $settings['google_recaptcha_site_key'], ['class' => 'form-control', 'placeholder' => __('admin.google_recaptcha_site_key')])}}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('google_recaptcha_secret_key', __('admin.google_recaptcha_secret_key'), [], false)}}
                {{Form::text('google_recaptcha_secret_key', $settings['google_recaptcha_secret_key'], ['class' => 'form-control', 'placeholder' => __('admin.google_recaptcha_secret_key')])}}
            </div>
        </div>

        <span class="section-title mb-3">
            <i class="fas fa-sliders-h mr-1"></i> @lang('admin.html_codes')
        </span>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('before_head_tag', __('admin.before_head_tag'), [], true)}}
                {{Form::textarea('before_head_tag', $settings['before_head_tag'], ['class' => 'form-control', 'rows' => '4', 'placeholder' => __('admin.before_head_tag')])}}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('after_head_tag', __('admin.after_head_tag'), [], true)}}
                {{Form::textarea('after_head_tag', $settings['after_head_tag'], ['class' => 'form-control', 'rows' => '4', 'placeholder' => __('admin.after_head_tag')])}}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('before_body_end_tag', __('admin.before_body_end_tag'), [], true)}}
                {{Form::textarea('before_body_end_tag', $settings['before_body_end_tag'], ['class' => 'form-control', 'rows' => '4', 'placeholder' => __('admin.before_body_end_tag')])}}
            </div>
        </div>

        <span class="section-title mb-3">
            <i class="fas fa-sliders-h mr-1"></i> @lang('admin.scraper_settings')
        </span>

        <div class="row">
            <div class="col-md-3 mb-3">
                <label for="languages">@lang('admin.submission_platform_google')</label>
                {!! Form::select('submission_platform_google', $platforms, $settings['submission_platform_google'], ['class' => 'form-control selectpicker', 'data-live-search' => 'true', 'id' => 'submission_platform_google' ]) !!}
            </div>
            <div class="col-md-3 mb-3">
                <label for="languages">@lang('admin.submission_platform_apple')</label>
                {!! Form::select('submission_platform_apple', $platforms, $settings['submission_platform_apple'], ['class' => 'form-control selectpicker', 'data-live-search' => 'true', 'id' => 'submission_platform_apple' ]) !!}
            </div>
            <div class="col-md-3 mb-3">
                {{Form::label('google_play_default_country', __('admin.google_play_default_country'))}}
                {{Form::text('google_play_default_country', $settings['google_play_default_country'], ['class' => 'form-control', 'placeholder' => __('admin.google_play_default_country')])}}
            </div>

            <div class="col-md-3 mb-3">
                {{Form::label('google_play_default_language', __('admin.google_play_default_language'))}}
                {{Form::text('google_play_default_language', $settings['google_play_default_language'], ['class' => 'form-control', 'placeholder' => __('admin.google_play_default_language')])}}
            </div>

            <div class="col-md-3 mb-3">
                {{Form::label('apple_app_store_country', __('admin.apple_app_store_country'))}}
                {{Form::text('apple_app_store_country', $settings['apple_app_store_country'], ['class' => 'form-control', 'placeholder' => __('admin.apple_app_store_country')])}}
            </div>
            
            <div class="col-md-3 mb-3">
                {{Form::label('screenshot_count', __('admin.screenshot_count') . " <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('screenshot_count', $settings['screenshot_count'], ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('admin.screenshot_count')])}}
            </div>
        </div>

        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('auto_submission_gps', null, $settings['auto_submission_gps'], ['id' => 'auto_submission_gps'])}}
                    {{Form::label('auto_submission_gps', __('admin.auto_submission_gps'))}}
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('auto_submission_aas', null, $settings['auto_submission_aas'], ['id' => 'auto_submission_aas'])}}
                    {{Form::label('auto_submission_aas', __('admin.auto_submission_aas'))}}
                </div>
            </div>
        </div>

        <span class="section-title mb-3">
            <i class="fas fa-sliders-h mr-1"></i> @lang('admin.other_settings')
        </span>

        <div class="row">

            <div class="col-md-12 mb-3">
                {{Form::label('recommended_terms', __('admin.recommended_terms'))}}
                {{Form::text('recommended_terms', $settings['recommended_terms'], ['class' => 'form-control', 'data-role' => 'tagsinput', 'placeholder' => __('admin.recommended_terms')])}}
            </div>

            <div class="col-md-3 mb-3">
                {{Form::label('meta_theme_color', __('admin.meta_theme_color'), [], false)}}
                {{Form::text('meta_theme_color', $settings['meta_theme_color'], ['class' => 'form-control my-colorpicker2', 'placeholder' => __('admin.meta_theme_color')])}}
            </div>

            <div class="col-md-3 mb-3">
                {{Form::label('navbar_background_color', __('admin.navbar_background_color'), [], false)}}
                {{Form::text('navbar_background_color', $settings['navbar_background_color'], ['class' => 'form-control my-colorpicker2', 'placeholder' => __('admin.navbar_background_color')])}}
            </div>
            
            <div class="col-md-3 mb-3">
                {{Form::label('navbar_text_color', __('admin.navbar_text_color'), [], false)}}
                {{Form::text('navbar_text_color', $settings['navbar_text_color'], ['class' => 'form-control my-colorpicker2', 'placeholder' => __('admin.navbar_text_color')])}}
            </div>

            <div class="col-md-3 mb-3">
                {{Form::label('navbar_text_hover_color', __('admin.navbar_text_hover_color'), [], false)}}
                {{Form::text('navbar_text_hover_color', $settings['navbar_text_hover_color'], ['class' => 'form-control my-colorpicker2', 'placeholder' => __('admin.navbar_text_hover_color')])}}
            </div>
            
            <div class="col-md-3 mb-3">
                {{Form::label('navbar_toggler_color', __('admin.navbar_toggler_color'), [], false)}}
                {{Form::text('navbar_toggler_color', $settings['navbar_toggler_color'], ['class' => 'form-control my-colorpicker2', 'placeholder' => __('admin.navbar_toggler_color')])}}
            </div>
            
            <div class="col-md-3 mb-3">
                {{Form::label('dropdown_arrow_color', __('admin.dropdown_arrow_color'), [], false)}}
                {{Form::text('dropdown_arrow_color', $settings['dropdown_arrow_color'], ['class' => 'form-control my-colorpicker2', 'placeholder' => __('admin.dropdown_arrow_color')])}}
            </div>
            
            <div class="col-md-3 mb-3">
                {{Form::label('apps_per_page', __('admin.apps_per_page') . " <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('apps_per_page', $settings['apps_per_page'], ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('admin.apps_per_page')])}}
            </div>

  <div class="col-md-3 mb-3">
                {{Form::label('news_per_page', __('admin.news_per_page') . " <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('news_per_page', $settings['news_per_page'], ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('admin.news_per_page')])}}
            </div>

  <div class="col-md-3 mb-3">
                {{Form::label('topics_per_page', __('admin.topics_per_page') . " <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('topics_per_page', $settings['topics_per_page'], ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('admin.topics_per_page')])}}
            </div>

            <div class="col-md-3 mb-3">
                {{Form::label('sitemap_records_per_page', __('admin.sitemap_records_per_page') . " <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('sitemap_records_per_page', $settings['sitemap_records_per_page'], ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('admin.sitemap_records_per_page')])}}
            </div>

            <div class="col-md-3 mb-3">
                {{Form::label('cookie_prefix', __('admin.cookie_prefix') . " <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('cookie_prefix', $settings['cookie_prefix'], ['class' => 'form-control', 'required' => 'required', 'maxlength' => 15, 'placeholder' => __('admin.cookie_prefix')])}}
            </div>

            <div class="col-md-3 mb-3">
                {{Form::label('time_before_redirect', __('admin.time_before_redirect') . " <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('time_before_redirect', $settings['time_before_redirect'], ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('admin.time_before_redirect')])}}
            </div>

            <div class="col-md-3 mb-3">
                {{Form::label('schema_org_price_currency', __('admin.schema_org_price_currency'))}}
                {{Form::text('schema_org_price_currency', $settings['schema_org_price_currency'], ['class' => 'form-control', 'placeholder' => __('admin.schema_org_price_currency')])}}
            </div>

        </div>

        <div class="row mb-3">
            <div class="col-md-3 mb-3">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('root_language', null, $settings['root_language'], ['id' => 'root_language'])}}
                    {{Form::label('root_language', __('admin.root_language'))}}
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('show_cookie_bar', null, $settings['show_cookie_bar'], ['id' => 'show_cookie_bar'])}}
                    {{Form::label('show_cookie_bar', __('admin.show_cookie_bar'))}}
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('auto_comment_approval', null, $settings['auto_comment_approval'], ['id' => 'auto_comment_approval'])}}
                    {{Form::label('auto_comment_approval', __('admin.auto_comment_approval'))}}
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('show_submission_form', null, $settings['show_submission_form'], ['id' => 'show_submission_form'])}}
                    {{Form::label('show_submission_form', __('admin.show_submission_form'))}}
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('reading_time', null, $settings['reading_time'], ['id' => 'reading_time'])}}
                    {{Form::label('reading_time', __('admin.reading_time'))}}
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('enable_show_more', null, $settings['enable_show_more'], ['id' => 'enable_show_more'])}}
                    {{Form::label('enable_show_more', __('admin.enable_show_more'))}}
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('infinite_scroll', null, $settings['infinite_scroll'], ['id' => 'infinite_scroll'])}}
                    {{Form::label('infinite_scroll', __('admin.infinite_scroll'))}}
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('rating_as_number', null, $settings['rating_as_number'], ['id' => 'rating_as_number'])}}
                    {{Form::label('rating_as_number', __('admin.rating_as_number'))}}
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('no_index_history', null, $settings['no_index_history'], ['id' => 'no_index_history'])}}
                    {{Form::label('no_index_history', __('admin.no_index_history'))}}
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('no_index_favorites', null, $settings['no_index_favorites'], ['id' => 'no_index_favorites'])}}
                    {{Form::label('no_index_favorites', __('admin.no_index_favorites'))}}
                </div>
            </div>            
            
            <div class="col-md-3 mb-3">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('show_top_bar', null, $settings['show_top_bar'], ['id' => 'show_top_bar'])}}
                    {{Form::label('show_top_bar', __('admin.show_top_bar'))}}
                </div>
            </div>
            
        </div>

        {{ Form::submit(__('admin.submit'), ['class' => 'btn button-green']) }}
        {!! Form::close() !!}

    </div>
</div>


@stop
