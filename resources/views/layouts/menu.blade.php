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
                <li class="{!! Request::is('/') ? "active" : "" !!} home-menu {{ (Session::get('locale') == 'ja') ? 'home-menu-ja' : '' }}">
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
                    <div class="hide_language" data-route="{{ url('language') }}" data-token="{{ csrf_token() }}"></div>
                    <div class="multiple-lang">
                        <select name="lang" id="countries" class="form-control btn-multiple-language">
                            <option value='en' {{ Session::get('locale') == 'en' ? 'selected' : '' }}
                            data-image="{{ asset('bower/ms-Dropdown/images/msdropdown/icons/blank.gif') }}"
                                    data-imagecss="flag england" data-title="{{ config('settings.language.en') }}">
                                {{ config('settings.language.en') }}
                            </option>
                            <option value='vi' {{ Session::get('locale') == 'vi' ? 'selected' : '' }}
                            data-image="{{ asset('bower/ms-Dropdown/images/msdropdown/icons/blank.gif') }}"
                                    data-imagecss="flag vn" data-title="{{ config('settings.language.vi') }}">
                                {{ config('settings.language.vi') }}
                            </option>
                            <option value='ja' {{ Session::get('locale') == 'ja' ? 'selected' : '' }}
                            data-image="{{ asset('bower/ms-Dropdown/images/msdropdown/icons/blank.gif') }}"
                                    data-imagecss="flag jp" data-title="{{ config('settings.language.ja') }}">
                                {{ config('settings.language.ja') }}
                            </option>
                        </select>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
