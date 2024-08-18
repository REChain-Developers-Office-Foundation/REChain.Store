@extends('adminlte::page')

@section('content_header', __('admin.sliders'))

@section('content')

@include('adminlte::inc.messages')

<div class="row">
    <div class="col-12">
        <a href="{{ asset(env('ADMIN_URL').'/sliders/create') }}" class="btn button-green mb-3"><i class="fas fa-plus-square"></i>
            @lang('admin.create_slider')</a>
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover text-nowrap m-0" id="table" data-delete-prompt-title="@lang('admin.oops')" data-delete-prompt-body="@lang('admin.delete_prompt')" data-yes="@lang('admin.yes')" data-cancel="@lang('admin.cancel')">
                    <thead>
                        <tr>
                            <th class="col-1">@lang('admin.image')</th>
                            <th class="col-1">@lang('admin.id')</th>
                            <th class="col-6">@lang('admin.title')</th>
                            <th class="col-1">@lang('admin.active')</th>
                            <th class="col-2">@lang('admin.date')</th>
                            <th class="col-1"><i class="fas fa-align-justify"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                        <tr id="{{$row->id}}">
                            <td><img src="{{ s3_switch($row->image, 3) }}" class="img-w100"></td>
                            <td>{{$row->id}}</td>
                            <td>{{$row->title}}</td>
                            <td>@if ($row->active == 1) <a href="{{ asset(env('ADMIN_URL')."/sliders?status=0&id=$row[id]") }}"
                                class="text-green"><i class="fas fa-check"></i></a> @else <a
                                href="{{ asset(env('ADMIN_URL')."/sliders?status=1&id=$row[id]") }}" class="text-dark"><i
                                    class="fas fa-times"></i></a> @endif</td>
                            <td>{{\Carbon\Carbon::parse($row->created_at)->translatedFormat('M d, Y')}}</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn p-0" data-toggle="dropdown" aria-expanded="false" data-boundary="viewport">
                                        <i class="fas fa-align-justify"></i>
                                    </button>
                                    <div class="dropdown-menu mr-3">
                                        <a class="dropdown-item" href="{{ route('sliders.edit', $row->id)}}"><i class="fas fa-edit mr-1"></i> @lang('admin.edit')</a>
                                        <div class="dropdown-divider"></div>
                                        <form id="delete_from_{{$row->id}}" method="POST" action="{{action('App\Http\Controllers\SliderController@destroy', $row['id'])}}">
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