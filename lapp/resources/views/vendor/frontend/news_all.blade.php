@extends('frontend::page')

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
        <div class="col-md-9 pe-md-2">

            <!-- Search Box -->
            @include('frontend::inc.partials', ['type' => '2'])
            <!-- /Search Box -->

            @if (!is_null($ad[5]))<div class="mb-3">{!! $ad[5] !!}</div>@endif

            @if($all_news->isEmpty())
            <h6 class="alert alert-warning alert-no-record mb-0">@lang('general.no_records_found')</h6>
            @else

            <div class="d-flex mb-3">
                <div class="my-auto">
                    <h1 class="h1-title mb-0">@if($category_news == 1 ) {{$category_query->title}} @else @lang('general.news') @endif</h1>
                </div>
                <div class="my-auto ms-auto"><a href="{{ asset($language_prefix.'news-rss') }}" class="rss-icon" target="_blank">{!! svg_icon('fas fa-rss') !!}</a></div>
            </div>

            <!-- News -->

            <div class="row topics" id="infinite-scroll">

                @foreach ($all_news as $key => $news)

                <div class="infinity-scroll col-md-4 col-12 mb-3 @if ($key++ % 2 != 1) pr-md-2 @else pl-md-2 @endif">
                    <a href="{{ asset($language_prefix.$settings['read_base']) }}/{{ $news->slug }}">
                        <img src="{{ s3_switch('pixel.png') }}" data-src="{{ s3_switch($news->image, 1) }}" class="lazy img-fluid rounded-top" alt="">
                        <div class="topic-box rounded">
                            {{ $news->title }}
                        </div>
                    </a>
                </div>

                @endforeach

                <!-- /News -->

            </div>

            @endif

            @if($settings['infinite_scroll'] == '0' )
            @if ($all_news->hasPages())
            <div class="d-flex mt-3">
                <div class="mx-auto">
                    {{ $all_news->onEachSide(1)->links() }}
                </div>
            </div>
            @endif
            @endif

            @if($settings['infinite_scroll'] == '1' )
            @if($all_news->currentPage() != $all_news->lastPage() )

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
                <a class="pagination__next" href="{{$all_news->nextPageUrl()}}">Next Page</a>
            </p>
            <!-- /Pagination -->

            @endif
            @endif

            @if (!is_null($ad[6]))<div>{!! $ad[6] !!}</div>@endif

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