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

            @if (!is_null($ad[8]))<div class="mb-3">{!! $ad[8] !!}</div>@endif

            <div class="shadow-sm p-2 app-details bg-white rounded">
                <div class="m-1" id="redirect" data-app-id="{{ $app_query->id }}" data-redirection-delay="{{$settings['time_before_redirect']*1000}}">

                    <div class="d-flex flex-row">

                        <div class="ms-2"><img src="{{ s3_switch($app_query->image) }}" alt="{{ $app_query->title ?: $app_query->main_title }}" class="app-image"></div>
                        <div class="my-auto">
                            <h1 class="mb-1 d-inline">{{ $app_query->title ?: $app_query->main_title }}</h1>@if (isset($app_query->version))<span class="version me-1">{{$app_query->version}}</span>@endif
                            <div class="d-block">{!!stars($app_query->votes, 2)!!}</div>
                            <span class="redirecting">@if($app_query->type == '1')
                                @lang('general.downloading_message')<i id="countdown" class="fst-normal d-block">({{$settings['time_before_redirect']}})</i>@else
                                @lang('general.redirecting_message')<i id="countdown" class="fst-normal d-block">({{$settings['time_before_redirect']}})</i>@endif
                            </span>
                        </div>
                    </div>

                </div>
            </div>

            @if (!is_null($ad[7]))<div class="mt-3">{!! $ad[7] !!}</div>@endif

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
