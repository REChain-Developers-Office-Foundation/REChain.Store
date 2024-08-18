@extends('adminlte::page')

@section('content_header', __('admin.apple_app_store'))

@section('content')

@include('adminlte::inc.messages')

<div class="row">

    <div class="col-12">

        <!-- search form -->
        <div class="shadow-sm p-2 bg-white rounded mb-3">
            <form action="{{ asset(env('ADMIN_URL').'/apple-scraper') }}" method="GET" class="sidebar-form scraper-form">
                <div class="input-group">
                    <input type="text" name="term" class="form-control border-0 text-dark" placeholder="@lang('admin.scraper_search_ap_store')" value="{{ Request::get('term') }}">
                    <span class="input-group-btn">
                        <button type="submit" id="search-btn" class="btn btn-flat">
                            <i class="fas fa-search"></i>
                        </button>
                    </span>
                </div>
            </form>
        </div>
        <!-- /.search form -->
        
        <div class="callout callout-dark">
            <p class="mb-0"><i class="fas fa-info-circle"></i> @lang('admin.apple_notice_title')
            <div class="collapse mt-3" id="collapseExample">
                @lang('admin.apple_notice_body')
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover text-nowrap m-0" id="table" data-delete-prompt-title="@lang('admin.oops')" data-delete-prompt-body="@lang('admin.delete_prompt')" data-yes="@lang('admin.yes')" data-cancel="@lang('admin.cancel')">
                    <thead>
                        <tr>
                            <th class="col-1">@lang('admin.image')</th>
                            <th class="col-9">@lang('admin.title')</th>
                            <th class="col-1">@lang('admin.status')</th>
                            <th class="col-1"><i class="fas fa-align-justify"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($apps as $row)
                        @if(isset($row['title']))
                        @php $scraper_query = DB::table('versions')->where('url', $row['url'])->first(); @endphp
                        <tr>
                            <td><img src="{{$row['image']}}" class="img-w100"></td>
                            <td><b>{{$row['title']}}</b><br /><p class="text-wrap"><i>{{$row['short']}}</i></p></td>
                            <td>@if (!$scraper_query == null)<span class="text-success">@lang('admin.added')</span>
                                @else <span class="text-dark">@lang('admin.not_added')</span>
                                @endif</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn p-0" data-toggle="dropdown" aria-expanded="false" data-boundary="viewport">
                                        <i class="fas fa-align-justify"></i>
                                    </button>
                                    <div class="dropdown-menu mr-3">
                                        <a class="dropdown-item" href="{{$row['url']}}" target="_Blank"><i class="fab fa-app-store-ios mr-1"></i> @lang('admin.view_app')</a>
                                        <a class="dropdown-item" href="{{action('App\Http\Controllers\AppleScraperController@show', $row['id'])}}"><i class="fas fa-edit mr-1"></i> @lang('admin.submit')</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</div>

@if(!isset($row['title']))
<h6 class="alert alert-warning-custom">@lang('admin.no_results')</h6>
@endif

@stop
