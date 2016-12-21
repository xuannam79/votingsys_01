$(document).ready(function () {
    $('.loader').hide();

    /* multiple language */
    $("#countries").msDropdown();

    //Auto close message
    $(".alert-dismissable").delay(5000).fadeOut(1000);
});
