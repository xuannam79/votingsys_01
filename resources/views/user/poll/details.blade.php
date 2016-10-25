@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">{{ trans('polls.poll_details') }}</div>
                <div class="panel-body">
                    <h4> {{ $poll->title }} </h4>
                    <p> {{ trans('polls.poll_initiate') }}
                        <label>{{ $poll->user->name }}</label>
                        <i>
                            <span class="label label-primary glyphicon glyphicon-user poll-details">
                                {{ $poll->countParticipants() }}
                            </span>
                            <span class="label label-info glyphicon glyphicon-comment poll-details">
                                <span class="comment-count">{{ $poll->countComments() }}</span>
                            </span>
                            <span class="label label-success glyphicon glyphicon-time poll-details">
                                {{ $poll->created_at->diffForHumans() }}
                            </span>
                        </i>
                    </p>
                    <label> {{trans('polls.where')}} </label>
                    <span>{{ $poll->location }}</span>
                    <br>
                    <i> {{ $poll->description }} </i>
                    <br><br>
                    {!! Form::open() !!}
                        @foreach ($poll->options as $option)
                            <div class="col-md-1 nopadding border">
                                <center>
                                @if ($poll->multiple)
                                    {!! Form::checkbox('option[]', $option->id, false, ['class' => 'poll-option']) !!}
                                @else
                                    {!! Form::radio('option[]', $option->id, false, ['class' => 'poll-option']) !!}
                                @endif
                                </center>
                            </div>
                            <div class="col-md-11 nopadding border">
                                <div class="col-md-9">
                                    {!! Form::label('option_name', $option->name, ['class' => 'poll-option']) !!}
                                </div>
                                @if ($option->image)
                                    <div class="col-md-2">
                                        <img class="poll-option img-option" src="{{ $option->showImage() }}">
                                    </div>
                                @endif
                                <div class="col-md-1">
                                    @if (!$isHideResult)
                                        <h1><span class="label label-default dropbtn">{{ $option->countVotes() }}</span></h1>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        <br>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
