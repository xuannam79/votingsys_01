<!DOCTYPE html>
<html>
    <head>
        <title>{{ trans('label.mail.create_poll.title') }}</title>
        <style>
            .content {
                background: darkcyan;
                padding: 50px;
            }
            .vote {
                display: block;
                margin: 50px auto;
                background: white;
                max-width: 500px;
                padding: 15px;
                box-shadow: 5px 5px 2px black;
            }
            .vote .heding {
                text-align: center;
            }
            .vote .body {
                padding:15px;
            }
            .dear {
                font-size: 20px;
            }
            .link-invite {
                background: green;
                color: white;
                display: block;
                width: 200px;
                text-align: center;
                margin: 0 auto;

            }
            .link-admin {
                background: orange;
                color: white;
                display: block;
                width: 400px;
                text-align: center;
                margin: 0 auto;
            }
            .hr-heading-body {
                width: 200px;
                border: 1px solid black;
            }
            .hr-body-footer {
                border: 1px solid darkcyan;
            }
            .btn-login {
                display: block;
                font-size: 15px;
                background: #337ab7;
                color: white;
                padding: 10px;
                border-radius: 10px;
                border: 1px solid #337ab7;
                box-shadow: 1px 1px 1px black;
            }
            .password {
                background: darkcyan;
                color: white;
                padding: 5px;
            }
            .box-info {
                border-width: 5px;
                border-style: double;
                padding: 10px;
            }
            .box-info .head {
                text-align: center;
            }
            .end {
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class="content">
            <div class="vote">
                <div class="heding">
                    <h2><b>{{ trans('label.mail.create_poll.head') }}</b></h2>
                </div>
                <hr class="hr-heading-body">
                <div class="body">
                    <p class="dear"><b>{{ trans('label.mail.create_poll.dear') }} {{ $userName }} </b></p>
                    <p> {!! trans('label.mail.create_poll.thank') !!}
                    <p>
                        <i class="link-invite">{{ trans('label.mail.create_poll.link_vote') }}</i><br>
                        <span>{{ trans('label.mail.create_poll.description_link_vote') }}</span>
                        <a href="{{ $linkVote }}" target="_blank">{{ $linkVote }}</a>
                    </p>
                    <p>
                        <i class="link-admin">{{ trans('label.mail.create_poll.link_admin') }}</i><br>
                        <span>{{ trans('label.mail.create_poll.description_link_admin') }}</span>
                        <a href="{{ $linkAdmin }}" target="_blank">{{ $linkAdmin }}</a>
                    </p>
                    @if ($password)
                        <p>{{ trans('label.mail.create_poll.password') }} <span class="password">{{ $password }}</span></p>
                    @endif
                    <div class="box-info">
                        <h4 class="head">{{ trans('polls.nav_tab_edit.info') }}</h4>
                        <p><b>{{ trans('polls.label.title') }}: </b> {{ (isset($title)) ? $title :  $poll->title }}</p>
                        <p><b>{{ trans('polls.label.description') }}: </b> {{ (isset($description)) ? $description : (($poll->description) ? $poll->description : trans('polls.label.no_data')) }}</p>
                        <p><b>{{ trans('polls.label.type') }}: </b> {{ (isset($type)) ? $type : $poll->multiple }}</p>
                        <p><b>{{ trans('polls.label.location') }}: </b> {{ (isset($location)) ? $location :  (($poll->location) ? $poll->location : trans('polls.label.no_data')) }}</p>
                        <p><b>{{ trans('polls.label.time_close') }}: </b> {{ (isset($closeDate)) ? $closeDate : (($poll->date_close) ? $poll->date_close : trans('polls.label.no_data')) }}</p>
                        <p><b>{{ trans('polls.label.created_at') }}: </b> {{ (isset($createdAt)) ? $createdAt : (($poll->created_at) ? $poll->created_at : trans('polls.label.no_data')) }}</p>
                    </div>
                </div>
                <p class="end">{{ trans('label.mail.create_poll.end') }}</p>
            </div>
        </div>
    </body>
</html>
