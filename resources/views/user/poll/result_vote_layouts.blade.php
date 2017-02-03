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
                <td class="td-detail-option">
                    <img src="{{ asset($data['image']) }}">
                    <span class="option-name">{{ $data['name'] }}</span>
                </td>
                <td><span id="id3{{ $data['option_id'] }}" class="badge">{{ $data['numberOfVote'] }}</span></td>
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
