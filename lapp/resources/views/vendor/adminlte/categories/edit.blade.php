@extends('adminlte::page')

@section('content_header', __('admin.edit_category'))

@section('content')

@include('adminlte::inc.messages')

<div class="card p-2">
    <div class="m-1">

        {!! Form::open(['action' => ['App\Http\Controllers\CategoryController@update', $row->id], 'method' => 'PUT', 'files' => true]) !!}

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('title', __('admin.title')." <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('title', $row->title, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('admin.title')])}}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('slug', __('admin.slug'))}}
                {{Form::text('slug', $row->slug, ['class' => 'form-control', 'placeholder' => __('admin.slug')])}}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <label for="fa_icon">@lang('admin.icon')</label>
                <select class="form-control selectpicker" data-live-search="true" id="fa_icon" name="fa_icon">
                    <option value="0">@lang('admin.none')</option>
                    @foreach($icons as $icon)
                    <option data-icon="{{ $icon->icon }}" value="{{ $icon->icon }}" {{ $row->fa_icon == $icon->icon ? 'selected="selected"' : '' }}>{{ $icon->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-3">
            @if(isset($row->image))
            <div class="col-md-1 mb-md-0 mb-3">
                <div class="position-relative" id="headingTwo" data-delete-prompt-title="@lang('admin.oops')" data-delete-prompt-body="@lang('admin.delete_prompt')" data-yes="@lang('admin.yes')" data-cancel="@lang('admin.cancel')">
                    <img src="{{ s3_switch("$row->image", 4) }}?r={{Str::random(40)}}" class="img-fluid">
                    <a href="{{url()->current()}}?remove_image&id={{ $row->id }}" class="remove_image">
                        <i class="fa fa-times-circle pull-right position-absolute image-removal"></i>
                    </a>
                </div>
            </div>
            @endif

            <div class="col-md-3 my-auto">
                <label>@lang('admin.image')</label>
                <div class="custom-file">
                    {{Form::label('image', __('admin.choose_image'), ['class' => 'custom-file-label'])}}
                    {{Form::file('image', ['class' => 'custom-file-input', 'id' => 'browse-image'])}}
                </div>
            </div>
        </div>

        <!-- row -->
        <div class="row mb-3">

            <div class="col-md-3">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('home_page', null, $row->home_page, ['id' => 'home_page'])}}
                    {{Form::label('home_page', __('admin.home_page'))}}
                </div>
            </div>

            <div class="col-md-3">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('footer', null, $row->footer, ['id' => 'footer'])}}
                    {{Form::label('footer', __('admin.footer'))}}
                </div>
            </div>

            <div class="col-md-3">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('navbar', null, $row->navbar, ['id' => 'navbar'])}}
                    {{Form::label('navbar', __('admin.navbar'))}}
                </div>
            </div>

            <div class="col-md-3">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('right_column', null, $row->right_column, ['id' => 'right_column'])}}
                    {{Form::label('right_column', __('admin.right_column'))}}
                </div>
            </div>

        </div>
        <!-- /.row -->

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

                        <div class="row">
                            <div class="col-md-12">
                                {{Form::label('title', __('admin.title'))}}
                                {{Form::text('titles['.$language->id.']', $title[$language->id] ?? '', ['class' => 'form-control', 'id' => 'title_'.$language->id, 'placeholder' => __('admin.title')])}}
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            @endforeach

        </div>

        @endif

        <div class="row mb-3">
            <div class="col-md-12">
                <label class="section-head">@lang('admin.seo_settings')</label>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('custom_title', __('admin.custom_title'))}}
                {{Form::text('custom_title', $row->custom_title, ['class' => 'form-control', 'placeholder' => __('admin.custom_title')])}}
                <div class="shortcodes mt-1"><b>@lang('admin.supported_shortcodes'):</b> <a href="#">%category_title%</a><a href="#">%site_title%</a><a href="#">%sep%</a><a href="#">%year%</a><a href="#">%month%</a><a href="#">%day%</a><a href="#">%month_text%</a><a href="#">%day_text%</a></div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('custom_description', __('admin.custom_description'))}}
                {{Form::textarea('custom_description', $row->custom_description, ['class' => 'form-control', 'rows' => '3', 'placeholder' => __('admin.custom_description')])}}
                <div class="shortcodes mt-1"><b>@lang('admin.supported_shortcodes'):</b> <a href="#">%category_title%</a><a href="#">%site_title%</a><a href="#">%sep%</a><a href="#">%year%</a><a href="#">%month%</a><a href="#">%day%</a><a href="#">%month_text%</a><a href="#">%day_text%</a></div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('custom_h1', __('admin.custom_h1'))}}
                {{Form::text('custom_h1', $row->custom_h1, ['class' => 'form-control', 'placeholder' => __('admin.custom_h1')])}}
                <div class="shortcodes mt-1"><b>@lang('admin.supported_shortcodes'):</b> <a href="#">%category_title%</a><a href="#">%site_title%</a><a href="#">%sep%</a><a href="#">%year%</a><a href="#">%month%</a><a href="#">%day%</a><a href="#">%month_text%</a><a href="#">%day_text%</a></div>
            </div>
        </div>

        {{ Form::submit(__('admin.submit'), ['class' => 'btn button-green']) }}
        {!! Form::close() !!}

    </div>
</div>

@stop