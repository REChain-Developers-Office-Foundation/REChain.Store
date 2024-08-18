@extends('adminlte::page')

@section('content')

@section('content_header', __('admin.openai_settings'))

@include('adminlte::inc.messages')

<div class="card p-2">
    <div class="m-1">

        {!! Form::open(['url' => url(env('ADMIN_URL').'/openai_settings'), 'method' => 'POST', 'files' => true]) !!}

        <span class="section-title mb-3">
            <i class="fas fa-sliders-h mr-1"></i> @lang('admin.openai_settings')
        </span>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('openai_max_tokens', __('admin.openai_max_tokens') . " <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('openai_max_tokens', $settings['openai_max_tokens'], ['class' => 'form-control', 'required' => 'required', 'maxlength' => 255, 'placeholder' => __('admin.openai_max_tokens')])}}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('openai_temperature', __('admin.openai_temperature') . " <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('openai_temperature', $settings['openai_temperature'], ['class' => 'form-control', 'required' => 'required', 'maxlength' => 255, 'placeholder' => __('admin.openai_temperature')])}}
            </div>
        </div>
        
             <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('openai_regenerate_command', __('admin.openai_regenerate_command'), [], false)}}
                {{Form::textarea('openai_regenerate_command', $settings['openai_regenerate_command'], ['class' => 'form-control', 'rows' => '4', 'placeholder' => __('admin.openai_regenerate_command')])}}
            </div>
        </div>    

             <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('openai_auto_regenerate_command', __('admin.openai_auto_regenerate_command'), [], false)}}
                {{Form::textarea('openai_auto_regenerate_command', $settings['openai_auto_regenerate_command'], ['class' => 'form-control', 'rows' => '4', 'placeholder' => __('admin.openai_auto_regenerate_command')])}}
            </div>
        </div>
        
            <div class="row mb-3">
            <div class="col-md-4">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('openai_auto_submission', null, $settings['openai_auto_submission'], ['id' => 'openai_auto_submission'])}}
                    {{Form::label('openai_auto_submission', __('admin.openai_auto_submission'))}}
                </div>
            </div>
        </div>
        
        
        {{ Form::submit(__('admin.submit'), ['class' => 'btn button-green']) }}
        {!! Form::close() !!}

    </div>
</div>


@stop
