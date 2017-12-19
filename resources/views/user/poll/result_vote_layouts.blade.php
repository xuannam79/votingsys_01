<div class="row header-table-mobile none-in-laptop">
    <div class="col-xs-2 col-sm-1 no-of-result-mobile">
        {{ trans('polls.no') }}
    </div>
    <div class="col-xs-7 col-sm-8">
        {{ trans('polls.label.option') }}
    </div>
    <div class="col-xs-3 col-sm-3 padding-vote-mobile">
        {{ trans('polls.number_vote') }}
    </div>
</div>
@php
    $maxVote = max(array_column($dataTableResult, 'numberOfVote'));
    $voted = true;
@endphp
@foreach ($dataTableResult as $key => $data)
    <div class="row none-in-laptop">
        <div class="col-xs-2 col-sm-1 no-of-result-mobile">
            {{ $key + 1 }}
        </div>
        <div class="col-xs-7 col-sm-8 content-mobile no-of-result-mobile">
            <p>{{ $data['name'] }}</p>
        </div>
        <div class="col-xs-3 col-sm-3 padding-vote-mobile">
            <span class="badge">{{ $data['numberOfVote'] }}</span>
            @if ($maxVote == $data['numberOfVote'] && $voted)
                @php
                    $voted = false;
                @endphp
                <img src="{{ asset(config('settings.option.path_trophy')) }}" class="trophy">
            @endif
        </div>
    </div>
@endforeach
<table class="table table-hover table-responsive none-tag-mobile">
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
                    <span class="badge">{{ $data['numberOfVote'] }}</span>
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
