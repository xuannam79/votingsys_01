<!DOCTYPE html>
<html>
<head>
    <title>{{ trans('label.mail.create_poll.title') }}</title>
    <style>
        .content {
            background: darkcyan;
            padding: 50px;
        }
        .register {
            display: block;
            margin: 50px auto;
            background: white;
            max-width: 500px;
            padding: 15px;
            box-shadow: 5px 5px 2px black;
        }
        .register .heding {
            text-align: center;
        }
        .register .body {
            padding:15px;
        }
        .dear {
            font-size: 20px;
        }
        .link-active {
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
        .box-info .head {
            text-align: center;
        }
    </style>
</head>
<body>
<div class="content">
    <div class="register">
        <div class="heding">
            <h2><b>{{ trans('label.mail.create_poll.head') }}</b></h2>
        </div>

        <hr class="hr-heading-body">
        <div class="body">
            <p> {!! trans('label.mail.feedback.header_content') !!}
            <p>{!! nl2br($dataRequest['feedback']) !!}</p>
        </div>
        <hr class="hr-body-footer">
    </div>
</div>
</body>
</html>
