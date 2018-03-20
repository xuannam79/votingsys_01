@php
    $isHideResult = $settingsPoll[config('settings.setting.hide_result')]['isHave'];
    $isTimeOut = $poll->isTimeOut();
@endphp
@foreach ($poll->options as $option)
    <li class="list-group-item parent-vote li-parent-vote perform-option clearfix list-option-{{ $option->id }} {{ $poll->haveDetail() || $isHaveImages ? 'is-description' : 'not-description' }}"
        onclick="voted('{{ $option->id }}', 'horizontal')">
        <div class="option-info pull-left">
            @if (!$isLimit && !$poll->isClosed() && !$isTimeOut)
                @if ($poll->multiple == trans('polls.label.multiple_choice'))
                    <div class="checkbox checkbox-primary checkbox-primary-mobile">
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
                    <label class="content-option-choose">
                        <span>
                            @if ($isHaveImages)
                                <a class="media-image pick-media-image-mobile none-in-laptop" href="javascript:void(0)">
                                    <div class="image-frame image-frame-mobile">
                                        <div class="image-ratio">
                                            <img src="{{ $option->showImage() }}" class="thumbOption image-option-choose" />
                                        </div>
                                        <span class="cz-label label-new">
                                            {{ trans('polls.label_for.option_image') }}
                                        </span>
                                    </div>
                                </a>
                            @endif
                            {{ $option->name ? $option->name : '' }}
                        </span>
                    </label>
                    <br>
                </div>
            @else
                @php
                    $hideChoose = true;
                @endphp
                <p class="content-option-choose">{{ $option->name ? $option->name : '' }}</p>
            @endif
        </div>
        <div class="voters-info pull-right voters-info-mobile">
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

        @if ($option->description)
            @php
                $haveShowMore = $option->paragraphTimes()
            @endphp
            <div class="clearfix none-in-laptop"></div>
            <div class="des-child-option des-child-option-mobile none-in-laptop">
                <span class="item-description-icon">
                    <i class="fa fa-quote-right" aria-hidden="true"></i>
                </span>
                <div class="description-body {{ $haveShowMore ? 'show-more show-more-mobile' : ''}}">
                    {!! $option->description !!}
                </div>
                @if ($haveShowMore)
                    <button type="button" class="btn-show-more btn-show-more-mobile btn-show-more-mobile-js">
                        <span>{{ trans('polls.message_client.show_more') }}</span>
                    </button>
                @endif
            </div>
        @endif
    </li>
    @if ($isHaveImages)
        <!--START: Win-Frame Add Image -->
        <div class="box-media-image-option
            image-option-detail
            {{ isset($hideChoose) ? 'image-option-detail-fix' : '' }}
            none-tag-mobile">
            <a class="media-image pick-media-image" href="javascript:void(0)">
                <div class="image-frame">
                    <div class="image-ratio">
                        <img src="{{ $option->showImage() }}" class="thumbOption image-option-choose" />
                    </div>
                    <span class="cz-label label-new">
                        {{ trans('polls.label_for.option_image') }}
                    </span>
                </div>
            </a>
        </div>
        <!--END: Win-Frame Add Image -->
    @endif
    @if ($option->description)
        @php
            $haveShowMore = $option->paragraphTimes()
        @endphp
        <div class="clearfix none-tag-mobile"></div>
        <div class="des-child-option none-tag-mobile">
            <span class="item-description-icon">
                <i class="fa fa-quote-right" aria-hidden="true"></i>
            </span>
            <div class="description-body {{ $haveShowMore ? 'show-more' : ''}}">
                {!! $option->description !!}
            </div>
            @if ($haveShowMore)
                <button type="button" class="btn-show-more">
                    <span>{{ trans('polls.message_client.show_more') }}</span>
                </button>
            @endif
        </div>
    @endif
@endforeach
