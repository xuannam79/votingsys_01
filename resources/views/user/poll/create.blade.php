@extends('layouts.app')
@section('title')
    {{ trans('polls.title') }}
@endsection
@section('content')

    <!-- Create poll -->
    <div class="row row-create-poll">
        <div class="loader"></div>
        <div class="hide"
             data-poll="{{ $data['jsonData'] }}"
             data-route-email="{{ url('/check-email') }}"
             data-route-link="{{ route('link-poll.store') }}"
             data-token="{{ csrf_token() }}"
             data-link-check-date="{{ url('/check-date-close-poll') }}">
        </div>
        {{
           Form::open([
               'route' => 'user-poll.store',
               'method' => 'POST',
               'id' => 'form_create_poll',
               'enctype' => 'multipart/form-data',
               'role' => 'form',
           ])
        }}
            <div id="create_poll_wizard" class="col-lg-8 col-lg-offset-2 well wrap-poll animated fadeInLeft">
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
                                    <a href="#info" data-toggle="tab" data-toggle="tooltip"  title="{{ trans('polls.label.step_1') }}" class="step">
                                        <span class="round-tabs one fa fa-info">
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#option" data-toggle="tab" data-toggle="tooltip" title="{{ trans('polls.label.step_2') }}" class="step">
                                        <span class="round-tabs two fa fa-question">
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#setting" data-toggle="tab" data-toggle="tooltip" title="{{ trans('polls.label.step_3') }}" class="step">
                                        <span class="round-tabs three fa fa-cog">
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#participant" data-toggle="tab" data-toggle="tooltip" class="step" title="{{ trans('polls.label.step_4') }}">
                                        <span class="round-tabs four fa fa-users">
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="tab-content">
                    <div class="tab-pane" id="info">
                        <div class="panel panel-darkcyan">
                            <div class="panel-heading panel-heading-darkcyan">
                                {{ trans('polls.label.step_1') }}
                            </div>
                            <div class="panel-body">
                                @include('layouts.poll_info')
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="option">
                        <div class="panel panel-darkcyan">
                            <div class="panel-heading panel-heading-darkcyan">
                                {{ trans('polls.label.step_2') }}
                            </div>
                            <div class="panel-body">
                                @include('layouts.poll_options')
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="setting">
                        <div class="panel panel-darkcyan">
                            <div class="panel-heading panel-heading-darkcyan">
                                {{ trans('polls.label.step_3') }}
                            </div>
                            <div class="panel-body">
                                @include('layouts.poll_setting')
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="participant">
                        <div class="panel panel-darkcyan">
                            <div class="panel-heading panel-heading-darkcyan">
                                {{ trans('polls.label.step_4') }}
                            </div>
                            <div class="panel-body">
                                @include('layouts.poll_participant')
                            </div>
                        </div>
                    </div>
                    <ul class="pager wizard">
                        <li class="finish"><a href="#" class="btn btn-change-step btn-darkcyan btn-finish">{{ trans('polls.button.finish') }}</a></li>
                        <li class="previous"><a href="#" class="btn-change-step btn btn-darkcyan">{{ trans('polls.button.previous') }}</a></li>
                        <li class="next"><a href="#" class="btn-change-step btn btn-darkcyan">{{ trans('polls.button.continue') }}</a></li>
                    </ul>
                </div>
            </div>
        {{ Form::close() }}
    </div>

    <!-- Feature -->
    <div class="row feature">
        <h2>{{ trans('label.feature.name') }}</h2>
        <div class="col-lg-2 animatedParent">
            <img class="animated fadeInUp slowest feature-img" src="{{ asset('uploads/images/Fpoll-vote.jpg') }}">
            <p class="animated feature-text">{{ trans('label.feature.vote') }}</p>
        </div>
        <div class="col-lg-2 animatedParent">
            <img class="animated fadeInUp slowest feature-img" src="{{ asset('uploads/images/Fpoll-chart.png') }}">
            <p class="animated feature-text">{{ trans('label.feature.chart') }}</p>
        </div>
        <div class="col-lg-2 animatedParent">
            <img class="animated fadeInUp slowest feature-img" src="{{ asset('uploads/images/Fpoll-security.jpg') }}">
            <p class="animated feature-text">{{ trans('label.feature.security') }}</p>
        </div>
        <div class="col-lg-2 animatedParent">
            <img class="animated fadeInUp slowest feature-img" src="{{ asset('uploads/images/Fpoll-export.png') }}">
            <p class="animated feature-text">{{ trans('label.feature.export') }}</p>
        </div>
        <div class="col-lg-2 animatedParent">
            <img class="animated fadeInUp slowest feature-img" src="{{ asset('uploads/images/Fpoll-responsive.jpg') }}">
            <p class="animated feature-text">{{ trans('label.feature.responsive') }}</p>
        </div>
        <div class="col-lg-2 animatedParent">
            <img class="animated fadeInUp slowest feature-img" src="{{ asset('uploads/images/Fpoll-like-share.jpg') }}">
            <p class="animated feature-text">{{ trans('label.feature.share') }}</p>
        </div>
    </div>
@endsection
