@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">{{ trans('polls.list_polls') }}</div>
                <div class="panel-body">
                    <div class="hide" data-route-initiated="{{ url('load-initiated-poll') }}"
                        data-route-participanted="{{ url('load-participanted-in-poll') }}"
                        data-route-closed="{{ url('load-closed-poll') }}"
                        data-message="{{ trans('polls.load_latest_polls') }}">
                    </div>
                    <h3 class="poll-history">
                        {{ trans('polls.polls_initiated') }}
                    </h3>
                    <button id="list-all-polls-initiated" class="btn btn-primary btn-initiated">
                        {{ trans('polls.list_all_polls') }}
                    </button>
                    <br>
                    <span class="message-initiated-poll lastest-poll-message"></span>
                    <div class="polls-initiated">
                        @if ($initiatedPolls->count())
                            @include('user.poll.list_polls_layouts', ['polls' => $initiatedPolls])
                        @endif
                    </div>
                    <br>
                    <h3 class="poll-history">
                        {{ trans('polls.polls_participated_in') }}
                    </h3>
                    <button id="list-all-polls-participated" class="btn btn-primary btn-participanted-in">
                        {{ trans('polls.list_all_polls') }}
                    </button>
                    <br>
                    <span class="message-participanted-in-poll lastest-poll-message"></span>
                    <div class="polls-participanted-in">
                        @if ($participatedPolls->count())
                            @include('user.poll.list_polls_layouts', ['polls' => $participatedPolls])
                        @endif
                    </div>
                    <br>
                    <h3 class="poll-history">
                        {{ trans('polls.polls_closed') }}
                    </h3>
                    <button id="list-all-polls-participated" class="btn btn-primary btn-closed">
                        {{ trans('polls.list_all_polls') }}
                    </button>
                    <br>
                    <span class="message-closed-poll lastest-poll-message"></span>
                    <div class="polls-closed">
                        @if ($closedPolls->count())
                            @include('user.poll.list_opened_polls_layouts', ['polls' => $closedPolls])
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
