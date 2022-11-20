$(document).ready(function() {
    // Tooltip Initialization
    $('[data-toggle="tooltip"]').tooltip({
        template: '<div class="tooltip md-tooltip"><div class="tooltip-arrow md-arrow"></div><div class="tooltip-inner md-inner"></div></div>'
    });

    // Dropdown
    let $dropdown = $('.dropdown');

    $dropdown.on('hide.bs.dropdown', function() {
        $(this).removeClass('active')
        $(this).find('.fas.fa-angle-down').removeClass('rotate-180');
    });

    $dropdown.on('shown.bs.dropdown', function() {
        $(this).addClass('active')
        $(this).find('.fas.fa-angle-down').addClass('rotate-180');
    });

    // Carousel
    $('.carousel .carousel-inner.vv-3 .carousel-item').each(function () {
        var next = $(this).next();

        if (!next.length) {
            next = $(this).siblings(':first');
        }

        next.children(':first-child').clone().appendTo($(this));

        for (var i = 0; i < 4; i++) {

            next = next.next();

            if (!next.length) {
                next = $(this).siblings(':first');
            }

            next.children(':first-child').clone().appendTo($(this));
        }

        $('.carousel').carousel('cycle');
    });

    // Navbar mobile
    let $icon_bulk = $('.skin-light .navbar .navbar-toggler .button-icon');

    $('.skin-light .navbar .navbar-toggler').on('click', function () {

        if ($icon_bulk.hasClass('open')) {
            $icon_bulk.find('i').removeClass('fa-times').addClass('fa-bars');
            $('html, body').removeClass('stop-scroll');
        } else {
            $icon_bulk.find('i').removeClass('fa-bars').addClass('fa-times');
            $('html, body').addClass('stop-scroll');
        }

        $('.skin-light .navbar .navbar-toggler .button-icon').toggleClass('open');
    });

    $container = $('#modal-container');

    // Locale modal
    navbarModal($('#navbar-locale-menu-link'), 'app_locale_get', 'navbar-locale-menu-modal', $container);

    // Currency modal
    navbarModal($('#navbar-currency-menu-link'), 'app_currency_get', 'navbar-currency-menu-modal', $container);

    // Ferme la bannière du haut
    $('.skin-light .alert.banner .close').click(function(e) {
        $.ajax({
            type: 'POST',
            url: Routing.generate('app_switch_banner'),
            data: {'id': $(this).attr('data-id')},
            success: function(data) {
                $(this).hide();
            }
        });
    });

    // Newsletter
    newsletter($('#newsletter-form'));

    // Password view
    $('.input-prefix.fa-eye').click(function () {
        passwordView($(this));
    });
});




