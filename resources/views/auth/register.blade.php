@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
    <div class="loader"></div>
        <div class="col-md-4 col-md-offset-4 animated fadeInDown register">
            <div class="panel panel-default panel-darkcyan-profile">
                <div class="panel-heading panel-heading-darkcyan">{{ trans('label.register') }}</div>
                <div class="panel-body">
                    @include('errors.errors')
                    {{
                        Form::open([
                            'route' => 'user-register',
                            'class' => 'form-horizontal',
                            'files' => true,
                            'id' => 'form-register',
                            'enctype' => 'multipart/form-data'
                        ])
                    }}
                        <div class="form-group">
                            <div class="input-group col-md-10 col-md-offset-1">
                                <span class="input-group-addon" id="basic-addon1">
                                    <i class="fa fa-user" aria-hidden="true"></i>
                                </span>
                                {{
                                    Form::text('name', null, [
                                        'id' => 'name',
                                        'class' => 'form-control',
                                        'placeholder' => trans('user.register.placeholder.name')
                                    ])
                                }}
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="input-group col-md-10 col-md-offset-1">
                                <span class="input-group-addon" id="basic-addon1">
                                    <i class="fa fa-envelope" aria-hidden="true"></i>
                                </span>
                                {{
                                    Form::email('email', null, [
                                        'id' => 'email',
                                        'class' => 'form-control',
                                        'placeholder' => trans('user.register.placeholder.email')
                                    ])
                                }}
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="input-group col-md-10 col-md-offset-1">
                                <span class="input-group-addon" id="basic-addon1">
                                    <i class="fa fa-transgender" aria-hidden="true"></i>
                                </span>
                                {{
                                    Form::select('gender', trans('label.gender'), null, [
                                        'id' => 'gender',
                                        'class' => 'form-control'
                                    ])
                                }}
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="input-group col-md-10 col-md-offset-1">
                                <span class="input-group-addon">
                                    <i class="fa fa-key" aria-hidden="true"></i>
                                </span>
                                {{
                                    Form::password('password', [
                                        'id' => 'password',
                                        'class' => 'form-control',
                                        'placeholder' => trans('user.register.placeholder.password')
                                    ])
                                }}
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="input-group col-md-10 col-md-offset-1">
                                <span class="input-group-addon">
                                    <i class="fa fa-key" aria-hidden="true"></i>
                                </span>
                                {{
                                    Form::password('password_confirmation', [
                                        'id' => 'password-confirm',
                                        'class' => 'form-control',
                                        'placeholder' => trans('user.register.placeholder.password_confirm')
                                    ])
                                }}
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="input-group col-md-10 col-md-offset-1">
                                <span class="input-group-addon" id="basic-addon1">
                                    <i class="fa fa-picture-o" aria-hidden="true"></i>
                                </span>
                                {{
                                    Form::file('avatar', [
                                        'class'=>'form-control',
                                        'onchange' => 'readURL(this, "preview-avatar")'
                                    ])
                                }}
                            </div>
                        </div>
                        <div class="form-group">
                            <img id="preview-avatar" src="#" class="col-md-4 col-md-offset-3 preview-image"  />
                        </div>

                        <div class="form-group">
                            <div class="col-md-12">
                                {{
                                    Form::button('<i class="fa fa-btn fa-user"></i> ' . trans('label.register'), [
                                        'id' => 'btn-register',
                                        'class' => 'btn btn-success btn-block btn-register btn-darkcyan'
                                    ])
                                }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <a class="btn btn-link" href="{{ url('/login') }}">
                                    {{ trans('label.login') }}
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a class="btn btn-link register-text" href="{{ url('/password/reset') }}">
                                    {{ trans('label.forgot_password') }}
                                </a>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
