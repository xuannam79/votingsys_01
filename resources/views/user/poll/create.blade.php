@extends('layouts.app')
@section('title')
    {{ trans('polls.title') }}
@endsection
@section('content')
    <div class="row" style="margin-bottom: 100px">
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
                         aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="background: darkcyan">
                    </div>
                </div>
                <div class="navbar panel">
                    <div class="navbar-inner board">
                        <div class="col-lg-10 col-lg-offset-1 panel-heading board-inner" style="padding: 5px">
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
                        <div class="panel" style="margin: 0; border-radius: 0;border-color: darkcyan">
                            <div class="panel-heading" style="background: darkcyan; border-color: darkcyan; border-radius: 0; color: white">
                                {{ trans('polls.label.step_1') }}
                            </div>
                            <div class="panel-body">
                                @include('layouts.poll_info')
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="option">
                        <div class="panel" style="border-color: darkcyan; border-radius: 0">
                            <div class="panel-heading" style="background: darkcyan; color: white; border-radius: 0">
                                {{ trans('polls.label.step_2') }}
                            </div>
                            <div class="panel-body">
                                @include('layouts.poll_options')
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="setting">
                        <div class="panel" style="border-color: darkcyan; border-radius: 0">
                            <div class="panel-heading" style="background: darkcyan; color: white; border-radius: 0">
                                {{ trans('polls.label.step_3') }}
                            </div>
                            <div class="panel-body">
                                @include('layouts.poll_setting')
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="participant">
                        <div class="panel" style="border-color: darkcyan; border-radius: 0">
                            <div class="panel-heading" style="background: darkcyan; color: white; border-radius: 0">
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

    <div class="row" style="background: #f3f4f4; min-height: 300px; padding-top: 20px">
        <h2 style="text-align: center; margin-bottom: 50px">{{ trans('label.feature.name') }}</h2>
        <div class="col-lg-2 animatedParent">
            <img class="animated growIn slowest" src="{{ asset('uploads/images/Fpoll-vote.jpg') }}" style="display: block; margin: 0 auto; width: 100px; height: 100px; border-radius: 50%">
            <p class="animated fadeInLeft" style="text-align: center">{{ trans('label.feature.vote') }}</p>
        </div>
        <div class="col-lg-2 animatedParent">
            <img class="animated growIn slowest" src="{{ asset('uploads/images/Fpoll-chart.png') }}" style="display: block; margin: 0 auto; width: 100px; height: 100px; border-radius: 50%">
            <p class="animated fadeInUp" style="text-align: center">{{ trans('label.feature.chart') }}</p>
        </div>
        <div class="col-lg-2 animatedParent">
            <img class="animated growIn slowest" src="{{ asset('uploads/images/Fpoll-security.jpg') }}" style="display: block; margin: 0 auto; width: 100px; height: 100px; border-radius: 50%">
            <p class="animated fadeInUp" style="text-align: center">{{ trans('label.feature.security') }}</p>
        </div>
        <div class="col-lg-2 animatedParent">
            <img class="animated growIn slowest" src="{{ asset('uploads/images/Fpoll-export.png') }}" style="display: block; margin: 0 auto; width: 100px; height: 100px; border-radius: 50%">
            <p class="animated fadeInRight" style="text-align: center">{{ trans('label.feature.export') }}</p>
        </div>
        <div class="col-lg-2 animatedParent">
            <img class="animated growIn slowest" src="{{ asset('uploads/images/Fpoll-responsive.jpg') }}" style="display: block; margin: 0 auto; width: 100px; height: 100px; border-radius: 50%">
            <p class="animated fadeInRight" style="text-align: center">{{ trans('label.feature.responsive') }}</p>
        </div>
        <div class="col-lg-2 animatedParent">
            <img class="animated growIn slowest" src="{{ asset('uploads/images/Fpoll-like-share.jpg') }}" style="display: block; margin: 0 auto; width: 100px; height: 100px; border-radius: 50%">
            <p class="animated fadeInRight" style="text-align: center">{{ trans('label.feature.share') }}</p>
        </div>
    </div>
@endsection
