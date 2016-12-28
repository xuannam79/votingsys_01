<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-body scroll-result">
            @if ($mergedParticipantVotes->count())
                <table class="table table-bordered table-detail-result">
                    <thead>
                    <th>{{ trans('polls.no') }}</th>
                    <th>{{ trans('polls.name')}}</th>
                    <th>{{ trans('polls.email')}}</th>
                    @foreach ($poll->options as $option)
                        <th style="min-width: 100px">
                            <center>
                                @if ($isHaveImages)
                                    <img src="{{ $option->showImage() }}" width="16px" height="16px">
                                @endif
                                <p>
                                    {{ str_limit($option->name, 10) }}
                                </p>
                            </center>
                        </th>
                    @endforeach
                    </thead>
                    <tbody>
                    @foreach ($mergedParticipantVotes as $vote)
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
                                        @if (isset($item->user_id))
                                            <td>{{ $item->user->name }}</td>
                                            <td>{{ $item->user->email }}</td>
                                        @else
                                            <td>{{ $item->participant->name }}</td>
                                            <td>{{ $item->participant->email }}</td>
                                        @endif
                                        @php
                                            $isShowVoteName = true;
                                        @endphp
                                    @endif
                                    @if ($item->option_id == $option->id)
                                        <td>
                                            <center><label class="label label-default"><span class="glyphicon glyphicon-ok"> </span></label></center>
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
                <div class="alert alert-info">
                    <p>{{ trans('polls.vote_empty') }}</p>
                </div>
            @endif
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('polls.close') }}</button>
        </div>
    </div>
</div>
