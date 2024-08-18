@extends('frontend::page')

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

            <div class="shadow-sm p-2 bg-white rounded pb-1" id="favorites-page" data-cookie-name="{{ $settings['cookie_prefix'].'_favorites' }}" data-no-favorites="@lang('general.no_favorites')">
                <div class="m-1">

                    <h1 class="section-title">@lang('general.favorites')</h1>
                    <div class="row app-list featured"></div>

                </div>
            </div>

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
