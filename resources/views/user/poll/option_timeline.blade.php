@php
    $isHideResult = $settingsPoll[config('settings.setting.hide_result')]['isHave'];
    $isTimeOut = $poll->isTimeOut();
@endphp
<!--START: Show option details-->
    <table class="table option-date tb-option" cellspacing="0" cellpadding="0">
        <thead class="thead">
            @if (!$isHideResult || Gate::allows('administer', $poll))
                <!--Start: Show result -->
                <tr class="fix-td result-poll">
                    @if (!$optionDates['hours'])
                        <td colspan="2">
                            <strong>
                                {{ $optionDates['participants']->count() . ' ' . trans('polls.participants')}}
                            </strong>
                        </td>
                    @else
                        <td class="td-not-bg" colspan="2"></td>
                    @endif
                    @foreach ($optionDates['id'] as $counter)
                        <td class="text-center">
                            @if (max(array_values($optionDates['id'])) == $counter)
                                <strong>{{ $counter }}</strong>
                            @else
                                {{ $counter }}
                            @endif
                        </td>
                    @endforeach
                </tr>
                <!--END: Show result -->
            @endif
            @if ($optionDates['hours'])
                <!--START: Show month + year of option -->
                <tr class="header date month">
                    @if ($optionDates['notHour'])
                        <td class="td-empty" colspan="2">
                            <strong>
                                {{ $optionDates['participants']->count() . ' ' . trans('polls.participants')}}
                            </strong>
                        </td>
                    @else
                        <td class="td-not-bg" colspan="2"></td>
                    @endif
                    @foreach ($optionDates['months'] as $data)
                        <td class="msep" colspan="{{ $data['count'] }}">
                            {{ $data['month'] }}
                        </td>
                    @endforeach
                    @if ($optionDates['text'])
                        <td class="hsep" colspan="{{ count($optionDates['text']) }}"></td>
                    @endif
                </tr>
                <!--END: Show month + year of option -->
                <!--START: Show week + day or only of option -->
                <tr class="header date day">
                    @if ($optionDates['notHour'])
                        <td class="hname">
                            {{ trans('polls.name')}}
                        </td>
                        <td class="hemail">
                            {{ trans('polls.email')}}
                        </td>
                    @else
                        <td class="td-empty" colspan="2">
                            <strong>
                                {{ $optionDates['participants']->count() . ' ' . trans('polls.participants')}}
                            </strong>
                        </td>
                    @endif
                    @foreach ($optionDates['days'] as $data)
                        @foreach ($data as $days)
                            <td class="dsep" colspan="{{ $days['count'] }}">
                                {{ $days['day'] }}
                            </td>
                        @endforeach
                    @endforeach
                    @if (!$optionDates['notHour'])
                        @if ($optionDates['text'])
                            <td class="hsep" colspan="{{ count($optionDates['text']) }}"></td>
                        @endif
                    @else
                        @foreach ($optionDates['text'] as $data)
                            <td class="hsep" colspan="1">
                                {{ $data['text'] }}
                            </td>
                        @endforeach
                    @endif
                </tr>
                <!--END: Show week + day or only of option -->
            @endif
            @if (!$optionDates['notHour'])
                <!--START: Show hour or text of option -->
                <tr class="header date time">
                    <td class="hname">
                        {{ trans('polls.name')}}
                    </td>
                    <td class="hemail">
                        {{ trans('polls.email')}}
                    </td>
                    @foreach($optionDates['hours'] as $data)
                        @if($data['hour'] == config('settings.hour_default'))
                            <td class="hsep" colspan="1">
                                <i class="fa fa-minus" aria-hidden="true"></i>
                            </td>
                        @else
                            <td class="hsep" colspan="1">
                                {{ $data['hour'] }}
                            </td>
                        @endif
                    @endforeach
                    @foreach($optionDates['text'] as $data)
                        <td class="hsep" colspan="1">
                            {{ $data['text'] }}
                        </td>
                    @endforeach
                </tr>
                <!--END: Show hour or text of option -->
            @endif
        </thead>
        @if (!$isHideResult || Gate::allows('administer', $poll))
            <tbody class="result-poll waypoints">
                @foreach ($optionDates['participants'] as $voter)
                    <tr>
                        <td class="nsep">{{ $voter['name'] }}</td>
                        <td class="esep">{{ $voter['email'] }}</td>
                        @if ($optionDates['hours'])
                            @foreach ($optionDates['hours'] as $hour)
                                @if ($voter['id']->contains($hour['id']))
                                    <td class="opsep pop">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </td>
                                    @continue
                                @endif
                                <td class="opsep pn"></td>
                            @endforeach
                        @endif
                        @if ($optionDates['text'])
                            @foreach ($optionDates['text'] as $text)
                                @if ($voter['id']->contains($text['id']))
                                    <td class="opsep pop">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </td>
                                    @continue
                                @endif
                                <td class="opsep pn"></td>
                            @endforeach
                        @endif
                    </tr>
                @endforeach
            </tbody>
        @endif
        <tfoot class="tf-check-option">
            @if (!$isLimit && !$poll->isClosed() && !$isTimeOut)
                <tr>
                    <td colspan="2" class="td-choose-option"></td>
                    @foreach ($optionDates['id'] as $id => $counter)
                        <td class="opsep p parent-vote td-choose-option" onclick="voted('{{ $id }}', 'timeline')">
                            @if ($poll->multiple == trans('polls.label.multiple_choice'))
                                <div class="checkbox checkbox-primary">
                                {!!
                                    Form::checkbox('timeline[]', $id, false, [
                                        'onClick' => 'voted("' . $id  .'", "timeline")',
                                        'id' => 'timeline-' . $id,
                                    ])
                                !!}
                            @else
                                <div class="radio radio-primary">
                                {!!
                                    Form::radio('timeline[]', $id, false, [
                                        'onClick' => 'voted("' . $id  .'", "timeline")',
                                        'id' => 'timeline-' . $id,
                                    ])
                                !!}
                            @endif
                            <label></label>
                            </div>
                        </td>
                    @endforeach
                </tr>
            @endif
        </tfoot>
    </table>
<!--END: Show option details-->
