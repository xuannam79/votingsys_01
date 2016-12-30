/*-----------------------------------------
            GLOBAL VARIABLE
-------------------------------------------*/
var pollData = $('.hide').data("poll");
var dataAction = $('.hide').data("page");
var _validFileExtensions = [".jpg", ".jpeg", ".bmp", ".gif", ".png"];
var actionShowHides = ['show', 'hide'];
var typeElement = ['id', 'class'];
var rand = function() {
    return Math.random().toString(36).substr(2); // remove `0.`
};

/*-----------------------------------------
                 FUNCTION
 -------------------------------------------*/
function nextTab(elem) {
    $(elem).next().find('a[data-toggle="tab"]').click();
}
function prevTab(elem) {
    $(elem).prev().find('a[data-toggle="tab"]').click();
}

/**
 *
 * tooltip bootstrap
 *
 */
$('a[data-toggle="tooltip"]').tooltip({
    animated: 'fade',
    placement: 'bottom',
    html: true
});

/**
 *
 * auto scroll to top
 *
 * @param id
 */
function autoScrollToElement(id) {
    $('html, body').animate({
        scrollTop: $("#" + id).offset().top + 100
    }, 2000);
}

/**
 * setting datetimepicker
 */
$(function () {
    $('#time_close_poll').datetimepicker({
        format: 'DD-MM-YYYY HH:mm'
    });
});

/**
 *
 * show message alert confirm
 *
 * @param message
 */
function confirmDelete(message) {
    return confirm(message);
}

/**
 * show or hide password of poll
 */
function showAndHidePassword() {
    if($('#password').attr("type") == "password"){
        $('#password').attr("type", "text");
        $('.show-password').html('<i class="fa fa-eye-slash" aria-hidden="true"></i>');
    } else {
        $('#password').attr("type", "password");
        $('.show-password').html('<i class="fa fa-eye" aria-hidden="true"></i>');
    }
}

/**
 *
 * show modal preview option image in view details
 *
 * @param image
 */
function showModelImage(image) {
    $('#imageOfOptionPreview').attr("src", image);
    $('#modalImageOption').modal('show');
}

/**
 *
 * show result in detail page
 *
 */
function showResultPoll() {
    $('.result-poll').toggle();

    if ($('.btn-show-result-poll').attr('id') == 'show') {
        $('.li-show-result-poll').removeClass('fa-eye').addClass('fa-eye-slash');
        $('.btn-show-result-poll').attr('id', 'hide');
    } else {
        $('.li-show-result-poll').removeClass('fa-eye-slash').addClass('fa-eye');
        $('.btn-show-result-poll').attr('id', 'show');
    }
}

/**
 *
 * hide label message
 *
 * @param element
 */
function hideLabelMessage(element) {
    setTimeout( function () {
        $(element).html('');
    }, 2000);
}

/**
 *
 * change tab vote save status of option
 *
 * @param id
 * @param type
 */
function voted(id, type) {
    if (type == 'horizontal') {
        $('#horizontal-' +id).prop('checked',! $('#horizontal-' + id).prop('checked'));
        $('#vertical-' +id).prop('checked', $('#horizontal-' + id).prop('checked'));
    } else {
        $('#vertical-' + id).prop('checked',! $('#vertical-' + id).prop('checked'));
        $('#horizontal-' +id).prop('checked', $('#vertical-' + id).prop('checked'));
    }
}

/**
 * Create option of poll
 *
 * @param viewOption
 * @param number
 * @param oldInput
 */
function createOption(viewOption, number, oldInput) {
    number = (typeof number === 'undefined') ? pollData.length.option : number;

    if (oldInput != null) {
        var oldOption = oldInput.optionText;
        jQuery.each( oldOption, function( id, val ) {
            var option = "";
            option = viewOption.replace(/idOption/g, id);
            $('.poll-option').append(option);
            $('#content-option-' + id).val(val);
        });
    } else {
        for (var i = 0; i < number; i++) {
            var id = rand();
            var option = "";
            option = viewOption.replace(/idOption/g, id);
            $('.poll-option').append(option);
        }
    }
}

/**
 * Show image input when click button have image icon
 *
 * @param idOption
 */
function showOptionImage(idOption) {
    addAutoOption(idOption);
    $('input[name = "optionImage[' + idOption + ']"]').click();
}

/**
 *
 * function auto add option
 *
 * @param idOption
 */
function addAutoOption(idOption) {
    $('#optionText-' + idOption).removeClass('error-input');
    if ($('#' + idOption).is(':last-child')) {
        var viewOption = pollData.view.option;
        var number = pollData.config.length.option_inc;
        createOption(viewOption, number);
    }
}


/**
 *
 * View picture when choose image
 *
 * @param input
 * @param idShow
 */
function readURL(input, idShow) {
    if (ValidateSingleInput(input.files[0].name)) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#' + idShow).show().attr('src', e.target.result);
                checkImageSame();
            };

            reader.readAsDataURL(input.files[0]);
        }
    } else {
        $('#' + idShow).hide();
    }
}

/**
 *
 * remove option of poll
 *
 * @param idOption
 * @param action
 */
function removeOpion(idOption, action) {
    if (typeof pollData !== "undefined"
        && typeof action !== "undefined"
        && action == "edit") {
        if (confirmDelete(pollData.message.confirm_delete_option)) {
            $("#" + idOption).remove();
        }
    } else {
        $("#" + idOption).remove();
        var optionLists = $('input[name^="optionText"]');

        if (optionLists.length == 0) {
            $('.error_option').addClass('has-error')
                .html('<span id="title-error" class="help-block">' + pollData.message.option_minimum + '</span>');
            var viewOption = pollData.view.option;
            createOption(viewOption);
        }
    }
}

/**
 *
 * show advance setting: custom link, set limit, set password
 *
 * @param key
 */
function settingAdvance(key) {
    $('#limit').removeClass('has-error');
    $('.error_limit').removeClass('has-error').html('');

    $('#link').removeClass('has-error');
    $('.error_link').removeClass('has-error').html('');

    $('#password').removeClass('has-error');
    $('.error_password').removeClass('has-error').html('');

    switch (key) {
        case pollData.config.setting.required:
            $("#setting-required").slideToggle();
            break;
        case pollData.config.setting.custom_link:
            $("#setting-link").slideToggle();
            break;
        case pollData.config.setting.set_limit:
            $("#setting-limit").slideToggle();
            break;
        case pollData.config.setting.set_password:
            $("#setting-password").slideToggle();
            break;
        default:
            break;
    }
}

/**
 *
 * validate format of option image
 *
 * @param oInput
 * @returns {boolean}
 * @constructor
 */
function ValidateSingleInput(oInput) {
    var sFileName = oInput;

    if (sFileName.length > 0) {
        var blnValid = false;

        for (var j = 0; j < _validFileExtensions.length; j++) {
            var sCurExtension = _validFileExtensions[j];
            if (sFileName.substr(
                    sFileName.length - sCurExtension.length, sCurExtension.length
                ).toLowerCase() == sCurExtension.toLowerCase()) {
                blnValid = true;
                break;
            }
        }

        if (!blnValid) {
            $('.error_option').addClass('has-error')
                .html('<span id="title-error" class="help-block">' + pollData.message.image + '</span>');

            return false;
        }
    }

    return true;
}

/**
 *
 * validate option when create, edit, duplicate poll
 *
 * @returns {boolean}
 */
function validateOption() {
    var optionLists = $('input[name^="optionText"]');
    var imageLists = $('input[name^="optionImage"]');
    var isOption = true;
    var isEmpty = false;
    $('#validateOption').html("");

    if (typeof dataAction !== "undefined") {
        if (dataAction == "duplicate") {
            if ($('.old-option').text().trim() !== "") {
                return true;
            }
        }
    }

    if (optionLists.length == 0 && imageLists.length == 0) {
        $('.error_option').addClass('has-error')
            .html('<span id="title-error" class="help-block">' + pollData.message.option_empty + '</span>');

        return false;
    }

    optionLists.each(function () {
        if ($(this).val() != "") {
            isEmpty = true;
        }
    });

    if (! isEmpty) {
        $('.error_option').addClass('has-error')
            .html('<span id="title-error" class="help-block">' + pollData.message.option_required + '</span>');

        return false;
    }

    optionLists.each(function (key) {
        var id = $(this).attr('id');

        if ($(this).val() == "") {
            imageLists.each(function (keyImage) {
                if (keyImage == key) {
                    if ($(this).val() != "") {
                        $('#' + id).addClass('error-input');
                        $('.error_option').addClass('has-error')
                            .html('<span id="title-error" class="help-block">'
                                + pollData.message.option_required + '</span>');
                        isOption = false;
                    }
                }
            });
        }
    });

    return isOption;
}

/**
 *
 * validate participant when create or duplicate poll
 *
 * @returns {boolean}
 */
function validateParticipant() {
    var members = $("#member").val();

    if (members == "") {
        $('.error_participant').removeClass('has-error');
        return true;
    }

    members = members.split(",");

    for (var index = 0; index < members.length; index++) {
        if (! validateEmail(members[index])) {
            $('.error_participant').addClass('has-error')
                .html('<span id="title-error" class="help-block">' + pollData.message.email + '</span>');

            return false;
        }
    }

    $('.error_participant').removeClass('has-error');

    return true;
}

/**
 *
 * jQuery Validate Emails with Regex
 *
 * @param email
 * @returns {boolean}
 */
function validateEmail(email) {
    var pattern = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    return $.trim(email).match(pattern) ? true : false;
}

/**
 * validate link
 *
 * @param token
 * @returns {*}
 */
function validateLink(token) {
    return $.ajax({
        url: $('.hide').data("routeLink"),
        type: 'post',
        async: false,
        dataType: 'json',
        data: {
            'token': token,
            '_token': $('.hide').data("token")
        },
        success: function (data) {
            if (data.success) {
                $('.message-send-mail').html("<div class='alert alert-success'>"
                    + pollData.message.send_email_success + "</div>");
            } else {
                $('.message-send-mail').html("<div class='alert alert-danger'>"
                    + pollData.message.send_email_fail + "</div>");
            }
        }
    });
}

/**
 *
 * validate update information of poll
 *
 * @returns {boolean}
 */
function updatePollInfo() {
    var valid = $("#form_update_poll_info").valid();
    if(! valid) {
        $validator.focusInvalid();
        return false;
    }

    $('.loader').show();
    return true;

}

/**
 *
 * validate update poll setting
 *
 * @returns {boolean}
 */
function updatePollSetting() {
    var isValidLink = true;
    var isValidLimit = true;
    var isValidPassword = true;

    $('input[name^="setting"]:checked').each(function () {
        switch ($(this).val()) {
            case pollData.config.setting.custom_link:
                isValidLink = checkLink($('.hide').data("linkPoll"));
                break;
            case pollData.config.setting.set_limit:
                isValidLimit = checkLimit();
                break;
            case pollData.config.setting.set_password:
                isValidPassword = checkPassword();
                break;
            default:
                break;
        }
    });

    if (isValidLink && isValidLimit && isValidPassword) {
        $('.loader').show();

        return true;
    }

    return false;
}

/**
 *
 * validate link of poll
 *
 * @param link
 * @returns {boolean}
 */
function checkLink(link) {
    var token = $('#link').val();
    var message = '';

    if (token == "") {
        $('#link').addClass('has-error');
        $('.error_link').addClass('has-error')
            .html('<span id="title-error" class="help-block">' + pollData.message.required + '</span>');

        return false;
    }

    if (typeof link !== 'undefined' && link != (pollData.config.link + token)) {
        if (validateLink($('#link').val()).responseJSON.success) {
            $('#link').addClass('has-error');
            $('.error_link').addClass('has-error')
                .html('<span id="title-error" class="help-block">' + pollData.message.link_exists + '</span>');


            return false;
        }
    }

    if (validateLink($('#link').val()).responseJSON.success) {
        $('#link').addClass('has-error');
        $('.error_link').addClass('has-error')
            .html('<span id="title-error" class="help-block">' + pollData.message.link_exists + '</span>');

        return false;
    }

    $('#link').removeClass('has-error');
    $('.error_link').removeClass('has-error')
        .html('<span id="title-success" class="help-block">' + pollData.message.link_valid + '</span>');

    return true;
}

/**
 *
 * validate limit of poll
 *
 * @returns {boolean}
 */
function checkLimit() {
    var limit = $('#limit').val();
    if (limit == "") {
        $('#limit').addClass('has-error');
        $('.error_limit').addClass('has-error')
            .html('<span id="title-error" class="help-block">' + pollData.message.required + '</span>');

        return false;
    }

    if (! Number.isInteger(limit) && ! (limit > 0)) {
        $('#limit').addClass('has-error');
        $('.error_limit').addClass('has-error')
            .html('<span id="title-error" class="help-block">' + pollData.message.number + '</span>');

        return false;
    }

    if (dataAction == 'edit' && limit <= $('.hide').data("totalVote")) {
        $('#limit').addClass('has-error');
        $('.error_limit').addClass('has-error')
            .html('<span id="title-error" class="help-block">'
                + pollData.message.number_edit + $('.hide').data("totalVote") + '</span>');

        return false;
    }

    $('#limit').removeClass('has-error');
    $('.error_limit').removeClass('has-error').html('');

    return true;
}

/**
 *
 * validate password of poll
 *
 * @returns {boolean}
 */
function checkPassword() {
    var password = $('#password').val();

    if (password == "") {
        $('#password').addClass('has-error');
        $('.error_password').addClass('has-error')
            .html('<span id="title-error" class="help-block">' + pollData.message.required + '</span>');

        return false;
    }

    $('#password').removeClass('has-error');
    $('.error_password').removeClass('has-error');

    return true;
}

/**
 *
 * check validate of form winzard: create, duplicate form
 *
 * @param elementWizard
 * @param formId
 * @returns {boolean}
 */
function checkValidFormWinzard(elementWizard, formId, validator) {

    //get index of tab current
    var wizard = elementWizard.bootstrapWizard('currentIndex');

    //get validation of form
    var valid = formId.valid();

    //check if form valid, it will be return TRUE
    if(! valid) {
        validator.focusInvalid();

        return false;
    }

    //check option of poll
    if (wizard == 1) {
        return validateOption() && (! checkOptionSame());
    }

    if (wizard == 2) {
        var isValid = true;
        var value = 0;

        $('input[name^="setting"]:checked').each(function () {
            value = parseInt($(this).val());

            switch (value){
                case (pollData.config.setting.custom_link):
                    isValid = checkLink();
                    break;
                case (pollData.config.setting.set_limit):
                    isValid = checkLimit();
                    break;
                case (pollData.config.setting.set_password):
                    isValid = checkPassword();
                    break;
                default:
                    break;
            }
        });

        return isValid;
    }
}

/**
 *
 * validate change link admin of poll
 *
 */
function changeLinkAdmin() {
    var tokenLinkAdmin = $('.hide').data('tokenAdmin');
    if (tokenLinkAdmin != $('#link_admin').val()) {
        $('#label_link_admin').removeAttr('href');
    } else {
        $('#label_link_admin').attr('href', $('.token-admin').val());
    }

    if ((pollData.config.link + $('#link_admin').val()).length > 60) {
        $('#label_link_admin').html((pollData.config.link + $('#link_admin').val()).substring(0, 60) + '...');
    } else {
        $('#label_link_admin').html(pollData.config.link + $('#link_admin').val());
    }

    if ($('#link_admin').val() == "") {
        $('#link_admin').addClass('has-error');
        $('.error_link_admin').addClass('has-error');
        $('.error_link_admin').html('<span id="title-error" class="help-block">' + pollData.message.required + '</span>');
    } else {
        if (validateLink($('#link_admin').val()).responseJSON.success) {
            $('#link_admin').addClass('has-error');
            $('.error_link_admin').addClass('has-error');
            $('.error_link_admin').html('<span id="title-error" class="help-block">' + pollData.message.link_exists + '</span>');
            hideLabelMessage('.error_link_admin');
        } else {
            $('#link_admin').removeClass('has-error');
            $('.error_link_admin').removeClass('has-error');
            $('.error_link_admin').html('<span id="title-success" class="help-block">' + pollData.message.link_valid + '</span>');
            hideLabelMessage('.error_link_admin');
        }
    }

}

/**
 *
 * validate change link vote of poll
 *
 */
function changeLinkUser() {
    var tokenLinkUser = $('.hide').data('tokenUser');
    if (tokenLinkUser != $('#link_user').val()) {
        $('#label_link_user').removeAttr('href');
    } else {
        $('#label_link_user').attr('href', $('.token-user').val());
    }

    if ((pollData.config.link + $('#link_user').val()).length > 60) {
        $('#label_link_user').html((pollData.config.link + $('#link_user').val()).substring(0, 60) + '...');
    } else {
        $('#label_link_user').html(pollData.config.link + $('#link_user').val());
    }


    if ($('#link_user').val() == "") {
        $('#link_user').addClass('has-error');
        $('.error_link_user').addClass('has-error');
        $('.error_link_user').html('<span id="title-error" class="help-block">' + pollData.message.required + '</span>');
    } else {
        if (validateLink($('#link_user').val()).responseJSON.success) {
            $('#link_user').addClass('has-error');
            $('.error_link_user').addClass('has-error');
            $('.error_link_user').html(
                '<span id="title-error" class="help-block">'
                + pollData.message.link_exists
                + '</span>');
            hideLabelMessage('.error_link_user');
        } else {
            $('#link_user').removeClass('has-error');
            $('.error_link_user').removeClass('has-error');
            $('.error_link_user').html('<span id="title-success" class="help-block">'
                + pollData.message.link_valid
                + '</span>');
            hideLabelMessage('.error_link_user');
        }
    }
}

/**
 *
 * check option of poll is same?
 *
 * @param input
 * @returns {boolean}
 */
function checkOptionSame(input) {
    var valuesSoFar = [];
    var isDuplicate = false;
    var message = ''
    $('input[name^="optionText"]').each(function () {
        var value = $(this).val();
        if (valuesSoFar.indexOf(value) !== -1 && value != "") {
            isDuplicate = true;
        }
        valuesSoFar.push(value);
    });

    if (isDuplicate) {

        $('.error_option').addClass('has-error').html('<span id="title-error" class="help-block">' + pollData.message.option_duplicate + '</span>');
    } else {
        $('.error_option').removeClass('has-error').html('');
    }

    return isDuplicate;
}

/**
 *
 * check option image of poll is same?
 *
 * @returns {boolean}
 */
function checkImageSame() {
    var srcs = [],
        temp;
    var  isDuplicate = false;
    var images = $(".poll-option img").get();

    $.each(images, function(key, image){
        temp = $('#' + $(image).attr("id")).attr("src");
        if (temp != "#") {
            if($.inArray(temp, srcs) < 0){
                srcs.push(temp);
            } else {
                isDuplicate = true;
            }
        }

    });

    if (isDuplicate) {
        $('.error_option').html('<span id="title-warning" class="help-block">'
            + pollData.message.option_image_duplicate + '</span>');
    } else {
        $('.error_option').html('');
    }

    return isDuplicate;
}

/**
 *
 * get current location of user when create poll
 *
 */
function getCurrentLocation() {
    if ($('#location').val() == '') {
        var map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: -34.397, lng: 150.644},
            zoom: 6
        });

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                $.ajax({
                    url: $('.hide').data("locationRoute"),
                    type: 'post',
                    data: {
                        'lat': position.coords.latitude,
                        'lng': position.coords.longitude,
                        '_token': $('.hide').data("token")
                    },
                    success: function (data) {
                        if (data.success && data.location != '') {
                            $('#location').val(data.location);
                        }
                    }
                });
            });
        }
    }
}

/*-----------------------------------------
                EVENT
 -------------------------------------------*/
/**
 * add method validate time
 */
if (typeof pollData !== "undefined") {
    jQuery.validator.addMethod("time", function(value, element) {
        if (value) {
            var routeCheckDate = $('.hide').data('linkCheckDate');
            var dateClosePoll = $('#time_close_poll').val();
            var result = $.ajax({
                type: 'GET',
                url: routeCheckDate,
                dataType: 'JSON',
                async: false,
                data: {
                    'date_close_poll': dateClosePoll,
                },
                success: function(data){
                    if (data.success) {
                        return true;
                    } else {
                        return false;
                    }
                }
            });

            return result.responseJSON.success;
        }

        return true;
    }, pollData.message.time_close_poll);
}

/*
 Back to top button
 */
$(window).scroll(function(){
    if ($(this).scrollTop() > 100) {
        $('#scroll').fadeIn();
    } else {
        $('#scroll').fadeOut();
    }
});
$('#scroll').click(function(){
    $("html, body").animate({ scrollTop: 0 }, 600);
    return false;
});

/*
 function show add option advance
 */
$(".btn-show-advance-add-option").click(function(){
    $('.addAdvance').slideToggle('slow');
    var id = this.id;
    if (id == "show") {
        $(".btn-show-advance-add-option").html('<span class="glyphicon glyphicon-hand-right"></span>');
        this.id = "hide";
    } else {
        $(".btn-show-advance-add-option").html('<span class="glyphicon glyphicon-hand-left"></span>');
        this.id = "show";
    }
});


$(document).ready(function () {

    /* finish form create or duplicate */
    $(".finish").click(function () {
        if (validateParticipant() & validateOption() && ! checkOptionSame()) {
            $('#form_create_poll, #form_duplicate_poll').submit();
            $('.loader').show();
        }
    });

    if (typeof pollData !== "undefined") {
        $.each(pollData.config.setting, function (index, value) {
            $("[name='setting\\[" + value + "\\]']").bootstrapSwitch({
                'onText' : pollData.message.on,
                'offText' : pollData.message.off
            });
        });

        var $validator = $("#form_create_poll, #form_update_poll_info, #form_duplicate_poll").validate({
            rules: {
                email: {
                    required: true,
                    maxlength: pollData.config.length.email,
                    email: true,
                },
                name: {
                    required: true,
                    maxlength: pollData.config.length.name
                },
                title: {
                    required: true,
                    maxlength: pollData.config.length.title
                },
                closingTime: {
                    time:true
                },
            },
            messages: {
                email: {
                    required: pollData.message.required,
                    maxlength: pollData.message.max + pollData.config.length.email,
                    email: pollData.message.email,
                },
                name: {
                    required: pollData.message.required,
                    maxlength: pollData.message.max + pollData.config.length.name
                },
                title: {
                    required: pollData.message.required,
                    maxlength: pollData.message.max +pollData.config.length.title
                },
                closingTime: {
                    time: pollData.message.time_close_poll
                }
            },
            highlight: function(element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function(element) {
                $(element).closest('.form-group').removeClass('has-error');
            },
            errorElement: 'span',
            errorClass: 'help-block',
            errorPlacement: function(error, element) {
                if(element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            }
        });


        $('#create_poll_wizard').bootstrapWizard({
            'tabClass': 'nav nav-tabs',
            onNext: function(tab, navigation, index) {
                return checkValidFormWinzard($('#create_poll_wizard'), $("#form_create_poll"), $validator);
            },
            onTabClick: function(tab, navigation, index) {
                return checkValidFormWinzard($('#create_poll_wizard'), $("#form_create_poll"), $validator);
            },
            onTabShow: function(tab, navigation, index) {
                var $total = navigation.find('li').length;
                var $current = index+1;
                var $percent = ($current/$total) * 100;
                $('#create_poll_wizard').find('.bar').css({width:$percent + '%'});

                if($current == 1) {
                    $('#create_poll_wizard').find('.pager .previous').hide();
                } else {
                    $('#create_poll_wizard').find('.pager .previous').show();

                    if($current == 2) {
                        $('.finish').removeClass('hidden');
                        $('.btn-finish').addClass('btn-center');
                    }

                    if($current == 4) {
                        $('.btn-finish').removeClass('btn-center');
                    }
                }
            }
        });
    }

    $('#voting_wizard').bootstrapWizard({
        // 'tabClass': 'nav nav-pills'
    });
    $('#manager_poll_wizard').bootstrapWizard({
        // 'tabClass': 'nav nav-pills'
    });
    $('#edit_poll_wizard').bootstrapWizard({
        // 'tabClass': 'nav nav-pills'
    });
    $('#duplicate_poll_wizard').bootstrapWizard({
        'tabClass': 'nav nav-tabs',
        onNext: function(tab, navigation, index) {
            return checkValidFormWinzard($('#duplicate_poll_wizard'), $("#form_duplicate_poll"), $validator);
        },
        onTabClick: function(tab, navigation, index) {
            return checkValidFormWinzard($('#duplicate_poll_wizard'), $("#form_duplicate_poll"), $validator);
        },
        onTabShow: function(tab, navigation, index) {
            var $total = navigation.find('li').length;
            var $current = index+1;
            var $percent = ($current/$total) * 100;
            $('#duplicate_poll_wizard').find('.bar').css({width:$percent+'%'});

            if($current == 1) {
                $('#create_poll_wizard').find('.pager .previous').hide();
            }
        }
    });
});

$(window).on('load', function() {
    if (typeof pollData !== "undefined") {
        var oldInput = pollData.oldInput;
        var viewOption = pollData.view.option;
        var number = pollData.config.length.option;
        createOption(viewOption, number, oldInput);

        if (oldInput) {
            $('#email-participant').show();
        }
    }
});

//autoload location
if (document.getElementById('location') != null) {
    google.maps.event.addDomListener(window, 'load', function () {
        var places = new google.maps.places.Autocomplete(document.getElementById('location'));
        google.maps.event.addListener(places, 'place_changed', function () {

        });
    });
}
