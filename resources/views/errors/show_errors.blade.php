@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">{{ trans('label.errors') }}</div>
                <div class="panel-body">
                    <center>
                        <h1>{{ $message }}</h1>
                        <br>
                        <img class="img-errors" src="{{ asset('/uploads/images/vote.png') }}">
                    </center>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
