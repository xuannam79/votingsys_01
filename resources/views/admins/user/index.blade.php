@extends('admins.master')
@section('title')
    {{ trans('user.title') }}
@endsection
@section('content')
    <div class="card">
        <div class="header">
            <h2>
                {{ trans('user.panel_head.index') }}
                <a href="{{ route('admin.user.create') }}"
                   class="btn btn-success btn-lg waves-effect">
                    {{ trans('user.button.create') }}
                </a>
            </h2>
        </div>
        <div class="body table-responsive">
        @include('layouts.error')
        @include('layouts.message')
        <!-- POLL SEARCH FORM -->
            {{ Form::open(['route' => 'admin.user.index', 'method' => 'GET', 'class' => 'form-inline']) }}
            <div class="card">
                <div class="header bg-cyan" id="search-text">
                    <h5>
                        {{ trans('user.label.search') }}
                        <a href="{{ route('admin.user.index') }}"
                           class="header-dropdown m-r--5 btn bg-red btn-xs waves-effect">
                            {{ trans('user.button.reset_search') }}
                        </a>
                    </h5>
                </div>
                <div class="body" id="form-search">
                    <div class="row clearfix">

                        <!-- NAME -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <b>{{ trans('user.label.name') }}</b>
                                <div class="form-line">
                                    {{
                                        Form::text('name', isset($input['name']) ? $input['name'] : "", [
                                            'class' => 'form-control',
                                            'id' => 'name',
                                        ])
                                    }}
                                </div>
                            </div>
                        </div>

                        <!-- EMAIL -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <b>{{ trans('user.label.email') }}</b>
                                <div class="form-line">
                                    {{
                                        Form::text('email', isset($input['email']) ? $input['email'] : "", [
                                            'class' => 'form-control',
                                            'id' => 'email',
                                        ])
                                    }}
                                </div>
                            </div>
                        </div>

                        <!-- CHAT WORK -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <b>{{ trans('user.label.chatwork') }}</b>
                                <div class="form-line">
                                    {{
                                        Form::text('chatwork', isset($input['chatwork_id']) ? $input['chatwork_id'] : "", [
                                            'class' => 'form-control',
                                            'id' => 'chatwork',
                                        ])
                                    }}
                                </div>
                            </div>
                        </div>

                        <!-- GENDER -->
                        <div class="col-md-3">
                            <b>{{ trans('user.label.gender.name') }}</b>
                            <div class="form-line">
                                {{
                                    Form::select('gender', $data['gender'], (isset($input['gender']) ? $input['gender'] : null), [
                                        'class' => 'form-control',
                                        'placeholder' => trans('user.placeholder.gender'),
                                    ])
                                }}
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix">
                        <div class="col-lg-4 col-lg-offset-4">
                            <button class="btn bg-cyan btn-block btn-lg waves-effect">
                                {{ trans('user.button.search') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        <!--END POLL SEARCH FORM -->

            @if ($users->count())
                <table class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>{{ trans('user.label.STT') }}</th>
                        <th>{{ trans('user.label.name') }}</th>
                        <th>{{ trans('user.label.email') }}</th>
                        <th>{{ trans('user.label.chatwork') }}</th>
                        <th>{{ trans('user.label.gender.name') }}</th>
                        <th>{{ trans('user.label.avatar') }}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $loop->index + 1 }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->chatwork_id }}</td>
                            <td>{{ $user->showGender() }}</td>
                            <td><img src="{{ $user->getAvatarPath() }}" class="avatar-list"></td>
                            <td>
                            {{
                                Form::open([
                                    'route' => ['admin.user.destroy', $user->id],
                                    'method' => 'DELETE',
                                    'onsubmit' => 'return confirmDelete("' . trans('user.message.confirm_delete') . '")',
                                ])
                            }}

                                <!-- BUTTON EDIT USER -->
                                <a href="{{ route('admin.user.edit', ['id' => $user->id]) }}"
                                   class="btn bg-orange btn-xs" data-toggle="tooltip" data-placement="top"
                                   title="" data-original-title="{{ trans('user.tooltip.edit') }}">
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
                                        'data-original-title' => trans('user.tooltip.delete')
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
                        trans_choice('label.paginations', $users->total(), [
                            'start' => $users->firstItem(),
                            'finish' => $users->lastItem(),
                            'numberOfRecords' => $users->total()
                        ])
                    }}
                </div>
                <div class="pagination pagination-lg">
                    {{ (isset($linkFilter) ? $linkFilter : $users->render()) }}
                </div>
            @else
                <div class="card">
                    <div class="body bg-light-blue">
                        {{ trans('user.message.not_found_users') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
