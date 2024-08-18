@extends('adminlte::page')

@section('content')

@section('content_header', __('admin.pwa_settings'))

@include('adminlte::inc.messages')

<div class="card p-2">
    <div class="m-1">
        
        {!! Form::open(['url' => url(env('ADMIN_URL').'/cdn_settings'), 'method' => 'POST', 'files' => true]) !!}
        {{ Form::submit(__('admin.push_assets'), ['class' => 'btn button-green']) }}
        {!! Form::close() !!}

    </div>
</div>


@stop
