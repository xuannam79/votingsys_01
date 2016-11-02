 @extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{ trans('history.history') }}</div>
                <div class="panel-body col-md-offset-1">
                <span class="poll-history">{{ $poll->created_at->format(config('settings.date_format')) }}</span>
                <h4> {{ trans('history.poll_created', ['name' => $poll->user->name]) }} </h4>
                <br>
                @if ($activities->count())
                    @foreach ($activities as $activity)
                        @if ($activity->type)
                            <h4>
                                @if ($activity->name)
                                    {!! $activity->getActivity($activity->name) !!}
                                @elseif ($activity->user_id)
                                    {!! $activity->getActivity($activity->user->name) !!}
                                @endif
                                <span>
                                    {{ $activity->created_at->diffForHumans() }}
                                </span>
                            </h4>
                        @endif
                    @endforeach
                @else
                    <h3 class="poll-history">{{ trans('history.history_empty') }}</h3>
                @endif
                <br>
                <a href="{{ URL::previous() }}" class="btn btn-default">{{ trans('history.back') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection
