$(document).ready(function(){

    var requiredPassword = $('.hide-password').data('requiredPassword');

    if (requiredPassword != '') {
        $('.details-poll').hide();
    } else {
        $('.details-poll').show();
    }

    $('.password').keyup(function(e) {
        e.preventDefault();
        var password = $('.password').val();
        var route = $('.hide-password').data('linkPassword');
        var pollId = $('.hide-password').data('pollId');
        var messageRequiredPassword = $('.hide-password').data('messageRequiredPassword');

        $.ajax({
            type: 'POST',
            url: route,
            dataType: 'JSON',
            data: {
                'password': password,
                'poll_id': pollId,
            },
            success: function(data){
                if (data.success) {
                    $('.details-poll').show();
                    $('.modal-dialog-password').empty();
                     $('.message-required-password').html('');
                } else {
                    $('.details-poll').hide();
                    $('.modal-dialog-password').show();
                    $('.message-required-password').html(messageRequiredPassword);
                }
            }
        });
    });
});
