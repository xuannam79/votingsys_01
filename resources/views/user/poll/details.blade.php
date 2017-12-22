@extends('layouts.app')
@push('detail-style')
<!-- ---------------------------------
        Style of detail poll
---------------------------------------->
    <!-- DATETIME PICKER: time close of poll -->
    {!! Html::style('bower/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css') !!}

    {!! Html::style('bower/sweetalert/dist/sweetalert.css') !!}

    <!-- SOCKET IO -->
    {!! Html::script('bower/socket.io-client/dist/socket.io.min.js') !!}

    <!-- THEME QUILL EDITOR: bubble -->
    {!! Html::style('bower/quill/quill.bubble.css') !!}

    <!-- THEME QUILL EDITOR: core -->
    {!! Html::style('bower/quill/quill.core.css') !!}

@endpush
@section('meta')
    <meta property="fb:app_id" content="708640145978561"/>
    <meta property="og:type" content="article" />
    <meta property="og:url" content="{{ $poll->getUserLink() }}" />
    <meta property="og:title" content="{{ $poll->title }}" />
    <meta property="og:description" content="{{ $poll->description }}" />
    <meta property="og:image" content="{{ asset('/uploads/images/vote.png') }}" />
@endsection
@section('content')
    <!-- START: Frame Upload Image By Link Or Upload File-->
    <div class="modal fade frame-upload-image-mobile-js" tabindex="-1" role="dialog" id="frame-upload-image">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="sub-tab">
                        <div class="sel">{{ trans('polls.label_for.add_picture_option') }}</div>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="win-img">
                        <div class="photo-tb">
                            <div class="row">
                                <div class="col col-md-9 photo-tb-url">
                                    <div class="add-link-image-group">
                                        {!! Form::text('urlImageTemp', null, [
                                            'class' => 'photo-tb-url-txt form-control',
                                            'placeholder' => trans('polls.message_client.empty_link_image'),
                                        ]) !!}
                                        <span class="add-image-by-link label-info">
                                            {{ trans('polls.button.add') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col col-md-3 photo-tb-ui">
                                    <div class="photo-tb-btn photo-tb-upload">
                                        <span class="fa fa-camera"></span>
                                        <p>{{ trans('polls.button.upload') }}</p>

                                    </div>
                                    <div class="photo-tb-btn photo-tb-del">
                                        <span class="fa fa-times"></span>
                                        <p>{{ trans('polls.button.delete') }}</p>
                                    </div>
                                </div>
                            </div>
                            {!! Form::file('fileImageTemp', ['class' => 'fileImgTemp file']) !!}
                        </div>
                        <div class="has-error">
                            <div class="help-block error-win-img" id="title-error"></div>
                        </div>
                        <div class="photo-preivew">
                            <img src="" class="img-pre-option">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-yes">
                        {{ trans('polls.button.okay') }}
                    </button>
                    <button type="button" class="btn btn-no" data-dismiss="modal">
                        {{ trans('polls.button.cancel') }}
                    </button>
                </div>
            </div>
      </div>
    </div>
    <!-- END: Frame Upload Image By Link Or Upload File-->

    @if($isEditVoted && !$isLimit && !$poll->isClosed() && !$isTimeOut)
        <!-- START: Frame Upload Image By Link Or Upload File [Edit Voting]-->
        <div class="modal fade" tabindex="-1" role="dialog" id="frame-edit-poll">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="sub-tab">
                            <div class="sel">{{ $poll->title }}</div>
                        </div>
                    </div>

                    <div class="modal-body">
                        {!! Form::open([
                            'action' => ['User\VoteController@update', $poll->id],
                            'id' => 'edit-voted-content',
                            'method' => 'PATCH',
                            'files' => true,
                        ]) !!}
                            <div class="poll-option">

                            </div>
                            <div class="error_option"></div>
                        {!! Form::close() !!}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-edit-submit">
                            {{ trans('polls.button.okay') }}
                        </button>
                        <button type="button" class="btn btn-no" data-dismiss="modal">
                            {{ trans('polls.button.cancel') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- END: Frame Upload Image By Link Or Upload File [Edit Voting]-->

        <!-- START: Model form edit voting-->
        <div class="modal" tabindex="-1" role="dialog" id="frame-upload-image-edit">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="sub-tab">
                            <div class="sel">{{ trans('polls.label_for.add_picture_option') }}</div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="win-img">
                            <div class="photo-tb">
                                <div class="row">
                                    <div class="col col-md-9 photo-tb-url">
                                        <div class="add-link-image-group">
                                            {!! Form::text('urlImageTemp', null, [
                                                'class' => 'photo-tb-url-txt-edit form-control',
                                                'placeholder' => trans('polls.message_client.empty_link_image'),
                                            ]) !!}
                                            <span class="add-image-by-link-edit label-info">
                                                {{ trans('polls.button.add') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col col-md-3 photo-tb-ui">
                                        <div class="photo-tb-btn photo-tb-upload-edit">
                                            <span class="fa fa-camera"></span>
                                            <p>{{ trans('polls.button.upload') }}</p>

                                        </div>
                                        <div class="photo-tb-btn photo-tb-del-edit">
                                            <span class="fa fa-times"></span>
                                            <p>{{ trans('polls.button.delete') }}</p>
                                        </div>
                                    </div>
                                </div>
                                {!! Form::file('fileImageTemp', ['class' => 'fileImgTempEdit file']) !!}
                            </div>
                            <div class="has-error">
                                <div class="help-block error-win-img-edit" id="title-error"></div>
                            </div>
                            <div class="photo-preivew">
                                <img src="" class="img-pre-option-edit">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-yes-edit">
                            {{ trans('polls.button.okay') }}
                        </button>
                        <button type="button" class="btn btn-no" data-dismiss="modal">
                            {{ trans('polls.button.cancel') }}
                        </button>
                    </div>
                </div>
          </div>
        </div>
        <!-- END: Model form edit voting-->
    @endif

    <!-- START: List voters-->
    <div class="modal fade voters-modal-mobile" tabindex="-1" role="dialog" id="voters-modal">
        <div class="modal-dialog modal-list-user-mobile" role="document">
            <div class="modal-content">
                <div class="modal-body" id="result-voters">

                </div>
            </div>
        </div>
    </div>
    <!-- END: List voters-->

    <div class="hide_vote_socket"
         data-host="{{ config('app.key_program.socket_host') }}"
         data-port="{{ config('app.key_program.socket_port') }}">
    </div>
    <div class="hide_chart" data-chart="{{ $optionRateBarChart }}"
                            data-name-chart="{{ $nameOptions }}"
                            data-pie-chart="{{ $dataToDrawPieChart }}"
                            data-title-chart="{{ $poll->title }}"
                            data-font-size="{{ $fontSize }}"
                            data-has-image="{{ $isHaveImages }}"></div>
    <div class="container container-mobile">
        <div class="row">
            <div class="loader"></div>
            @include('noty.message')
            @include('errors.errors')
            <div id="voting_wizard" class="col-lg-10 col-lg-offset-1
                                            col-md-10 col-md-offset-1
                                            col-sm-10 col-sm-offset-1
                                            wrap-poll">
                <div class="navbar panel panel-default panel-detail-poll panel-detail-poll-mobile">
                    <div class="panel-body navbar-inner col-lg-12 panel-body-detail-poll">
                        <div class="col-lg-6 col-lg-offset-3
                                    col-md-6 col-md-offset-3
                                    col-sm-8 col-sm-offset-2
                                    col-xs-8 col-xs-offset-2
                                    panel-heading panel-test
                                    tag-info-mobile">
                            <ul class="center-mobile">
                                <li><a href="#vote" data-toggle="tab">{{ trans('polls.nav_tab_edit.voting') }}</a></li>
                                <li class="none-tag-mobile"><a href="#info" data-toggle="tab">{{ trans('polls.nav_tab_edit.info') }}</a></li>
                                @if (Session::has('isVotedSuccess') && Session::get('isVotedSuccess'))
                                    <li class="active"><a href="#result" data-toggle="tab">{{ trans('polls.nav_tab_edit.result') }}</a></li>
                                @else
                                    <li><a href="#result" data-toggle="tab">{{ trans('polls.nav_tab_edit.result') }}</a></li>
                                @endif
                            </ul>
                        </div>
                        <div class="col-lg-3 col-auth-detail none-tag-mobile">
                            @if (auth()->user() && auth()->user()->id == $poll->user_id)
                                <a href="{{ $poll->getAdminLink() }}" class="btn btn-darkcyan btn-primary btn-xs btn-auth-detail">
                                    <span class="glyphicon glyphicon-pencil"></span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="hide-vote-details" data-poll-id="{{ $poll->id }}"></div>
                <div class="hide-vote" data-poll-id="{{ $poll->id }}" data-is-owner-poll="{{ $isOwnerPoll }}"></div>
                @if (session('message'))
                    <div class="alert alert-info message-infor-detail alert-dismissable">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <i class="icon fa fa-info"></i> {!! session('message') !!}
                    </div>
                @endif
                @if (isset($message))
                    <div class="alert alert-info message-infor-detail alert-dismissable">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <i class="icon fa fa-info"></i> {!! session('message') !!}
                    </div>
                @endif
                <div class="tab-content">
                    <div class="tab-pane" id="vote">
                        @if ($isLimit)
                            <div class="alert alert-warning alert-poll-set-ip">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <span class='glyphicon glyphicon-warning-sign'></span>
                                {{ trans('polls.reach_limit') }}
                            </div>
                        @endif
                        @if ($isTimeOut)
                            <div class="alert alert-warning alert-poll-set-ip">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <span class='glyphicon glyphicon-warning-sign'></span>
                                {{ trans('polls.message_poll_time_out') }}
                            </div>
                        @endif
                        {!! Form::open(['route' => 'vote.store','id' => 'form-vote', 'files' => true]) !!}
                            <!-- VOTE OPTION -->
                            <div class="panel panel-default panel-vote-option">
                                <div class="panel-body panel-body-vote-option vote-option-mobile">
                                    <div class="col-lg-12 title-poll-mobile">
                                        <h4>{{ $poll->title }}</h4>
                                        <p class="description-poll">{!! cleanText($poll->description) !!}</p>
                                        <label class="poll-count poll-count-mobile">
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
                                    <div class="tab-content tab-content-detail box-style-option">
                                        <div class="col-lg-12 none-tag-mobile">
                                            <div class="vote-style" data-option="{{ $viewOption }}">
                                                <ul class="nav nav-pills poll-tabs">
                                                    @if($isEditVoted && !$isLimit && !$poll->isClosed() && !$isTimeOut)
                                                        <li class="s-tab">
                                                            <a href="javascript:void(0)" class="btn-vote-style edit-each-option tab-link">
                                                                <i class="fa fa-pencil" aria-hidden="true"></i>
                                                            </a>
                                                        </li>
                                                    @endif
                                                    @if (!$isHideResult || Gate::allows('administer', $poll))
                                                        <!-- <li class="s-tab">
                                                            <a id="hide" class="btn-show-result-poll btn-vote-style tab-link" onclick="showResultPoll()">
                                                                <i class="fa fa-eye-slash li-show-result-poll" aria-hidden="true"></i>
                                                            </a>
                                                        </li> -->
                                                    @endif
                                                    <li class="active s-tab">
                                                        <a data-toggle="tab" href="#horizontal" class="btn-vote-style tab-link">
                                                            <i class="fa fa-bars" aria-hidden="true"></i>
                                                            <span class="fa-bar-moblie">{{ trans('polls.button.horizontal') }}</span>
                                                        </a>
                                                    </li>
                                                    <li class="s-tab">
                                                        <a data-toggle="tab" href="#timeline" class="btn-vote-style tab-link">
                                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                                            <span class="fa-bar-moblie">{{ trans('polls.button.timeline') }}</span>
                                                        </a>
                                                    </li>
                                                    <li class="s-tab">
                                                        <a data-toggle="tab" href="#vertical" class="btn-vote-style tab-link">
                                                            <i class="fa fa-th" aria-hidden="true"></i>
                                                            <span class="fa-bar-moblie">{{ trans('polls.button.vertical') }}</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                                <div class="hl animated"></div>
                                            </div>
                                        </div>

                                        <!-- VOTE OPTION HORIZONTAL-->
                                        <div id="horizontal" class="tab-pane fade in active vote-style-detail clearfix">
                                            <div class="col-lg-12 horizontal-overflow vote-content-mobile">
                                                @foreach ($poll->options as $option)
                                                    <li class="list-group-item parent-vote li-parent-vote perform-option clearfix list-option-{{ $option->id }} {{ $poll->haveDetail() || $isHaveImages ? 'is-description' : 'not-description' }}"
                                                        onclick="voted('{{ $option->id }}', 'horizontal')">
                                                        <div class="option-info pull-left">
                                                            @if (!$isLimit && !$poll->isClosed() && !$isTimeOut)
                                                                @if ($poll->multiple == trans('polls.label.multiple_choice'))
                                                                    <div class="checkbox checkbox-primary checkbox-primary-mobile">
                                                                        {!!
                                                                            Form::checkbox('option[]', $option->id, false, [
                                                                                'onClick' => 'voted("' . $option->id  . '", "horizontal")',
                                                                                'class' => ($isHaveImages) ? 'poll-option-detail' : 'poll-option-detail-not-image',
                                                                                'id' => 'horizontal-' . $option->id
                                                                            ])
                                                                        !!}
                                                                @else
                                                                    <div class="radio radio-primary">
                                                                        {!!
                                                                            Form::radio('option[]', $option->id, false, [
                                                                                'onClick' => 'voted("' . $option->id  . '", "horizontal")',
                                                                                'class' => ($isHaveImages) ? 'poll-option-detail' : 'poll-option-detail-not-image',
                                                                                'id' => 'horizontal-' . $option->id
                                                                            ])
                                                                        !!}
                                                                @endif
                                                                    <label class="content-option-choose">
                                                                        <span>
                                                                            @if ($isHaveImages)
                                                                                <a class="media-image pick-media-image-mobile none-in-laptop" href="javascript:void(0)">
                                                                                    <div class="image-frame image-frame-mobile">
                                                                                        <div class="image-ratio">
                                                                                            <img src="{{ $option->showImage() }}" class="thumbOption image-option-choose" />
                                                                                        </div>
                                                                                        <span class="cz-label label-new">
                                                                                            {{ trans('polls.label_for.option_image') }}
                                                                                        </span>
                                                                                    </div>
                                                                                </a>
                                                                            @endif
                                                                            {{ $option->name ? $option->name : '' }}
                                                                        </span>
                                                                    </label>
                                                                    <br>
                                                                </div>
                                                            @else
                                                                @php
                                                                    $hideChoose = true;
                                                                @endphp
                                                                <p class="content-option-choose">{{ $option->name ? $option->name : '' }}</p>
                                                            @endif
                                                        </div>
                                                        <div class="voters-info pull-right voters-info-mobile">
                                                            @if (!$isHideResult || Gate::allows('administer', $poll))
                                                                <div class="voters clearfix result-poll {{ isset($hideChoose) ? 'voters-fix' : '' }} result-poll-mobile">
                                                                    @foreach (array_slice($listVoter[$option->id], 0, config('settings.limit_voters_option')) as $voter)
                                                                        <div class="voter-avatar voter-avatar-mobile" data-toggle="tooltip"
                                                                            data-placement="{{ $loop->parent->last ? 'top' : 'bottom'}}"
                                                                            title="{{ $voter['name'] }}">
                                                                            <img src="{{ $voter['avatar'] }}">
                                                                        </div>
                                                                    @endforeach
                                                                    @if ($option->countVotes() > config('settings.limit_voters_option'))
                                                                        <div class="voter-avatar voter-avatar-mobile">
                                                                            <div class="hidden-counter"
                                                                                data-url-modal-voter="{{ action('User\VoteController@getModalOptionVoters', $option->id) }}">
                                                                                <span>+{{ $option->countVotes() - config('settings.limit_voters_option') }}</span>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                        @if ($option->description)
                                                            @php
                                                                $haveShowMore = $option->paragraphTimes()
                                                            @endphp
                                                            <div class="clearfix none-in-laptop"></div>
                                                            <div class="des-child-option des-child-option-mobile none-in-laptop">
                                                                <span class="item-description-icon">
                                                                    <i class="fa fa-quote-right" aria-hidden="true"></i>
                                                                </span>
                                                                <div class="description-body {{ $haveShowMore ? 'show-more show-more-mobile' : ''}}">
                                                                    {!! $option->description !!}
                                                                </div>
                                                                @if ($haveShowMore)
                                                                    <button type="button" class="btn-show-more btn-show-more-mobile btn-show-more-mobile-js">
                                                                        <span>{{ trans('polls.message_client.show_more') }}</span>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </li>
                                                    @if ($isHaveImages)
                                                        <!--START: Win-Frame Add Image -->
                                                        <div class="box-media-image-option
                                                            image-option-detail
                                                            {{ isset($hideChoose) ? 'image-option-detail-fix' : '' }}
                                                            none-tag-mobile">
                                                            <a class="media-image pick-media-image" href="javascript:void(0)">
                                                                <div class="image-frame">
                                                                    <div class="image-ratio">
                                                                        <img src="{{ $option->showImage() }}" class="thumbOption image-option-choose" />
                                                                    </div>
                                                                    <span class="cz-label label-new">
                                                                        {{ trans('polls.label_for.option_image') }}
                                                                    </span>
                                                                </div>
                                                            </a>
                                                        </div>
                                                        <!--END: Win-Frame Add Image -->
                                                    @endif
                                                    @if ($option->description)
                                                        @php
                                                            $haveShowMore = $option->paragraphTimes()
                                                        @endphp
                                                        <div class="clearfix none-tag-mobile"></div>
                                                        <div class="des-child-option none-tag-mobile">
                                                            <span class="item-description-icon">
                                                                <i class="fa fa-quote-right" aria-hidden="true"></i>
                                                            </span>
                                                            <div class="description-body {{ $haveShowMore ? 'show-more' : ''}}">
                                                                {!! $option->description !!}
                                                            </div>
                                                            @if ($haveShowMore)
                                                                <button type="button" class="btn-show-more">
                                                                    <span>{{ trans('polls.message_client.show_more') }}</span>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                        <!--END: VOTE OPTION HORIZONTAL-->

                                        <!-- VOTE OPTION VERTICAL-->
                                        <div id="vertical" class="tab-pane fade in vote-style-detail">
                                            <div class="col-lg-12 vertical-overflow">
                                                @foreach ($poll->options as $option)
                                                    <div class="col-lg-4 vertical-option">
                                                        <div class="panel panel-default" id="{{ $option->id }}">
                                                            <div class="panel-heading parent-vote panel-heading-vertical"  onclick="voted('{{ $option->id }}', 'horizontal')">
                                                                @if (!$isLimit && !$poll->isClosed() && !$isTimeOut)
                                                                    @if ($poll->multiple == trans('polls.label.multiple_choice'))
                                                                        {!!
                                                                            Form::checkbox('option_vertical[]', $option->id, false, [
                                                                                'onClick' => 'voted("' . $option->id  .'","vertical")',
                                                                                'id' => 'vertical-' . $option->id
                                                                            ])
                                                                        !!}
                                                                    @else
                                                                        {!!
                                                                            Form::radio('option_vertical[]', $option->id, false, [
                                                                                'onClick' => 'voted("' . $option->id  .'","vertical")',
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
                                            </div>
                                        </div>
                                        <!--END: VOTE OPTION VERTICAL-->

                                        <!--START: Show Option With Time line  -->
                                        <div id="timeline" data-get-cookie= "{{ action('User\ParticipantController@getCookie')}}"
                                            class="tab-pane fade vote-style-detail">
                                            @include('layouts.options.timeline')
                                        </div>
                                        <!--END: Show Option With Time line -->

                                        @if ($isAllowAddOption && !$isLimit && !$poll->isClosed() && !$isTimeOut)
                                            @include('user.poll.option_adding')
                                        @endif
                                    </div>
                                </div>
                                <div class="message-validation"></div>
                                <div class="panel-footer">
                                    @if (!$isLimit && !$poll->isClosed() && !$isTimeOut)
                                        {!! Form::hidden('pollId', $poll->id) !!}
                                        <div class="row">
                                            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5 col-xs-name-vote name-vote-mobile">
                                                <div class="input-group  {{ ($isRequiredName || $isRequiredNameAndEmail) ? "required" : "" }}">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-user" aria-hidden="true"></i>
                                                    </span>
                                                    {!!
                                                        Form::text('nameVote', auth()->check() ? auth()->user()->name : null, [
                                                            'class' => 'form-control nameVote',
                                                            'readonly' => auth()->check() && auth()->user()-> haveWsmAction() ? 'readonly' : false,
                                                            'placeholder' => trans('polls.placeholder.enter_name')
                                                        ])
                                                    !!}
                                                </div>
                                            </div>
                                            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5 col-xs-email-vote email-vote-mobile">
                                                <div class="input-group {{ ($isRequiredEmail || $isRequiredNameAndEmail) ? "required" : "" }}">
                                                    <span class="input-group-addon">
                                                        <i class="glyphicon glyphicon-envelope" aria-hidden="true"></i>
                                                    </span>
                                                    {!!
                                                        Form::email('emailVote', auth()->check() ? auth()->user()->email : null, [
                                                            'class' => 'form-control emailVote',
                                                            'readonly' => auth()->check() && auth()->user()-> haveWsmAction() ? 'readonly' : false,
                                                            'placeholder' => trans('polls.placeholder.email')
                                                        ])
                                                    !!}
                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 col-btn-xs-vote btn-vote-mobile">
                                                <span class="input-group-btn js-data-validate"
                                                    data-message-email="{{ trans('polls.message_email') }}"
                                                    data-id-poll="{{ $poll->id }}"
                                                    data-url="{{ url('/check-email') }}"
                                                    data-url-check-exist-email={{ action('User\VoteController@ajaxCheckIfExistEmailVote') }}
                                                    data-message-email-exists= "{{ trans('polls.message_client.email_exists') }}"
                                                    data-message-error-occurs= "{{ trans('polls.message_client.error_occurs') }}"
                                                    data-message-validate-email="{{ trans('polls.message_validate_email') }}"
                                                    data-message-required-email="{{ trans('polls.message_required_email') }}"
                                                    data-message-required-name="{{ trans('polls.message_validate_name') }}"
                                                    data-message-required-name-and-email="{{ trans('polls.message_validate_name_and_email') }}"
                                                    data-message-required-type-email="{{ trans('polls.required_type_email', ['type' => $typeEmail]) }}"
                                                    data-is-required-email="{{ $isRequiredEmail ? 1 : 0 }}"
                                                    data-is-required-name="{{ $isRequiredName ? 1 : 0 }}"
                                                    data-is-not-same-email="{{ $isNoTheSameEmail ? 1 : 0 }}"
                                                    data-is-accecpt-type-mail="{{ $isAccecptTypeMail ? 1 : 0 }}"
                                                    data-type-email="{{ $typeEmail }} "
                                                    data-is-required-name-and-email="{{ $isRequiredNameAndEmail ? 1 : 0 }}"
                                                    data-vote-limit-name="{{ trans('polls.validation.name.max') }}">
                                                    {{ Form::button(trans('polls.vote'), ['class' => 'btn btn-success btn-vote', 'disabled']) }}
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
                    <div class="tab-pane none-tag-mobile" id="info">
                        <div class="message-validation"></div>
                        <div class="panel panel-default panel-vote-option">
                            <div class="panel-body panel-body-vote-option">
                            <!-- POLL INFO -->
                                <div class="col-lg-12 poll-info">
                                    <h4>{{ $poll->title }}</h4>
                                    <p class="description-poll">{!! cleanText($poll->description) !!}</p>
                                    <p class="poll-info-not-xs">
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

                                    <div class="poll-info-xs">
                                        <p>
                                            <i class="fa fa-clock-o" aria-hidden="true"></i> {{ $poll->created_at }}
                                        </p>
                                        <p>
                                            <i class="fa fa-user" aria-hidden="true"></i>
                                            @if ($poll->user_id)
                                                <label class="label-poll-info">{{ $poll->user->name }}</label>
                                            @else
                                                <label class="label-poll-info">{{ $poll->name }}</label>
                                            @endif
                                        </p>
                                        @if ($poll->location)
                                            <p data-placement="top" data-toggle="tooltip" title="{{ $poll->location }}">
                                                <i class="fa fa-map-marker" aria-hidden="true"></i>
                                                {{ str_limit($poll->location, config('settings.str_limit.location')) }}
                                            </p>
                                        @endif
                                        <p class="count-participant">
                                            <span class="fa fa-users"></span> {{ $countParticipantsVoted }}
                                        </p>
                                        <p class="comment-count">
                                            <span class="glyphicon glyphicon-comment"></span> {{ $poll->countComments() }}
                                        </p>
                                        @if ($poll->date_close)
                                            <p>
                                                <span class="fa fa-times-circle"></span> <i>{{ $poll->date_close }}</i>
                                            </p>
                                        @endif
                                    </div>

                                </div>
                            <!-- COMMENT -->
                                <div class="col-md-12" id="panel-comment">
                                    <div class="panel panel-default panel-darkcyan">
                                        <div class="panel-heading panel-heading-darkcyan">
                                            <h4>
                                                <span class="comment-count">{{ $poll->countComments() }} </span>
                                                {{ trans('polls.comments') }}
                                                <span data-label-show-comment = "<i class='fa fa-eye' aria-hidden='true'></i>"
                                                      data-label-hide="<i class='fa fa-eye-slash' aria-hidden='true'></i>">
                                                <button class="btn btn-warning show btn-xs" id="show-hide-list-comment">
                                                    <i class="fa fa-eye-slash" aria-hidden="true"></i>
                                                </button>
                                            </span>
                                            </h4>
                                        </div>
                                        <div class="panel-body">
                                            <div class="hide js-remove-comment" data-route="{{ url('user/comment') }}" data-confirm-remove="{{ trans('polls.confirmRemove') }}">
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
                                                            <span class="comment-text">{{ $comment->content }}</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @if (count($poll->comments))
                                                <div class="col-lg-12 hr-comment">
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
                                                    <div class="col-md-6 comment">
                                                        {!!
                                                            Form::text('name', auth()->check() ? auth()->user()->name : null, [
                                                                'class' => 'form-control comment-info-name',
                                                                'id' => 'name' . $poll->id,
                                                                'placeholder' => trans('polls.placeholder.full_name'),
                                                            ])
                                                        !!}
                                                    </div>
                                                    <div class="col-md-10 comment"
                                                        data-poll-id="{{ $poll->id }}"
                                                        data-user="{{ auth()->check() ? auth()->user()->name : '' }}"
                                                        data-comment-name="{{ trans('polls.comment_name') }}"
                                                        data-comment-content="{{ trans('polls.comment_content') }}"
                                                        data-comment-limit-name="{{ trans('polls.validation.name.max') }}"
                                                        data-comment-limit-content="{{ trans('polls.validation.content.max') }}">
                                                        {!!
                                                            Form::textarea('content', null, [
                                                                'class' => 'form-control comment-info-content',
                                                                'rows' => config('settings.poll.comment_row'),
                                                                'placeholder' => trans('polls.placeholder.comment'),
                                                                'id' => 'content' . $poll->id,
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
                        </div>
                    </div>
                    <!-- POLL RESULT -->
                    <!-- if voted -> tab result(active) -->
                    @if (Session::has('isVotedSuccess') && Session::get('isVotedSuccess'))
                        @php
                            Session::forget('isVotedSuccess');
                        @endphp
                        <div class="tab-pane active vote-option-mobile" id="result">
                    @else
                        <div class="tab-pane" id="result">
                    @endif
                            <div class="panel panel-default panel-vote-option">
                                @if (!$isHideResult || Gate::allows('administer', $poll))
                                    <div class="bar-pie-chart">
                                        @if ($optionRateBarChart != "null")
                                            <div class="panel-heading panel-result-detail vote-option-mobile">
                                                <ul class="nav nav-pills">
                                                    <li class="active">
                                                        <a data-toggle="tab" href="#table">
                                                            <i class="fa fa-table" aria-hidden="true"></i>
                                                        </a>
                                                    </li>
                                                    <li class="bar-chart-mobile">
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
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                <div class="panel-body panel-body-vote-option vote-option-mobile">
                                    @if (!$isHideResult || Gate::allows('administer', $poll))
                                    <!-- if have not vote -> hide tab style result -->
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

                                                <div class="modal fade model-show-details model-show-details-mobile" id="myModal" role="dialog">
                                                    @include('user.poll.vote_details_layouts')
                                                </div>
                                                <div class="col-lg-12 table-poll-result result-vote-poll result-vote-poll-mobile">
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


                                                    <table class="table table-hover none-tag-mobile">
                                                        <thead>
                                                            <tr>
                                                                <th class="no-mobile">{{ trans('polls.no') }}</th>
                                                                <th class="answer-mobile">{{ trans('polls.label.option') }}</th>
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
                                                                    <td class="no-mobile">{{ $key + 1 }}</td>
                                                                    <td class="{{ ($isHaveImages) ? 'td-poll-result' : '' }} answer-mobile">
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
                                                </div>
                                            </div>
                                            <!-- MODEL VOTE CHART-->
                                            @if ($optionRateBarChart)
                                                <div class="show-piechart tab-pane fade" id="pieChart" role="dialog">
                                                    <div class="col-lg-12 with-bar-mobile">
                                                        <!-- pie chart -->
                                                        <div id="chart_div" class="chart_div_mobile"></div>
                                                    </div>
                                                </div>
                                            @endif

                                            @if ($optionRateBarChart)
                                                <div class="show-barchart tab-pane fade" id="barChart" role="dialog">
                                                    <div class="col-lg-12">
                                                        <!-- bar chart -->
                                                        <div id="chart"></div>
                                                    </div>
                                                </div>
                                            @endif
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
    </div>

    <!-- Modal detail chart-->
    <div id="myModalChart" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title chart-detail-name"></h4>
                </div>
                <div class="modal-body">
                    <img src="#" class="chart-detail-image">
                </div>
            </div>
        </div>
    </div>
    <div class="js-date-close"
        data-date-close="{{ $poll->date_close ? strtotime($poll->date_close) : '' }}"
        data-link="{{ $linkUser }}">
    </div>
</div>
@endsection
@push('detail-scripts')

<!-- ---------------------------------
    Javascript of detail poll
---------------------------------------->
    <!-- FORM WINZARD: form step -->
    {!! Html::script('bower/twitter-bootstrap-wizard/jquery.bootstrap.wizard.js') !!}

    {!! Html::script('bower/quill/quill.min.js') !!}

    <!-- DATETIME PICKER: time close of poll -->
    {!! Html::script('/bower/moment/min/moment.min.js') !!}
    {!! Html::script('/bower/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') !!}

    {!! Html::script('bower/sweetalert/dist/sweetalert.min.js') !!}

    <!-- COMMENT -->
    {!! Html::script(elixir('js/comment.js')) !!}

    <!-- PLUGIN ADD IMAGE FOR OPTIONS -->
    {!! Html::script(elixir('js/jqAddImageOption.js')) !!}

    {!! Html::script('bower/waypoints/lib/jquery.waypoints.min.js') !!}

    <!-- POLL -->
    {!! Html::script(elixir('js/poll.js')) !!}

    <!-- VOTE -->
    {!! Html::script(elixir('js/vote.js')) !!}

    <!-- VOTE SOCKET-->
    {!! Html::script(elixir('js/voteSocket.js')) !!}

    <!-- SOCIAL: like, share -->
    {!! Html::script(elixir('js/shareSocial.js')) !!}


    <!-- HIGHCHART-->
    {!! Html::script('bower/highcharts/highcharts.js') !!}
    {!! Html::script('bower/highcharts/highcharts-3d.js') !!}

    <!-- CHART -->
    {!! Html::script(elixir('js/chart.js')) !!}


@endpush
