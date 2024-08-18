<!DOCTYPE html>
<html lang="{{$language_code}}">
<head>
    @include('frontend::inc.head')
</head>
<body>

@include('frontend::inc.header')

@yield('content')

@include('frontend::inc.footer')

</body>
</html>