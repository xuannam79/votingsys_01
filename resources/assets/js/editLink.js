$(document).ready(function(){

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.edit-link-user').click(function(e){
        e.preventDefault();
        divChangeAmount = $(this).parent();
        var tokenLinkUser = divChangeAmount.data('tokenLinkUser');
        var pollId = $('.hide').data('pollId');
        var route = $('.hide').data('route');
        var linkExist = $('.hide').data('linkExist');
        var linkInvalid = $('.hide').data('linkInvalid');
        var editLinkSuccess = $('.hide').data('editLinkSuccess');
        var tokenInput = $('.token-user').val();

        if (tokenInput == '') {
            $('.message-link-user').html(linkInvalid);

            return;
        }

        if (tokenLinkUser == tokenInput) {
            $('.message-link-user').html(linkExist);

            return;
        }

        $.ajax({
            type: 'POST',
            url: route + '/' + pollId,
            dataType: 'JSON',
            data: {
                '_method': 'PUT',
                'token_input': tokenInput,
                'is_link_admin': 0,
            },
            success: function(data){
                if (data.success) {
                    $('.message-link-user').html(editLinkSuccess);
                } else {
                    if (data.is_exist) {
                        $('.message-link-user').html(linkExist);
                    } else {
                        $('.message-link-user').html(linkInvalid);
                    }
                }
            }
        });
    });

    $('.edit-link-admin').click(function(e){
        e.preventDefault();
        divChangeAmount = $(this).parent();
        var tokenLinkUser = divChangeAmount.data('tokenLinkAdmin');
        var pollId = $('.hide').data('pollId');
        var route = $('.hide').data('route');
        var linkExist = $('.hide').data('linkExist');
        var linkInvalid = $('.hide').data('linkInvalid');
        var editLinkSuccess = $('.hide').data('editLinkSuccess');
        var tokenInput = $('.token-admin').val();

        if (tokenInput == '') {
            $('.message-link-admin').html(linkInvalid);

            return;
        }

        if (tokenLinkUser == tokenInput) {
            $('.message-link-admin').html(linkExist);

            return;
        }

        $.ajax({
            type: 'POST',
            url: route + '/' + pollId,
            dataType: 'JSON',
            data: {
                '_method': 'PUT',
                'token_input': tokenInput,
                'is_link_admin': 1,
            },
            success: function(data){
                if (data.success) {
                    $('.message-link-admin').html(editLinkSuccess);
                } else {
                    if (data.is_exist) {
                        $('.message-link-admin').html(linkExist);
                    } else {
                        $('.message-link-admin').html(linkInvalid);
                    }
                }
            }
        });
    });
});
