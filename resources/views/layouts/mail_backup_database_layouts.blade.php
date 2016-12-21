<!DOCTYPE html>
<html>
<head>
    <title>{{ trans('label.mail.create_poll.title') }}</title>
    <style>
        .content {
            background: darkcyan;
            padding: 50px;
        }
        .backup_database {
            display: block;
            margin: 50px auto;
            background: white;
            max-width: 500px;
            padding: 15px;
            box-shadow: 5px 5px 2px black;
        }
        .hr-body-footer {
            border: 1px solid darkcyan;
        }
    </style>
</head>
<body>
<div class="content">
    <div class="backup_database">
        <div class="heading">
            <h2><b>{{ trans('label.mail.backup_database.head') }}</b></h2>
        </div>
    </div>
</div>
</body>
</html>
