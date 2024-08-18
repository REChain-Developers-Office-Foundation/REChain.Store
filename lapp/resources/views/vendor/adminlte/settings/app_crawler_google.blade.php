@extends('adminlte::page')

@section('content_header', __('admin.category_settings'))

@section('content')

@include('adminlte::inc.messages')

@foreach (json_decode($crawler_categories_google) as $key => $value)
@php $all_cat=json_decode($crawler_categories_google); @endphp
@php $crawler_category_setting[$key]=$value; @endphp
@endforeach

<div class="card p-2">
    <div class="m-1">

        <!-- form -->
        <form method="POST" action="{{url(env('ADMIN_URL').'/scraper_categories_google')}}">
            @csrf @method('POST')

            <!-- row -->
            <div class="row mb-3">

                @foreach($app_categories as $id => $row )

                @if ( !property_exists($all_cat,$row->getId()) )
                @php $crawler_category_setting[$row->getId()] = '0'; @endphp
                @endif

                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{$row->getId()}}</label>
                        <select title="@lang('admin.select_category')" name="{{$row->getId()}}" class="form-control selectpicker" data-live-search="true">
                            <option value="0">@lang('admin.select_category')</option>
                            @foreach($categories as $category)
                            <option data-icon="{{ $category->fa_icon }}" value="{{ $category->id }}" {{ $crawler_category_setting[$row->getId()] == $category->id ? ' selected' : '' }}>{{ $category->title }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endforeach

            </div>
            <!-- /.row -->

            {{ Form::submit(__('admin.submit'), ['class' => 'btn button-green']) }}
            {!! Form::close() !!}

    </div>
</div>

@stop
