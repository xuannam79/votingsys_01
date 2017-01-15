@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
        <div class="loader"></div>
            <div class="hide-validate" data-error-avatar="{{ trans('validation.image') }}"></div>
            <div class="col-lg-4 col-lg-offset-4
                        col-md-4 col-md-offset-4
                        col-sm-6 col-sm-offset-3
                        col-xs-8 col-xs-offset-2
                        col-xs-register
                        animated fadeInDown register">
                <div class="panel panel-default panel-darkcyan-profile">
                    <div class="panel-heading panel-heading-darkcyan">{{ trans('label.feedback') }}</div>
                    <div class="panel-body">
                        @include('errors.errors')
                        @include('noty.message')
                        {{
                            Form::open([
                                'action' => 'FeedbackController@sendFeedback',
                                'class' => 'form-horizontal',
                                'id' => 'form-feedback',
                            ])
                        }}
                            <div class="form-group">
                                <div class="input-group
                                            col-lg-10 col-lg-offset-1
                                            col-md-10 col-md-offset-1
                                            col-sm-10 col-sm-offset-1
                                            col-xs-10 col-xs-offset-1
                                            col-xs-register">
                                    <span class="input-group-addon" id="basic-addon1">
                                        <i class="fa fa-user" aria-hidden="true"></i>
                                    </span>
                                    {{
                                        Form::text('name', auth()->check() ? auth()->user()->name : null, [
                                            'id' => 'name',
                                            'class' => 'form-control',
                                            'placeholder' => trans('user.register.placeholder.name')
                                        ])
                                    }}
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="input-group
                                            col-lg-10 col-lg-offset-1
                                            col-md-10 col-md-offset-1
                                            col-sm-10 col-sm-offset-1
                                            col-xs-10 col-xs-offset-1
                                            col-xs-register">
                                    <span class="input-group-addon" id="basic-addon1">
                                        <i class="fa fa-envelope" aria-hidden="true"></i>
                                    </span>
                                    {{
                                        Form::email('email', auth()->check() ? auth()->user()->email : null, [
                                            'id' => 'email',
                                            'class' => 'form-control',
                                            'placeholder' => trans('user.register.placeholder.email')
                                        ])
                                    }}
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="input-group
                                            col-lg-10 col-lg-offset-1
                                            col-md-10 col-md-offset-1
                                            col-sm-10 col-sm-offset-1
                                            col-xs-10 col-xs-offset-1
                                            col-xs-register">
                                    {{
                                        Form::textarea('feedback', null, [
                                            'id' => 'feedback',
                                            'class' => 'form-control',
                                            'placeholder' => trans('user.register.placeholder.feedback')
                                        ])
                                    }}
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 col-xs-register">
                                    {{
                                        Form::button('<i class="fa fa-envelope-open-o"></i> ' . trans('label.send_feedback'), [
                                            'id' => 'btn-feedback',
                                            'class' => 'btn btn-success btn-block btn-register btn-darkcyan',
                                            'type' => 'submit',
                                        ])
                                    }}
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
