@extends('adminlte::page')

@section('content_header', __('admin.edit_language'))

@section('content')

@include('adminlte::inc.messages')

<div class="card p-2">
    <div class="m-1">

        {!! Form::open(['action' => ['App\Http\Controllers\LanguageController@update', $row->id], 'method' => 'PUT', 'files' => true]) !!}

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('title', "Title <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('title', $row->title, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Title'])}}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('slug', 'Slug')}}
                {{Form::text('slug', $row->slug, ['class' => 'form-control', 'placeholder' => 'Slug'])}}
            </div>
        </div>

        <div class="row mb-3">
            
         <div class="col-md-3">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('left_column', null, $row->left_column, ['id' => 'left_column'])}}
                    {{Form::label('left_column', 'Left Column')}}
                </div>
            </div>            

        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <label class="section-head">@lang('admin.seo_settings')</label>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('custom_title', "Custom Meta Title")}}
                {{Form::text('custom_title', $row->custom_title, ['class' => 'form-control', 'placeholder' => 'Custom Meta Title'])}}
                    <div class="shortcodes mt-1"><b>@lang('admin.supported_shortcodes'):</b> <a href="#">%language_title%</a><a href="#">%site_title%</a><a href="#">%sep%</a><a href="#">%year%</a><a href="#">%month%</a><a href="#">%day%</a><a href="#">%month_text%</a><a href="#">%day_text%</a></div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('custom_description', "Custom Meta Description")}}
                {{Form::textarea('custom_description', $row->custom_description, ['class' => 'form-control', 'rows' => '5', 'placeholder' => 'Custom Meta Description'])}}
                    <div class="shortcodes mt-1"><b>@lang('admin.supported_shortcodes'):</b> <a href="#">%language_title%</a><a href="#">%site_title%</a><a href="#">%sep%</a><a href="#">%year%</a><a href="#">%month%</a><a href="#">%day%</a><a href="#">%month_text%</a><a href="#">%day_text%</a></div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('custom_h1', __('admin.custom_h1_title'))}}
                {{Form::text('custom_h1', $row->custom_h1, ['class' => 'form-control', 'placeholder' => __('admin.custom_h1_title')])}}
                    <div class="shortcodes mt-1"><b>@lang('admin.supported_shortcodes'):</b> <a href="#">%language_title%</a><a href="#">%site_title%</a><a href="#">%sep%</a><a href="#">%year%</a><a href="#">%month%</a><a href="#">%day%</a><a href="#">%month_text%</a><a href="#">%day_text%</a></div>
            </div>
        </div>
                
        {{ Form::submit(__('admin.submit'), ['class' => 'btn button-green']) }}
        {!! Form::close() !!}

    </div>
</div>

@stop
