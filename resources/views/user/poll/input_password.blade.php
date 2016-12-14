@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-4 col-lg-offset-4
                    col-md-6 col-md-offset-3
                    col-sm-6 col-sm-offset-3">
            <div class="panel panel-default panel-darkcyan">
                <div class="panel-heading panel-heading-darkcyan">{{ trans('label.password') }}</div>
                <div class="panel-body">
                    @include('message')
                    @include('errors.errors')
                    {{ Form::open(['route' => 'set-password.store']) }}
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon1"><i class="fa fa-lock" aria-hidden="true"></i></span>
                                {{
                                    Form::password('password', [
                                        'id' => 'password',
                                        'class' => 'form-control',
                                        'placeholder' => trans('polls.placeholder.password_poll'),
                                        'autofocus' => 'true'
                                    ])
                                }}
                                <div class="input-group-btn">
                                    {{
                                        Form::button('<i class="glyphicon glyphicon-ok"></i>', [
                                            'type' => 'submit',
                                            'class' => 'btn btn-primary btn-input-password'
                                        ])
                                    }}
                                </div>
                            </div>
                            {{ Form::hidden('poll_id', $poll->id) }}
                            {{ Form::hidden('token', $token) }}
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
