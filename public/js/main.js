$(document).ready(function() {

    $('.nav-item').on('click', function() {
        let navItem = $(this);
       if (!navItem.hasClass('nav-item-underscore')) {
           $('.nav-item').removeClass('nav-item-underscore');
           navItem.addClass('nav-item-underscore');
       }
    });


    $('.user-account').on('click', function() {
        let account = $(this);
        if (account.attr()) {
            $('.user-account').animate({
                height: '60px'
            }, 500);
            account.animate({
                height: '200px'
            }, 500);
        }
    });

});