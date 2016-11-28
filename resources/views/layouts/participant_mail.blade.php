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
        .link-invite {
            background: green;
            color: white;
            display: block;
            width: 200px;
            text-align: center;
            margin: 0 auto;

        }
        .hr-heading-body {
            width: 200px;
            border: 1px solid black;
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
            <p>{{ trans('label.mail.participant_vote.invite') }}</p>
            <p>
                <i class="link-invite">{{ trans('label.mail.create_poll.link_vote') }}</i><br>
                <a href="{{ $linkVote }}" target="_blank">{{ $linkVote }}</a>
            </p>
            @if ($password)
                <p>{{ trans('label.mail.create_poll.password') }} <span class="password">{{ $password }}</span></p>
            @endif
        </div>
        <p class="end">{{ trans('label.mail.create_poll.end') }}</p>
    </div>
</div>
</body>
</html>
