@extends('layouts.app')
@section('title')
    {{ trans('polls.title') }}
@endsection
@section('content')
    <div class="hide"
         data-poll="{{ $data["jsonData"] }}"
         data-page="edit"
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
        <div id="edit_poll_wizard" class="col-lg-10 col-lg-offset-1
                                         col-md-10 col-md-offset-1
                                         col-sm-10 col-sm-offset-1
                                         well wrap-poll">
            <div class="navbar panel">
                <div class="navbar-inner">
                    <div class="col-lg-6 col-lg-offset-3
                                col-md-6 col-md-offset-3
                                col-sm-8 col-sm-offset-2
                                col-xs-8 col-xs-offset-2
                                panel-heading panel-test {{ (Session::get('locale') == 'ja' && ! $countParticipantsVoted) ? 'panel-jp-manage-poll' : '' }}">
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
                <div class="alert alert-info alert-dismissable">
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
