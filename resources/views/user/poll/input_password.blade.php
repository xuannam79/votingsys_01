@extends('layouts.app')
@push('detail-style')

<!-- ---------------------------------
            Style of detail poll
    ---------------------------------------->

<!-- DATETIME PICKER: time close of poll -->
{!! Html::style('bower/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css') !!}

<!-- GOOGLE CHART-->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<!-- SOCKET IO -->
{!! Html::script('bower/socket.io-client/dist/socket.io.min.js') !!}
@endpush
@section('content')
    <div class="hide_vote_socket"
         data-host="{{ config('app.key_program.socket_host') }}"
         data-port="{{ config('app.key_program.socket_port') }}">
    </div>
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
@push('detail-scripts')

    <!-- ---------------------------------
        Javascript of detail poll
    ---------------------------------------->
    <!-- FORM WINZARD: form step -->
    {!! Html::script('bower/twitter-bootstrap-wizard/jquery.bootstrap.wizard.js') !!}

    <!-- DATETIME PICKER: time close of poll -->
    {!! Html::script('/bower/moment/min/moment.min.js') !!}
    {!! Html::script('/bower/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') !!}

    <!-- SOCKET IO -->
    {!! Html::script('bower/socket.io-client/dist/socket.io.min.js') !!}

    <!-- COMMENT -->
    {!! Html::script('js/comment.js') !!}

    <!-- VOTE -->
    {!! Html::script('js/vote.js') !!}

    <!-- VOTE SOCKET-->
    {!! Html::script('js/voteSocket.js') !!}

    <!-- SOCIAL: like, share -->
    {!! Html::script('js/shareSocial.js') !!}

    <!-- POLL -->
    {!! Html::script('js/poll.js') !!}

    <!-- HIGHCHART-->
    {!! Html::script('bower/highcharts/highcharts.js') !!}
    {!! Html::script('bower/highcharts/highcharts-3d.js') !!}

    <!-- CHART -->
    {!! Html::script('js/chart.js') !!}
@endpush
