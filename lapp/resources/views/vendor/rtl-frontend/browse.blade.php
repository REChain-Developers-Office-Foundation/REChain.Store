@extends('rtl-frontend::page')

@section('content')

<!-- Container -->
<div class="container my-3">

    <!-- Grid row -->
    <div class="row">

        <!-- Left column -->
        <div class="col-md-9 ps-md-2 mb-3">

            <!-- Search Box -->
            @include('rtl-frontend::inc.partials', ['type' => '2'])
            <!-- /Search Box -->

            @if (!is_null($ad[5]))<div class="mb-3">{!! $ad[5] !!}</div>@endif


            @if($rows->isEmpty())
            <h6 class="alert alert-warning alert-no-record mb-0">@lang('general.no_records_found')</h6>
            @else

            <h1 class="h1-title mb-3">@if($type == '1')@lang('general.all_categories')@else @lang('general.all_platforms')@endif</h1>

            <div class="shadow-sm p-2 bg-white rounded pb-0">

                <div class="m-2 mb-0">

                    <div class="row">
                        @foreach ($rows as $row)
                        @if($type == '1' && $row->type == '1')
                        <div class="col-md-4 col-6 mb-3 text-truncate">
                            <a href="{{ asset($language_prefix.$settings['category_base'].'/'.$row->slug) }}" class="text-dark">
                                @if(isset($row->image))
                                <img src="{{ s3_switch('pixel.png') }}" data-src="{{ s3_switch($row->image, 4) }}" alt="{{ $row->title }}" class="rounded-circle w-35px lazy">
                                @elseif($row->fa_icon != '0')
                                <span class="rounded-circle w-35px section-icon">{!! svg_icon($row->fa_icon) !!}</span>
                                @endif
                                {{ $row->title }}
                            </a>
                        </div>
                        @endif
            
                        @if($type == '2')
                        <div class="col-md-4 col-6 mb-3 text-truncate">
                            <a href="{{ asset($language_prefix.$settings['platform_base'].'/'.$row->slug) }}" class="text-dark">
                                @if(isset($row->image))
                                <img src="{{ s3_switch('pixel.png') }}" data-src="{{ s3_switch($row->image, 5) }}" alt="{{ $row->title }}" class="rounded-circle w-35px lazy">
                                @elseif($row->fa_icon != '0')
                                <span class="rounded-circle w-35px section-icon">{!! svg_icon($row->fa_icon) !!}</span>
                                @endif
                                {{ $row->title }}
                            </a>
                        </div>
                        @endif

                        @endforeach
                    </div>

                </div>
            </div>

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
