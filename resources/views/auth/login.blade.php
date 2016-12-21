@extends('layouts.app')
@push('login-style')

    <!-- SOCIAL BUTTON: login by social -->
    {!! Html::style('bower/bootstrap-social/bootstrap-social.css') !!}
@endpush
@section('content')
<div class="container">
    <div class="row">
        <div class="loader"></div>
        <div class="col-lg-4 col-lg-offset-4
                    col-md-4 col-md-offset-4
                    col-sm-6 col-sm-offset-3
                    col-xs-8 col-xs-offset-2
                    col-xs-login
                    animated fadeInUp login">
            <div class="panel panel-default panel-darkcyan-profile">
                <div class="panel-heading panel-heading-darkcyan">{{ trans('label.login') }}</div>
                <div class="panel-body">
                    @if (Session::has('messages'))
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="alert alert-success">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                {!! Session::get('messages') !!}
                            </div>
                        </div>
                    @endif
                    @include('errors.errors')
                    {{ Form::open(['route' => 'user-login', 'class' => 'form-horizontal']) }}
                        <div class="form-group">
                            <div class="col-lg-10 col-lg-offset-1
                                        col-md-10 col-md-offset-1
                                        col-sm-10 col-sm-offset-1
                                        col-xs-10 col-xs-offset-1
                                        col-xs-login">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-envelope" aria-hidden="true"></i>
                                    </span>
                                    {{
                                        Form::email('email', old('email'), [
                                            'class' => 'form-control',
                                            'placeholder' => trans('user.login.placeholder.email')
                                        ])
                                    }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-10 col-lg-offset-1
                                        col-md-10 col-md-offset-1
                                        col-sm-10 col-sm-offset-1
                                        col-xs-10 col-xs-offset-1
                                        col-xs-login">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-key" aria-hidden="true"></i>
                                    </span>
                                    {{
                                        Form::password('password', [
                                            'id' => 'password',
                                            'class' => 'form-control',
                                            'placeholder' => trans('user.login.placeholder.password')
                                        ])
                                    }}
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-lg-11 col-lg-offset-1
                                        col-md-11 col-md-offset-1
                                        col-sm-11 col-sm-offset-1
                                        col-xs-10 col-xs-offset-1
                                        col-xs-login">
                                <div class="checkbox">
                                    <label>
                                        {{ Form::checkbox('remember') }}
                                        {{ trans('label.remember') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-10 col-lg-offset-1
                                            col-md-10 col-md-offset-1
                                            col-sm-10 col-sm-offset-1
                                            col-xs-10 col-xs-offset-1
                                            col-xs-login col-xs-btn-login">
                                    {{
                                        Form::button('<span class="glyphicon glyphicon-log-in"></span> ' . trans('label.login'), [
                                            'type' => 'submit',
                                            'class' => 'btn btn-success btn-block btn-login btn-darkcyan'
                                        ])
                                    }}
                                </div>
                            </div>
                            <br>
                            <div class="row btn-social-custom">
                                <div class="col-lg-10 col-lg-offset-1
                                            col-md-10 col-md-offset-1
                                            col-sm-10 col-sm-offset-1
                                            col-xs-10 col-xs-offset-1
                                            col-xs-login col-xs-btn-login">
                                    <a class="btn btn-login btn-block btn-social btn-facebook" href="{{ url('redirect/facebook') }}">
                                        <span class="fa fa-facebook"></span>
                                        <span>{{ trans('auth.facebook_login') }}</span>
                                    </a>
                                </div>
                            </div>

                            <div class="row btn-social-custom">
                                <div class="col-lg-10 col-lg-offset-1
                                            col-md-10 col-md-offset-1
                                            col-sm-10 col-sm-offset-1
                                            col-xs-10 col-xs-offset-1
                                            col-xs-login col-xs-btn-login">
                                    <a class="btn btn-login btn-block btn-social btn-google" href="{{ url('redirect/google') }}">
                                        <span class="fa fa-google"></span>
                                        {{ trans('auth.google_login') }}
                                    </a>
                                </div>
                            </div>

                            <div class="row btn-social-custom">
                                <div class="col-lg-10 col-lg-offset-1
                                            col-md-10 col-md-offset-1
                                            col-sm-10 col-sm-offset-1
                                            col-xs-10 col-xs-offset-1
                                            col-xs-login col-xs-btn-login">
                                    <a class="btn btn-login btn-block btn-social btn-twitter" href="{{ url('redirect/twitter') }}">
                                        <span class="fa fa-twitter"></span>
                                        {{ trans('auth.twitter_login') }}
                                    </a>
                                </div>
                            </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col-xs-login">
                                    <a class="btn btn-link" href="{{ url('/password/reset') }}">
                                        {{ trans('label.forgot_password') }}
                                    </a>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col-xs-login">
                                    <a class="btn btn-link register-text" href="{{ url('/register') }}">
                                        {{ trans('label.register') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
