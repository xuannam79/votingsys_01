$(document).ready(function(){

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.btn-vote').attr('disabled', true);
    $('.poll-option').on('click', function() {
        $('.btn-vote').attr('disabled', !($('.poll-option').is(':checked')));
    });
    $('.parent-vote').on('click', function() {
        $('.btn-vote').attr('disabled', !($('.poll-option').is(':checked')));
    });

    $('.loader').hide();

    $('.btn-vote').on('click', function() {
        var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
        divChangeAmount = $(this).parent();
        var url = divChangeAmount.data('url');
        var isRequiredEmail = divChangeAmount.data('isRequiredEmail');
        var isRequiredName = divChangeAmount.data('isRequiredName');
        var isRequiredNameAndEmail = divChangeAmount.data('isRequiredNameAndEmail');
        var nameVote = $('.nameVote').val();
        var emailVote = $('.emailVote').val();

        if (isRequiredEmail== 1) {
            if (emailVote.trim() != '') {
                if (testEmail.test(emailVote)) {
                    this.disabled = true;
                    $('.message-validation').removeClass('alert alert-warning');
                    $('.message-validation').html('');
                    $('#form-vote').submit();
                    $('.loader').show();
                } else {
                    divChangeAmount = $(this).parent();
                    $('.message-validation').addClass('alert alert-warning');
                    var message = "<span class='glyphicon glyphicon-warning-sign'></span>" + ' ' +  divChangeAmount.data('messageValidateEmail');
                    $('.message-validation').html(message);
                }
            } else {
                divChangeAmount = $(this).parent();
                $('.message-validation').addClass('alert alert-warning');
                var message = "<span class='glyphicon glyphicon-warning-sign'></span>" + ' ' + divChangeAmount.data('messageRequiredEmail');
                $('.message-validation').html(message);
            }
        } else if (isRequiredName == 1) {
            if (nameVote.trim() != '') {
                this.disabled = true;
                $('.message-validation').removeClass('alert alert-warning');
                $('.message-validation').html('');
                $('#form-vote').submit();
                $('.loader').show();
            } else {
                divChangeAmount = $(this).parent();
                $('.message-validation').addClass('alert alert-warning');
                var message = "<span class='glyphicon glyphicon-warning-sign'></span>" + ' ' + divChangeAmount.data('messageRequiredName');
                $('.message-validation').html(message);
            }
        } else if (isRequiredNameAndEmail == 1) {
            if (nameVote.trim() != '') {
                if (emailVote.trim() == '') {
                    divChangeAmount = $(this).parent();
                    $('.message-validation').addClass('alert alert-warning');
                    var message = "<span class='glyphicon glyphicon-warning-sign'></span>" + ' ' + divChangeAmount.data('messageRequiredEmail');
                    $('.message-validation').html(message);
                } else {
                    if (testEmail.test(emailVote)) {
                        this.disabled = true;
                        $('.message-validation').removeClass('alert alert-warning');
                        $('.message-validation').html('');
                        $('#form-vote').submit();
                        $('.loader').show();
                    } else {
                        divChangeAmount = $(this).parent();
                        $('.message-validation').addClass('alert alert-warning');
                        var message = "<span class='glyphicon glyphicon-warning-sign'></span>" + ' ' +  divChangeAmount.data('messageValidateEmail');
                        $('.message-validation').html(message);
                    }
                }
            } else {
                if (emailVote.trim() == '') {
                    divChangeAmount = $(this).parent();
                    $('.message-validation').addClass('alert alert-warning');
                    var message = "<span class='glyphicon glyphicon-warning-sign'></span>" + ' ' + divChangeAmount.data('messageRequiredNameAndEmail');
                    $('.message-validation').html(message);
                } else {
                    divChangeAmount = $(this).parent();
                    $('.message-validation').addClass('alert alert-warning');
                    var message = "<span class='glyphicon glyphicon-warning-sign'></span>" + ' ' + divChangeAmount.data('messageRequiredName');
                    $('.message-validation').html(message);
                }
            }
        } else {
            var isPassValidate = false;
            if (emailVote.trim() != '') {
                if (testEmail.test(emailVote)) {
                    isPassValidate = true;
                } else {
                    divChangeAmount = $(this).parent();
                    $('.message-validation').addClass('alert alert-warning');
                    var message = "<span class='glyphicon glyphicon-warning-sign'></span>" + ' ' +  divChangeAmount.data('messageValidateEmail');
                    $('.message-validation').html(message);
                }
            } else {
                isPassValidate = true;
            }

            if (isPassValidate) {
                this.disabled = true;
                $('.message-validation').removeClass('alert alert-warning');
                $('.message-validation').html('');
                $('#form-vote').submit();
                $('.loader').show();
            }
        }
    });
});
