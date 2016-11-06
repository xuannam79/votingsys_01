//autoload location
if (document.getElementById('location') != null) {
    google.maps.event.addDomListener(window, 'load', function () {
        var places = new google.maps.places.Autocomplete(document.getElementById('location'));
        google.maps.event.addListener(places, 'place_changed', function () {

        });
    });
}


var loadFile = function(event) {
    var output = document.getElementById('output');
    output.src = URL.createObjectURL(event.target.files[0]);
};

/*
 * get data from server
 */
var pollData = $('.hide').data("poll");
var dataAction = $('.hide').data("action");
var dataSettingEdit = $('.hide').data("settingEdit");

/**--------------------------------------------------------------
-                         USER CREATE POLL                      -
 ---------------------------------------------------------------*/
$(document).ready(function () {

    $(".finish").click(function () {
        if (validateParticipant() & validateOption() && ! checkOptionSame()) {
            $('#form_create_poll').submit();
            $('.loader').show();
        }
    });
});

function nextTab(elem) {
    $(elem).next().find('a[data-toggle="tab"]').click();
}
function prevTab(elem) {
    $(elem).prev().find('a[data-toggle="tab"]').click();
}

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

// Create option
function createOption(viewOption, number, oldInput) {
    number = (typeof number === 'undefined') ? 1 : number;

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


//Show option image
function showOptionImage(idOption) {
    addAutoOption(idOption);
    $('input[name = "optionImage[' + idOption + ']"]').click();
}

// Preview image
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
    }

}

//remove option
function removeOpion(idOption, action) {
    if (typeof pollData !== "undefined" && typeof action !== "undefined") {
        if (confirmDelete(pollData.message.confirm_delete_option)) {
            $("#" + idOption).remove();
        }
    } else {
        $("#" + idOption).remove();
        var optionLists = $('input[name^="optionText"]');
        if (optionLists.length == 0) {
            $('.error_option').closest('.form-group').addClass('has-error');
            $('.error_option').html('<span id="title-error" class="help-block">' + pollData.message.option_minimum + '</span>');
            var viewOption = pollData.view.option;
            var number = pollData.config.length.option;
            createOption(viewOption);
        }
    }

}

//add option
function addOption(data) {
    var number = $('#number').val();
    var view = data.view.option;
    number = (typeof number == 'undefined' || number == "") ? 1 : number;
    createOption(view, number);
}

//random name of option
var rand = function() {
    return Math.random().toString(36).substr(2); // remove `0.`
};

//show advance setting: custom link, set limit, set password
function settingAdvance(key) {
    $('#limit').closest('.form-group').removeClass('has-error');
    $('.error_limit').closest('.form-group').removeClass('has-error');
    $('.error_limit').html('');
    $('#link').closest('.form-group').removeClass('has-error');
    $('.error_link').closest('.form-group').removeClass('has-error');
    $('.error_link').html('');
    $('#password').closest('.form-group').removeClass('has-error');
    $('.error_password').closest('.form-group').removeClass('has-error');
    $('.error_password').html('');
    if (key == pollData.config.setting.custom_link) {
        $("#setting-link").slideToggle();
    } else if (key == pollData.config.setting.set_limit) {
        $("#setting-limit").slideToggle();
    } else if (key == pollData.config.setting.set_password) {
        $("#setting-password").slideToggle();
    }
}
var _validFileExtensions = [".jpg", ".jpeg", ".bmp", ".gif", ".png"];
function ValidateSingleInput(oInput) {
        var sFileName = oInput;
        if (sFileName.length > 0) {
            var blnValid = false;
            for (var j = 0; j < _validFileExtensions.length; j++) {
                var sCurExtension = _validFileExtensions[j];
                if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
                    blnValid = true;
                    break;
                }
            }

            if (!blnValid) {
                $('.error_option').closest('.form-group').addClass('has-error');
                $('.error_option').html('<span id="title-error" class="help-block">' + pollData.message.image + '</span>');
                return false;
            }
        }
    return true;
}

function validateOption() {
    var optionLists = $('input[name^="optionText"]');
    var imageLists = $('input[name^="optionImage"]');
    var isOption = true;
    var isEmpty = false;
    $('#validateOption').html("");

    if (optionLists.length == 0 && imageLists.length == 0) {
        $('.error_option').closest('.form-group').addClass('has-error');
        $('.error_option').html('<span id="title-error" class="help-block">' + pollData.message.option_empty + '</span>');
        return false;
    }

    optionLists.each(function () {
        if ($(this).val() != "") {
            isEmpty = true;
        }
    });

    if (! isEmpty) {
        $('.error_option').closest('.form-group').addClass('has-error');
        $('.error_option').html('<span id="title-error" class="help-block">' + pollData.message.option_required + '</span>');
        return false;
    }

    optionLists.each(function (key) {
        var id = $(this).attr('id');

        if ($(this).val() == "") {
            imageLists.each(function (keyImage) {
                if (keyImage == key) {
                    if ($(this).val() != "") {
                        $('#' + id).addClass('error-input');
                        $('.error_option').closest('.form-group').addClass('has-error');
                        $('.error_option').html('<span id="title-error" class="help-block">' + pollData.message.option_required + '</span>');
                        isOption = false;
                    }
                }
            });
        }
    });

    return isOption;
}

function validateParticipant() {
    var members = $("#member").val();

    if (members == "") {
        $('.error_participant').closest('.form-group').removeClass('has-error');
        return true;
    }

    members = members.split(",");

    for (var index = 0; index < members.length; index++) {
        if (! validateEmail(members[index])) {
            $('.error_participant').closest('.form-group').addClass('has-error');
            $('.error_participant').html('<span id="title-error" class="help-block">' + pollData.message.email + '</span>');
            return false;
        }
    }

    $('.error_participant').closest('.form-group').removeClass('has-error');
    return true;

}

//Auto close message
$(".alert-dismissable").delay(3000).fadeOut(1000);

//Datetime picker
$(function () {
    $('#time_close_poll').datetimepicker({
        format: 'DD-MM-YYYY HH:mm'
    });
});

function showOptionDetail() {
    $('#option-detail').slideToggle();
}

function showSettingDetail() {
    $('#setting-detail').slideToggle();
}

function confirmDelete(message) {
    return confirm(message);
}

//validate email
function validateEmailExists() {

    return $.ajax({
        url: $('.hide').data("routeEmail"),
        type: 'post',
        async: false,
        dataType: 'json',
        data: {
            'email': $('#email').val(),
            '_token': $('.hide').data("token")
        },
        success: function (data) {
            if (! data.success) {
                $('#email').closest('.form-group').addClass('has-error');
                $('.error_email').closest('.form-group').addClass('has-error');
                $('.error_email').html('<span id="title-error" class="help-block">' + pollData.message.email_exist + '</span>');
            }
        }
    });

}

/* jQuery Validate Emails with Regex */
function validateEmail(email) {
    var pattern = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    return $.trim(email).match(pattern) ? true : false;
}

//validate link
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
                $('.message-send-mail').html("<div class='alert alert-success'>" + pollData.message.send_email_success + "</div>");
            } else {
                $('.message-send-mail').html("<div class='alert alert-danger'>" + pollData.message.send_email_fail + "</div>");
            }
        }
    });
}

//add method validate time
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


$(document).ready(function() {
    if (typeof pollData !== "undefined") {
        $.each(pollData.config.setting, function (index, value) {
            $("[name='setting\\[" + value + "\\]']").bootstrapSwitch({
                'onText' : pollData.message.on,
                'offText' : pollData.message.off
            });
        });

        $('[data-toggle="tooltip"]').tooltip();
        var $validator = $("#form_create_poll, #form_update_poll_info").validate({
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
                    // exist: pollData.message.email_exist
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

                //get index of tab current
                var wizard = $('#create_poll_wizard').bootstrapWizard('currentIndex');

                //get validation of form
                var valid = $("#form_create_poll").valid();

                //check if form valid, it will be return TRUE
                if(! valid) {
                    $validator.focusInvalid();

                    return false;
                }

                //check option of poll
                if (wizard == 1) {
                    return validateOption() && (! checkOptionSame());
                } else if (wizard == 2) {
                    var isValid = true;

                    $('input[name^="setting"]:checked').each(function () {
                        if ($(this).val() == pollData.config.setting.custom_link) {
                            isValid = checkLink();
                        } else if ($(this).val() == pollData.config.setting.set_limit) {
                            isValid = checkLimit();
                        } else if ($(this).val() == pollData.config.setting.set_password) {
                            isValid = checkPassword();
                        }
                    });

                    return isValid;
                }

            },
            onTabClick: function(tab, navigation, index) {
                //get validation of form
                var valid = $("#form_create_poll").valid();

                //get index of tab current
                var wizard = $('#create_poll_wizard').bootstrapWizard('currentIndex');

                if(! valid) {
                    $validator.focusInvalid();

                    return false;
                }
                //check option of poll
                if (wizard == 1) {
                    return validateOption() && (! checkOptionSame());
                } else if (wizard == 2) {
                    var isValid = true;

                    $('input[name^="setting"]:checked').each(function () {
                        if ($(this).val() == pollData.config.setting.custom_link) {
                            isValid = checkLink();
                        } else if ($(this).val() == pollData.config.setting.set_limit) {
                            isValid = checkLimit();
                        } else if ($(this).val() == pollData.config.setting.set_password) {
                            isValid = checkPassword();
                        }
                    });

                    return isValid;
                }
            },
            onTabShow: function(tab, navigation, index) {
                var $total = navigation.find('li').length;
                var $current = index+1;
                var $percent = ($current/$total) * 100;
                $('#create_poll_wizard').find('.bar').css({width:$percent+'%'});
                if($current == 1) {
                    $('#create_poll_wizard').find('.pager .previous').hide();
                    $('.info-explain').css('display', 'block');
                    $('div[class=explain]').not('.info-explain').css('display', 'none !important');
                } else {
                    $('#create_poll_wizard').find('.pager .previous').show();
                    if($current == 2) {
                        $('div[class=explain]').not('.option-explain').css('display', 'none !important');
                        $('.option-explain').css('display', 'block');
                        $('.finish').removeClass('hidden');
                        $('.btn-finish').addClass('btn-center');
                    }
                    if($current == 3) {
                        $(! '.setting-explain').hide('slow');
                        $('.setting-explain').show('slow');
                    }
                    if($current == 4) {
                        $(! '.participant-explain').hide('slow');
                        $('.participant-explain').show('slow');
                        $('.btn-finish').removeClass('btn-center');
                    }
                }

                if (index == 0) {
                    $('.info-explain').css('display', 'block');
                    $('.option-explain').css('display', 'none');
                    $('.panel-setting-explain').css('display', 'none');
                    $('.panel-participant-explain').css('display', 'none');
                } else if (index == 1) {
                    $('.info-explain').css('display', 'none');
                    $('.option-explain').css('display', 'block');
                    $('.panel-setting-explain').css('display', 'none');
                    $('.panel-participant-explain').css('display', 'none');
                } else if (index == 2) {
                    $('.info-explain').css('display', 'none');
                    $('.option-explain').css('display', 'none');
                    $('.panel-setting-explain').css('display', 'block');
                    $('.panel-participant-explain').css('display', 'none');
                } else if (index == 3) {
                    $('.info-explain').css('display', 'none');
                    $('.option-explain').css('display', 'none');
                    $('.panel-setting-explain').css('display', 'none');
                    $('.panel-participant-explain').css('display', 'block');
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
        // 'tabClass': 'nav nav-pills',
        onTabClick: function(tab, navigation, index) {

            return false;
        }
    });

});

function updatePollInfo()
{
    console.log("update");
    //get validation of form
    var valid = $("#form_update_poll_info").valid();
    if(! valid) {
        $validator.focusInvalid();
        return false;
    }

    $('.loader').show();
    return true;

}

/*
 validate update poll setting
 */
function updatePollSetting() {
    var isValidLink = true;
    var isValidLimit = true;
    var isValidPassword = true;

    $('input[name^="setting"]:checked').each(function () {
        if ($(this).val() == pollData.config.setting.custom_link) {
            isValidLink = checkLinkUpdate($('.hide').data("linkPoll"));
        } else if ($(this).val() == pollData.config.setting.set_limit) {
            isValidLimit = checkLimit();
        } else if ($(this).val() == pollData.config.setting.set_password) {
            isValidPassword = checkPassword();
        }
    });

    if (isValidLink && isValidLimit && isValidPassword) {
        $('.loader').show();

        return true;
    }

    return false;
}

function checkLinkUpdate(link) {
    console.log(link)
    var token = $('#link').val();

    if (token == "") {
        $('#link').closest('.form-group').addClass('has-error');
        $('.error_link').closest('.form-group').addClass('has-error');
        $('.error_link').html('<span id="title-error" class="help-block">' + pollData.message.required + '</span>');

        return false;
    }

    if (link != (pollData.config.link + token)) {
        if (validateLink($('#link').val()).responseJSON.success) {
            $('#link').closest('.form-group').addClass('has-error');
            $('.error_link').closest('.form-group').addClass('has-error');
            $('.error_link').html('<span id="title-error" class="help-block">' + pollData.message.link_exists + '</span>');

            return false;
        }
    }

    $('#link').closest('.form-group').removeClass('has-error');
    $('.error_link').closest('.form-group').removeClass('has-error');
    $('.error_link').html('<span id="title-success" class="help-block">' + pollData.message.link_valid + '</span>');

    return true;
}
/*
 show password
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

/*
validate link of poll
 */
function checkLink() {
    var token = $('#link').val();

    if (token == "") {
        $('#link').closest('.form-group').addClass('has-error');
        $('.error_link').closest('.form-group').addClass('has-error');
        $('.error_link').html('<span id="title-error" class="help-block">' + pollData.message.required + '</span>');

        return false;
    }

    if (validateLink($('#link').val()).responseJSON.success) {
        $('#link').closest('.form-group').addClass('has-error');
        $('.error_link').closest('.form-group').addClass('has-error');
        $('.error_link').html('<span id="title-error" class="help-block">' + pollData.message.link_exists + '</span>');

        return false;
    }

    $('#link').closest('.form-group').removeClass('has-error');
    $('.error_link').closest('.form-group').removeClass('has-error');
    $('.error_link').html('<span id="title-success" class="help-block">' + pollData.message.link_valid + '</span>');

    return true;
}

/*
validate limit of poll
 */
function checkLimit() {
    var limit = $('#limit').val();

    if (limit == "") {
        $('#limit').closest('.form-group').addClass('has-error');
        $('.error_limit').closest('.form-group').addClass('has-error');
        $('.error_limit').html('<span id="title-error" class="help-block">' + pollData.message.required + '</span>');

        return false;
    }

    if (! Number.isInteger(limit) && ! (limit > 0)) {
        $('#limit').closest('.form-group').addClass('has-error');
        $('.error_limit').closest('.form-group').addClass('has-error');
        $('.error_limit').html('<span id="title-error" class="help-block">' + pollData.message.number + '</span>');

        return false;
    }

    if (dataAction == 'edit' && limit <= $('.hide').data("totalVote")) {
        $('#limit').closest('.form-group').addClass('has-error');
        $('.error_limit').closest('.form-group').addClass('has-error');
        $('.error_limit').html('<span id="title-error" class="help-block">' + pollData.message.number_edit + $('.hide').data("totalVote") + '</span>');

        return false;
    }

    $('#limit').closest('.form-group').removeClass('has-error');
    $('.error_limit').closest('.form-group').removeClass('has-error');
    $('.error_limit').html('');

    return true;
}

/*
 validate password of poll
 */
function checkPassword() {
    var password = $('#password').val();

    if (password == "") {
        $('#password').closest('.form-group').addClass('has-error');
        $('.error_password').closest('.form-group').addClass('has-error');
        $('.error_password').html('<span id="title-error" class="help-block">' + pollData.message.required + '</span>');

        return false;
    }

    $('#password').closest('.form-group').removeClass('has-error');
    $('.error_password').closest('.form-group').removeClass('has-error');

    return true;
}

/*
show modal preview option image in view details
 */
function showModelImage(image) {
    $('#imageOfOptionPreview').attr("src", image);
    $('#modalImageOption').modal('show');
}

/*
show panel body of poll option horizontal in view details
 */
function showPanelImage(id) {
    $('#option_' + id).toggle('slow');
}

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
        $('#link_admin').closest('.form-group').addClass('has-error');
        $('.error_link_admin').closest('.form-group').addClass('has-error');
        $('.error_link_admin').html('<span id="title-error" class="help-block">' + pollData.message.required + '</span>');
    } else {
        if (validateLink($('#link_admin').val()).responseJSON.success) {
            $('#link_admin').closest('.form-group').addClass('has-error');
            $('.error_link_admin').closest('.form-group').addClass('has-error');
            $('.error_link_admin').html('<span id="title-error" class="help-block">' + pollData.message.link_exists + '</span>');
        } else {
            $('#link_admin').closest('.form-group').removeClass('has-error');
            $('.error_link_admin').closest('.form-group').removeClass('has-error');
            $('.error_link_admin').html('<span id="title-success" class="help-block">' + pollData.message.link_valid + '</span>');
        }
    }

}

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
        $('#link_user').closest('.form-group').addClass('has-error');
        $('.error_link_user').closest('.form-group').addClass('has-error');
        $('.error_link_user').html('<span id="title-error" class="help-block">' + pollData.message.required + '</span>');
    } else {
        if (validateLink($('#link_user').val()).responseJSON.success) {
            $('#link_user').closest('.form-group').addClass('has-error');
            $('.error_link_user').closest('.form-group').addClass('has-error');
            $('.error_link_user').html('<span id="title-error" class="help-block">' + pollData.message.link_exists + '</span>');
        } else {
            $('#link_user').closest('.form-group').removeClass('has-error');
            $('.error_link_user').closest('.form-group').removeClass('has-error');
            $('.error_link_user').html('<span id="title-success" class="help-block">' + pollData.message.link_valid + '</span>');
        }
    }
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
function auto add option
 */
function addAutoOption(idOption) {
    $('#optionText-' + idOption).removeClass('error-input');
    if ($('#' + idOption).is(':last-child')) {
        var viewOption = pollData.view.option;
        var number = pollData.config.length.option;
        createOption(viewOption);
    }
}


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

/*
 sendMailAgain
 */
function sendMailAgain() {
    var poll = $('.hide').data('emailPoll');
    var link = $('.hide').data('emailLink');
    var password = $('.hide').data('emailPassword');
    var message = $('.hide').data('emailMessage');
    $('.loader').show();
    $.ajax({
        url: $('.hide').data("emailRoute"),
        type: 'post',
        data: {
            'poll': poll,
            'link': link,
            'password': password,
            '_token': $('.hide').data("token")
        },
        success: function (data) {
            $('.loader').hide();
            if (data.success) {
                $('.message-send-mail').html("<div class='alert alert-success'>" + message.send_email_success + "</div>");
            } else {
                $('.message-send-mail').html("<div class='alert alert-danger'>" + message.send_email_fail + "</div>");
            }
        }
    });
}

/*
auto copy link
 */
function copyToClipboard(element, link) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(link).select();
    document.execCommand("copy");
    $temp.remove();
}

function checkOptionSame(input) {
    var valuesSoFar = [];
    var isDuplicate = false;
    $('input[name^="optionText"]').each(function () {
        var value = $(this).val();
        if (valuesSoFar.indexOf(value) !== -1 && value != "") {
            isDuplicate = true;
        }
        valuesSoFar.push(value);
    });

    if (isDuplicate) {
        $('.error_option').closest('.form-group').addClass('has-error');
        $('.error_option').html('<span id="title-error" class="help-block">' + pollData.message.option_duplicate + '</span>');
    } else {
        $('.error_option').closest('.form-group').removeClass('has-error');
        $('.error_option').html('');
    }

    return isDuplicate;
}

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
        $('.error_option').html('<span id="title-warning" class="help-block">' + pollData.message.option_image_duplicate + '</span>');
    } else {
        $('.error_option').html('');
    }

    return isDuplicate;
}

function voted(id, type) {
    if (type == 'horizontal') {
        $('#horizontal-' +id).prop('checked',! $('#horizontal-' + id).prop('checked'));
        $('#vertical-' +id).prop('checked', $('#horizontal-' + id).prop('checked'));
    } else {
        $('#vertical-' + id).prop('checked',! $('#vertical-' + id).prop('checked'));
        $('#horizontal-' +id).prop('checked', $('#vertical-' + id).prop('checked'));
    }
}

function autoScrollToElement(id) {
    $('html, body').animate({
        scrollTop: $("#" + id).offset().top + 100
    }, 2000);
}


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

$('a[data-toggle="tooltip"]').tooltip({
    animated: 'fade',
    placement: 'bottom',
    html: true
});


$(document).ready(function() {
    $("#countries").msDropdown();
})
