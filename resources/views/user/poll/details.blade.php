@extends('layouts.app')
@section('meta')
    <meta property="fb:app_id" content="708640145978561"/>
    <meta property="og:type" content="article" />
    <meta property="og:url" content="{{ $poll->getUserLink() }}" />
    <meta property="og:title" content="{{ $poll->name }}" />
    <meta property="og:description" content="{{ $poll->description }}" />
    <meta property="og:image" content="{{ asset('/uploads/images/vote.png') }}" />
@endsection
@section('content')
    <div class="container">
    <div class="row">
        <div class="loader"></div>
        <div id="voting_wizard" class="col-lg-10 col-lg-offset-1 well wrap-poll">
            <div class="navbar panel panel-default" style="margin-bottom: 0; border-radius: 0">
                <div class="panel-body navbar-inner col-lg-12" style="padding: 0">
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

            <div class="tab-content">
                @include('layouts.message')
                <div class="tab-pane" id="vote">
                @if ($isLimit)
                    <label class="alert alert-danger col-lg-6 col-lg-offset-3"> <span class="glyphicon glyphicon-warning-sign"></span>
                        {{ trans('polls.reach_limit') }}
                    </label>
                @endif
                @if ($isSetIp && (auth()->check() && $isUserVoted || $isSetIp && !auth()->check() && $isParticipantVoted))
                    <div class="col-lg-12">
                        <div class="alert alert-warning col-lg-10 col-lg-offset-1">
                            <span class='glyphicon glyphicon-warning-sign'></span>
                            {{ trans('polls.message_vote_one_time') }}
                        </div>
                    </div>
                @endif
                {!! Form::open(['route' => 'vote.store','id' => 'form-vote']) !!}
                    <!-- VOTE OPTION -->
                    <div class="panel panel-default" style="border-radius: 0">
                        <div class="panel-body" style="padding: 5px">
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

                                <label class="poll-count" style="width: 100%;">
                                    <span class="label label-primary glyphicon glyphicon-user poll-details">
                                        {{ $mergedParticipantVotes->count() }}
                                    </span>
                                    <span class="label label-info glyphicon glyphicon-comment poll-details">
                                        <span class="comment-count">{{ $poll->countComments() }}</span>
                                    </span>
                                    <span class="label label-success glyphicon glyphicon-time poll-details">
                                        {{ $poll->created_at }}
                                    </span>
                                    @if ($poll->date_close)
                                        <span style="float: right; margin-left: 20px" data-placement="top" data-toggle="tooltip" title="{{ trans('polls.label.time_close') }}">
                                            {{ trans('polls.label.time_close') }}: <i>{{ $poll->date_close }}</i>
                                        </span>
                                    @endif
                                </label>
                            </div>


                            <div class="tab-content">
                                <div class="col-lg-12">
                                    <div class="col-lg-2 col-lg-offset-10" style="padding-right: 4px; clear: both">
                                        <ul class="nav nav-pills" style="float: right;">
                                            @if (!$isHideResult || Gate::allows('administer', $poll))
                                                <li>
                                                    <a style="padding: 5px 10px" id="hide" class="btn-show-result-poll" onclick="showResultPoll()">
                                                        <i class="fa fa-eye-slash li-show-result-poll" aria-hidden="true"></i>
                                                    </a>
                                                </li>
                                            @endif
                                            <li>
                                                <a data-toggle="tab" href="#vertical" style="padding: 5px 10px">
                                                    <i class="fa fa-th" aria-hidden="true"></i>
                                                </a>
                                            </li>
                                            <li class="active">
                                                <a data-toggle="tab" href="#horizontal" style="padding: 5px 10px">
                                                    <i class="fa fa-bars" aria-hidden="true"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <!-- VOTE OPTION HORIZONTAL-->
                                <div id="horizontal" class="tab-pane fade in active" style="clear: both">
                                    <div class="col-lg-12" style="max-height: 400px; overflow-y: scroll;">
                                        @foreach ($poll->options as $option)
                                            <li class="list-group-item parent-vote" style="min-height: 70px; border-radius: 0"  onclick="voted('{{ $option->id }}', 'horizontal')">
                                                @if (!$isHideResult || Gate::allows('administer', $poll))
                                                    <span class="badge float-xs-right result-poll">{{ $option->countVotes() }}</span>
                                                @endif

                                                @if ($isSetIp && (auth()->check() && ! $isUserVoted || $isSetIp && !auth()->check() && ! $isParticipantVoted) || ! $isLimit && ! $poll->isClosed() && ! $isSetIp)
                                                    @if ($poll->multiple == trans('polls.label.multiple_choice'))
                                                        {!! Form::checkbox('option[]', $option->id, false, ['onClick' => 'voted("' . $option->id  .'","horizontal")', 'class' => 'poll-option poll-option-detail', 'id' => 'horizontal-' . $option->id]) !!}
                                                    @else
                                                        {!! Form::radio('option[]', $option->id, false, ['onClick' => 'voted("' . $option->id  .'","horizontal")', 'class' => 'poll-option poll-option-detail', 'id' => 'horizontal-' . $option->id]) !!}
                                                    @endif
                                                @endif
                                                <div class="option-name">
                                                    <p><img src="{{ $option->showImage() }}" width="50px" height="50px" style="float: left; cursor: pointer" onclick="showModelImage('{{ $option->showImage() }}')">
                                                    <span style="display: block; margin-left: 60px">{{ $option->name ? $option->name : " " }}</span>
                                                    </p>
                                                </div>
                                            </li>
                                        @endforeach
                                    </div>
                                </div>
                                <!-- VOTE OPTION VERTICAL-->
                                <div id="vertical" class="tab-pane fade in" style="clear: both">
                                    @if ($isSetIp && (auth()->check() && $isUserVoted || $isSetIp && !auth()->check() && $isParticipantVoted))
                                        <div class="alert alert-warning">
                                            <span class='glyphicon glyphicon-warning-sign'></span>
                                            {{ trans('polls.message_vote_one_time') }}
                                        </div>
                                    @endif
                                    <div class="col-lg-12" style="padding-right: 0; padding-left: 0; max-height: 400px; overflow-y: scroll">
                                        @foreach ($poll->options as $option)
                                            <div class="col-lg-4" style="padding-left: 5px; padding-right: 5px">
                                                <div class="panel panel-default" id="{{ $option->id }}">
                                                    <div class="panel-heading parent-vote"  onclick="voted('{{ $option->id }}', 'horizontal')">
                                                        @if ($isSetIp && (auth()->check() && ! $isUserVoted || $isSetIp && !auth()->check() && ! $isParticipantVoted) || ! $isLimit && ! $poll->isClosed() && ! $isSetIp)
                                                            @if ($poll->multiple == trans('polls.label.multiple_choice'))
                                                                {!! Form::checkbox('option_vertical[]', $option->id, false, ['onClick' => 'voted("' . $option->id  .'","vertical")', 'class' => 'poll-option', 'id' => 'vertical-' . $option->id]) !!}
                                                            @else
                                                                {!! Form::radio('option_vertical[]', $option->id, false, ['onClick' => 'voted("' . $option->id  .'","vertical")', 'class' => 'poll-option', 'id' => 'vertical-' . $option->id]) !!}
                                                            @endif
                                                        @endif
                                                        @if (!$isHideResult || Gate::allows('administer', $poll))
                                                            <span class="badge result-poll" style="float: right; display: none">{{ $option->countVotes() }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="panel-body" style="height:120px; overflow-y: scroll">
                                                        <p><img src="{{ $option->showImage() }}" onclick="showModelImage('{{ $option->showImage() }}')" width="32px" height="32px" style="cursor: pointer">
                                                        {{ $option->name ? $option->name : " " }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer">
                            @if ($isSetIp && (auth()->check() && ! $isUserVoted || $isSetIp && !auth()->check() && ! $isParticipantVoted) || ! $isLimit && ! $poll->isClosed() && ! $isSetIp)
                                {!! Form::hidden('pollId', $poll->id) !!}
                                {!! Form::hidden('isRequiredEmail', $isRequiredEmail) !!}
                                <div class="row">
                                    <div class="col-lg-5">
                                        <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-user" aria-hidden="true"></i>
                                    </span>
                                            {!! Form::text('nameVote', auth()->check() ? auth()->user()->name : null, ['class' => 'form-control nameVote', 'placeholder' => trans('polls.placeholder.enter_name')]) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="input-group {{ ($isRequiredEmail) ? "required" : "" }}">
                                    <span class="input-group-addon">
                                        <i class="glyphicon glyphicon-envelope" aria-hidden="true"></i>
                                    </span>
                                            {!! Form::email('emailVote', auth()->check() ? auth()->user()->email : null, ['class' => 'form-control emailVote', 'placeholder' => trans('polls.placeholder.email')]) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <span class="input-group-btn" data-message-email="{{ trans('polls.message_email') }}" data-url="{{ url('/check-email') }}" data-message-required-email="{{ trans('polls.message_required_email') }}" data-message-validate-email="{{ trans('polls.message_validate_email') }}" data-is-required-email="{{ $isRequiredEmail ? 1 : 0 }}">
                                        {{ Form::button(trans('polls.vote'), ['class' => 'btn btn-success btn-vote', !$isUserVoted ? 'disabled' : '']) }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- MODAL VIEW IMAGE-->
                    <div id="modalImageOption" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">{{ trans('polls.image_preview') }}</h4>
                                </div>
                                <div class="modal-body">
                                    <img src="#" id="imageOfOptionPreview" style="display: block; margin: 0 auto">
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
                    <div class="col-lg-12">
                        <h4 style="word-wrap: break-word">
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
                            <span style="margin-right: 20px">
                                <i class="fa fa-clock-o" aria-hidden="true"></i> {{ $poll->created_at }}
                            </span>
                            <span>
                                <i class="fa fa-user" aria-hidden="true"></i>
                                @if ($poll->user_id)
                                    <label style="color: blue;">{{ $poll->user->name }}</label>
                                @else
                                    <label style="color: blue;">{{ $poll->name }}</label>
                                @endif
                            </span>
                            @if ($poll->location)
                                <span style="float: right; cursor: pointer" data-placement="top" data-toggle="tooltip" title="{{ $poll->location }}">
                                    <i class="fa fa-map-marker" aria-hidden="true"></i> {{ str_limit($poll->location, 20) }}
                                </span>
                            @endif
                        </p>
                        <div class="form-group col-lg-12" style="padding: 0">
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
                        <div class="panel panel-default" style="border-radius: 0; border-color: darkcyan">
                            <div class="panel-heading" style="border-radius: 0; background: darkcyan; border-color: darkcyan; color: white">
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
                                        <hr style="border: 1px solid darkcyan">
                                    </div>
                                @endif
                                <div class="col-lg-12 comment" data-label-add-comment = "{{ trans('polls.add_comment') }}" data-label-hide="{{ trans('polls.hide') }}" style="padding: 0">
                                    {!! Form::open(['route' => 'comment.store', 'class' => 'form-horizontal', 'id' => 'form-comment']) !!}
                                    <div>
                                        <label class="message-validate comment-name-validate"> </label>
                                        <label class="message-validate comment-content-validate"></label>
                                    </div>
                                    <div class="col-md-6  comment">
                                        {!! Form::text('name', auth()->check() ? auth()->user()->name : null, ['class' => 'form-control', 'id' => 'name' . $poll->id, 'placeholder' => trans('polls.placeholder.full_name')]) !!}
                                    </div>
                                    <div class="col-md-10 comment" data-poll-id="{{ $poll->id }}" data-user="{{ auth()->check() ? auth()->user()->name : '' }}" data-comment-name="{{ trans('polls.comment_name') }}" data-comment-content="{{ trans('polls.comment_content') }}">
                                        {!! Form::textarea('content', null, ['class' => 'form-control', 'rows' => config('settings.poll.comment_row'), 'placeholder' => trans('polls.placeholder.comment'), 'id' => 'content' . $poll->id]) !!}
                                        {{ Form::button(trans('polls.save_comment'), ['type' => 'submit', 'class' => 'btn addComment']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- POLL RESULT -->
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
                            @if ($optionRateBarChart != "null")
                                <div class="panel-heading">
                                    <ul class="nav nav-pills">
                                        <li class="active">
                                            <a data-toggle="tab" href="#table">
                                                <i class="fa fa-table" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a data-toggle="tab" href="#barChart">
                                                <i class="fa fa-bar-chart" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a data-toggle="tab" href="#pieChart">
                                                <i class="fa fa-pie-chart" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                            <div class="panel-body">
                                <div class="tab-content">
                                    <!-- TABLE RESULT -->
                                    <div id="table" class="tab-pane fade in active">
                                        <div class="col-lg-12" style="padding: 0; margin-bottom: 20px">
                                            <!-- SHOW DETAIL VOTE -->
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal" style="float: right;
    background: darkcyan;
    border-color: darkcyan;
    border-radius: 0;
    box-shadow: 2px 2px 2px black;">
                                                <span class="glyphicon glyphicon-eye-open"></span>
                                                {{ trans('polls.show_vote_details') }}
                                            </button>
                                        </div>

                                        <div class="modal fade" id="myModal" role="dialog">
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
                                                                                {{--<img class="img-option" src="{{ $option->showImage() }}">--}}
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
                                        </div>
                                        <div class="col-lg-12" style="clear: both; max-height: 400px; overflow-x: hidden; overflow-y: scroll">
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
                                                        <td style="width: 720px">
                                                            <img src="{{ asset($data['image']) }}" width="50px" height="50px" style="float: left">
                                                            <p style="margin-left: 60px; display: block">{{ $data['name'] }}</p>
                                                        </td>
                                                        <td><span class="badge">{{ $data['numberOfVote'] }}</span></td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- MODEL VOTE CHART-->
                                    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                                    @if ($optionRateBarChart)
                                        <div class="tab-pane fade" id="pieChart" role="dialog">
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
                                                        var options = {'width': 700, 'height': 400};
                                                        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
                                                        chart.draw(data, options);
                                                    }
                                                </script>
                                                <div id="chart_div"></div>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($optionRateBarChart)
                                    <div class="tab-pane fade" id="barChart" role="dialog">
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
                                                    var options = {'width': 700, 'height': 400, 'is3D': true};
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
                        <div class="alert alert-warning">
                            <span class='glyphicon glyphicon-warning-sign'></span>
                            {{ trans('polls.hide_result_message') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
