@if (!is_null($ad[2]))<div class="container text-center mb-3">{!! $ad[2] !!}</div>@endif

<div class="footer-container">
    <footer class="page-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-3 mx-auto">
                    
                    @if ($settings['use_text_logo'] == '0')
                   <img src="{{ s3_switch('logo.png') }}?t={{$settings['update_count']}}" class="footer-logo mb-1" width="128" height="30" alt="{{$settings['site_title']}}">
                    @else
                    <a href="{{ asset($language_prefix) }}"><span class="text-logo">{{$settings['site_title']}}</span></a>
                    @endif
                    
                    <span class="d-block mt-2">{{$settings['site_description']}}</span>
                    <div class="clearfix mb-1"></div>
                    @if ($settings['show_submission_form'] == '1')
                    <a href="{{ asset($language_prefix.'submit-app') }}" class="submit-app">@lang('general.submit_your_app')</a>
                    @endif

                    @if (!empty($settings['twitter_account']))<a href="https://twitter.com/{{$settings['twitter_account']}}" aria-label="Twitter" target="_blank">{!! svg_icon('twitter-footer') !!}</a>@endif
                    @if (!empty($settings['facebook_page']))<a href="{{$settings['facebook_page']}}" aria-label="Facebook" target="_blank">{!! svg_icon('facebook-footer') !!}</a>@endif
                    @if (!empty($settings['telegram_page']))<a href="{{$settings['telegram_page']}}" aria-label="Telegram" target="_blank">{!! svg_icon('telegram-footer') !!}</a>@endif
                    @if (!empty($settings['youtube_page']))<a href="{{$settings['youtube_page']}}" aria-label="YouTube" target="_blank">{!! svg_icon('youtube-footer') !!}</a>@endif
                </div>
                <div class="clearfix w-100 d-md-none">&nbsp;</div>
                <div class="col-md-3 col-4">
                    <span class="section-head">@lang('general.pages')</span><br><br>
                    <ul class="list-unstyled">
                        @foreach ($footer_pages as $page)
                        <li><a href="{{ asset($language_prefix.$settings['page_base'].'/'.$page[1]) }}">{{$page[0]}}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-3 col-4">
                    <span class="section-head">@lang('general.categories')</span><br><br>
                    <ul class="list-unstyled">
                        @foreach ($categories as $category)
                        @if($category->footer == '1' && $category->type='1')
                        <li><a href="{{ asset($language_prefix.$settings['category_base'].'/'.$category->slug) }}">{{$category->title}}</a></li>
                        @endif
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-3 col-4">
                    <span class="section-head mt-3 mb-4">@lang('general.platforms')</span><br><br>
                    <ul class="list-unstyled">
                        @foreach ($platforms as $platform)
                        @if($platform->footer == '1')
                        <li><a href="{{ asset($language_prefix.$settings['platform_base'].'/'.$platform->slug) }}">{{$platform->title}}</a></li>
                        @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-copyright text-center py-2">© {{date('Y')}} @lang('general.copyright_notice') - <a href="{{ asset('/') }}"> {{$settings['site_title']}}</a></div>
    </footer>
</div>

@if ($settings['show_cookie_bar'] == '1')
<!-- Cookie Alert -->
<div class="alert text-center cookiealert py-2" role="alert">
    @lang('general.cookies_note')
    <button type="button" class="btn btn-sm accept-cookies" aria-label="Close">
        @lang('general.accept_cookies')
    </button>
</div>
<!-- /Cookie Alert -->
@endif

{!!$settings['before_body_end_tag']!!}

@stack('assets_footer')

<!-- Lazy Load -->
<script src="{{ asset('js/jquery.lazy.min.js') }}?2.1.0"></script>
<!-- Lazy Load -->
<script defer async src="{{ asset('js/jquery-ui.min.js') }}?2.1.0"></script>
<!-- Other JS -->
<script src="{{ asset('js/scripts.js') }}?2.1.0"></script>
<!-- Bootstrap -->
<script src="{{ asset('js/bootstrap.bundle.min.js') }}?2.1.0"></script>
