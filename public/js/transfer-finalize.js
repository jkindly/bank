(function($) {

    console.log('elo');

    console.log($('form').serialize());

    $('.send-transfer-btn').click(function(e) {
        e.preventDefault();
        $.ajax({
            url: '/transfer/domestic',
            type: 'POST',
            data: $('form').serialize(),
            dataType: 'json',
            async: true,
            cache: false,
            success: function(data) {
                console.log(data);
            }
        });
    });






}(jQuery));