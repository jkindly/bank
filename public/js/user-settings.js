(function($) {
    let loading = $('.loading');

    $('.settings-user-data-bar').click(function() {
        $.ajax({
            url: '/settings/ajaxUserData',
            dataType: 'json',
            async: true,
            cache: false,
            beforeSend: function() {
                $(this).html('');
                loading.show();
            },
            success: function(data) {
                $('.user-settings-content').html(data);
            },
            complete: function() {
                loading.hide();
            }
        });
    });

}(jQuery));