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
            let difference = 60-time;
            if (difference <= 0) {
                clearInterval();
                window.location.replace("http://localhost:8000/transfer/domestic");
                $('.transfer-finalize-remaining-time').html('Pozostały czas: <span style="color:red;">0 sekund</span>');
            } else if (difference <= 10) {
                $('.transfer-finalize-remaining-time').html('Pozostały czas: ' + '<span style="color:red;">' + difference + ' sekund</span>');
            } else {
                $('.transfer-finalize-remaining-time').html('Pozostały czas: ' + difference + ' sekund');
            }
            time++;
        }, 1000)
    }
}(jQuery));