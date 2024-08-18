@extends('adminlte::page')

@section('content_header', __('admin.create_app'))

@section('content')

@include('adminlte::inc.messages')

<div class="card p-2">
    <div class="m-1">

        {!! Form::open(['action' => 'App\Http\Controllers\ApplicationController@store', 'method' => 'POST', 'files' => true]) !!}

        <input type="hidden" name="image" value="{{$app['image']}}" />
        <input type="hidden" name="screenshots" value="{{$app['screenshots']}}" />
        <input type="hidden" name="app_store" value="2" />

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('title', __('admin.title')." <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('title', $app['title'], ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('admin.title')])}}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('slug', __('admin.slug'))}}
                {{Form::text('slug', '', ['class' => 'form-control', 'placeholder' => __('admin.slug')])}}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-1 mb-md-0 mb-3">
                <img src="{{$app['image']}}" class="img-fluid">
            </div>
            <div class="col-md-3 my-auto">
                <div class="custom-file">
                    {{Form::label('different_image', __('admin.choose_image'), ['class' => 'custom-file-label'])}}
                    {{Form::file('different_image', ['class' => 'custom-file-input'])}}
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('description', __('admin.description')." <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::textarea('description', $description, ['class' => 'form-control', 'rows' => '3', 'required' => 'required', 'placeholder' => __('admin.description')])}}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('tags', __('admin.tags'))}}
                {{Form::text('tags', '', ['class' => 'form-control', 'data-role' => 'tagsinput', 'placeholder' => __('admin.tags')])}}
            </div>
        </div>

        <!-- row -->
        <div class="row mb-3">

            <div class="col-md-4">
                <label for="categories">@lang('admin.categories') <span class="text-danger">*</span></label>
                {!! Form::select('categories[]', $categories, old('categories') ?? $item_category, ['class' => 'form-control selectpicker', 'required' => 'required', 'multiple' => 'multiple', 'data-live-search' => 'true', 'id' => 'categories' ]) !!}
            </div>

            <div class="col-md-4">
                <label for="platforms">@lang('admin.platforms') <span class="text-danger">*</span></label>
                {!! Form::select('platforms[]', $platforms, old('platforms') ?? $item_platform, ['class' => 'form-control selectpicker', 'required' => 'required', 'multiple' => 'multiple', 'data-live-search' => 'true', 'id' => 'platforms' ]) !!}
            </div>

            <div class="col-md-4">
                <label for="type">@lang('admin.button_type') <span class="text-danger">*</span></label>
                {{Form::select('type', array('1' => __('admin.download_now'), '2' => __('admin.visit_page')), 2, ['class' => 'form-control', 'id' => 'type'])}}
            </div>

        </div>
        <!-- /.row -->

        <!-- row -->
        <div class="row mb-3">

            <div class="col-md-4">
                {{Form::label('developer', __('admin.developer'))}}
                {{Form::text('developer', $app['developer'], ['class' => 'form-control', 'placeholder' => __('admin.developer')])}}
            </div>

            <div class="col-md-4">
                {{Form::label('license', __('admin.license'))}}
                {{Form::text('license', $app['price'], ['class' => 'form-control', 'placeholder' => __('admin.license')])}}
            </div>

            <div class="col-md-4">
                {{Form::label('buy_url', __('admin.buy_url'))}}
                {{Form::text('buy_url', '', ['class' => 'form-control', 'placeholder' => __('admin.buy_url')])}}
            </div>

        </div>
        <!-- /.row -->

        <!-- row -->
        <div class="row mb-3">

            <div class="col-md-4">
                {{Form::label('page_views', __('admin.page_views')." <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('page_views', '0', ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('admin.page_views')])}}
            </div>

            <div class="col-md-4">
                {{Form::label('up_votes', __('admin.up_votes')." <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('up_votes', '0', ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('admin.up_votes')])}}
            </div>

            <div class="col-md-4">
                {{Form::label('down_votes', __('admin.down_votes')." <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('down_votes', '0', ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('admin.down_votes')])}}
            </div>

        </div>
        <!-- /.row -->

        <!-- row -->
        <div class="row mb-3">

            <div class="col-md-3">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('featured', null, null, ['id' => 'featured'])}}
                    {{Form::label('featured', __('admin.featured'))}}
                </div>
            </div>

            <div class="col-md-3">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('pinned', null, null, ['id' => 'pinned'])}}
                    {{Form::label('pinned', __('admin.pinned'))}}
                </div>
            </div>

            <div class="col-md-3">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('editors_choice', null, null, ['id' => 'editors_choice'])}}
                    {{Form::label('editors_choice', __('admin.editors_choice'))}}
                </div>
            </div>

            <div class="col-md-3">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('must_have', null, null, ['id' => 'must_have'])}}
                    {{Form::label('must_have', __('admin.must_have'))}}
                </div>
            </div>

        </div>
        <!-- /.row -->

        <div class="row mb-2">
            <div class="col-md-12">
                {{Form::label('details', __('admin.content'))}}
                <div class="clearfix"></div>
                <button type="button" class="btn button-green btn-sm mb-3 regenerate">@lang('admin.openai_regenerate') <i class="fas fa-spinner fa-spin" id="translation_regenerate" style="display: none;"></i></button>
                <button type="button" class="btn button-green btn-sm mb-3" data-toggle="modal" data-target="#exampleModal" data-whatever="@mdo">@lang('admin.openai_generate')</button>
                {{Form::textarea('details', $detailed_description, ['class' => 'textarea textarea-style', 'id' => 'details', 'placeholder' => __('admin.content')])}}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <label class="section-head">@lang('admin.version_details')</label>
            </div>
        </div>

        <!-- row -->
        <div class="row mb-3">

            <div class="col-md-3">
                {{Form::label('version', __('admin.version'))}}
                {{Form::text('version', $app['version'], ['class' => 'form-control', 'placeholder' => __('admin.version')])}}
            </div>

            <div class="col-md-3">
                {{Form::label('file_size', __('admin.file_size'))}}
                {{Form::text('file_size', $app['file_size'], ['class' => 'form-control', 'placeholder' => __('admin.file_size')])}}
            </div>

            <div class="col-md-3">
                {{Form::label('counter', __('admin.downloads')." <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('counter', '0', ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('admin.downloads')])}}
            </div>

            <div class="col-md-3">
                <label>@lang('admin.file')</label>
                <div class="custom-file">
                    {{Form::label('file', __('admin.choose_file'), ['class' => 'custom-file-label'])}}
                    {{Form::file('file', ['class' => 'custom-file-input'])}}
                </div>
            </div>

        </div>
        <!-- /.row -->

        <!-- row -->
        <div class="row mb-3">

            <div class="col-md-6">
                {{Form::label('url', __('admin.download_page_url'))}}
                {{Form::text('url', $app['url'], ['class' => 'form-control', 'placeholder' => __('admin.download_page_url')])}}
            </div>

        </div>
        <!-- /.row -->

        <div class="row mb-3">
            <div class="col-md-12">
                <label class="section-head">@lang('admin.seo_settings')</label>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('custom_title', __('admin.custom_title'))}}
                {{Form::text('custom_title', '', ['class' => 'form-control', 'placeholder' => __('admin.custom_title')])}}
                <div class="shortcodes mt-1"><b>@lang('admin.supported_shortcodes'):</b> <a href="#">%app_title%</a><a href="#">%categories%</a><a href="#">%platforms%</a><a href="#">%site_title%</a><a href="#">%sep%</a><a href="#">%year%</a><a href="#">%month%</a><a href="#">%day%</a><a href="#">%month_text%</a><a href="#">%day_text%</a></div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('custom_description', __('admin.custom_description'))}}
                {{Form::textarea('custom_description', '', ['class' => 'form-control', 'rows' => '3', 'placeholder' => __('admin.custom_description')])}}
                <div class="shortcodes mt-1"><b>@lang('admin.supported_shortcodes'):</b> <a href="#">%app_title%</a><a href="#">%categories%</a><a href="#">%platforms%</a><a href="#">%site_title%</a><a href="#">%sep%</a><a href="#">%year%</a><a href="#">%month%</a><a href="#">%day%</a><a href="#">%month_text%</a><a href="#">%day_text%</a></div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('custom_h1', __('admin.custom_h1'))}}
                {{Form::text('custom_h1', '', ['class' => 'form-control', 'placeholder' => __('admin.custom_h1')])}}
                <div class="shortcodes mt-1"><b>@lang('admin.supported_shortcodes'):</b> <a href="#">%app_title%</a><a href="#">%categories%</a><a href="#">%platforms%</a><a href="#">%site_title%</a><a href="#">%sep%</a><a href="#">%year%</a><a href="#">%month%</a><a href="#">%day%</a><a href="#">%month_text%</a><a href="#">%day_text%</a></div>
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