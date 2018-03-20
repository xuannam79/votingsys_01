$(document).ready(function(){

    // Plugin Search Row
    $.fn.searchRow = function(options) {
        // These are the defaults.
        var defaults = {
            jo: $('.model-show-details').find('tbody tr').not('.header, :last-child'),
            warpper: '.model-show-details'
        };

        var options = $.extend(defaults, options);

        var inputText = this[0];

        $(options.warpper).on('keyup', inputText, function (e) {
            var $this = e.target;

            if (inputText === $this) {
                //split the current value of searchInput
                var data = $this.value.split(" ");

                if ($this.value == "") {
                    options.jo.show();
                    return;
                }

                //hide all the rows
                options.jo.hide();

                //Recusively filter the jquery object to get results.
                options.jo.filter(function (i, v) {
                    var $t = $(this);
                    for (var d = 0; d < data.length; ++d) {
                        if ($t.is(":contains('" + data[d] + "')")) {
                            return true;
                        }
                    }
                    return false;
                })
                //show the rows that match.
                .show();
            }
        });
    }

    $('.search-row-detail').searchRow();

    // Set position thead
    $('#timeline').scroll(function () {
        var $boxTimeline = $('#timeline');
        var scrollTop = $boxTimeline.scrollTop();

        $('.fixed-header').css('top', scrollTop);

        if ($boxTimeline.prop('scrollHeight') > $boxTimeline.height()) {
            $('.td-fixed-check').css('bottom', -scrollTop);
        }
    });

    $('.btn-vote-style').on('shown.bs.tab', function (e) {
        var contentTab = $(e.target).prop('href');

        if (contentTab.indexOf('timeline') !== -1) {
            createWaypointThead();

            if ($('#timeline').prop('scrollHeight') > $('#timeline').height()) {
                createFixedTfoot();
            }

            return;
        }

        destroyDom();
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.loader').hide();

    $.extend({
        xResponse: function(url, data, messageError) {
            // local var
            var theResponse = null;
            // jQuery ajax
            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                dataType: "json",
                async: false,

                beforeSend: function () {
                    $('.loader').show();
                },

                success: function(respText) {
                    theResponse = respText.status;
                },

                complete: function () {
                    $('.loader').hide();
                },

                error: function () {
                    showMessage(messageError);
                    $('.loader').hide();
                }
            });
            // Return the response text
            return theResponse;
        }
    });

    $('#timeline').on('click', '.js-edit-vote', function (e) {
        var $this = $(this);
        var data = $this.closest('.inline-edit').data('edit-voted');
        var $tr = $this.closest('tr');
        var nameVote = $tr.find('.name-voter').text();
        var emailVote = $tr.find('.email-voter').text();

        $tr.hide();

        $('.js-voter-box').not($tr).show();

        $('.tf-check-option').addClass('hide');

        $('.row-temp').remove();

        // set data global for button save
        data.email = emailVote;
        $('#data-voter').data('dataRequest', data);

        var $optionChoose = $('.tf-check-option tr').clone().addClass('row-temp');

        // Remove class button action edit and cancel
        $optionChoose.get(1).classList.remove('hide');

        // Show input text to edit information of voter or hide input text if authentication was
        if (data.vote_id || data.user_id) {
            $optionChoose.find('.td-name-temp').html(nameVote);
            $optionChoose.find('.td-email-temp').html(emailVote);
        } else {
            $optionChoose.find('input[name=name]').val(nameVote).removeClass('hide');
            $optionChoose.find('input[name=email]').val(emailVote).removeClass('hide');
        }

        // Show option to edit
        $optionChoose.find('*').removeAttr('onclick id');
        var $optionEdit = $optionChoose.find('.opsep input')
            .prop('checked', false)
            .attr({'name': 'optionedit[]', 'class': 'data-edit-option'});

        $tr.find('.opsep').each(function (index, value) {
            if ($(this).hasClass('pop')) {
                $optionEdit.eq(index).prop('checked', true);
            }
        });

        $('.td-fixed-check').remove();

        $(this).closest('tr').after($optionChoose);
    });

    $('#timeline').on('click', '.save-edit-vote', function () {
        var $this = $(this);
        var object = $('.js-data-validate').data();
        var name = $('.input-name').val().trim();
        var email = $('.input-email').val().trim();
        var dataRequest = $('#data-voter').data('dataRequest');

        // Validate when dom show errror if exists
        if ($('.tr-input.row-temp span').removeClass('hide').length > 0) {
            var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;

            if (name && name.length > 100) {
                $('.js-show-error-name').text(object.voteLimitName);

                return false;
            }

            if (email && !testEmail.test(email)) {
                $('.js-show-error-email').text(object.messageValidateEmail);

                return false;
            }

            if (object.isRequiredNameAndEmail && name == '' && email == '') {
                $('.js-show-error-name').text(object.messageRequiredNameAndEmail);

                return false;
            }

            if (object.isRequiredNameAndEmail || object.isRequiredEmail) {

                if (email == '') {
                    $('.js-show-error-email').text(object.messageRequiredEmail);

                    return false;
                }

                if (object.isAccecptTypeMail && !checkAccecptTypeEmail(object.typeEmail.trim(), email)) {
                    $('.js-show-error-email').text(object.messageRequiredTypeEmail);

                    return false;
                }

                // Check email exist if propety require email
                if (object.isNotSameEmail
                    && $.xResponse(
                        object.urlCheckExistEmail,
                        {pollId: object.idPoll, emailVote: email, emailIgnore: dataRequest.email}
                    )
                ) {
                    $('.js-show-error-email').text(object.messageEmailExists);

                    return false;
                }

                $('.js-show-error-email').empty();
            }

            if (object.isRequiredNameAndEmail || object.isRequiredName) {
                if (name == '') {
                    $('.js-show-error-name').text(object.messageRequiredName);

                    return false;
                }

                if (name.length > 100) {
                    $('.js-show-error-name').text(object.voteLimitName);

                    return false;
                }

                $('.js-show-error-name').empty();
            }
        }

        // Set data to request to edit vote of option by voter
        var dataRequest = $('#data-voter').data('dataRequest');
        var urlRequest = $this.data('url-edit');

        if (typeof name != 'undefined' && typeof email != 'undefined') {
            dataRequest.name = name;
            dataRequest.email = email;
        }

        dataRequest.poll_id = $this.parent().data('poll-id');
        dataRequest.option = $('.data-edit-option:checked').map(function () {
            return $(this).val();
        })
        .get();

        // Request input option when voted
        if (dataRequest.option.length === 0) {
            $this.siblings('.mess-option').removeClass('hide');

            return false;
        }

        $this.siblings('.mess-option').addClass('hide');

        if ( $(this).data('requestRunning') ) {
            return;
        }

        $(this).data('requestRunning', true);

        $.ajax({
            url: urlRequest,
            type: 'POST',
            data: dataRequest,
            dataType: 'json',
            async: false,

            beforeSend: function () {
                $('.loader').show();
            },

            success: function(respText) {
                if (!respText.status) {
                    $this.siblings('.mess-error').removeClass('hide');
                } else {
                    $this.siblings('.mess-error').addClass('hide');
                }
            },

            complete: function () {
                $('.loader').hide();
                $(this).data('requestRunning', false);
            },
        });
    });

    $('#timeline').on('click', '.cancel-edit-vote', function () {
        $('.row-temp').remove();
        $('.td-fixed-check').remove();

        $('.js-voter-box:hidden').show();
        $('.tf-check-option').removeClass('hide');

        if ($('#timeline').prop('scrollHeight') > $('#timeline').height()) {
            createFixedTfoot();
        }
    });

    $('#timeline').on('click', '.js-delete-vote', function () {
        // Set data to request to delete vote of option by voter
        var dataRequest = $(this).closest('.inline-edit').data('edit-voted');
        var dataVoter = $(this).data();

        dataRequest._method = 'DELETE';
        dataRequest.option = dataVoter.option;
        dataRequest.poll_id = dataVoter.pollId;

        swal({
            title: dataVoter.message,
            type: "info",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            closeOnConfirm: true
        }, function () {
            $('.loader').show();
            $.ajax({
                url: dataVoter.urlDelete,
                type: 'DELETE',
                data: dataRequest,
                dataType: 'json',
                async: false,
                success: function(respText) {
                    if (!respText.status) {
                        alert(respText.message);
                    }
                },

                complete: function () {
                    $('.loader').hide();
                },
            });
        });
    });

    $(window).resize(function () {
        if ($('#timeline').is(':visible')) {
            destroyDom();
            createWaypointThead();

            if ($('#timeline').prop('scrollHeight') > $('#timeline').height()) {
                createFixedTfoot();
            }
        }
    });

    $('.btn-vote').prop('disabled', true);

    $('.btn-vote').on('click', function() {
        var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
        divChangeAmount = $(this).parent();
        var url = divChangeAmount.data('url');
        var idPoll = divChangeAmount.data('idPoll');
        var urlCheckExistEmail = divChangeAmount.data('urlCheckExistEmail');
        var isRequiredEmail = divChangeAmount.data('isRequiredEmail');
        var isRequiredName = divChangeAmount.data('isRequiredName');
        var isRequiredNameAndEmail = divChangeAmount.data('isRequiredNameAndEmail');
        var isNotTheSameEmail = divChangeAmount.data('isNotSameEmail');
        var nameVote = $('.nameVote').val();
        var emailVote = $('.emailVote').val();
        var message = '';
        var data = divChangeAmount.data();

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
                        // Check pass type email of setting
                        if (data.isAccecptTypeMail) {
                            if (!checkAccecptTypeEmail(data.typeEmail.trim(), emailVote.trim())) {
                                showMessage(data.messageRequiredTypeEmail);

                                return;
                            }
                        }

                        // Check email exist if propety require email
                        if (isNotTheSameEmail) {
                            var status = $.xResponse(urlCheckExistEmail, {pollId: idPoll, emailVote: emailVote});

                            if (status) {
                                showMessage(data.messageEmailExists, data.messageErrorOccurs);

                                return;
                            }
                        }

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
                        // Check pass type email of setting
                        if (data.isAccecptTypeMail) {
                            if (!checkAccecptTypeEmail(data.typeEmail, emailVote)) {
                                showMessage(data.messageRequiredTypeEmail);

                                return;
                            }
                        }

                        // Check email exist if propety require email
                        if (isNotTheSameEmail) {
                            var status = $.xResponse(urlCheckExistEmail, {pollId: idPoll, emailVote: emailVote});

                            if (status) {
                                showMessage(data.messageEmailExists, data.messageErrorOccurs);

                                return;
                            }
                        }

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
                // Check email exist if propety require email
                if (isNotTheSameEmail) {
                    var status = $.xResponse(urlCheckExistEmail, {pollId: idPoll, emailVote: emailVote});

                    if (status) {
                        showMessage(data.messageEmailExists, data.messageErrorOccurs);

                        return;
                    }
                }
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

    function showMessage(message)
    {
        var message = "<span class='glyphicon glyphicon-warning-sign'></span>"
            + ' ' +  message;
        $('.message-validation').addClass('alert alert-warning alert-poll-set-ip').html(message);
        $('.emailVote').addClass('error');

        if ($('.nameVote').hasClass('error')) {
            $('.nameVote').removeClass('error');
        }
    }

    function checkAccecptTypeEmail(typeEmail, email)
    {
        var type = email.substr(email.indexOf('@') + 1).trim();
        var typeEmail = typeEmail.split(',');
        var sameExtensionEmail = false;

        if (type.length && typeEmail.length) {
            typeEmail.some(function (item) {
                return sameExtensionEmail = (item.trim() === type);
            });
        }

        return sameExtensionEmail;
    }

    $('.btn-edit-submit').on('click', function () {
        var isEmpty = false;
        $('input[id^=optionText]').each(function () {
            if ($(this).val() == '') {
                return isEmpty = true;
            }
        });

        if (isEmpty) {
            var messageEmpty = $('.vote-style').data('option').message.option_required;
            $('.btn-edit-submit').prop('disabled', true);
            $('.error_option').addClass('has-error').html('<span id="title-error" class="help-block">' + messageEmpty + '</span>');

            return;
        }

        $('.btn-edit-submit').prop('disabled', false);
        $('#edit-voted-content').submit();
    });

    $('#message-flash').fadeIn().delay(3000).fadeOut();

    $('#frame-upload-image-edit').on('hidden.bs.modal', function () {
        $('body').attr('class', 'modal-open');
    });

    $('.edit-each-option').on('click', function () {
        var dataClient = $(this).closest('.vote-style').data('option');
        var dataViewOption = dataClient.view.option;
        var data = {optionText: {}};

        $('.content-option-choose').each(function (index) {
            var idOption = $('input[name="option[]"]').eq(index).val();
            data.optionText[idOption] = $(this).text();
        });

        $('.poll-option').html('')
        createOption(dataViewOption, '', data);

        var frnCheckTheSame = ['checkOptionSame(this, \'', dataClient.message.option_duplicate, '\')'].join('');
        $('#frame-edit-poll').find('.btn-remove-option').remove();
        $('#frame-edit-poll').find('input[name^=optionText]')
            .removeAttr('onfocus onclick')
            .attr({
                onblur: frnCheckTheSame,
                onkeyup: frnCheckTheSame
            });

        pickDateOption();

        $('.li-parent-vote').find('.image-option-choose').each(function (index) {
            var srcImage = $(this).prop('src');
            if (srcImage.indexOf('default-thumb.gif') == -1 ) {
                $('#frame-edit-poll').find('.render-img').eq(index).attr('src', srcImage);
                $('#frame-edit-poll').find('.box-media-image').eq(index).css('display', 'inline-block');
            }
        });

        $('#frame-edit-poll').modal('show');
    });

    $('#horizontal').on('click', '.modal-voter', function () {
        var url = $(this).data('url-modal-voter');

        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                if (data.status) {
                    $('#result-voters').html(data.voters);
                    $('.loader').hide();
                }
            },

            beforeSend: function () {
                $('.loader').show();
            },

            complete: function () {
                $('.loader').hide();

                $('#voters-modal').modal('show');

                $('#voters-modal').off('keyup');
                $('.search-voters-row').searchRow({
                    jo: $('.voters-row'),
                    warpper: '#voters-modal'
                });
            },
        });
    });

});

var jqAddNewImageOption11 = new jqAddImageOption({
    wrapperPoll: '.poll-option',
    parentOption: '.form-group',
    thumbImageOption: '.render-img',
    btnChooseImage: '.upload-photo',
    frImage: "#frame-upload-image-edit",
    frUploadFile: '.photo-tb-upload-edit',
    frPreImage: '.img-pre-option-edit',
    frAddImgLink: '.add-image-by-link-edit',
    frInputText: '.photo-tb-url-txt-edit',
    frDelPhoto: '.photo-tb-del-edit',
    frConfirmYes: '.btn-yes-edit',
    frInputFileTemp: '.fileImgTempEdit',
    frContentError : '.error-win-img-edit',
    messages: 'div[data-option]',
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
        this.$allOption = $('input[id^=horizontal]');

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
        this.$boxVote.on('click', '.parent-vote' ,this.chooseOptionWithLi.bind(this));
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
        this.$datePicker.datetimepicker({
            showClose: true,
            icons: {close: 'glyphicon glyphicon-ok'},
            format: 'DD-MM-YYYY HH:mm',
        }).data('DateTimePicker').show();
    }

    this.addTextDate = function (e) {
        var dateChoosed = moment(e.date).format('DD-MM-YYYY HH:mm');
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
                $('input[id^=horizontal]')
                    .add('input[id^=vertical]')
                    .add('input[id^=timeline]')
                    .add('input[id^=timeline-temp]')
                    .prop('checked', false);
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
            $('input[id^=horizontal]').prop('checked', false);
        } else {
            //is checkbox
            if (!this.$newOption.prop('checked') && !$('input[id^=horizontal]').is(':checked')) {
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

        this.$btnVote.prop('disabled', !($('input[id^=horizontal]').is(':checked')));
        if (this.$newOption.is(':checked')) {
            this.$btnVote.prop('disabled', false);
        }
    }

    this._viewSubmit = function () {
        this.$newOption.prop('checked', true);
        this.$btnVote.prop('disabled', false);
    }

    this._notViewSubmit = function () {
        if (!$('input[id^=horizontal]').is(':checked')) {
            this.$btnVote.prop('disabled', true);
        }

        this.$newOption.prop('checked', false);
    }

    this._renderImage = function (e) {
        if (this.isRadio) {
            $('input[id^=horizontal]').prop('checked', false);
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
        var regEx = /^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}\s+([01]?[0-9]|2[0-3]):[0-5][0-9]$/;
        return dateString.match(regEx) != null;
    }
}

/**
 * Init plugin add image for option
 */
var jqAddNewImageOption = new jqAddImageOption({
    wrapperPoll: '.box-style-option',
    btnChooseImage: '.upload-photo',
    parentOption: '.parent-vote-new-option',
    messages: 'div[data-message]',
    frImage: "#frame-upload-image",
    frUploadFile: '.photo-tb-upload',
    frPreImage: '.img-pre-option',
    frAddImgLink: '.add-image-by-link',
    frInputText: '.photo-tb-url-txt',
    frDelPhoto: '.photo-tb-del',
    frConfirmYes: '.btn-yes',
    frInputFileTemp: '.fileImgTemp',
    frContentError : '.error-win-img',
    messages: 'div[data-option]',
    horizontalWrapper: '.horizontal-overflow',
});

var addOption = new addNewOption();
addOption.init();
