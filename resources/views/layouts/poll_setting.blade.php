@if (isset($page) && $page == "edit")
    {{
       Form::open([
           'route' => ['user-poll.update', $poll->id],
           'method' => 'PUT',
           'id' => 'form_update_poll_setting',
           'role' => 'form',
           'onsubmit' => 'return updatePollSetting()',
       ])
    }}
@endif
@foreach ($data['viewData']['settings'] as $settingKey => $settingText)
    @if (isset($page)
        && ($page == 'edit' || $page == 'duplicate')
        && $settingKey == config('settings.setting.required')
        && (array_key_exists(config('settings.setting.required_email'), $setting)
            || array_key_exists(config('settings.setting.required_name'), $setting)
            || array_key_exists(config('settings.setting.required_name_and_email'), $setting)))
        <div class="form-group">
            <label>
                {{
                    Form::checkbox('setting[' . $settingKey . ']', $settingKey, true, [
                        'onchange' => 'settingAdvance(' . $settingKey . ')',
                        'class' => 'switch-checkbox-setting'
                    ])
                }}
                <span class='span-text-setting'>{{ $settingText }} </span>
            </label>
        </div>
    @else
        <div class="form-group">
            <label>
                {{
                    Form::checkbox('setting[' . $settingKey . ']', $settingKey,
                        (isset($page)
                        && ($page == 'edit' || $page == 'duplicate')
                        && array_key_exists($settingKey, $setting)) ? true : null, [
                            'onchange' => 'settingAdvance(' . $settingKey . ')',
                            'class' => 'switch-checkbox-setting'
                        ])
                }}
                <span class='span-text-setting'>{{ $settingText }} </span>
            </label>
        </div>
    @endif
    @if ($settingKey == config('settings.setting.required'))
        <div class="form-group {{ (isset($page)
                                && ($page == 'edit' || $page == 'duplicate')
                                && $settingKey == config('settings.setting.required')
                                && (array_key_exists(config('settings.setting.required_email'), $setting)
                                    || array_key_exists(config('settings.setting.required_name'), $setting)
                                    || array_key_exists(config('settings.setting.required_name_and_email'), $setting))) ? "" : "setting-advance" }}"
             id="setting-required">
            <label class="radio-inline radio-setting-required">
                {{ Form::radio('setting_child[required]', config('settings.setting.required_name'), true) }}
                {{ trans('polls.label.setting.required_name') }}
            </label>
            <label class="radio-inline radio-setting-required">
                {{
                    Form::radio('setting_child[required]', config('settings.setting.required_email'),
                        (isset($page)
                        && ($page == 'edit' || $page == 'duplicate')
                        && $settingKey == config('settings.setting.required')
                        && array_key_exists(config('settings.setting.required_email'), $setting)) ? true : null)
                }}
                {{ trans('polls.label.setting.required_email') }}
            </label>
            <label class="radio-inline radio-setting-required">
                {{
                    Form::radio('setting_child[required]',
                        config('settings.setting.required_name_and_email'),
                        (isset($page)
                        && ($page == 'edit' || $page == 'duplicate')
                        && $settingKey == config('settings.setting.required')
                        && array_key_exists(config('settings.setting.required_name_and_email'), $setting)) ? true : null)
                }}
                {{ trans('polls.label.setting.required_name_and_email') }}
            </label>
        </div>
    @elseif ($settingKey == config('settings.setting.custom_link'))
        <div class="form-group
            {{ (isset($page)
            && ($page == 'edit' || $page == 'duplicate')
            && array_key_exists($settingKey, $setting)) ? "" : "setting-advance" }}" id="setting-link">
            <div class="row">
                <div class="col-lg-8">
                    <label class="col-xs-link">{{ str_limit(url('/') . config('settings.email.link_vote'), 30) }}</label>
                    <div class="input-group input-group-link">
                        <span class="input-group-addon input-group-addon-link">
                            {{ str_limit(url('/') . config('settings.email.link_vote'), 30) }}
                        </span>
                        {{
                            Form::text('value[link]',
                            (isset($page)
                            && ($page == 'edit' || $page == 'duplicate')
                            && array_key_exists($settingKey, $setting)) ? $setting[$settingKey] :
                                (isset($poll) && $poll && $poll->getTokenLink(config('settings.link_poll.vote'))
                                    ? $poll->getTokenLink(config('settings.link_poll.vote'))
                                    : str_random(config('settings.length_poll.link'))), [
                                'class' => 'form-control',
                                'id' => 'link',
                                'placeholder' => trans('polls.placeholder.token_link'),
                                'onkeyup' => 'checkLink()',
                            ])
                        }}
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="error_link"></div>
        </div>
    @elseif ($settingKey == config('settings.setting.set_limit'))
        <div class="form-group
            {{ (isset($page)
            && ($page == 'edit' || $page == 'duplicate')
            && array_key_exists($settingKey, $setting)) ? "" : "setting-advance" }}" id="setting-limit">
            <div class="row">
                <div class="col-lg-3">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-list-ol" aria-hidden="true"></i>
                        </span>
                        {{
                           Form::number('value[limit]',
                                (isset($page)
                                && ($page == 'edit' || $page == 'duplicate')
                                && array_key_exists($settingKey, $setting)) ? $setting[$settingKey] : null, [
                               'class' => 'form-control',
                               'id' => 'limit',
                               'min' => (isset($page) && $page == 'edit') ? $totalVote : 1,
                               'max' => 1000,
                               'placeholder' => trans('polls.placeholder.number_limit'),
                               'onkeyup' => 'checkLimit()',
                           ])
                        }}
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="error_limit"></div>
        </div>
    @elseif ($settingKey == config('settings.setting.set_password'))
        <div class="form-group
            {{ (isset($page)
            && ($page == 'edit' || $page == 'duplicate')
            && array_key_exists($settingKey, $setting)) ? "" : "setting-advance" }}" id="setting-password">
            <div class="row">
                <div class="col-lg-6">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-key" aria-hidden="true"></i>
                        </span>
                        @if (isset($page) && $page == "edit")
                            {{
                                Form::text('value[password]',
                                (isset($page)
                                && ($page == 'edit' || $page == 'duplicate')
                                && array_key_exists($settingKey, $setting)) ? $setting[$settingKey] : null, [
                                    'class' => 'form-control',
                                    'id' => 'password',
                                    'placeholder' => trans('polls.placeholder.password_poll'),
                                    'onkeyup' => 'checkPassword()',
                                ])
                            }}
                        @else
                            {{
                                Form::password('value[password]', [
                                    'class' => 'form-control',
                                    'id' => 'password',
                                    'placeholder' => trans('polls.placeholder.password_poll'),
                                    'onkeyup' => 'checkPassword()',
                                ])
                            }}
                        @endif

                        <span class="input-group-btn">
                            <button class="btn btn-default show-password" type="button" id="show" onclick="showAndHidePassword()">
                                 @if (isset($page) && $page == "edit")
                                    <i class="fa fa-eye-slash" aria-hidden="true"></i>
                                 @else
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                 @endif
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="error_password"></div>
        </div>
    @else
       @continue
    @endif
@endforeach
@if (isset($page) && $page == "edit")
    <input type="submit" class="btn btn-success btn-edit-info btn-xs" name="btn_edit" value="{{ trans('polls.button.save_setting') }}">
    <a href="{{ $poll->getAdminLink() }}" class="btn btn-success btn-back-edit btn-xs">{{ trans('polls.button.edit_back') }}</a>
    {{ Form::close() }}
@endif
