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
                                    Form::text('name', (auth()->user()) ? auth()->user()->name : null, [
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
                                    Form::text('email', (auth()->user()) ? auth()->user()->email : null, [
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
                                    Form::text('chatwork_id', (auth()->user()) ? auth()->user()->chatwork_id : null, [
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
                                Form::radio('type', config('settings.type_poll.single_choice'), null, [
                                    'id' => trans('polls.label_for.single_choice'),
                                    'class' => 'with-gap',
                                ])
                            }}
                            {{ Form::label(trans('polls.label_for.single_choice'), trans('polls.label.single_choice')) }}
                            {{
                                Form::radio('type', config('settings.type_poll.multiple_choice'), null, [
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
                            @foreach ($dataView['setting'] as $key => $value)
                            <div class="form-line">
                                {{
                                     Form::checkbox('setting[' . $key .']', $key, null, [
                                         'onchange' => 'settingAdvance(' . $key . ')',
                                         'id' => $key,
                                      ])
                                      }}
                                {{ Form::label($key, $value) }}
                            </div>
                            <!-- SETTING: CUSTOM LINK -->
                             @if ($key == config('settings.setting.custom_link'))
                                 <div class="input-group {{ is_null($dataView['oldInput']) ? "setting-advance" : "" }}" id="new-link">
                                 <span class="input-group-addon">{{ Form::label('url', url('/') . config('settings.email.link_vote')) }}</span>
                                  <div class="form-line">
                                    {{   Form::text('value[link]', str_random(config('settings.length_poll.link')), [
                                              'class' => 'form-control',
                                               'id' => 'link',
                                                ])
                                      }}
                                       </div>
                                       </div>
                                         <!-- SETTING: SET LIMIT -->
                           @elseif ($key == config('settings.setting.set_limit'))
                                <div class="{{ is_null($dataView['oldInput']) ? "setting-advance" : "" }}" id="set-limit">
                                      {{
                                      Form::label(
                                             trans('polls.label_for.setting.set_limit'),
                                             trans('polls.label.setting.set_limit')
                                         )
                                   }}
                                     <div class="form-group">
                                         <div class="form-line">
                                             {{
                                             Form::text('value[limit]', null, [
                                                 'class' => 'form-control',
                                                 'id' => 'limit',
                                             ])
                                         }}
                                         </div>
                                     </div>
                                  </div>
                                   <!-- SETTING: SET PASSWORD -->
                            @elseif ($key == config('settings.setting.set_password'))

                                <div class="{{ is_null($dataView['oldInput']) ? "setting-advance" : "" }}" id="set-password">
                                      {{
                                       Form::label(
                                           trans('polls.label_for.setting.set_password'),
                                             trans('polls.label.setting.set_password')
                                         )
                                     }}
                                     <div class="form-group">
                                         <div class="form-line">
                                             {{
                                         Form::password('value[password]', [
                                              'class' => 'form-control',
                                               'id' => 'password',
                                          ])
                                      }}
                                        </div>
                                 </div>
                                 </div>
                                   @else
                                 @continue
                             @endif
                         @endforeach
                    </fieldset>

                    <!-- STEP 4: PARTICIPANT -->
                    <h3>{{ trans('polls.label.step_4') }}</h3>
                    <fieldset>
                        <div class="form-group">
                            {{ Form::label(trans('polls.label_for.invite'), trans('polls.label.invite')) }}
                            {{
                                Form::radio('participant', config('settings.participant.invite_all'), true, [
                                      'id' => trans('polls.label_for.invite_all'),
                                      'class' => 'with-gap'
                                  ])
                            }}
                            {{ Form::label(trans('polls.label_for.invite_all'), trans('polls.label.invite_all')) }}
                            {{
                                Form::radio('participant', config('settings.participant.invite_people'), false, [
                                    'id' => trans('polls.label_for.invite_people'),
                                    'class' => 'with-gap'
                                ])
                            }}
                            {{ Form::label(trans('polls.label_for.invite_people'), trans('polls.label.invite_people')) }}
                        </div>
                        <div class="email-participant">
 <div class="form-group demo-tagsinput-area">
                                 {{ Form::label(trans('polls.label_for.invite'), trans('polls.label.invite')) }}
                                <div class="form-line">
                                {{
                                     Form::text('member', null, [
                                         'id' => 'member',
                                         'class' => 'form-control',
                                         'placeholder' => trans('polls.placeholder.email_participant'),
                                         'data-role' => 'tagsinput',
                                     ])
                                 }}
                                  </div>
                        </div>
                    </fieldset>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection
