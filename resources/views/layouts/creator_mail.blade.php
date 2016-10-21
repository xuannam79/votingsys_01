<!DOCTYPE html>
<html>
<head>
    <style>
        h1 {
            text-align: center;
        }
    </style>
</head>
<body>
<h1>{{ trans('label.mail.head') }}</h1>
<hr>
<h3>{{ trans('label.mail.link_vote') }}</h3> <a href="{{ $link }}" target="_blank">{{ $link }}</a>
<h3>{{ trans('label.mail.link_admin') }}</h3> <a href="{{ $linkAdmin }}" target="_blank">{{ $linkAdmin }}</a>
</body>
</html>
