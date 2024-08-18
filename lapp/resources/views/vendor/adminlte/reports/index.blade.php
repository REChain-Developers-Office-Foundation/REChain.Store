@extends('adminlte::page')

@section('content_header', __('admin.reports'))

@section('content')

@include('adminlte::inc.messages')

<div class="row">
    <div class="col-12">

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover text-nowrap m-0" id="table">
                    <thead>
                        <tr>
                            <th class="col-1">@lang('admin.id')</th>
                            <th class="col-4">@lang('admin.app')</th>
                            <th class="col-3">@lang('admin.reason')</th>
                            <th class="col-1">@lang('admin.status')</th>
                            <th class="col-2">@lang('admin.date')</th>
                            <th class="col-1"><i class="fas fa-align-justify"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)

                        <div class="modal fade" id="modal-{{ $row->id }}">
                            <div class="modal-dialog">
                                <div class="modal-content p-0">
                                    <div class="modal-header py-2">
                                        <h6 class="modal-title font-weight-bold">@lang('admin.report_details')</h6>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body pb-0">
                                        <ul class="list-unstyled px-2 mx-1 pt-1">
                                            <li><strong>@lang('admin.app'):</strong> {{$row->title}}</li>
                                            <li><strong>@lang('admin.email'):</strong> {{ $row->email }}</li>
                                            <li><strong>@lang('admin.reason'):</strong> {{$reason_label[$row->reason]}}</li>
                                            <li><strong>@lang('admin.date'):</strong> {{\Carbon\Carbon::parse($row->created_at)->translatedFormat('M d, Y H:i:s')}}</li>
                                            <li><strong>@lang('admin.ip_address'):</strong> {{ $row->ip }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <tr id="{{$row->id}}">
                            <td>{{$row->id}}</td>
                            <td>{{$row->title}}</td>
                            <td>{{$reason_label[$row->reason]}}</td>
                            <td>@if ($row->solved == 0) <span class="text-warning">@lang('admin.pending')</span> @else <span class="text-success">@lang('admin.solved')</span> @endif</td>
                            <td>{{\Carbon\Carbon::parse($row->created_at)->translatedFormat('M d, Y')}}</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn p-0" data-toggle="dropdown" aria-expanded="false" data-boundary="viewport">
                                        <i class="fas fa-align-justify"></i>
                                    </button>
                                    <div class="dropdown-menu mr-3">
                                        <a class="dropdown-item" href="{{ asset($settings['app_base']) }}/{{ $row->slug }}" target="_blank"><i class="fas fa-external-link-alt mr-1"></i> @lang('admin.browse')</a>
                                        <a class="dropdown-item" href="{{ route('apps.edit', $row->app_id)}}"><i class="fas fa-edit mr-1"></i> @lang('admin.edit')</a>
                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modal-{{ $row->id }}"><i class="fas fa-info-circle mr-1"></i> @lang('admin.show_details')</a>
                                        <div class="dropdown-divider"></div>
                                        <form id="solve_from_{{$row->id}}" method="POST" action="{{action('App\Http\Controllers\ReportController@update', $row['id'])}}">
                                            {{ csrf_field() }}
                                            {{ method_field('PUT') }}
                                            <a href="javascript:void(0);" data-id="{{$row->id}}" class="dropdown-item _solve_data" role="button">
                                                @if ($row->solved == 0) <i class="fas fa-check-circle mr-1"></i> @lang('admin.mark_as_solved') @else <i class="fas fa-times-circle mr-1"></i> @lang('admin.mark_as_unsolved') @endif</a>
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

{{ $rows->onEachSide(1)->links() }}

@stop