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
<div class="col-lg-4 col-lg-offset-4">
    <div class="hide-poll-closed"></div>
    <div class="panel panel-default panel-darkcyan">
        <div class="panel-heading panel-heading-darkcyan">
            <span class="glyphicon glyphicon-info-sign"></span> {{ trans('label.errors') }}
        </div>
        <div class="panel-body">
            <h5 class="text-danger">
                {{ $message }}
                <br>
            </h5>
            <div class="box-login-wsm-setting">
                <a class="btn btn-login btn-block btn-social btn-wsm" href="{{ url('redirect/framgia') }}">
                    <span class="fa wsm"> <img src="{{ asset('uploads/images/white_wsm.png') }}" alt="WSM"></span>
                    {{ trans('auth.wsm_login') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

