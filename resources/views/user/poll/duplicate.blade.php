@extends('layouts.app')
@section('title')
    {{ trans('polls.title') }}
@endsection
@section('content')
    <div class="col-lg-12">
        <div class="loader"></div>
        <div class="hide"
             data-poll="{{ $data['jsonData'] }}"
             data-route-email="{{ url('/check-email') }}"
             data-route-link="{{ route('link-poll.store') }}"
             data-token="{{ csrf_token() }}">
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
        <div id="duplicate_poll_wizard" class="col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2 well wrap-poll">
            <div class="navbar panel">
                <div class="navbar-inner">
                    <div class="col-md-12 col-lg-10 col-lg-offset-1 panel-heading">
                        <ul>
                            <li><a href="#info" data-toggle="tab">{{ trans('polls.label.step_1') }}</a></li>
                            <li><a href="#option" data-toggle="tab">{{ trans('polls.label.step_2') }}</a></li>
                            <li><a href="#setting" data-toggle="tab">{{ trans('polls.label.step_3') }}</a></li>
                            <li><a href="#participant" data-toggle="tab">{{ trans('polls.label.step_4') }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            @include('layouts.error')
            @include('layouts.message')
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
                    <li class="previous"><a href="#" class="btn-change-step btn btn-primary">Previous</a></li>
                    <li class="next"><a href="#" class="btn-change-step btn btn-primary">Next</a></li>
                    <li class="finish"><a href="#" class="btn btn-change-step btn-primary btn-finish">Finish</a></li>
                </ul>
            </div>
        </div>
        {{ Form::close() }}
    </div>
@endsection
