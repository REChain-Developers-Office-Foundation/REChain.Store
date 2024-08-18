@extends('adminlte::page')

@section('content_header', __('admin.create_slider'))

@section('content')

@include('adminlte::inc.messages')

<div class="card p-2">
    <div class="m-1">

        {!! Form::open(['action' => 'App\Http\Controllers\SliderController@store', 'method' => 'POST', 'files' => true]) !!}

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('title', __('admin.title')." <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('title', '', ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('admin.title')])}}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <label for="link">@lang('admin.app') <span class="text-danger">*</span></label>
                {!! Form::select('link', $apps, old('link'), ['class' => 'form-control selectpicker', 'required' => 'required', 'data-live-search' => 'true', 'id' => 'link' ]) !!}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <label>@lang('admin.image') <span class="text-danger">*</span></label>
                <div class="custom-file">
                    {{Form::label('image', __('admin.choose_image'), ['class' => 'custom-file-label'])}}
                    {{Form::file('image', ['class' => 'custom-file-input', 'required' => 'required', 'id' => 'browse-image'])}}
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('active', null, true, ['id' => 'active'])}}
                    {{Form::label('active', __('admin.active'))}}
                </div>
            </div>
        </div>

        {{ Form::submit(__('admin.submit'), ['class' => 'btn button-green']) }}
        {!! Form::close() !!}

    </div>
</div>

@stop
