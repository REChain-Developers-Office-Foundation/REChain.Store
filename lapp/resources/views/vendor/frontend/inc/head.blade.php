<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
{!!
\MetaTag::setPath()
->setDefault(['robots' => 'follow', 'canonical' => url()->current()])
->setDefault(['og_site_name' => $settings['site_title']])
->setDefault(['og_locale' => $locale_tags[$language_code]])
->render()
!!}

@if ($head_type == '0')
@foreach ($languages as $language)
<link rel="alternate" hreflang="{{ $language->code }}" href="{{ asset($menu_language_prefix[$language->id]) }}">
@endforeach
@endif

@if ($head_type == '1')
{!! $schema_data->toScript() !!}
@foreach ($app_languages as $translation)
<link rel="alternate" hreflang="{{ $translation['code'] }}" href="{{ asset($menu_language_prefix[$translation['id']].$slug[$translation['id']]['app_base'].'/'.$app_query->slug) }}">
@endforeach
@endif

@if ($head_type == '2')
@foreach ($category_languages as $translation)
<link rel="alternate" hreflang="{{ $translation['code'] }}" href="{{ asset($menu_language_prefix[$translation['id']].$slug[$translation['id']]['category_base'].'/'.$category_query->slug) }}">
@endforeach
@endif

@if ($head_type == '3')
@foreach ($platform_languages as $translation)
<link rel="alternate" hreflang="{{ $translation['code'] }}" href="{{ asset($menu_language_prefix[$translation['id']].$settings['platform_base'].'/'.$platform_query->slug) }}">
@endforeach
@endif

@if ($head_type == '4')
@foreach ($page_languages as $translation)
<link rel="alternate" hreflang="{{ $translation['code'] }}" href="{{ asset($menu_language_prefix[$translation['id']].$slug[$translation['id']]['page_base'].'/'.$page_query->slug) }}">
@endforeach
@endif

@if ($head_type == '5')
@foreach ($news_languages as $translation)
<link rel="alternate" hreflang="{{ $translation['code'] }}" href="{{ asset($menu_language_prefix[$translation['id']].$slug[$translation['id']]['read_base'].'/'.$page_query->slug) }}">
@endforeach
@endif

@if ($head_type == '6')
@foreach ($languages as $language)
@if($language->language != $language_name)
<link rel="alternate" hreflang="{{ $language->code }}" href="{{ asset($menu_language_prefix[$language->id].$slug[$language->id]['news_base']) }}">
@endif
@endforeach
@endif

@if ($head_type == '7')
@foreach ($topic_languages as $translation)
<link rel="alternate" hreflang="{{ $translation['code'] }}" href="{{ asset($menu_language_prefix[$translation['id']].$slug[$translation['id']]['topic_base'].'/'.$topic_query->slug) }}">
@endforeach
@endif

@if ($head_type == '8')
@foreach ($languages as $language)
@if($language->language != $language_name)
<link rel="alternate" hreflang="{{ $language->code }}" href="{{ asset($menu_language_prefix[$language->id].$slug[$language->id]['topic_base']) }}">
@endif
@endforeach
@endif

@if ($head_type == '9')
@foreach ($languages as $language)
@if($language->language != $language_name)
<link rel="alternate" hreflang="{{ $language->code }}" href="{{ asset($menu_language_prefix[$language->id].$slug[$language->id]['contact_slug']) }}">
@endif
@endforeach
@endif

@if ($head_type == '10')
@foreach ($languages as $language)
@if($language->language != $language_name)
<link rel="alternate" hreflang="{{ $language->code }}" href="{{ asset($menu_language_prefix[$language->id].'featured-apps') }}">
@endif
@endforeach
@endif

@if ($head_type == '11')
@foreach ($languages as $language)
@if($language->language != $language_name)
<link rel="alternate" hreflang="{{ $language->code }}" href="{{ asset($menu_language_prefix[$language->id].'popular-apps') }}">
@endif
@endforeach
@endif

@if ($head_type == '12')
@foreach ($languages as $language)
@if($language->language != $language_name)
<link rel="alternate" hreflang="{{ $language->code }}" href="{{ asset($menu_language_prefix[$language->id].'editors-choice') }}">
@endif
@endforeach
@endif

@if ($head_type == '13')
@foreach ($languages as $language)
@if($language->language != $language_name)
<link rel="alternate" hreflang="{{ $language->code }}" href="{{ asset($menu_language_prefix[$language->id].'must-have-apps') }}">
@endif
@endforeach
@endif

@if ($head_type == '14')
@foreach ($languages as $language)
@if($language->language != $language_name)
<link rel="alternate" hreflang="{{ $language->code }}" href="{{ asset($menu_language_prefix[$language->id].'recently-updated-apps') }}">
@endif
@endforeach
@endif

@if ($head_type == '15')
@foreach ($languages as $language)
@if($language->language != $language_name)
<link rel="alternate" hreflang="{{ $language->code }}" href="{{ asset($menu_language_prefix[$language->id].'all-categories') }}">
@endif
@endforeach
@endif

@if ($head_type == '16')
@foreach ($languages as $language)
@if($language->language != $language_name)
<link rel="alternate" hreflang="{{ $language->code }}" href="{{ asset($menu_language_prefix[$language->id].'all-platforms') }}">
@endif
@endforeach
@endif

@if ($head_type == '17')
@foreach ($languages as $language)
@if($language->language != $language_name)
<link rel="alternate" hreflang="{{ $language->code }}" href="{{ asset($menu_language_prefix[$language->id].'browse-history') }}">
@endif
@endforeach
@endif

@if ($head_type == '18')
@foreach ($languages as $language)
@if($language->language != $language_name)
<link rel="alternate" hreflang="{{ $language->code }}" href="{{ asset($menu_language_prefix[$language->id].'favorites') }}">
@endif
@endforeach
@endif

@if ($head_type == '19')
@foreach ($news_category_languages as $translation)
<link rel="alternate" hreflang="{{ $translation['code'] }}" href="{{ asset($menu_language_prefix[$translation['id']].$slug[$translation['id']]['news_base'].'/'.$category_query->slug) }}">
@endforeach
@endif

@if ($head_type == '20')
@foreach ($languages as $language)
@if($language->language != $language_name)
<link rel="alternate" hreflang="{{ $language->code }}" href="{{ asset($menu_language_prefix[$language->id].'search') }}">
@endif
@endforeach
@endif

@if ($head_type == '21')
@foreach ($languages as $language)
@if($language->language != $language_name)
<link rel="alternate" hreflang="{{ $language->code }}" href="{{ asset($menu_language_prefix[$language->id].'submit-app') }}">
@endif
@endforeach
@endif

@if ($head_type == '22')
@foreach ($app_languages as $translation)
@php $id = substr(url()->current(), strrpos(url()->current(), '/') + 1); @endphp
<link rel="alternate" hreflang="{{ $translation['code'] }}" href="{{ asset($menu_language_prefix[$translation['id']].'redirect'.'/'.$app_query->slug.'/'.$id) }}">
@endforeach
@endif

@if ($head_type == '23')
@foreach ($languages as $language)
@if($language->language != $language_name)
<link rel="alternate" hreflang="{{ $language->code }}" href="{{ asset($menu_language_prefix[$language->id].'new-apps') }}">
@endif
@endforeach
@endif

<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="alternate" type="application/rss+xml" title="{{$settings['site_title']}}" href="{{ asset($language_prefix.'rss') }}">
<link rel="icon" type="image/png" href="{{ s3_switch('favicon.png') }}?t={{$settings['update_count']}}">
<meta property="base_url" content="{{ rtrim(asset($language_prefix), "/") }}">

@if(!empty($settings['meta_theme_color']))
<meta name="theme-color" content="{{ $settings['meta_theme_color'] }}">
@endisset

<!-- Bootstrap -->
<link href="{{ asset('css/bootstrap.min.css') }}?2.1.0" rel="stylesheet">
<!-- Common Styles -->
<link href="{{ asset('css/app.css') }}?2.1.0" rel="stylesheet">
<!-- Custom Styles -->
<link href="{{ asset('css/custom.css') }}?t={{$settings['update_count']}}" rel="stylesheet">
<!-- jQuery UI -->
<link href="{{ asset('css/jquery-ui.min.css') }}?2.1.0" rel="stylesheet">
<!-- Flag Icons -->
<link href="{{ asset('css/flag-icons.min.css') }}?2.1.0" rel="stylesheet">
<!-- jQuery -->
<script src="{{ asset('js/jquery-3.6.1.min.js') }}?2.1.0"></script>
<!-- js-cookie -->
<script src="{{ asset('js/js.cookie.min.js') }}?2.1.0"></script>
<!-- notificationManager -->
<script src="{{ asset('js/notificationManager.js') }}?2.1.0"></script>
<!-- Other JS -->
<script src="{{ asset('js/other.js') }}?2.1.0"></script>

{!! $localBusiness->toScript() !!}

@stack('assets_header')

@if ($settings['enable_google_recaptcha'] == '1')
<!-- Google reCAPTCHA -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif

@if ($settings['enable_pwa'] == '1')
<script type="module">
    import 'https://cdn.jsdelivr.net/npm/@pwabuilder/pwaupdate';
   const el = document.createElement('pwa-update');
   document.body.appendChild(el);
</script>

<link rel="manifest" href="{{ asset('manifest.json') }}">
@endif

{!!$settings['before_head_tag']!!}
