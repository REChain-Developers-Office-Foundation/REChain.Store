@extends('adminlte::page')

@section('content')

@section('content_header', __('admin.pwa_settings'))

@include('adminlte::inc.messages')

<div class="card p-2">
    <div class="m-1">

        {!! Form::open(['url' => url(env('ADMIN_URL').'/pwa_settings'), 'method' => 'POST', 'files' => true]) !!}

        <span class="section-title mb-3">
            <i class="fas fa-sliders-h mr-1"></i> @lang('admin.pwa_settings')
        </span>

        <div class="row mb-3">
            <div class="col-md-4">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('enable_pwa', null, $settings['enable_pwa'], ['id' => 'enable_pwa'])}}
                    {{Form::label('enable_pwa', __('admin.enable_pwa'))}}
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('pwa_name', __('admin.pwa_name') . " <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('pwa_name', $settings['pwa_name'], ['class' => 'form-control', 'required' => 'required', 'maxlength' => 255, 'placeholder' => __('admin.pwa_name')])}}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('pwa_short_name', __('admin.pwa_short_name') . " <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('pwa_short_name', $settings['pwa_short_name'], ['class' => 'form-control', 'required' => 'required', 'maxlength' => 255, 'placeholder' => __('admin.pwa_short_name')])}}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('pwa_description', __('admin.pwa_description') . " <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('pwa_description', $settings['pwa_description'], ['class' => 'form-control', 'required' => 'required', 'maxlength' => 255, 'placeholder' => __('admin.pwa_description')])}}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                {{Form::label('pwa_theme_color', __('admin.pwa_theme_color') . " <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('pwa_theme_color', $settings['pwa_theme_color'], ['class' => 'form-control my-colorpicker2', 'required' => 'required', 'placeholder' => __('admin.pwa_theme_color')])}}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                {{Form::label('pwa_background_color', __('admin.pwa_background_color') . " <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('pwa_background_color', $settings['pwa_background_color'], ['class' => 'form-control my-colorpicker2', 'required' => 'required', 'placeholder' => __('admin.pwa_background_color')])}}
            </div>
        </div>

        <div class="row mb-3">

            <div class="col-md-3">
                <label>@lang('admin.pwa_screenshot') <sup><span class="right badge size-sup">1080x1920</span></sup></label>
                <div class="custom-file custom-file-share">
                    {{Form::label('pwa_screenshot', __('admin.choose_image'), ['class' => 'custom-file-label'])}}
                    {{Form::file('pwa_screenshot', ['class' => 'custom-file-input'])}}
                </div>
                <a href="{{ asset('/images/pwa-screenshot.png') }}?r={{Str::random(40)}}" data-toggle="lightbox" class="preview d-table mt-1">@lang('admin.preview')</a>
            </div>
            
        </div>
        
        <div class="row mb-3">

            <div class="col-md-3">
                <label>@lang('admin.pwa_icon') <sup><span class="right badge size-sup">512x512</span></sup></label>
                <div class="custom-file custom-file-share">
                    {{Form::label('pwa_icon', __('admin.choose_image'), ['class' => 'custom-file-label'])}}
                    {{Form::file('pwa_512', ['class' => 'custom-file-input'])}}
                </div>
                <a href="{{ asset('/images/pwa-512x512.png') }}?r={{Str::random(40)}}" data-toggle="lightbox" class="preview d-table mt-1">@lang('admin.preview')</a>
            </div>      
                                    
            <div class="col-md-3">
                <label>@lang('admin.pwa_icon') <sup><span class="right badge size-sup">192x192</span></sup></label>
                <div class="custom-file custom-file-share">
                    {{Form::label('pwa_icon', __('admin.choose_image'), ['class' => 'custom-file-label'])}}
                    {{Form::file('pwa_192', ['class' => 'custom-file-input'])}}
                </div>
                <a href="{{ asset('/images/pwa-192x192.png') }}?r={{Str::random(40)}}" data-toggle="lightbox" class="preview d-table mt-1">@lang('admin.preview')</a>
            </div>      
            
            <div class="col-md-3">
                <label>@lang('admin.pwa_icon') <sup><span class="right badge size-sup">48x48</span></sup></label>
                <div class="custom-file custom-file-share">
                    {{Form::label('pwa_icon', __('admin.choose_image'), ['class' => 'custom-file-label'])}}
                    {{Form::file('pwa_48', ['class' => 'custom-file-input'])}}
                </div>
                <a href="{{ asset('/images/pwa-48x48.png') }}?r={{Str::random(40)}}" data-toggle="lightbox" class="preview d-table mt-1">@lang('admin.preview')</a>
            </div>      
                                    
            <div class="col-md-3">
                <label>@lang('admin.pwa_icon') <sup><span class="right badge size-sup">24x24</span></sup></label>
                <div class="custom-file custom-file-share">
                    {{Form::label('pwa_icon', __('admin.choose_image'), ['class' => 'custom-file-label'])}}
                    {{Form::file('pwa_24', ['class' => 'custom-file-input'])}}
                </div>
                <a href="{{ asset('/images/pwa-24x24.png') }}?r={{Str::random(40)}}" data-toggle="lightbox" class="preview d-table mt-1">@lang('admin.preview')</a>
            </div>      
            
        </div>

        {{ Form::submit(__('admin.submit'), ['class' => 'btn button-green']) }}
        {!! Form::close() !!}

    </div>
</div>


@stop
