{!!$settings['after_head_tag']!!}
@if ($settings['show_top_bar'] == '1')
<div class="container-fluid">
    <div class="row top-bar">
        <div class="color-1 col-2 col-md-1"></div>
        <div class="color-2 col-2 col-md-1"></div>
        <div class="color-3 col-2 col-md-1"></div>
        <div class="color-4 col-2 col-md-1"></div>
        <div class="color-5 col-4 col-md-8"></div>
    </div>
</div>
@endif
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-0 py-3" aria-label="Main navigation">
    <div class="container extend-navbar mx-md-auto mx-0">

        @if ($settings['use_text_logo'] == '0')
        <a href="{{ asset($language_prefix) }}"><img src="{{ s3_switch('logo.png') }}?t={{$settings['update_count']}}" class="logo me-2" width="128" height="30" alt="{{$settings['site_title']}}"></a>
        @else
        <a href="{{ asset($language_prefix) }}"><span class="text-logo">{{$settings['site_title']}}</span></a>
        @endif

        <button class="navbar-toggler p-0 border-0" type="button" id="navbarSideCollapse" aria-label="Toggle navigation">
            {!! svg_icon('fas fa-bars') !!}
        </button>

        <div class="navbar-collapse offcanvas-collapse bg-white" id="navbarsExampleDefault" aria-labelledby="offcanvasLabel">
            <div class="offcanvas-header inline-block d-lg-none px-0 pb-1">
                <h5 class="offcanvas-title" id="offcanvasLabel">@lang('general.menu')</h5>
                <button type="button" class="btn-close text-reset p-0 m-0" data-bs-dismiss="navbarsExampleDefault" id="closeMenu" aria-label="Close"></button>
            </div>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                @if($categories->where('navbar', 1)->where('type', 1)->count() >= 1)
                <li class="nav-item dropdown ms-md-1">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdown_categories" data-bs-toggle="dropdown" aria-expanded="false">@lang('general.categories')</a>
                    <ul class="dropdown-menu shadow" aria-labelledby="dropdown_categories">
                        @foreach ($categories as $category)
                        @if($category->navbar == '1' && $category->type == '1')
                        <li class="px-md-1"><a class="dropdown-item px-md-2" href="{{ asset($language_prefix.$settings['category_base'].'/'.$category->slug) }}">
                        @if(isset($category->image))
                                <img src="{{ s3_switch($category->image, 4) }}" alt="{{ $category->title }}" class="rounded-circle w-25px">
                                @elseif($category->fa_icon != '0')
                                <span class="rounded-circle w-25px section-icon">{!! svg_icon($category->fa_icon) !!}</span>
                                @endif
                        {{$category->title}}</a></li>
                        @endif
                        @endforeach
                    </ul>
                </li>
                @endif

                @if($platforms->where('navbar', 1)->count() >= 1)
                <li class="nav-item dropdown ms-md-1">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdown_platforms" data-bs-toggle="dropdown" aria-expanded="false">@lang('general.platforms')</a>
                    <ul class="dropdown-menu shadow" aria-labelledby="dropdown_platforms">
                        @foreach ($platforms as $platform)
                        @if($platform->navbar == '1')
                        <li class="px-md-1"><a class="dropdown-item px-md-2" href="{{ asset($language_prefix.$settings['platform_base'].'/'.$platform->slug) }}">
                                @if(isset($platform->image))
                                <img src="{{ s3_switch($platform->image, 5) }}" alt="{{ $platform->title }}" class="rounded-circle w-25px">
                                @elseif($platform->fa_icon != '0')
                                <span class="rounded-circle w-25px section-icon">{!! svg_icon($platform->fa_icon) !!}</span>
                                @endif
                        {{$platform->title}}</a></li>
                        @endif
                        @endforeach
                    </ul>
                </li>
                @endif

                @if($categories->where('navbar', 1)->where('type', 2)->count() >= 1)
                <li class="nav-item dropdown ms-md-1">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdown_news" data-bs-toggle="dropdown" aria-expanded="false">@lang('general.news')</a>
                    <ul class="dropdown-menu shadow" aria-labelledby="dropdown_news">
                        @foreach ($categories as $category)
                        @if($category->navbar == '1' && $category->type == '2')
                        <li class="px-md-1"><a class="dropdown-item px-md-2" href="{{ asset($language_prefix.$settings['news_base'].'/'.$category->slug) }}">
                        @if(isset($category->image))
                                <img src="{{ s3_switch($category->image, 4) }}" alt="{{ $category->title }}" class="rounded-circle w-25px">
                                @elseif($category->fa_icon != '0')
                                <span class="rounded-circle w-25px section-icon">{!! svg_icon($category->fa_icon) !!}</span>
                                @endif
                        {{$category->title}}</a></li>
                        @endif
                        @endforeach
                    </ul>
                </li>
                @endif

                <li class="nav-item">
                    <a class="nav-link ms-md-1" href="{{ asset($language_prefix.$settings['topic_base']) }}">@lang('general.topics')</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link ms-md-1" href="{{ asset($language_prefix.'favorites') }}">@lang('general.favorites')</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link ms-md-1" href="{{ asset($language_prefix.'browse-history') }}">@lang('general.browse_history')</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link ms-md-1" href="{{ asset($language_prefix.$settings['contact_slug']) }}">@lang('general.contact')</a>
                </li>

            </ul>

            @if (count($languages) > 1)

            <form class="d-flex">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle me-0 pe-0 pt-0 pb-md-0" href="#" id="dropdown01" data-bs-toggle="dropdown" aria-expanded="false"><span class="fi fi-{{$language_icon_code}}"></span> {{$language_name}}</a>
                        <ul class="dropdown-menu shadow" aria-labelledby="dropdown01">
                            @foreach ($languages as $language)

                            @if($language->language != $language_name)<li class="mb-md-0 mb-1"><a class="dropdown-item" href="{{ asset($menu_language_prefix[$language->id]) }}"><span class="fi fi-{{$language->icon}}"></span> {{$language->language}}</a></li>@endif
                            @endforeach
                        </ul>
                    </li>
                </ul>
            </form>

            @endif

        </div>
    </div>
</nav>

@if (!is_null($ad[1]))<div class="container text-center mt-3">{!! $ad[1] !!}</div>@endif
