@extends('adminlte::page')

@section('content_header', __('admin.edit_news'))

@section('content')

@include('adminlte::inc.messages')

<div class="card p-2">
    <div class="m-1">

        {!! Form::open(['action' => ['App\Http\Controllers\NewsController@update', $row->id], 'method' => 'PUT', 'files' => true]) !!}

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
                {{Form::label('description', __('admin.description')." <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::textarea('description', $row->description, ['class' => 'form-control', 'rows' => '2', 'required' => 'required', 'placeholder' => __('admin.description')])}}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <label for="categories">@lang('admin.categories') <span class="text-danger">*</span></label>
                {!! Form::select('categories[]', $categories, old('categories') ? old('categories') : $row->categories->pluck('id')->toArray(), ['class' => 'form-control selectpicker', 'required' => 'required', 'multiple' => 'multiple', 'data-live-search' => 'true', 'id' => 'categories' ]) !!}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('content', __('admin.content')." <span class=\"text-danger\">*</span>", [], false)}}
                <div class="clearfix"></div>
                <button type="button" class="btn button-green btn-sm mb-3 regenerate">@lang('admin.openai_regenerate') <i class="fas fa-spinner fa-spin" id="translation_regenerate" style="display: none;"></i></button>
                <button type="button" class="btn button-green btn-sm mb-3" data-toggle="modal" data-target="#exampleModal" data-whatever="@mdo">@lang('admin.openai_generate')</button>
                {{Form::textarea('content', $row->content, ['class' => 'textarea textarea-style', 'id' => 'details', 'required' => 'required', 'placeholder' => __('admin.content')])}}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <label>@lang('admin.image') <sup><a href="{{ s3_switch($row->image, 1) }}?r={{Str::random(40)}}" data-toggle="lightbox" class="right badge size-sup text-white">@lang('admin.preview')</a></sup></label>
                <div class="custom-file">
                    {{Form::label('image', __('admin.choose_image'), ['class' => 'custom-file-label'])}}
                    {{Form::file('image', ['class' => 'custom-file-input', 'id' => 'browse-image'])}}
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('page_views', __('admin.page_views')." <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('page_views', $row->page_views, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('admin.page_views')])}}
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

                        <div class="row mb-3">
                            <div class="col-md-12">
                                {{Form::label('description', __('admin.description'))}}
                                {{Form::textarea('descriptions['.$language->id.']', $description[$language->id] ?? '', ['class' => 'form-control', 'id' => 'description_'.$language->id, 'rows' => '3', 'placeholder' => __('admin.description')])}}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                {{Form::label('content', __('admin.content'))}}
                                {{Form::textarea('contents['.$language->id.']', $content[$language->id] ?? '', ['class' => 'textarea language-area details_data_'.$language->id.'', 'id' => $language->id, 'placeholder' => __('admin.content')])}}
                            </div>
                        </div>

                        <input type="hidden" name="val[{{$language->id}}]" id="val_{{$language->id}}" value="{{$content[$language->id] ?? ''}}">

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
                <div class="shortcodes mt-1"><b>@lang('admin.supported_shortcodes'):</b> <a href="#">%news_title%</a><a href="#">%site_title%</a><a href="#">%sep%</a><a href="#">%year%</a><a href="#">%month%</a><a href="#">%day%</a><a href="#">%month_text%</a><a href="#">%day_text%</a></div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('custom_description', __('admin.custom_description'))}}
                {{Form::textarea('custom_description', $row->custom_description, ['class' => 'form-control', 'rows' => '3', 'placeholder' => __('admin.custom_description')])}}
                <div class="shortcodes mt-1"><b>@lang('admin.supported_shortcodes'):</b> <a href="#">%news_title%</a><a href="#">%site_title%</a><a href="#">%sep%</a><a href="#">%year%</a><a href="#">%month%</a><a href="#">%day%</a><a href="#">%month_text%</a><a href="#">%day_text%</a></div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('custom_h1', __('admin.custom_h1'))}}
                {{Form::text('custom_h1', $row->custom_h1, ['class' => 'form-control', 'placeholder' => __('admin.custom_h1')])}}
                <div class="shortcodes mt-1"><b>@lang('admin.supported_shortcodes'):</b> <a href="#">%news_title%</a><a href="#">%site_title%</a><a href="#">%sep%</a><a href="#">%year%</a><a href="#">%month%</a><a href="#">%day%</a><a href="#">%month_text%</a><a href="#">%day_text%</a></div>
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