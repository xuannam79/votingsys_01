<h4> {{ $poll->title }} </h4>
<label> {{ trans('polls.poll_initiate') }} {{ $poll->user->name }}</label>
<i>
    </span>
        {{ $poll->created_at->diffForHumans() }}
    </span>
</i>
<br>
<label> {{trans('polls.where')}} </label>
<span>{{ $poll->location }}</span>
<br>
<i> {{ $poll->description }} </i>
<br><br>
@if ($votes->count())
<table class="table table-bordered">
<thead>
<tr>
    <th><center>{{ trans('polls.no') }}</center></th>
    <th><center>{{ $isRequiredEmail ? trans('polls.email') : trans('polls.name')}}</center></th>
    @foreach ($poll->options as $option)
        <th>
            <center>
                {{ $option->name }}
            </center>
        </th>
    @endforeach
</tr>
</thead>
<tbody>
    @foreach ($votes as $vote)
    <tr>
        <td><center>{{ ++$numberOfVote }}</center></td>
        @php
            $isShowVoteName = false;
        @endphp
        @foreach ($poll->options as $option)
            @php
                $isShowOptionUserVote = false;
            @endphp
            @foreach ($vote as $item)
                @if (! $isShowVoteName)
                    <td>
                        @if (isset($item->user_id))
                            {{ $isRequiredEmail ? $item->user->email : $item->user->name }}
                        @else
                            {{ $isRequiredEmail ? $item->participant->email : $item->participant->name }}
                        @endif
                    </td>
                    @php
                        $isShowVoteName = true;
                    @endphp
                @endif
                @if ($item->option_id == $option->id)
                    <td>
                        <center>x</center>
                    </td>
                    @php
                        $isShowOptionUserVote = true;
                    @endphp
                @endif
            @endforeach
            @if (!$isShowOptionUserVote)
                <td></td>
            @endif
        @endforeach
    </tr>
    @endforeach
</tbody>
</table>
@else
    <center>
        <p>{{ trans('polls.vote_empty') }}</p>
    </center>
@endif
