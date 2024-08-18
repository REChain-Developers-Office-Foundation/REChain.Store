@extends('adminlte::page')

@section('content_header', __('admin.google_play_store'))

@section('content')

@include('adminlte::inc.messages')

<div class="row">

    <div class="col-12">

                 <!-- search form -->
                 <div class="shadow-sm p-2 bg-white rounded mb-3">
                 <form action="{{ asset(env('ADMIN_URL').'/google-scraper') }}" method="GET" class="sidebar-form scraper-form">
                    <div class="input-group">
                        <input type="text" name="term" class="form-control border-0 text-dark"
                            placeholder="@lang('admin.scraper_search_gp_store')" value="{{ Request::get('term') }}">
                        <span class="input-group-btn">
                            <button type="submit" id="search-btn" class="btn btn-flat">
                                <i class="fas fa-search"></i>
                            </button>
                        </span>
                    </div>
                </form>
            </div>
            <!-- /.search form -->
                
        <div class="card">                
            <div class="table-responsive">
                <table class="table table-hover text-nowrap m-0" id="table" data-delete-prompt-title="@lang('admin.oops')" data-delete-prompt-body="@lang('admin.delete_prompt')" data-yes="@lang('admin.yes')" data-cancel="@lang('admin.cancel')">
                    <thead>
                        <tr>
                            <th class="col-1">@lang('admin.image')</th>
                            <th class="col-8">@lang('admin.title')</th>
                            <th class="col-1">@lang('admin.rating')</th>
                            <th class="col-1">@lang('admin.status')</th>
                            <th class="col-1"><i class="fas fa-align-justify"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($apps as $row)
                        @php $scraper_query = DB::table('versions')->where('url', $row['url'])->first(); @endphp
                        <tr>
                            <td><img src="{{$row['image']}}" class="img-w100"></td>
                                <td><b>{{$row['title']}}</b><br /><i>{{$row['author']}}</i></td>
                                <td>{{$row['rating']}} &#9733;</td>
                                <td>@if (!$scraper_query == null)<span class="text-success">@lang('admin.added')</span>
                                @else <span class="text-dark">@lang('admin.not_added')</span>
                                @endif</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn p-0" data-toggle="dropdown" aria-expanded="false" data-boundary="viewport">
                                        <i class="fas fa-align-justify"></i>
                                    </button>
                                    <div class="dropdown-menu mr-3">
                                        <a class="dropdown-item" href="{{$row['url']}}" target="_blank"><i class="fab fa-google-play mr-1"></i> @lang('admin.view_app')</a>
                                        <a class="dropdown-item" href="{{action('App\Http\Controllers\GoogleScraperController@show', $row['id'])}}"><i class="fas fa-edit mr-1"></i> @lang('admin.submit')</a>
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

@if(count($apps) == '0')
<h6 class="alert alert-warning-custom">@lang('admin.no_results')</h6>
@endif

@stop