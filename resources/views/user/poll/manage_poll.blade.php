@extends('layouts.app')
@push('manage-style')

    <!-- ---------------------------------
                Style of manage poll
    ---------------------------------------->

    <!-- SWEET ALERT: alert message js -->
    {!! Html::style('bower/sweetalert/dist/sweetalert.css') !!}

    <!-- BOOTSTRAP SWITCH: setting of poll -->
    {!! Html::style('bower/bootstrap-switch/dist/css/bootstrap2/bootstrap-switch.min.css') !!}

    <!-- DATETIME PICKER: time close of poll -->
    {!! Html::style('bower/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css') !!}

    <!-- SOCKET IO -->
    {!! Html::script('bower/socket.io-client/dist/socket.io.min.js') !!}
@endpush
@section('content')
    <div class="hide_vote_socket"
         data-host="{{ config('app.key_program.socket_host') }}"
         data-port="{{ config('app.key_program.socket_port') }}">
    </div>
    <div class="hide_chart" data-chart="{{ $optionRateBarChart }}"
         data-name-chart="{{ $nameOptions }}"
         data-pie-chart="{{ $dataToDrawPieChart }}"
         data-title-chart="{{ $poll->title }}"
         data-font-size="{{ $fontSize }}"></div>
    <div class="hide"
        data-poll="{{ $data['jsonData'] }}"
        data-poll-id="{{ $poll->id }}" data-route="{{ url('user/poll') }}"
        data-edit-link-success="{{ trans('polls.edit_link_successfully') }}"
        data-edit-link-admin-success="{{ trans('polls.edit_link_admin_successfully') }}"
        data-link="{{ url('link') }}"
        data-route-link="{{ route('link-poll.store') }}"
        data-delete-participant="{{ trans('polls.confirm_delete_all_participant') }}"
        data-close-poll="{{ trans('polls.confirm_close_poll') }}"
        data-reopen-poll="{{ trans('polls.confirm_reopen_poll') }}"
        data-url-reopen-poll="{{ url('user/poll') }}"
        data-token-admin="{{ $tokenLinkAdmin }}"
        data-token-user="{{ $tokenLinkUser }}"
        data-url-admin="{{ url('/link') }}"
        data-link-check-date="{{ url('/check-date-close-poll') }}"
        data-location-route="{{ route('location.store') }}">
    </div>
    <div class="container">
        <div class="row">
            <span class="manage-poll-count-participant">{{ $countParticipantsVoted }}</span>
            <div class="hide-vote" data-poll-id="{{ $poll->id }}"></div>
            <div class="loader"></div>
            <div id="manager_poll_wizard" class="col-lg-10 col-lg-offset-1
                                                 col-md-10 col-md-offset-1
                                                 col-sm-10 col-sm-offset-1
                                                 well wrap-poll">
                <div class="navbar panel">
                    <div class="navbar-inner">
                        <div class="col-lg-6 col-lg-offset-3
                                    col-md-6 col-md-offset-3
                                    col-sm-8 col-sm-offset-2
                                    col-xs-8 col-xs-offset-2
                                    panel-heading panel-test {{ (Session::get('locale') == 'ja' && ! $countParticipantsVoted)
                                    ? 'panel-jp-manage-poll' : '' }}">
                            <ul>
                                <li><a href="#info" data-toggle="tab">{{ trans('polls.nav_tab_edit.info') }}</a></li>
                                <li><a href="#vote_detail" data-toggle="tab">{{ trans('polls.nav_tab_edit.result') }}</a></li>
                                <li><a href="#activity" data-toggle="tab">{{ trans('polls.nav_tab_edit.activity') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <span class="latest-token-user"></span>
                @if (Session::has('messages'))
                    <div class="alert alert-success alert-messages alert-dismissable">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <span class="glyphicon glyphicon-info-sign"></span> {!! Session::get('messages') !!}
                    </div>
                @endif
                <div class="tab-content">
                    @include('layouts.message')
                    <div class="tab-pane" id="info">
                        <a href="{{ url('/') . config('settings.email.link_vote') . $tokenLinkUser }}" target="_blank" class="menu-manager-info">
                            <i class="fa fa-link" aria-hidden="true"></i> {{ trans('polls.link_vote') }}
                        </a>
                        <a href="#" class="menu-manager-info" data-toggle="modal" data-target="#showOptionModal">
                            <i class="fa fa-list" aria-hidden="true"></i> {{ trans('polls.view_option') }}
                        </a>
                        <a href="#" class="menu-manager-info" data-toggle="modal" data-target="#showSettingModal">
                            <i class="fa fa-cog" aria-hidden="true"></i> {{ trans('polls.view_setting') }}
                        </a>
                        <p class="status_poll">{!! $poll->status !!}</p>
                        <hr class="hr-darkcyan">
                        @include('layouts.poll_info')
                    </div>

                    <!-- Modal Option-->
                    <div id="showOptionModal" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">{{ trans('polls.label.step_2') }}</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="row manager-modal-option">
                                        @foreach ($poll->options as $option)
                                            <div class="col-lg-12 {{ ($isHaveImages) ? 'option-detail-modal' : '' }}">
                                                @if ($isHaveImages)
                                                    <img src="{{ $option->showImage() }}">
                                                @endif
                                                <p>{{ $option->name }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Setting-->
                    <div id="showSettingModal" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">{{ trans('polls.label.step_3') }}</h4>
                                </div>
                                <div class="modal-body">
                                    @if ($settings)
                                        @foreach ($settings as $setting)
                                            @foreach($setting as $text => $value )
                                                <h4>{{ $text }}
                                                    @if ($text == trans('polls.label.setting.custom_link'))
                                                        <span class="label label-default">
                                                            <a href="{{ url('/') . config('settings.email.link_vote') . $value }}"
                                                               class="href_setting_link" target="_blank">
                                                                {{ url('/') . config('settings.email.link_vote') . $value }}
                                                            </a>
                                                        </span>
                                                    @elseif($value)
                                                        <span class="label label-default">{{ $value }}</span>
                                                    @endif
                                                </h4>
                                            @endforeach
                                        @endforeach
                                    @else
                                        <div class="alert alert-info">
                                            {{ trans('polls.message.no_setting') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="vote_detail">
                        <div class="row">
                            <div class="col-md-1 col-lg-1 col-sm-1 col-xs-2 col-menu-result">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <ul class="nav nav-pills nav-stacked pie_bar_chart_manage">
                                            <li class="active li-result-table
                                                {{ ($optionRateBarChart != "null") ? '': 'hide-result-li' }}">
                                                <a data-toggle="tab" href="#table">
                                                    <i class="fa fa-table" aria-hidden="true"></i>
                                                </a>
                                            </li>
                                            @if ($optionRateBarChart != "null")
                                                <li><a data-toggle="tab" href="#menu2">
                                                        <i class="fa fa-bar-chart" aria-hidden="true"></i>
                                                    </a>
                                                </li>
                                                <li><a data-toggle="tab" href="#menu3">
                                                        <i class="fa fa-pie-chart" aria-hidden="true"></i>
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-11 col-ls-11 col-sm-11 col-xs-10 col-result-detail">
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="table">
                                        <div class="panel panel-default panel-darkcyan">
                                            <div class="panel-heading panel-heading-darkcyan">
                                                {{ trans('polls.table_result') }}
                                            </div>
                                            <div class="panel-body">
                                                @if ($countParticipantsVoted)
                                                    <div class="row">
                                                        <div class="col-lg-4 col-lg-offset-2
                                                                    col-md-4 col-md-offset-2
                                                                    col-sm-5 col-xs-detail
                                                                    ">
                                                            <button type="button" class="btn btn-administration btn-block btn-sm"
                                                                    data-toggle="modal" data-target="#myModal">
                                                                <i class="fa fa-list" aria-hidden="true"></i> {{ trans('polls.show_vote_details') }}
                                                            </button>
                                                        </div>
                                                        <div class="col-lg-3 col-md-3 col-sm-3 col-sm-export col-xs-detail">
                                                            {{ Form::open(['route' => ['exportPDF', 'poll_id' => $poll->id]]) }}
                                                            {{
                                                                Form::button('<span class="glyphicon glyphicon-export"></span>' . ' '
                                                                . trans('polls.export_pdf'), [
                                                                    'type' => 'submit',
                                                                    'class' => 'btn btn-administration btn-block btn-sm'
                                                                ])
                                                            }}
                                                            {{ Form::close() }}
                                                        </div>
                                                        <div class="col-lg-3 col-md-3 col-sm-4 col-xs-detail">
                                                            {{ Form::open(['route' => ['exportExcel', 'poll_id' => $poll->id]]) }}
                                                            {{
                                                                Form::button('<span class="glyphicon glyphicon-export"></span>' . ' ' . trans('polls.export_excel'), [
                                                                    'type' => 'submit',
                                                                    'class' => 'btn btn-administration btn-block btn-sm'
                                                                ])
                                                            }}
                                                            {{ Form::close() }}
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="col-lg-12 manager-tabel-result-poll result-vote-poll">
                                                    <table class="table table-hover table-responsive table-option-result-detail">
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
                                                                    <td class="{{ ($isHaveImages) ? 'td-poll-result' : '' }}">
                                                                        @if ($isHaveImages)
                                                                            <img src="{{ asset($data['image']) }}">
                                                                        @endif
                                                                        <p>{{ $data['name'] }}</p>
                                                                    </td>
                                                                    <td><span class="badge">{{ $data['numberOfVote'] }}</span></td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="modal fade model-show-details" id="myModal" role="dialog">
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
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">
                                                                {{ trans('polls.close') }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="menu2">
                                        <div class="panel panel-default panel-darkcyan">
                                            <div class="panel-heading panel-heading-darkcyan">
                                                {{ trans('polls.bar_chart') }}
                                            </div>
                                            <div class="show-barchart panel-body">
                                                @if ($optionRateBarChart)
                                                    <!-- bar chart -->
                                                    <div id="chart"></div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="menu3">
                                        <div class="panel panel-default panel-darkcyan">
                                            <div class="panel-heading panel-heading-darkcyan">
                                                {{ trans('polls.pie_chart') }}
                                            </div>
                                            <div class="show-piechart panel-body">
                                                <!-- pie chart -->
                                                @if ($optionRateBarChart)
                                                    <div id="chart_div"></div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix visible-lg"></div>
                        </div>
                    </div>

                    <div class="tab-pane" id="activity">
                        <div class="row">
                            <div class="col-lg-6">
                                <a id="label_link_admin" target="_blank">
                                    {{ str_limit(url('/') . config('settings.email.link_vote') . $tokenLinkAdmin,
                                    config('settings.limit_link')) }}
                                </a>
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon3">
                                            <i class="fa fa-link" aria-hidden="true"></i>
                                        </span>
                                        {{
                                            Form::text('administer_link', $tokenLinkAdmin, [
                                                'class' => 'form-control token-admin',
                                                'id' => 'link_admin',
                                                'onkeyup' => 'changeLinkAdmin()',
                                            ])
                                        }}
                                        <span class="input-group-btn" data-token-link-admin="{{ $tokenLinkAdmin }}">
                                            {{
                                                Form::button('<i class="fa fa-check" aria-hidden="true"></i>', [
                                                    'class' => 'btn btn-success btn-darkcyan edit-link-admin'
                                                ])
                                            }}
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="error_link_admin"></div>
                                </div>
                                <div class="form-group">
                                    <label class="label label-info message-link-admin"></label>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <a id="label_link_user" target="_blank">
                                    {{ str_limit(url('/') . config('settings.email.link_vote') . $tokenLinkUser,
                                    config('settings.limit_link')) }}
                                </a>
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon3">
                                        <i class="fa fa-link" aria-hidden="true"></i>
                                    </span>
                                    {{
                                        Form::text('participation_link', $tokenLinkUser, [
                                            'class' => 'form-control token-user',
                                            'id' => 'link_user',
                                            'onkeyup' => 'changeLinkUser()',
                                        ])
                                    }}
                                    <span class="input-group-btn" data-token-link-user="{{ $tokenLinkUser }}">
                                        {{
                                            Form::button('<i class="fa fa-check" aria-hidden="true"></i>', [
                                                'class' => 'btn btn-success btn-darkcyan edit-link-user'
                                            ])
                                        }}
                                    </span>
                                </div>
                                <div class="form-group">
                                    <div class="error_link_user"></div>
                                </div>
                                <div class="form-group">
                                    <label class="label label-info message-link-user"></label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <hr class="hr-darkcyan">
                        </div>
                        <div class="row menu-activity-poll">
                            <div class="col-lg-3 col-md-3 col-xs-activity">
                                <a href="{{ URL::action('User\ActivityController@show', $poll->id) }}"
                                   class="btn btn-administration btn-sm btn-block">
                                    <span class="fa fa-history"></span>
                                    {{ trans('polls.view_history') }}
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-3 col-xs-activity">
                                <a href="{{ route('edit-poll', $poll->getTokenAdminLink()) }}"
                                   class="btn btn-administration btn-sm btn-block">
                                    <span class="fa fa-pencil"></span> {{ trans('polls.tooltip.edit') }}
                                </a>

                            </div>
                            <div class="col-lg-3 col-md-3 col-xs-activity">
                                @if ($poll->isClosed())
                                    <a class="reopen-poll btn btn-administration btn-sm btn-block" href="#">
                                        <i class="fa fa-external-link" aria-hidden="true"></i>
                                        {{ trans('polls.reopen_poll') }}
                                    </a>
                                @endif
                                @if (! $poll->isClosed())
                                    {{
                                        Form::open([
                                            'route' => ['poll.destroy', $poll->id],
                                            'id' => 'close-poll',
                                            'method' => 'delete'
                                        ])
                                    }}
                                    {{
                                        Form::button('<span class="fa fa-times-circle"></span>' . ' '
                                                    . trans('polls.close_poll'), [
                                            'class' => 'close-poll btn btn-block btn-sm btn-administration',
                                        ])
                                    }}
                                    {{ Form::close() }}
                                @endif
                            </div>
                            <div class="col-lg-3 col-md-3 col-xs-activity div-delete-participant
                                {{ (Session::get('locale') == 'ja' && ! $countParticipantsVoted)
                                ? 'col-md-activity-jp' : '' }}">
                                @if (! $countParticipantsVoted)
                                    <a href="{{ route('duplicate.show', $poll->id) }}"
                                       class="btn btn-administration btn-block btn-duplicate btn-sm">
                                        <span class="fa fa-files-o"></span> {{ trans('polls.tooltip.duplicate') }}
                                    </a>
                                    @else
                                     <div class="delete-all-participants">
                                        {!!
                                            Form::open([
                                                'route' => ['delete_all_participant',
                                                'poll_id' => $poll->id],
                                                'id' => 'form-delete-participant'
                                            ])
                                        !!}
                                        {{
                                            Form::button('<span class="fa fa-trash-o"></span>' . ' '
                                            . trans('polls.delete_all_participants'), [
                                                'class' => 'btn-delete-participant btn btn-block
                                                btn-sm btn-administration btn-duplicate',
                                            ])
                                        }}
                                    </div>

                                @endif
                                    <div class="delete-all-participants-soket">
                                        {!!
                                            Form::open([
                                                'route' => ['delete_all_participant', 'poll_id' => $poll->id],
                                                'id' => 'form-delete-participant'
                                            ])
                                        !!}
                                        {{
                                            Form::button('<span class="fa fa-trash-o"></span>' . ' '
                                            . trans('polls.delete_all_participants'), [
                                                'class' => 'btn-delete-participant
                                                btn-sm btn btn-block btn-administration',
                                            ])
                                        }}
                                    </div>
                                </div>
                        </div>
                        <div class="row menu-activity-poll menu-add-soket">
                                <div class="col-lg-3 col-xs-activity">
                                    <a href="{{ route('duplicate.show', $poll->id) }}" target="_blank"
                                       class="btn btn-administration btn-sm btn-block">
                                        <span class="fa fa-files-o"></span> {{ trans('polls.tooltip.duplicate') }}
                                    </a>
                                </div>
                            </div>
                        @if ($countParticipantsVoted)
                            <div class="row menu-activity-poll menu-add">
                                <div class="col-lg-3 col-xs-activity">
                                    <a href="{{ route('duplicate.show', $poll->id) }}" target="_blank"
                                       class="btn btn-administration btn-sm btn-block btn-duplicate">
                                        <span class="fa fa-files-o"></span> {{ trans('polls.tooltip.duplicate') }}
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('manage-scripts')

    <!-- ---------------------------------
        Javascript of manage poll
    ---------------------------------------->

    <!-- FORM WINZARD: form step -->
    {!! Html::script('bower/twitter-bootstrap-wizard/jquery.bootstrap.wizard.js') !!}

    <!-- DATETIME PICKER: time close of poll -->
    {!! Html::script('/bower/moment/min/moment.min.js') !!}
    {!! Html::script('/bower/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') !!}

    <!-- BOOTSTRAP SWITCH: setting of poll -->
    {!! Html::script('bower/bootstrap-switch/dist/js/bootstrap-switch.min.js') !!}

    <!-- JQUERY VALIDATE: validate info of poll -->
    {!! Html::script('bower/jquery-validation/dist/jquery.validate.min.js') !!}

    <!-- SWEET ALERT: alert message js -->
    {!! Html::script('bower/sweetalert/dist/sweetalert.min.js') !!}


    <!-- MANAGE POLL -->
    {!! Html::script('js/managePoll.js') !!}

    {!! Html::script('js/poll.js') !!}

    <!-- VOTE SOCKET-->
    {!! Html::script('js/voteSocket.js') !!}

    <!-- EDIT LINK POLL-->
    {!! Html::script('js/editLink.js') !!}

    <!-- HIGHCHART-->
    {!! Html::script('bower/highcharts/highcharts.js') !!}
    {!! Html::script('bower/highcharts/highcharts-3d.js') !!}

    <!-- CHART -->
    {!! Html::script('js/chart.js') !!}

@endpush
