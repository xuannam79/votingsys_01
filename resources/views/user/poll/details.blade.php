@extends('layouts.app')
@section('meta')
    <meta property="fb:app_id" content="708640145978561"/>
    <meta property="og:type" content="article" />
    <meta property="og:url" content="{{ $poll->getUserLink() }}" />
    <meta property="og:title" content="{{ $poll->name }}" />
    <meta property="og:description" content="{{ $poll->description }}" />
    <meta property="og:image" content="{{ asset('/uploads/images/vote.png') }}" />
@endsection
    <script src="https://cdn.socket.io/socket.io-1.3.4.js"></script>
@section('content')
    <div class="container">
        <div class="row">
            <div class="loader"></div>
            <div id="voting_wizard" class="col-lg-10 col-lg-offset-1 well wrap-poll">
                <div class="navbar panel panel-default panel-detail-poll">
                    <div class="panel-body navbar-inner col-lg-12 panel-body-detail-poll">
                        <div class="col-lg-6 col-lg-offset-3 panel-heading">
                            <ul>
                                <li><a href="#vote" data-toggle="tab">{{ trans('polls.nav_tab_edit.voting') }}</a></li>
                                <li><a href="#info" data-toggle="tab">{{ trans('polls.nav_tab_edit.info') }}</a></li>
                                @if (Session::has('isVotedSuccess') && Session::get('isVotedSuccess'))
                                    <li class="active"><a href="#result" data-toggle="tab">{{ trans('polls.nav_tab_edit.result') }}</a></li>
                                @else
                                    <li><a href="#result" data-toggle="tab">{{ trans('polls.nav_tab_edit.result') }}</a></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="hide-vote-details" data-poll-id="{{ $poll->id }}"></div>
                <div class="hide-vote" data-poll-id="{{ $poll->id }}"></div>
                <div class="tab-content">
                    @include('layouts.message')
                    <div class="tab-pane" id="vote">
                        @if ($isLimit)
                            <div class="col-lg-12">
                                <label class="alert alert-danger col-lg-4 col-lg-offset-4 alert-poll-limit">
                                    <span class="glyphicon glyphicon-warning-sign"></span>
                                    {{ trans('polls.reach_limit') }}
                                </label>
                            </div>
                        @endif
                        @if ($isSetIp && (auth()->check() && $isUserVoted || $isSetIp && !auth()->check() && $isParticipantVoted))
                            <div class="alert alert-warning alert-poll-set-ip">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <span class='glyphicon glyphicon-warning-sign'></span>
                                {{ trans('polls.message_vote_one_time') }}
                            </div>
                        @endif
                        @if ($isTimeOut)
                            <div class="alert alert-warning alert-poll-set-ip">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <span class='glyphicon glyphicon-warning-sign'></span>
                                {{ trans('polls.message_poll_time_out') }}
                            </div>
                        @endif
                        {!! Form::open(['route' => 'vote.store','id' => 'form-vote']) !!}
                            <!-- VOTE OPTION -->
                            <div class="panel panel-default panel-vote-option">
                                <div class="panel-body panel-body-vote-option">
                                    <label class="message-validation"></label>
                                    <div class="col-lg-12">
                                        <h4>{{ $poll->title }}
                                            @if ($poll->description)
                                                <span>
                                                    <a href="#" data-placement="right" data-toggle="tooltip" title="{{ $poll->description }}">
                                                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                                                    </a>
                                                </span>
                                            @endif
                                        </h4>
                                        <label class="poll-count">
                                            <span class="label label-primary glyphicon glyphicon-user poll-details">
                                                <span class="count-participant">{{ $countParticipantsVoted }}</span>
                                            </span>
                                            <span class="label label-info glyphicon glyphicon-comment poll-details">
                                                <span class="comment-count">{{ $poll->countComments() }}</span>
                                            </span>
                                            <span class="label label-success glyphicon glyphicon-time poll-details">
                                                {{ $poll->created_at }}
                                            </span>
                                            @if ($poll->date_close)
                                                <span class="span-date-close" data-placement="top" data-toggle="tooltip" title="{{ trans('polls.label.time_close') }}">
                                                    {{ trans('polls.label.time_close') }}: <i>{{ $poll->date_close }}</i>
                                                </span>
                                            @endif
                                        </label>
                                    </div>
                                    <div class="tab-content">
                                        <div class="col-lg-12">
                                            <div class="col-lg-2 col-lg-offset-10 vote-style">
                                                <ul class="nav nav-pills">
                                                    @if (!$isHideResult || Gate::allows('administer', $poll))
                                                        <li>
                                                            <a id="hide" class="btn-show-result-poll btn-vote-style" onclick="showResultPoll()">
                                                                <i class="fa fa-eye-slash li-show-result-poll" aria-hidden="true"></i>
                                                            </a>
                                                        </li>
                                                    @endif
                                                    <li>
                                                        <a data-toggle="tab" href="#vertical" class="btn-vote-style">
                                                            <i class="fa fa-th" aria-hidden="true"></i>
                                                        </a>
                                                    </li>
                                                    <li class="active">
                                                        <a data-toggle="tab" href="#horizontal" class="btn-vote-style">
                                                            <i class="fa fa-bars" aria-hidden="true"></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <!-- VOTE OPTION HORIZONTAL-->
                                        <div id="horizontal" class="tab-pane fade in active vote-style-detail">
                                            <div class="col-lg-12 horizontal-overflow">
                                                @foreach ($poll->options as $option)
                                                    <li class="list-group-item parent-vote li-parent-vote" onclick="voted('{{ $option->id }}', 'horizontal')">
                                                        @if (!$isHideResult || Gate::allows('administer', $poll))
                                                            <span id="id1{{ $option->id }}" class="badge float-xs-right result-poll">{{ $option->countVotes() }}</span>
                                                        @endif

                                                        <!-- show checkbox(multiple selection) or radio button(single selection) to vote if
                                                                1. Not enough number maximum of poll
                                                                2. Poll have not closed
                                                                3. Not set vote by IP of computer
                                                                4. Set vote IP. if :
                                                                    a. user have been login but have not vote
                                                                    b. user have not login and have not vote
                                                        -->
                                                        @if ($isSetIp && auth()->check() && ! $isUserVoted
                                                            || ($isSetIp && ! auth()->check() && ! $isParticipantVoted)
                                                            || ! $isLimit && ! $poll->isClosed() && ! $isSetIp)
                                                            @if ($poll->multiple == trans('polls.label.multiple_choice'))
                                                                {!!
                                                                    Form::checkbox('option[]', $option->id, false, [
                                                                        'onClick' => 'voted("' . $option->id  .'", "horizontal")',
                                                                        'class' => 'poll-option poll-option-detail',
                                                                        'id' => 'horizontal-' . $option->id
                                                                    ])
                                                                !!}
                                                            @else
                                                                {!!
                                                                    Form::radio('option[]', $option->id, false, [
                                                                        'onClick' => 'voted("' . $option->id  .'", "horizontal")',
                                                                        'class' => 'poll-option poll-option-detail',
                                                                        'id' => 'horizontal-' . $option->id
                                                                    ])
                                                                !!}
                                                            @endif
                                                        @endif
                                                        <div class="option-name">
                                                            <p>
                                                                <img src="{{ $option->showImage() }}" class="image-option-vote" onclick="showModelImage('{{ $option->showImage() }}')">
                                                                <span class="content-option-vote">{{ $option->name ? $option->name : " " }}</span>
                                                            </p>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </div>
                                        </div>

                                        <!-- VOTE OPTION VERTICAL-->
                                        <div id="vertical" class="tab-pane fade in vote-style-detail">
                                            <div class="col-lg-12 vertical-overflow">
                                                @foreach ($poll->options as $option)
                                                    <div class="col-lg-4 vertical-option">
                                                        <div class="panel panel-default" id="{{ $option->id }}">
                                                            <div class="panel-heading parent-vote panel-heading-vertical"  onclick="voted('{{ $option->id }}', 'horizontal')">
                                                                @if ($isSetIp && auth()->check() && ! $isUserVoted
                                                                    || $isSetIp && !auth()->check() && ! $isParticipantVoted
                                                                    || ! $isLimit && ! $poll->isClosed() && ! $isSetIp)
                                                                    @if ($poll->multiple == trans('polls.label.multiple_choice'))
                                                                        {!!
                                                                            Form::checkbox('option_vertical[]', $option->id, false, [
                                                                                'onClick' => 'voted("' . $option->id  .'","vertical")',
                                                                                'class' => 'poll-option',
                                                                                'id' => 'vertical-' . $option->id
                                                                            ])
                                                                        !!}
                                                                    @else
                                                                        {!!
                                                                            Form::radio('option_vertical[]', $option->id, false, [
                                                                                'onClick' => 'voted("' . $option->id  .'","vertical")',
                                                                                'class' => 'poll-option',
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
                                                                    <img src="{{ $option->showImage() }}" onclick="showModelImage('{{ $option->showImage() }}')">
                                                                    {{ $option->name ? $option->name : " " }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    @if ($isSetIp && auth()->check() && ! $isUserVoted
                                        || $isSetIp && !auth()->check() && ! $isParticipantVoted
                                        || ! $isLimit && ! $poll->isClosed() && ! $isSetIp && !$isTimeOut)
                                        {!! Form::hidden('pollId', $poll->id) !!}
                                        {!! Form::hidden('isRequiredEmail', $isRequiredEmail) !!}
                                        <div class="row">
                                            <div class="col-lg-5">
                                                <div class="input-group  {{ ($isRequiredName || $isRequiredNameAndEmail) ? "required" : "" }}">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-user" aria-hidden="true"></i>
                                                    </span>
                                                    {!!
                                                        Form::text('nameVote', auth()->check() ? auth()->user()->name : null, [
                                                            'class' => 'form-control nameVote',
                                                            'placeholder' => trans('polls.placeholder.enter_name')
                                                        ])
                                                    !!}
                                                </div>
                                            </div>
                                            <div class="col-lg-5">
                                                <div class="input-group {{ ($isRequiredEmail || $isRequiredNameAndEmail) ? "required" : "" }}">
                                                    <span class="input-group-addon">
                                                        <i class="glyphicon glyphicon-envelope" aria-hidden="true"></i>
                                                    </span>
                                                    {!!
                                                        Form::email('emailVote', auth()->check() ? auth()->user()->email : null, [
                                                            'class' => 'form-control emailVote',
                                                            'placeholder' => trans('polls.placeholder.email')
                                                        ])
                                                    !!}
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <span class="input-group-btn"
                                                    data-message-email="{{ trans('polls.message_email') }}"
                                                    data-url="{{ url('/check-email') }}"
                                                    data-message-validate-email="{{ trans('polls.message_validate_email') }}"
                                                    data-message-required-email="{{ trans('polls.message_required_email') }}"
                                                    data-message-required-name="{{ trans('polls.message_validate_name') }}"
                                                    data-message-required-name-and-email="{{ trans('polls.message_validate_name_and_email') }}"
                                                    data-is-required-email="{{ $isRequiredEmail ? 1 : 0 }}"
                                                    data-is-required-name="{{ $isRequiredName ? 1 : 0 }}"
                                                    data-is-required-name-and-email="{{ $isRequiredNameAndEmail ? 1 : 0 }}">
                                                    {{ Form::button(trans('polls.vote'), ['class' => 'btn btn-success btn-vote', ! $isUserVoted ? 'disabled' : '']) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- MODAL VIEW IMAGE-->
                            <div id="modalImageOption" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title">{{ trans('polls.image_preview') }}</h4>
                                        </div>
                                        <div class="modal-body">
                                            <img src="#" id="imageOfOptionPreview">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('polls.close') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {!! Form::close() !!}
                    </div>
                    <div class="tab-pane" id="info">
                        <!-- POLL INFO -->
                        <div class="col-lg-12 poll-info">
                            <h4>
                                {{ $poll->title }}
                                @if ($poll->description)
                                    <span>
                                        <a href="#" data-placement="right" data-toggle="tooltip" title="{{ $poll->description }}">
                                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                                        </a>
                                    </span>
                                @endif
                            </h4>
                            <p>
                                <span class="span-info">
                                    <i class="fa fa-clock-o" aria-hidden="true"></i> {{ $poll->created_at }}
                                </span>
                                <span>
                                    <i class="fa fa-user" aria-hidden="true"></i>
                                    @if ($poll->user_id)
                                        <label class="label-poll-info">{{ $poll->user->name }}</label>
                                    @else
                                        <label class="label-poll-info">{{ $poll->name }}</label>
                                    @endif
                                </span>
                                @if ($poll->location)
                                    <span class="span-location-poll" data-placement="top" data-toggle="tooltip" title="{{ $poll->location }}">
                                        <i class="fa fa-map-marker" aria-hidden="true"></i>
                                        {{ str_limit($poll->location, config('settings.str_limit.location')) }}
                                    </span>
                                @endif
                            </p>
                            <div class="form-group col-lg-12 div-like-share">
                                <div class="fb-like"
                                     data-href="{{ $poll->getUserLink() }}"
                                     data-layout="standard" data-action="like"
                                     data-size="small" data-show-faces="true"
                                     data-share="true">
                                </div>
                            </div>
                        </div>
                        <!-- COMMENT -->
                        <div class="col-md-12" id="panel-comment">
                            <div class="panel panel-default panel-darkcyan">
                                <div class="panel-heading panel-heading-darkcyan">
                                    <h4>
                                        <span class="comment-count">{{ $poll->countComments() }} </span>
                                        {{ trans('polls.comments') }}
                                        <span data-label-show-comment = "{{ trans('polls.show_comments') }}" data-label-hide="{{ trans('polls.hide') }}">
                                        <button class="btn btn-warning show" id="show-hide-list-comment">{{ trans('polls.hide') }}</button>
                                    </span>
                                    </h4>
                                </div>
                                <div class="panel-body">
                                    <div class="hide" data-route="{{ url('user/comment') }}" data-confirm-remove="{{ trans('polls.confirmRemove') }}">
                                    </div>
                                    <div class="comments">
                                        @foreach ($poll->comments as $comment)
                                            <div class="col-md-12" id="{{ $comment->id }}">
                                                <br>
                                                <div class="col-md-1 col-lg-1">
                                                    @if (isset($comment->user) && ($comment->name == $comment->user->name))
                                                        <img class="img-comment img-circle" src="{{ $comment->user->getAvatarPath() }}">
                                                    @else
                                                        <img class="img-comment img-circle" src="{{ $comment->showDefaultAvatar() }}">
                                                    @endif
                                                </div>
                                                <div class="col-md-11 col-lg-11">
                                                    <label data-comment-id="{{ $comment->id }}" data-poll-id="{{ $poll->id }}">
                                                        <label class="user-comment">{{ $comment->name }}</label>
                                                        {{ $comment->created_at->diffForHumans() }}
                                                        @if (Gate::allows('ownerPoll', $poll))
                                                            <span class="glyphicon glyphicon-trash delete-comment"></span>
                                                        @endif
                                                    </label>
                                                    <br>
                                                    {{ $comment->content }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if (count($poll->comments))
                                        <div class="col-lg-12">
                                            <hr class="hr-darkcyan">
                                        </div>
                                    @endif
                                    <div class="col-lg-12 comment comment-poll"
                                         data-label-add-comment = "{{ trans('polls.add_comment') }}"
                                         data-label-hide="{{ trans('polls.hide') }}">
                                        {!! Form::open(['route' => 'comment.store', 'class' => 'form-horizontal', 'id' => 'form-comment']) !!}
                                            <div>
                                                <label class="message-validate comment-name-validate"> </label>
                                                <label class="message-validate comment-content-validate"></label>
                                            </div>
                                            <div class="col-md-6  comment">
                                                {!!
                                                    Form::text('name', auth()->check() ? auth()->user()->name : null, [
                                                        'class' => 'form-control',
                                                        'id' => 'name' . $poll->id,
                                                        'placeholder' => trans('polls.placeholder.full_name')
                                                    ])
                                                !!}
                                            </div>
                                            <div class="col-md-10 comment"
                                                 data-poll-id="{{ $poll->id }}"
                                                 data-user="{{ auth()->check() ? auth()->user()->name : '' }}"
                                                 data-comment-name="{{ trans('polls.comment_name') }}"
                                                 data-comment-content="{{ trans('polls.comment_content') }}">
                                                {!!
                                                    Form::textarea('content', null, [
                                                        'class' => 'form-control',
                                                        'rows' => config('settings.poll.comment_row'),
                                                        'placeholder' => trans('polls.placeholder.comment'),
                                                        'id' => 'content' . $poll->id
                                                    ])
                                                !!}
                                                {{ Form::button(trans('polls.save_comment'), ['type' => 'submit', 'class' => 'btn addComment']) }}
                                            </div>
                                        {!! Form::close() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- POLL RESULT -->
                    <!-- if voted -> tab result(active) -->
                    @if (Session::has('isVotedSuccess') && Session::get('isVotedSuccess'))
                        @php
                            Session::forget('isVotedSuccess');
                        @endphp
                        <div class="tab-pane active" id="result">
                    @else
                        <div class="tab-pane" id="result">
                    @endif
                        @if (!$isHideResult || Gate::allows('administer', $poll))
                            <div class="panel panel-default">
                                <!-- if have not vote -> hide tab style result -->
                                <div class="panel-heading bar-pie-chart">
                                    @if ($optionRateBarChart != "null")
                                        <ul class="nav nav-pills">
                                            <li class="active">
                                                <a data-toggle="tab" href="#table">
                                                    <i class="fa fa-table" aria-hidden="true"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="tab-bar-chart" data-toggle="tab" href="#barChart">
                                                    <i class="fa fa-bar-chart" aria-hidden="true"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="tab-pie-chart" data-toggle="tab" href="#pieChart">
                                                    <i class="fa fa-pie-chart" aria-hidden="true"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    @endif
                                </div>
                                <div class="panel-body">
                                    <div class="tab-content">
                                        <!-- TABLE RESULT -->
                                        <div id="table" class="tab-pane fade show-details_default in active">
                                            <div class="col-lg-12 div-show-detail-vote">
                                                <!-- SHOW DETAIL VOTE -->
                                                <button type="button" class="btn btn-primary btn-show-detail-vote" data-toggle="modal" data-target="#myModal">
                                                    <span class="glyphicon glyphicon-eye-open"></span>
                                                    {{ trans('polls.show_vote_details') }}
                                                </button>
                                            </div>

                                            <div class="modal fade model-show-details" id="myModal" role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-body scroll-result">
                                                            @if ($countParticipantsVoted)
                                                                <table class="table table-bordered table-detail-result">
                                                                    <thead>
                                                                        <th>{{ trans('polls.no') }}</th>
                                                                        <th>{{ trans('polls.name')}}</th>
                                                                        <th>{{ trans('polls.email')}}</th>
                                                                        @foreach ($poll->options as $option)
                                                                            <th class="th-detail-vote">
                                                                                <center>
                                                                                    <p>
                                                                                        {{ str_limit($option->name, 50) }}
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
                                                                                                <td>{{ $item->participant->showName() }}</td>
                                                                                                <td>{{ $item->participant->email }}</td>
                                                                                            @endif
                                                                                            @php
                                                                                                $isShowVoteName = true;
                                                                                            @endphp
                                                                                        @endif
                                                                                        @if ($item->option_id == $option->id)
                                                                                            <td>
                                                                                                <center>
                                                                                                    <label class="label label-default">
                                                                                                        <span class="glyphicon glyphicon-ok"> </span>
                                                                                                    </label>
                                                                                                </center>
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
                                            </div>
                                            <div class="col-lg-12 table-poll-result result-vote-poll">
                                                <table class="table table-hover table-responsive">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ trans('polls.no') }}</th>
                                                            <th>{{ trans('polls.label.option') }}</th>
                                                            <th>{{ trans('polls.number_vote') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($dataTableResult as $key => $data)
                                                            <tr>
                                                                <td>{{ $key + 1 }}</td>
                                                                <td class="td-poll-result">
                                                                    <img src="{{ asset($data['image']) }}">
                                                                    <p>{{ $data['name'] }}</p>
                                                                </td>
                                                                <td><span id="id3{{ $data['option_id'] }}" class="badge">{{ $data['numberOfVote'] }}</span></td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <!-- MODEL VOTE CHART-->
                                        @if ($optionRateBarChart)
                                            <div class="show-piechart tab-pane fade" id="pieChart" role="dialog">
                                                <div class="col-lg-12">
                                                    <!-- pie chart -->
                                                    <script type="text/javascript">
                                                        google.charts.load('current', {'packages':['corechart']});
                                                        google.charts.setOnLoadCallback(drawChart);
                                                        function drawChart() {
                                                            // Create the data table.
                                                            var data = new google.visualization.DataTable();
                                                            data.addColumn('string', 'Topping');
                                                            data.addColumn('number', 'Slices');
                                                            var optionRateBarChart = {!! $optionRateBarChart !!};
                                                            data.addRows(optionRateBarChart);
                                                            var options = {
                                                                'width': 850,
                                                                'height': 450,
                                                                is3D: true,
                                                                forceIFrame: true,
                                                                pieSliceTextStyle: {
                                                                    fontSize: '20px'
                                                                },
                                                            };
                                                            var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
                                                            chart.draw(data, options);
                                                        }
                                                    </script>
                                                    <div id="chart_div"></div>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($optionRateBarChart)
                                            <div class="show-barchart tab-pane fade" id="barChart" role="dialog">
                                                <div class="col-lg-12">
                                                    <!-- bar chart -->
                                                    <script type="text/javascript">
                                                        google.charts.load('current', {'packages':['corechart']});
                                                        google.charts.setOnLoadCallback(drawChart);
                                                        function drawChart() {
                                                            // Create the data table.
                                                            var data = new google.visualization.DataTable();
                                                            data.addColumn('string', 'Topping');
                                                            data.addColumn('number', '');

                                                            var optionRateBarChart = {!! $optionRateBarChart !!};
                                                            data.addRows(optionRateBarChart);
                                                            var options = {
                                                                'width': 750,
                                                                'height': 400,
                                                                chartArea:{
                                                                    left:250,
                                                                },
                                                                colors: ['darkcyan'],
                                                                hAxis: {
                                                                    gridlines: {
                                                                        count: 4
                                                                    }
                                                                },
                                                            };
                                                            var chart = new google.visualization.BarChart(document.getElementById('chart'));
                                                            chart.draw(data, options);
                                                        }
                                                    </script>
                                                    <div id="chart"></div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning alert-hide-result">
                                <span class='glyphicon glyphicon-warning-sign'></span>
                                {{ trans('polls.hide_result_message') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
