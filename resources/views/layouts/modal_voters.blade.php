<div class="input-group search-voters-box">
    {!! Form::text('searchTD', null, [
        'class' => 'form-control search-voters-row',
        'placeholder' => 'Search'
    ]) !!}
    <div class="input-group-btn">
        {{ Form::button('<i class="glyphicon glyphicon-search"></i>', ['class' => 'btn btn-yes']) }}
    </div>
</div>
<ul class="nav listing-sppoll">
    @foreach ($voters as $voter)
        <li class="voters-row">
            <p class="voters-box">
                <span class="search-avatar">
                    <img src="{{ $voter['avatar'] }}" alt="">
                </span>
                <span class="poll-info">
                    <span class="poll-detail">
                        <i class="fa fa-user"></i>
                        <small>{{ $voter['name'] }}</small>
                    </span>
                    <span class="poll-detail">
                        <i class="fa fa-envelope" aria-hidden="true"></i>
                        <small>{{ $voter['email'] }}</small>
                    </span>
                </span>
            </p>
        </li>
    @endforeach
</ul>
