$(document).ready(function(){

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.loader').hide();
    $('#label_link_user').attr('href', $('.token-user').val());
    $('#label_link_admin').attr('href', $('.token-admin').val());

    $('.edit-link-user').click(function(e){
        e.preventDefault();
        $('.loader').show();
        divChangeAmount = $(this).parent();
        var tokenLinkUser = divChangeAmount.data('tokenLinkUser');
        var pollId = $('.hide').data('pollId');
        var route = $('.hide').data('route');
        var linkExist = $('.hide').data('linkExist');
        var linkInvalid = $('.hide').data('linkInvalid');
        var editLinkSuccess = $('.hide').data('editLinkSuccess');
        var tokenInput = $('.token-user').val();

        if (tokenInput == '') {
            $('.loader').hide();

            return;
        }

        if ($('.latest-token-user').html() == tokenInput) {
            $('.loader').hide();
            $('.message-link-user').html('');
            $('#title-success').html('');
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
                $('.loader').hide();

                if (data.success) {
                    $('#label_link_user').attr('href', $('.token-user').val());
                    $('.message-link-user').html(editLinkSuccess);
                    $('.latest-token-user').html(data.token_link);
                    hideLabelMessage('.message-link-user');
                }
            }
        });
    });

    $('.edit-link-admin').click(function(e){
        e.preventDefault();
        $('.loader').show();
        divChangeAmount = $(this).parent();
        var tokenLinkUser = divChangeAmount.data('tokenLinkAdmin');
        var pollId = $('.hide').data('pollId');
        var route = $('.hide').data('route');
        var linkExist = $('.hide').data('linkExist');
        var linkInvalid = $('.hide').data('linkInvalid');
        var editLinkSuccess = $('.hide').data('editLinkAdminSuccess');
        var tokenInput = $('.token-admin').val();

        if (tokenInput == '') {
            $('.loader').hide();

            return;
        }

        if (tokenLinkUser == tokenInput) {
            $('.loader').hide();

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
                $('.loader').hide();

                if (data.success) {
                     swal({
                        title: editLinkSuccess,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    setTimeout(function(){
                        window.location.href = $('.hide').data('urlAdmin') + "/" + tokenInput;
                    } , 2000);
                }
            }
        });
    });
});
