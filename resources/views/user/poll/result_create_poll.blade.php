@extends('layouts.app')
@section('content')
    <div class="col-lg-6 col-lg-offset-3">
        <div class="hide"
            data-email-poll="{{ json_encode($poll->toArray()) }}"
            data-email-link="{{ json_encode($link) }}"
            data-email-password="{{ (isset($password) && $password) ? $password : "" }}"
            data-email-route="{{ url('/check-email') }}"
            data-email-token="{{ csrf_token() }}"
            data-email-message="{{ json_encode(trans('polls.message_client')) }}" >
        </div>
        <div class="panel panel-default animated fadeInDown panel-darkcyan-profile">
            <div class="panel-heading panel-heading-darkcyan">
                {{ trans('polls.result_create.head') }}
            </div>
            <div class="panel-body">
                <div class=" col-lg-12">
                    <div class="message-send-mail col-lg-10 col-lg-offset-1">

                    </div>
                </div>
                <h3>{{ trans('polls.result_create.thank') }} {{ ($poll->user_id) ? $poll->user->name : $poll->name }}</h3>
                <h4>{{ trans('polls.result_create.create_success') }}</h4>
                <p>{{ trans('polls.result_create.send_mail', ['email' => ($poll->user_id) ? $poll->user->email : $poll->email]) }}</p>
                <p><a href="javascript:void(0);" onclick="sendMailAgain()">{{ trans('polls.send_mail_again') }}</a></p>
                <p><b>{{ trans('polls.result_create.participant_link') }}</b></p>
                <p><i>{{ trans('polls.result_create.help_participant') }}</i></p>
                <a href="{{ $poll->getUserLink() }}" target="_blank" id="linkVote">{{ str_limit($poll->getUserLink(), 47) }}</a>
                <button class="btn btn-success btn-xs" onclick="copyToClipboard('#linkVote', '{{ $poll->getUserLink() }}')">
                    <span class="glyphicon glyphicon-copy"></span> {{ trans('polls.copy_link') }}
                </button>
                @if (isset($password) && $password)
                    <p>{{ trans('label.password') }}: {{ $password }}</p>
                @endif
                <hr>
                <p><b>{{ trans('polls.result_create.link_admin') }}</b></p>
                <p><i>{{ trans('polls.result_create.help_admin') }}</i></p>
                <a href="{{ $poll->getAdminLink() }}" id="linkAdmin" target="_blank">{{ str_limit($poll->getAdminLink(), 47) }}</a>
                <button class="btn btn-success btn-xs" onclick="copyToClipboard('#linkAdmin', '{{ $poll->getAdminLink() }}')">
                    <span class="glyphicon glyphicon-copy"></span> {{ trans('polls.copy_link') }}
                </button>
            </div>
        </div>
    </div>
@endsection
@push('common-script')
    <!-- Common -->
    {!! Html::script('js/common.js') !!}
@endpush
