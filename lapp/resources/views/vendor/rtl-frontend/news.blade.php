@extends('rtl-frontend::page')

@section('content')

{!! $breadcrumb_schema_data->toScript() !!}
{!! $news_schema !!}

<!-- Container -->
<div class="container my-3">

    <!-- Grid row -->
    <div class="row">

        <!-- Left column -->
        <div class="col-md-9 ps-md-2">

            <!-- Search Box -->
            @include('rtl-frontend::inc.partials', ['type' => '2'])
            <!-- /Search Box -->

            @if (!is_null($ad[5]))<div class="mb-3">{!! $ad[5] !!}</div>@endif

            <!-- App Details -->
            <div class="shadow-sm p-2 topic-list bg-white rounded mb-3">

                <div class="breadcrumbs mb-2 px-1">
                        <a href="{{ asset('') }}">@lang('general.homepage')</a>

                        »

                        <a href="{{ asset($language_prefix.$settings['news_base']) }}">@lang('general.news')</a>

                        »

                        @if(count($page_query->categories) > 1)
                        @foreach ($page_query->categories as $category)
                        @if ($loop->first)
                        @if(isset($category_name[$category->id]))
                        <button class="btn dropdown-toggle p-0" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{$category_name[$category->id]}}</button>
                        @endif
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            @if(isset($category_name[$category->id]))
                            <a class="dropdown-item" href="{{ asset($language_prefix.$settings['news_base'].'/'.$category->slug) }}">{{$category_name[$category->id]}}</a>
                            @endif
                            @else
                            @if(isset($category_name[$category->id]))
                            <a class="dropdown-item" href="{{ asset($language_prefix.$settings['news_base'].'/'.$category->slug) }}">{{$category_name[$category->id]}}</a>
                            @endif
                            @endif
                            @endforeach
                        </div>
                        @if(isset($category_name[$category->id]))
                        »
                        @endif
                        @else
                        @foreach ($page_query->categories as $category)
                        @if(isset($category_name[$category->id]))
                        <a href="{{ asset($language_prefix.$settings['news_base'].'/'.$category->slug) }}">{{$category_name[$category->id]}}</a>
                        @endif
                        @if(isset($category_name[$category->id]))
                        »
                        @endif
                        @endforeach
                        @endif

                        <a href="{{url()->current()}}">{{$page_query->title}}</a>
                    </div>
                    
                    <div class="m-1">

                    <h1>{{$h1_title}}</h1>

                    @if ($settings['reading_time'] == '1')<span class="news-reading-time mb-2">@lang('general.read', ['time' => $reading_time])</span>@endif

                    <span class="description">{!! $page_query->description !!}</span>

                    <span class="date mt-1 pb-1">{{\Carbon\Carbon::parse($page_query->created_at)->translatedFormat('F d, Y H:i')}}</span>

                    <div class="smi row mx-0 mt-2 mb-3">
                        <div class="col fav text-center p-2 facebook"><a onclick="sm_share('https://www.facebook.com/sharer/sharer.php?u={{url()->current()}}','Facebook','600','300');" href="javascript:void(0);">{!! svg_icon('fab fa-facebook-f') !!} <span class="d-none d-lg-inline-block">Facebook</span></a></div>
                        <div class="col fav text-center p-2 twitter"><a onclick="sm_share('http://twitter.com/share?text={{$page_query->title}}&url={{url()->current()}}','Twitter','600','300');" href="javascript:void(0);">{!! svg_icon('fab fa-twitter') !!} <span class="d-none d-lg-inline-block">Twitter</span></a></div>
                        <div class="col fav text-center p-2 linkedin"><a onclick="sm_share('https://www.linkedin.com/sharing/share-offsite/?url={{url()->current()}}','Linkedin','600','300');" href="javascript:void(0);">{!! svg_icon('fab fa-linkedin-in') !!} <span class="d-none d-lg-inline-block">Linkedin</span></a></div>
                        <div class="col fav text-center p-2 email"><a href="mailto:?subject={{$page_query->title}}&amp;body={{url()->current()}}">{!! svg_icon('fas fa-envelope') !!} <span class="d-none d-lg-inline-block">E-mail</span></a></div>
                        <div class="col fav text-center p-2 whatsapp"><a onclick="sm_share('https://api.whatsapp.com/send?text={{$page_query->title}} {{url()->current()}}','WhatsApp','700','650');" href="javascript:void(0);">{!! svg_icon('fab fa-whatsapp') !!} <span class="d-none d-lg-inline-block">WhatsApp</span></a></div>
                        <div class="col fav text-center p-2 rss"><a href="{{ asset($language_prefix.'news-rss') }}" target="_blank">{!! svg_icon('fas fa-rss') !!} <span class="d-none d-lg-inline-block">RSS</span></a></div>
                    </div>

                    <img src="{{ s3_switch($page_query->image, 1) }}" width="880" height="514" class="img-fluid mb-2" alt="{{$page_query->title}}">

                    {!! $page_query->content !!}

                </div>
            </div>

            <div class="d-flex mb-3">
                <div class="my-auto ms-auto">
                    <h2 class="h2-title m-0">@lang('general.user_comments') ({{ count($news_comments) }})</h2>
                </div>
                <div class="my-auto"><a href="#" class="btn add-comment text-white float-end mt-0">@lang('general.add_comment')</a></div>
            </div>

            <div class="shadow-sm p-2 bg-white rounded pb-1">
                <div class="m-1">

                    <div class="user-reviews">

                        @if ($news_comments->isEmpty())
                        <div class="alert alert-warning show mb-2" role="alert">@lang('general.no_comments_yet')</div>
                        @endif

                        @foreach ($news_comments as $comment)

                        <div class="review mt-2 @if ($loop->first) border-0 @endif pt-0">
                            <p class="title @if ($loop->first) pt-0 mt-0 @endif">"{{{$comment->title}}}"</p>
                            <div class="row">
                                <div class="col-6">
                                    <p class="name">{{{$comment->name}}}</p>
                                </div>
                            </div>
                            <p class="date" data-bs-toggle="tooltip" data-bs-placement="top" title="{{\Carbon\Carbon::parse($comment->created_at)->translatedFormat('F d, Y H:i')}}">
                                {{ \Carbon\Carbon::parse($comment->created_at)->diffForHumans() }}</p>

                            <p class="comment">{{{$comment->comment}}}</p>
                        </div>

                        @endforeach

                        <div class="comment-box" id="comment-section" data-fill-all-fields="@lang('general.fill_all_fields')">

                            <form id="comment-form">

                                <div class="review-title mt-3 mb-2" id="review-title">
                                    @lang('general.add_comment')</div>

                                <input type="hidden" name="content_id" value="{{$page_query->id}}" />
                                <input type="hidden" name="type" value="2" />

                                <div class="mb-3">
                                    <label for="name">@lang('general.your_name'): <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control mt-2" id="name" name="name" required>
                                </div>

                                <div class="mb-3">
                                    <label for="title">@lang('general.comment_title'): <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control mt-2" id="title" name="title" required>
                                </div>

                                <div class="mb-3">
                                    <label for="email">@lang('general.your_email'): <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control mt-2" id="email" name="email" required>
                                    <small id="emailHelp" class="form-text text-muted">@lang('general.email_notification')</small>
                                </div>

                                <div class="mb-3">
                                    <label for="comment">@lang('general.your_comment'): <span class="text-danger">*</span></label>
                                    <textarea class="form-control mt-2" rows="5" id="comment" name="comment" maxlength="1000" required></textarea>
                                </div>

                            </form>

                            <button type="submit" class="btn m-0 comment-button text-white" onclick="form_control()">@lang('general.submit')</button>

                            <div id="comment_result">
                                <div class="alert alert-warning show mt-3 mb-2" role="alert">
                                    @lang('general.comment_rules')</div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

            @if (!is_null($ad[6]))<div class="mt-3">{!! $ad[6] !!}</div>@endif

        </div>
        <!-- /Left column -->

        <!-- Right column -->
        <div class="col-md-3 pe-md-2 mt-md-0 mt-3">
            @include('rtl-frontend::inc.partials', ['type' => '0'])
            @include('rtl-frontend::inc.partials', ['type' => '1'])
            @include('rtl-frontend::inc.partials', ['type' => '3'])
        </div>
        <!-- /Right column -->

    </div>
    <!-- /Grid row -->

</div>
<!-- /Container -->

@endsection
