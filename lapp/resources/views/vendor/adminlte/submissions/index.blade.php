@extends('adminlte::page')

@section('content_header', __('admin.submissions'))

@section('content')

@include('adminlte::inc.messages')

<div class="row">
    <div class="col-12">

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover text-nowrap m-0" id="table" data-delete-prompt-title="@lang('admin.oops')" data-delete-prompt-body="@lang('admin.delete_prompt')" data-yes="@lang('admin.yes')" data-cancel="@lang('admin.cancel')">
                    <thead>
                        <tr>
                            <th class="col-1">@lang('admin.id')</th>
                            <th class="col-8">@lang('admin.app')</th>
                            <th class="col-2">@lang('admin.date')</th>
                            <th class="col-1"><i class="fas fa-align-justify"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($submissions as $row)
                        
                         <div class="modal fade" id="modal-{{ $row->id }}">
                            <div class="modal-dialog">
                                <div class="modal-content p-0">
                                    <div class="modal-header py-2">
                                        <h6 class="modal-title font-weight-bold">@lang('admin.submission_details')</h6>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body pb-0">
                                        <ul class="list-unstyled px-2 mx-1 pt-1">
                                            <li><strong>@lang('admin.name'):</strong> {{ $row->name }}</li>
                                            <li><strong>@lang('admin.email'):</strong> {{ $row->email }}</li>
                                            <li><strong>@lang('admin.app'):</strong> {{ $row->title }}</li>
                                            <li><strong>@lang('admin.date'):</strong> {{\Carbon\Carbon::parse($row->created_at)->translatedFormat('M d, Y H:i:s')}}</li>
                                            <li><strong>@lang('admin.ip_address'):</strong> {{ $row->ip }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>


                            <tr>
                            <td>{{$row['id']}}</td>
                            <td>{{$row['title']}}</td>
                            <td>{{\Carbon\Carbon::parse($row->created_at)->translatedFormat('M d, Y')}}</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn p-0" data-toggle="dropdown" aria-expanded="false" data-boundary="viewport">
                                        <i class="fas fa-align-justify"></i>
                                    </button>
                                    <div class="dropdown-menu mr-3">
                                        <a class="dropdown-item" href="{{action('App\Http\Controllers\SubmissionController@show', $row['id'])}}"><i class="fas fa-arrow-circle-right mr-1"></i> @lang('admin.submit')</a>
                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modal-{{ $row->id }}"><i class="fas fa-info-circle mr-1"></i> @lang('admin.show_details')</a>
                                        <div class="dropdown-divider"></div>
                                        <form id="delete_from_{{$row->id}}" method="POST" action="{{action('App\Http\Controllers\SubmissionController@destroy', $row['id'])}}">
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

@if($submissions->isEmpty())
<h6 class="alert alert-warning-custom">@lang('admin.no_records')</h6>
@endif

{{ $submissions->onEachSide(1)->links() }}

@stop