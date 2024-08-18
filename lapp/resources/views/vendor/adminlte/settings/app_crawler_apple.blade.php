@extends('adminlte::page')

@section('content_header', __('admin.category_settings'))

@section('content')

@include('adminlte::inc.messages')


@foreach (json_decode($crawler_categories_apple) as $key => $value)
@php $all_cat=json_decode($crawler_categories_apple); @endphp
@php $crawler_category_setting[$key]=$value; @endphp
@endforeach

<div class="card p-2">
    <div class="m-1">

        <!-- form -->
        <form method="POST" action="{{url(env('ADMIN_URL').'/scraper_categories_apple')}}">
            @csrf @method('POST')

            <!-- row -->
            <div class="row">

                @foreach($app_categories as $id => $row )

                @if ( !property_exists($all_cat,$row) )
                @php $crawler_category_setting[$row] = '0'; @endphp
                @endif

                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{$id}}</label>
                        <select title="@lang('admin.select_category')" name="{{$id}}" class="form-control selectpicker" data-live-search="true">
                            <option value="0">@lang('admin.select_category')</option>
                            @foreach($categories as $category)
                            <option data-icon="{{ $category->fa_icon }}" value="{{ $category->id }}" {{ $crawler_category_setting[$id] == $category->id ? ' selected' : '' }}>{{ $category->title }}
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
