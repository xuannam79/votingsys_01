<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <title>@yield('title')</title>
        <!-- Favicon-->
        <link rel="icon" href="favicon.ico" type="image/x-icon">

        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

        <!-- Bootstrap Core Css -->
        <link href="{{ asset('bower/adminbsb-materialdesign/plugins/bootstrap/css/bootstrap.css') }}" rel="stylesheet">

        <!-- Bootstrap Tagsinput Css -->
        <link href="{{ asset('bower/adminbsb-materialdesign/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css') }}" rel="stylesheet">

        <!-- Waves Effect Css -->
        <link href="{{ asset('bower/adminbsb-materialdesign/plugins/node-waves/waves.css') }}" rel="stylesheet" />

        <!-- Animation Css -->
        <link href="{{ asset('bower/adminbsb-materialdesign/plugins/animate-css/animate.css') }}" rel="stylesheet" />

        <!-- Preloader Css -->
        <link href="{{ asset('bower/adminbsb-materialdesign/plugins/material-design-preloader/md-preloader.css') }}" rel="stylesheet" />

        <!-- Bootstrap Spinner Css -->
        <link href="{{ asset('bower/adminbsb-materialdesign/plugins/jquery-spinner/css/bootstrap-spinner.css') }}" rel="stylesheet">

        <!-- Morris Chart Css-->
        <link href="{{ asset('bower/adminbsb-materialdesign/plugins/morrisjs/morris.css') }}" rel="stylesheet" />

        <!-- Custom Css -->
        <link href="{{ asset('bower/adminbsb-materialdesign/css/style.css') }}" rel="stylesheet">
        <link href="{{ asset('css/admin/master.css') }}" rel="stylesheet">

        <!-- Bootstrap Select Css -->
        <link href="{{ asset('bower/adminbsb-materialdesign/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" />

        <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
        <link href="{{ asset('bower/adminbsb-materialdesign/css/themes/all-themes.css') }}" rel="stylesheet" />

        <!-- Sweet Alert Css -->
        <link href="{{ asset('bower/adminbsb-materialdesign/plugins/sweetalert/sweetalert.css') }}" rel="stylesheet" />

        <!--Bootstrap Toggle -->
        <link href="{{ asset('bower/bootstrap-toggle/css/bootstrap-toggle.min.css') }}" rel="stylesheet">

    </head>

    <body class="theme-cyan">
        <!-- Page Loader -->
        <div class="page-loader-wrapper">
            <div class="loader">
                <div class="md-preloader pl-size-md">
                    <svg viewbox="0 0 75 75">
                        <circle cx="37.5" cy="37.5" r="33.5" class="pl-red" stroke-width="4" />
                    </svg>
                </div>
            </div>
        </div>
        <!-- #END# Page Loader -->
        <!-- Overlay For Sidebars -->
        <div class="overlay"></div>
        <!-- #END# Overlay For Sidebars -->
        <!-- Top Bar -->
        <nav class="navbar">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse"
                       data-target="#navbar-collapse" aria-expanded="false">
                    </a>
                    <a href="javascript:void(0);" class="bars"></a>
                    <a class="navbar-brand">{{ trans('label.name_admin_page') }}</a>
                </div>
            </div>
        </nav>
        <!-- #Top Bar -->
        <section>
            <!-- Left Sidebar -->
            <aside id="leftsidebar" class="sidebar">
                <!-- User Info -->
                <div class="user-info">
                    <div class="image">
                        <img src="{{ auth()->user()->getAvatarPath() }}" width="48" height="48" alt="User" />
                    </div>
                    <div class="info-container">
                        <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ auth()->user()->name }}</div>
                        <div class="email">{{ auth()->user()->email }}</div>
                        <div class="btn-group user-helper-dropdown">
                            <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>
                            <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
                            <ul class="dropdown-menu pull-right">
                                <li>
                                    <a href="{{ URL::action('User\UsersController@index') }}"><i class="material-icons">person</i>{{ trans('label.profile') }}</a>
                                </li>
                                <li role="seperator" class="divider"></li>
                                <li>
                                    <a href="{{ url('/logout') }}"><i class="material-icons">input</i>{{ trans('label.logout') }}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- #User Info -->
                <!-- Menu -->
                <div class="menu">
                    <ul class="list">
                        <li class="header">{{ trans('label.main_menu') }}</li>
                        <li class="active">
                            <a href="{{ URL::action('HomeController@index') }}">
                                <i class="material-icons">home</i>
                                <span>{{ trans('label.home') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="menu-toggle">
                                <i class="material-icons">person</i>
                                <span>{{ trans('label.nav_menu.user') }}</span>
                            </a>
                            <ul class="ml-menu">
                                <li>
                                    <a href="{{ route('admin.user.create') }}">
                                        <i class="material-icons">person_add</i>
                                        <span>{{ trans('user.panel_head.create') }}</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.user.index') }}">
                                        <i class="material-icons">people</i>
                                        <span>{{ trans('user.panel_head.index') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="menu-toggle">
                                <i class="material-icons">poll</i>
                                <span>{{ trans('label.nav_menu.poll') }}</span>
                            </a>
                            <ul class="ml-menu">
                                <li>
                                    <a href="{{ route('admin.poll.create') }}">
                                        <i class="material-icons">add</i>
                                        <span>{{ trans('polls.head.create') }}</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.poll.index') }}">
                                        <i class="material-icons">list</i>
                                        <span>{{ trans('polls.head.index') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <!-- #Menu -->
                <!-- Footer -->
                <div class="legal">
                    <div class="copyright">
                         {{ trans('label.footer.copyright') }}
                    </div>
                </div>
                <!-- #Footer -->
            </aside>
            <!-- #END# Left Sidebar -->
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-10 col-lg-offset-1">
                        @yield('content')
                    </div>
                </div>
            </div>
        </section>

        <!-- Google api -->
        <script type="text/javascript"
                src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCzfBLqeROyZ1xGhOWb_oG7zmdYcCQdaI8&v=3.exp&sensor=false&libraries=places">
        </script>

        <!-- Jquery Core Js -->
        <script src="{{ asset('bower/adminbsb-materialdesign/plugins/jquery/jquery.min.js') }}"></script>

        <!-- Bootstrap Core Js -->
        <script src="{{ asset('bower/adminbsb-materialdesign/plugins/bootstrap/js/bootstrap.js') }}"></script>

        <!-- Select Plugin Js -->
        <script src="{{ asset('bower/adminbsb-materialdesign/plugins/bootstrap-select/js/bootstrap-select.js') }}"></script>

        <!-- Slimscroll Plugin Js -->
        <script src="{{ asset('bower/adminbsb-materialdesign/plugins/jquery-slimscroll/jquery.slimscroll.js') }}"></script>

        <!-- Waves Effect Plugin Js -->
        <script src="{{ asset('bower/adminbsb-materialdesign/plugins/node-waves/waves.js') }}"></script>

        <!-- Jquery CountTo Plugin Js -->
        <script src="{{ asset('bower/adminbsb-materialdesign/plugins/jquery-countto/jquery.countTo.js') }}"></script>

        <!-- Morris Plugin Js -->
        <script src="{{ asset('bower/adminbsb-materialdesign/plugins/raphael/raphael.min.js') }}"></script>
        <script src="{{ asset('bower/adminbsb-materialdesign/plugins/morrisjs/morris.js') }}"></script>

        <!-- ChartJs -->
        <script src="{{ asset('bower/adminbsb-materialdesign/plugins/chartjs/Chart.bundle.js') }}"></script>

        <!-- Sparkline Chart Plugin Js -->
        <script src="{{ asset('bower/adminbsb-materialdesign/plugins/jquery-sparkline/jquery.sparkline.js') }}"></script>

        <!-- Jquery Spinner Plugin Js -->
        <script src="{{ asset('bower/adminbsb-materialdesign/plugins/jquery-spinner/js/jquery.spinner.js') }}"></script>

        <!-- Custom Js -->
        <script src="{{ asset('bower/adminbsb-materialdesign/js/admin.js') }}"></script>
        <script src="{{ asset('bower/adminbsb-materialdesign/js/pages/ui/tooltips-popovers.js') }}"></script>

        <!-- Demo Js -->
        <script src="{{ asset('bower/adminbsb-materialdesign/js/demo.js') }}"></script>

        <!-- Admin js -->
        <script src="{{ asset('js/admin/master.js') }}"></script>

        <!-- form-wizard -->
        <!-- Jquery Validation Plugin Css -->
        <script src="{{ asset('bower/adminbsb-materialdesign/plugins/jquery-validation/jquery.validate.js') }}"></script>

        <!-- JQuery Steps Plugin Js -->
        <script src="{{ asset('bower/adminbsb-materialdesign/plugins/jquery-steps/jquery.steps.js') }}"></script>

        <!-- Sweet Alert Plugin Js -->
        <script src="{{ asset('bower/adminbsb-materialdesign/plugins/sweetalert/sweetalert.min.js') }}"></script>

        <!-- Waves Effect Plugin Js -->
        <script src="{{ asset('bower/adminbsb-materialdesign/plugins/node-waves/waves.js') }}"></script>
        <!-- END form-wizard -->

        <!--Bootstrap Toggle JS-->
        <script src="{{ asset('bower/bootstrap-toggle/js/bootstrap-toggle.min.js') }}"></script>

        <!-- Bootstrap Tags Input Plugin Js -->
        <script src="{{ asset('bower/adminbsb-materialdesign/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js') }}"></script>

        <!-- Select Plugin Js -->
        <script src="{{ asset('bower/adminbsb-materialdesign/plugins/bootstrap-select/js/bootstrap-select.js') }}"></script>
    </body>
</html>
