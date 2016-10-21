@extends('admins.master')
@section('title')
    {{ trans('polls.title') }}
@endsection
@section('content')
    <div class="hide" data-poll ="{{ $data }}"></div>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>{{ trans('polls.head.create') }}</h2>
            </div>
            <div class="body">
                @include('layouts.error')
                @include('layouts.message')
                {{
                    Form::open([
                        'route' => 'admin.poll.store',
                        'method' => 'POST',
                        'id' => 'form_create_poll',
                        'enctype' => 'multipart/form-data',
                    ])
                }}
                    <!-- STEP 1: POLL INFOR -->
                    <h3>{{ trans('polls.label.step_1') }}</h3>
                    <fieldset>

                        <!-- FULL NAME -->
                        {{ Form::label(trans('polls.label_for.full_name'), trans('polls.label.full_name')) }}
                        <div class="form-group">
                            <div class="form-line">
                                {{
                                    Form::text('name', null, [
                                        'class' => 'form-control',
                                        'id' => trans('polls.label_for.full_name'),
                                        'placeholder' => trans('polls.placeholder.full_name'),
                                    ])
                                }}
                            </div>
                        </div>

                        <!-- EMAIL -->
                        {{ Form::label(trans('polls.label_for.email'), trans('polls.label.email')) }}
                        <div class="form-group">
                            <div class="form-line">
                                {{
                                    Form::text('email', null, [
                                        'class' => 'form-control',
                                        'id' => trans('polls.label_for.email'),
                                        'placeholder' => trans('polls.placeholder.email'),
                                    ])
                                }}
                            </div>
                        </div>

                        <!-- CHAT WORK -->
                        {{ Form::label(trans('polls.label_for.chatwork'), trans('polls.label.chatwork')) }}
                        <div class="form-group">
                            <div class="form-line">
                                {{
                                    Form::text('chatwork_id', null, [
                                        'class' => 'form-control',
                                        'id' => trans('polls.label_for.chatwork'),
                                        'placeholder' => trans('polls.placeholder.chatwork'),
                                    ])
                                }}
                            </div>
                        </div>

                        <!-- TITLE -->
                        {{ Form::label(trans('polls.label_for.title'), trans('polls.label.title')) }}
                        <div class="form-group">
                            <div class="form-line">
                                {{
                                    Form::text('title', null, [
                                        'class' => 'form-control',
                                        'id' => trans('polls.label_for.title'),
                                        'placeholder' => trans('polls.placeholder.title'),
                                    ])
                                }}
                            </div>
                        </div>

                        <!-- DESCRIPTION -->
                        {{ Form::label(trans('polls.label_for.description'), trans('polls.label.description')) }}
                        <div class="form-group">
                            <div class="form-line">
                                {{
                                    Form::textarea('description', null, [
                                        'class' => 'form-control',
                                        'id' => trans('polls.label_for.description'),
                                        'placeholder' => trans('polls.placeholder.description'),
                                    ])
                                }}
                            </div>
                        </div>

                        <!-- LOCATION -->
                        {{ Form::label(trans('polls.label_for.location'), trans('polls.label.location')) }}
                        <div class="form-group">
                            <div class="form-line">
                                {{
                                    Form::text('location', null, [
                                        'class' => 'form-control',
                                        'id' => trans('polls.label_for.location'),
                                        'placeholder' => trans('polls.placeholder.location'),
                                    ])
                                }}
                            </div>
                        </div>

                        <!-- TYPE -->
                        <div class="form-group">
                            {{ Form::label(trans('polls.label_for.type'), trans('polls.label.type')) }}
                            {{
                                Form::radio('type', config('settings.type.single_choice'), null, [
                                    'id' => trans('polls.label_for.single_choice'),
                                    'class' => 'with-gap',
                                ])
                            }}
                            {{ Form::label(trans('polls.label_for.single_choice'), trans('polls.label.single_choice')) }}
                            {{
                                Form::radio('type', config('settings.type.multiple_choice'), null, [
                                    'id' => trans('polls.label_for.multiple_choice'),
                                    'class' => 'with-gap',
                                ])
                            }}
                            {{ Form::label(trans('polls.label_for.multiple_choice'), trans('polls.label.multiple_choice')) }}
                        </div>
                    </fieldset>

                    <!-- STEP 2: POLL OPTION -->
                    <h3>{{ trans('polls.label.step_2') }}</h3>
                    <fieldset>

                        <!-- OPTION LISTS -->
                        <div class="poll-option"></div>

                        <!-- BUTTON ADD OPTION -->
                        <div class="col-lg-3">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" id="number" class="form-control" value="1"
                                           placeholder="{{ trans('polls.placeholder.number_add') }}">
                                </div>
                                <span class="input-group-addon">
                                    <button class="btn btn-default" type="button" onclick="addOption({{ $data }})">
                                        <i class="material-icons">add</i>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </fieldset>

                    <!-- STEP 3: POLL SETTING -->
                    <h3>{{ trans('polls.label.step_3') }}</h3>
                    <fieldset>

                        <!-- required email -->
                        <h5>{{ trans('polls.label.setting.required_email') }}
                            {{
                                Form::checkbox(
                                    config('settings.input_setting.email'),
                                    config('settings.setting.required_email'),
                                    null, [
                                        'id' => config('settings.input_setting.email'),
                                        'data-toggle' => 'toggle',
                                        'data-onstyle' => 'success',
                                    ])
                            }}
                        </h5>

                        <!-- add answer -->
                        <h5>{{ trans('polls.label.setting.add_answer') }}
                            {{
                                Form::checkbox(
                                    config('settings.input_setting.answer'),
                                    config('settings.setting.add_answer'),
                                    null, [
                                        'id' => config('settings.input_setting.answer'),
                                        'data-toggle' => 'toggle',
                                        'data-onstyle' => 'success',
                                    ])
                            }}
                        </h5>

                        <!-- hide result -->
                        <h5>{{ trans('polls.label.setting.hide_result') }}
                            {{
                                Form::checkbox(
                                    config('settings.input_setting.result'),
                                    config('settings.setting.hide_result'),
                                    null, [
                                        'id' => config('settings.input_setting.result'),
                                        'data-toggle' => 'toggle',
                                        'data-onstyle' => 'success',
                                    ])
                            }}
                        </h5>

                        <!-- custom link -->
                        <h5>{{ trans('polls.label.setting.custom_link') }}
                            {{
                                Form::checkbox(
                                    config('settings.input_setting.link'),
                                    config('settings.setting.custom_link'),
                                    null, [
                                        'id' => config('settings.input_setting.link'),
                                        'data-toggle' => 'toggle',
                                        'data-onstyle' => 'success',
                                    ])
                            }}
                        </h5>
                        <div class="form-group" id="link-poll">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3">
                                    {{ url('/') . config('settings.email.link_vote') }}
                                </span>
                                <div class="form-line">
                                    {{
                                        Form::text('link', null, [
                                            'class' => 'form-control',
                                            'id' => 'basic-url',
                                            'aria-describedby' => 'basic-addon3',
                                            'onkeyup' => 'checkLink("' . route('link.store') . '", "' . csrf_token() . '")',
                                        ])
                                    }}
                                </div>
                            </div>
                        </div>

                        <!-- set limit -->
                        <h5>{{ trans('polls.label.setting.set_limit') }}
                            {{
                                Form::checkbox(
                                    config('settings.input_setting.limit'),
                                    config('settings.setting.set_limit'),
                                    null, [
                                        'id' => config('settings.input_setting.limit'),
                                        'data-toggle' => 'toggle',
                                        'data-onstyle' => 'success',
                                    ])
                            }}
                        </h5>
                        <div class="form-group" id="poll-limit">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3"><i class="material-icons">keyboard_return</i></span>
                                <div class="form-line">
                                    {{
                                        Form::text('limit', null, [
                                            'class' => 'form-control',
                                            'id' => 'limit',
                                            'aria-describedby' => 'basic-addon3',
                                            'placeholder' => trans('polls.placeholder.number_limit'),
                                        ])
                                    }}
                                </div>
                            </div>
                        </div>

                        <!-- set password -->
                        <h5>{{ trans('polls.label.setting.set_password') }}
                            {{
                                Form::checkbox(
                                    config('settings.input_setting.password'),
                                    config('settings.setting.set_password'),
                                    null, [
                                        'id' => config('settings.input_setting.password'),
                                        'data-toggle' => 'toggle',
                                        'data-onstyle' => 'success',
                                    ])
                            }}
                        </h5>
                        <div class="form-group" id="password-poll">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3"><i class="material-icons">security</i></span>
                                <div class="form-line">
                                    {{
                                        Form::password('password_poll', [
                                            'class' => 'form-control',
                                            'aria-describedby' => 'basic-addon3',
                                            'placeholder' => trans('polls.placeholder.password_poll'),
                                        ])
                                    }}
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <!-- STEP 4: PARTICIPANT -->
                    <h3>{{ trans('polls.label.step_4') }}</h3>
                    <fieldset>
                        <div class="form-group">
                            {{ Form::label(trans('polls.label_for.invite'), trans('polls.label.invite')) }}
                            {{
                                Form::radio('invite', config('settings.participant.invite_all'), true, [
                                    'id' => trans('polls.label_for.invite_all'),
                                    'class' => 'with-gap'
                                ])
                            }}
                            {{ Form::label(trans('polls.label_for.invite_all'), trans('polls.label.invite_all')) }}
                            {{
                                Form::radio('invite', config('settings.participant.invite_people'), false, [
                                    'id' => trans('polls.label_for.invite_people'),
                                    'class' => 'with-gap'
                                ])
                            }}
                            {{ Form::label(trans('polls.label_for.invite_people'), trans('polls.label.invite_people')) }}
                        </div>
                        <div class="email-participant">

                            <!-- EMAIL LISTS -->
                            <div class="email-invited"></div>

                            <!-- BUTTON ADD EMAIL -->
                            <div class="col-lg-3">
                                <div class="input-group">
                                    <div class="form-line">
                                        <input type="text" id="number-email" class="form-control" value="1"
                                               placeholder="{{ trans('polls.placeholder.number_add') }}">
                                    </div>
                                    <span class="input-group-addon">
                                    <button class="btn btn-default" type="button" onclick="addEmail({{ $data }})">
                                        <i class="material-icons">add</i>
                                    </button>
                                </span>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection
