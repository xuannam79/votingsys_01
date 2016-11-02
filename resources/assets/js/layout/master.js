//autoload location
google.maps.event.addDomListener(window, 'load', function () {
    var places = new google.maps.places.Autocomplete(document.getElementById('location'));
    google.maps.event.addListener(places, 'place_changed', function () {

    });
});
/*
 * get data from server
 */
var dataCreatePoll = $('.hide').data("poll");

/**--------------------------------------------------------------
-                         USER CREATE POLL                      -
 ---------------------------------------------------------------*/
$(document).ready(function () {
    //Initialize tooltips
    $('.nav-tabs > li a[title]').tooltip();

    //Wizard
    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {

        var $target = $(e.target);

        if ($target.parent().hasClass('disabled')) {
            return false;
        }
    });

    $(".next-step").click(function (e) {
        var isNext = false;

        if ($(this).val() == "info" && validateInfo()) {
            isNext = true;
        }

        if ($(this).val() == "option" && validateOption()) {
            isNext = true;
        }

        if ($(this).val() == "setting" && validateSetting()) {
            isNext = true;
        }

        if (isNext) {
            var $active = $('.wizard .nav-tabs li.active');
            $active.next().removeClass('disabled');
            nextTab($active);
        }
    });

    $(".finish").click(function () {
        if (validateParticipant()) {
            $('#create-poll').submit();
        }
    });
    $(".prev-step").click(function (e) {

        var $active = $('.wizard .nav-tabs li.active');
        prevTab($active);

    });

    if (typeof dataCreatePoll !== "undefined") {
        $('input[type=radio][name=participant]').change(function () {
            if (this.value == dataCreatePoll.message.config.invite_all) {
                $("#validateParticipant").html("");
                $('#email-participant').hide('slow');
            }
            else if (this.value == dataCreatePoll.message.config.invite_people) {
                $('#email-participant').show('slow');
            }
        });
    }
});

function nextTab(elem) {
    $(elem).next().find('a[data-toggle="tab"]').click();
}
function prevTab(elem) {
    $(elem).prev().find('a[data-toggle="tab"]').click();
}

$(window).on('load', function() {
    if (typeof dataCreatePoll !== "undefined") {
        var oldInput = dataCreatePoll.oldInput;
        var viewOption = dataCreatePoll.view.option;
        var number = dataCreatePoll.message.numberOfOptions;
        createOption(viewOption, number, oldInput);

        if (oldInput && oldInput.participant == dataCreatePoll.message.config.invite_people) {
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
    $('input[name = "optionImage[' + idOption + ']"]').click();
}

// Preview image
function readURL(input, idShow) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#' + idShow).show().attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

//remove option
function removeOpion(idOption, action) {
    if (typeof dataCreatePoll !== "undefined" && typeof action !== "undefined") {
        if (confirmDelete(dataCreatePoll.message.confirm_delete_option)) {
            $("#" + idOption).remove();
        }
    } else {
        $("#" + idOption).remove();
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
    if (typeof dataCreatePoll !== "undefined") {
        if (key == dataCreatePoll.message.setting.link) {
            $("#new-link").slideToggle();
        } else if (key == dataCreatePoll.message.setting.limit) {
            $("#set-limit").slideToggle();
        } else if (key == dataCreatePoll.message.setting.password) {
            $("#set-password").slideToggle();
        }
    }
}

//Validate input
function validateInput(text, length, type, name) {
    var message = "";
    var regexEmail = /^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i;

    if (typeof dataCreatePoll !== "undefined") {
        if (text == "") {
            message = dataCreatePoll.message.validate.required + name;
        } else if (text.length > length) {
            message = dataCreatePoll.message.validate.max + length + dataCreatePoll.message.validate.character;
        } else if (type == "email" && ! regexEmail.test(text)) {
            message = dataCreatePoll.message.validate.email;
        } else if (type === "number" && ! Number.isInteger(text) && ! (text > 0)) {
            message = dataCreatePoll.message.validate.number;
        }
    }

    return message;
}

//validate radiobutton of checkbox
function validateRadioAndCheckbox(value, name) {
    var message = "";

    if (typeof dataCreatePoll !== "undefined" && (typeof value === "undefined" || value == "")) {
        message = dataCreatePoll.message.validate.choose + name;
    }

    return message;
}

function validateInfo() {
    if (typeof dataCreatePoll !== "undefined") {
        var messageTitle = validateInput($('#title').val(), dataCreatePoll.message.length.title, "text", "title");
        var messageName = validateInput($('#name').val(), dataCreatePoll.message.length.name, "text", "name");
        var messageEmail = validateInput($('#email').val(), dataCreatePoll.message.length.email, "email", "email");
        var messageType = validateRadioAndCheckbox($("input[name=type]:checked").val(), "type");

        //reset message
        $('#validateTitle').html("");
        $('#validateDescription').html("");
        $('#validateName').html("");
        $('#validateEmail').html("");
        $('#validateType').html("");

        if (messageTitle != "") {
            $("#title").addClass("error");
            $("#title").after("<div id='validateTitle'>" +
                "<span class='label label-danger'>" + messageTitle + "</span>" +
                "</div>");
        } else {
            $("#title").removeClass("error");
        }

        if ($('#description').val().length > dataCreatePoll.message.length.description) {
            $("#description").addClass("error");
            $("#description").after("<div id='validateDescription'>" +
                "<span class='label label-danger'>" + dataCreatePoll.message.validate.max + dataCreatePoll.message.length.description
                + "</span></div>");
        } else {
            $("#description").removeClass("error");
        }

        if (messageName != "") {
            $("#name").addClass("error");
            $("#name").after("<div id='validateName'>" +
                "<span class='label label-danger'>" + messageName + "</span>" +
                "</div>");
        } else {
            $("#name").removeClass("error");
        }

        if (messageEmail != "") {
            $("#email").addClass("error");
            $("#email").after("<div id='validateEmail'>" +
                "<span class='label label-danger'>" + messageEmail + "</span>" +
                "</div>");
        } else {
            $("#email").removeClass("error");
            var dataEmailRoute = $('.hide').data("routeEmail");
            var token = $('.hide').data("token");
            if (checkEmail(dataEmailRoute, token).responseJSON.success) {
                return false;
            }
        }

        if (messageType != "") {
            $("#type").after("<div id='validateType'>" +
                "<span class='label label-danger'>" + messageType + "</span>" +
                "</div>");
        } else {
            $("#type").removeClass("error");
        }

        if (messageTitle == ""
            && messageName == ""
            && messageEmail == ""
            && messageType == ""
            && $('#description').val().length < dataCreatePoll.message.length.description) {
            return true;
        }

        return false;
    }

    return true;
}

function validateOption() {
    if (typeof dataCreatePoll !== "undefined") {
        var optionLists = $('input[name^="optionText"]');
        var imageLists = $('input[name^="optionImage"]');
        var isOption = false;
        $('#validateOption').html("");

        if (optionLists.length == 0 && imageLists.length == 0) {
            $('.option').after("<div id='validateOption'>" +
                "<span class='label label-danger'>" + dataCreatePoll.message.validate.option_empty + "</span>" +
                "</div>");
            return false;
        }
        optionLists.each(function () {
            if ($(this).val() != "") {
                isOption = true;
            } else {
                imageLists.each(function () {
                    if ($(this).val() != "") {
                        isOption = true;
                    }
                });
            }
        });

        if (!isOption) {
            $('.option').after("<div id='validateOption'>" +
                "<span class='label label-danger'>" + dataCreatePoll.message.validate.option_required + "</span>" +
                "</div>");
            return false;
        }

        return true;
    }

    return true;
}

function validateSetting() {
    $('#validateLink').html("");
    $('#validateLimit').html("");
    $('#validatePassword').html("");
    var isValid = true;

    if (typeof dataCreatePoll !== "undefined") {
        $('input[name^="setting"]:checked').each(function () {
            if ($(this).val() == dataCreatePoll.message.setting.link) {
                var messageLink = validateInput($('#link').val(), dataCreatePoll.message.length.link, "text", "link");

                if (messageLink != "") {
                    isValid = false;

                    //custom link
                    $("#new-link").after("<div id='validateLink'>" +
                        "<p><span class='label label-danger'>" + messageLink + "</span></p><br>" +
                        "</div>");
                } else {
                    var dataLinkRoute = $('.hide').data("routeLink");
                    var token = $('.hide').data("token");
                    if (checkLink(dataLinkRoute, token).responseJSON.success) {
                        isValid = false;
                    }
                }
            }

            if ($(this).val() == dataCreatePoll.message.setting.limit) {
                var messageLimit = validateInput($('#limit').val(), dataCreatePoll.message.length.limit, "number", "limit");

                if (messageLimit != "") {
                    isValid = false;
                    $("#set-limit").after("<div id='validateLimit'>" +
                        "<p><span class='label label-danger'> " + messageLimit + "</span><p><br>" +
                        "</div>");
                }
            }

            if ($(this).val() == dataCreatePoll.message.setting.password) {
                var messagePassword = validateInput($('#password').val(), dataCreatePoll.message.length.password, "text", "password");

                if (messagePassword != "") {
                    //custom link
                    isValid = false;
                    $("#set-password").after("<div id='validatePassword'>" +
                        "<p><span class='label label-danger'>" + messagePassword + "</span><p><br>" +
                        "</div>");
                }
            }

        });
    }

    return isValid;
}

function validateParticipant() {
    var participant = $("input[name='participant']:checked").val();
    $("#validateParticipant").html("");

    if (typeof dataCreatePoll !== "undefined") {
        if (participant == dataCreatePoll.message.config.invite_people) {
            var members = $("#member").val();
            members = members.split(",");

            if (members.length > 0) {
                for (var index = 0; index < members.length; index++) {
                    var messageParticipant = validateInput(members[index], dataCreatePoll.message.length.email, "email", "participant");

                    if (messageParticipant != "") {

                        //custom link
                        $("#email-participant").after("<div id='validateParticipant'>" +
                            "<p><span class='label label-danger'>" + messageParticipant + "</span><p><br>" +
                            "</div>");

                        return false;
                    }
                }

                return true;
            }

            //custom link
            $("#email-participant").after("<div id='validateParticipant'>" +
                "<p><span class='label label-danger'>" + dataCreatePoll.message.validate.participant_empty + "</span><p><br>" +
                "</div>");

            return false;
        }

        return true;
    }

    return true;
}

function checkEmail(route, token) {
    if (typeof dataCreatePoll !== "undefined") {
        return $.ajax({
            url: route,
            type: 'post',
            async: false,
            dataType: 'json',
            data: {
                'email': $('#email').val(),
                '_token': token,
            },
            success: function (data) {
                if (data.success) {
                    //email exitsts
                    $("#email").addClass("error");
                    $('.email-error').html("<div class='label label-danger'>"
                        + dataCreatePoll.message.validate.email_exists
                        + "</div>");
                } else {
                    $("#email").removeClass("error");
                    $('.email-error').html("<div class='label label-success'>"
                        + dataCreatePoll.message.validate.email_valid
                        + "</div>");
                }
            }
        });
    }
}

//check token of link exist
function checkLink(route, token) {
    if (typeof dataCreatePoll !== "undefined") {
        return $.ajax({
            url: route,
            type: 'post',
            async: false,
            dataType: 'json',
            data: {
                'value': $('#link').val(),
                '_token': token,
            },
            success: function (data) {
                if (data.success) {
                    //link exists
                    $("#link").addClass("error");
                    $('.link-error').html("<div class='label label-danger'>" + dataPage.message.validate.link_exists + "</div>");
                } else {
                    $("#link").removeClass("error");
                    $('.link-error').html("<div class='alert alert-success'>" + dataPage.message.validate.link_valid + "</div>");
                }
            }
        });
    }
}
