(function($) {
    let loading = $('.loading');
    let content = $('.user-settings-content');
    let settingsName;

    $('.user-settings-option').click(function() {
        let clickedBarId = $(this).attr('id');
        switch (clickedBarId) {
            case 'settings-user-data-bar':
                settingsName = 'user_data';
                break;
            case 'settings-security-bar':
                settingsName = 'security';
                break;
        }
        $.ajax({
            url: '/settings/ajaxSettingsContent',
            dataType: 'json',
            type: 'POST',
            data: {settingsName: settingsName},
            async: true,
            cache: false,
            beforeSend: function() {
                content.html('');
                loading.show();
            },
            success: function(data) {
                content.html(data);
            },
            complete: function() {
                loading.hide();
            }
        });
    });


    content.on('click', '.edit-user-data-btn', function() {
        let id = $(this).parent().parent().attr('id');
        if (id === undefined) return;
        console.log(id);
        $.ajax({
            url: '/settings/ajaxChange/'+id,
            dataType: 'json',
            async: true,
            cache: false,
            beforeSend: function() {
                content.html('');
                loading.show();
            },
            success: function(data) {
                content.html(data);
            },
            error: function() {
                content.html('Wystąpił błąd');
            },
            complete: function() {
                loading.hide();
            }
        });
    });

}(jQuery));