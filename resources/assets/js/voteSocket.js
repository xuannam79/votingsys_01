$(document).ready(function(){
    var host = $('.hide_vote_socket').data('host');
    var port = $('.hide_vote_socket').data('port');
    var link = (port == '') ? host : host + ":" + port;
    var socket = io.connect(link);

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

            if ($('.bar-pie-chart').html() == "") {
                $('.bar-pie-chart').html($.parseJSON(data).html_pie_bar_chart);
            }

            if ($('.manage-poll-count-participant').text() == '0') {
                $('.li-result-table').removeClass('hide-result-li');
                $('.pie_bar_chart_manage').append($.parseJSON(data).html_pie_bar_manage_chart);
                $('.manage-poll-count-participant').text('1');
            }

            $('.delete-all-participants-soket').css('display', 'block');
            $('.div-delete-participant').removeClass('col-md-activity-jp');
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
        var isOwnerPoll = $('.hide-vote').data('isOwnerPoll');

        if ($.parseJSON(data).success && $.parseJSON(data).poll_id == pollId) {
            if (isOwnerPoll == "1") {
                $('.comments').append($.parseJSON(data).htmlOwner);
            } else {
                $('.comments').append($.parseJSON(data).htmlNotOwner);
            }
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

    //socket close poll
    socket.on('editPoll', function (data) {
        var pollId = $('.hide-vote-details').data('pollId');

        if ($.parseJSON(data).success && $.parseJSON(data).poll_id == pollId) {
            window.location.href = $.parseJSON(data).link_user;
        }
    });
});
