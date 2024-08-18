@extends('adminlte::page')

@section('content_header', __('admin.create_version'))

@section('content')

@include('adminlte::inc.messages')

<div class="card p-2">
    <div class="m-1">

        {!! Form::open(['action' => 'App\Http\Controllers\VersionController@store', 'method' => 'POST', 'files' => true]) !!}
       
        <input type="hidden" name="app_id" value="{{ app('request')->input('app') }}" />

        <!-- row -->
          <div class="row mb-3">

            <div class="col-md-3">
                {{Form::label('version', __('admin.version'))}}
                {{Form::text('version', '', ['class' => 'form-control', 'placeholder' => __('admin.version')])}}
            </div>

            <div class="col-md-3">
                {{Form::label('file_size', __('admin.file_size'))}}
                {{Form::text('file_size', '', ['class' => 'form-control', 'placeholder' => __('admin.file_size')])}}
            </div>

            <div class="col-md-3">
                {{Form::label('counter', __('admin.downloads')." <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('counter', 0, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('admin.downloads')])}}
            </div>

            <div class="col-md-3">
                <label>@lang('admin.file')</label>
                <div class="custom-file">
                    {{Form::label('file', __('admin.choose_file'), ['class' => 'custom-file-label'])}}
                    {{Form::file('file', ['class' => 'custom-file-input', 'id' => 'browse-file'])}}
                </div>
            </div>

        </div>
        <!-- /.row -->

        <!-- row -->
        <div class="row mb-3">

            <div class="col-md-6">
                {{Form::label('url', __('admin.download_page_url'))}}
                {{Form::text('url', '', ['class' => 'form-control', 'placeholder' => __('admin.download_page_url')])}}
            </div>

        </div>
        <!-- /.row -->
        
        {{ Form::submit(__('admin.submit'), ['class' => 'btn button-green']) }}
        {!! Form::close() !!}

    </div>
</div>

@stop
