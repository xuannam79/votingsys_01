@extends('layouts.app')
@section('title')
    {{ trans('polls.title') }}
@endsection
@section('content')
    <div class="hide"
         data-poll="{{ $data["jsonData"] }}"
         data-action="edit"
         data-setting-edit="{{ json_encode($setting) }}"
         data-route-link="{{ route('link-poll.store') }}"
         data-token="{{ csrf_token() }}"
        data-route-limit="{{ route('limit.store') }}"
        data-poll-return="{{ json_encode($poll) }}"
        data-total-vote="{{ $totalVote }}"
        data-link-poll="{{ $poll->getUserLink() }}">
    </div>
    <div class="loader"></div>
    <div class="container">
        <div id="edit_poll_wizard" class="col-lg-10 col-lg-offset-1 well wrap-poll">
            <div class="navbar panel">
                <div class="navbar-inner">
                    <div class="col-md-12 col-lg-4 col-lg-offset-4 panel-heading">
                        <ul>
                            <li><a href="#info" data-toggle="tab">{{ trans('polls.label.step_1') }}</a></li>
                            <li><a href="#setting" data-toggle="tab">{{ trans('polls.label.step_3') }}</a></li>
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
                <div class="tab-pane" id="setting">
                    @include('layouts.poll_setting')
                </div>
            </div>
        </div>
    </div>
@endsection
