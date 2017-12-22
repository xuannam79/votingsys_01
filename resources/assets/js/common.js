/**
 *
 * sendMailAgain: page -> result create poll
 *
 */
function sendMailAgain() {
    var poll = $('.hide').data('emailPoll');
    var link = $('.hide').data('emailLink');
    var password = $('.hide').data('emailPassword');
    var message = $('.hide').data('emailMessage');
    $('.loader').show();
    $.ajax({
        url: $('.hide').data("emailRoute"),
        type: 'post',
        data: {
            'poll': poll,
            'link': link,
            'password': password,
            '_token': $('.hide').data("emailToken")
        },
        success: function (data) {
            $('.loader').hide();
            if (data.success) {
                $('.message-send-mail').html("<div class='alert alert-success alert-dismissable'>"
                    + message.send_email_success + "</div>");
            } else {
                $('.message-send-mail').html("<div class='alert alert-danger alert-dismissable'>"
                    + message.send_email_fail + "</div>");
            }
        }
    });
}

//added in 19/12 when fix view for mobile
$(function () {
    //add class container-fluid-mobile to div.container-fluid when at page result create poll
    $('.create-poll-finish-mobile').parent().addClass('container-fluid-mobile')
})
