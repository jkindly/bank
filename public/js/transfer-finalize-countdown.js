(function($) {

    let time;

    $.ajax({
        url: '/transfer/domestic/finalize',
        dataType: 'json',
        async: true,
        cache: false,
        success: function(data) {
            time = data;
            remainTime(time);
        }
    });

    function remainTime(time) {
        setInterval(function () {
            let remainingTime = $('.transfer-finalize-remaining-time');
            let progressBar = $('.transfer-progress-bar-progress')
            let difference = 100-time;
            if (difference <= 0) {
                clearInterval();
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
                progressBar.css('width', '100%');
                remainingTime.html('Pozostały czas: <span style="color:red;">0 sekund</span>');
            } else if (difference <= 10) {
                remainingTime.html('Pozostały czas: ' + '<span style="color:red;">' + difference + ' sekund</span>');
                progressBar.css('width', time +'%');
            } else {
                remainingTime.html('Pozostały czas: ' + difference + ' sekund');
                progressBar.css('width', time +'%');
            }
            time++;
        }, 1000)
    }
}(jQuery));