@if (Session::has('message'))
    <div class="col-lg-12">
        <div class="col-lg-10 col-lg-offset-1 alert alert-success">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {!! Session::get('message') !!}
        </div>
    </div>
@endif
