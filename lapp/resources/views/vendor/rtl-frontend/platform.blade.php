@extends('rtl-frontend::page')

@if ($settings['infinite_scroll'] == '1')
@push('assets_header')
<script src="{{ asset('js/infinite-scroll.pkgd.min.js') }}"></script>
@endpush
@endif

@section('content')

{!! $breadcrumb_schema_data->toScript() !!}

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
                    <h1 class="section-title">{{$h1_title}}</h1>
                    <div id="infinite-scroll">
                        <div class="row infinity-scroll app-list">
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
            </div>
            <!-- /Apps -->

            @endif

            @if($settings['infinite_scroll'] == '0' )
            @if ($apps->hasPages())
            <div class="d-flex mt-3">
                <div class="mx-auto">
                    {{ $apps->onEachSide(1)->links() }}
                </div>
            </div>
            @endif
            @endif

            @if($settings['infinite_scroll'] == '1' )
            @if($apps->currentPage() != $apps->lastPage() )

            <!-- Loading -->
            <div class="container text-center">
                <div class="page-load-status">
                    <div class="infinite-scroll-request loader-ellips mb-3">
                        @lang('general.loading')
                    </div>
                </div>
            </div>
            <!-- /Loading -->

            <!-- Pagination -->
            <p class="pagination-next">
                <a class="pagination__next" href="{{$apps->nextPageUrl()}}">@lang('general.next_page')</a>
            </p>
            <!-- /Pagination -->

            @endif
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
