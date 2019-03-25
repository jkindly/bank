(function($) {

    // let hrefLength = document.location.href.split('/').length;
    let currentPath = document.location.href.split('/');

    $('.nav-item a[href="/'+currentPath[3]+'"]').parent().addClass('nav-item-underscore');

    $.getScript('transfer-finalize-countdown.js', function() {

    });

}(jQuery));