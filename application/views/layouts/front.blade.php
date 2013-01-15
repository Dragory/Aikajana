<!doctype html>
<html>
<head>
    <meta charset="UTF-8">

    <title>Aikajana</title>

    <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/flags.css') }}">
</head>
<body>
    <script type="text/javascript">
        var charts = [];
    </script>

    <div id="menu">
        {{ $menu }}

    </div>

    <div id="content">
        {{ $content }}

    </div>

    <script type="text/javascript">
        var imgUrl = '{{ asset('img') }}';
        var jsUrl  = '{{ asset('js') }}';
        var cssUrl = '{{ asset('css') }}';
    </script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script type="text/javascript" src="{{ asset('js/chart.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/main.js') }}"></script>
</body>
</html>