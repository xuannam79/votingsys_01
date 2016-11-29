@extends('layouts.app')

@section('content')
    <div class="hide"
        data-poll="{{ $data['jsonData'] }}"
        data-poll-id="{{ $poll->id }}" data-route="{{ url('user/poll') }}"
        data-edit-link-success="{{ trans('polls.edit_link_successfully') }}"
        data-link="{{ url('link') }}"
        data-route-link="{{ route('link-poll.store') }}"
        data-delete-participant="{{ trans('polls.confirm_delete_all_participant') }}"
        data-close-poll="{{ trans('polls.confirm_close_poll') }}"
        data-reopen-poll="{{ trans('polls.confirm_reopen_poll') }}"
        data-url-reopen-poll="{{ url('user/poll') }}"
        data-token-admin="{{ $tokenLinkAdmin }}"
        data-token-user="{{ $tokenLinkUser }}">
    </div>
    <div class="container">
        <div class="row">
            <div class="loader"></div>
            <div id="manager_poll_wizard" class="col-md-10 col-md-offset-1 well wrap-poll">
                <div class="navbar panel">
                    <div class="navbar-inner">
                        <div class="col-lg-6 col-lg-offset-3 panel-heading">
                            <ul>
                                <li><a href="#info" data-toggle="tab">{{ trans('polls.poll_info') }}</a></li>
                                <li><a href="#vote_detail" data-toggle="tab">{{ trans('polls.show_result') }}</a></li>
                                <li><a href="#activity" data-toggle="tab">{{ trans('polls.activity_poll') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="tab-content">
                    @include('layouts.message')
                    @if (Session::has('messages'))
                        <div class="col-lg-12">
                            <div class="col-lg-8 col-lg-offset-2 alert alert-success">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                {!! Session::get('messages') !!}
                            </div>
                        </div>
                    @endif
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
                        <p>{!! $poll->status !!}</p>
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
                                            <div class="col-lg-12 option-detail-modal">
                                                <img src="{{ $option->showImage() }}">
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
                                                            <a href="{{ url('/') . config('settings.email.link_vote') . $value }}" class="href_setting_link" target="_blank">
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
                            <div class="col-md-2">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <ul class="nav nav-pills nav-stacked">
                                            <li class="active"><a data-toggle="tab" href="#statistic">
                                                    <i class="fa fa-calculator" aria-hidden="true"></i>
                                                </a>
                                            </li>
                                            <li><a data-toggle="tab" href="#table">
                                                    <i class="fa fa-table" aria-hidden="true"></i>
                                                </a>
                                            </li>
                                            @if ($optionRateBarChart != 'null')
                                                <li><a data-toggle="tab" href="#menu2">
                                                        <i class="fa fa-bar-chart" aria-hidden="true"></i>
                                                    </a>
                                                </li>
                                            @endif
                                            @if ($optionRatePieChart)
                                                <li><a data-toggle="tab" href="#menu3">
                                                        <i class="fa fa-pie-chart" aria-hidden="true"></i>
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="statistic">
                                        <div class="panel panel-default animated fadeInRight panel-darkcyan">
                                            <div class="panel-heading panel-heading-darkcyan">
                                                {{ trans('polls.statistic') }}
                                            </div>
                                            <div class="panel-body">
                                                <h4>{{ trans('polls.total_vote') }}:
                                                    <span class="statistic-font" class="badge">
                                                    {{ $statistic['total'] }}
                                                </span>
                                                </h4>
                                                @if ($statistic['total'] > config('settings.default_value'))
                                                    <h4>{{ trans('polls.vote_first_time') }}:
                                                        <span class="statistic-font">{{ $statistic['firstTime'] }}</span>
                                                    </h4>
                                                    <h4>{{ trans('polls.vote_last_time') }}:
                                                        <span class="statistic-font">{{ $statistic['lastTime'] }}</span>
                                                    </h4>
                                                    @if ($statistic['largestVote']['number'] > 0 && $statistic['largestVote']['option'])
                                                        <h4>{{ trans('polls.option_highest_vote') }}:
                                                            @if (! empty($statistic['largestVote']['option']))
                                                                @foreach ($statistic['largestVote']['option'] as $largestVote)
                                                                    <span class="statistic-font wrap-text">[{{ $largestVote->name }}]</span>
                                                                    @if (! $loop->last)
                                                                        ,
                                                                    @endif
                                                                @endforeach
                                                                <span class="statistic-font wrap-text">({{ $statistic['largestVote']['number'] . ' ' . trans('polls.vote')}})</span>
                                                            @endif
                                                        </h4>
                                                    @endif
                                                    <h4>{{ trans('polls.option_lowest_vote') }}:
                                                        @if (! empty($statistic['leastVote']['option']))
                                                            @foreach ($statistic['leastVote']['option'] as $leastVote )
                                                                <span class="statistic-font wrap-text">[{{ $leastVote->name }}]</span>
                                                                @if (! $loop->last)
                                                                    ,
                                                                @endif
                                                            @endforeach
                                                            <span class="statistic-font wrap-text">({{ $statistic['leastVote']['number'] . ' ' . trans('polls.vote')}})</span>
                                                        @endif
                                                    </h4>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="table">
                                        <div class="panel panel-default animated fadeInRight panel-darkcyan">
                                            <div class="panel-heading panel-heading-darkcyan">
                                                {{ trans('polls.table_result') }}
                                            </div>
                                            <div class="panel-body">
                                                @if ($poll->countParticipants())
                                                    <div class="row">
                                                        <div class="col-lg-3 col-lg-offset-3">
                                                            <button type="button" class="btn btn-administration" data-toggle="modal" data-target="#myModal">
                                                                <i class="fa fa-list" aria-hidden="true"></i> {{ trans('polls.show_vote_details') }}
                                                            </button>
                                                        </div>
                                                        <div class="col-lg-3">
                                                            {{ Form::open(['route' => ['exportPDF', 'poll_id' => $poll->id]]) }}
                                                            {{
                                                                Form::button('<span class="glyphicon glyphicon-export"></span>' . ' ' . trans('polls.export_pdf'), [
                                                                    'type' => 'submit',
                                                                    'class' => 'btn btn-administration btn-right'
                                                                ])
                                                            }}
                                                            {{ Form::close() }}
                                                        </div>
                                                        <div class="col-lg-3">
                                                            {{ Form::open(['route' => ['exportExcel', 'poll_id' => $poll->id]]) }}
                                                            {{
                                                                Form::button('<span class="glyphicon glyphicon-export"></span>' . ' ' . trans('polls.export_excel'), [
                                                                    'type' => 'submit',
                                                                    'class' => 'btn btn-administration btn-right'
                                                                ])
                                                            }}
                                                            {{ Form::close() }}
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="col-lg-12 manager-tabel-result-poll">
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
                                                                    <td class="td-detail-option">
                                                                        <img src="{{ asset($data['image']) }}">
                                                                        {{ $data['name'] }}
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
                                        </div>
                                    </div>
                                    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                                    <div class="tab-pane fade" id="menu2">
                                        <div class="panel panel-default animated fadeInRight panel-darkcyan">
                                            <div class="panel-heading panel-heading-darkcyan">
                                                {{ trans('polls.bar_chart') }}
                                            </div>
                                            <div class="panel-body">
                                                @if ($optionRateBarChart)
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
                                                            var options = {'width': 700, 'height': 400};
                                                            var chart = new google.visualization.BarChart(document.getElementById('chart'));
                                                            chart.draw(data, options);
                                                        }
                                                    </script>

                                                    <div id="chart"></div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="menu3">
                                        <div class="panel panel-default animated fadeInRight panel-darkcyan">
                                            <div class="panel-heading panel-heading-darkcyan">
                                                {{ trans('polls.pie_chart') }}
                                            </div>
                                            <div class="panel-body">
                                                <!-- pie chart -->
                                                @if ($optionRateBarChart)
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
                                <a id="label_link_admin">
                                    {{ str_limit(url('/') . config('settings.email.link_vote') . $tokenLinkAdmin, config('settings.limit_link')) }}
                                </a>
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon3">
                                            <i class="fa fa-link" aria-hidden="true"></i>
                                        </span>
                                        <input type="text" name="administer_link" class="form-control token-admin"
                                               value="{{ $tokenLinkAdmin }}" id="link_admin" onkeyup="changeLinkAdmin()">
                                        <span class="input-group-btn" data-token-link-admin="{{ $tokenLinkAdmin }}">
                                            {{ Form::button('<i class="fa fa-check" aria-hidden="true"></i>', ['class' => 'btn btn-success edit-link-admin']) }}
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
                                <a id="label_link_user">
                                    {{ str_limit(url('/') . config('settings.email.link_vote') . $tokenLinkUser, config('settings.limit_link')) }}
                                </a>
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon3">
                                        <i class="fa fa-link" aria-hidden="true"></i>
                                    </span>
                                    <input type="text" name="participation_link" class="form-control token-user"
                                           value="{{ $tokenLinkUser }}" id="link_user" onkeyup="changeLinkUser()">
                                    <span class="input-group-btn" data-token-link-user="{{ $tokenLinkUser }}">
                                        <button class="btn btn-success edit-link-user" type="button">
                                            <i class="fa fa-check" aria-hidden="true"></i>
                                        </button>
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
                        <div class="row">
                            <div class="col-lg-3">

                                <a href="{{ URL::action('User\ActivityController@show', $poll->id) }}" class="btn btn-administration btn-block">
                                    <span class="fa fa-history"></span>
                                    {{ trans('polls.view_history') }}
                                </a>
                            </div>
                            <div class="col-lg-3">
                                <a href="{{ route('user-poll.edit', $poll->id) }}" class="btn btn-administration btn-block">
                                    <span class="fa fa-pencil"></span> {{ trans('polls.tooltip.edit') }}
                                </a>
                            </div>
                            <div class="col-lg-3">
                                @if ($poll->isClosed())
                                    <a class="reopen-poll btn btn-administration btn-block" href="#">
                                        <i class="fa fa-external-link" aria-hidden="true"></i> {{ trans('polls.reopen_poll') }}
                                    </a>
                                @endif
                                @if (! $poll->isClosed())
                                    {{ Form::open(['route' => ['poll.destroy', $poll->id], 'id' => 'close-poll', 'method' => 'delete']) }}
                                    {{
                                        Form::button('<span class="fa fa-times-circle"></span>' . ' ' . trans('polls.close_poll'), [
                                            'class' => 'close-poll btn btn-block btn-administration',
                                        ])
                                    }}
                                    {{ Form::close() }}
                                @endif
                            </div>
                            <div class="col-lg-3">
                                @if ($poll->countParticipants())
                                    {!! Form::open(['route' => ['delete_all_participant', 'poll_id' => $poll->id], 'id' => 'form-delete-participant']) !!}
                                    {{
                                        Form::button('<span class="fa fa-trash-o"></span>' . ' ' . trans('polls.delete_all_participants'), [
                                            'class' => 'btn-delete-participant btn btn-block btn-administration',
                                        ])
                                    }}
                                    {{ Form::close() }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
