@extends('layouts.app')

@section('content')
{{--<script src="https://cdn.socket.io/socket.io-1.3.4.js"></script>--}}
@push('show-error-scripts')

<!-- ---------------------------------
        Javascript of detail poll
    ---------------------------------------->
<!-- SOCKET IO -->
{!! Html::script('bower/socket.io-client/dist/socket.io.min.js') !!}

<!-- VOTE SOCKET-->
{!! Html::script(elixir('js/voteSocket.js')) !!}

@endpush
<div class="hide_vote_socket"
     data-host="{{ config('app.key_program.socket_host') }}"
     data-port="{{ config('app.key_program.socket_port') }}">
</div>
<div class="col-lg-4 col-lg-offset-4 info-mobile info-destop">
    <div class="hide-poll-closed" data-poll-id="{{ $pollId }}"></div>
    <div class="panel panel-default panel-darkcyan">
        <div class="panel-heading panel-heading-darkcyan">
        </div>
        <div class="panel-heading panel-heading-darkcyan none-tag-mobile">
            <span class="glyphicon glyphicon-info-sign"></span> {{ trans('label.errors') }}
        </div>
        <div class="panel-heading panel-heading-message-mobile none-in-laptop">
            <div class="tag-info-mobile tag-message-mobile">
                <span class="glyphicon glyphicon-info-sign"></span> {{ trans('label.errors') }}
            </div>
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

