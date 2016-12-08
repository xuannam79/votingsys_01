$(document).ready(function(){
    var socket = io.connect('http://localhost:8890');

    if ($('.manage-poll-count-participant').text() == '0') {
        $('.delete-all-participants').hide();
    }

    //socket vote poll
    socket.on('votes', function (data) {
        var pollId = $('.hide-vote').data('pollId');

        if ($.parseJSON(data).success && $.parseJSON(data).poll_id == pollId) {
            $('.count-participant').html($.parseJSON(data).count_participant);
            jQuery.each($.parseJSON(data).result, function(key, value ) {
                //update count vote latest
                $('#id1' + value.option_id).html(value.count_vote);
                $('#id2' + value.option_id).html(value.count_vote);
                $('.result-vote-poll').empty();
                $('.result-vote-poll').append($.parseJSON(data).html_result_vote)
                $('.model-show-details').empty();
                $('.model-show-details').append($.parseJSON(data).html);
            });

            if ($('.count-participant').text() == '1') {
                $('.bar-pie-chart').empty();
                $('.bar-pie-chart').append($.parseJSON(data).html_pie_bar_chart);
            }

            if ($('.manage-poll-count-participant').text() == '0') {
                $('.pie_bar_chart_manage').append($.parseJSON(data).html_pie_bar_manage_chart);
                $('.manage-poll-count-participant').text('1');
            }

            $('.delete-all-participants-soket').css('display', 'block');
            $('.btn-duplicate').css('display', 'none');
            $('.menu-add-soket').css('display', 'block');

            $('.show-piechart').empty();
            $('.show-piechart').append($.parseJSON(data).htmlPieChart);
            $('.show-barchart').empty();
            $('.show-barchart').append($.parseJSON(data).htmlBarChart);
        }
    });

    //socket comment poll
    socket.on('comment', function (data) {
        var pollId = $('.hide-vote').data('pollId');

        if ($.parseJSON(data).success && $.parseJSON(data).poll_id == pollId) {
            $('.comments').append($.parseJSON(data).html);
            var commentCount = $('.comment-count').html();
            $('.comment-count').html(parseInt(commentCount) + 1);
            $('#content' + pollId).val('');
        }
    });

    //socket close poll
    socket.on('closePoll', function (data) {
        var pollId = $('.hide-vote-details').data('pollId');

        if ($.parseJSON(data).success && $.parseJSON(data).poll_id == pollId) {
            window.location.href = $.parseJSON(data).link_user;
        }
    });

     //socket reopen poll
    socket.on('reopenPoll', function (data) {
        var pollId = $('.hide-poll-closed').data('pollId');

        if ($.parseJSON(data).success && $.parseJSON(data).poll_id == pollId) {
            window.location.href = $.parseJSON(data).link_user;
        }
    });

    //socket delete all participant of poll
    socket.on('deleteParticipant', function (data) {
        var pollId = $('.hide-vote').data('pollId');

        if ($.parseJSON(data).poll_id == pollId) {
            $('.count-participant').html('0');
            $('.model-show-details').empty();
            $('.show-piechart').empty();
            $('.show-barchart').empty();
            $('.bar-pie-chart').empty();
            $('.show-details_default').addClass('in active');
            $('.model-show-details').append($.parseJSON(data).modal_details_empty);
            jQuery.each($.parseJSON(data).result, function(key,value ) {
                $('#id1' + value.option_id).html('0');
                $('#id2' + value.option_id).html('0');
                $('#id3' + value.option_id).html('0');
            });
        }
    });
});
