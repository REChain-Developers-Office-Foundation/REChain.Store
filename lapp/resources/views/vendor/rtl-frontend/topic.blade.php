@extends('rtl-frontend::page')

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

            <div class="shadow-sm p-2 topic-list bg-white rounded mb-3">

                <div class="breadcrumbs mb-2 px-1">
                        <a href="{{ asset('/') }}">@lang('general.homepage')</a> » <a href="{{ asset($language_prefix.$settings['topic_base']) }}">@lang('general.topics')</a> » <a href="{{url()->current()}}">{{$topic_query->title}}</a>
                    </div>

                    <div class="m-1">

                    <h1>{{ $h1_title }}</h1>
                    
                    <span class="description pb-1">{!! $topic_query->description !!}</span>

                    <div class="smi row mx-0 mt-2 mb-3">
                        <div class="col fav text-center p-2 facebook"><a onclick="sm_share('https://www.facebook.com/sharer/sharer.php?u={{url()->current()}}','Facebook','600','300');" href="javascript:void(0);">{!! svg_icon('fab fa-facebook-f') !!} <span class="d-none d-lg-inline-block">Facebook</span></a></div>
                        <div class="col fav text-center p-2 twitter"><a onclick="sm_share('http://twitter.com/share?text={{$topic_query->title}}&url={{url()->current()}}','Twitter','600','300');" href="javascript:void(0);">{!! svg_icon('fab fa-twitter') !!} <span class="d-none d-lg-inline-block">Twitter</span></a></div>
                        <div class="col fav text-center p-2 linkedin"><a onclick="sm_share('https://www.linkedin.com/sharing/share-offsite/?url={{url()->current()}}','Linkedin','600','300');" href="javascript:void(0);">{!! svg_icon('fab fa-linkedin-in') !!} <span class="d-none d-lg-inline-block">Linkedin</span></a></div>
                        <div class="col fav text-center p-2 email"><a href="mailto:?subject={{$topic_query->title}}&amp;body={{url()->current()}}">{!! svg_icon('fas fa-envelope') !!} <span class="d-none d-lg-inline-block">E-mail</span></a></div>
                        <div class="col fav text-center p-2 whatsapp"><a onclick="sm_share('https://api.whatsapp.com/send?text={{$topic_query->title}} {{url()->current()}}','WhatsApp','700','650');" href="javascript:void(0);">{!! svg_icon('fab fa-whatsapp') !!} <span class="d-none d-lg-inline-block">WhatsApp</span></a></div>
                    </div>

                    <img src="{{ s3_switch($topic_query->image, 2) }}" width="880" height="514" class="img-fluid" alt="{{$topic_query->title}}">

                    @foreach ($apps as $app)
                    @php $app_title[$app->id]=$app->title ?? $app->main_title; @endphp
                    @php $app_image[$app->id]=$app->image; @endphp
                    @php $app_slug[$app->id]=$app->slug; @endphp
                    @php $app_developer[$app->id]=$app->developer; @endphp
                    @php $app_votes[$app->id]=$app->votes; @endphp
                    @php $app_license[$app->id]=$app->license; @endphp
                    @endforeach

                    <!-- Topics Items -->
                    <div class="mt-3 topic-item">

                        @php $i=1 @endphp

                        @foreach ($topic_list_query as $app)

                        @if (!empty($app_slug[$app]))

                        <a href="{{ asset($language_prefix.$settings['app_base']) }}/{{ $app_slug[$app] }}">
                            <div class="d-flex">
                                <div class="ms-2">@if (isset($app_license[$app]))<span class="license shadow-sm">{{$app_license[$app]}}</span>@endif<img src="{{ s3_switch($app_image[$app] ?? 'no_image.png') }}" class="image" width="200" height="200" alt="{{ $app_title[$app] }}"></div>
                                <div class="flex-grow-1 my-auto me-1">
                                    <h2 class="title">{{ $i }}. {{ $app_title[$app] }}</h2><span class="developer">{{ $app_developer[$app] }}</span>{!!stars($app_votes[$app], '2')!!}
                                </div>
                            </div>
                        </a>

                        @php $i++ @endphp

                        @if(!$loop->last)
                        <div class="app-space"></div>
                        @endif

                        @endif
                        @endforeach

                    </div>
                    <!-- /Topics Items -->

                </div>

            </div>

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
