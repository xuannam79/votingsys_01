<!DOCTYPE html>
<html>
<head>
    <title>{{ trans('label.mail.edit_option.title') }}</title>
    <style>
        .content {
            background: darkcyan;
            padding: 50px;
            min-height: 1000px;
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
        .hr-heading-body {
            width: 200px;
            border: 1px solid black;
        }
        h3 {
            text-align: center;
        }
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            margin: 10px auto;
            text-align: center;
        }
        th, td {
            padding: 10px;
        }
        .summary {
            background: orange;
            color: white;
            display: block;
            width: 400px;
            text-align: center;
            margin: 0 auto;
        }
        .option {
            padding: 5px;
            background: grey;
            color: white;
            margin: 10px auto;
        }
        .end {
            text-align: center;
        }
        p {
            word-wrap: break-word;
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
            <p class="dear"><b>{{ trans('label.mail.create_poll.dear') }} {{ $creatorName }} </b></p>
            <p class="summary">{{ trans('label.mail.edit_poll.summary') }}</p>
            <table>
                <thead>
                <tr>
                    <th>{{ trans('label.mail.edit_setting.old_setting') }}</th>
                    <th>{{ trans('label.mail.edit_setting.new_setting') }}</th>
                    <th>{{ trans('label.mail.edit_poll.thead.date') }}</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        @foreach ($oldSettings as $setting)
                            @foreach ($setting as $label => $value)
                                <p>{{ $label }}</p><span>{{ $value }}</span>
                            @endforeach
                        @endforeach
                    </td>
                    <td>
                        @foreach ($newSettings as $setting)
                            @foreach ($setting as $label => $value)
                                <p>{{ $label }}</p><span>{{ $value }}</span>
                            @endforeach
                        @endforeach
                    </td>
                    <td>
                        {{ $now }}
                    </td>
                </tr>
                </tbody>
            </table>
            <p>
                {{ trans('label.mail.edit_option.thank') }}
            </p>
            <p class="end">{{ trans('label.mail.create_poll.end') }}</p>
        </div>
    </div>
</div>
</body>
</html>
