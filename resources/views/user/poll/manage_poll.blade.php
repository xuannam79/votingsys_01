@extends('layouts.app')
@section('meta')
    <meta property="og:url" content="{{ $tokenLinkUser }}" />
    <meta property="og:type" content="article" />
    <meta property="og:title" ontent="{{ $poll->title }}" />
    <meta property="og:description" content="{{ $poll->description }}?" />
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">{{ trans('polls.poll_details') }}</div>
                <div class="panel-body">
                    <div class="hide" data-poll-id="{{ $poll->id }}" data-route="{{ url('user/poll') }}"
                        data-link-exist="{{ trans('polls.link_exist') }}" data-link-invalid="{{ trans('polls.link_invalid') }}"
                        data-edit-link-success="{{ trans('polls.edit_link_successfully') }}"
                        data-link="{{ url('link') }}">
                    </div>
                    <div class="col-md-12">
                        {{ Form::open(['route' => ['poll.destroy', $poll->id], 'method' => 'delete']) }}
                            {{
                                Form::button('<span class="glyphicon glyphicon-remove-sign"></span>' . ' ' . trans('polls.close_poll'), [
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-administration',
                                    'onclick' => 'return confirm("' . trans('polls.confirm_close_poll') . '")'
                                ])
                            }}
                        {{ Form::close() }}
                        @if ($poll->countParticipants())
                            <a href="{{ URL::action('User\ParticipantController@deleteAllParticipant', ['poll_id' => $poll->id]) }}" class="btn btn-danger  btn-administration">
                                <span class="glyphicon glyphicon-remove-sign"></span>
                                {{ trans('polls.delete_all_participants') }}
                            </a>
                        @else
                            <a class="btn btn-danger btn-administration disable-link">
                                <span class="glyphicon glyphicon-remove-sign"></span>
                                {{ trans('polls.delete_all_participants') }}
                            </a>
                        @endif
                        <a href="{{ URL::action('User\ActivityController@show', $poll->id) }}" class="btn btn-primary  btn-administration">
                            <span class="glyphicon glyphicon-star-empty"></span>
                            {{ trans('polls.view_history') }}
                        </a>
                        {{ Form::open(['route' => ['exportPDF', 'poll_id' => $poll->id]]) }}
                            {{
                                Form::button('<span class="glyphicon glyphicon-export"></span>' . ' ' . trans('polls.export_pdf'), [
                                    'type' => 'submit',
                                    'class' => 'btn btn-primary btn-administration'
                                ])
                            }}
                        {{ Form::close() }}

                        {{ Form::open(['route' => ['exportExcel', 'poll_id' => $poll->id]]) }}
                            {{
                                Form::button('<span class="glyphicon glyphicon-export"></span>' . ' ' . trans('polls.export_excel'), [
                                    'type' => 'submit',
                                    'class' => 'btn btn-primary btn-administration'
                            ])
                        }}
                        {{ Form::close() }}
                        <br><br>
                        <div class="col-md-12">
                            <i>{{ trans('polls.participation_link') }}</i>
                                <span class="glyphicon glyphicon-arrow-right btn-link-user"></span>
                            <br>
                            <div class="col-md-5">
                                <label>{{ url('link') }}</label>
                            </div>
                            <div class="col-md-3">
                                {{ Form::text('participation_link', $tokenLinkUser, ['class' => 'form-control token-user']) }}
                            </div>
                            <div class="col-md-2" data-token-link-user="{{ $tokenLinkUser }}">
                                {{ Form::button(trans('polls.edit_link_user'), ['class' => 'btn btn-success edit-link-user']) }}
                            </div>
                            <div class="col-md-2">
                                <label class="message-link-user"></label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <i>{{ trans('polls.administer_link') }}</i>
                            <span class="glyphicon glyphicon-arrow-right btn-link-admin"></span>
                            <br>
                            <div class="col-md-5">
                                <label>{{ url('link') }}</label>
                            </div>
                            <div class="col-md-3">
                                {{ Form::text('administer_link', $tokenLinkAdmin, ['class' => 'form-control token-admin']) }}
                            </div>
                            <div class="col-md-2" data-token-link-admin="{{ $tokenLinkAdmin }}">
                                {{ Form::button(trans('polls.edit_link_admin'), ['class' => 'btn btn-success edit-link-admin']) }}
                            </div>
                            <div class="col-md-2">
                                <label class="message-link-admin"></label>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <hr>
                        <table class="table table-bordered">
                            <thead>
                            <th><center>{{ trans('polls.no') }}</center></th>
                            <th><center>{{ $isRequiredEmail ? trans('polls.email') : trans('polls.name')}}</center></th>
                            @foreach ($poll->options as $option)
                                <th>
                                    <center>
                                        <img class="img-option" src="{{ $option->showImage() }}">
                                        <br>
                                        {{ $option->name }}
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
                                                <td>
                                                    @if (isset($item->user_id))
                                                        {{ Form::open(['route' => ['vote.destroy', $item->user->id], 'method' => 'delete']) }}
                                                        {{ Form::hidden('type', config('settings.type.user')) }}
                                                        {{ $isRequiredEmail ? $item->user->email : $item->user->name }}
                                                    @else
                                                        {{ Form::open(['route' => ['vote.destroy', $item->participant->id], 'method' => 'delete']) }}
                                                        {{ Form::hidden('type', config('settings.type.participant')) }}
                                                        {{ $isRequiredEmail ? $item->participant->email : $item->participant->name }}
                                                    @endif
                                                    @if (Gate::allows('administer', $poll))
                                                        {{ Form::hidden('poll_id', $poll->id) }}
                                                        {{ Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs remove-vote', 'onclick' => 'return confirm("' . trans('polls.confirm_delete_vote') . '")']) }}
                                                    @endif
                                                    {{ Form::close() }}
                                                </td>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
