<!DOCTYPE html>
<html>
<head>
    @include ('parts.head')
</head>
<body>
    @include ('parts.header')
    @yield ('content')
    @include ('parts.footer')
</body>
</html>