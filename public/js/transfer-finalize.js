(function($) {

    $('.cancel-transfer-btn').click(function() {
        $.ajax({
            url: '/transfer/domestic/ajaxDeclineDomesticTransfer',
            type: 'POST',
            async: true,
            cache: false,
            success: function(data) {
                if (data === 'transfer_declined') {
                    location.reload();
                }
            }
        });
    });

}(jQuery));