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
                            @if (! $totalVote)
                                <li><a href="#option" data-toggle="tab">{{ trans('polls.label.step_2') }}</a></li>
                            @endif
                            <li><a href="#setting" data-toggle="tab">{{ trans('polls.label.step_3') }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            @include('layouts.error')
            @include('layouts.message')
            @if ($totalVote)
                <div class="alert alert-info">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong><span class="glyphicon glyphicon-info-sign"></span></strong> {{ trans('polls.poll_voted') }}
                </div>
            @endif
            <div class="tab-content">
                <div class="tab-pane" id="info">
                    @include('layouts.poll_info')
                </div>
                @if (! $totalVote)
                    <div class="tab-pane" id="option">
                        @include('layouts.poll_options')
                    </div>
                @endif
                <div class="tab-pane" id="setting">
                    @include('layouts.poll_setting')
                </div>
            </div>
        </div>
    </div>
@endsection
