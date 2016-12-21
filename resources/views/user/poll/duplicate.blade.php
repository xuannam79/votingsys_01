@extends('layouts.app')
@push('create-style')
    <!-- TAG INPUT: participant -->
    {!! Html::style('bower/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') !!}

    <!-- BOOTSTRAP SWITCH: setting of poll -->
    {!! Html::style('bower/bootstrap-switch/dist/css/bootstrap2/bootstrap-switch.min.css') !!}

    <!-- DATETIME PICKER: time close of poll -->
    {!! Html::style('bower/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css') !!}
@endpush
@section('title')
    {{ trans('polls.title') }}
@endsection
@section('content')
    <div class="row row-create-poll">
        <div class="loader"></div>
        <div class="hide"
             data-poll="{{ $data['jsonData'] }}"
             data-route-email="{{ url('/check-email') }}"
             data-route-link="{{ route('link-poll.store') }}"
             data-token="{{ csrf_token() }}"
             data-link-check-date="{{ url('/check-date-close-poll') }}"
             data-location-route="{{ route('location.store') }}"
             data-page="duplicate">
        </div>
        {{
           Form::open([
               'route' => 'duplicate.store',
               'method' => 'POST',
               'id' => 'form_duplicate_poll',
               'enctype' => 'multipart/form-data',
               'role' => 'form',
           ])
        }}
        <div id="duplicate_poll_wizard" class="col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2 well wrap-poll animated fadeInLeft">
            @include('layouts.error')
            @include('layouts.message')
            <div class="progress">
                <div class="progress-bar progress-bar-success progress-bar-striped bar" role="progressbar"
                     aria-valuenow="40" aria-valuemin="0" aria-valuemax="100">
                </div>
            </div>
            <div class="navbar panel">
                <div class="navbar-inner board">
                    <div class="col-lg-10 col-lg-offset-1 panel-heading board-inner panel-heading-create-poll">
                        <ul class="nav nav-tabs voting">
                            <div class="liner"></div>
                            <li>
                                <a href="#info" data-toggle="tab" data-toggle="tooltip"
                                   title="{{ trans('polls.label.step_1') }}" class="step">
                                    <span class="round-tabs one fa fa-info"></span>
                                </a>
                            </li>
                            <li>
                                <a href="#option" data-toggle="tab" data-toggle="tooltip"
                                   title="{{ trans('polls.label.step_2') }}" class="step">
                                    <span class="round-tabs two fa fa-question"></span>
                                </a>
                            </li>
                            <li>
                                <a href="#setting" data-toggle="tab" data-toggle="tooltip"
                                   title="{{ trans('polls.label.step_3') }}" class="step">
                                    <span class="round-tabs three fa fa-cog"></span>
                                </a>
                            </li>
                            <li>
                                <a href="#participant" data-toggle="tab" data-toggle="tooltip" class="step"
                                   title="{{ trans('polls.label.step_4') }}">
                                    <span class="round-tabs four fa fa-users"></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="tab-content">
                <div class="tab-pane" id="info">
                    @include('layouts.poll_info')
                </div>
                <div class="tab-pane" id="option">
                    @include('layouts.poll_options')
                </div>
                <div class="tab-pane" id="setting">
                    @include('layouts.poll_setting')
                </div>
                <div class="tab-pane" id="participant">
                    @include('layouts.poll_participant')
                </div>
                <ul class="pager wizard">
                    <li class="finish">
                        <a href="javascript:void(0);" class="btn btn-change-step btn-darkcyan btn-finish">
                            {{ trans('polls.button.finish') }}
                        </a>
                    </li>
                    <li class="previous">
                        <a href="javascript:void(0);" class="btn-change-step btn btn-darkcyan">
                            {{ trans('polls.button.previous') }}
                        </a>
                    </li>
                    <li class="next">
                        <a href="javascript:void(0);" class="btn-change-step btn btn-darkcyan">
                            {{ trans('polls.button.continue') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        {{ Form::close() }}
    </div>
@endsection
@push('create-scripts')

    <!-- ---------------------------------
            Javascript of create poll
    ---------------------------------------->

    <!-- FORM WINZARD: form step -->
    {!! Html::script('bower/twitter-bootstrap-wizard/jquery.bootstrap.wizard.js') !!}

    <!-- TAG INPUT: participant -->
    {!! Html::script('/bower/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') !!}

    <!-- DATETIME PICKER: time close of poll -->
    {!! Html::script('/bower/moment/min/moment.min.js') !!}
    {!! Html::script('/bower/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') !!}

    <!-- BOOTSTRAP SWITCH: setting of poll -->
    {!! Html::script('bower/bootstrap-switch/dist/js/bootstrap-switch.min.js') !!}

    <!-- JQUERY VALIDATE: validate info of poll -->
    {!! Html::script('bower/jquery-validation/dist/jquery.validate.min.js') !!}

    <!-- VOTE -->
    {!! Html::script('js/vote.js') !!}

    <!-- POLL -->
    {!! Html::script('js/poll.js') !!}
@endpush
