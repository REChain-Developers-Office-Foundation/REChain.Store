@extends('adminlte::page')

@section('content_header', __('admin.search_results'))

@section('content')

@include('adminlte::inc.messages')

@if($apps->isNotEmpty() || $categories->isNotEmpty() || $platforms->isNotEmpty() || $topics->isNotEmpty() || $news->isNotEmpty() || $pages->isNotEmpty())

<ul class="nav nav-tabs" id="myTab" role="tablist">

    @php $active='0' @endphp

    <!-- Apps Nav -->
    @if($apps->isNotEmpty())
    <li class="nav-item">
        <a class="nav-link text-dark{{ $active == '0' ? ' active' : '' }}" id="apps-tab" data-toggle="tab" href="#apps" role="tab" aria-controls="apps" aria-selected="false">@if($apps->isNotEmpty())<b>@lang('admin.apps')</b> ({{count($apps)}})@endif</a>
    </li>
    @php $active='1' @endphp
    @endif
    <!-- /Apps Nav -->

    <!-- Categories Nav -->
    @if($categories->isNotEmpty())
    <li class="nav-item">
        <a class="nav-link text-dark{{ $active == '0' ? ' active' : '' }}" id="categories-tab" data-toggle="tab" href="#categories" role="tab" aria-controls="categories" aria-selected="false">@if($categories->isNotEmpty())<b>@lang('admin.categories')</b> ({{count($categories)}})@endif</a>
    </li>
    @php $active='1' @endphp
    @endif
    <!-- /Categories Nav -->
    
    <!-- Platforms Nav -->
    @if($platforms->isNotEmpty())
    <li class="nav-item">
        <a class="nav-link text-dark{{ $active == '0' ? ' active' : '' }}" id="platforms-tab" data-toggle="tab" href="#platforms" role="tab" aria-controls="platforms" aria-selected="false">@if($platforms->isNotEmpty())<b>@lang('admin.platforms')</b> ({{count($platforms)}})@endif</a>
    </li>
    @php $active='1' @endphp
    @endif
    <!-- /Platforms Nav -->
        
    <!-- Topics Nav -->
    @if($topics->isNotEmpty())
    <li class="nav-item">
        <a class="nav-link text-dark{{ $active == '0' ? ' active' : '' }}" id="topics-tab" data-toggle="tab" href="#topics" role="tab" aria-controls="topics" aria-selected="false">@if($topics->isNotEmpty())<b>@lang('admin.topics')</b> ({{count($topics)}})@endif</a>
    </li>
    @php $active='1' @endphp
    @endif
    <!-- /Topics Nav -->
        
    <!-- News Nav -->
    @if($news->isNotEmpty())
    <li class="nav-item">
        <a class="nav-link text-dark{{ $active == '0' ? ' active' : '' }}" id="news-tab" data-toggle="tab" href="#news" role="tab" aria-controls="news" aria-selected="false">@if($news->isNotEmpty())<b>@lang('admin.news')</b> ({{count($news)}})@endif</a>
    </li>
    @php $active='1' @endphp
    @endif
    <!-- /News Nav -->
    
    <!-- Pages Nav -->
    @if($pages->isNotEmpty())
    <li class="nav-item">
        <a class="nav-link text-dark{{ $active == '0' ? ' active' : '' }}" id="pages-tab" data-toggle="tab" href="#pages" role="tab" aria-controls="pages" aria-selected="false">@if($pages->isNotEmpty())<b>@lang('admin.pages')</b> ({{count($pages)}})@endif</a>
    </li>
    @php $active='1' @endphp
    @endif
    <!-- /Pages Nav -->

</ul>

<div class="tab-content" id="myTabContent">

    @php $active='0' @endphp

    <!-- Apps Tab -->
    @if($apps->isNotEmpty())
    <div class="tab-pane{{ $active == '0' ? ' show active' : '' }}" id="apps" role="tabpanel" aria-labelledby="apps-tab">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-hover text-nowrap m-0" id="table" data-delete-prompt-title="Oops..." data-delete-prompt-body="Are you sure you want to delete it?" data-yes="Yes" data-cancel="Cancel">
                            <thead>
                                <tr>
                                    <th class="col-1">@lang('admin.id')</th>
                                    <th class="col-10">@lang('admin.title')</th>
                                    <th class="col-1"><i class="fas fa-align-justify"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($apps as $row)
                                <tr>
                                    <td>{{$row->id}}</td>
                                    <td>{{$row->title}}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn p-0" data-toggle="dropdown" aria-expanded="false" data-boundary="viewport">
                                                <i class="fas fa-align-justify"></i>
                                            </button>
                                            <div class="dropdown-menu mr-3">
                                                <a class="dropdown-item" href="{{ asset($settings['app_base']) }}/{{ $row->slug }}" target="_blank"><i class="fas fa-external-link-alt mr-1"></i> @lang('admin.browse')</a>
                                                <a class="dropdown-item" href="{{ route('apps.edit', $row->id) }}"><i class="fas fa-edit mr-1"></i> @lang('admin.edit')</a>
                                                <a class="dropdown-item" href="{{asset(env('ADMIN_URL') . '/versions/'.$row->id)}}"><i class="fas fa-code-branch mr-1"></i> @lang('admin.versions')</a>
                                                <div class="dropdown-divider"></div>
                                                <form id="delete_from_{{$row->id}}_apps" method="POST" action="{{ action('App\Http\Controllers\ApplicationController@destroy', $row['id']) }}">
                                                    {{ csrf_field() }}
                                                    {{ method_field('DELETE') }}
                                                    <a href="javascript:void(0);" data-id="{{ $row->id }}_apps" class="dropdown-item _delete_data" role="button"><i class="fas fa-ban mr-1"></i> @lang('admin.delete')</a>
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
    </div>
    @php $active='1' @endphp
    @endif
    <!-- /Apps Tab -->

    <!-- Categories Tab -->
    @if($categories->isNotEmpty())
    <div class="tab-pane{{ $active == '0' ? ' show active' : '' }}" id="categories" role="tabpanel" aria-labelledby="categories-tab">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-hover text-nowrap m-0" id="table" data-delete-prompt-title="Oops..." data-delete-prompt-body="Are you sure you want to delete it?" data-yes="Yes" data-cancel="Cancel">
                            <thead>
                                <tr>
                                    <th class="col-1">@lang('admin.id')</th>
                                    <th class="col-10">@lang('admin.title')</th>
                                    <th class="col-1"><i class="fas fa-align-justify"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $row)
                                <tr>
                                    <td>{{$row->id}}</td>
                                    <td>{{$row->title}}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn p-0" data-toggle="dropdown" aria-expanded="false" data-boundary="viewport">
                                                <i class="fas fa-align-justify"></i>
                                            </button>
                                            <div class="dropdown-menu mr-3">
                                                <a class="dropdown-item" href="{{ asset($settings['category_base']) }}/{{ $row->slug }}" target="_blank"><i class="fas fa-external-link-alt mr-1"></i> @lang('admin.browse')</a>
                                                <a class="dropdown-item" href="{{ route('categories.edit', $row->id) }}"><i class="fas fa-edit mr-1"></i> @lang('admin.edit')</a>
                                                <div class="dropdown-divider"></div>
                                                <form id="delete_from_{{$row->id}}_categories" method="POST" action="{{ action('App\Http\Controllers\CategoryController@destroy', $row['id']) }}">
                                                    {{ csrf_field() }}
                                                    {{ method_field('DELETE') }}
                                                    <a href="javascript:void(0);" data-id="{{ $row->id }}_categories" class="dropdown-item _delete_data" role="button"><i class="fas fa-ban mr-1"></i> @lang('admin.delete')</a>
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
    </div>
    @php $active='1' @endphp
    @endif
    <!-- /Categories Tab -->
    
    <!-- Platforms Tab -->
    @if($platforms->isNotEmpty())
    <div class="tab-pane{{ $active == '0' ? ' show active' : '' }}" id="platforms" role="tabpanel" aria-labelledby="platforms-tab">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-hover text-nowrap m-0" id="table" data-delete-prompt-title="Oops..." data-delete-prompt-body="Are you sure you want to delete it?" data-yes="Yes" data-cancel="Cancel">
                            <thead>
                                <tr>
                                    <th class="col-1">@lang('admin.id')</th>
                                    <th class="col-10">@lang('admin.title')</th>
                                    <th class="col-1"><i class="fas fa-align-justify"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($platforms as $row)
                                <tr>
                                    <td>{{$row->id}}</td>
                                    <td>{{$row->title}}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn p-0" data-toggle="dropdown" aria-expanded="false" data-boundary="viewport">
                                                <i class="fas fa-align-justify"></i>
                                            </button>
                                            <div class="dropdown-menu mr-3">
                                                <a class="dropdown-item" href="{{ asset($settings['platform_base']) }}/{{ $row->slug }}" target="_blank"><i class="fas fa-external-link-alt mr-1"></i> @lang('admin.browse')</a>
                                                <a class="dropdown-item" href="{{ route('platforms.edit', $row->id) }}"><i class="fas fa-edit mr-1"></i> @lang('admin.edit')</a>
                                                <div class="dropdown-divider"></div>
                                                <form id="delete_from_{{$row->id}}_platforms" method="POST" action="{{ action('App\Http\Controllers\PlatformController@destroy', $row['id']) }}">
                                                    {{ csrf_field() }}
                                                    {{ method_field('DELETE') }}
                                                    <a href="javascript:void(0);" data-id="{{ $row->id }}_platforms" class="dropdown-item _delete_data" role="button"><i class="fas fa-ban mr-1"></i> @lang('admin.delete')</a>
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
    </div>
    @php $active='1' @endphp
    @endif
    <!-- /Platforms Tab -->

    <!-- Topics Tab -->
    @if($topics->isNotEmpty())
    <div class="tab-pane{{ $active == '0' ? ' show active' : '' }}" id="topics" role="tabpanel" aria-labelledby="topics-tab">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-hover text-nowrap m-0" id="table" data-delete-prompt-title="Oops..." data-delete-prompt-body="Are you sure you want to delete it?" data-yes="Yes" data-cancel="Cancel">
                            <thead>
                                <tr>
                                    <th class="col-1">@lang('admin.id')</th>
                                    <th class="col-10">@lang('admin.title')</th>
                                    <th class="col-1"><i class="fas fa-align-justify"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topics as $row)
                                <tr>
                                    <td>{{$row->id}}</td>
                                    <td>{{$row->title}}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn p-0" data-toggle="dropdown" aria-expanded="false" data-boundary="viewport">
                                                <i class="fas fa-align-justify"></i>
                                            </button>
                                            <div class="dropdown-menu mr-3">
                                                <a class="dropdown-item" href="{{ asset($settings['topic_base']) }}/{{ $row->slug }}" target="_blank"><i class="fas fa-external-link-alt mr-1"></i> @lang('admin.browse')</a>
                                                <a class="dropdown-item" href="{{ asset(env('ADMIN_URL')."/topic/$row->id") }}"><i class="fas fa-star mr-1"></i>@lang('admin.items')</a>
                                                <a class="dropdown-item" href="{{ route('topics.edit', $row->id) }}"><i class="fas fa-edit mr-1"></i> @lang('admin.edit')</a>
                                                <div class="dropdown-divider"></div>
                                                <form id="delete_from_{{$row->id}}_topics" method="POST" action="{{ action('App\Http\Controllers\TopicController@destroy', $row['id']) }}">
                                                    {{ csrf_field() }}
                                                    {{ method_field('DELETE') }}
                                                    <a href="javascript:void(0);" data-id="{{ $row->id }}_topics" class="dropdown-item _delete_data" role="button"><i class="fas fa-ban mr-1"></i> @lang('admin.delete')</a>
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
    </div>
    @php $active='1' @endphp
    @endif
    <!-- /Topics Tab -->
    
    <!-- News Tab -->
    @if($news->isNotEmpty())
    <div class="tab-pane{{ $active == '0' ? ' show active' : '' }}" id="news" role="tabpanel" aria-labelledby="news-tab">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-hover text-nowrap m-0" id="table" data-delete-prompt-title="Oops..." data-delete-prompt-body="Are you sure you want to delete it?" data-yes="Yes" data-cancel="Cancel">
                            <thead>
                                <tr>
                                    <th class="col-1">@lang('admin.id')</th>
                                    <th class="col-10">@lang('admin.title')</th>
                                    <th class="col-1"><i class="fas fa-align-justify"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($news as $row)
                                <tr>
                                    <td>{{$row->id}}</td>
                                    <td>{{$row->title}}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn p-0" data-toggle="dropdown" aria-expanded="false" data-boundary="viewport">
                                                <i class="fas fa-align-justify"></i>
                                            </button>
                                            <div class="dropdown-menu mr-3">
                                                <a class="dropdown-item" href="{{ asset($settings['news_base']) }}/{{ $row->slug }}" target="_blank"><i class="fas fa-external-link-alt mr-1"></i> @lang('admin.browse')</a>
                                                <a class="dropdown-item" href="{{ route('news.edit', $row->id) }}"><i class="fas fa-edit mr-1"></i> @lang('admin.edit')</a>
                                                <div class="dropdown-divider"></div>
                                                <form id="delete_from_{{$row->id}}_news" method="POST" action="{{ action('App\Http\Controllers\NewsController@destroy', $row['id']) }}">
                                                    {{ csrf_field() }}
                                                    {{ method_field('DELETE') }}
                                                    <a href="javascript:void(0);" data-id="{{ $row->id }}_news" class="dropdown-item _delete_data" role="button"><i class="fas fa-ban mr-1"></i> @lang('admin.delete')</a>
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
    </div>
    @php $active='1' @endphp
    @endif
    <!-- /News Tab -->
    
    <!-- Pages Tab -->
    @if($pages->isNotEmpty())
    <div class="tab-pane{{ $active == '0' ? ' show active' : '' }}" id="pages" role="tabpanel" aria-labelledby="pages-tab">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-hover text-nowrap m-0" id="table" data-delete-prompt-title="Oops..." data-delete-prompt-body="Are you sure you want to delete it?" data-yes="Yes" data-cancel="Cancel">
                            <thead>
                                <tr>
                                    <th class="col-1">@lang('admin.id')</th>
                                    <th class="col-10">@lang('admin.title')</th>
                                    <th class="col-1"><i class="fas fa-align-justify"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pages as $row)
                                <tr>
                                    <td>{{$row->id}}</td>
                                    <td>{{$row->title}}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn p-0" data-toggle="dropdown" aria-expanded="false" data-boundary="viewport">
                                                <i class="fas fa-align-justify"></i>
                                            </button>
                                            <div class="dropdown-menu mr-3">
                                                <a class="dropdown-item" href="{{ asset($settings['page_base']) }}/{{ $row->slug }}" target="_blank"><i class="fas fa-external-link-alt mr-1"></i> @lang('admin.browse')</a>
                                                <a class="dropdown-item" href="{{ route('pages.edit', $row->id)}}"><i class="fas fa-edit mr-1"></i> @lang('admin.edit')</a>
                                                <div class="dropdown-divider"></div>
                                                <form id="delete_from_{{$row->id}}_pages" method="POST" action="{{action('App\Http\Controllers\PageController@destroy', $row['id'])}}">
                                                    {{ csrf_field() }}
                                                    {{ method_field('DELETE') }}
                                                    <a href="javascript:void(0);" data-id="{{$row->id}}_pages" class="dropdown-item _delete_data" role="button"><i class="fas fa-ban mr-1"></i> @lang('admin.delete')</a>
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
    </div>
    @php $active='1' @endphp
    @endif
    <!-- /Pages Tab -->

    @endif

    @if($apps->isEmpty() && $categories->isEmpty() && $platforms->isEmpty() && $topics->isEmpty() && $news->isEmpty() && $pages->isEmpty())
    <p class="alert alert-warning-custom">@lang('admin.no_search_result')</p>
    @endif

    @stop
