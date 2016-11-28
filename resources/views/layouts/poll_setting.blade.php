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
    <div class="form-group">
        <label>
            <input type="checkbox" name="setting[{{ $settingKey }}]" {{ (isset($page) && ($page == 'edit' || $page == 'duplicate') && array_key_exists($settingKey, $setting)) ? "checked" : "" }}
            value="{{ $settingKey }}" onchange="settingAdvance('{{ $settingKey }}')"> {{ $settingText }}
        </label>
    </div>
    @if ($settingKey == config('settings.setting.custom_link'))
        <div class="form-group {{ (isset($page) && ($page == 'edit' || $page == 'duplicate') && array_key_exists($settingKey, $setting)) ? "" : "setting-advance" }}" id="setting-link">
            <div class="row">
                <div class="col-lg-8">
                    <div class="input-group">
                        <span class="input-group-addon">
                            {{ str_limit(url('/') . config('settings.email.link_vote'), 30) }}
                        </span>
                        {{
                            Form::text('value[link]', (isset($page) && ($page == 'edit' || $page == 'duplicate') && array_key_exists($settingKey, $setting)) ? $setting[$settingKey] : str_random(config('settings.length_poll.link')), [
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
        <div class="form-group {{ (isset($page) && ($page == 'edit' || $page == 'duplicate') && array_key_exists($settingKey, $setting)) ? "" : "setting-advance" }}" id="setting-limit">
            <div class="row">
                <div class="col-lg-3">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-list-ol" aria-hidden="true"></i>
                        </span>
                        {{
                           Form::number('value[limit]', (isset($page) && ($page == 'edit' || $page == 'duplicate') && array_key_exists($settingKey, $setting)) ? $setting[$settingKey] : null, [
                               'class' => 'form-control',
                               'id' => 'limit',
                               'min' => (isset($page) && $page == 'edit') ? $totalVote : null,
                               'max' => 1000,
                               'placeholder' => trans('polls.placeholder.number_limit'),
                               'oninput' => "validity.valid||(value='1');",
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
        <div class="form-group {{ (isset($page) && ($page == 'edit' || $page == 'duplicate') && array_key_exists($settingKey, $setting)) ? "" : "setting-advance" }}" id="setting-password">
            <div class="row">
                <div class="col-lg-6">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-key" aria-hidden="true"></i>
                        </span>
                        @if (isset($page) && $page == "edit")
                            {{
                                Form::text('value[password]', (isset($page) && ($page == 'edit' || $page == 'duplicate') && array_key_exists($settingKey, $setting)) ? $setting[$settingKey] : null, [
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
    <input type="submit" class="btn btn-success btn-edit-info" name="btn_edit" value="{{ trans('polls.button.save_setting') }}">
    <a href="{{ $poll->getAdminLink() }}" class="btn" style="background: darkcyan; border-color: darkcyan; border-radius: 0; color: white; float: right; box-shadow: 1px 1px 1px black">{{ trans('polls.button.edit_back') }}</a>
    {{ Form::close() }}
@endif
