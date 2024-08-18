@extends('adminlte::page')

@section('content_header', __('admin.apps'))

@section('content')

@include('adminlte::inc.messages')

<div class="row">
    <div class="col-12">
        <a href="{{ asset(env('ADMIN_URL').'/apps/create') }}" class="btn button-green mb-3"><i class="fas fa-plus-square"></i>
            @lang('admin.create_app')</a>
        <div class="card">

            <div class="table-responsive">
                <table class="table table-hover text-nowrap m-0" id="table" data-delete-prompt-title="@lang('admin.oops')" data-delete-prompt-body="@lang('admin.delete_prompt')" data-yes="@lang('admin.yes')" data-cancel="@lang('admin.cancel')">
                    <thead>
                        <tr>
                            <th class="col-1 text-center">
                                <div class="icheck-wetasphalt">
                                    <input id="check-all" name="check-all" class="check-all" type="checkbox">
                                    <label for="check-all"></label>
                                </div>
                            </th>
                            <th class="col-1">@lang('admin.image')</th>
                            <th class="col-1">@lang('admin.id')</th>
                            <th class="col-4">@lang('admin.title')</th>
                            <th class="col-1">@lang('admin.page_views')</th>
                            <th class="col-1">@lang('admin.versions')</th>
                            <th class="col-2">@lang('admin.date')</th>
                            <th class="col-1"><i class="fas fa-align-justify"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                        @php if(empty($row->image)){ $row->image='no_image.png'; }@endphp
                        <tr id="{{$row->id}}">
                            <td class="text-center">

                                <div class="icheck-wetasphalt">
                                    <input id="app_{{$row->id}}" name="submissions[]" class="check" type="checkbox">
                                    <label for="app_{{$row->id}}"></label>
                                </div>

                            </td>
                            <td><img src="{{ s3_switch("$row->image") }}" class="img-w100"></td>
                            <td>{{$row->id}}</td>
                            <td><a href="{{ asset($settings['app_base']) }}/{{ $row->slug }}" class="text-dark" target="_blank">{{$row->title}}</a></td>
                            <td>{{number_format($row->page_views)}}</td>
                            <td>{{count($row->versions)}}</td>
                            <td>{{\Carbon\Carbon::parse($row->created_at)->translatedFormat('M d, Y H:i')}}</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn p-0" data-toggle="dropdown" aria-expanded="false" data-boundary="viewport">
                                        <i class="fas fa-align-justify"></i>
                                    </button>
                                    <div class="dropdown-menu mr-3">
                                        <a class="dropdown-item" href="{{ asset($settings['app_base']) }}/{{ $row->slug }}" target="_blank"><i class="fas fa-external-link-alt mr-1"></i> @lang('admin.browse')</a>
                                        <a class="dropdown-item" href="{{ route('apps.edit', $row->id)}}"><i class="fas fa-edit mr-1"></i> @lang('admin.edit')</a>
                                        <a class="dropdown-item" href="{{asset(env('ADMIN_URL') . '/versions/'.$row->id)}}"><i class="fas fa-code-branch mr-1"></i> @lang('admin.versions')</a>
                                        <div class="dropdown-divider"></div>
                                        <form id="delete_from_{{$row->id}}" method="POST" action="{{action('App\Http\Controllers\ApplicationController@destroy', $row['id'])}}">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <a href="javascript:void(0);" data-id="{{$row->id}}" class="_delete_data dropdown-item" role="button"><i class="fas fa-ban mr-1"></i> @lang('admin.delete')</a>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>
                @if(!$rows->isEmpty())
                <div class="card-footer clearfix">
                    <button type="submit" class="btn btn-danger" onclick="bulk_delete()">@lang('admin.delete')</button>
                </div>
                @endif

            </div>

        </div>

    </div>
</div>

@if($rows->isEmpty())
<h6 class="alert alert-warning-custom">@lang('admin.no_records')</h6>
@endif

{{ $rows->onEachSide(1)->links() }}

@stop
