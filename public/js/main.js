(function($) {

    //underscoring menu items with blue bar
    console.log(window.location.pathname);
    let currentPath = window.location.pathname;
    $('.nav-item a[href="'+currentPath+'"]').parent().addClass('nav-item-underscore');

}(jQuery));