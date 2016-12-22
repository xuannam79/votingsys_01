@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-primary panel-darkcyan-profile">
                    <div class="panel-heading panel-heading-darkcyan">{{ trans('history.history') }}</div>
                    <div class="panel-body">
                        <span class="poll-history">{{ $poll->created_at->format(config('settings.date_format')) }}</span>
                        <h4 class="wrap-text"> {{ trans('history.poll_created', ['name' => ($poll->user_id) ? $poll->user->name : $poll->name]) }} </h4>
                        <br>
                        @if ($activities->count())
                            @foreach ($activities as $activity)
                                @if ($activity->type)
                                    <h4>
                                        @if (isset($activity->name))
                                            {!! $activity->getActivity($activity->name) !!}
                                            <span>
                                        {{ $activity->created_at->diffForHumans() }}
                                    </span>
                                        @elseif (isset($activity->user_id) && isset($activity->user))
                                            {!! $activity->getActivity($activity->user->name) !!}
                                            <span>
                                                {{ $activity->created_at->diffForHumans() }}
                                            </span>
                                        @else
                                            {!! $activity->getActivity('') !!}
                                            <span>
                                                {{ $activity->created_at->diffForHumans() }}
                                            </span>
                                        @endif
                                    </h4>
                                @endif
                            @endforeach
                        @else
                            <h3 class="poll-history">{{ trans('history.history_empty') }}</h3>
                        @endif
                        <br>
                        <a href="{{ $poll->getAdminLink() }}" class="btn btn-primary btn-darkcyan">
                            <span class="fa fa-backward"></span> {{ trans('history.back') }}
                        </a>
                    </div>
                    <div class="panel-footer">
                        {!! $activities->render() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
