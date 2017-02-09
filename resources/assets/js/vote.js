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

/**
 * Add New Option
 */
var addNewOption = function () {
    this.init = function() {
        this.cacheDom();
        this.bindEvent();
    }

    this.cacheDom = function () {
        // DOM to process image of option
        this.$preview = $('.render-img');
        this.$btnFileImg = $('.btn-file-img');
        this.$deleteImg = $('.deleteImg');
        this.$parentThumb = $('.box-media-image');

        // DOM to process option text
        this.$newOption = $('.new-option');
        this.$liParent = $('.parent-vote');
        this.$inputTextOption = $('.text-new-option');
        this.$allOption = $('.poll-option');
        this.$contentOption = $('.option-name').find('span');

        // DOM button to vote
        this.$btnVote = $('.btn-vote');
        this.$boxVote = $('#voting_wizard');

        // DOM warpper vote
        this.$voteImageWrapper = $('.vote-preview-wrapper');

        // DOM to scrollTop div
        this.$horizontalWrapper = $('.horizontal-overflow');

        // Check option that choose radio or checkbox
        this.isRadio = this.$newOption.is(':radio');

        // DOM to show message validation
        this.$showError = $('#error_option');
        this.messageValidate = this.$showError.data('message');

        // DOM of datepicker
        this.$datePicker = $('.date-time-picker');
        this.$pickDate = $('.pick-date');
    }

    this.bindEvent = function () {
        this.$showError.hide();
        this.$preview.on('load', this.loadPreImage.bind(this));
        this.$inputTextOption.on('input', this.addText.bind(this));
        this.$boxVote.on('click', '.new-option', this.chooseOption.bind(this));
        this.$liParent.on('click', this.chooseOptionWithLi.bind(this));
        this.$pickDate.on('click', this.getTextDatePicker.bind(this));
        this.$datePicker.on('dp.hide', this.addTextDate.bind(this));
    }

    this.loadPreImage = function () {
        this.$deleteImg.show();
        this.$voteImageWrapper.css('border', '1px solid #ccc');
        this.$voteImageWrapper.css('border-top', 'none');
    }

    this.getTextDatePicker = function () {
        this.$pickDate.data('isHasDate', true);
        var textInput = typeof this.addText() === 'undefined' ? '' : this.addText().trim();
        if (this._isValidDate(textInput)) {
            textInput = '';
        }

        this.$datePicker.data('preText', textInput);
        this.$datePicker.datetimepicker().data('DateTimePicker').show();
    }

    this.addTextDate = function (e) {
        var dateChoosed = moment(e.date).format('MM/DD/YYYY h:mm A');
        var preText = this.$datePicker.data('preText');
        var fullText = (preText + ' ' + dateChoosed).trim();

        this.$inputTextOption.val(fullText);

        // Destroy event datepicker that convert text input
        this.$datePicker.data('DateTimePicker').hide().destroy();
        this.$pickDate.data('isHasDate', false);
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

    this.deletePhoto = function () {
        this.$preview.attr('src', '').hide();
        this.$parentThumb.hide();
        this.$deleteImg.hide();
        this.$voteImageWrapper.css('border', 'none');
        if (this.$inputTextOption.val() == '') {
            this._notViewSubmit();
        }
    }

    this.addText = function () {
        var lengthInput = this.$inputTextOption.val().trim();

        if (!this._validateDuplicate(lengthInput)) {
            this.$showError.hide();
            if (this.isRadio) {
                this.$allOption.prop('checked', false);
            }

            if (lengthInput == '' && !this.$pickDate.data('isHasDate')) {
                this._notViewSubmit();

                return;
            }

            this._viewSubmit();

            return lengthInput;
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
        if (this.isRadio) {
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
        this.$voteImageWrapper.css('border', '1px solid #ccc');
        this.$voteImageWrapper.css('border-top', 'none');
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

    this._isValidDate = function (dateString) {
        var regEx = /^\d{2}\/\d{2}\/\d{4}\s+[\d]+:\d{2}\s+(AM|PM)$/;
        return dateString.match(regEx) != null;
    }
}

/**
 * Init plugin add image for option
 */
var jqAddNewImageOption = new jqAddImageOption({
    wrapperPoll: '.horizontal-overflow',
    btnChooseImage: '.upload-photo',
    parentOption: '.parent-vote-new-option',
    messages: 'div[data-message]',
});

var addOption = new addNewOption();
addOption.init();
