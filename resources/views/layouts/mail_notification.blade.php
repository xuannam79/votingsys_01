<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="{{ asset('css/layout/mail_notification.css') }}">
</head>
<body>
<div class="header-mail">
    <h1>{{ trans('label.mail.edit_poll.head') }}</h1>
</div>
<h3>{{ trans('label.mail.edit_poll.summary') }}</h3>
<table>
    <thead>
    <tr>
        <th>{{ trans('label.mail.edit_poll.thead.STT') }}</th>
        <th>{{ trans('label.mail.edit_poll.thead.info') }}</th>
        <th>{{ trans('label.mail.edit_poll.thead.old_data') }}</th>
        <th>{{ trans('label.mail.edit_poll.thead.new_data') }}</th>
        <th>{{ trans('label.mail.edit_poll.thead.date') }}</th>
    </tr>
    </thead>
    <tbody>
    @for ($index = 0; $index < count($data); $index++)
        @foreach($data[$index] as $key => $value)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $key }}</td>
                <td>{{ $old[$index][$key] }}</td>
                <td>{{ $value }}</td>
                <td>{{ $now }}</td>
            </tr>
        @endforeach
    @endfor
    </tbody>
</table>
</body>
</html>
