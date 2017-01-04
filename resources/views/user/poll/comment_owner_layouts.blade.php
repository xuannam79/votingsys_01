<div class="col-md-12" id="{{ $commentId }}">
    <br>
    <div class="col-md-1 col-lg-1">
        <img class="img-comment img-circle" src="{!! $imageComment !!}">
    </div>
    <div class="col-md-11 col-lg-11">
        <label data-comment-id="{{ $commentId }}" data-poll-id="{{ $pollId }}">
            <label class="user-comment">{{{ $name }}}</label>
            {{ $createdAt }}
        </label>
        <br>
        <p class="comment-text">{{{ $content }}}</p>
    </div>
</div>
