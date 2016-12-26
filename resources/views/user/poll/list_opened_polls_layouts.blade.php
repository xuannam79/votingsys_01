<table class="table table-bordered">
    <thead>
        <th>{{ trans('polls.subject') }}</th>
        <th>{{ trans('polls.participants') }}</th>
        <th>{{ trans('polls.latest_activity') }}</th>
        <th></th>
    </thead>
    <tbody>
        <!--  Sort activities of poll by created_at -->
        @foreach ($polls->sortBy(function($poll)
            {
              return $poll->activities->sortBy('id')->last()->created_at;
            })->reverse() as $poll)
            @if ($poll->getUserLink())
                <tr>
                    <td>
                        <a href="{{ $poll->getUserLink() }}">
                            {{ $poll->title }}
                        </a>
                    </td>
                    <td>{{ $poll->countParticipants() }}</td>
                    @if ($poll->activities->count())
                        <td>{{ $poll->activities->sortBy('id')->last()->created_at->diffForHumans() }}</td>
                    @else
                        <td></td>
                    @endif
                    @if (Gate::allows('ownerPoll', $poll))
                        <td>
                            <a class="btn btn-success btn-block fa fa-external-link" href="{{ URL::action('User\PollController@edit', ['id' => $poll->id]) }}">
                                {{ trans('polls.reopen_poll') }}
                            </a>
                        </td>
                    @endif
                </tr>
            @endif
        @endforeach
    </tbody>
</table>
