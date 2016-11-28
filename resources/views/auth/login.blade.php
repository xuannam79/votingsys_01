@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="loader"></div>
        <div class="col-md-4 col-md-offset-4 animated fadeInUp login">
            <div class="panel panel-default">
                <div class="panel-heading">{{ trans('label.login') }}</div>
                <div class="panel-body">
                    @if (Session::has('messages'))
                        <div class="col-lg-12">
                            <div class="col-lg-12 alert alert-success">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                {!! Session::get('messages') !!}
                            </div>
                        </div>
                    @endif

                    <div class="row btn-social-login">
                        <div class="col-md-4">
                            <a class="btn btn-social btn-xs btn-facebook" href="{{ url('redirect/facebook') }}">
                                <span class="fa fa-facebook"></span>
                                {{ trans('auth.facebook_login') }}
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a class="btn btn-social btn-xs btn-google" href="{{ url('redirect/google') }}">
                                <span class="fa fa-google"></span>
                                {{ trans('auth.google_login') }}
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a class="btn btn-social btn-xs btn-twitter" href="{{ url('redirect/twitter') }}">
                                <span class="fa fa-twitter"></span>
                                {{ trans('auth.twitter_login') }}
                            </a>
                        </div>
                    </div>
                    <hr>
                    @include('errors.errors')
                    {{ Form::open(['route' => 'user-login', 'class' => 'form-horizontal']) }}
                        <div class="form-group">
                            <div class="col-md-10 col-md-offset-1">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-envelope" aria-hidden="true"></i>
                                    </span>
                                    {{ Form::email('email', old('email'), ['class' => 'form-control', 'placeholder' => trans('user.login.placeholder.email')]) }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-10 col-md-offset-1">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-key" aria-hidden="true"></i>
                                    </span>
                                    {{ Form::password('password', ['id' => 'password', 'class' => 'form-control', 'placeholder' => trans('user.login.placeholder.password')]) }}
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-11 col-md-offset-1">
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
                                <div class="col-md-10 col-md-offset-1">
                                    {{ Form::button('<span class="glyphicon glyphicon-log-in"></span> ' . trans('label.login'), ['type' => 'submit', 'class' => 'btn btn-success btn-block btn-login']) }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <a class="btn btn-link" href="{{ url('/password/reset') }}">
                                        {{ trans('label.forgot_password') }}
                                    </a>
                                </div>
                                <div class="col-md-6">
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
</div>
@endsection
