<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="shortcut icon" href="{{ asset('uploads/images/fpoll_logo.png') }}"/>
        @yield('meta')
        <title>{{ config('app.name', 'Laravel') }}</title>

        {!! Html::style('css/app.css') !!}

        <!-- Dropdown multiple language -->
        {!! Html::style('bower/ms-Dropdown/css/msdropdown/dd.css') !!}
        {!! Html::style('bower/ms-Dropdown/css/msdropdown/flags.css') !!}

        <!-- scroll animation -->
        {!! Html::style('bower/css3-animate-it/css/animations.css') !!}

        <!-- Sweet alert -->
        {!! Html::style('bower/sweetalert/dist/sweetalert.css') !!}

        <!-- Bootstrap CSS -->
        {!! Html::style('bower/bootstrap/dist/css/bootstrap.min.css') !!}

        <!-- Bootstrap theme CSS -->
        {!! Html::style('bower/bootstrap/dist/css/bootstrap-theme.min.css') !!}

        <!-- Styles -->
        {!! Html::style('css/layout/master.css') !!}
        {!! Html::style('css/user.css') !!}

        <!-- Bootstrap datatable CSS -->
        {!! Html::style('bower/datatables.net-bs/css/dataTables.bootstrap.min.css') !!}

        <!-- Animate -->
        {!! Html::style('bower/animate.css/animate.min.css') !!}

        <!-- Social button -->
        {!! Html::style('bower/font-awesome/css/font-awesome.min.css') !!}
        {!! Html::style('bower/bootstrap-social/bootstrap-social.css') !!}

        <!-- Bootstrap Tag Input css -->
        {!! Html::style('bower/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') !!}

        <!-- Bootstrap Switch css -->
        {!! Html::style('bower/bootstrap-switch/dist/css/bootstrap2/bootstrap-switch.min.css') !!}

        <!-- Datetime picker -->
        {!! Html::style('bower/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css') !!}

        <!-- Google api -->
        @if (Session::get('locale') == 'ja')
            <script type="text/javascript"
                    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCzfBLqeROyZ1xGhOWb_oG7zmdYcCQdaI8&v=3.exp&libraries=places&language=ja">
            </script>
        @elseif(Session::get('locale') == 'vi')
            <script type="text/javascript"
                    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCzfBLqeROyZ1xGhOWb_oG7zmdYcCQdaI8&v=3.exp&libraries=places&language=vi">
            </script>
        @else
            <script type="text/javascript"
                    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCzfBLqeROyZ1xGhOWb_oG7zmdYcCQdaI8&v=3.exp&libraries=places&language=en">
            </script>
        @endif
    </head>
    <body>

        <!-- MENU -->
        <nav class="navbar navbar-default  animated fadeInDown">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#menu">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="{{ asset("/") }}">
                        <img src="{{ asset('uploads/images/fpoll_logo.png') }}">
                    </a>
                </div>
                <div class="collapse navbar-collapse" id="menu">
                    <ul class="nav-menu nav navbar-nav">
                        <li {!! Request::is('/') ? 'class="active"' : '' !!}>
                            <a href="{{ asset("/") }}"><span class="glyphicon glyphicon-home"></span> {{ trans('label.home') }}</a>
                        </li>
                        <li>
                            <a href="{{ url('/tutorial') }}" target="_blank"><span class="glyphicon glyphicon-file"></span> {{ trans('label.tutorial') }}</a>
                        </li>
                        <li>
                            <a href="{{ config('settings.feedback') }}" target="_blank"><span class="fa fa-commenting"></span> {{ trans('label.feedback') }}</a>
                        </li>
                        @if (auth()->check())
                            <li {!! Request::is('user/poll') ? 'class="active"' : '' !!}>
                                <a href="{{ URL::action('User\PollController@index') }}">
                                    <i class="fa fa-history" aria-hidden="true"></i> {{ trans('polls.poll_history') }}
                                </a>
                            </li>
                        @endif
                    </ul>
                    <ul class="nav-menu nav navbar-nav navbar-right">
                        @if (Auth::guest())
                            <li {!! Request::is('login') ? 'class="active"' : '' !!}>
                                <a href="{{ url('/login') }}">
                                    <span class="glyphicon glyphicon-log-in"></span> {{ trans('label.login') }}
                                </a>
                            </li>
                            <li {!! Request::is('register') ? 'class="active"' : '' !!}>
                                <a href="{{ url('/register') }}">
                                    <span class="glyphicon glyphicon-registration-mark"></span> {{ trans('label.register') }}
                                </a>
                            </li>
                        @else
                            <li {!! Request::is('user/profile') ? 'class="active"' : '' !!}>
                                <a href="{{ URL::action('User\UsersController@index') }}">
                                    <span>
                                        <img class="img-circle img-profile-header" src="{{ auth()->user()->getAvatarPath() }}">
                                        {{ str_limit(auth()->user()->name, 10) }}
                                    </span>
                                </a>
                            </li>
                            <li {!! Request::is('/logout') ? 'class="active"' : '' !!}>
                                <a href="{{ url('/logout') }}"
                                   onclick="event.preventDefault();
                                         document.getElementById('logout-form').submit();">
                                    <span class="glyphicon glyphicon-log-out">
                                        {{ trans('label.logout') }}
                                    </span>
                                </a>
                                <form id="logout-form" action="{{ url('/logout') }}" method="POST">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        @endif
                        <li>
                            <div class="hide_language" data-route="{{ url('language') }}"></div>
                            <div class="multiple-lang">
                                <select name="lang" id="countries" class="form-control btn-multiple-language">
                                    <option value='en' {{ Session::get('locale') == 'en' ? 'selected' : '' }} data-image="{{ asset('bower/ms-Dropdown/images/msdropdown/icons/blank.gif') }} " data-imagecss="flag england" data-title="English">English</option>
                                    <option value='vi' {{ Session::get('locale') == 'vi' ? 'selected' : '' }} data-image="{{ asset('bower/ms-Dropdown/images/msdropdown/icons/blank.gif') }}" data-imagecss="flag vn" data-title="Tiếng Việt">Tiếng Việt</option>
                                </select>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- CONTENT -->
        <div class="container-fluid">
            @yield('content')
            <script src="//code.jquery.com/jquery.js"></script>
            <a href="javascript:void(0);" id="scroll">{{ trans('label.top') }}<span></span></a>
        </div>

        <!-- FOOTER -->
        <footer>
            <div class="col-lg-3 footer-left">
                <p><b class="char-app">F</b><label>poll</label></p>
                <p>
                    <img src="{{ asset('uploads/images/logo.png') }}" class="copyright-image">
                    <a href="{{ config('settings.copyright') }}" target="_blank" class="copyright-text">
                        {!! trans('label.footer.copyright') !!}
                    </a>
                </p>
            </div>
            <div class="col-lg-5 footer-center">
                <div class="col-lg-12">
                    <p><i class="fa fa-map-marker" aria-hidden="true"></i> {{ trans('label.footer.location') }}</p>
                </div>
                <div class="col-lg-12">
                    <p><i class="fa fa-phone" aria-hidden="true"></i> {{ trans('label.footer.phone') }}</p>
                </div>
                <div class="col-lg-12">
                    <p><i class="fa fa-envelope" aria-hidden="true"></i> {{ trans('label.footer.email') }}</p>
                </div>
            </div>
            <div class="col-lg-4 footer-right">
                <p>{{ trans('label.footer.about') }}</p>
                <a href="{{ trans('label.footer.facebook') }}" target="_blank" class="btn btn-primary">
                    <span><i class="fa fa-facebook" aria-hidden="true"></i></span>
                </a>
                <a href="{{ trans('label.footer.github') }}" target="_blank" class="btn btn-warning">
                    <span><i class="fa fa-github" aria-hidden="true"></i></span>
                </a>
                <a href="{{ trans('label.footer.linkedin') }}" target="_blank" class="btn btn-success">
                    <span><i class="fa fa-linkedin" aria-hidden="true"></i></span>
                </a>
            </div>
        </footer>

        <!-- jQuery -->
        {!! Html::script('bower/jquery/dist/jquery.min.js') !!}
        {!! Html::script('bower/jquery-validation/dist/jquery.validate.min.js') !!}

        <!-- Bootstrap -->
        {!! Html::script('bower/bootstrap/dist/js/bootstrap.min.js') !!}

        <!-- winzard -->
        {!! Html::script('bower/twitter-bootstrap-wizard/jquery.bootstrap.wizard.js') !!}

        <!-- Scripts -->
        {!! Html::script('js/shareSocial.js') !!}
        {!! Html::script('js/comment.js') !!}
        {!! Html::script('js/vote.js') !!}
        {!! Html::script('js/listPolls.js') !!}
        {!! Html::script('js/managePoll.js') !!}
        {!! Html::script('js/editLink.js') !!}
        {!! Html::script('js/multipleLanguage.js') !!}
        {!! Html::script('js/layout/master.js') !!}

        <!-- jQuery Datatable JavaScript -->
        {!! Html::script('/bower/datatables.net/js/jquery.dataTables.min.js') !!}

        <!-- Bootstrap Datatable JavaScript -->
        {!! Html::script('/bower/datatables.net-bs/js/dataTables.bootstrap.min.js') !!}

        <!-- Tag Input -->
        {!! Html::script('/bower/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') !!}

        <!-- Datetime picker -->
        {!! Html::script('/bower/moment/min/moment.min.js') !!}
        {!! Html::script('/bower/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') !!}

        <!-- Bootstrap switch -->
        {!! Html::script('bower/bootstrap-switch/dist/js/bootstrap-switch.min.js') !!}

        <!-- sweet alert -->
        {!! Html::script('bower/sweetalert/dist/sweetalert.min.js') !!}

        <!-- Dropdown multiple language -->
        {!! Html::script('bower/ms-Dropdown/js/msdropdown/jquery.dd.min.js') !!}

        <!-- scroll animate -->
        {!! Html::script('bower/css3-animate-it/js/css3-animate-it.js') !!}
    </body>
</html>
