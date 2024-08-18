@extends('adminlte::page')

@section('content_header', __('admin.edit_translation'))

@section('content')

@include('adminlte::inc.messages')

<div class="card p-2">
    <div class="m-1">

        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link text-dark active" id="language-tab" data-toggle="tab" href="#language" role="tab" aria-controls="language" aria-selected="true">@lang('admin.language_details')</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" id="frontend-tab" data-toggle="tab" href="#frontend" role="tab" aria-controls="frontend" aria-selected="false">@lang('admin.frontend')</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" id="dashboard-tab" data-toggle="tab" href="#dashboard" role="tab" aria-controls="dashboard" aria-selected="false">@lang('admin.dashboard')</a>
            </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">

            <div class="tab-pane active" id="language" role="tabpanel" aria-labelledby="language-tab">

                <!-- form -->
                <form method="POST" id="form_1" enctype="multipart/form-data" action="{{action('App\Http\Controllers\TranslationController@update', $id)}}">
                    @csrf @method('PUT')

                    <input name="translation_type" type="hidden" value="1">

                    <!-- box-body -->
                    <div class="box-body">

                        <div class="row mb-3">
                            <div class="col-md-3 my-auto">
                                {{Form::label('language', __('admin.language')." <span class=\"text-danger\">*</span>", [], false)}}
                            </div>
                            <div class="col-md-9">
                                {{Form::text('language', $translation->language, ['class' => 'form-control', 'required' => 'required', 'readonly' => $translation->code == 'en' ? true: false, 'placeholder' => __('admin.language')])}}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3 my-auto">
                                {{Form::label('language', __('admin.language_code')." <span class=\"text-danger\">*</span>", [], false)}}
                            </div>
                            <div class="col-md-9">
                                {{Form::text('code', $translation->code, ['class' => 'form-control', 'required' => 'required', 'readonly' => $translation->code == 'en' ? true: false, 'placeholder' => __('admin.language_code')])}}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3 my-auto">
                                {{Form::label('language', __('admin.og_locale_tag')." <span class=\"text-danger\">*</span>", [], false)}}
                            </div>
                            <div class="col-md-9">
                                {{Form::text('locale_code', $translation->locale_code, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('admin.og_locale_tag')])}}
                            </div>
                        </div>

                                    <div class="row mb-3">
                                <div class="col-md-3">
                                    <label>@lang('admin.country_flag') <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-md-9">
                                    <select title="@lang('admin.select_flag')" required="required" name="icon" class="form-control selectpicker" data-live-search="true">
                                        @foreach($countries as $country)
                                        <option value="{{$country['code']}}" data-content="<span class='fi fi-{{$country['code']}}'></span> {{$country['name']}}" {{ $translation->icon == $country['code'] ? ' selected' : '' }}></option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        <div class="row mb-3">
                            <div class="col-md-3 my-auto">
                                {{Form::label('text_direction', __('admin.text_direction')." <span class=\"text-danger\">*</span>", [], false)}}
                            </div>
                            <div class="col-md-9">
                                {{Form::select('text_direction', array('1' => __('admin.ltr'), '2' => __('admin.rtl')), $translation->text_direction, ['class' => 'form-control', 'id' => 'text_direction'])}}
                            </div>
                        </div>

                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        {{ Form::submit(__('admin.submit'), ['class' => 'btn button-green']) }}
                    </div>

                </form>
                <!-- /.form -->

            </div>

            <div class="tab-pane" id="frontend" role="tabpanel" aria-labelledby="frontend-tab">

                <!-- form -->
                <form method="POST" id="form_2" enctype="multipart/form-data" action="{{action('App\Http\Controllers\TranslationController@update', $id)}}">
                    @csrf @method('PUT')

                    <input name="translation_type" type="hidden" value="2">

                    <!-- box-body -->
                    <div class="box-body">

                        @foreach($translation_frontend_org as $key => $item)
                        @if ($loop->first) @continue @endif

                        <div class="row mb-3">
                            <div class="col-md-3 my-auto">
                                {{Form::label('language', $key)}}
                            </div>
                            <div class="col-md-9">
                                {{Form::text($key, $translation_frontend_target[$key], ['class' => 'form-control', 'placeholder' => $key])}}
                            </div>
                        </div>

                        @endforeach

                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        {{ Form::submit(__('admin.submit'), ['class' => 'btn button-green']) }}
                    </div>

                </form>
                <!-- /.form -->

            </div>

            <div class="tab-pane" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">

                <!-- form -->
                <form method="POST" id="form_2" enctype="multipart/form-data" action="{{action('App\Http\Controllers\TranslationController@update', $id)}}">
                    @csrf @method('PUT')

                    <input name="translation_type" type="hidden" value="3">

                    <!-- box-body -->
                    <div class="box-body">

                        @foreach($translation_admin_org as $key => $item)
                        @if ($loop->first) @continue @endif
                        
                        <div class="row mb-3">
                            <div class="col-md-3 my-auto">
                                {{Form::label('language', $key)}}
                            </div>
                            <div class="col-md-9">
                                {{Form::text($key, $translation_admin_target[$key], ['class' => 'form-control', 'placeholder' => $key])}}
                            </div>
                        </div>
                        
                        @endforeach

                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        {{ Form::submit(__('admin.submit'), ['class' => 'btn button-green']) }}
                    </div>

                </form>
                <!-- /.form -->

            </div>

        </div>

    </div>
</div>

@stop