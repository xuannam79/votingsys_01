<footer>
    <div class="col-lg-3 col-md-3 col-sm-3 footer-left">
        <p><b class="char-app">F</b><label>poll</label></p>
        <p>
            <a href="{{ config('settings.copyright') }}" target="_blank">
                <img src="{{ asset('uploads/images/logo.png') }}" class="copyright-image">
            </a>
            {!! trans('label.footer.copyright') !!}
        </p>
    </div>
    <div class="col-lg-5 col-md-5 col-sm-5 footer-center">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <p><i class="fa fa-map-marker" aria-hidden="true"></i> {{ trans('label.footer.location') }}</p>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12">
            <p><i class="fa fa-phone" aria-hidden="true"></i> {{ trans('label.footer.phone') }}</p>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12">
            <p><i class="fa fa-envelope" aria-hidden="true"></i> {{ trans('label.footer.email') }}</p>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 footer-right">
        <p>{{ trans('label.footer.about') }}</p>
        <a href="{{ trans('label.footer.facebook') }}" target="_blank" class="btn btn-primary">
            <span><i class="fa fa-facebook" aria-hidden="true"></i></span>
        </a>
        <a href="{{ trans('label.footer.github') }}" target="_blank" class="btn btn-warning">
            <span><i class="fa fa-github" aria-hidden="true"></i></span>
        </a>
        <a href="{{ trans('label.footer.linkedin') }}" target="_blank" class="btn btn-success">
            <span><i class="fa fa-linkedin" aria-hidden="true"></i></span>
        </a>
    </div>
</footer>
