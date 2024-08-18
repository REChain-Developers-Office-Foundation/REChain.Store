@extends('adminlte::page')

@section('content_header', __('admin.comments'))

@section('content')

@include('adminlte::inc.messages')

<div class="row">
    <div class="col-12">

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover text-sm-nowrap m-0" id="table" data-delete-prompt-title="@lang('admin.oops')" data-delete-prompt-body="@lang('admin.delete_prompt')" data-yes="@lang('admin.yes')" data-cancel="@lang('admin.cancel')">
                    <thead>
                        <tr>
                            <th class="col-1">@lang('admin.id')</th>
                            <th class="col-3">@lang('admin.app')/@lang('admin.news')</th>
                            <th class="col-5">@lang('admin.comment')</th>
                            <th class="col-1">@lang('admin.rating')</th>
                            <th class="col-1">@lang('admin.status')</th>
                            <th class="col-1"><i class="fas fa-align-justify"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($comments as $row)
                        <div class="modal fade" id="modal-{{ $row->id }}">
                            <div class="modal-dialog">
                                <div class="modal-content p-0">
                                    <div class="modal-header py-2">
                                        <h6 class="modal-title font-weight-bold">@lang('admin.comment_details')</h6>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body pb-0">
                                        <ul class="list-unstyled px-2 mx-1 pt-1">
                                            <li><strong>@if($row->type == '1') @lang('admin.app') @else @lang('admin.news')@endif:</strong> {{$row['app_title']}}</li>
                                            <li><strong>@lang('admin.name'):</strong> {{ $row->name }}</li>
                                            <li><strong>@lang('admin.email'):</strong> {{ $row->email }}</li>
                                            <li><strong>@lang('admin.title'):</strong> {{ $row->title }}</li>
                                            <li><strong>@lang('admin.comment'):</strong> {{ $row->comment }}</li>
                                            @if($row->type == '1')
                                            <li><strong>@lang('admin.rating'):</strong> {{ $row->rating }}/5</li>
                                            @endif
                                            <li><strong>@lang('admin.date'):</strong> {{\Carbon\Carbon::parse($row->created_at)->translatedFormat('M d, Y H:i:s')}}</li>
                                            <li><strong>@lang('admin.ip_address'):</strong> {{ $row->ip }}</li>
                                            <li><strong>@lang('admin.status'):</strong> @if ($row->approval == 0) <span class="text-dark"> @lang('admin.pending')</<span> @else <span class="text-success"> @lang('admin.approved')</span> @endif</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <tr id="{{ $row->id }}">
                            <td>{{$row->id}}</td>
                            <td><a href="@if($row->type == '1'){{ asset($settings['app_base']) }}/{{ $row->slug }}@else{{ asset($settings['read_base']) }}/{{ $row->slug }}@endif" class="text-dark" target="_blank">{{$row['app_title']}}</a></td>
                            <td><b class="d-block mb-1">{{{$row->title}}}</b>{{{$row->comment}}}<i class="d-block text-right text-muted mt-1"><b>&#65293;{{{$row->name}}}</b></i></td>
                            <td>@if($row->type == '1') {{{$row->rating}}}/5 @else - @endif</td>
                            @if ($row->approval == 0)
                            <td><span class="text-dark"> @lang('admin.pending')</span></td>
                            @else
                            <td><span class="text-success"> @lang('admin.approved')</span></td>
                            @endif
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn p-0" data-toggle="dropdown" aria-expanded="false" data-boundary="viewport">
                                        <i class="fas fa-align-justify"></i>
                                    </button>
                                    <div class="dropdown-menu mr-3">
                                        <a class="dropdown-item" href="@if($row->type == '1'){{ asset($settings['app_base']) }}/{{ $row->slug }}@else{{ asset($settings['read_base']) }}/{{ $row->slug }}@endif" target="_blank"><i class="fas fa-external-link-alt mr-1"></i> @lang('admin.browse')</a>
                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modal-{{ $row->id }}"><i class="fas fa-info-circle mr-1"></i> @lang('admin.show_details')</a>
                                        <form id="solve_from_{{$row->id}}" method="POST" action="{{action('App\Http\Controllers\CommentController@update', $row['id'])}}">
                                            {{ csrf_field() }}
                                            {{ method_field('PUT') }}
                                            <a href="javascript:void(0);" data-id="{{$row->id}}" class="dropdown-item _solve_data" role="button">
                                                @if ($row->approval == 0) <i class="fas fa-check-circle mr-1"></i> @lang('admin.mark_as_approved') @else <i class="fas fa-times-circle mr-1"></i> @lang('admin.mark_as_unapproved') @endif</a>
                                        </form>
                                        <div class="dropdown-divider"></div>
                                        <form id="delete_from_{{$row->id}}" method="POST" action="{{action('App\Http\Controllers\CommentController@destroy', $row['id'])}}">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <a href="javascript:void(0);" data-id="{{$row->id}}" class="dropdown-item _delete_data">
                                                <i class="fas fa-ban mr-1"></i> @lang('admin.delete')
                                            </a>
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

        @if($comments->isEmpty())
        <h6 class="alert alert-warning-custom">@lang('admin.no_records')</h6>
        @endif

    </div>
</div>

{{ $comments->onEachSide(1)->links() }}

@endsection
