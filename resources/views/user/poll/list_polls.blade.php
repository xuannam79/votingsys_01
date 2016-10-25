 @extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">{{ trans('polls.list_polls') }}</div>
                <div class="panel-body">
                    @if ($initiatedPolls->count())
                        <h3 class="poll-history">{{ trans('polls.polls_initiated') }}</h3>
                        <table class="table table-striped">
                            <thead>
                                <th>{{ trans('polls.subject') }}</th>
                                <th>{{ trans('polls.participants') }}</th>
                                <th>{{ trans('polls.latest_activity') }}</th>
                                <th></th>
                            </thead>
                            <tbody>
                            @foreach ($initiatedPolls as $initiatedPoll)
                                <tr>
                                    <td>
                                        <a href="{{ URL::action('User\PollController@show', ['id' => $initiatedPoll->id]) }}">
                                            {{ $initiatedPoll->title }}
                                        </a>
                                    </td>
                                    <td>{{ $initiatedPoll->countParticipants() }}</td>
                                    <td>{{ $initiatedPoll->activities->sortBy('id')->last()->created_at->diffForHumans() }}</td>
                                    <td><a href="{{ URL::action('User\PollController@show', ['id' => $initiatedPoll->id]) }}">{{ trans('polls.administer') }}</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                    <br>
                    @if ($participatedPolls->count())
                        <h3 class="poll-history">{{ trans('polls.polls_participated_in') }}</h3>
                        <table class="table table-striped">
                            <thead>
                                <th>{{ trans('polls.subject') }}</th>
                                <th>{{ trans('polls.participants') }}</th>
                                <th>{{ trans('polls.latest_activity') }}</th>
                                <th></th>
                            </thead>
                            <tbody>
                            @foreach ($participatedPolls as $participatedPoll)
                                <tr>
                                    <td>
                                        <label>{{ $participatedPoll->title }}</label>
                                    </td>
                                    <td>{{ $participatedPoll->countParticipants() }}</td>
                                    <td>{{ $participatedPoll->activities->sortBy('id')->last()->created_at->diffForHumans() }}</td>
                                    @if (Gate::allows('administer', $participatedPoll))
                                        <td><a href="{{ URL::action('User\PollController@show', ['id' => $participatedPoll->id]) }}">{{ trans('polls.administer') }}</a></td>
                                    @else
                                        <td></td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                    <br>
                    @if ($closedPolls->count())
                        <h3 class="poll-history">{{ trans('polls.polls_closed') }}</h3>
                        <table class="table table-striped">
                            <thead>
                                <th>{{ trans('polls.subject') }}</th>
                                <th>{{ trans('polls.participants') }}</th>
                                <th>{{ trans('polls.latest_activity') }}</th>
                                <th></th>
                            </thead>
                            <tbody>
                            @foreach ($closedPolls as $closedPoll)
                                <tr>
                                    <td>{{ $closedPoll->title }}</td>
                                    <td>{{ $closedPoll->countParticipants() }}</td>
                                    <td>{{ $closedPoll->activities->sortBy('id')->last()->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
