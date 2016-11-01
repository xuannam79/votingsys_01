$(document).ready(function(){

    $('.btn-link-user').click(function(e) {
        e.preventDefault();
        var link = $('.hide').data('link');
        window.location.href = link + '/' + $('.token-user').val();
    });

    $('.btn-link-admin').click(function(e) {
        e.preventDefault();
        var link = $('.hide').data('link');
        window.location.href = link + '/' + $('.token-admin').val();
    });
});
