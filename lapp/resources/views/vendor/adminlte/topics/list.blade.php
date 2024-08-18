@extends('adminlte::page')

@section('content_header', __('admin.topic_items'))

@section('content')

@include('adminlte::inc.messages')

<div class="callout callout-dark">
    <p><i class="fas fa-info-circle"></i> @lang('admin.sortable_items')</p>
</div>

<div class="card p-2">
    <div class="m-1">

        @foreach ($apps as $app)
        @php $app_title[$app->id]=$app->title; @endphp
        @php $app_image[$app->id]=$app->image; @endphp
        @endforeach

        {!! Form::open(['action' => ['App\Http\Controllers\TopicItemController@update', $id], 'method' => 'PUT', 'files' => true]) !!}

        <!-- box-body -->
        <div class="box-body no-padding">

            <div class="table-responsive">
                <table class="table table-striped" id="table" data-delete-prompt="@lang('admin.delete_prompt')" data-yes="@lang('admin.yes')" data-cancel="@lang('admin.cancel')" data-delete="@lang('admin.delete')" data-app-title="@lang('admin.app')">
                    <thead>
                        <tr>
                            <th class="col-1 border-0 pt-0 mt-0">@lang('admin.image')</th>
                            <th class="col-11 border-0 pt-0 mt-0">@lang('admin.app')</th>
                        </tr>
                    </thead>
                    <tbody class="sortable-topics">

                        @if (count($item_list) >= 1)
                        @foreach($item_list as $key => $row)
                        @if (!empty($app_title[$row]))

                        <tr class='topic_list' id="{{ $key }}">
                            <td><img src="{{ s3_switch($app_image[$row] ?? 'no_image.png') }}" id='img_{{ $key }}' class="img-w100"></td>
                            <td><input type='text' onclick='this.focus(); this.select()' class='topiclist form-control' id='topiclist_{{ $key }}' placeholder='@lang('admin.app')' value="{{ $app_title[$row] }}"><input type='button' value='@lang('admin.delete')' class="btn btn-sm bg-red mt-3 _delete_topic" id='remove'><input type='hidden' class='appid' id='appid_{{ $key }}' name="appid_{{ $key }}" value="{{ $row }}"></td>
                        </tr>
                        @endif
                        @endforeach
                        @else
                        <tr class='topic_list' id="1">
                            <td><img src="{{ s3_switch('no_image.png') }}" id='img_1' class="img-w100"></td>
                            <td><input type='text' onclick='this.focus(); this.select()' class='topiclist form-control' id='topiclist_1' placeholder='@lang('admin.app')'><input type='button' value='@lang('admin.delete')' class='btn btn-sm bg-red mt-3 _delete_topic' id='remove'><input type='hidden' class='appid' id='appid_1' name="appid_1"></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

        </div>
        <!-- /.box-body -->

        <div class="box-footer">
            {{ Form::button(__('admin.add_more'), ['class' => 'btn button-purple', 'id' => 'add_more']) }}
            {{ Form::submit(__('admin.submit'), ['class' => 'btn button-green ml-2']) }}
        </div>

        {!! Form::close() !!}

    </div>
</div>

@endsection
