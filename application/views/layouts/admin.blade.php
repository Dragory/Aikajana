<?php
    header('Content-Type: text/html; charset=UTF-8');
?>
<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <title>{{ __('admin.title') }}</title>

    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') }}" media="screen">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker.css') }}" media="screen">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/colorpicker.css') }}" media="screen">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/admin.css') }}" media="screen">
</head>
<body>
    <div id="wrap">
        <div id="content">
            {{ $breadcrumb }}
            {{ $status }}
            {{ $content }}
        </div>
        <div id="footer">
            {{ $footer }}
        </div>
    </div>

    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-datepicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-colorpicker.js') }}"></script>
    <script type="text/javascript">
        (function() {
            $('.datepicker').datepicker({
                'format': 'yyyy-mm-dd'
            });
            $('.colorpicker').colorpicker({
                'format': 'hex'
            });
        }());
    </script>
</body>
</html>