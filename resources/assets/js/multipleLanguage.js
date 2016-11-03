$(document).ready(function(){

    $('.btn-multiple-language').change(function(e) {
        e.preventDefault();
        divChangeAmount = $(this).parent();
        var route = divChangeAmount.data('route');
        var lang = $('.btn-multiple-language').val();

        $.ajax({
            type: 'POST',
            url: route,
            dataType: 'JSON',
            data: {
                'lang': lang,
            },
            success: function(data){
                if (data.success) {
                    window.location.href = data.url_back;
                }
            }
        });
    });
});
