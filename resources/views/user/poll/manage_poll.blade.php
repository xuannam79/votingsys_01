@extends('layouts.app')
@section('meta')
    <meta property="og:url" content="{{ $tokenLinkUser }}" />
    <meta property="og:type" content="article" />
    <meta property="og:title" ontent="{{ $poll->title }}" />
    <meta property="og:description" content="{{ $poll->description }}?" />
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">{{ trans('polls.poll_details') }}</div>
                <div class="panel-body">
                    <div class="hide" data-poll-id="{{ $poll->id }}" data-route="{{ url('poll') }}"
                        data-link-exist="{{ trans('polls.link_exist') }}" data-link-invalid="{{ trans('polls.link_invalid') }}"
                        data-edit-link-success="{{ trans('polls.edit_link_successfully') }}"
                        data-link="{{ url('link') }}">
                    </div>
                    <div class="col-md-12">
                        {{ Form::open(['route' => ['poll.destroy', $poll->id], 'method' => 'delete']) }}
                            {{
                                Form::button('<span class="glyphicon glyphicon-remove-sign"></span>' . ' ' . trans('polls.close_poll'), [
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-administration',
                                    'onclick' => 'return confirm("' . trans('polls.confirm_close_poll') . '")'
                                ])
                            }}
                        {{ Form::close() }}
                        <a href="{{ URL::action('User\ActivityController@show', $poll->id) }}" class="btn btn-primary  btn-administration">
                            <span class="glyphicon glyphicon-star-empty"></span>
                            {{ trans('polls.view_history') }}
                        </a>
                        <br><br>
                        <div class="col-md-12">
                            <i>{{ trans('polls.participation_link') }}</i>
                                <span class="glyphicon glyphicon-arrow-right btn-link-user"></span>
                            <br>
                            <div class="col-md-5">
                                <label>{{ url('link') }}</label>
                            </div>
                            <div class="col-md-3">
                                {{ Form::text('participation_link', $tokenLinkUser, ['class' => 'form-control token-user']) }}
                            </div>
                            <div class="col-md-2" data-token-link-user="{{ $tokenLinkUser }}">
                                {{ Form::button(trans('polls.edit_link_user'), ['class' => 'btn btn-success edit-link-user']) }}
                            </div>
                            <div class="col-md-2">
                                <label class="message-link-user"></label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <i>{{ trans('polls.administer_link') }}</i>
                            <span class="glyphicon glyphicon-arrow-right btn-link-admin"></span>
                            <br>
                            <div class="col-md-5">
                                <label>{{ url('link') }}</label>
                            </div>
                            <div class="col-md-3">
                                {{ Form::text('administer_link', $tokenLinkAdmin, ['class' => 'form-control token-admin']) }}
                            </div>
                            <div class="col-md-2" data-token-link-admin="{{ $tokenLinkAdmin }}">
                                {{ Form::button(trans('polls.edit_link_admin'), ['class' => 'btn btn-success edit-link-admin']) }}
                            </div>
                            <div class="col-md-2">
                                <label class="message-link-admin"></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
