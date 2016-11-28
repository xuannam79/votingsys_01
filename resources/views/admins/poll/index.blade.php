@extends('admins.master')
@section('title')
    {{ trans('polls.title') }}
@endsection
@section('content')
    <div class="card">
        <div class="header">
            <h2>
                {{ trans('polls.head.index') }}
                <a href="{{ route('admin.poll.create') }}"
                   class="btn btn-success btn-lg waves-effect">
                    {{ trans('polls.button.create_poll') }}
                </a>
            </h2>
        </div>
        <div class="body table-responsive">
            @include('layouts.message')
            @include('layouts.error')
            <!-- POLL SEARCH FORM -->
            {{ Form::open(['route' => 'admin.poll.index', 'method' => 'GET', 'class' => 'form-inline']) }}
                <div class="card">
                    <div class="header bg-cyan" id="search-text">
                        <h5>
                            {{ trans('polls.label.search') }}
                            <a href="{{ route('admin.poll.index') }}"
                               class="header-dropdown m-r--5 btn bg-red btn-xs waves-effect">
                                {{ trans('polls.button.reset_search') }}
                            </a>
                        </h5>
                    </div>
                    <div class="body" id="form-search">
                        <div class="row clearfix">

                            <!-- NAME -->
                            <div class="col-md-2">
                                <div class="form-group">
                                    <b>{{ trans('polls.table.tbody.name') }}</b>
                                    <div class="form-line">
                                        {{
                                            Form::text('name', isset($input['name']) ? $input['name'] : "", [
                                                'class' => 'form-control',
                                                'id' => 'creator',
                                            ])
                                        }}
                                    </div>
                                </div>
                            </div>

                            <!-- EMAIL -->
                            <div class="col-md-2">
                                <div class="form-group">
                                    <b>{{ trans('polls.table.tbody.email') }}</b>
                                    <div class="form-line">
                                        {{
                                            Form::text('email', isset($input['email']) ? $input['email'] : "", [
                                                'class' => 'form-control',
                                                'id' => 'creator',
                                            ])
                                        }}
                                    </div>
                                </div>
                            </div>

                            <!-- TITLE -->
                            <div class="col-md-2">
                                <b>{{ trans('polls.table.thead.title') }}</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="material-icons">title</i>
                                    </span>
                                    <div class="form-line">
                                        {{
                                            Form::text('title', isset($input['title']) ? $input['title'] : "", [
                                                'class' => 'form-control',
                                                'id' => 'title',
                                            ])
                                        }}
                                    </div>
                                </div>
                            </div>

                            <!-- TYPE -->
                            <div class="col-md-3">
                                <b>{{ trans('polls.table.thead.type') }}</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="material-icons">radio_button_checked</i>
                                    </span>
                                    <div class="form-line">
                                        {{
                                            Form::select(
                                                'type',
                                                $data['type'],
                                                (isset($input['type']) ? $input['type'] : null),
                                                ['class' => 'form-control']
                                            )
                                        }}
                                    </div>
                                </div>
                            </div>

                            <!-- STATUS -->
                            <div class="col-md-3">
                                <b>{{ trans('polls.table.thead.status') }}</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="material-icons">lock_open</i>
                                    </span>
                                    <div class="form-line">
                                        {!!
                                            Form::select('status',
                                                $data['status'],
                                                (isset($input['status']) ? $input['status'] : null),
                                                ['class' => 'form-control']
                                            )
                                        !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-lg-4 col-lg-offset-4">
                                <button class="btn bg-cyan btn-block btn-lg waves-effect">
                                    {{ trans('polls.button.search_poll') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            {{ Form::close() }}
        <!--END POLL SEARCH FORM -->

            @if ($polls->count())
                    <div class="hide" data-route="{{ route('status.store') }}"
                         data-token="{{ csrf_token() }}"
                         data-status-open="{{ trans('polls.label.poll_opening') }}"
                         data-tooltip-open="{{ trans('polls.tooltip.open') }}"
                         data-tooltip-close="{{ trans('polls.tooltip.close') }}"></div>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>{{ trans('polls.table.thead.STT') }}</th>
                            <th>{{ trans('polls.table.thead.creator') }}</th>
                            <th>{{ trans('polls.table.thead.title') }}</th>
                            <th>{{ trans('polls.table.thead.type') }}</th>
                            <th>
                               {{ trans('polls.table.thead.link') }}
                            </th>
                            <th>{{ trans('polls.table.thead.status') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($polls as $poll)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>
                                    {{ trans('polls.table.tbody.name') .  $poll->user->name}}<br>
                                    {{ trans('polls.table.tbody.email') . $poll->user->email }}
                                </td>
                                <td>{{ str_limit($poll->title, 50) }}</td>
                                <td>{{ $poll->multiple }}</td>
                                <td>
                                     <b>{{ trans('polls.table.tbody.link_participant') }}</b> <br>
                                     {{ (empty($links['participant'][$poll->id])) ? "" : $links['participant'][$poll->id] }}<br>
                                    <b>{{ trans('polls.table.tbody.link_administration') }}</b> <br>
                                     {{ (empty($links['administration'][$poll->id])) ? "" : $links['administration'][$poll->id] }}
                                </td>

                                <td id="status_{{ $poll->id }}">{!! $poll->status !!}</td>
                                <td>
                                    {{
                                        Form::open([
                                            'route' => ['admin.poll.destroy', $poll->id],
                                            'method' => 'DELETE',
                                            'onsubmit' => 'return confirmDelete("' . trans('polls.message.confirm_delete') . '")',
                                        ])
                                    }}

                                        <!-- BUTTON OPEN POLL -->
                                        @if ($poll->status == trans('polls.label.poll_closed'))
                                            <button type="button" id="btn_{{ $poll->id }}" onclick="changeStatusOfPoll({{ $poll->id }})"
                                               class="btn bg-brown btn-xs" data-toggle="tooltip" data-placement="top"
                                               title="" data-original-title="{{ trans('polls.tooltip.open') }}">
                                                <i class="material-icons">lock_open</i>
                                            </button>
                                        @else

                                        <!-- BUTTON CLOSE POLL -->
                                            <button type="button" id="btn_{{ $poll->id }}" onclick="changeStatusOfPoll({{ $poll->id }})"
                                               class="btn bg-brown btn-xs" data-toggle="tooltip" data-placement="top"
                                               title="" data-original-title="{{ trans('polls.tooltip.close') }}">
                                                <i class="material-icons">lock</i>
                                            </button>
                                        @endif

                                        <!-- BUTTON EDIT POLL -->
                                        <a href="{{ route('admin.poll.edit', ['id' => $poll->id]) }}"
                                            class="btn bg-orange btn-xs" data-toggle="tooltip" data-placement="top"
                                           title="" data-original-title="{{ trans('polls.tooltip.edit') }}">
                                            <i class="material-icons">edit</i>
                                        </a>

                                        <!-- BUTTON DELETE POLL -->
                                        {{
                                            Form::button('<i class="material-icons">delete</i>', [
                                                'type' => 'submit',
                                                'class' => 'btn bg-red btn-xs',
                                                'data-toggle' => 'tooltip',
                                                'data-placement' => 'top',
                                                'title' => '',
                                                'data-original-title' => trans('polls.tooltip.delete')
                                            ])
                                        }}
                                    {{ Form::close() }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="dataTables_info">
                    {{
                        trans_choice('label.paginations', $polls->total(), [
                            'start' => $polls->firstItem(),
                            'finish' => $polls->lastItem(),
                            'numberOfRecords' => $polls->total()
                        ])
                    }}
                </div>
                <div class="pagination pagination-lg">
                    {{ (($input) ? $linkFilter : $polls->render()) }}
                </div>
            @else
                <div class="card">
                    <div class="body bg-light-blue">
                        {{ trans('polls.message.not_found_polls') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
