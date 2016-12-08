@extends('layouts.app')
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
                        <a href="#" class="btn btn-change-step btn-darkcyan btn-finish">
                            {{ trans('polls.button.finish') }}
                        </a>
                    </li>
                    <li class="previous">
                        <a href="#" class="btn-change-step btn btn-darkcyan">
                            {{ trans('polls.button.previous') }}
                        </a>
                    </li>
                    <li class="next">
                        <a href="#" class="btn-change-step btn btn-darkcyan">
                            {{ trans('polls.button.continue') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        {{ Form::close() }}
    </div>
@endsection
