@php
    $isHideResult = $settingsPoll[config('settings.setting.hide_result')]['isHave'];
    $isTimeOut = $poll->isTimeOut();
@endphp
@foreach ($poll->options as $option)
    <div class="col-lg-4 vertical-option">
        <div class="panel panel-default" id="{{ $option->id }}">
            <div class="panel-heading parent-vote panel-heading-vertical"  onclick="voted('{{ $option->id }}', 'horizontal')">
                @if (!$isLimit && !$poll->isClosed() && !$isTimeOut)
                    @if ($poll->multiple == trans('polls.label.multiple_choice'))
                        {!!
                            Form::checkbox('option_vertical[]', $option->id, false, [
                                'onClick' => 'voted("' . $option->id . '","vertical")',
                                'id' => 'vertical-' . $option->id
                            ])
                        !!}
                    @else
                        {!!
                            Form::radio('option_vertical[]', $option->id, false, [
                                'onClick' => 'voted("' . $option->id . '","vertical")',
                                'id' => 'vertical-' . $option->id
                            ])
                        !!}
                    @endif
                @endif
                @if (!$isHideResult || Gate::allows('administer', $poll))
                    <span id="id2{{ $option->id }}" class="badge result-poll result-poll-vertical">{{ $option->countVotes() }}</span>
                @endif
            </div>
            <div class="panel-body panel-body-vertical-option">
                <p>
                    @if($isHaveImages)
                        <img src="{{ $option->showImage() }}" onclick="showModelImage('{{ $option->showImage() }}')">
                    @endif
                    {{ $option->name ? $option->name : " " }}
                </p>
            </div>
        </div>
    </div>
@endforeach
