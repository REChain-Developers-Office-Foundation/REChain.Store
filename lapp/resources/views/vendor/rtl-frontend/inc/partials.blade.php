@if($type == '0')
@if(count($popular_apps) >= 1)
<div class="shadow-sm apps top-apps p-2 bg-white rounded mb-3">
    <div class="m-1">
        <a href="{{ asset($language_prefix.'popular-apps') }}" class="more float-start">@lang('general.more') »</a>
        <h2 class="section-title">@lang('general.popular_apps')</h2>

        @foreach ($popular_apps as $app)
        <a href="{{ asset($language_prefix.$settings['app_base']) }}/{{ $app[1] }}">
            <div class="row mb-2">
                <div class="col-3 ps-0"><img src="{{ s3_switch('pixel.png') }}" data-src="{{ s3_switch($app[2] ?? 'no_image.png') }}" width="200" height="200" class="img-fluid lazy" alt="{{ $app[0] ?: $app[5] }}"></div>
                <div class="col-9 my-auto"><span class="app-title">{{ $app[0] ?: $app[5] }}</span>{!!stars($app[4], $settings['rating_as_number'])!!}<span class="developer">{{ $app[3] }}</span></div>
            </div>
        </a>
        @if(!$loop->last)
        <div class="app-space"></div>
        @endif

        @endforeach

    </div>
</div>
@endif
@if (!is_null($ad[16]))<div class="text-center mb-3">{!! $ad[16] !!}</div>@endif
@endif

@if(count($editors_choice) >= 1 && $type == '1')
<div class="shadow-sm apps top-apps p-2 bg-white rounded mb-3">
    <div class="m-1">

        <a href="{{ asset($language_prefix.'editors-choice') }}" class="more float-start">@lang('general.more') »</a>
        <h2 class="section-title">@lang('general.editors_choice')</h2>

        @foreach ($editors_choice as $app)
        <a href="{{ asset($language_prefix.$settings['app_base']) }}/{{ $app->slug }}">
            <div class="row mb-2">
                <div class="col-3 ps-0"><img src="{{ s3_switch('pixel.png') }}" data-src="{{ s3_switch($app->image ?? 'no_image.png') }}" width="200" height="200" class="img-fluid lazy" alt="{{ $app->title ?: $app->main_title }}"></div>
                <div class="col-9 my-auto"><span class="app-title">{{ $app->title ?: $app->main_title }}</span>{!!stars($app->votes, $settings['rating_as_number'], '2')!!}<span class="developer">{{ $app->developer }}</span></div>
            </div>
        </a>
        @if(!$loop->last)
        <div class="app-space"></div>
        @endif

        @endforeach

    </div>
</div>
@if (!is_null($ad[14]))<div class="text-center mb-3">{!! $ad[14] !!}</div>@endif
@endif

@if($type == '3')
@if($categories->where('right_column', 1)->count() >= 1)
<div class="shadow-sm apps top-apps p-2 pb-0 bg-white rounded mb-3">
    <div class="m-1 pb-1">

        <a href="{{ asset($language_prefix.'all-categories') }}" class="float-start">@lang('general.all_categories') »</a>
        <h3 class="section-title">@lang('general.categories')</h3>

        <div class="row right-column">
            @foreach ($categories as $category)
            @if($category->right_column == '1' && $category->type='1')
            <div class="col-6 mb-2 pb-1"><a href="{{ asset($language_prefix.$settings['category_base']) }}/{{ $category->slug }}">
                                @if(isset($category->image))
                                <img src="{{ s3_switch('pixel.png') }}" data-src="{{ s3_switch($category->image, 4) }}" alt="{{ $category->title }}" class="rounded-circle w-25px lazy">
                                @elseif($category->fa_icon != '0')
                                <span class="rounded-circle w-25px section-icon">{!! svg_icon($category->fa_icon) !!}</span>
                                @endif
            {{ $category->title }}</a></div>
            @endif
            @endforeach
        </div>

    </div>
</div>
@if (!is_null($ad[18]))<div class="text-center mb-3">{!! $ad[18] !!}</div>@endif
@endif

@if($platforms->where('right_column', 1)->count() >= 1)
<div class="shadow-sm apps top-apps p-2 pb-0 bg-white rounded">
    <div class="m-1 pb-1">

        <a href="{{ asset($language_prefix.'all-platforms') }}" class="float-start">@lang('general.all_platforms') »</a>
        <h3 class="section-title">@lang('general.platforms')</h3>

        <div class="row right-column">
            @foreach ($platforms as $platform)
            @if($platform->right_column == '1')
            <div class="col-6 mb-2 pb-1"><a href="{{ asset($language_prefix.$settings['platform_base']) }}/{{ $platform->slug }}">
                                @if(isset($platform->image))
                                <img src="{{ s3_switch('pixel.png') }}" data-src="{{ s3_switch($platform->image, 5) }}" alt="{{ $platform->title }}" class="rounded-circle w-25px lazy">
                                @elseif($platform->fa_icon != '0')
                                <span class="rounded-circle w-25px section-icon">{!! svg_icon($platform->fa_icon) !!}</span>
                                @endif
                 {{ $platform->title }}</a></div>
                 @endif
            @endforeach
        </div>

    </div>
</div>
@endif
@if (!is_null($ad[19]))<div class="text-center mt-3">{!! $ad[19] !!}</div>@endif
@endif

@if($type == '2')

@if (!is_null($ad[20]))<div class="text-center mb-3">{!! $ad[20] !!}</div>@endif

<div class="@if (empty($settings['recommended_terms'])) mb-3 @else mb-md-3 mb-2 @endif">
    <div class="col-12">
        <form method="post" action="{{ asset($language_prefix.'search') }}" class="d-flex shadow-sm p-2 bg-white rounded">
            {{ csrf_field() }}
            <input class="form-control search-form ms-2 pe-1" id="search-form" name="term" type="search" placeholder="@lang('general.search_apps')" aria-label="@lang('general.search_apps')">
            <button class="btn search-btn" type="submit" aria-label="@lang('general.search')"> {!! svg_icon('fas fa-search') !!}</button>
        </form>
    </div>
</div>

@if (!empty($settings['recommended_terms']))
@php $terms = explode(",", $settings['recommended_terms']); @endphp
@if (count($terms) > 0)

<div class="recommended-terms mb-md-2 mb-1" id="terms">
    <span class="d-md-inline-block d-block mt-0 mb-md-0 mb-2">@lang('general.recommended_terms')</span>
    @foreach ($terms as $term)
    <a href="#" class="mb-2">{{ $term }}</a>
    @endforeach
</div>

@endif
@endif

@endif
