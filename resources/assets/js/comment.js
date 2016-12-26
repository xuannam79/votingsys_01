$(document).ready(function(){

     $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#form-comment').show();
    $('#add-comment').on('click', function() {
        divChangeAmount = $(this).parent();

        if ( $('#form-comment').css('display') == 'none' ) {
            $('#form-comment').show();
            var labelHide = divChangeAmount.data('labelHide');
            $('#add-comment').html(labelHide);
        } else {
            $('#form-comment').hide();
            var labelAddComment = divChangeAmount.data('labelAddComment');
            $('#add-comment').html(labelAddComment);
        }
    });

    $('.comments').show();
    $('#show-hide-list-comment').on('click', function() {
        divChangeAmount = $(this).parent();

        if ( $('.comments').css('display') == 'none' ) {
            $('.comments').show();
            var labelHide = divChangeAmount.data('labelHide');
            $('#show-hide-list-comment').html(labelHide);
            $('.hr-comment').css('display', 'block');
        } else {
            $('.comments').hide();
            var labelShowComment = divChangeAmount.data('labelShowComment');
            $('#show-hide-list-comment').html(labelShowComment);
            $('.hr-comment').css('display', 'none');
        }
    });

    $('.addComment').click(function(e){
        e.preventDefault();
        divChangeAmount = $(this).parent();
        var pollId = divChangeAmount.data('pollId');
        var route = $('.hide').data('route');
        var user = divChangeAmount.data('user');
        var content = $('#content' + pollId).val();
        var name = $('#name' + pollId).val();

        var commentName = divChangeAmount.data('commentName');
        var commentContent = divChangeAmount.data('commentContent');
        var commentLimitName = divChangeAmount.data('commentLimitName');
        var commentLimitContent = divChangeAmount.data('commentLimitContent');
        var passValidate = true;

        var pollId = divChangeAmount.data('pollId');
        var content = $('#content' + pollId).val();
        var name = $('#name' + pollId).val();

        /* remove class error */
        $('.comment-info-name, .comment-info-content').removeClass('error');
        $('.comment-name-validate, .comment-content-validate').removeClass('alert alert-poll-set-ip').html('');

        if (name.trim() == '') {
            $('.comment-info-name').addClass('error');
            $('.comment-name-validate').addClass('alert alert-poll-set-ip')
                .html('<span class="glyphicon glyphicon-warning-sign"></span>' + ' ' + commentName);

            return false;
        }

        if (name.trim().length >= 100) {
            $('.comment-info-name').addClass('error');
            $('.comment-name-validate').addClass('alert alert-poll-set-ip')
                .html('<span class="glyphicon glyphicon-warning-sign"></span>' + ' ' + commentLimitName);

            return false;
        }

        if (content.trim() == '') {
            $('.comment-info-content').addClass('error');
            $('.comment-name-validate').addClass('alert alert-poll-set-ip')
                .html('<span class="glyphicon glyphicon-warning-sign"></span>' + ' ' + commentContent);

            return false;
        }

        if (content.trim().length >= 255) {
            $('.comment-info-content').addClass('error');
            $('.comment-name-validate').addClass('alert alert-poll-set-ip')
                .html('<span class="glyphicon glyphicon-warning-sign"></span>' + ' ' + commentLimitContent);

            return false;
        }

        $.ajax({
            type: 'POST',
            url: route,
            dataType: 'JSON',
            data: {
                'poll_id': pollId,
                'content': content,
                'name': name,
            },
            success: function(data){
                if (data.success) {
                    $('#content' + pollId).val('');
                    $('.comments').append(data.html);
                    var commentCount = $('.comment-count').html();
                    $('.comment-count').html(parseInt(commentCount) + 1);
                }
            }
        });
    });

    $(document).on( 'click', '.delete-comment', function (e) {
        e.preventDefault();
        var confirmRemove = $('.hide').data('confirmRemove');

        if (confirm(confirmRemove)) {
            divChangeAmount = $(this).parent();
            var commentId = divChangeAmount.data('commentId');
            var pollId = divChangeAmount.data('pollId');
            var route = $('.hide').data('route');

            $.ajax({
                type: 'POST',
                url: route + '/' + commentId,
                dataType: 'JSON',
                data: {
                    '_method': 'DELETE',
                    'comment_id': commentId,
                    'poll_id': pollId,
                },
                success: function(data){
                    if (data.success) {
                        $('#' + commentId).hide();
                        var commentCount = $('.comment-count').html();
                        $('.comment-count').html(parseInt(commentCount) - 1);
                    }
                }
            });
        }
    });
});
