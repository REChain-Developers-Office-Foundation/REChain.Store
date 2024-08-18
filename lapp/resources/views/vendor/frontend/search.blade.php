@extends('frontend::page')

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

            @if (request()->method() == 'GET')
            <div class="col-12">
                <h6 class="alert alert-warning alert-no-record mb-0">@lang('general.search_box_warning')</h6>
            </div>
            @else

            @if($errors->any())
            <div class="col-12">
                <h6 class="alert alert-warning alert-no-record mb-0">@lang('general.min_3_letters')</h6>
            </div>
            @else

            @if($apps->isEmpty())
            <div class="col-12">
                <h6 class="alert alert-warning alert-no-record mb-0">@lang('general.no_search_results')</h6>
            </div>
            @endif

            @if (count($apps) != 0)

            <!-- Apps -->
            <div class="shadow-sm p-2 bg-white rounded mb-3 pb-0">
                <div class="m-1">
                    <h2 class="section-title">@lang('general.search_results_for', ['keyword' => $search_query])</h2>
                    <div class="row app-list">
                        @foreach ($apps as $app)
                        <div class="col-4 col-md-4 mb-3">
                            <a href="{{ asset($language_prefix.$settings['app_base']) }}/{{ $app->slug }}">
                                <div class="row">
                                    <div class="col-md-4"><img src="{{ s3_switch('pixel.png') }}" data-src="{{ s3_switch($app->image ?? 'no_image.png') }}" class="lazy img-fluid" width="200" height="200" alt="{{ $app->title ?: $app->main_title }}"></div>
                                    <div class="col-md-8 ps-md-0 pt-md-0 pt-2"><span class="title pt-0 mt-0">{{ $app->title ?: $app->main_title }}</span><span class="developer mt-1">{{ $app->developer }}</span>{!!stars($app->votes, $settings['rating_as_number'])!!}</div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <!-- /Apps -->
            @endif

            @endif
            @endif

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
