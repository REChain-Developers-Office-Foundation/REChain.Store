@extends('adminlte::page')

@section('content_header', __('admin.create_topic'))

@section('content')

@include('adminlte::inc.messages')

<div class="card p-2">
    <div class="m-1">

        {!! Form::open(['action' => 'App\Http\Controllers\TopicController@store', 'method' => 'POST', 'files' => true]) !!}

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('title', __('admin.title')." <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('title', '', ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('admin.title')])}}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('slug', __('admin.slug'))}}
                {{Form::text('slug', '', ['class' => 'form-control', 'placeholder' => __('admin.slug')])}}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('description', __('admin.description'))}}
                {{Form::textarea('description', '', ['class' => 'form-control', 'rows' => '2', 'placeholder' => __('admin.description')])}}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <label>@lang('admin.image') <span class="text-danger">*</span></label>
                <div class="custom-file">
                    {{Form::label('image', __('admin.choose_image'), ['class' => 'custom-file-label'])}}
                    {{Form::file('image', ['class' => 'custom-file-input', 'required' => 'required', 'id' => 'browse-image'])}}
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('page_views', __('admin.page_views')." <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('page_views', '0', ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('admin.page_views')])}}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <label class="section-head">@lang('admin.seo_settings')</label>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('custom_title', __('admin.custom_title'))}}
                {{Form::text('custom_title', '', ['class' => 'form-control', 'placeholder' => __('admin.custom_title')])}}
                <div class="shortcodes mt-1"><b>@lang('admin.supported_shortcodes'):</b> <a href="#">%topic_title%</a><a href="#">%site_title%</a><a href="#">%sep%</a><a href="#">%year%</a><a href="#">%month%</a><a href="#">%day%</a><a href="#">%month_text%</a><a href="#">%day_text%</a></div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('custom_description', __('admin.custom_description'))}}
                {{Form::textarea('custom_description', '', ['class' => 'form-control', 'rows' => '3', 'placeholder' => __('admin.custom_description')])}}
                <div class="shortcodes mt-1"><b>@lang('admin.supported_shortcodes'):</b> <a href="#">%topic_title%</a><a href="#">%site_title%</a><a href="#">%sep%</a><a href="#">%year%</a><a href="#">%month%</a><a href="#">%day%</a><a href="#">%month_text%</a><a href="#">%day_text%</a></div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('custom_h1', __('admin.custom_h1'))}}
                {{Form::text('custom_h1', '', ['class' => 'form-control', 'placeholder' => __('admin.custom_h1')])}}
                <div class="shortcodes mt-1"><b>@lang('admin.supported_shortcodes'):</b> <a href="#">%topic_title%</a><a href="#">%site_title%</a><a href="#">%sep%</a><a href="#">%year%</a><a href="#">%month%</a><a href="#">%day%</a><a href="#">%month_text%</a><a href="#">%day_text%</a></div>
            </div>
        </div>

        {{ Form::submit(__('admin.submit'), ['class' => 'btn button-green']) }}
        {!! Form::close() !!}

    </div>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header px-0 pt-0 pb-2">
        <h6 class="modal-title" id="exampleModalLabel" style="font-weight: 600;">@lang('admin.openai_generate')</h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body pb-0">
        <form>
          <div class="form-group">
            <label for="message-text" class="col-form-label">@lang('admin.command')</label>
            <textarea class="form-control" id="message-text"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn button-green btn-sm openai">@lang('admin.generate') <i class="fas fa-spinner fa-spin" id="translation_generate" style="display: none;"></i></button>
      </div>
    </div>
  </div>
</div>

@stop
