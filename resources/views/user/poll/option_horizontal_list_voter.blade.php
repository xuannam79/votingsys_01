@php
    $isHideResult = $settingsPoll[config('settings.setting.hide_result')]['isHave'];
    $isTimeOut = $poll->isTimeOut();
@endphp
<div class="voters clearfix result-poll {{ isset($hideChoose) ? 'voters-fix' : '' }} result-poll-mobile">
    @if (!$isHideResult || Gate::allows('administer', $poll))
        <div class="voters clearfix result-poll {{ isset($hideChoose) ? 'voters-fix' : '' }} result-poll-mobile">
            @if ($numberOfVote)
                <div class="voter-avatar voter-avatar-mobile">
                    <span class="hidden-counter">{{ $option->countVotes() }}</span>
                </div>
            @else
                @foreach (array_slice($listVoter[$option->id], 0, config('settings.limit_voters_option')) as $voter)
                    <div class="voter-avatar voter-avatar-mobile" data-toggle="tooltip"
                        data-placement="{{ $loop->parent->last ? 'top' : 'bottom'}}"
                        title="{{ $voter['name'] }}">
                        <img src="{{ $voter['avatar'] }}">
                    </div>
                @endforeach

                @if ($option->countVotes() > config('settings.limit_voters_option'))
                    <div class="voter-avatar voter-avatar-mobile">
                        <div class="hidden-counter"
                            data-url-modal-voter="{{ action('User\VoteController@getModalOptionVoters', $option->id) }}">
                            <span>+{{ $option->countVotes() - config('settings.limit_voters_option') }}</span>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    @endif
</div>
