@extends('adminlte::page')

@section('content')

@if($language_menu == '0')
@section('content_header', __('admin.seo_settings'))
@endif
@if($language_menu == '1')
@section('content_header', __('admin.languages'))
@endif

@include('adminlte::inc.messages')


@if($language_menu == '1')

<div class="card">

    <div class="table-responsive">
        <table class="table table-hover text-nowrap m-0" id="table" data-delete-prompt-title="@lang('admin.oops')" data-delete-prompt-body="@lang('admin.delete_prompt')" data-yes="@lang('admin.yes')" data-cancel="@lang('admin.cancel')">
            <thead>
                <tr>
                    <th class="col-1">@lang('admin.id')</th>
                    <th class="col-10">@lang('admin.language')</th>
                    <th class="col-1"><i class="fas fa-align-justify"></i></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($translations as $row)
                <tr id="{{$row->id}}">
                    <td>{{$row->id}}</td>
                    <td><span class="fi fi-{{$language_code[$row->id]}}"></span> {{$row->language}}</td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn p-0" data-toggle="dropdown" aria-expanded="false" data-boundary="viewport">
                                <i class="fas fa-align-justify"></i>
                            </button>
                            <div class="dropdown-menu mr-3">
                                <a class="dropdown-item" href="{{ asset(env('ADMIN_URL').'/seo_settings/'.$row->id) }}" class="btn btn-link btn-sm bg-purple"><i class="fas fa-edit"></i>
                                    @lang('admin.view')
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

@else

<div class="card p-2">
    <div class="m-1">

        {!! Form::open(['url' => url(env('ADMIN_URL').'/seo_settings/'.request()->route('id')), 'method' => 'POST']) !!}


        <span class="section-title mb-3">
            <i class="fas fa-sliders-h mr-1"></i> @lang('admin.general_settings')
        </span>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('site_title', __('admin.site_title') . " <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('site_title', $seo_settings['site_title'], ['class' => 'form-control', 'required' => 'required', 'maxlength' => 255, 'placeholder' => __('admin.site_title')])}}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('site_description', __('admin.site_description'))}}
                {{Form::textarea('site_description', $seo_settings['site_description'], ['class' => 'form-control', 'rows' => '5', 'placeholder' => __('admin.site_description')])}}
                <div class="shortcodes mt-1"><b>@lang('admin.supported_shortcodes'):</b> <a href="#">%site_title%</a><a href="#">%sep%</a><a href="#">%year%</a><a href="#">%month%</a><a href="#">%day%</a><a href="#">%month_text%</a><a href="#">%day_text%</a></div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('home_page_h1', __('admin.h1_title'), [], false)}}
                {{Form::text('home_page_h1', $seo_settings['home_page_h1'], ['class' => 'form-control', 'placeholder' => __('admin.h1_title')])}}
                <div class="shortcodes mt-1"><b>@lang('admin.supported_shortcodes'):</b> <a href="#">%site_title%</a><a href="#">%sep%</a><a href="#">%year%</a><a href="#">%month%</a><a href="#">%day%</a><a href="#">%month_text%</a><a href="#">%day_text%</a></div>
            </div>
        </div>

        <span class="section-title mb-3">
            <i class="fas fa-sliders-h mr-1"></i> @lang('admin.permalinks')
        </span>

        <div class="row">
            <div class="col-md-3 mb-3">
                {{Form::label('app_base', __('admin.app_base') . " <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('app_base', $seo_settings['app_base'], ['class' => 'form-control', 'required' => 'required', 'maxlength' => 255, 'placeholder' => __('admin.app_base')])}}
            </div>


            <div class="col-md-3 mb-3">
                {{Form::label('category_base', __('admin.category_base') . " <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('category_base', $seo_settings['category_base'], ['class' => 'form-control', 'required' => 'required', 'maxlength' => 255, 'placeholder' => __('admin.category_base')])}}
            </div>

            <div class="col-md-3 mb-3">
                {{Form::label('platform_base', __('admin.platform_base') . " <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('platform_base', $seo_settings['platform_base'], ['class' => 'form-control', 'required' => 'required', 'maxlength' => 255, 'placeholder' => __('admin.platform_base')])}}
            </div>

            <div class="col-md-3">
                {{Form::label('news_base', __('admin.news_base') . " <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('news_base', $seo_settings['news_base'], ['class' => 'form-control', 'required' => 'required', 'maxlength' => 255, 'placeholder' => __('admin.news_base')])}}
            </div>

            <div class="col-md-3 mb-3">
                {{Form::label('read_base', __('admin.read_base') . " <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('read_base', $seo_settings['read_base'], ['class' => 'form-control', 'required' => 'required', 'maxlength' => 255, 'placeholder' => __('admin.read_base')])}}
            </div>

            <div class="col-md-3 mb-3">
                {{Form::label('page_base', __('admin.page_base') . " <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('page_base', $seo_settings['page_base'], ['class' => 'form-control', 'required' => 'required', 'maxlength' => 255, 'placeholder' => __('admin.page_base')])}}
            </div>

            <div class="col-md-3 mb-3">
                {{Form::label('topic_base', __('admin.topic_base') . " <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('topic_base', $seo_settings['topic_base'], ['class' => 'form-control', 'required' => 'required', 'maxlength' => 255, 'placeholder' => __('admin.topic_base')])}}
            </div>

            <div class="col-md-3 mb-3">
                {{Form::label('tag_base', __('admin.tag_base') . " <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('tag_base', $seo_settings['tag_base'], ['class' => 'form-control', 'required' => 'required', 'maxlength' => 255, 'placeholder' => __('admin.tag_base')])}}
            </div>
            
            <div class="col-md-3 mb-3">
                {{Form::label('contact_slug', __('admin.contact_slug') . " <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('contact_slug', $seo_settings['contact_slug'], ['class' => 'form-control', 'required' => 'required', 'maxlength' => 255, 'placeholder' => __('admin.contact_slug')])}}
            </div>
        </div>

        {{ Form::submit(__('admin.submit'), ['class' => 'btn button-green']) }}
        {!! Form::close() !!}

    </div>
</div>

@endif

@stop