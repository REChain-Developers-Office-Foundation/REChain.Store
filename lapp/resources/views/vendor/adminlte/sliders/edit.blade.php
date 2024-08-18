@extends('adminlte::page')

@section('content_header', __('admin.edit_slider'))

@section('content')

@include('adminlte::inc.messages')

<div class="card p-2">
    <div class="m-1">

        {!! Form::open(['action' => ['App\Http\Controllers\SliderController@update', $row->id], 'method' => 'PUT', 'files' => true]) !!}

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('title', __('admin.title')." <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('title', $row->title, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('admin.title')])}}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <label for="link">@lang('admin.app') <span class="text-danger">*</span></label>
                {!! Form::select('link', $apps, old('link') ? old('link') : $row->link, ['class' => 'form-control selectpicker', 'required' => 'required', 'data-live-search' => 'true', 'id' => 'link' ]) !!}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <label>@lang('admin.image') <sup><a href="{{ s3_switch($row->image, 3) }}?r={{Str::random(40)}}" data-toggle="lightbox" class="right badge size-sup text-white">@lang('admin.preview')</a></sup></label>
                <div class="custom-file">
                    {{Form::label('image', __('admin.choose_image'), ['class' => 'custom-file-label'])}}
                    {{Form::file('image', ['class' => 'custom-file-input', 'id' => 'browse-image'])}}
                </div>
            </div>
        </div>     

        <div class="row mb-3">
            <div class="col-md-12">
            <div class="icheck-wetasphalt">
                {{Form::checkbox('active', null, $row->active, ['id' => 'active'])}}
                {{Form::label('active', __('admin.active'))}}
            </div>
        </div>
        
    </div>
    
    
                
        @if(count($languages) >= '1')

        <div class="row mb-3">
            <div class="col-md-12">
                <label class="section-head">@lang('admin.translations')</label>
            </div>
        </div>

        <div class="accordion" id="accordionExample">

            @foreach($languages as $language)

            <div class="card">
                <div class="card-header" id="headingOne" data-delete-prompt-title="@lang('admin.oops')" data-delete-prompt-body="@lang('admin.delete_prompt')" data-yes="@lang('admin.yes')" data-cancel="@lang('admin.cancel')">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-dark text-left" type="button" data-toggle="collapse" data-target="#id_{{$language->id}}" aria-expanded="false" aria-controls="id_{{$language->id}}">
                            {{$language->language}}
                            @if(isset($title[$language->id]))<span class="badge badge-success bg-green-custom float-right mt-1">@lang('admin.translated')</span>@endisset
                            @if(isset($title[$language->id]))<a class="delete_translation badge badge-danger bg-red-custom float-right mt-1 mr-2" href="{{url()->current()}}?delete&lang={{ $language->id }}">@lang('admin.delete')</a>@endisset
                        </button>
                    </h2>
                </div>

                <div id="id_{{$language->id}}" class="collapse" aria-labelledby="id_{{$language->id}}" data-parent="#accordionExample">
                    <div class="card-body">
                        
                    <button onclick="translate()" type="button" class="btn button-green btn-sm mb-3 translate" data-id="{{$language->id}}" data-language-from="{{$settings['site_language']}}" data-language-to="{{$language->code}}">@lang('admin.google_translate')</button>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                {{Form::label('title', __('admin.title'))}}
                                {{Form::text('titles['.$language->id.']', $title[$language->id] ?? '', ['class' => 'form-control', 'id' => 'title_'.$language->id, 'placeholder' => __('admin.title')])}}
                            </div>
                        </div>

                        <input type="hidden" name="val[{{$language->id}}]" id="val_{{$language->id}}" value="{{$content[$language->id] ?? ''}}">

                    </div>
                </div>
            </div>

            @endforeach

        </div>

        @endif

        {{ Form::submit(__('admin.submit'), ['class' => 'btn button-green']) }}
        {!! Form::close() !!}

    </div>
</div>

@stop
