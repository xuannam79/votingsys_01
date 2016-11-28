$(document).ready(function(){

    $('.btn-initiated').on('click', function() {
        var routeInitiated = $('.hide').data('routeInitiated');
        var message = $('.hide').data('message');

        $.ajax({
            type: 'GET',
            url: routeInitiated,
            dataType: 'JSON',
            success: function(data){
                if (data.success) {
                    $('.polls-initiated').empty();
                    $('.polls-initiated').append(data.html);
                    $('.message-initiated-poll').html(message);
                }
            }
        });
    });

    $('.btn-participanted-in').on('click', function() {
        var routeParticipanted = $('.hide').data('routeParticipanted');
        var message = $('.hide').data('message');

        $.ajax({
            type: 'GET',
            url: routeParticipanted,
            dataType: 'JSON',
            success: function(data){
                if (data.success) {
                    $('.polls-participanted-in').empty();
                    $('.polls-participanted-in').append(data.html);
                    $('.message-participanted-in-poll').html(message);
                }
            }
        });
    });

    $('.btn-closed').on('click', function() {
        var routeClosed = $('.hide').data('routeClosed');
        var message = $('.hide').data('message');

        $.ajax({
            type: 'GET',
            url: routeClosed,
            dataType: 'JSON',
            success: function(data){
                if (data.success) {
                    $('.polls-closed').empty();
                    $('.polls-closed').append(data.html);
                    $('.message-closed-poll').html(message);
                }
            }
        });
    });
});
