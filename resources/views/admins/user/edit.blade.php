@extends('admins.master')
@section('title')
    {{ trans('user.title') }}
@endsection
@section('content')
    <div class="card">
        <div class="header">
            <h2>
                {{ trans('user.panel_head.edit') }}
            </h2>
        </div>
        <div class="body">
        @include('layouts.error')
        @include('layouts.message')
        {{
            Form::open([
                'route' => ['admin.user.update', $user->id],
                'method' => 'PUT',
                'id' => 'form_update_user',
                'enctype' => 'multipart/form-data',
            ])
        }}
        <!-- FULL NAME -->
            <div class="form-group">
                {{ Form::label(trans('user.label_for.name'), trans('user.label.name') . trans('user.label.required')) }}
                <div class="form-line">
                    {{
                        Form::text('name', $user->name, [
                            'class' => 'form-control',
                            'id' => trans('user.label_for.name'),
                            'placeholder' => trans('user.placeholder.name'),
                            'required' => true,
                            'maxlength' => config('settings.length_user.name'),
                        ])
                    }}
                </div>
            </div>

            <!-- EMAIL -->
            {{ Form::label(trans('user.label_for.email'), trans('user.label.email') . trans('user.label.required')) }}
            <div class="form-group">
                <div class="form-line">
                    {{
                        Form::email('email', $user->email, [
                            'class' => 'form-control',
                            'id' => trans('user.label_for.email'),
                            'placeholder' => trans('user.placeholder.email'),
                            'required' => true,
                            'maxlength' => config('settings.length_user.email'),
                        ])
                    }}
                </div>
            </div>

            <!-- CHAT WORK -->
            {{ Form::label(trans('user.label_for.chatwork'), trans('user.label.chatwork')) }}
            <div class="form-group">
                <div class="form-line">
                    {{
                        Form::text('chatwork_id', $user->chatwork_id, [
                            'class' => 'form-control',
                            'id' => trans('user.label_for.chatwork'),
                            'placeholder' => trans('user.placeholder.chatwork'),
                            'maxlength' => config('settings.length_user.chatwork'),
                        ])
                    }}
                </div>
            </div>

            <!-- GENDER -->
            <div class="form-group">
                {{ Form::label(trans('user.label_for.gender.name'), trans('user.label.gender.name')) }}
                {{
                    Form::radio('gender', config('settings.gender_constant.male'), ($user->gender == trans('user.label.gender.male') ? true : null), [
                        'id' => trans('user.label_for.gender.male'),
                        'class' => 'with-gap',
                        'checked' => true,
                    ])
                }}
                {{ Form::label(trans('user.label_for.gender.male'), trans('user.label.gender.male')) }}
                {{
                    Form::radio('gender', config('settings.gender_constant.female'), ($user->gender == trans('user.label.gender.female') ? true : null), [
                        'id' => trans('user.label_for.gender.female'),
                        'class' => 'with-gap',
                    ])
                }}
                {{ Form::label(trans('user.label_for.gender.female'), trans('user.label.gender.female')) }}
                {{
                    Form::radio('gender', config('settings.gender_constant.other'), ($user->gender == trans('user.label.gender.other') ? true : null), [
                        'id' => trans('user.label_for.gender.other'),
                        'class' => 'with-gap',
                    ])
                }}
                {{ Form::label(trans('user.label_for.gender.other'), trans('user.label.gender.other')) }}
            </div>

            <!-- AVATAR -->
            {{ Form::label(trans('user.label_for.avatar'), trans('user.label.avatar')) }}
            <div class="form-group">
                <div class="form-line">
                    {{
                        Form::file('avatar', [
                            'id' => trans('user.label_for.avatar'),
                            'onchange' => 'readURL(this, "preview-avatar")',
                            'maxlength' => config('settings.length_user.avatar'),
                        ])
                    }}
                </div>
                <div class="row clearfix">
                    <div class="col-lg-3">
                        <img src="{{ $user->getAvatarPath() }}" class="avatar-new">
                    </div>
                    <div class="col-lg-3">
                        <img id="preview-avatar" src="#" class="preview-image avatar-new" />
                    </div>
                </div>
            </div>

            <!-- BUTTON -->
            <div class="row clearfix">
                {{
                    Form::submit(trans('user.button.edit'), [
                        'class' => 'col-lg-3 col-lg-offset-2 btn btn-success waves-effect'
                    ])
                }}
                <a href="{{ route('admin.user.index') }}" class="col-lg-3 col-lg-offset-2 btn btn-primary waves-effect">
                    {{ trans('user.button.back') }}
                </a>
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection
