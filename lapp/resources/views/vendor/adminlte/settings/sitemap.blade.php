@extends('adminlte::page')

@section('content')

@section('content_header', __('admin.sitemap_settings'))

@include('adminlte::inc.messages')

<div class="card p-2">
    <div class="m-1">

        {!! Form::open(['url' => url(env('ADMIN_URL').'/sitemap_settings'), 'method' => 'POST', 'files' => true]) !!}

        <span class="section-title mb-3">
            <i class="fas fa-sliders-h mr-1"></i> @lang('admin.sitemap_index')
        </span>

        {{Form::text('sitemap_index', asset('/sitemap.xml'), ['class' => 'form-control mb-3', 'readonly' => 'true'])}}

        <span class="section-title mb-3">
            <i class="fas fa-sliders-h mr-1"></i> @lang('admin.index_sitemaps_settings')
        </span>

        <div class="row mb-3">
            <div class="col-md-3">
                <label for="changefreq">@lang('admin.changefreq') <span class="text-danger">*</span></label>
                {!! Form::select('sitemap_home_changefreq', sitemap_changefreq(), old('sitemap_home_changefreq') ? old('sitemap_home_changefreq') : $settings['sitemap_home_changefreq'], ['class' => 'form-control selectpicker', 'required' => 'required', 'data-live-search' => 'true', 'id' => 'sitemap_home_changefreq' ]) !!}
            </div>

            <div class="col-md-3">
                <label for="changefreq">@lang('admin.priority') <span class="text-danger">*</span></label>
                {!! Form::select('sitemap_home_priority', sitemap_priority(), old('sitemap_home_priority') ? old('sitemap_home_priority') : $settings['sitemap_home_priority'], ['class' => 'form-control selectpicker', 'required' => 'required', 'data-live-search' => 'true', 'id' => 'sitemap_home_priority' ]) !!}
            </div>
        </div>

        <span class="section-title mb-3">
            <i class="fas fa-sliders-h mr-1"></i> @lang('admin.app_sitemaps_settings')
        </span>

        <div class="row mb-3">
            <div class="col-md-3">
                <label for="changefreq">@lang('admin.changefreq') <span class="text-danger">*</span></label>
                {!! Form::select('sitemap_app_changefreq', sitemap_changefreq(), old('sitemap_app_changefreq') ? old('sitemap_app_changefreq') : $settings['sitemap_app_changefreq'], ['class' => 'form-control selectpicker', 'required' => 'required', 'data-live-search' => 'true', 'id' => 'sitemap_app_changefreq' ]) !!}
            </div>

            <div class="col-md-3">
                <label for="changefreq">@lang('admin.priority') <span class="text-danger">*</span></label>
                {!! Form::select('sitemap_app_priority', sitemap_priority(), old('sitemap_app_priority') ? old('sitemap_app_priority') : $settings['sitemap_app_priority'], ['class' => 'form-control selectpicker', 'required' => 'required', 'data-live-search' => 'true', 'id' => 'sitemap_app_priority' ]) !!}
            </div>
        </div>

        <span class="section-title mb-3">
            <i class="fas fa-sliders-h mr-1"></i> @lang('admin.news_sitemaps_settings')
        </span>

        <div class="row mb-3">
            <div class="col-md-3">
                <label for="changefreq">@lang('admin.changefreq') <span class="text-danger">*</span></label>
                {!! Form::select('sitemap_news_changefreq', sitemap_changefreq(), old('sitemap_news_changefreq') ? old('sitemap_news_changefreq') : $settings['sitemap_news_changefreq'], ['class' => 'form-control selectpicker', 'required' => 'required', 'data-live-search' => 'true', 'id' => 'sitemap_news_changefreq' ]) !!}
            </div>

            <div class="col-md-3">
                <label for="changefreq">@lang('admin.priority') <span class="text-danger">*</span></label>
                {!! Form::select('sitemap_news_priority', sitemap_priority(), old('sitemap_news_priority') ? old('sitemap_news_priority') : $settings['sitemap_news_priority'], ['class' => 'form-control selectpicker', 'required' => 'required', 'data-live-search' => 'true', 'id' => 'sitemap_news_priority' ]) !!}
            </div>
        </div>

        <span class="section-title mb-3">
            <i class="fas fa-sliders-h mr-1"></i> @lang('admin.topic_sitemaps_settings')
        </span>

        <div class="row mb-3">
            <div class="col-md-3">
                <label for="changefreq">@lang('admin.changefreq') <span class="text-danger">*</span></label>
                {!! Form::select('sitemap_topic_changefreq', sitemap_changefreq(), old('sitemap_topic_changefreq') ? old('sitemap_topic_changefreq') : $settings['sitemap_topic_changefreq'], ['class' => 'form-control selectpicker', 'required' => 'required', 'data-live-search' => 'true', 'id' => 'sitemap_topic_changefreq' ]) !!}
            </div>

            <div class="col-md-3">
                <label for="changefreq">@lang('admin.priority') <span class="text-danger">*</span></label>
                {!! Form::select('sitemap_topic_priority', sitemap_priority(), old('sitemap_topic_priority') ? old('sitemap_topic_priority') : $settings['sitemap_topic_priority'], ['class' => 'form-control selectpicker', 'required' => 'required', 'data-live-search' => 'true', 'id' => 'sitemap_topic_priority' ]) !!}
            </div>
        </div>

        <span class="section-title mb-3">
            <i class="fas fa-sliders-h mr-1"></i> @lang('admin.page_sitemaps_settings')
        </span>

        <div class="row mb-3">
            <div class="col-md-3">
                <label for="changefreq">@lang('admin.changefreq') <span class="text-danger">*</span></label>
                {!! Form::select('sitemap_page_changefreq', sitemap_changefreq(), old('sitemap_page_changefreq') ? old('sitemap_page_changefreq') : $settings['sitemap_page_changefreq'], ['class' => 'form-control selectpicker', 'required' => 'required', 'data-live-search' => 'true', 'id' => 'sitemap_page_changefreq' ]) !!}
            </div>

            <div class="col-md-3">
                <label for="changefreq">@lang('admin.priority') <span class="text-danger">*</span></label>
                {!! Form::select('sitemap_page_priority', sitemap_priority(), old('sitemap_page_priority') ? old('sitemap_page_priority') : $settings['sitemap_page_priority'], ['class' => 'form-control selectpicker', 'required' => 'required', 'data-live-search' => 'true', 'id' => 'sitemap_page_priority' ]) !!}
            </div>
        </div>

        <span class="section-title mb-3">
            <i class="fas fa-sliders-h mr-1"></i> @lang('admin.category_sitemaps_settings')
        </span>

        <div class="row mb-3">
            <div class="col-md-3">
                <label for="changefreq">@lang('admin.changefreq') <span class="text-danger">*</span></label>
                {!! Form::select('sitemap_category_changefreq', sitemap_changefreq(), old('sitemap_category_changefreq') ? old('sitemap_category_changefreq') : $settings['sitemap_category_changefreq'], ['class' => 'form-control selectpicker', 'required' => 'required', 'data-live-search' => 'true', 'id' => 'sitemap_category_changefreq' ]) !!}
            </div>

            <div class="col-md-3">
                <label for="changefreq">@lang('admin.priority') <span class="text-danger">*</span></label>
                {!! Form::select('sitemap_category_priority', sitemap_priority(), old('sitemap_category_priority') ? old('sitemap_category_priority') : $settings['sitemap_category_priority'], ['class' => 'form-control selectpicker', 'required' => 'required', 'data-live-search' => 'true', 'id' => 'sitemap_category_priority' ]) !!}
            </div>
        </div>

        <span class="section-title mb-3">
            <i class="fas fa-sliders-h mr-1"></i> @lang('admin.platform_sitemaps_settings')
        </span>

        <div class="row mb-3">
            <div class="col-md-3">
                <label for="changefreq">@lang('admin.changefreq') <span class="text-danger">*</span></label>
                {!! Form::select('sitemap_platform_changefreq', sitemap_changefreq(), old('sitemap_platform_changefreq') ? old('sitemap_platform_changefreq') : $settings['sitemap_platform_changefreq'], ['class' => 'form-control selectpicker', 'required' => 'required', 'data-live-search' => 'true', 'id' => 'sitemap_platform_changefreq' ]) !!}
            </div>

            <div class="col-md-3">
                <label for="changefreq">@lang('admin.priority') <span class="text-danger">*</span></label>
                {!! Form::select('sitemap_platform_priority', sitemap_priority(), old('sitemap_platform_priority') ? old('sitemap_platform_priority') : $settings['sitemap_platform_priority'], ['class' => 'form-control selectpicker', 'required' => 'required', 'data-live-search' => 'true', 'id' => 'sitemap_platform_priority' ]) !!}
            </div>
        </div>

        <span class="section-title mb-3">
            <i class="fas fa-sliders-h mr-1"></i> @lang('admin.tag_sitemaps_settings')
        </span>

        <div class="row mb-3">
            <div class="col-md-3">
                <label for="changefreq">@lang('admin.changefreq') <span class="text-danger">*</span></label>
                {!! Form::select('sitemap_tag_changefreq', sitemap_changefreq(), old('sitemap_tag_changefreq') ? old('sitemap_tag_changefreq') : $settings['sitemap_tag_changefreq'], ['class' => 'form-control selectpicker', 'required' => 'required', 'data-live-search' => 'true', 'id' => 'sitemap_tag_changefreq' ]) !!}
            </div>

            <div class="col-md-3">
                <label for="changefreq">@lang('admin.priority') <span class="text-danger">*</span></label>
                {!! Form::select('sitemap_tag_priority', sitemap_priority(), old('sitemap_tag_priority') ? old('sitemap_tag_priority') : $settings['sitemap_tag_priority'], ['class' => 'form-control selectpicker', 'required' => 'required', 'data-live-search' => 'true', 'id' => 'sitemap_tag_priority' ]) !!}
            </div>
        </div>

        <span class="section-title mb-3">
            <i class="fas fa-sliders-h mr-1"></i> @lang('admin.additional_sitemaps')
        </span>

        <div id="add_sitemaps" data-delete-prompt-title="@lang('admin.oops')" data-delete-prompt-body="@lang('admin.delete_prompt')" data-yes="@lang('admin.yes')" data-cancel="@lang('admin.cancel')">
            @foreach ($addl_sitemaps as $row)

            <div class="row mb-3 topic_list border-bottom pb-4">

                <div class="col-md-6">
                    {{Form::label('url', __('admin.url') . " <span class=\"text-danger\">*</span>", [], false)}} <a class="delete-sitemap text-gray float-right" href="javascript:void(0);">@lang('admin.delete')</a>
                    {{Form::text('url_addl[]', $row->url, ['class' => 'form-control', 'required' => 'required', 'maxlength' => 255, 'placeholder' => __('admin.url')])}}
                </div>

                <div class="col-md-2">
                    <label for="changefreq">@lang('admin.changefreq') <span class="text-danger">*</span></label>
                    {!! Form::select('changefreq_addl[]', sitemap_changefreq(), old('changefreq') ? old('changefreq') : $row->changefreq, ['class' => 'form-control selectpicker', 'required' => 'required', 'data-live-search' => 'true', 'id' => 'changefreq' ]) !!}
                </div>

                <div class="col-md-2">
                    <label for="changefreq">@lang('admin.priority') <span class="text-danger">*</span></label>
                    {!! Form::select('priority_addl[]', sitemap_priority(), old('priority') ? old('priority') : $row->priority, ['class' => 'form-control selectpicker', 'required' => 'required', 'data-live-search' => 'true', 'id' => 'priority' ]) !!}
                </div>
                <div class="col-md-2">
                    {{Form::label('last_update', __('admin.last_update') . " <span class=\"text-danger\">*</span>", [], false)}}
                    {{Form::text('last_update[]', $row->last_update, ['class' => 'form-control', 'required' => 'required', 'maxlength' => 255, 'placeholder' => __('admin.last_update')])}}
                </div>
            </div>

            @endforeach
            
            <div id="sitemap-warning" class="alert alert-warning-custom d-none" role="alert">
                @lang('admin.no_addl_sitemap')
              </div>

        </div>

        {{ Form::button(__('admin.add_more'), ['class' => 'btn button-purple', 'id' => 'add_more_sitemap']) }}
        {{ Form::submit(__('admin.submit'), ['class' => 'btn button-green ml-2']) }}
        {!! Form::close() !!}

    </div>
</div>

@stop
