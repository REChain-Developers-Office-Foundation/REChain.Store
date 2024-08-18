@extends('adminlte::page')

@section('content_header', __('admin.ads'))

@section('content')

@include('adminlte::inc.messages')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover text-nowrap m-0" id="table">
                    <thead>
                        <tr>
                            <th class="col-10">@lang('admin.ad_spot')</th>
                            <th class="col-1">@lang('admin.status')</th>
                            <th class="col-1"><i class="fas fa-align-justify"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                        <tr>
                            <td>@lang('admin.'.$row['title'])</td>
                            @if (empty($row->code))
                            <td>@lang('admin.available')</td>
                            @else
                            <td>@lang('admin.in_use')</td>
                            @endif
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn p-0" data-toggle="dropdown" aria-expanded="false" data-boundary="viewport">
                                        <i class="fas fa-align-justify"></i>
                                    </button>
                                    <div class="dropdown-menu mr-3">
                                        <a class="dropdown-item" href="{{action('App\Http\Controllers\AdController@edit', $row['id'])}}"><i class="fas fa-edit mr-1"></i> @lang('admin.edit')</a>
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

@endsection