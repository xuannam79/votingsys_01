@php
    $isHideResult = $settingsPoll[config('settings.setting.hide_result')]['isHave'];
    $isTimeOut = $poll->isTimeOut();
@endphp
@foreach ($poll->options as $option)
    <li class="list-group-item parent-vote li-parent-vote perform-option" onclick="voted('{{ $option->id }}', 'horizontal')">
        @if (!$isLimit && !$poll->isClosed() && !$isTimeOut)
            @if ($poll->multiple == trans('polls.label.multiple_choice'))
                <div class="checkbox checkbox-primary">
                    {!!
                        Form::checkbox('option[]', $option->id, false, [
                            'onClick' => 'voted("' . $option->id  . '", "horizontal")',
                            'class' => ($isHaveImages) ? 'poll-option-detail' : 'poll-option-detail-not-image',
                            'id' => 'horizontal-' . $option->id
                        ])
                    !!}
            @else
                <div class="radio radio-primary">
                    {!!
                        Form::radio('option[]', $option->id, false, [
                            'onClick' => 'voted("' . $option->id  . '", "horizontal")',
                            'class' => ($isHaveImages) ? 'poll-option-detail' : 'poll-option-detail-not-image',
                            'id' => 'horizontal-' . $option->id
                        ])
                    !!}
            @endif
                <label>{{ $option->name ? $option->name : '' }}</label>
                <br>
            </div>
            @if ($isHaveImages)
                <!--START: Win-Frame Add Image -->
                <div class="box-media-image-option image-option-detail">
                    <a class="media-image" href="javascript:void(0)" onclick="showModelImage('{{ $option->showImage() }}')">
                        <div class="image-frame">
                            <div class="image-ratio">
                                <img src="{{ $option->showImage() }}" class="thumbOption" />
                            </div>
                            <span class="cz-label label-new">
                                {{ trans('polls.label_for.option_image') }}
                            </span>
                        </div>
                    </a>
                </div>
                <!--END: Win-Frame Add Image -->
            @endif
        @endif
        @if (!$isHideResult || Gate::allows('administer', $poll))
            <div class="voters clearfix result-poll">
                @foreach(array_slice($option->listVoter(), 0, config('settings.limit_voters_option')) as $voter)
                    <div class="voter-avatar" data-toggle="tooltip" title="{{ $voter['name'] }}">
                        <img src="{{ $voter['avatar'] }}">
                    </div>
                @endforeach
                @if($option->countVotes() > config('settings.limit_voters_option'))
                    <div class="voter-avatar">
                        <div class="hidden-counter"
                            data-url-modal-voter="{{ action('User\VoteController@getModalOptionVoters', $option->id) }}">
                            <span>+{{ $option->countVotes() - config('settings.limit_voters_option') }}</span>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </li>
@endforeach
