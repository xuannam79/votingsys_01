@extends('layouts.app')
@section('title')
    {{ trans('polls.title') }}
@endsection
@section('content')
    <div class="container">
        <div class="hide" data-poll="{{ $dataJson }}"
             data-action="edit"
             data-route-link="{{ route('link.store') }}"
             data-route-email="{{ route('email.store') }}"
             data-token="{{ csrf_token() }}"></div>
        <div class="row">
            <section>
                <div class="wizard create-poll">
                    <div class="wizard-inner">
                        <div class="connecting-line"></div>
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#step1" data-toggle="tab" aria-controls="step1" role="tab" title="Step 1">
                            <span class="round-tab">
                                <i class="glyphicon glyphicon-info-sign"></i>
                            </span>
                                </a>
                            </li>

                            <li role="presentation" class="disabled">
                                <a href="#step2" data-toggle="tab" aria-controls="step2" role="tab" title="Step 2">
                            <span class="round-tab">
                                <i class="glyphicon glyphicon-option-horizontal"></i>
                            </span>
                                </a>
                            </li>
                            <li role="presentation" class="disabled">
                                <a href="#step3" data-toggle="tab" aria-controls="step3" role="tab" title="Step 3">
                            <span class="round-tab">
                                <i class="glyphicon glyphicon-cog"></i>
                            </span>
                                </a>
                            </li>

                            <li role="presentation" class="disabled">
                                <a href="#complete" data-toggle="tab" aria-controls="complete" role="tab" title="Complete">
                            <span class="round-tab">
                                <i class="glyphicon glyphicon-user"></i>
                            </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    @include('layouts.error')
                    @include('layouts.message')

                    <div class="tab-content">

                        <!---------------------------------------------------/
                        /             INFORMATION                           /
                        /---------------------------------------------------->

                        <div class="tab-pane active" role="tabpanel" id="step1">
                            {{
                                Form::open([
                                    'route' => ['poll.update', $poll->id],
                                    'method' => 'PUT',
                                    'id' => 'create-poll',
                                    'enctype' => 'multipart/form-data',
                                    'role' => 'form',
                                ])
                            }}
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3>{{ strtoupper(trans('polls.label.step_1')) }}</h3>
                                    </div>
                                    <div class="panel-body">

                                        <!-- STATUS -->
                                        <div class="form-group" id="type">
                                            {{ Form::label(trans('polls.label_for.status'), trans('polls.label.status')) }}
                                            <label class="radio-inline">
                                                {{ Form::radio('status', config('settings.status.open'), ($poll->status == trans('polls.label.poll_opening')) ? true : null) }}
                                                {{ trans('polls.label.opening') }}
                                            </label>
                                            <label class="radio-inline">
                                                {{ Form::radio('status', config('settings.status.close'), ($poll->status == trans('polls.label.poll_closed')) ? true : null) }}
                                                {{ trans('polls.label.closed') }}
                                            </label>
                                        </div>

                                        <!-- TITLE -->
                                        <div class="form-group">
                                            {{ Form::label(trans('polls.label_for.title'), trans('polls.label.title')) }}
                                            {{
                                                Form::text('title', $poll->title, [
                                                    'class' => 'form-control',
                                                    'id' => trans('polls.label_for.title'),
                                                    'placeholder' => trans('polls.placeholder.title'),
                                                ])
                                            }}
                                        </div>

                                        <!-- LOCATION -->
                                        <div class="form-group">
                                            {{ Form::label(trans('polls.label_for.location'), trans('polls.label.location')) }}
                                            {{
                                                Form::text('location', $poll->location, [
                                                    'class' => 'form-control',
                                                    'id' => trans('polls.label_for.location'),
                                                    'placeholder' => trans('polls.placeholder.location'),
                                                ])
                                            }}
                                        </div>

                                        <!-- DESCRIPTION -->
                                        <div class="form-group">
                                            {{ Form::label(trans('polls.label_for.description'), trans('polls.label.description')) }}
                                            {{
                                                Form::textarea('description', $poll->description, [
                                                    'class' => 'form-control',
                                                    'id' => trans('polls.label_for.description'),
                                                    'placeholder' => trans('polls.placeholder.description'),
                                                ])
                                            }}
                                        </div>

                                        <!-- NAME -->
                                        <div class="form-group">
                                            {{ Form::label(trans('polls.label_for.full_name'), trans('polls.label.full_name')) }}
                                            {{
                                                Form::text('name', $poll->user->name, [
                                                    'class' => 'form-control',
                                                    'id' => trans('polls.label_for.full_name'),
                                                    'placeholder' => trans('polls.placeholder.full_name'),
                                                ])
                                            }}
                                        </div>

                                        <!-- EMAIL -->
                                        <div class="form-group">
                                            {{ Form::label(trans('polls.label_for.email'), trans('polls.label.email')) }}
                                            {{
                                                Form::text('email', $poll->user->email, [
                                                    'class' => 'form-control',
                                                    'id' => trans('polls.label_for.email'),
                                                    'placeholder' => trans('polls.placeholder.email'),
                                                ])
                                            }}
                                            <div class="email-error"></div>
                                        </div>

                                        <!-- CHATWORK -->
                                        <div class="form-group">
                                            {{ Form::label(trans('polls.label_for.chatwork'), trans('polls.label.chatwork')) }}
                                            {{
                                                Form::text('chatwork_id', $poll->user->chatwork_id, [
                                                    'class' => 'form-control',
                                                    'id' => trans('polls.label_for.chatwork'),
                                                    'placeholder' => trans('polls.placeholder.chatwork'),
                                                ])
                                            }}
                                        </div>

                                        <!-- TYPE -->
                                        <div class="form-group" id="type">
                                            {{ Form::label(trans('polls.label_for.type'), trans('polls.label.type')) }}
                                            <label class="radio-inline">
                                                {{ Form::radio('type', config('settings.type.single_choice'), ($poll->multiple == trans('polls.label.single_choice')) ? true : null) }}
                                                {{ trans('polls.label.single_choice') }}
                                            </label>
                                            <label class="radio-inline">
                                                {{ Form::radio('type', config('settings.type.multiple_choice'), ($poll->multiple == trans('polls.label.multiple_choice')) ? true : null) }}
                                                {{ trans('polls.label.multiple_choice') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <ul class="list-inline pull-right">
                                    <li>
                                        {{
                                            Form::submit(trans('polls.button.save_info'), [
                                                'class' => 'btn btn-success',
                                                'name' => 'btn_edit',
                                            ])
                                        }}

                                        {{
                                            Form::button(trans('polls.button.continue'), [
                                                'class' => 'btn btn-primary next-step',
                                                'value' => 'info',
                                            ])
                                        }}
                                    </li>
                                </ul>
                            {{ Form::close() }}
                        </div>


                        <!---------------------------------------------------/
                        /                   OPTION                           /
                        /---------------------------------------------------->

                        <div class="tab-pane" role="tabpanel" id="step2">
                            {{
                                Form::open([
                                    'route' => ['poll.update', $poll->id],
                                    'method' => 'PUT',
                                    'id' => 'create-poll',
                                    'enctype' => 'multipart/form-data',
                                    'role' => 'form',
                                ])
                            }}
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3>{{ strtoupper(trans('polls.label.step_2')) }}</h3>
                                    </div>
                                    <div class="panel-body option">
                                        <!-- OLD OPTION -->
                                        <div class="old-option">
                                            @foreach($poll->options as $option)
                                                <div id="{{ $option->id }}">
                                                    <div class="form-group">
                                                        {{ Form::text('option['. $option->id .']', $option->name, ['class' => 'form-control']) }}
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="row clearfix">
                                                            <div class="col-lg-3">
                                                                <img src="{{ asset(config('settings.option.path_image') . $option->image) }}" class="image-option">
                                                            </div>
                                                            <div class="col-lg-3">
                                                                <img id="preview_{{ $option->id }}" src="#" class="preview-image" />
                                                            </div>
                                                        </div>
                                                        <div class="row clearfix">
                                                            {{ Form::file('image['. $option->id .']', ['onchange' => 'readURL(this, "preview_' . $option->id . '")']) }}
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <button type="button" class="btn btn-danger" onclick="removeOpion('{{ $option->id }}', 'edit')">{{ trans('polls.button.remove') }}</button>
                                                    </div>
                                                    <hr>
                                                </div>
                                            @endforeach
                                        </div>
                                        <!-- OPTION LISTS -->
                                        <div class="poll-option"></div>

                                        <!-- BUTTON ADD OPTION -->
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="form-line">
                                                    {{
                                                        Form::text('number', config('settings.length_poll.number_option'), [
                                                            'class' => 'form-control',
                                                            'placeholder' => trans('polls.placeholder.number_add'),
                                                            'id' => 'number',
                                                        ])
                                                    }}
                                                </div>
                                                <span class="input-group-btn">
                                                    {{
                                                        Form::button('<span class="glyphicon glyphicon-plus"></span>', [
                                                            'class' => 'btn btn-default',
                                                            'onclick' => 'addOption(' . $dataJson . ')'
                                                        ])
                                                    }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <ul class="list-inline pull-right">
                                    <li>{{ Form::button(trans('polls.button.previous'), ['class' => 'btn btn-default prev-step']) }}</li>
                                    <li>
                                        {{
                                            Form::submit(trans('polls.button.save_option'), [
                                                'class' => 'btn btn-success',
                                                'name' => 'btn_edit',
                                            ])
                                        }}
                                        {{
                                            Form::button(trans('polls.button.continue'), [
                                                'class' => 'btn btn-primary next-step',
                                                'value' => 'option',
                                            ])
                                        }}
                                    </li>
                                </ul>
                            {{ Form::close() }}
                        </div>

                        <!---------------------------------------------------/
                        /                   SETTING                          /
                        /---------------------------------------------------->
                        <div class="tab-pane" role="tabpanel" id="step3">
                            {{
                                Form::open([
                                    'route' => ['poll.update', $poll->id],
                                    'method' => 'PUT',
                                    'id' => 'create-poll',
                                    'enctype' => 'multipart/form-data',
                                    'role' => 'form',
                                ])
                            }}
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3>{{ strtoupper(trans('polls.label.step_3')) }}</h3>
                                    </div>
                                    <div class="panel-body">
                                        @foreach ($dataView['setting'] as $key => $value)
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        {{ Form::checkbox('setting[]', $key, empty($setting[$key]) ? null : true, ['onchange' => 'settingAdvance(' . $key . ')']) }}
                                                    </span>
                                                    {{ Form::text('setting_text', $value, ['disabled' => true, 'class' => 'form-control']) }}
                                                </div>
                                            </div>

                                            <!-- SETTING: CUSTOM LINK -->
                                            @if ($key == config('settings.setting.custom_link'))
                                                <div class="form-group {{ empty($setting[$key]) ? "setting-advance" : "" }}" id="new-link">
                                                    {{
                                                        Form::label(
                                                            trans('polls.label_for.setting.custom_link'),
                                                            trans('polls.label.setting.custom_link')
                                                        )
                                                    }}
                                                    <div class="input-group">
                                                        <span class="input-group-addon">
                                                            {{ Form::text('url', url('/') . config('settings.email.link_vote'), ['disable' => true]) }}
                                                        </span>
                                                        {{
                                                            Form::text('value[link]', empty($setting[$key]) ? str_random(config('settings.length_poll.link')) : $setting[$key], [
                                                                'class' => 'form-control',
                                                                'id' => 'link',
                                                            ])
                                                        }}
                                                        <div class="link-error"></div>
                                                    </div>
                                                </div>

                                                <!-- SETTING: SET LIMIT -->
                                            @elseif ($key == config('settings.setting.set_limit'))
                                                <div class="form-group {{ empty($setting[$key]) ? "setting-advance" : "" }}" id="set-limit">
                                                    {{
                                                        Form::label(
                                                            trans('polls.label_for.setting.set_limit'),
                                                            trans('polls.label.setting.set_limit')
                                                        )
                                                    }}
                                                    {{
                                                        Form::text('value[limit]', empty($setting[$key]) ? null : $setting[$key], [
                                                            'class' => 'form-control',
                                                            'id' => 'limit',
                                                        ])
                                                    }}
                                                </div>

                                                <!-- SETTING: SET PASSWORD -->
                                            @elseif ($key == config('settings.setting.set_password'))
                                                <div class="form-group {{ empty($setting[$key]) ? "setting-advance" : "" }}" id="set-password">
                                                    {{
                                                        Form::label(
                                                            trans('polls.label_for.setting.set_password'),
                                                            trans('polls.label.setting.set_password')
                                                        )
                                                    }}
                                                    {{
                                                        Form::password('value[password]', [
                                                            'class' => 'form-control',
                                                            'id' => 'password',
                                                        ])
                                                    }}
                                                </div>
                                            @else
                                                @continue
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                <ul class="list-inline pull-right">
                                    <li>{{ Form::button(trans('polls.button.previous'), ['class' => 'btn btn-default prev-step']) }}</li>
                                    <li>
                                        {{
                                            Form::submit(trans('polls.button.save_setting'), [
                                                'class' => 'btn btn-success',
                                                'name' => 'btn_edit',
                                            ])
                                        }}
                                    </li>
                                </ul>
                            {{ Form::close() }}
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
