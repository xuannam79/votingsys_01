$(document).ready(function(){

    $('.loader').hide();

    $('.btn-delete-participant').on('click', function() {
        var confirmDeleteParticipant = $('.hide').data('deleteParticipant');
        swal({
            title: confirmDeleteParticipant,
            type: "info",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            closeOnConfirm: true
        },
        function(){
            $('#form-delete-participant').submit();
            $('.loader').show();
        });
    });

    $('.close-poll').on('click', function() {
        swal({
            title: $('.hide').data('closePoll'),
            type: "info",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            closeOnConfirm: true
        },
        function(){
            $('#close-poll').submit();
            $('.loader').show();
        });
    });

    $('.reopen-poll').on('click', function() {
        var confirmReopenPoll = $('.hide').data('reopenPoll');
        var urlReopenPoll = $('.hide').data('urlReopenPoll');
        var pollId = $('.hide').data('pollId');
        swal({
            title: confirmReopenPoll,
            type: "info",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            closeOnConfirm: true
        },
        function(){
            window.location.href = urlReopenPoll + '/' + pollId + '/edit';
            $('.loader').show();
        });
    });

     $('#btn-register').on('click', function() {
        $('#form-register').submit();
        $('.loader').show();
    });

    $('.btn-reset-password').on('click', function() {
        $('#form-reset-password').submit();
        $('.loader').show();
    });
});
