@extends('rtl-frontend::page')

@push('assets_header')
<!-- Lity CSS -->
<link href="{{ asset('css/simpleLightbox.min.css') }}" rel="stylesheet">
@endpush

@push('assets_footer')
<!-- Popper -->
<script src="{{ asset('js/popper.min.js') }}"></script>

<!-- Lity JS -->
<script src="{{ asset('js/simpleLightbox.min.js') }}"></script>
@endpush

@section('content')

{!! $breadcrumb_schema_data->toScript() !!}

<!-- Container -->
<div class="container my-3">

    <!-- Grid row -->
    <div class="row">

        <!-- Left column -->
        <div class="col-md-9 ps-md-2">

            <div id="notificationsContainer"></div>

            <!-- Search Box -->
            @include('rtl-frontend::inc.partials', ['type' => '2'])
            <!-- /Search Box -->

            @if (!is_null($ad[5]))<div class="mb-3">{!! $ad[5] !!}</div>@endif

            <!-- App Details -->
            <div class="shadow-sm p-2 app-details bg-white rounded mb-3">

                <div class="show-qr-code float-start">{!! svg_icon('fas fa-qrcode') !!}
                    <div class="qr-code bg-white p-1">
                        <img src="{{ asset(DNS2D::getBarcodePNGPath(url()->current(), 'QRCODE', 4.25, 4.25, '255,255,255', $app_query->slug)) }}">
                    </div>
                </div>

                <div class="breadcrumbs mb-2 px-1">
                    <a href="{{ asset('/') }}">@lang('general.homepage')</a>

                    »

                    @if(count($app_query->categories) > 1)
                    @foreach ($app_query->categories as $category)
                    
                    @if ($loop->first)
                    @if(isset($category_name[$category->id]))
                    <button class="btn dropdown-toggle p-0" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{$category_name[$category->id]}}</button>
                    @endif
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        @if(isset($category_name[$category->id]))
                        <a class="dropdown-item" href="{{ asset($language_prefix.$settings['category_base'].'/'.$category->slug) }}">{{$category_name[$category->id]}}</a>
                        @endif
                        @else
                        @if(isset($category_name[$category->id]))
                        <a class="dropdown-item" href="{{ asset($language_prefix.$settings['category_base'].'/'.$category->slug) }}">{{$category_name[$category->id]}}</a>
                        @endif
                        @endif
                        @endforeach
                    </div>
                    @if(isset($category_name[$category->id]))
                    »
                    @endif
                    
                    @else

                    @foreach ($app_query->categories as $category)
                    @if(isset($category_name[$category->id]))
                    <a href="{{ asset($language_prefix.$settings['category_base'].'/'.$category->slug) }}">{{$category_name[$category->id]}}</a>
                    @endif
                    @if(isset($category_name[$category->id]))
                    »
                    @endif
                    @endforeach
                    @endif
                    
                    @if(count($app_query->platforms) > 1)
                    @foreach ($app_query->platforms as $platform)
                    @if ($loop->first)
                    @if(isset($platform_name[$platform->id]))
                    <button class="btn dropdown-toggle p-0" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{$platform_name[$platform->id]}}</button>
                    @endif
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        @if(isset($platform_name[$platform->id]))
                        <a class="dropdown-item" href="{{ asset($language_prefix.$settings['platform_base'].'/'.$platform->slug) }}">{{$platform_name[$platform->id]}}</a>
                        @endif
                        @else
                        @if(isset($platform_name[$platform->id]))
                        <a class="dropdown-item" href="{{ asset($language_prefix.$settings['platform_base'].'/'.$platform->slug) }}">{{$platform_name[$platform->id]}}</a>
                        @endif
                        @endif
                        @endforeach
                    </div>
                    @else
                    @foreach ($app_query->platforms as $platform)
                    @if(isset($platform_name[$platform->id]))
                    <a href="{{ asset($language_prefix.$settings['platform_base'].'/'.$platform->slug) }}">{{$platform_name[$platform->id]}}</a>
                    @endif
                    @endforeach
                    @endif

                    @if(isset($platform_name[$platform->id]))
                    »
                    @endif

                    <a href="{{url()->current()}}">{{ $app_query->title ?: $app_query->main_title }}</a>
                </div>

                <div class="m-1" id="app" data-thumb="{{ s3_switch($app_query->image ?? 'no_image.png') }}" data-url="{{url()->current()}}" data-title="{{ $app_query->title }}" data-site-title="{{ $settings['site_title'] }}" data-add-message="@lang('general.add_message')" data-remove-message="@lang('general.remove_message')" data-cookie-prefix="{{ $settings['cookie_prefix'] }}">

                    <div class="d-flex">
                        <div class="flex-shrink-0 ms-1">
                            <img src="{{ s3_switch($app_query->image) }}" class="app-image" alt="{{ $app_query->title }}">
                        </div>
                        <div class="flex-grow-1 me-2 @if (!isset($app_query->url))my-auto @endif">
                            <h1>{{$h1_title}}</h1>
                            @if (isset($app_query->version))<span class="version">{{$app_query->version}}</span>@endif <span class="developer my-1">@lang('general.by', ['developer' => $app_query->developer]) </span>
                            <div class="clearfix"></div>
                            {!!stars($app_query->votes, 1)!!}<span class="votes">@if($app_query->votes != '0.00')/5 @endif</span><span class="review-data me-1">{!! svg_icon('fas fa-user') !!} ({{$app_query->total_votes}} @lang('general.reviews'))</a>
                                @if (isset($app_query->app_updated))<span class="date mt-1">{{\Carbon\Carbon::parse($app_query->app_updated)->translatedFormat('F d, Y')}}</span>@endif
                        </div>

                    </div>

                    <div class="smi row mx-0 mt-2 pt-1 text-center">
                        <div class="col fav p-2 facebook"><a onclick="sm_share('https://www.facebook.com/sharer/sharer.php?u={{url()->current()}}','Facebook','600','300');" href="javascript:void(0);">{!! svg_icon('fab fa-facebook-f') !!} <span class="d-none d-lg-inline-block">Facebook</span></a></div>
                        <div class="col fav p-2 twitter"><a onclick="sm_share('http://twitter.com/share?text={{$app_query->title}}&url={{url()->current()}}','Twitter','600','300');" href="javascript:void(0);">{!! svg_icon('fab fa-twitter') !!} <span class="d-none d-lg-inline-block">Twitter</span></a></div>
                        <div class="col fav p-2 linkedin"><a onclick="sm_share('https://www.linkedin.com/sharing/share-offsite/?url={{url()->current()}}','Linkedin','600','300');" href="javascript:void(0);">{!! svg_icon('fab fa-linkedin-in') !!} <span class="d-none d-lg-inline-block">Linkedin</span></a></div>
                        <div class="col fav p-2 email"><a href="mailto:?subject={{$app_query->title}}&amp;body={{url()->current()}}">{!! svg_icon('fas fa-envelope') !!} <span class="d-none d-lg-inline-block">E-mail</span></a></div>
                        <div class="col fav p-2 whatsapp"><a onclick="sm_share('https://api.whatsapp.com/send?text={{$app_query->title}} {{url()->current()}}','WhatsApp','700','650');" href="javascript:void(0);">{!! svg_icon('fab fa-whatsapp') !!} <span class="d-none d-lg-inline-block">WhatsApp</span></a></div>
                        <div class="col p-2 favorites"><button class="add-favorites">{!! svg_icon('heart') !!}</button><span class="d-none d-lg-inline-block">@lang('general.favorites')</span></div>

                    </div>

                </div>
            </div>
            <!-- /App Details-->

            @if ($app_query->screenshots != "")
            <!-- Screenshots-->
            <div class="shadow-sm bg-white rounded mb-3">
                <div class="container screenshots">
                    <div class="row">

                        <div id="screenshot-main">
                            <div id="left" class="shadow-sm">{!! svg_icon('fas fa-angle-left') !!}</div>
                            <div id="right" class="shadow-sm">{!! svg_icon('fas fa-angle-right') !!}</div>

                            @foreach($screenshot_data as $image_name)
                            <a href="{{ s3_switch($image_name, 6) }}"><img src="{{ s3_switch('pixel.png') }}" data-src="{{ s3_switch($image_name, 6) }}" class="lazy mr-1" alt="{{$app_query->title}}"></a>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
            <!-- /Screenshots -->
            @endif

            @if (!is_null($ad[21]))<div class="text-center mb-3">{!! $ad[21] !!}</div>@endif

            <!-- Latest Version -->
            <div class="shadow-sm p-2 app-details bg-white rounded mb-3">
                <div class="m-1">

                    <h2 class="mb-3">@lang('general.latest_version')</h2>

                    <div class="row">
                        @if (isset($app_query->version))<div class="col-md-4 col-6 mb-3"><b>@lang('general.version')</b><br />{{$app_query->version}}</div>@endif

                        @if (isset($app_query->app_updated))<div class="col-md-4 col-6 mb-3"><b>@lang('general.update')</b><br />{{\Carbon\Carbon::parse($app_query->app_updated)->translatedFormat('F d, Y')}}</div>
                        @endif

                        @if (isset($app_query->developer))<div class="col-md-4 col-6 mb-3"><b>@lang('general.developer')</b><br />{{$app_query->developer}}</div>@endif

                        <div class="col-md-4 col-6 mb-3"><b>@lang('general.categories')</b><br />
                            @foreach ($app_query->categories as $category)
                            @if(isset($category_name[$category->id]))
                            <a href="{{ asset($language_prefix.$settings['category_base'].'/'.$category->slug) }}">{{$category_name[$category->id]}}</a>
                            @endif
                            @if(!$loop->last)<span class="sep">›</span>
                            @endif
                            @endforeach
                        </div>

                        <div class="col-md-4 col-6 mb-3"><b>@lang('general.platforms')</b><br />
                            @foreach ($app_query->platforms as $platform)
                            @if(isset($platform_name[$platform->id]))
                            <a href="{{ asset($language_prefix.$settings['platform_base'].'/'.$platform->slug) }}">{{$platform_name[$platform->id]}}</a>
                            @endif
                            @if(!$loop->last)<span class="sep">›</span>
                            @endif
                            @endforeach
                        </div>

                        @if (isset($app_query->file_size))<div class="col-md-4 col-6 mb-3"><b>@lang('general.file_size')</b><br />{{$app_query->file_size}}</div>@endif

                        @if (isset($app_query->counter))<div class="col-md-4 col-6 mb-3"><b>@lang('general.downloads')</b><br />{{number_format($app_query->counter)}}</div>@endif

                        @if (isset($app_query->license))<div class="col-md-4 col-6 mb-3"><b>@lang('general.license')</b><br />{{$app_query->license}}</div>@endif
                        @if (isset($app_query->package_name))<div class="col-md-4 col-6 text-break mb-3"><b>@lang('general.package_name')</b><br>{{$app_query->package_name}}</div>@endif
                        <div class="col-md-4 col-6 mb-3"><b>@lang('general.report')</b><br /><a href="return false;" class="report" data-bs-toggle="modal" data-bs-target="#MyModal">@lang('general.report_a_problem')</a></div>
                    </div>

                    @if (isset($app_query->url))
                    @if($app_query->type=='1')
                    <a href="{{ asset($language_prefix.'redirect/' . $app_query->slug.'/'.$app_query->version_id) }}"><span class="download-btn shadow-sm">{!! svg_icon('fas fa-download') !!} @lang('general.download') @if (isset($app_query->file_size))({{$app_query->file_size}})@endif</span></a>
                    @else
                    <a href="{{ asset($language_prefix.'redirect/' . $app_query->slug.'/'.$app_query->version_id) }}"><span class="download-btn shadow-sm">{!! svg_icon('fas fa-external-link-alt') !!} @lang('general.visit_page')</span></a>
                    @endif
                    @endif

                    @if(!empty($app_query->buy_url))
                    <a href="{{ $app_query->buy_url }}" target="_blank"><span class="buy-btn shadow-sm mt-3">{!! svg_icon('fas fa-tag') !!}
                            @lang('general.buy_now')</span></a>
                    @endif

                </div>
            </div>
            <!-- /Latest Version-->

            @if (!is_null($ad[22]))<div class="text-center mb-3">{!! $ad[22] !!}</div>@endif

            <!-- Tags -->
            @if (isset($app_query->tags))
            @if (count($app_query->tags) > 0)
            <div class="shadow-sm p-2 tags bg-white rounded mb-3">
                <div class="m-1">
                    <h3>@lang('general.tags', ['app' => $app_query->title])</h3>
                    <ul>
                        @foreach ($app_query->tags as $tag)
                        <li><a href="{{ asset($language_prefix.$settings['tag_base']) }}/{{ $tag['slug'] }}">{{ $tag['name'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <!-- /Tags-->
            @endif
            @endif

            @if(count($versions) >= '2')
            <!-- Old Versions-->
            <div class="shadow-sm p-2 app-details bg-white rounded mb-3">
                <div class="m-1">
                    <h2 class="mb-3">@lang('general.old_versions_of') {{$app_query->title}}</h2>
                    @foreach ($versions as $version)

                    @if(!$loop->first)
                    <div class="d-flex mb-2">
                        <div class="flex-grow-1 my-auto">
                            <h4>{{$app_query->title}} {{$version->version}}</h4><span class="last-update">{{\Carbon\Carbon::parse($version->updated_at)->translatedFormat('F d, Y')}}</span> @if (isset($version->file_size))<span class="file-size ms-2">{{$version->file_size}}</span>@endif
                        </div>
                        <div class="my-auto"><a href="{{ asset($language_prefix.'redirect/' . $app_query->slug.'/'.$version->id) }}"><span class="download-btn-sm shadow-sm py-2">{!! svg_icon('fas fa-download') !!} @lang('general.download')</span></a></div>
                    </div>
                    @endif

                    @if(!$loop->last && !$loop->first)
                    <div class="app-space mb-2"></div>
                    @endif

                    @endforeach
                </div>
            </div>
            <!-- /Old Versions-->
            @endif

            <!-- App Description-->
            <div class="shadow-sm p-2 app-details bg-white rounded mb-3">
                <div class="m-1">
                    <h2 class="mb-3">@lang('general.more_about', ['app' => $app_query->title])</h2>
                    <div @if ($settings['enable_show_more']=='1' ) class="app-description" id="app-description" @endif data-show-more="@lang('general.show_more')" data-show-less="@lang('general.show_less')">
                        <span class="description">{{ $app_query->description }}</span>
                        <div class="clearfix mb-3"></div>
                        {!! $app_query->details !!}
                    </div>
                </div>
            </div>
            <!-- /App Description-->

            @if (!is_null($ad[23]))<div class="text-center mb-3">{!! $ad[23] !!}</div>@endif

            <!-- App Vote-->
            <h4 class="mb-3">@lang('general.rate_the_app')</h4>

            <div class="row mx-0 up-down mb-3">
                <div class="col-6 down shadow-sm rounded-end" id="vote-data" data-vote-success="@lang('general.vote_success')" data-vote-error="@lang('general.vote_error')">

                    <button class="rate_app" data-id="{{ $app_query->id }}" data-action="down">
                        <i class="down-button">
                            {!! svg_icon('down_vote') !!}
                        </i>
                        <small class="text-dark" id="down">{{number_format($app_query->down_votes)}}</small>
                    </button>
                </div>

                <div class="col-6 up shadow-sm rounded-start">

                    <button class="rate_app" data-id="{{ $app_query->id }}" data-action="up">
                        <i class="up-button">
                            {!! svg_icon('up_vote') !!}
                        </i>
                        <small class="text-dark" id="up"> {{number_format($app_query->up_votes)}}</small>
                    </button>
                </div>
            </div>
            <!-- /App Vote-->

            <div class="shadow-sm p-2 bg-white rounded pb-1">
                <div class="m-1">

                    <a href="#" class="btn add-comment text-white float-start">@lang('general.add_comment_review')</a>
                    <div class="review-title">
                        <h4>@lang('general.user_reviews')</h4>
                        <div class="stars">
                            @for ($i = 1; $i <= 5; $i++) @if($i<=round($app_query->votes))
                                {!! svg_icon('fas fa-star checked') !!}
                                @else
                                {!! svg_icon('fas fa-star') !!}
                                @endif
                                @endfor
                        </div>
                    </div>

                    <div class="mt-2">@lang('general.based_on', ['count' => $app_query->total_votes])</div>

                    <div class="user-ratings mt-3">

                        @foreach ($comment_order as $rating => $total_rating )

                        @php array_sum($comment_order) ? $bar_length=(100/array_sum($comment_order))*$total_rating : $bar_length='0'; @endphp

                        <div class="row mx-0">
                            <div class="col-2 p-0">{{ trans_choice('general.star', $rating) }}</div>
                            <div class="col-9">
                                <div class="progress" data-bar-width="{{$bar_length}}">
                                    <div class="progress-bar"></div>
                                </div>
                            </div>
                            <div class="col-1 p-0 votes text-center">{{$total_rating}}</div>
                        </div>

                        @endforeach

                    </div>

                    <div class="user-reviews">

                        @if ($app_comments->isEmpty())
                        <div class="alert alert-warning show mt-1 mb-2" role="alert">@lang('general.no_reviews_yet')</div>
                        @endif

                        @foreach ($app_comments as $comment)

                        <div class="review mt-2">
                            <p class="title">"{{{$comment->title}}}"</p>
                            <div class="row">
                                <div class="col-6">
                                    <p class="name">{{{$comment->name}}}</p>
                                </div>
                                <div class="col-6 text-start">
                                    {!!stars($comment->rating, '2')!!}
                                </div>
                            </div>
                            <p class="date" data-bs-toggle="tooltip" data-bs-placement="top" title="{{\Carbon\Carbon::parse($comment->created_at)->translatedFormat('F d, Y H:i')}}">
                                {{ \Carbon\Carbon::parse($comment->created_at)->diffForHumans() }}</p>

                            <p class="comment">{{{$comment->comment}}}</p>
                        </div>

                        @endforeach

                        <div class="comment-box" id="comment-section" data-fill-all-fields="@lang('general.fill_all_fields')">

                            <form id="comment-form">

                                <div class="review-title mt-3 mb-3" id="review-title">
                                    @lang('general.add_comment_review')</div>

                                <input type="hidden" name="content_id" value="{{$app_query->id}}" />
                                <input type="hidden" name="type" value="1" />

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

                                <div class="mb-3">
                                    <label for="rating">@lang('general.your_rating'): <span class="text-danger">*</span></label>
                                    <div class="user_ratings mt-1" id="rating" data-rating-id="{{$app_query->id}}">
                                        <input type="radio" id="user_rating" name="user_rating" value="1">
                                        <input type="radio" id="user_rating" name="user_rating" value="2">
                                        <input type="radio" id="user_rating" name="user_rating" value="3">
                                        <input type="radio" id="user_rating" name="user_rating" value="4">
                                        <input type="radio" id="user_rating" name="user_rating" value="5" checked>
                                    </div>
                                    <div class="clearfix"></div>
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
            
            @if(count($other_apps_category) >= 1)
            <!-- Other Apps in the Category -->
            <div class="shadow-sm p-2 bg-other rounded mt-3 pb-0">
                <div class="m-1 mb-0">

                    <a href="{{ asset($language_prefix.$settings['category_base'].'/'.$category->slug) }}" class="more float-start">@lang('general.more') »</a>
                    <h2 class="section-title">@lang('general.other_apps_category')</h2>

                    <div class="row app-list">
                        @foreach ($other_apps_category as $app)
                        <div class="col-4 col-md-4 mb-md-3 mb-2">
                            <a href="{{ asset($language_prefix.$settings['app_base']) }}/{{ $app->slug }}">
                                <div class="row">
                                    <div class="col-md-4"><img src="{{ s3_switch('pixel.png') }}" data-src="{{ s3_switch($app->image ?? 'no_image.png') }}" class="lazy img-fluid" width="200" height="200" alt="{{ $app->title ?? $app->main_title }}"></div>
                                    <div class="col-md-8 pe-md-0 pt-md-0 pt-2"><span class="title">{{ $app->title ?? $app->main_title }}</span><span class="developer my-md-1 my-0">{{ $app->developer }}</span></div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>

                </div>
            </div>
            <!-- /Other Apps in the Category -->
            @endif

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


<!-- Report Form -->

<div class="modal align-middle" id="MyModal">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">@lang('general.report_a_problem')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="icon-close"></i>
                </button>
            </div>

            <div class="modal-body submission-box pt-1 pb-1" id="report-submission-section" data-station-id="{{ $app_query->id }}" data-error="@lang('general.error')" data-recaptcha-error="@lang('general.recaptcha_error')">

                <form id="report-submission-form">

                    <div class="mt-3">
                        <input type="email" class="form-control" id="email" name="email" placeholder="@lang('general.email')" required>
                    </div>

                    <div class="mt-3">
                        <select title="@lang('general.select_a_reason')" id="reason" name="reason" class="form-select">
                            <option selected="selected" disabled="disabled">@lang('general.select_a_reason')</option>
                            @foreach(report_reasons() as $key => $reason)
                            <option value="{{$key}}">{{$reason}}</option>
                            @endforeach
                        </select>
                    </div>

                    @if ($settings['enable_google_recaptcha'] == '1')
                    <div class="g-recaptcha mt-3" data-sitekey="{{ $settings['google_recaptcha_site_key'] }}"></div>
                    @endif

                    <div id="report-submission-result" class="mb-3"></div>

                    <div class="modal-footer pt-0 ps-0">
                        <button type="button" class="btn submit-button text-white m-0" onclick="report_submission_form()">@lang('general.submit')</button>
                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

<!-- /Report Form -->

@endsection
