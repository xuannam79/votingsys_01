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
        var socketData = $.parseJSON(data);

        if (socketData.success && socketData.poll_id == pollId) {
            $('.count-participant').html(socketData.count_participant);
            jQuery.each(socketData.result, function(key, value ) {
                //update count vote latest
                $('#id1' + value.option_id).html(value.count_vote);
                $('#id2' + value.option_id).html(value.count_vote);
                $('.result-vote-poll').empty();
                $('.result-vote-poll').append(socketData.html_result_vote)
                $('.model-show-details').empty();
                $('.model-show-details').off('keyup');
                $('.model-show-details').append(socketData.html);
                $('.search-row-detail').searchRow();
            });

            if (typeof socketData.horizontalOption != 'undefined') {
                let settings = socketData.horizontalOption;
                let options = settings.optionDetail;

                for (let idOption in options) {
                    if (!settings.isHideResult && !settings.isOwner) {
                        $(".list-option-" + idOption).find(".voters-info").html(options[idOption]["list_voters"]);
                    }

                    if (settings.isLimit || settings.isClosed || settings.isTimeOut) {
                        let $optionInfo = $(".list-option-" + idOption).find(".option-info");
                        let $overrideContent = $('<p>').addClass('content-option-choose').text(options[idOption]["nameOption"]);

                        $optionInfo.empty();
                        $optionInfo.html($overrideContent)
                    }
                }

                $('[data-toggle="tooltip"]').tooltip();
            }

            if (typeof socketData.verticalOption != 'undefined') {
                $('.vertical-overflow').html(socketData.verticalOption);
            }

            if (typeof socketData.timelineOption != 'undefined') {
                var $table = $(socketData.timelineOption);
                var url = $('#timeline').data('get-cookie');

                $.get(url, function (data) {
                    $('.tb-option').remove();

                    $table.find('.nsep.action-box').each(function (index) {
                        var $actionbox = $(this);
                        var dataAction = $actionbox.find('.inline-edit').data('edit-voted')

                        // Condition to allow edit or delete option that was voted
                        var userChoiceSingle = data.user && dataAction.vote_id && data.user.id == dataAction.id;
                        var userChoiceMutiple = data.user && dataAction.user_id && data.user.id == dataAction.user_id;
                        var haveCookie = data.cookie && data.cookie.indexOf(dataAction.id) > -1;
                        var participantChoice = !dataAction.vote_id && !dataAction.user_id && haveCookie;

                        if (userChoiceSingle || userChoiceMutiple || participantChoice) {
                            $actionbox.removeClass('hide');
                        } else {
                            $actionbox.removeClass('hide').empty();
                        }
                    });

                    if ($('.fixed-header').length) {
                        $('.fixed-header').after($table);

                        createWaypointThead();

                        $('.td-fixed-check').remove();
                        createFixedTfoot();

                        return;
                    }

                    $('#timeline').append($table);

                    createFixedTfoot();
                }, 'json');
            }

            if ($('.bar-pie-chart').html() == "") {
                $('.bar-pie-chart').html(socketData.html_pie_bar_chart);
            }

            if ($('.manage-poll-count-participant').text() == '0') {
                $('.li-result-table').removeClass('hide-result-li');
                $('.pie_bar_chart_manage').append(socketData.html_pie_bar_manage_chart);
                $('.manage-poll-count-participant').text('1');
            }

            $('.delete-all-participants-soket').css('display', 'block');
            $('.div-delete-participant').removeClass('col-md-activity-jp');
            $('.btn-duplicate').css('display', 'none');
            $('.menu-add-soket').css('display', 'block');

            $('.show-piechart').empty();
            $('.show-piechart').append(socketData.htmlPieChart);
            $('.show-barchart').empty();
            $('.show-barchart').append(socketData.htmlBarChart);
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

        var scrollHeight = $('.comments').prop('scrollHeight');
        if (scrollHeight > 300) {
            $('.comments').animate({
                    scrollTop: scrollHeight
            }, 1000);
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

    socket.on('emit-close-poll', function (data) {
        if (link = data.link) {
            window.location.href = link;
        }
    })

    // auto close poll when time out
    var userLink = $('.js-date-close').data('link');
    var dateClose = $('.js-date-close').data('date-close');
    var now = moment().unix();
    var timeStart = ((dateClose - now) * 1000) + 1000;

    if (dateClose != '' && now < dateClose) {
        setTimeout(function () {
            socket.emit('close-poll', userLink);
        }, timeStart)
    }
});
