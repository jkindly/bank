(function($) {
    let loading = $('.loading');
    let header = $('.user-settings-content-header');
    let content = $('.user-settings-content');
    let settingsName;

    $.fn.serializeObject = function()
    {
        let o = {};
        let a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };

    let headers = {
        'user-data' : '<i class="fas fa-user-edit"></i>Twoje Dane',
        'security' : '<i class="fas fa-lock"></i>Bezpieczeństwo'
    };

    // LOADING USER INFORMATION BY CHOOSING LEFT SETTINGS OPTION
    $('.user-settings-option').click(function() {
        let clickedBarId = $(this).attr('id');
        switch (clickedBarId) {
            case 'settings-user-data-bar':
                settingsName = 'user-data';
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
                header.html(headers[settingsName]);
                content.html(data);
            },
            complete: function() {
                loading.hide();
            }
        });
    });


    // LOADING FORM TO EDIT USER INFORMATIONS
    content.on('click', '.edit-user-data-btn', function() {
        let id = $(this).parent().parent().attr('id');
        if (id === undefined) return;
        console.log(id);
        $.ajax({
            url: '/settings/ajaxLoadForm/'+id,
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

    // VALIDATING FORM
    content.on('click', '#edit-user-address-btn', function(e) {
        e.preventDefault();
        let form = $('#edit-user-address-form').serializeObject();

        $.ajax({
            url: '/settings/ajaxValidateForm/user-address',
            dataType: 'json',
            method: 'POST',
            data: form,
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

    content.on('click', '.verification-code-btn', function(e) {
        e.preventDefault();
        let form = $('#verification-code-form').serializeObject();
        $.ajax({
            url: '/settings/ajaxUpdate/user-address',
            dataType: 'json',
            method: 'POST',
            data: form,
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