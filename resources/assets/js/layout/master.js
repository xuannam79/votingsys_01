$(document).ready(function () {
    $('.loader').hide();

    /* multiple language */
    $("#countries").msDropdown();

    //Auto close message
    $(".alert-dismissable").delay(5000).fadeOut(1000);

    $('[data-toggle="tooltip"]').tooltip();
});

var loadFile = function(event) {
    var output = document.getElementById('output');
    output.src = URL.createObjectURL(event.target.files[0]);
};

var _validFileExtensions = [".jpg", ".jpeg", ".bmp", ".gif", ".png"];

/**
 *
 * View picture when choose image
 *
 * @param input
 * @param idShow
 */
function readURLRegister(input, idShow) {
    if (ValidateSingleInputRegister(input.files[0].name)) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#' + idShow).show().attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
    } else {
        $('#' + idShow).hide();
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
function ValidateSingleInputRegister(oInput) {
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
            $('.error-avatar').addClass('has-error')
                .html('<span id="title-error" class="help-block">' + $('.hide-validate').data('errorAvatar') + '</span>');
            $('#btn-register').attr('disabled', true)

            return false;
        }
    }

    $('.error-avatar').removeClass('has-error').html('');
    $('#btn-register').removeAttr('disabled');

    return true;
}

function showToolTipOptionContent(path, name) {
    if (path == '') {
        $(".chart-detail-image").remove();
    } else {
        $(".chart-detail-image").attr("src", path);
    }

    $(".chart-detail-name").html(name);
    $('#myModalChart').modal('toggle');
}

/**
 *
 * auto copy link
 *
 * @param element
 * @param link
 */
function copyToClipboard(element, link) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(link).select();
    document.execCommand("copy");
    $temp.remove();
}
