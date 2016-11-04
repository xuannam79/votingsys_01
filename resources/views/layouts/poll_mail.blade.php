<!DOCTYPE html>
<html>
    <head>
        <style>
            .head-participant h1 {
                text-align: center;
                color: white;
                margin: 100px
            }

            .panel {
                border: 1px solid black;
                background: white;
                height: 300px;
            }

            .introduction-website {
                width: 800px;
                margin: 0 auto;
            }

            .panel-header {
                background: rgb(5, 100, 146);
                color: white;
                height: 50px;
                padding-top: 20px;
            }

            .panel-header h3 {
                margin: 0;
                text-align: center;
            }

            .panel-body {
                padding: 20px;
            }

            .btn-admin {
                background: rgb(5, 100, 146);
                color: white;
                height: 50px;
                width: 150px;
                margin-left: 300px;
                box-shadow: 2px 2px 2px black;
            }
        </style>
    </head>
    <body>
        <div class="head-participant">
            <h1>{{ trans('mails.participant.head') }}</h1>
        </div>
        <div class="introduction-website">
            <div class="panel">
                <div class="panel-header">
                    <h3>{{ trans('mails.label.introduction') }}</h3>
                </div>
                <div class="panel-body">
                    <p>{!! trans('mails.introduction') !!} </p>
                    <h3>{{ trans('mails.participant.link') }} <a href="{{ $link }}" target="_blank">{{ $link }}</a></h3>
                    @if ($administration)
                        <a href="{{ $linkAdmin }}">
                            <button class="btn-admin"><h3>{{ trans('polls.button.administration') }}</h3></button>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </body>
</html>
