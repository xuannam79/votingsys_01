@if (session('message'))
    <div class="alert alert-info message-infor">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <i class="icon fa fa-info"></i> {{ session('message') }}
    </div>
@endif
@if (isset($message))
    <div class="alert alert-info message-infor">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <i class="icon fa fa-info"></i> {{ $message }}
    </div>
@endif
