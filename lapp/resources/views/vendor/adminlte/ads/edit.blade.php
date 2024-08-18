@extends('adminlte::page')

@section('content_header', __('admin.edit_ad'))

@section('content')

@include('adminlte::inc.messages')

<div class="card p-2">
    <div class="m-1">

        {!! Form::open(['action' => ['App\Http\Controllers\AdController@update', $row->id], 'method' => 'PUT']) !!}

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('title', __('admin.ad_spot'))}}
                {{Form::text('title', __('admin.'.$row['title']), ['class' => 'form-control', 'placeholder' => __('admin.ad_spot'), 'readonly' => 'true'])}}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('code', __('admin.html_code'))}}
                {{Form::textarea('code', $row->code, ['class' => 'form-control', 'rows' => '10', 'placeholder' => __('admin.html_code')])}}
            </div>
        </div>

        {{ Form::submit(__('admin.submit'), ['class' => 'btn button-green']) }}
        {!! Form::close() !!}

    </div>
</div>

@stop