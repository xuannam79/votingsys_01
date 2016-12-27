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
        var message = '';

        if (isRequiredEmail == 1) {
            if (emailVote.trim() != '') {
                if (testEmail.test(emailVote)) {
                    if (nameVote.trim().length >= 100) {
                        divChangeAmount = $(this).parent();
                        message = "<span class='glyphicon glyphicon-warning-sign'></span>"
                            + ' ' + divChangeAmount.data('voteLimitName');
                        $('.message-validation').addClass('alert alert-warning alert-poll-set-ip').html(message);
                        $('.nameVote').addClass('error');

                        if ($('.emailVote').hasClass('error')) {
                            $('.emailVote').removeClass('error');
                        }
                    } else {
                        this.disabled = true;
                        removeClassError();
                        $('.message-validation').removeClass('alert alert-warning alert-poll-set-ip').html('');
                        $('#form-vote').submit();
                        $('.loader').show();
                    }
                } else {
                    divChangeAmount = $(this).parent();
                    message = "<span class='glyphicon glyphicon-warning-sign'></span>"
                        + ' ' +  divChangeAmount.data('messageValidateEmail');
                    $('.message-validation').addClass('alert alert-warning alert-poll-set-ip').html(message);
                    $('.emailVote').addClass('error');

                    if ($('.nameVote').hasClass('error')) {
                        $('.nameVote').removeClass('error');
                    }
                }
            } else {
                divChangeAmount = $(this).parent();
                message = "<span class='glyphicon glyphicon-warning-sign'></span>"
                    + ' ' + divChangeAmount.data('messageRequiredEmail');
                $('.message-validation').addClass('alert alert-warning alert-poll-set-ip').html(message);
                $('.emailVote').addClass('error');

                if ($('.nameVote').hasClass('error')) {
                    $('.nameVote').removeClass('error');
                }
            }
        } else if (isRequiredName == 1) {
            if (nameVote.trim() != '') {
                if (nameVote.trim().length < 100) {
                    if (emailVote.trim() != '') {
                        if (testEmail.test(emailVote)) {
                            this.disabled = true;
                            removeClassError();
                            $('.message-validation').removeClass('alert alert-warning alert-poll-set-ip').html('');
                            $('#form-vote').submit();
                            $('.loader').show();
                        } else {
                            divChangeAmount = $(this).parent();
                            message = "<span class='glyphicon glyphicon-warning-sign'></span>"
                                + ' ' +  divChangeAmount.data('messageValidateEmail');
                            $('.message-validation').addClass('alert alert-warning alert-poll-set-ip').html(message);
                            $('.emailVote').addClass('error');

                            if ($('.nameVote').hasClass('error')) {
                                $('.nameVote').removeClass('error');
                            }
                        }
                    } else {
                        this.disabled = true;
                        removeClassError();
                        $('.message-validation').removeClass('alert alert-warning alert-poll-set-ip').html('');
                        $('#form-vote').submit();
                        $('.loader').show();
                    }
                } else {
                    divChangeAmount = $(this).parent();
                    message = "<span class='glyphicon glyphicon-warning-sign'></span>"
                        + ' ' + divChangeAmount.data('voteLimitName');
                    $('.message-validation').addClass('alert alert-warning alert-poll-set-ip').html(message);
                    $('.nameVote').addClass('error');

                    if ($('.emailVote').hasClass('error')) {
                        $('.emailVote').removeClass('error');
                    }
                }
            } else {
                divChangeAmount = $(this).parent();
                message = "<span class='glyphicon glyphicon-warning-sign'></span>"
                    + ' ' + divChangeAmount.data('messageRequiredName');
                $('.message-validation').addClass('alert alert-warning alert-poll-set-ip').html(message);
                $('.nameVote').addClass('error');

                if ($('.emailVote').hasClass('error')) {
                    $('.emailVote').removeClass('error');
                }
            }
        } else if (isRequiredNameAndEmail == 1) {
            if (nameVote.trim() != '' && nameVote.trim().length < 100) {
                if (emailVote.trim() == '') {
                    divChangeAmount = $(this).parent();
                    message = "<span class='glyphicon glyphicon-warning-sign'></span>"
                        + ' ' + divChangeAmount.data('messageRequiredEmail');
                    $('.message-validation').addClass('alert alert-warning alert-poll-set-ip').html(message);
                    $('.emailVote').addClass('error');

                    if ($('.nameVote').hasClass('error')) {
                        $('.nameVote').removeClass('error');
                    }
                } else {
                    if (testEmail.test(emailVote)) {
                        this.disabled = true;
                        removeClassError();
                        $('.message-validation').removeClass('alert alert-warning alert-poll-set-ip').html('');
                        $('#form-vote').submit();
                        $('.loader').show();
                    } else {
                        divChangeAmount = $(this).parent();
                        message = "<span class='glyphicon glyphicon-warning-sign'></span>"
                            + ' ' +  divChangeAmount.data('messageValidateEmail');
                        $('.message-validation').addClass('alert alert-warning alert-poll-set-ip').html(message);
                        $('.emailVote').addClass('error');

                        if ($('.nameVote').hasClass('error')) {
                            $('.nameVote').removeClass('error');
                        }
                    }
                }
            } else {
                if (nameVote.trim() == '') {
                    if (emailVote.trim() == '') {
                        divChangeAmount = $(this).parent();
                        message = "<span class='glyphicon glyphicon-warning-sign'></span>"
                            + ' ' + divChangeAmount.data('messageRequiredNameAndEmail');
                        $('.message-validation').addClass('alert alert-warning alert-poll-set-ip').html(message);
                        $('.nameVote').addClass('error');
                        $('.emailVote').addClass('error');
                    } else {
                        divChangeAmount = $(this).parent();
                        message = "<span class='glyphicon glyphicon-warning-sign'></span>"
                            + ' ' + divChangeAmount.data('messageRequiredName');
                        $('.message-validation').addClass('alert alert-warning alert-poll-set-ip').html(message);
                        $('.nameVote').addClass('error');

                        if ($('.emailVote').hasClass('error')) {
                            $('.emailVote').removeClass('error');
                        }
                    }
                } else if (nameVote.trim().length >= 100) {
                    divChangeAmount = $(this).parent();
                    message = "<span class='glyphicon glyphicon-warning-sign'></span>"
                        + ' ' + divChangeAmount.data('voteLimitName');
                    $('.message-validation').addClass('alert alert-warning alert-poll-set-ip').html(message);
                    $('.nameVote').addClass('error');

                    if ($('.emailVote').hasClass('error')) {
                        $('.emailVote').removeClass('error');
                    }
                }
            }
        } else {
            var isPassValidate = false;

            if (emailVote.trim() != '') {
                if (testEmail.test(emailVote)) {
                    isPassValidate = true;
                } else {
                    divChangeAmount = $(this).parent();
                    message = "<span class='glyphicon glyphicon-warning-sign'></span>"
                        + ' ' +  divChangeAmount.data('messageValidateEmail');
                    $('.message-validation').addClass('alert alert-warning alert-poll-set-ip').html(message);
                    $('.emailVote').addClass('error');

                    if ($('.nameVote').hasClass('error')) {
                        $('.nameVote').removeClass('error');
                    }
                }
            } else {
                isPassValidate = true;
            }

            if (nameVote.trim().length >= 100) {
                divChangeAmount = $(this).parent();
                message = "<span class='glyphicon glyphicon-warning-sign'></span>"
                    + ' ' + divChangeAmount.data('voteLimitName');
                $('.message-validation').addClass('alert alert-warning alert-poll-set-ip').html(message);
                $('.nameVote').addClass('error');

                if ($('.emailVote').hasClass('error')) {
                    $('.emailVote').removeClass('error');
                }
                isPassValidate = false;
            }

            if (isPassValidate) {
                this.disabled = true;
                removeClassError();
                $('.message-validation').removeClass('alert alert-warning alert-poll-set-ip').html('');
                $('#form-vote').submit();
                $('.loader').show();
            }
        }
    });

    function removeClassError()
    {
        if ($('.emailVote').hasClass('error')) {
            $('.emailVote').removeClass('error');
        }

        if ($('.nameVote').hasClass('error')) {
            $('.nameVote').removeClass('error');
        }
    }
});
