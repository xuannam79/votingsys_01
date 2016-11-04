@extends('admins.master')
@section('title')
    {{ trans('polls.title') }}
@endsection
@section('content')
    <div class="hide" data-poll="{{ $data }}"
         data-route-link="{{ route('link.store') }}"
         data-token="{{ csrf_token() }}"></div>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>{{ trans('polls.head.edit') }}</h2>
            </div>
            <div class="body">
            @include('layouts.error')
            @include('layouts.message')
                <!-- Nav tabs -->
                <ul class="nav nav-tabs tab-nav-right" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#infor" data-toggle="tab">
                            {{ trans('polls.nav_tab_edit.infor') }}
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#option" data-toggle="tab">
                            {{ trans('polls.nav_tab_edit.option') }}
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#edit-setting" data-toggle="tab">
                            {{ trans('polls.nav_tab_edit.setting') }}
                        </a>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">

                    <!-- POLL INFORMATION -->
                    <div role="tabpanel" class="tab-pane fade in active" id="infor">
                        <h3>{{ trans('polls.label.step_1') }}</h3>
                        {{ Form::open(['route' => ['admin.poll.update', $poll->id], 'method' => 'PUT']) }}

                            <!-- STATUS -->
                            <div class="form-group">
                                {{ Form::label(trans('polls.label_for.status'), trans('polls.label.status')) }}
                                <div class="demo-radio-button">

                                    <!-- Poll opening -->
                                    {{
                                        Form::radio('status', config('settings.status.open'),
                                            ($poll->status == trans('polls.label.poll_opening') ? "true" : null), [
                                                'class' => 'form-control',
                                                'id' => trans('polls.label_for.opening')
                                        ])
                                    }}
                                    {{ Form::label(trans('polls.label_for.opening'), trans('polls.label.opening')) }}

                                    <!-- Poll closed -->
                                    {{
                                        Form::radio('status', config('settings.status.close'),
                                            ($poll->status == trans('polls.label.poll_closed') ? "true" : null), [
                                                'class' => 'form-control',
                                                'id' => trans('polls.label_for.closed')
                                        ])
                                    }}
                                    {{ Form::label(trans('polls.label_for.closed'), trans('polls.label.closed')) }}
                                </div>
                            </div>

                            <!-- FULL NAME-->
                            <div class="form-group">
                                {{ Form::label(trans('polls.label_for.full_name'), trans('polls.label.full_name')) }}
                                <div class="form-line">
                                    {{
                                        Form::text('name', $poll->user->name, [
                                            'class' => 'form-control',
                                            'id' => trans('polls.label_for.full_name'),
                                            'placeholder' => trans('polls.placeholder.full_name'),
                                        ])
                                    }}
                                </div>
                            </div>

                            <!-- EMAIL-->
                            <div class="form-group form-float">
                                {{ Form::label(trans('polls.label_for.email'), trans('polls.label.email')) }}
                                <div class="form-line">
                                    {{
                                        Form::email('email', $poll->user->email, [
                                            'class' => 'form-control',
                                            'id' => trans('polls.label_for.email'),
                                            'placeholder' => trans('polls.placeholder.email'),
                                        ])
                                    }}
                                </div>
                            </div>

                            <!-- CHATWORK ID-->
                            <div class="form-group">
                                {{ Form::label(trans('polls.label_for.chatwork'), trans('polls.label.chatwork')) }}
                                <div class="form-line">
                                    {{
                                        Form::text('chatwork_id', $poll->user->chatwork_id, [
                                            'class' => 'form-control',
                                            'id' => trans('polls.label_for.chatwork'),
                                            'placeholder' => trans('polls.placeholder.chatwork'),
                                        ])
                                    }}
                                </div>
                            </div>

                            <!-- TITLE-->
                            <div class="form-group">
                                {{ Form::label(trans('polls.label_for.title'), trans('polls.label.title')) }}
                                <div class="form-line">
                                    {{
                                        Form::text('title', $poll->title, [
                                            'class' => 'form-control',
                                            'id' => trans('polls.label_for.title'),
                                            'placeholder' => trans('polls.placeholder.title'),
                                        ])
                                    }}
                                </div>
                            </div>

                            <!-- LOCATION-->
                            <div class="form-group">
                                {{ Form::label(trans('polls.label_for.location'), trans('polls.label.location')) }}
                                <div class="form-line">
                                    {{
                                        Form::text('location', $poll->location, [
                                            'class' => 'form-control',
                                            'id' => trans('polls.label_for.location'),
                                            'placeholder' => trans('polls.placeholder.location'),
                                        ])
                                    }}
                                </div>
                            </div>

                            <!-- DESCRIPTION-->
                            <div class="form-group">
                                {{ Form::label(trans('polls.label_for.description'), trans('polls.label.description')) }}
                                <div class="form-line">
                                    {{
                                        Form::textarea('description', $poll->description, [
                                            'class' => 'form-control',
                                            'id' => trans('polls.label_for.description'),
                                            'placeholder' => trans('polls.placeholder.description'),
                                        ])
                                    }}
                                </div>
                            </div>

                            <!-- TYPE-->
                            <div class="form-group">
                                {{ Form::label(trans('polls.label_for.type'), trans('polls.label.type')) }}
                                <div class="demo-radio-button">

                                    <!-- Multiple choice -->
                                    {{
                                        Form::radio('type', config('settings.type_poll.multiple_choice'),
                                            ($poll->multiple == trans('polls.label.multiple_choice') ? "true" : null), [
                                                'class' => 'form-control',
                                                'id' => trans('polls.label_for.multiple_choice')
                                        ])
                                    }}
                                    {{ Form::label(trans('polls.label_for.multiple_choice'), trans('polls.label.multiple_choice')) }}

                                    <!-- Single choice -->
                                    {{
                                        Form::radio('type', config('settings.type_poll.single_choice'),
                                            ($poll->multiple == trans('polls.label.single_choice') ? "true" : null), [
                                                'class' => 'form-control',
                                                'id' => trans('polls.label_for.single_choice')
                                        ])
                                    }}
                                    {{ Form::label(trans('polls.label_for.single_choice'), trans('polls.label.single_choice')) }}
                                </div>
                            </div>

                            <!-- BUTTON -->
                            <div class="row clearfix">
                                <div class="col-lg-3 col-lg-offset-3">
                                    {{
                                        Form::submit(trans('polls.button.change_poll_infor'), [
                                            'class' => 'btn bg-cyan btn-block btn-lg waves-effect',
                                            'name' => 'btn_edit',
                                        ])
                                    }}
                                </div>
                                <div class="col-lg-3">
                                    <a href="{{ route('admin.poll.index') }}" class="btn bg-brown btn-block btn-lg waves-effect">
                                        {{ trans('polls.button.back') }}
                                    </a>
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="option">
                        <h3>{{ trans('polls.label.step_2') }}</h3>
                        {{ Form::open(['route' => ['admin.poll.update', $poll->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}

                            <!-- OPTION-->
                            @foreach($poll->options as $option)
                                <div class="card" id="{{ $option->id }}">
                                    <div class="body">
                                        <div class="row clearfix">
                                            <div class="form-group">
                                                {{ Form::label(trans('polls.label_for.option'), trans('polls.label.option')) }}
                                                <button class="btn bg-red btn-xs waves-effect" type="button" onclick="removeOpion('{{ $option->id }}', 'edit')">
                                                    <i class="material-icons">delete</i> {{ trans('polls.button.remove') }}
                                                </button>
                                                <div class="form-line">
                                                    {{
                                                        Form::text('option[' . $option->id . ']', $option->name, [
                                                            'class' => 'form-control',
                                                            'id' => trans('polls.label_for.option'),
                                                            'placeholder' => trans('polls.placeholder.option'),
                                                        ])
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <img class="image-option-edit" src="{{ asset(($option->image) ? config('settings.option.path_image') .
                                                $option->image : config('settings.option.path_image_default')) }}" >
                                        </div>
                                        <div class="row">
                                            <div class="form-group">
                                                {{ Form::label(trans('polls.label_for.option_image'), trans('polls.label.option_image')) }}
                                                <div class="form-line">
                                                    {{ Form::file('image[' . $option->id . ']') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="row clearfix">

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
                            </div>

                            <!-- BUTTON SUBMIT-->
                            <div class="row clearfix">
                                <div class="col-lg-3 col-lg-offset-3">
                                    {{
                                        Form::submit(trans('polls.button.change_poll_option'), [
                                            'class' => 'btn bg-cyan btn-block btn-lg waves-effect',
                                            'name' => 'btn_edit',
                                        ])
                                    }}
                                </div>
                                <div class="col-lg-3">
                                    <a href="{{ route('admin.poll.index') }}" class="btn bg-brown btn-block btn-lg waves-effect">
                                        {{ trans('polls.button.back') }}
                                    </a>
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="edit-setting">
                        {{ Form::open(['route' => ['admin.poll.update', $poll->id], 'method' =>'PUT']) }}
                            <!-- SETTING -->
                            <div class="card">
                                <div class="header"> {{ trans('polls.label.step_3') }}</div>
                                <div class="body">

                                    <!-- REQUIRED EMAIL -->
                                    <div class="form-group">
                                        <div class="switch">
                                            <label>
                                                <b>{{ strtoupper(trans('polls.label.setting.required_email')) }}</b>
                                                {{
                                                    Form::checkbox(
                                                        'setting[' . config('settings.input_setting.email') . ']',
                                                        config('settings.setting.required_email'),
                                                        array_key_exists(config('settings.setting.required_email'), $settings) ? true : null
                                                    )
                                                }}
                                                <span class="lever switch-col-cyan"></span>
                                            </label>
                                        </div>
                                    </div>


                                    <!-- ADD ANSWER -->
                                    <div class="form-group">
                                        <div class="switch">
                                            <label><b>{{ strtoupper(trans('polls.label.setting.add_answer')) }}</b>
                                                {{
                                                    Form::checkbox(
                                                        'setting[' . config('settings.input_setting.answer') . ']',
                                                        config('settings.setting.add_answer'),
                                                        array_key_exists(config('settings.setting.add_answer'), $settings) ? true : null
                                                    )
                                                }}
                                                <span class="lever switch-col-cyan"></span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- HIDE RESULT -->
                                    <div class="form-group">
                                        <div class="switch">
                                            <label><b>{{ strtoupper(trans('polls.label.setting.hide_result')) }}</b>
                                                {{
                                                    Form::checkbox(
                                                        'setting[' . config('settings.input_setting.result') . ']',
                                                        config('settings.setting.hide_result'),
                                                        array_key_exists(config('settings.setting.hide_result'), $settings) ? true : null
                                                    )
                                                }}
                                                <span class="lever switch-col-cyan"></span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- CUSTOM LINK -->
                                    <div class="form-group">
                                        <div class="switch">
                                            <label><b>{{ strtoupper(trans('polls.label.setting.custom_link')) }}</b>
                                                {{
                                                    Form::checkbox(
                                                        'setting[' . config('settings.input_setting.link') . ']',
                                                        config('settings.setting.custom_link'),
                                                        array_key_exists(config('settings.setting.custom_link'), $settings) ? true : null, [
                                                        'id' => config('settings.input_setting.link'),
                                                    ])
                                                }}
                                                <span class="lever switch-col-cyan"></span>
                                            </label>
                                        </div>
                                        <div class="form-group" id="link-poll">
                                            <div class="input-group">
                                                <span class="input-group-addon" id="basic-addon3">
                                                    {{ url('/') . config('settings.email.link_vote') }}
                                                </span>
                                                <div class="form-line">
                                                    {{
                                                        Form::text('value[' . config('settings.input_setting.link') . ']',
                                                            isset($settings[config('settings.setting.custom_link')])
                                                            ? $settings[config('settings.setting.custom_link')] : null, [
                                                                'class' => 'form-control',
                                                                'id' => 'link',
                                                                'aria-describedby' => 'basic-addon3',
                                                                'onkeyup' => 'checkLink("' . route('link.store') . '", "' . csrf_token() . '")',
                                                        ])
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="error link-error"></div>
                                    </div>

                                    <!-- SET LIMIT -->
                                    <div class="form-group">
                                        <div class="switch">
                                            <label><b>{{ strtoupper(trans('polls.label.setting.set_limit')) }}</b>
                                                {{
                                                    Form::checkbox(
                                                        'setting[' . config('settings.input_setting.limit') . ']',
                                                        config('settings.setting.set_limit'),
                                                        array_key_exists(config('settings.setting.set_limit'), $settings) ? true : null, [
                                                        'id' => config('settings.input_setting.limit'),
                                                    ])
                                                }}
                                                <span class="lever switch-col-cyan"></span>
                                            </label>
                                        </div>
                                        <div class="form-group" id="poll-limit">
                                            <div class="input-group">
                                                <span class="input-group-addon" id="basic-addon3"><i class="material-icons">keyboard_return</i></span>
                                                <div class="form-line">
                                                    {{
                                                        Form::text('value[' . config('settings.input_setting.limit') . ']',
                                                            isset($settings[config('settings.setting.set_limit')]) ? $settings[config('settings.setting.set_limit')] : null, [
                                                                'class' => 'form-control',
                                                                'id' => 'limit',
                                                                'aria-describedby' => 'basic-addon3',
                                                                'placeholder' => trans('polls.placeholder.number_limit'),
                                                        ])
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- SET PASSWORD -->
                                    <div class="form-group">
                                        <div class="switch">
                                            <label><b>{{ strtoupper(trans('polls.label.setting.set_password')) }}</b>
                                                {{
                                                    Form::checkbox(
                                                        'setting[' . config('settings.input_setting.password') . ']',
                                                        config('settings.setting.set_password'),
                                                        array_key_exists(config('settings.setting.set_password'), $settings) ? true : null, [
                                                        'id' => config('settings.input_setting.password'),
                                                    ])
                                                }}
                                                <span class="lever switch-col-cyan"></span>
                                            </label>
                                        </div>
                                        <div class="form-group" id="password-poll">
                                            <div class="input-group">
                                                <span class="input-group-addon" id="basic-addon3"><i class="material-icons">security</i></span>
                                                <div class="form-line">
                                                    {{
                                                        Form::password('value[' . config('settings.input_setting.password') . ']', [
                                                            'class' => 'form-control',
                                                            'aria-describedby' => 'basic-addon3',
                                                            'placeholder' => trans('polls.placeholder.password_poll'),
                                                        ])
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- PARTICIPANT -->
                            <div class="card">
                                <div class="header"> {{ trans('polls.label.step_4') }}</div>
                                <div class="body">

                                    <!-- EMAIL OF PARTICIPANT-->
                                    {{ Form::label(trans('polls.label_for.invite'), trans('polls.label.add_invite')) }}
                                    <div class="form-group demo-tagsinput-area">
                                        <div class="form-line">
                                            <i class="material-icons">email</i>
                                            {{ Form::text('participant', null, ['class' => 'form-control', 'data-role' => 'tagsinput']) }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- BUTTON SUBMIT-->
                            <div class="row clearfix">
                                <div class="col-lg-3 col-lg-offset-3">
                                    {{
                                        Form::submit(trans('polls.button.change_poll_setting'), [
                                            'class' => 'btn bg-cyan btn-block btn-lg waves-effect',
                                            'name' => 'btn_edit',
                                        ])
                                    }}
                                </div>
                                <div class="col-lg-3">
                                    <a href="{{ route('admin.poll.index') }}" class="btn bg-brown btn-block btn-lg waves-effect">
                                        {{ trans('polls.button.back') }}
                                    </a>
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
