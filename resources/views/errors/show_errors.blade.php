@extends('layouts.app')

@section('content')
<div class="col-lg-4 col-lg-offset-4">
    <div class="panel panel-default panel-darkcyan">
        <div class="panel-heading panel-heading-darkcyan">
            <span class="glyphicon glyphicon-info-sign"></span> {{ trans('label.errors') }}
        </div>
        <div class="panel-body">
            <center>
                <h3>{{ $message }}</h3>
                <br>
                <img style="max-height: 300px" class="img-responsive" src="{{ asset('uploads/images/finish.jpg') }}">
            </center>
        </div>
    </div>
</div>
@endsection
