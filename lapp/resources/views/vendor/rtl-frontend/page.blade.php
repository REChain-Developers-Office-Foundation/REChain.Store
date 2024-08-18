<!DOCTYPE html>
<html dir="rtl" lang="{{$language_code}}">
<head>
    @include('rtl-frontend::inc.head')
</head>
<body>

@include('rtl-frontend::inc.header')

@yield('content')

@include('rtl-frontend::inc.footer')

</body>
</html>