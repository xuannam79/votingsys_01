//autoload location
google.maps.event.addDomListener(window, 'load', function () {
    var places = new google.maps.places.Autocomplete(document.getElementById('location'));
    google.maps.event.addListener(places, 'place_changed', function () {

    });
});

$(function () {
    var form = $('#form_create_poll').show();
    var data = $('.hide').data("poll");

    form.steps({
        headerTag: 'h3',
        bodyTag: 'fieldset',
        transitionEffect: 'slideLeft',
        onInit: function (event, currentIndex) {
            $.AdminBSB.input.activate();

            //Set tab width
            var $tab = $(event.currentTarget).find('ul[role="tablist"] li');
            var tabCount = $tab.length;
            $tab.css('width', (100 / tabCount) + '%');

            //set button waves effect
            setButtonWavesEffect(event);
        },
        onStepChanging: function (event, currentIndex, newIndex) {
            if (currentIndex > newIndex) {
                return true;
            }

            if (currentIndex < newIndex) {
                form.find('.body:eq(' + newIndex + ') label.error').remove();
                form.find('.body:eq(' + newIndex + ') .error').removeClass('error');
            }

            form.validate().settings.ignore = ':disabled,:hidden';
            return form.valid();
        },
        onStepChanged: function (event, currentIndex, priorIndex) {
            setButtonWavesEffect(event);
        },
        onFinishing: function (event, currentIndex) {
            form.validate().settings.ignore = ':disabled';
            return form.valid();
        },
        onFinished: function (event, currentIndex) {
            swal(data.message.submit_form);
            setTimeout(function () {
                $('#form_create_poll').submit();
            }, 2000);
        }
    });
});

$(window).on('load', function() {
    var data = $('.hide').data("poll");

    //init page
    if (typeof data !== "undefined") {
        var oldInput = data.oldInput;
        var viewOption = data.view.option;
        var viewEmail = data.view.email;
        var number = data.message.numberOfOptions;
        createOption(viewOption, number, oldInput);
        createEmailParticipant(viewEmail, number, oldInput);
    }
});

// Create option
function createOption(viewOption, number, oldInput) {
    number = (typeof number == 'undefined') ? 3 : number;

    if (oldInput) {
        var oldOption = oldInput.option;
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
            $('#' + idShow).show('slow').attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

//remove option
function removeOpion(idOption, action) {
    var dataPage = $('.hide').data("poll");

    if (typeof dataPage !== "undefined" && typeof action !== "undefined") {
        if (confirmDelete(dataPage.message.confirm_delete_option)) {
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

//setting
$(function() {
    $('#custom_link').change(function() {
        var value = $(this).prop('checked');
        var dataRouteLink = $('.hide').data("routeLink");
        var dataToken = $('.hide').data("token");

        if(value ) {
            $("#link-poll").show("slow");
            if ($('#link').val() != "") {
                checkLink(dataRouteLink, dataToken);
            }
        } else {
            $('.link-error').html("");
            $("#link-poll").hide("slow");
        }
    });

    $('#set_limit').change(function() {
        var value = $(this).prop('checked');
        if(value ) {
            $("#poll-limit").show("slow");
        } else {
            $("#poll-limit").hide("slow");
        }
    });

    $('#set_password').change(function() {
        var value = $(this).prop('checked');
        if(value ) {
            $("#password-poll").show("slow");
        } else {
            $("#password-poll").hide("slow");
        }
    });
});

//check token of link exist
function checkLink(route, token) {
    var dataPage = $('.hide').data("poll");

    if (typeof dataPage !== 'undefined') {
        $.ajax({
            url: route,
            type: 'post',
            data: {
                'value': $('#link').val(),
                '_token': token,
            },
            success: function (data) {
                console.log(data);
                if (data.success) {
                    //link exists
                    $('.link-error').html("<div class='alert alert-danger'>" + dataPage.message.link_exists + "</div>");
                } else {
                    $('.link-error').html("<div class='alert alert-success'>" + dataPage.message.link_valid + "</div>");
                }
            }
        });
    }
}

$(document).ready(function() {
    var data = $('.hide').data("poll");

    if (typeof data !== 'undefined') {

        //change participant
        $('input[type=radio][name=participant]').change(function () {
            if (this.value == data.message.config.invite_all) {
                $('.email-participant').hide('slow');
            }
            else if (this.value == data.message.config.invite_people) {
                $('.email-participant').show('slow');
            }
        });

        //setting have validate
        var customLink = $('#custom_link').prop('checked');
        var pollLimit = $('#set_limit').prop('checked');
        var setPassword = $('#set_password').prop('checked');

        if (customLink) {
            $("#link-poll").show("slow");
        } else {
            $("#link-poll").hide("slow");
        }

        if (pollLimit) {
            $("#poll-limit").show("slow");
        } else {
            $("#poll-limit").hide("slow");
        }

        if (setPassword) {
            $("#password-poll").show("slow");
        } else {
            $("#password-poll").hide("slow");
        }
    }

    $("#search-text").click(function(){
        $("#form-search").slideToggle("slow");
    });
});


var rand = function() {
    return Math.random().toString(36).substr(2); // remove `0.`
};

function setButtonWavesEffect(event) {
    $(event.currentTarget).find('[role="menu"] li a').removeClass('waves-effect');
    $(event.currentTarget).find('[role="menu"] li:not(.disabled) a').addClass('waves-effect');
}

function confirmDelete(message) {
    return confirm(message);
}

/**
 *
 * change status of poll: open <---> close
 *
 * @param pollId
 */
function changeStatusOfPoll(pollId) {
    var route = $('.hide').data("route");
    var token = $('.hide').data("token");
    var statusOpening = $('.hide').data("statusOpen");
    $.ajax({
        url: route,
        type: 'post',
        data: {
            'id': pollId,
            '_token': token,
        },
        success: function (data) {
            if (data.success) {
                $('#status_' + pollId).html(data.status);
                if (data.status === statusOpening) {
                    $('#btn_' + pollId).html("<i class='material-icons'>lock</i>");
                    $('#btn_' + pollId).attr("data-original-title", $('.hide').data("tooltipClose"));
                } else {
                    $('#btn_' + pollId).html("<i class='material-icons'>lock_open</i>");
                    $('#btn_' + pollId).attr("data-original-title", $('.hide').data("tooltipOpen"));
                }
            }
        }
    });
}

//show advance setting: custom link, set limit, set password
 function settingAdvance(key) {
     var dataCreatePoll = $('.hide').data("poll");
        console.log(dataCreatePoll);
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
