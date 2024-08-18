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

            @if($all_topics->isEmpty())
            <h6 class="alert alert-warning alert-no-record mb-0">@lang('general.no_records_found')</h6>
            @else

            <h1 class="h1-title mb-3">@lang('general.topics')</h1>

            <!-- Topics -->

            <div class="row topics" id="infinite-scroll">

                @foreach ($all_topics as $key => $topics)

                <div class="infinity-scroll col-md-4 col-12 mb-3 @if ($key++ % 2 != 1) pr-md-2 @else pl-md-2 @endif">

                    <a href="{{ asset($language_prefix.$settings['topic_base']) }}/{{ $topics->slug }}">
                        <img src="{{ s3_switch('pixel.png') }}" data-src="{{ s3_switch($topics->image, 2) }}" class="lazy img-fluid rounded-top" alt="">
                        <div class="topic-box rounded">
                            {{ $topics->title}}
                        </div>
                    </a>

                </div>

                @endforeach

                <!-- /Topics -->

            </div>

            @endif

            @if($settings['infinite_scroll'] == '0' )
            @if ($all_topics->hasPages())
            <div class="d-flex mt-3">
                <div class="mx-auto">
                    {{ $all_topics->onEachSide(1)->links() }}
                </div>
            </div>
            @endif
            @endif

            @if($settings['infinite_scroll'] == '1' )
            @if($all_topics->currentPage() != $all_topics->lastPage() )

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
                <a class="pagination__next" href="{{$all_topics->nextPageUrl()}}">@lang('general.next_page')</a>
            </p>
            <!-- /Pagination -->

            @endif
            @endif
            @if (!is_null($ad[6]))<div>{!! $ad[6] !!}</div>@endif

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
