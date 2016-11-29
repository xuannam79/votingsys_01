@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{ trans('label.password') }}</div>
                <div class="panel-body">
                    @include('message')
                    @include('errors.errors')
                    <div class="col-lg-12">
                        <div class="modal-dialog-password">
                            <div class="modal-content-password">
                                <div class="modal-bodymodal-dialog-password">
                                    <fieldset class="required-password">
                                        <div class="form-group">
                                            <div class="col-md-10 col-md-offset-1">
                                                {{ Form::open(['route' => 'set-password.store']) }}
                                                <div class="col-md-10">
                                                    <div class="input-group">
                                                        <span class="input-group-addon" id="basic-addon1"><i class="fa fa-key" aria-hidden="true"></i></span>
                                                        {{
                                                            Form::password('password', [
                                                                'id' => 'password',
                                                                'class' => 'form-control',
                                                                'placeholder' => trans('polls.placeholder.password_poll'),
                                                                'autofocus' => 'true'
                                                            ])
                                                        }}
                                                    </div>
                                                    {{ Form::hidden('poll_id', $poll->id) }}
                                                    {{ Form::hidden('token', $token) }}
                                                </div>
                                                <div class="col-md-2">
                                                    {{ Form::button('<i class="glyphicon glyphicon-ok"></i>'
                                                        . ' ' . trans('polls.check'), [
                                                            'type' => 'submit',
                                                            'class' => 'btn btn-primary btn-input-password'
                                                        ])
                                                    }}
                                                </div>
                                                {{ Form::close() }}
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
