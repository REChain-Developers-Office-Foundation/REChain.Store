<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    {{-- Base Meta Tags --}}
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ s3_switch('favicon.png') }}" />
    <meta property="base_url" content="{{ rtrim(asset(env('ADMIN_URL'))) }}">

    {{-- Custom Meta Tags --}}
    @yield('meta_tags')

    {{-- Title --}}
    <title>
        @yield('title_prefix', config('adminlte.title_prefix', ''))
        @yield('title', config('adminlte.title', 'AdminLTE 3'))
        @yield('title_postfix', config('adminlte.title_postfix', ''))@hasSection('content_header') - @yield('content_header')@endif
    </title>

    {{-- Custom stylesheets (pre AdminLTE) --}}
    @yield('adminlte_css_pre')

    {{-- Base Stylesheets --}}
    @if(!config('adminlte.enabled_laravel_mix'))
        <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}?v2.1.0">
        <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}?v2.1.0">

        {{-- Configured Stylesheets --}}
        @include('adminlte::plugins', ['type' => 'css'])

        <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}?v2.1.0">
        <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/custom.css') }}?v2.1.0">
        <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}?v2.1.0">
        <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/bootstrap-select.min.css') }}?v2.1.0">
        <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/sweetalert2.css') }}?v2.1.0">
        <link rel="stylesheet" href="{{ asset('vendor/summernote/summernote-bs4.css') }}?v2.1.0">
        <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/bootstrap-colorpicker.min.css') }}?v2.1.0">
        <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/ekko-lightbox.css') }}?v2.1.0">
        <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/tagsinput.css') }}?v2.1.0">
        <link rel="stylesheet" href="{{ asset('css/flag-icons.min.css') }}?v2.1.0">
        <link rel="stylesheet" href="{{ asset('css/jquery-ui.min.css') }}?v2.1.0">

        @if (Auth::check())
        <script src="{{ asset('vendor/adminlte/dist/js/jquery-3.6.1.min.js') }}?v2.1.0"></script>
        <script src="{{ asset('vendor/adminlte/dist/js/custom.js') }}?v2.1.0"></script>
        <script src="{{ asset('vendor/other/jquery-ui.js') }}?v2.1.0"></script>
        <script src="{{ asset('vendor/other/jquery.ui.touch-punch.min.js') }}?v2.1.0"></script>
        <script src="{{ asset('vendor/adminlte/dist/js/sweetalert2.js') }}?v2.1.0"></script>
        <script src="{{ asset('js/notificationManager.js') }}?v2.1.0"></script>
        <script src="{{ asset('vendor/adminlte/dist/js/jquery.form.min.js') }}?v2.1.0"></script>
        @endif

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

        @endif 
</head>

<body class="@yield('classes_body')" @yield('body_data')>

    {{-- Body Content --}}
    @yield('body')

    @if (Auth::check())
    {{-- Base Scripts --}}
    @if(!config('adminlte.enabled_laravel_mix'))
        <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}?v2.1.0"></script>
        <script src="{{ asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}?v2.1.0"></script>
        <script src="{{ asset('vendor/summernote/summernote-bs4.js') }}?v2.1.0"></script>
        {{-- Configured Scripts --}}
        @include('adminlte::plugins', ['type' => 'js'])

        <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}?v2.1.0"></script>
        <script src="{{ asset('vendor/adminlte/dist/js/bootstrap-select.min.js') }}?v2.1.0"></script>
        <script src="{{ asset('vendor/adminlte/dist/js/bootstrap-colorpicker.min.js') }}?v2.1.0"></script>
        <script src="{{ asset('vendor/adminlte/dist/js/ekko-lightbox.min.js') }}?v2.1.0"></script>
        <script src="{{ asset('vendor/adminlte/dist/js/tagsinput.js') }}?v2.1.0"></script>
        <script src="{{ asset('vendor/adminlte/dist/js/other.js') }}?v2.1.0"></script>
    @else
        <script src="{{ mix(config('adminlte.laravel_mix_js_path', 'js/app.js')) }}?v2.1.0"></script>
    @endif
    @endif

    {{-- Livewire Script --}}
    @if(config('adminlte.livewire'))
        @if(app()->version() >= 7)
            @livewireScripts
        @else
            <livewire:scripts />
        @endif
    @endif

    {{-- Custom Scripts --}}
    @yield('adminlte_js')

</body>

</html>
