<!DOCTYPE html>
<html>
    <head>
        <style>
            h1 {
                text-align: center;
            }
            table, th, td {
                border: 1px solid black;
                border-collapse: collapse;
            }
            th, td {
                padding: 15px;
            }
        </style>
    </head>
    <body>
        <h1>{{ trans('label.mail.head') }}</h1>
        <hr>
        <h3>{{ $title . ' voted ' . $optionName }}</h3>
        {{ trans('polls.link_vote') }}: <a href="{{ $linkUser }}" target="_blank">{{ $linkUser }}</a>
        <br>
        {{ trans('polls.link_admin') }}: <a href="{{ $linkAdmin }}" target="_blank">{{ $linkAdmin }}</a>
        <br>
        <table class="table table-bordered">
        <thead>
        <tr>
            <th>{{ trans('polls.no') }}</th>
            <th>{{ trans('polls.option.name_vote') }}</th>
            <th>{{ trans('polls.option.count_vote') }}</th>
            <th>{{ trans('polls.option.rate_vote') }}</th>
        </tr>
        </thead>
        <tbody>
            @php
                $countOption = 0;
            @endphp
            @foreach ($optionRate as $option)
                <tr>
                    <td><center>{{ ++$countOption }}</center></td>
                    <td><center>{{ str_limit($option['name'], 45) }}</center></td>
                    <td><center>{{ $option['count'] }}</center></td>
                    <td>
                        <center>
                            {{ $option['rate'] }}
                            %
                        </td>
                        </center>
                </tr>
            @endforeach
        </tbody>
        </table>
    </body>
</html>
