@extends('adminlte::page')

@section('content_header', __('admin.edit_error_handling'))

@section('content')

@include('adminlte::inc.messages')

<div class="card p-2">
    <div class="m-1">

        {!! Form::open(['action' => ['App\Http\Controllers\ErrorHandlingController@update', $id], 'method' => 'PUT']) !!}

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('code', __('admin.code'))}}
                {{Form::textarea('code', $code, ['class' => 'form-control', 'rows' => '10', 'placeholder' => __('admin.codes')])}}
            </div>
        </div>
        
        {{ Form::submit(__('admin.submit'), ['class' => 'btn button-green']) }}
        {!! Form::close() !!}

    </div>
</div>

@stop
