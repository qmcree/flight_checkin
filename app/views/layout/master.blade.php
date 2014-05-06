<!doctype html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>    <html class="lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>    <html class="lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en" class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Flight Checkin Automator</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" type="text/css" href="/packages/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="/packages/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" />
    <link rel="stylesheet" type="text/css" href="/packages/flight_checkin/css/flight_checkin.css" />

    <!-- <script src="//modernizr.com/downloads/modernizr-latest.js"></script> --> <!-- @todo Build production version. -->
</head>
<body>
<!--[if lt IE 8]>
<p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

@section('navbar')
<nav id="navbar-top" class="navbar navbar-default" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-top-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">Flight Checkin Automator</a>
        </div>
        <p class="navbar-text"><small>Helping {{ $reservation_count }} passengers get that perfect seat.</small></p>
    </div>
</nav>
@show

<div class="container">
        @if (isset($_success) && !is_null($_success))
        <div class="alert alert-success">
            {{ $_success }}
        </div>
        @endif

        @if (isset($_info) && !is_null($_info))
        <div class="alert alert-info">
            {{ $_info }}
        </div>
        @endif

        @if (isset($_warning) && !is_null($_warning))
        <div class="alert alert-warning">
            {{ $_warning }}
        </div>
        @endif

        @if (isset($_danger) && !is_null($_danger))
        <div class="alert alert-danger">
            {{ $_danger }}
        </div>
        @endif

        @yield('content')
</div>

<!-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script> -->
<script src="/packages/jquery/js/jquery.min.js"></script>
<script src="/packages/underscore/js/underscore-min.js"></script>
<script src="/packages/bootstrap/js/bootstrap.min.js"></script>
<script src="/packages/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script src="/packages/flight_checkin/js/flight_checkin.js"></script>
</body>
</html>