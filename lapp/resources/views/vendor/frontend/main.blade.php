@extends('frontend::page')

@push('assets_header')
<!-- Swiper CSS -->
<link href="{{ asset('css/swiper.min.css') }}" rel="stylesheet">
@endpush

@push('assets_footer')
<!-- Swiper JS -->
<script src="{{ asset('js/swiper.min.js') }}"></script>
@endpush

@section('content')

<!-- Container -->
<div class="container my-3">

    <!-- Grid row -->
    <div class="row">

        <!-- Left column -->
        <div class="col-md-9 pe-md-2">

            <!-- Search Box -->
            @include('frontend::inc.partials', ['type' => '2'])
            <!-- /Search Box -->

            @if (!is_null($ad[5]))<div class="mb-3">{!! $ad[5] !!}</div>@endif

            <!-- Slider -->
            @if (count($sliders) > 0)
            <div class="swiper-container swiper-main mb-3" id="swiper-main">
                <div class="swiper-wrapper">
                    @foreach ($sliders as $slider)

                    <div class="swiper-slide">
                        <a href="{{ asset($language_prefix.$settings['app_base']) }}/{{ $slider->slug }}">
                            <div class="coverbg"></div>
                            <h3><img src="{{ s3_switch($slider->logo ?? 'no_image.png') }}" class="logo img-fluid float-start" width="50" height="50" alt="{{ $slider->title ?: $slider->main_title }}">{{ $slider->title ?: $slider->main_title }}<br>{!!stars($slider->votes, 2)!!}</h3>
                            <img src="{{ s3_switch($slider->image, 3) }}" class="img-fluid" width="850" height="410" alt="{{ $slider->title ?: $slider->main_title }}">
                        </a>
                    </div>
                    @endforeach

                </div>
                <div class="swiper-button-next swiper-button-white d-md-block d-none"></div>
                <div class="swiper-button-prev swiper-button-white d-md-block d-none"></div>
                <div class="swiper-pagination-main"></div>
            </div>
            @endif
            <!-- /Slider -->

            @if (!is_null($ad[9]))<div class="text-center mb-3">{!! $ad[9] !!}</div>@endif

            @if (!empty($h1_title))<h1 class="h1-title mb-3">{{ $h1_title }}</h1>@endif

            <!-- New Apps -->
            @if(count($new_apps) >= 1)
            <div class="shadow-sm p-2 bg-white rounded mb-3 pb-0">
                <div class="m-1">

                    <a href="{{ asset($language_prefix.'new-apps') }}" class="more float-end">@lang('general.more') »</a>
                    <h2 class="section-title">@lang('general.new_apps')</h2>

                    <div class="row app-list">
                        @foreach ($new_apps as $app)
                        <div class="col-4 mb-3">
                            <a href="{{ asset($language_prefix.$settings['app_base']) }}/{{ $app->slug }}">
                                <div class="row">
                                    <div class="col-md-4"><img src="{{ s3_switch('pixel.png') }}" data-src="{{ s3_switch($app->image ?? 'no_image.png') }}" class="lazy img-fluid" width="200" height="200" alt="{{ $app->title ?: $app->main_title }}"></div>
                                    <div class="col-md-8 ps-md-0 pt-md-0 pt-2"><span class="title">{{ $app->title ?: $app->main_title }}</span><span class="developer my-md-1 my-0">{{ $app->developer }}</span><span class="date">{{\Carbon\Carbon::parse($app->created_at)->translatedFormat('M d, Y')}}</span></div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>

                </div>
            </div>
            @endif
            <!-- /New Apps -->

            @if (!is_null($ad[10]))<div class="text-center mb-3">{!! $ad[10] !!}</div>@endif

            @if(count($featured_apps) >= 1)

            <!-- Featured Apps -->
            <div class="shadow-sm p-2 bg-white rounded mb-3">
                <div class="m-1">

                    <a href="{{ asset($language_prefix.'featured-apps') }}" class="more float-end">@lang('general.more') »</a>
                    <h2 class="section-title">@lang('general.featured_apps')</h2>

                    <div class="row app-list featured">
                        @foreach ($featured_apps as $app)
                        <div class="col-4 col-md-1-5 @if($loop->last && count($featured_apps) >= 9)d-md-none d-md-block @endif mb-md-0 mb-2">
                            <a href="{{ asset($language_prefix.$settings['app_base']) }}/{{ $app->slug }}"><img src="{{ s3_switch('pixel.png') }}" data-src="{{ s3_switch($app->image ?? 'no_image.png') }}" class="lazy img-fluid rounded" width="200" height="200" alt="{{ $app->title ?: $app->main_title }}"><span class="title mt-1">{{ $app->title ?: $app->main_title }}</span></a>
                        </div>

                        @endforeach
                    </div>

                </div>
            </div>
            <!-- /Featured Apps -->
            @endif

            @if (!is_null($ad[12]))<div class="text-center mb-3">{!! $ad[12] !!}</div>@endif
            
            <!-- Recently Updated Apps -->
            @if(count($recently_updated_apps) >= 1)
            <div class="shadow-sm p-2 bg-white rounded mb-3 pb-0">
                <div class="m-1">

                    <a href="{{ asset($language_prefix.'recently-updated-apps') }}" class="more float-end">@lang('general.more') »</a>
                    <h2 class="section-title">@lang('general.recently_updated_apps')</h2>

                    <div class="row app-list">
                        @foreach ($recently_updated_apps as $app)
                        <div class="col-4 col-md-4 mb-md-3 mb-2">
                            <a href="{{ asset($language_prefix.$settings['app_base']) }}/{{ $app->slug }}">
                                <div class="row">
                                    <div class="col-md-4"><img src="{{ s3_switch('pixel.png') }}" data-src="{{ s3_switch($app->image ?? 'no_image.png') }}" class="lazy img-fluid" width="200" height="200" alt="{{ $app->title ?: $app->main_title }}"></div>
                                    <div class="col-md-8 ps-md-0 pt-md-0 pt-2"><span class="title">{{ $app->title ?: $app->main_title }}</span><span class="developer my-md-1 my-0">{{ $app->developer }}</span><span class="date">{{\Carbon\Carbon::parse($app->updated_at)->translatedFormat('M d, Y')}}</span></div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>

                </div>
            </div>
            @endif
            <!-- /Recently Updated Apps -->

            @if (!is_null($ad[24]))<div class="text-center mb-3">{!! $ad[24] !!}</div>@endif

            <!-- Topics -->
            @if(count($latest_topics) >= 1)
            <a href="{{ asset($language_prefix.$settings['topic_base']) }}" class="float-end">@lang('general.all_topics') »</a>
            <h2 class="h2-title">@lang('general.topics')</h2>

            <div class="row topics pt-2">

                @foreach ($latest_topics as $topics)

                <div class="col-md-4 col-12 mb-3">

                    <a href="{{ asset($language_prefix.$settings['topic_base']) }}/{{ $topics->slug }}">
                        <img src="{{ s3_switch('pixel.png') }}" data-src="{{ s3_switch($topics->image, 2) }}" class="lazy img-fluid rounded-top" width="880" height="514" alt="{{ $topics->title}}">
                        <div class="topic-box rounded">
                            {{ $topics->title}}
                        </div>
                    </a>

                </div>

                @endforeach

            </div>
            @endif
            <!-- /Topics -->

            @if (!is_null($ad[15]))<div class="text-center mb-3">{!! $ad[15] !!}</div>@endif

            <!-- Must-Have Apps -->
            @if(count($must_have_apps) >= 1)
            <div class="shadow-sm p-2 bg-white rounded mb-3 pb-0">
                <div class="m-1">
                    <a href="{{ asset($language_prefix.'must-have-apps') }}" class="more float-end">@lang('general.more') »</a>
                    <h2 class="section-title">@lang('general.must_have_apps')</h2>
                    <div class="row app-list">
                        @foreach ($must_have_apps as $app)
                        <div class="col-4 col-md-4 mb-md-3 mb-2">
                            <a href="{{ asset($language_prefix.$settings['app_base']) }}/{{ $app->slug }}">
                                <div class="row">
                                    <div class="col-md-4"><img src="{{ s3_switch('pixel.png') }}" data-src="{{ s3_switch($app->image ?? 'no_image.png') }}" class="lazy img-fluid" width="200" height="200" alt="{{ $app->title ?: $app->main_title }}"></div>
                                    <div class="col-md-8 ps-md-0 pt-md-0 pt-2"><span class="title">{{ $app->title ?: $app->main_title }}</span><span class="developer mt-md-1 mt-0">{{ $app->developer }}</span>{!!stars($app->votes, $settings['rating_as_number'])!!}</div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            <!-- /Must-Have Apps -->

            @if (!is_null($ad[13]))<div class="text-center mb-3">{!! $ad[13] !!}</div>@endif

            <!-- Latest News -->
            @if(count($latest_news) >= 1)
            <a href="{{ asset($language_prefix.$settings['news_base']) }}" class="float-end">@lang('general.all_news') »</a>
            <h2 class="h2-title">@lang('general.news')</h2>

            <div class="row topics pt-2">

                @foreach ($latest_news as $news)

                <div class="col-md-6 col-12 mb-3">

                    <a href="{{ asset($language_prefix.$settings['read_base']) }}/{{ $news->slug }}">
                        <img src="{{ s3_switch('pixel.png') }}" data-src="{{ s3_switch($news->image, 1) }}" class="lazy img-fluid rounded-top" width="880" height="514" alt="{{ $news->title}}">
                        <div class="topic-box rounded">
                            {{ $news->title }}
                        </div>
                    </a>

                </div>

                @endforeach

            </div>
            @endif
            <!-- /Last Latest -->

            @if (!is_null($ad[11]))<div class="text-center mb-3">{!! $ad[11] !!}</div>@endif

            <!-- Popular Apps In Last 24 Hours -->
            @if(count($apps_24_hours) >= 1)
            <div class="shadow-sm p-2 pb-0 bg-white rounded">
                <div class="m-1">
                    <a href="{{ asset($language_prefix.'popular-apps-24-hours') }}" class="more float-end">@lang('general.more') »</a>
                    <h2 class="section-title">@lang('general.popular_apps_24_hours')</h2>
                    <div class="row app-list">
                        @foreach ($apps_24_hours as $app)
                        <div class="col-4 col-md-4 mb-md-3 mb-2">
                            <a href="{{ asset($language_prefix.$settings['app_base']) }}/{{ $app->slug }}">
                                <div class="row">
                                    <div class="col-md-4"><img src="{{ s3_switch('pixel.png') }}" data-src="{{ s3_switch($app->image ?? 'no_image.png') }}" class="lazy img-fluid" width="200" height="200" alt="{{ $app->title ?: $app->main_title }}"></div>
                                    <div class="col-md-8 ps-md-0 pt-md-0 pt-2"><span class="title">{{ $app->title ?: $app->main_title }}</span><span class="developer mt-md-1 mt-0">{{ $app->developer }}</span>{!!stars($app->votes, $settings['rating_as_number'])!!}</div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            <!-- /Popular Apps In Last 24 Hours -->

            @foreach ($categories as $category)
            @if($category->home_page == '1' && $category->type == '1')

            @if(count($home_categories[$category->id]) >= 1)
            <!-- {{ $category->title }} -->
            <div class="shadow-sm p-2 bg-category rounded mt-3 pb-0">
                <div class="m-1 mb-0">

                    <a href="{{ asset($language_prefix.$settings['category_base'].'/'.$category->slug) }}" class="more float-end">@lang('general.more') »</a>
                    <h2 class="section-title">{{ $category->title }}</h2>

                    <div class="row app-list">
                        @foreach ($home_categories[$category->id] as $app)
                        <div class="col-4 col-md-4 mb-md-3 mb-2">
                            <a href="{{ asset($language_prefix.$settings['app_base']) }}/{{ $app->slug }}">
                                <div class="row">
                                    <div class="col-md-4"><img src="{{ s3_switch('pixel.png') }}" data-src="{{ s3_switch($app->image ?? 'no_image.png') }}" class="lazy img-fluid" width="200" height="200" alt="{{ $app->title ?: $app->main_title }}"></div>
                                    <div class="col-md-8 ps-md-0 pt-md-0 pt-2"><span class="title">{{ $app->title ?: $app->main_title }}</span><span class="developer my-md-1 my-0">{{ $app->developer }}</span><span class="date">{{\Carbon\Carbon::parse($app->created_at)->translatedFormat('M d, Y')}}</span></div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>

                </div>
            </div>
            <!-- /{{ $category->title }} -->
            @endif
            
            @endif
            @endforeach
            
            @if (!is_null($ad[6]))<div class="mt-3">{!! $ad[6] !!}</div>@endif

        </div>
        <!-- /Left column -->

        <!-- Right column -->
        <div class="col-md-3 ps-md-2 mt-md-0 mt-3">
            @include('frontend::inc.partials', ['type' => '0'])
            @include('frontend::inc.partials', ['type' => '1'])
            @include('frontend::inc.partials', ['type' => '3'])
        </div>
        <!-- /Right column -->

    </div>
    <!-- /Grid row -->

</div>
<!-- /Container -->

@endsection
