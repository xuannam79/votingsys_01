<div class="modal-dialog modal-dialog-mobile">
    <div class="modal-content">
        <div class="modal-body scroll-result">
            <div class="input-group col-xs-5 search-row">
                {!! Form::text('searchTD', null, [
                    'class' => 'form-control search-row-detail',
                    'placeholder' => 'Search'
                ]) !!}
                <div class="input-group-btn">
                    <div class="input-group-btn">
                        {{ Form::button('<i class="glyphicon glyphicon-search"></i>', [
                            'class' => 'btn btn-yes'
                        ]) }}
                    </div>
                </div>
            </div>
            <!--START: Show option details-->
            <table class="table option-date details-vote-mobile" cellspacing="0" cellpadding="0">
                <tbody>
                    @if($optionDates['hours'])
                        <!--START: Show month + year of option -->
                        <tr class="header date month">
                            @if($optionDates['notHour'])
                                <td colspan="2">
                                    <strong>
                                        {{ $optionDates['participants']->count() . ' ' . trans('polls.participants')}}
                                    </strong>
                                </td>
                            @else
                                <td colspan="2"></td>
                            @endif
                            @foreach($optionDates['months'] as $data)
                                <td class="msep" colspan="{{ $data['count'] }}">
                                    {{ $data['month'] }}
                                </td>
                            @endforeach
                            @if($optionDates['text'])
                                <td class="hsep" colspan="{{ count($optionDates['text']) }}"></td>
                            @endif
                        </tr>
                        <!--END: Show month + year of option -->
                        <!--START: Show week + day or only of option -->
                        <tr class="header date day">
                            @if($optionDates['notHour'])
                                <td class="hname">
                                    {{ trans('polls.name')}}
                                </td>
                                <td class="hemail">
                                    {{ trans('polls.email')}}
                                </td>
                            @else
                                <td colspan="2">
                                    <strong>
                                        {{ $optionDates['participants']->count() . ' ' . trans('polls.participants')}}
                                    </strong>
                                </td>
                            @endif
                            @foreach($optionDates['days'] as $data)
                                @foreach($data as $days)
                                    <td class="dsep" colspan="{{ $days['count'] }}">
                                        {{ $days['day'] }}
                                    </td>
                                @endforeach
                            @endforeach
                            @if(!$optionDates['notHour'])
                                @if($optionDates['text'])
                                    <td class="hsep" colspan="{{ count($optionDates['text']) }}"></td>
                                @endif
                            @else
                                @foreach($optionDates['text'] as $data)
                                    <td class="hsep" colspan="1">
                                        {{ $data['text'] }}
                                    </td>
                                @endforeach
                            @endif
                        </tr>
                        <!--END: Show week + day or only of option -->
                    @endif
                    @if(!$optionDates['notHour'])
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
                    @foreach($optionDates['participants'] as $voter)
                        <tr>
                            <td class="nsep">{{ $voter['name'] }}</td>
                            <td class="esep">{{ $voter['email'] }}</td>
                            @if($optionDates['hours'])
                                @foreach($optionDates['hours'] as $hour)
                                    @if($voter['id']->contains($hour['id']))
                                        <td class="opsep pop">
                                            <i class="fa fa-check" aria-hidden="true"></i>
                                        </td>
                                        @continue
                                    @endif
                                    <td class="opsep pn"></td>
                                @endforeach
                            @endif
                            @if($optionDates['text'])
                                @foreach($optionDates['text'] as $text)
                                    @if($voter['id']->contains($text['id']))
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
                    <!--Start: Show result -->
                    <tr>
                        @if ($optionDates['participants']->count() > config('settings.limit_show_option_below'))
                            <td colspan="2">
                                <strong>
                                    {{ $optionDates['participants']->count() . ' ' . trans('polls.participants')}}
                                </strong>
                            </td>
                        @else
                             <td colspan="2">
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
                </tbody>
            </table>
            <!--END: Show option details-->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-yes" data-dismiss="modal">{{ trans('polls.close') }}</button>
        </div>
    </div>
</div>
