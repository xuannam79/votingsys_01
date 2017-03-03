<table class="table table-hover table-responsive">
    <thead>
        <tr>
            <th>{{ trans('polls.no') }}</th>
            <th>{{ trans('polls.label.option') }}</th>
            <th>{{ trans('polls.number_vote') }}</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @php
            $maxVote = max(array_column($dataTableResult, 'numberOfVote'));
            $voted = true;
        @endphp
        @foreach ($dataTableResult as $key => $data)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td class="{{ ($isHaveImages) ? 'td-poll-result' : '' }}">
                    @if ($isHaveImages)
                        <img src="{{ asset($data['image']) }}">
                    @endif
                    <p>{{ $data['name'] }}</p>
                </td>
                <td>
                    <div class="voters voters-td clearfix">
                        @foreach (array_slice($data['listVoter'], 0, config('settings.limit_voters_option')) as $voter)
                            <div class="voter-avatar" data-toggle="tooltip" title="{{ $voter['name'] }}">
                                <img src="{{ $voter['avatar'] }}">
                            </div>
                        @endforeach
                        @if ($data['numberOfVote'] > config('settings.limit_voters_option'))
                            <div class="voter-avatar">
                                <div class="hidden-counter"
                                    data-url-modal-voter="{{ action('User\VoteController@getModalOptionVoters', $data['option_id']) }}">
                                    <span>{{ $data['numberOfVote'] - config('settings.limit_voters_option') }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </td>
                <td>
                    @if ($maxVote == $data['numberOfVote'] && $voted)
                        @php
                            $voted = false;
                        @endphp
                        <img src="{{ asset(config('settings.option.path_trophy')) }}" class="trophy">
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
