$(document).ready(function(){

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.btn-vote').prop('disabled', true);

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

function addNewOption() {
    this.init = function() {
        this.cacheDom();
        this.bindEvent();
    }

    this.cacheDom = function () {
        this.$preview = $('.render-img');
        this.$btnFileImg = $('.btn-file-img');
        this.$inputFileImg = $('#input-file-image');
        this.$deleteImg = $('.deleteImg');
        this.$btnVote = $('.btn-vote');
        this.$newOption = $('.new-option');
        this.$inputTextOption = $('.text-new-option');
        this.$boxVote = $('#voting_wizard');
        this.$allOption = $('.poll-option');
        this.$liParent = $('.parent-vote');
        this.$horizontalWrapper = $('.horizontal-overflow');
        this.$showError = $('.error_option');
        this.$contentOption = $('.option-name').find('span');
        this.isRadio = this.$newOption.is(':radio');
        this.messageValidate = this.$showError.data('message');
    }

    this.bindEvent = function () {
        this.$showError.hide();
        this.$btnFileImg.on('click', this.showBox.bind(this));
        this.$inputFileImg.on('change', this.readURL.bind(this));
        this.$inputTextOption.on('input', this.addText.bind(this));
        this.$boxVote.on('click', '.new-option', this.chooseOption.bind(this));
        this.$boxVote.on('click', '.deleteImg', this.deletePhoto.bind(this));
        this.$liParent.on('click', this.chooseOptionWithLi.bind(this));
    }

    this.readURL = function () {
        var input = this.$inputFileImg[0];
        var isImage = this._checkExtensionImage(input);
        var lengthInput = this.$inputTextOption.val();

        if (!this._validateDuplicate(lengthInput)) {
            if (isImage) {
                this.$showError.hide();

                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = this._renderImage.bind(this);

                    reader.readAsDataURL(input.files[0]);
                }
            } else {
                this.$inputFileImg.val('');
                this._displayError(this.messageValidate.image);
            }
        }
    }

    this.showBox = function () {
        this.$inputFileImg.click();
    }

    this.deletePhoto = function () {
        this.$preview.attr('src', '').hide();
        this.$deleteImg.hide();
        this.$inputFileImg.val('');
        if (this.$inputTextOption.val() == '') {
            this._notViewSubmit();
        }
    }

    this.addText = function () {
        var lengthInput = this.$inputTextOption.val();

        if (!this._validateDuplicate(lengthInput)) {
            this.$showError.hide();
            if (this.isRadio) {
                this.$allOption.prop('checked', false);
            }

            if (lengthInput.trim() == '' && this.$preview.attr('src') == '') {
                this._notViewSubmit();

                return;
            }
            this._viewSubmit();
        } else {
            this._notViewSubmit();
            this._displayError(this.messageValidate.option_duplicate);
        }
    }

    this.chooseOption = function (e) {
        var lengthInput = this.$inputTextOption.val();

        if (lengthInput == '') {
            if (this.isRadio) {
                this.$btnVote.prop('disabled', true);
                this.$newOption.prop('checked', false);
            }

            this.deletePhoto();
        }

        if (this.isRadio) {
            this.$allOption.prop('checked', false);
        } else {
            //is checkbox
            if (!this.$newOption.prop('checked') && !this.$allOption.is(':checked')) {
                this.$btnVote.prop('disabled', true);

                return;
            }

            this.$btnVote.prop('disabled', false);
        }
    }

    this.chooseOptionWithLi = function () {
        if (this.$newOption.is(':checked') && this.isRadio) {
            this.$newOption.prop('checked', false);
            this.$inputTextOption.val('')
            this.deletePhoto();
        }

        this.$btnVote.prop('disabled', !(this.$allOption.is(':checked')));
        if (this.$newOption.is(':checked')) {
            this.$btnVote.prop('disabled', false);
        }
    }

    this._viewSubmit = function () {
        this.$newOption.prop('checked', true);
        this.$btnVote.prop('disabled', false);
    }

    this._notViewSubmit = function () {
        if (!this.$allOption.is(':checked')) {
            this.$btnVote.prop('disabled', true);
        }

        this.$newOption.prop('checked', false);
    }

    this._renderImage = function (e) {
        if (this.isRadio) {
            this.$allOption.prop('checked', false);
        }

        this.$preview.attr('src', e.target.result).show();
        this.$deleteImg.show();
        this._viewSubmit();
        this._toScrollLast();
    }

    this._checkExtensionImage = function (input) {
        var fileUploadPath = input.value;
        var extension = fileUploadPath.substring(fileUploadPath.lastIndexOf('.') + 1).toLowerCase();
        var ruleExtension = ['gif', 'png', 'bmp', 'jpeg', 'jpg'];

        if (ruleExtension.indexOf(extension) > -1) {
            return true;
        }

        return false;
    }

    this._validateDuplicate = function (text) {
        var isDuplicate = false;
        this.$contentOption.each(function () {
            if (text == $(this).text()) {
                return isDuplicate = true;
            }
        });

        return isDuplicate;
    }

    this._toScrollLast = function () {
        var scrollHeight = $('.horizontal-overflow').prop("scrollHeight");
        if (scrollHeight > 400) {
            this.$horizontalWrapper.animate({
                    scrollTop: scrollHeight
            }, 1000);
        }
    }

    this._displayError = function (message) {
        this.$showError.find('.help-block').text(message);
        this.$showError.show();
        this._toScrollLast();
    }
}

var addOption= new addNewOption();
addOption.init();
