$(document).ready(function() {
    // SideNav Button Initialization
    $('.button-collapse').sideNav({
        edge: 'left',
        closeOnClick: false,
        breakpoint: 1440,
        menuWidth: 270,
        timeDurationOpen: 300,
        timeDurationClose: 200,
        timeDurationOverlayOpen: 50,
        timeDurationOverlayClose: 200,
        easingOpen: 'easeOutQuad',
        easingClose: 'easeOutCubic',
        showOverlay: true,
        showCloseButton: false
    });

    // SideNav Scrollbar Initialization
    var sideNavScrollbar = document.querySelector('.custom-scrollbar');
    var ps = new PerfectScrollbar(sideNavScrollbar);

    // Tooltip Initialization
    $('[data-toggle="tooltip"]').tooltip({
        template: '<div class="tooltip md-tooltip"><div class="tooltip-arrow md-arrow"></div><div class="tooltip-inner md-inner"></div></div>'
    });

    // Material select
    $('.mdb-select').materialSelect();
    $('.select-wrapper.md-form.md-outline input.select-dropdown').bind('focus blur', function () {
        $(this).closest('.select-outline').find('label').toggleClass('active');
        $(this).closest('.select-outline').find('.caret').toggleClass('active');
    });

    //  Notification
    let options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "md-toast-top-left",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "400",
        "hideDuration": "1500",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    $('.toast').toast(options)

    // Gestion des notification serveur
    flashes($('.notify'));

    // Animation init
    new WOW().init();
});

function passwordView(element) {
    if (element.hasClass('fa-eye')) {
        element.removeClass('fa-eye').addClass('fa-eye-slash view');

        element.siblings('input').get(0).type = 'text';
    } else {
        element.removeClass('fa-eye-slash view').addClass('fa-eye');

        element.siblings('input').get(0).type = 'password';
    }
}

function generatePassword($elements) {
    showLoading();

    let $request = new Request('https://api.motdepasse.xyz/create/?include_digits&include_lowercase&include_uppercase&password_length=8&quantity=1');

    fetch($request)
        .then((response) => response.json())
        .then(function(json_response) {
            json_response.passwords.forEach((password) => {
                $.each($elements, function(index, element){
                    $(element).val(password);
                })

                hideLoading();
            });
        });
}

function flashes (selector) {
    selector.each(function (index, element) {
        if ($(element).html() !== undefined) {
            toastr[$(element).attr('app-data')]($(element).html());
        }
    })
}

function simpleModals(element, route, elementRacine) {
    element.click(function (e) {
        e.preventDefault();

        showLoading();

        let $id = $(this).attr('id'), $modal = '#confirm'+$id;

        $.ajax({
            url: Routing.generate(route, {id: $id}),
            type: 'GET',
            success: function(data) {
                hideLoading();

                $(elementRacine).html(data.html);
                $($modal).modal()
            }
        });
    });
}

function bulkModals(element, container, route, elementRacine) {
    element.click(function (e) {
        e.preventDefault();

        showLoading();

        let ids = [];

        container.find('.list-checkbook').each(function () {
            if ($(this).prop('checked')) {
                ids.push($(this).val());
            }
        });

        console.log(JSON.stringify(ids))

        if (ids.length) {
            let $modal = '#confirmMulti'+ids.length;

            $.ajax({
                url: Routing.generate(route),
                data: {'data': JSON.stringify(ids)},
                type: 'GET',
                success: function(data) {
                    hideLoading();

                   $(elementRacine).html(data.html);
                   $($modal).modal();
                },
            });
        }
    });
}

function showLoading() {
    $('body .loader').show();
}

function hideLoading() {
    $('body .loader').hide();
}

function notification(titre, message, options, type) {
    if(typeof options == 'undefined') options = {};
    if(typeof type == 'undefined') type = "info";

    toastr[type](message, titre, options);
}



