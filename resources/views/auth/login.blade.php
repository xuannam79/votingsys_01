@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Login</div>
                <div class="panel-body">
                    @include('errors.errors')
                    {{ Form::open(['url' => '/login', 'class' => 'form-horizontal']) }}
                        <div class="form-group">
                            {{ Form::label('email', trans('label.email'), ['class' => 'col-md-4 control-label']) }}

                            <div class="col-md-6">
                                {{ Form::email('email', old('email'), ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('password', trans('label.password'), ['class' => 'col-md-4 control-label']) }}

                            <div class="col-md-6">
                                {{ Form::password('password', ['id' => 'password', 'class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        {{ Form::checkbox('remember') }}
                                        {{ trans('label.remember') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                {{ Form::button('<i class="fa fa-btn fa-sign-in"></i>' . trans('label.login'), ['type' => 'submit', 'class' => 'btn btn-success']) }}
                                <a class="btn btn-link" href="{{ url('/password/reset') }}">
                                    {{ trans('label.forgot_password') }}
                                </a>
                                <p>
                                    <a class="btn btn-link" href="{{ url('redirect/facebook') }}">
                                        <span class="fa fa-facebook"></span> {{ trans('auth.facebook_login') }}
                                    </a>

                                    <a class="btn btn-link" href="{{ url('redirect/twitter') }}">
                                        <span class="fa fa-twitter"></span> {{ trans('auth.twitter_login') }}
                                    </a>

                                    <a class="btn btn-link" href="{{ url('redirect/google') }}">
                                        <span class="fa fa-google"></span> {{ trans('auth.google_login') }}
                                    </a>
                                </p>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
