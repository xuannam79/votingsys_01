@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading">{{ trans('label.profile') }}</div>
                <div class="panel-body">
                    @include('errors.errors')

                    {{ Form::model($currentUser, ['method' => 'PATCH', 'route' => ['profile.update', $currentUser->id], 'class' => 'form-horizontal', 'role' => 'form', 'files' => true]) }}
                        <div class="form-group">
                        <div class="col-md-12 col-md-offset-4">
                            <img class="img-profile" id="output" src="{{ asset($currentUser->getAvatarPath()) }}"/>
                        </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('avatar', trans('label.avatar'), ['class' => 'col-md-4 control-label']) }}
                            <div class="col-md-6">
                                {{ Form::file('avatar', ['onchange' => 'loadFile(event)']) }}
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('name', trans('label.name'), ['class' => 'col-md-4 control-label']) }}
                            <div class="col-md-6">
                                {{ Form::text('name', $currentUser->name, ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('email', trans('label.email'), ['class' => 'col-md-4 control-label']) }}
                            <div class="col-md-6">
                                {{ Form::email('email', $currentUser->email, ['class' => 'form-control', 'name' => 'email']) }}
                             </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('gender', trans('label.label_gender'), ['class' => 'col-md-4 control-label']) }}
                            <div class="col-md-6">
                                {{ Form::select('gender', trans('label.gender'), $currentUser->gender, ['id' => 'gender', 'class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('password', trans('label.password'), ['class' => 'col-md-4 control-label']) }}
                            <div class="col-md-6">
                                {{ Form::password('password', ['class' => 'form-control', 'name' => 'password']) }}
                             </div>
                        </div>

                        <div class="form-group">
                        {{ Form::label('email', trans('label.confirm_password'), ['class' => 'col-md-4 control-label']) }}
                            <div class="col-md-6">
                                {{ Form::password('password', ['class' => 'form-control', 'name' => 'password_confirmation']) }}
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-user"></i> {{ trans('label.edit') }}
                                </button>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>

            </div>
            @if (auth()->user()->role == config('roles.admin'))
                <div class="row">
                    <a href="{{ route('admin.user.index') }}" class="btn btn-warning btn-large btn-block">
                        {{ trans('label.admin_page') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
