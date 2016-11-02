<!DOCTYPE html>
<html>
    <head>
        <title> @yield('title')</title>

        <!-- Bootstrap css -->
        <link href="{{ asset('bower/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">

        <!-- Bootstrap Tag Input css -->
        <link href="{{ asset('bower/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}" rel="stylesheet">

        <!-- Master layout css -->
        <link href="{{ asset('css/layout/master.css') }}" rel="stylesheet">

    </head>
    <body>
        @yield('content')

        <!-- Google api -->
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCzfBLqeROyZ1xGhOWb_oG7zmdYcCQdaI8&v=3.exp&sensor=false&libraries=places"></script>

        <!-- Jquery js -->
        <script src="{{ asset('bower/jquery/dist/jquery.min.js') }}" type="text/javascript"></script>

        <!-- Bootstrap js -->
        <script src="{{ asset('bower/bootstrap/dist/js/bootstrap.min.js') }}" type="text/javascript"></script>

        <!-- Bootstrap Tag Input js -->
        <script src="{{ asset('bower/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}" type="text/javascript"></script>

        <!-- Master layout js -->
        <script src="{{ asset('js/layout/master.js') }}" type="text/javascript"></script>
    </body>
</html>
