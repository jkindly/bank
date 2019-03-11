(function($) {

    let hrefLength = document.location.href.split('/').length;
    let currentPath = document.location.href.split('/');

    if (hrefLength > 4) {
        $('.nav-item a[href="/'+currentPath[3]+'/'+currentPath[4]+'"]').parent().addClass('nav-item-underscore');
    } else {
        $('.nav-item a[href="/'+currentPath[3]+'"]').parent().addClass('nav-item-underscore');
    }

}(jQuery));