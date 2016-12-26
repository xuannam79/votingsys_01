<table class="table table-bordered">
    <thead>
        <th>{{ trans('polls.subject') }}</th>
        <th>{{ trans('polls.participants') }}</th>
        <th>{{ trans('polls.latest_activity') }}</th>
        <th>{{ trans('polls.action') }}</th>
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
                        <td>{{ trans('polls.not_activity') }}</td>
                    @endif
                    @if (Gate::allows('ownerPoll', $poll))
                        <td>
                            <a href="{{ $poll->getAdminLink() }}">
                                {{ trans('polls.link') }}
                            </a>
                        </td>
                    @else
                        <td></td>
                    @endif
                </tr>
            @endif
        @endforeach
    </tbody>
</table>
