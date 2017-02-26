@php
    $isHideResult = $settingsPoll[config('settings.setting.hide_result')]['isHave'];
    $isTimeOut = $poll->isTimeOut();
@endphp
@foreach ($poll->options as $option)
    <li class="list-group-item parent-vote li-parent-vote" onclick="voted('{{ $option->id }}', 'horizontal')">
        @if (!$isHideResult || Gate::allows('administer', $poll))
            <span id="id1{{ $option->id }}" class="badge float-xs-right result-poll">{{ $option->countVotes() }}</span>
        @endif

        @if (!$isLimit && !$poll->isClosed() && !$isTimeOut)
            @if ($poll->multiple == trans('polls.label.multiple_choice'))
                {!!
                    Form::checkbox('option[]', $option->id, false, [
                        'onClick' => 'voted("' . $option->id  . '", "horizontal")',
                        'class' => ($isHaveImages) ? 'poll-option-detail' : 'poll-option-detail-not-image',
                        'id' => 'horizontal-' . $option->id
                    ])
                !!}
            @else
                {!!
                    Form::radio('option[]', $option->id, false, [
                        'onClick' => 'voted("' . $option->id  . '", "horizontal")',
                        'class' => ($isHaveImages) ? 'poll-option-detail' : 'poll-option-detail-not-image',
                        'id' => 'horizontal-' . $option->id
                    ])
                !!}
            @endif
        @endif
        <div class="option-name">
            <p>
                @if($isHaveImages)
                    <img src="{{ $option->showImage() }}" class="image-option-vote" onclick="showModelImage('{{ $option->showImage() }}')">
                @endif
                <span class="{{ ($isHaveImages) ? 'content-option-vote' :  'content-option-not-image'}}">{{ $option->name ? $option->name : " " }}</span>
            </p>
        </div>
        <div class="voters clearfix">
            @foreach(array_slice($option->listVoter(), 0, config('settings.limit_voters_option')) as $voter)
                <div class="voter-avatar" data-toggle="tooltip" title="{{ $voter['name'] }}">
                    <img src="{{ $voter['avatar'] }}">
                </div>
            @endforeach
            @if($option->countVotes() > config('settings.limit_voters_option'))
                <div class="voter-avatar">
                    <div class="hidden-counter"
                        data-url-modal-voter="{{ action('User\VoteController@getModalOptionVoters', $option->id) }}">
                        <span>{{ $option->countVotes() > config('settings.limit_voters_option') }}</span>
                    </div>
                </div>
            @endif
        </div>
    </li>
@endforeach
