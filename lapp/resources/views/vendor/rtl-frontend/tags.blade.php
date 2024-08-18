@extends('rtl-frontend::page')

@section('content')

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

            @if($apps->isEmpty())
            <h6 class="alert alert-warning alert-no-record mb-0">@lang('general.no_records_found')</h6>
            @else

            <!-- Apps -->
            <div class="shadow-sm p-2 bg-white rounded mb-3 pb-0">
                <div class="m-1">
                    <h1 class="section-title">@lang('general.tagged_with', ['keyword' => $tag_title])</h1>
                    <div class="row app-list">
                        @foreach ($apps as $app)
                        <div class="col-4 col-md-4 mb-3">
                            <a href="{{ asset($language_prefix.$settings['app_base']) }}/{{ $app->slug }}">
                                <div class="row">
                                    <div class="col-md-4"><img src="{{ s3_switch('pixel.png') }}" data-src="{{ s3_switch($app->image ?? 'no_image.png') }}" class="lazy img-fluid" width="200" height="200" alt="{{ $app->title ?: $app->main_title }}"></div>
                                    <div class="col-md-8 pe-md-0 pt-md-0 pt-2"><span class="title pt-0 mt-0">{{ $app->title ?: $app->main_title }}</span><span class="developer mt-1">{{ $app->developer }}</span>{!!stars($app->votes, $settings['rating_as_number'])!!}</div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <!-- /Apps -->

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

@endsection
