@extends('adminlte::page')

@section('content_header', __('admin.news_categories'))

@section('content')

@include('adminlte::inc.messages')

<div class="row">
    <div class="col-12">
        <a href="{{ asset(env('ADMIN_URL').'/news-categories/create') }}" class="btn button-green mb-3"><i class="fas fa-plus-square"></i>
            @lang('admin.create_category')</a>
 <a href="{{ asset(env('ADMIN_URL').'/news-categories/sort') }}" class="btn button-dark ml-2 mb-3"><i class="fas fa-arrows-alt"></i>
            @lang('admin.sort_items')</a>    
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover text-nowrap m-0" id="table" data-delete-prompt-title="@lang('admin.oops')" data-delete-prompt-body="@lang('admin.delete_prompt')" data-yes="@lang('admin.yes')" data-cancel="@lang('admin.cancel')">
                    <thead>
                        <tr>
                            <th class="col-1">@lang('admin.id')</th>
                            <th class="col-7">@lang('admin.title')</th>
                            <th class="col-1">@lang('admin.news')</th>
                            <th class="col-2">@lang('admin.date')</th>
                            <th class="col-1"><i class="fas fa-align-justify"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                        <tr id="{{$row->id}}">
                            <td>{{$row->id}}</td>
                            <td><a href="{{ asset($settings['news_base']) }}/{{ $row->slug }}" class="text-dark" target="_blank">{{$row->title}}</a></td>
                            <td>{{count($row->news)}}</td>
                            <td>{{\Carbon\Carbon::parse($row->created_at)->translatedFormat('M d, Y')}}</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn p-0" data-toggle="dropdown" aria-expanded="false" data-boundary="viewport">
                                        <i class="fas fa-align-justify"></i>
                                    </button>
                                    <div class="dropdown-menu mr-3">
                                        <a class="dropdown-item" href="{{ asset($settings['news_base']) }}/{{ $row->slug }}" target="_blank"><i class="fas fa-external-link-alt mr-1"></i> @lang('admin.browse')</a>
                                        <a class="dropdown-item" href="{{ route('news-categories.edit', $row->id)}}"><i class="fas fa-edit mr-1"></i> @lang('admin.edit')</a>
                                        <div class="dropdown-divider"></div>
                                        <form id="delete_from_{{$row->id}}" method="POST" action="{{action('App\Http\Controllers\NewsCategoryController@destroy', $row['id'])}}">
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

{{ $rows->onEachSide(1)->links() }}

@stop