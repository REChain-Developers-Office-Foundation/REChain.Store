@extends('adminlte::page')

@section('content')

@section('content_header', __('admin.account_settings'))

@include('adminlte::inc.messages')

<div class="card p-2">
    <div class="m-1">

        {!! Form::open(['url' => url(env('ADMIN_URL').'/account_settings'), 'method' => 'POST', 'files' => true]) !!}

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('email', __('admin.email') . " <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('email', Auth::user()->email, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('admin.email')])}}
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('current-password', __('admin.current_password') . " <span class=\"text-danger\">*</span>", [], false)}}
                {{ Form::password('current-password', array('class' => 'form-control')) }}
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('new-password', __('admin.new_password'))}}
                {{ Form::password('new-password', array('class' => 'form-control')) }}
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('new-password_confirmation', __('admin.confirm_new_password'))}}
                {{ Form::password('new-password_confirmation', array('class' => 'form-control')) }}
            </div>
        </div>

        {{ Form::submit(__('admin.submit'), ['class' => 'btn button-green']) }}

    </div>
</div>

{!! Form::close() !!}

@stop