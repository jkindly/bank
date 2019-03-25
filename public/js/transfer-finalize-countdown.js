(function($) {

    $.ajax({
        url: '/transfer/domestic/finalize',
        dataType: 'json',
        async: true,
        cache: false,
        success: function(data) {
            remainTime(data);
        }
    });

    function remainTime(time) {
        setInterval(function () {
            let remainingTime = $('.transfer-finalize-remaining-time');
            let progressBar = $('.transfer-progress-bar-progress')
            let difference = 100-time;
            if (difference <= 0) {
                clearInterval();
                // window.location.replace("http://localhost:8000/transfer/domestic");
                location.reload();
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