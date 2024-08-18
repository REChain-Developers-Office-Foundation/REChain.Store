@extends('adminlte::page')

@section('content_header', __('admin.translations'))

@section('content')

@include('adminlte::inc.messages')

<div class="row">
    <div id="notificationsContainer"></div>

    <div class="col-12">
        <a href="{{ asset(env('ADMIN_URL').'/translations/create') }}" class="btn button-green mb-3"><i class="fas fa-plus-square"></i>
            @lang('admin.create_translation')</a>
                    <div class="callout callout-dark">
            <p><i class="fas fa-info-circle"></i> @lang('admin.sortable_items')</p>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover text-nowrap m-0" id="table" data-delete-prompt-title="Oops..." data-delete-prompt-body="Are you sure you want to delete it?" data-yes="Yes" data-cancel="Cancel">
                    <thead>
                        <tr>
                            <th class="col-1">@lang('admin.id')</th>
                            <th class="col-10">@lang('admin.language')</th>
                            <th class="col-1"><i class="fas fa-align-justify"></i></th>
                        </tr>
                    </thead>
                    <tbody class="sortable-posts" id="translations">
                            @foreach($rows as $row)
                        <tr id="{{$row->id}}">
                            <td>{{$row->id}}</td>
                                <td>{{$row->language}}</td>
                                <td>
                                <div class="btn-group">
                                    <button type="button" class="btn p-0" data-toggle="dropdown" aria-expanded="false" data-boundary="viewport">
                                        <i class="fas fa-align-justify"></i>
                                    </button>
                                    <div class="dropdown-menu mr-3">
                                        <a class="dropdown-item" href="{{action('App\Http\Controllers\TranslationController@edit', $row->id)}}"><i class="fas fa-edit mr-1"></i> @lang('admin.edit')</a>
                                        @if($row->id != '1')<div class="dropdown-divider"></div>@endif
                                        <form id="delete_from_{{$row->id}}" method="POST" action="{{action('App\Http\Controllers\TranslationController@destroy', $row['id'])}}">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            @if($row->id != '1')
                                            <a href="javascript:void(0);" data-id="{{$row->id}}" class="dropdown-item _delete_data" role="button"><i class="fas fa-ban mr-1"></i> @lang('admin.delete')</a>

                                                @endif
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

    </div>
</div>

@if($rows->isEmpty())
<h6 class="alert alert-warning-custom">@lang('admin.no_records')</h6>
@endif

@stop





