@extends('adminlte::page')

@section('content_header', __('admin.sort_items'))

@section('content')

@include('adminlte::inc.messages')

<div class="row">
    <div id="notificationsContainer"></div>

    <div class="col-12">
        <div class="callout callout-dark">
            <p><i class="fas fa-info-circle"></i> @lang('admin.sortable_items')</p>
        </div>
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover text-nowrap m-0" id="table" data-delete-prompt-title="@lang('admin.oops')" data-delete-prompt-body="@lang('admin.delete_prompt')" data-yes="@lang('admin.yes')" data-cancel="@lang('admin.cancel')">
                     <thead>
                        <tr>
                            <th class="col-1">@lang('admin.id')</th>
                            <th class="col-7">@lang('admin.title')</th>
                            <th class="col-1">@lang('admin.apps')</th>
                            <th class="col-2">@lang('admin.date')</th>
                            <th class="col-1"><i class="fas fa-align-justify"></i></th>
                        </tr>
                    </thead>
                    <tbody class="sortable-items" id="platforms">
                        @foreach ($rows as $row)
                        <tr id="{{$row->id}}">
                            <td>{{$row->id}}</td>
                            <td>{{$row->title}}</td>
                            <td>{{count($row->applications)}}</td>
                            <td>{{\Carbon\Carbon::parse($row->created_at)->translatedFormat('M d, Y')}}</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn p-0" data-toggle="dropdown" aria-expanded="false" data-boundary="viewport">
                                        <i class="fas fa-align-justify"></i>
                                    </button>
                                    <div class="dropdown-menu mr-3">
                                        <a class="dropdown-item" href="{{ asset($settings['category_base']) }}/{{ $row->slug }}" target="_blank"><i class="fas fa-external-link-alt mr-1"></i> @lang('admin.browse')</a>
                                        <a class="dropdown-item" href="{{ route('categories.edit', $row->id)}}"><i class="fas fa-edit mr-1"></i> @lang('admin.edit')</a>
                                        <div class="dropdown-divider"></div>
                                        <form id="delete_from_{{$row->id}}" method="POST" action="{{action('App\Http\Controllers\CategoryController@destroy', $row['id'])}}">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <a href="javascript:void(0);" data-id="{{$row->id}}" class="dropdown-item _delete_data" role="button"><i class="fas fa-ban mr-1"></i> @lang('admin.delete')</a>
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

    </div>
</div>

@if($rows->isEmpty())
<h6 class="alert alert-warning-custom">@lang('admin.no_records')</h6>
@endif

@stop