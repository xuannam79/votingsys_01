<!DOCTYPE html>
<html>
<head>
    <title>{{ trans('label.mail.create_poll.title') }}</title>
    <style>
        .content {
            background: darkcyan;
            padding: 50px;
        }
        .open-poll {
            display: block;
            margin: 50px auto;
            background: white;
            max-width: 500px;
            padding: 15px;
            box-shadow: 5px 5px 2px black;
        }
        .open-poll .heding {
            text-align: center;
        }
        .open-poll .body {
            padding:15px;
        }
        .dear {
            font-size: 20px;
        }
        .new-link {
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
        .hr-body-footer {
            border: 1px solid darkcyan;
        }
    </style>
</head>
<body>
<div class="content">
    <div class="open-poll">
        <div class="heding">
            <h2><b>{{ trans('label.mail.create_poll.head') }}</b></h2>
        </div>
        <hr class="hr-heading-body">
        <div class="body">
            {!! trans('label.mail.open_poll.thank') !!}
            <p>
                <i class="new-link">{{ trans('label.mail.open_poll.link_admin') }}</i><br>
                <a href="{{ $link }}" target="_blank">{{ $link }}</a>
            </p>
        </div>
       <hr class="hr-body-footer">
    </div>
</div>
</body>
</html>

