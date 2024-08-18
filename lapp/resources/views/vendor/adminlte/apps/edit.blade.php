@extends('adminlte::page')

@section('content_header', __('admin.edit_app'))

@section('content')

@include('adminlte::inc.messages')

<div class="card p-2">
    <div class="m-1">

        {!! Form::open(['action' => ['App\Http\Controllers\ApplicationController@update', $row->id], 'method' => 'PUT', 'files' => true]) !!}

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
            <div class="col-md-1 mb-md-0 mb-3">
                <div class="position-relative" id="headingTwo" data-delete-prompt-title="@lang('admin.oops')" data-delete-prompt-body="@lang('admin.delete_prompt')" data-yes="@lang('admin.yes')" data-cancel="@lang('admin.cancel')">
                    <img src="{{ s3_switch("$row->image") }}?r={{Str::random(40)}}" class="img-fluid">
                    @if($row->image != 'no_image.png')
                    <a href="{{url()->current()}}?remove_image&id={{ $row->id }}" class="remove_image">
                        <i class="fa fa-times-circle pull-right position-absolute image-removal"></i>
                    </a>
                    @endif
                </div>
            </div>
            
            <div class="col-md-3 my-auto">
                <div class="custom-file">
                    {{Form::label('image', __('admin.choose_image'), ['class' => 'custom-file-label'])}}
                    {{Form::file('image', ['class' => 'custom-file-input', 'id' => 'browse-image'])}}
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('description', __('admin.description')." <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::textarea('description', $row->description, ['class' => 'form-control', 'rows' => '3', 'required' => 'required', 'placeholder' => __('admin.description')])}}
            </div>
        </div>

        <div class="row mb-1">
            <div class="col-md-12">
                {{Form::label('details', __('admin.content'))}}
                <div class="clearfix"></div>
                <button type="button" class="btn button-green btn-sm mb-3 regenerate">@lang('admin.openai_regenerate') <i class="fas fa-spinner fa-spin" id="translation_regenerate" style="display: none;"></i></button>
                <button type="button" class="btn button-green btn-sm mb-3" data-toggle="modal" data-target="#exampleModal" data-whatever="@mdo">@lang('admin.openai_generate')</button>

                {{Form::textarea('details', $row->details, ['class' => 'textarea textarea-style', 'id' => 'details', 'placeholder' => __('admin.content')])}}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('tags', __('admin.tags'))}}
                {{Form::text('tags', $tags, ['class' => 'form-control', 'data-role' => 'tagsinput', 'placeholder' => __('admin.tags')])}}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('package_name', __('admin.package_name'))}}
                {{Form::text('package_name', $row->package_name, ['class' => 'form-control', 'placeholder' => __('admin.package_name')])}}
            </div>
        </div>

        <!-- row -->
        <div class="row mb-3">

            <div class="col-md-4">
                <label for="categories">@lang('admin.categories') <span class="text-danger">*</span></label>
                {!! Form::select('categories[]', $categories, old('categories') ? old('categories') : $row->categories->pluck('id')->toArray(), ['class' => 'form-control selectpicker', 'required' => 'required', 'multiple' => 'multiple', 'data-live-search' => 'true', 'id' => 'categories' ]) !!}
            </div>

            <div class="col-md-4">
                <label for="platforms">@lang('admin.platforms') <span class="text-danger">*</span></label>
                {!! Form::select('platforms[]', $platforms, old('platforms') ? old('platforms') : $row->platforms->pluck('id')->toArray(), ['class' => 'form-control selectpicker', 'required' => 'required', 'multiple' => 'multiple', 'data-live-search' => 'true', 'id' => 'platforms' ]) !!}
            </div>

            <div class="col-md-4">
                <label for="type">@lang('admin.button_type') <span class="text-danger">*</span></label>
                {{Form::select('type', array('1' => __('admin.download_now'), '2' => __('admin.visit_page')), $row->type, ['class' => 'form-control', 'id' => 'type'])}}
            </div>

        </div>
        <!-- /.row -->

        <!-- row -->
        <div class="row mb-3">

            <div class="col-md-4">
                {{Form::label('developer', __('admin.developer'))}}
                {{Form::text('developer', $row->developer, ['class' => 'form-control', 'placeholder' => __('admin.developer')])}}
            </div>

            <div class="col-md-4">
                {{Form::label('license', __('admin.license'))}}
                {{Form::text('license', $row->license, ['class' => 'form-control', 'placeholder' => __('admin.license')])}}
            </div>

            <div class="col-md-4">
                {{Form::label('buy_url', __('admin.buy_url'))}}
                {{Form::text('buy_url', $row->buy_url, ['class' => 'form-control', 'placeholder' => __('admin.buy_url')])}}
            </div>

        </div>
        <!-- /.row -->

        <!-- row -->
        <div class="row mb-3">

            <div class="col-md-4">
                {{Form::label('page_views', __('admin.page_views')." <span class=\"text-danger\">*</span>", [], false)}}
                {{Form::text('page_views', $row->page_views, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('admin.page_views')])}}
            </div>

            <div class="col-md-4">
                {{Form::label('up_votes', __('admin.up_votes'))}}
                {{Form::text('up_votes', $row->up_votes, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('admin.up_votes')])}}
            </div>

            <div class="col-md-4">
                {{Form::label('down_votes', __('admin.down_votes'))}}
                {{Form::text('down_votes', $row->down_votes, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('admin.down_votes')])}}
            </div>

        </div>
        <!-- /.row -->

        <!-- row -->
        <div class="row mb-3">

            <div class="col-md-3">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('featured', null, $row->featured, ['id' => 'featured'])}}
                    {{Form::label('featured', __('admin.featured'))}}
                </div>
            </div>

            <div class="col-md-3">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('pinned', null, $row->pinned, ['id' => 'pinned'])}}
                    {{Form::label('pinned', __('admin.pinned'))}}
                </div>
            </div>

            <div class="col-md-3">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('editors_choice', null, $row->editors_choice, ['id' => 'editors_choice'])}}
                    {{Form::label('editors_choice', __('admin.editors_choice'))}}
                </div>
            </div>

            <div class="col-md-3">
                <div class="icheck-wetasphalt">
                    {{Form::checkbox('must_have', null, $row->must_have, ['id' => 'must_have'])}}
                    {{Form::label('must_have', __('admin.must_have'))}}
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
                                {{Form::label('details', __('admin.content'))}}
                                {{Form::textarea('details_data['.$language->id.']', $details[$language->id] ?? '', ['class' => 'textarea language-area details_data_'.$language->id.'', 'id' => $language->id, 'placeholder' => __('admin.content')])}}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                {{Form::label('custom_title', __('admin.custom_title'))}}
                                {{Form::text('custom_titles['.$language->id.']', $custom_title_lang[$language->id] ?? '', ['class' => 'form-control', 'placeholder' => __('admin.custom_title')])}}
                                <div class="shortcodes mt-1"><b>@lang('admin.supported_shortcodes'):</b> <a href="#">%app_title%</a><a href="#">%categories%</a><a href="#">%platforms%</a><a href="#">%site_title%</a><a href="#">%sep%</a><a href="#">%year%</a><a href="#">%month%</a><a href="#">%day%</a><a href="#">%month_text%</a><a href="#">%day_text%</a></div>
                            </div>
                        </div>


                        <div class="row mb-3">
                            <div class="col-md-12">
                                {{Form::label('custom_description', __('admin.custom_description'))}}
                                {{Form::textarea('custom_descriptions['.$language->id.']', $custom_description_lang[$language->id] ?? '', ['class' => 'form-control', 'rows' => '3', 'placeholder' => __('admin.custom_description')])}}
                                <div class="shortcodes mt-1"><b>@lang('admin.supported_shortcodes'):</b> <a href="#">%app_title%</a><a href="#">%categories%</a><a href="#">%platforms%</a><a href="#">%site_title%</a><a href="#">%sep%</a><a href="#">%year%</a><a href="#">%month%</a><a href="#">%day%</a><a href="#">%month_text%</a><a href="#">%day_text%</a></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                {{Form::label('custom_h1', __('admin.custom_h1'))}}
                                {{Form::text('custom_h1s['.$language->id.']', $custom_h1_lang[$language->id] ?? '', ['class' => 'form-control', 'placeholder' => __('admin.custom_h1')])}}
                                <div class="shortcodes mt-1"><b>@lang('admin.supported_shortcodes'):</b> <a href="#">%app_title%</a><a href="#">%categories%</a><a href="#">%platforms%</a><a href="#">%site_title%</a><a href="#">%sep%</a><a href="#">%year%</a><a href="#">%month%</a><a href="#">%day%</a><a href="#">%month_text%</a><a href="#">%day_text%</a></div>
                            </div>
                        </div>


                        <input type="hidden" name="val[{{$language->id}}]" id="val_{{$language->id}}" value="{{$details[$language->id] ?? ''}}">

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
                <div class="shortcodes mt-1"><b>@lang('admin.supported_shortcodes'):</b> <a href="#">%app_title%</a><a href="#">%categories%</a><a href="#">%platforms%</a><a href="#">%site_title%</a><a href="#">%sep%</a><a href="#">%year%</a><a href="#">%month%</a><a href="#">%day%</a><a href="#">%month_text%</a><a href="#">%day_text%</a></div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('custom_description', __('admin.custom_description'))}}
                {{Form::textarea('custom_description', $row->custom_description, ['class' => 'form-control', 'rows' => '3', 'placeholder' => __('admin.custom_description')])}}
                <div class="shortcodes mt-1"><b>@lang('admin.supported_shortcodes'):</b> <a href="#">%app_title%</a><a href="#">%categories%</a><a href="#">%platforms%</a><a href="#">%site_title%</a><a href="#">%sep%</a><a href="#">%year%</a><a href="#">%month%</a><a href="#">%day%</a><a href="#">%month_text%</a><a href="#">%day_text%</a></div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                {{Form::label('custom_h1', __('admin.custom_h1'))}}
                {{Form::text('custom_h1', $row->custom_h1, ['class' => 'form-control', 'placeholder' => __('admin.custom_h1')])}}
                <div class="shortcodes mt-1"><b>@lang('admin.supported_shortcodes'):</b> <a href="#">%app_title%</a><a href="#">%categories%</a><a href="#">%platforms%</a><a href="#">%site_title%</a><a href="#">%sep%</a><a href="#">%year%</a><a href="#">%month%</a><a href="#">%day%</a><a href="#">%month_text%</a><a href="#">%day_text%</a></div>
            </div>
        </div>

        {{ Form::submit(__('admin.submit'), ['class' => 'btn button-green']) }}
        {!! Form::close() !!}

    </div>
</div>

<div class="m-1">

    <div class="row mb-3">
        <div class="col-md-12">
            <label class="section-head">@lang('admin.versions')</label>
        </div>
    </div>

    <a href="{{ asset(env('ADMIN_URL').'/versions/create?app='.$id) }}" class="btn button-green mb-3"><i class="fas fa-plus-square"></i>
        @lang('admin.create_version')</a>

    @if($versions->isEmpty())
    <h6 class="alert alert-warning-custom">@lang('admin.no_records')</h6>
    @else

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover text-nowrap m-0" id="table" data-delete-prompt-title="@lang('admin.oops')" data-delete-prompt-body="@lang('admin.delete_prompt')" data-yes="@lang('admin.yes')" data-cancel="@lang('admin.cancel')">
                <thead>
                    <tr>
                        <th class="col-1">@lang('admin.id')</th>
                        <th class="col-7">@lang('admin.version')</th>
                        <th class="col-1">@lang('admin.downloads')</th>
                        <th class="col-2">@lang('admin.date')</th>
                        <th class="col-1"><i class="fas fa-align-justify"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($versions as $version)
                    <tr id="{{$version->id}}">
                        <td>{{$version->id}}</td>
                        <td>{{$version->version ?? 'N/A'}}</td>
                        <td>{{number_format($version->counter)}}</td>
                        <td>{{\Carbon\Carbon::parse($version->created_at)->translatedFormat('M d, Y')}}</td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn p-0" data-toggle="dropdown" aria-expanded="false" data-boundary="viewport">
                                    <i class="fas fa-align-justify"></i>
                                </button>
                                <div class="dropdown-menu mr-3">
                                    <a class="dropdown-item" href="{{ route('versions.edit', $version->id)}}"><i class="fas fa-edit mr-1"></i> @lang('admin.edit')</a>
                                    <div class="dropdown-divider"></div>
                                    <form id="delete_from_{{$version->id}}" method="POST" action="{{action('App\Http\Controllers\VersionController@destroy', $version['id'])}}">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                        <a href="javascript:void(0);" data-id="{{$version->id}}" class="dropdown-item _delete_data" role="button"><i class="fas fa-ban mr-1"></i> @lang('admin.delete')</a>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    @endif

</div>

<div class="card p-2">
    <div class="m-1">

        <div class="row mb-3">
            <div class="col-md-12">
                <label class="section-head">@lang('admin.screenshots')</label>
            </div>
        </div>

        <!-- screenshots -->
        <div id="screenshots" data-succesfully-deleted="@lang('admin.content_succesfully_deleted')" data-success="@lang('admin.success')" data-upload-message="@lang('admin.content_succesfully_uploaded')" data-error="@lang('admin.error')" data-uploaded="@lang('admin.uploaded')">

            <form method="POST" action="{{ route('upload', ['app_id'=>$row->id]) }}" id="screenshot_form" enctype="multipart/form-data">
                @csrf
                <div class="form-group row">
                    <div class="col-md-3">
                        <div class="custom-file">
                            {{Form::label('file', __('admin.choose_image'), ['class' => 'custom-file-label'])}}
                            {{Form::file('file[]', ['class' => 'custom-file-input', 'multiple' => 'multiple', 'id' => 'file'])}}
                        </div>
                    </div>
                </div>

                <input type="submit" name="upload" value="@lang('admin.upload')" class="btn button-green" />

            </form>

            <div class="progress-box d-none mt-3">
                <div class="progress-upload">
                    <div class="progress-bar" aria-valuenow="" aria-valuemin="0" aria-valuemax="100">
                        0%
                    </div>
                </div>
            </div>

            <div id="success" class="row">
                @php
                if ($row->screenshots != "") {

                $mysplit = explode(',', $row->screenshots);
                $screenshot_data = array_reverse($mysplit);

                $image_code_s = '';
                foreach($screenshot_data as $screenshot) {
                $image_code_s .= '<div class="col-md-2 mb-1 text-center"><a href="'.s3_switch($screenshot, 6).'" data-toggle="lightbox"><img src="'.s3_switch($screenshot, 6).'" class="img-thumbnail mt-3" /></a><button type="button" data-name="'.$screenshot.'" data-app-id="'.$row->id.'" class="btn btn-danger mt-3 remove_screenshot">' . __('admin.delete') . '</button></div>';
                }
                echo $image_code_s;
                } else {
                echo '<div class="col-md-12 text-center mb-1 mt-3">
                    <h6 class="alert alert-warning-custom mb-0">' . __('admin.no_screenshots_yet') . '</h6>
                </div>';
                }
                @endphp
            </div>

        </div>
        <!-- /.screenshots -->

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