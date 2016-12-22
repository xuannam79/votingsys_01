@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
    <div class="loader"></div>
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default panel-darkcyan-profile">
                <div class="panel-heading panel-heading-darkcyan">{{ trans('polls.list_polls') }}</div>
                <div class="panel-body">
                    <div class="hide" data-route-initiated="{{ url('load-initiated-poll') }}"
                        data-route-participanted="{{ url('load-participanted-in-poll') }}"
                        data-route-closed="{{ url('load-closed-poll') }}"
                        data-message="{{ trans('polls.load_latest_polls') }}">
                    </div>

                    <ul class="nav nav-pills">
                        <li class="active">
                            <a data-toggle="pill" href="#home">
                                {{ trans('polls.polls_initiated') }}
                            </a>
                        </li>
                        <li>
                            <a data-toggle="pill" href="#menu1">
                                {{ trans('polls.polls_participated_in') }}
                            </a>
                        </li>
                        <li>
                            <a data-toggle="pill" href="#menu2">
                                {{ trans('polls.polls_closed') }}
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div id="home" class="tab-pane fade in active">
                            <div class="well list-poll-history">
                                <button id="list-all-polls-initiated" class="btn btn-primary btn-initiated all-poll-user btn-darkcyan">
                                    {{ trans('polls.list_all_polls') }}
                                </button>
                                <p class="message-initiated-poll lastest-poll-message"></p>
                                <div class="polls-initiated">
                                    @if ($initiatedPolls->count())
                                        @include('user.poll.list_polls_layouts', ['polls' => $initiatedPolls])
                                    @else
                                        <div class="alert alert-info">
                                            {{ trans('polls.message.no_poll_create') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div id="menu1" class="tab-pane fade">
                            <div class="well list-poll-history">
                                <button id="list-all-polls-participated" class="btn btn-primary btn-participanted-in all-poll-user btn-darkcyan">
                                    {{ trans('polls.list_all_polls') }}
                                </button>
                                <p class="message-participanted-in-poll lastest-poll-message"></p>
                                <div class="polls-participanted-in">
                                    @if ($participatedPolls->count())
                                        @include('user.poll.list_polls_layouts', ['polls' => $participatedPolls])
                                    @else
                                        <div class="alert alert-info">
                                            {{ trans('polls.message.no_poll_participant') }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>
                        <div id="menu2" class="tab-pane fade">
                            <div class="well list-poll-history">
                                <button id="list-all-polls-participated" class="btn btn-primary btn-closed all-poll-user btn-darkcyan">
                                    {{ trans('polls.list_all_polls') }}
                                </button>
                                <p class="message-closed-poll lastest-poll-message"></p>
                                <div class="polls-closed">
                                    @if ($closedPolls->count())
                                        @include('user.poll.list_opened_polls_layouts', ['polls' => $closedPolls])
                                    @else
                                        <div class="alert alert-info">
                                            {{ trans('polls.message.no_poll_close') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('list-poll-scripts')

    <!-- ---------------------------------
        Javascript of list poll
    ---------------------------------------->
    {!! Html::script('js/listPolls.js') !!}
@endpush
