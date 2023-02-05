$(document).ready(function() {

    // Time js
    const terms = [
        { time: 45, divide: 60, text: "moins d'une minute" },
        { time: 90, divide: 60, text: 'environ une minute' },
        { time: 45 * 60, divide: 60, text: '%d minutes' },
        { time: 90 * 60, divide: 60 * 60, text: 'environ une heure' },
        { time: 24 * 60 * 60, divide: 60 * 60, text: '%d heures' },
        { time: 42 * 60 * 60, divide: 24 * 60 * 60, text: 'environ un jour' },
        { time: 30 * 24 * 60 * 60, divide: 24 * 60 * 60, text: '%d jours' },
        { time: 45 * 24 * 60 * 60, divide: 24 * 60 * 60 * 30, text: 'environ un mois' },
        { time: 365 * 24 * 60 * 60, divide: 24 * 60 * 60 * 30, text: '%d mois' },
        { time: 365 * 1.5 * 24 * 60 * 60, divide: 24 * 60 * 60 * 365, text: 'environ un an' },
        { time: Infinity, divide: 24 * 60 * 60 * 365, text: '%d ans' }
    ];

    let $dataTime = $('[data-time]');

    $dataTime.each(function (index, element) {
        const timestamp = parseInt(element.getAttribute('data-time'), 10) * 1000;
        const date = new Date(timestamp);

        updateText(date, element, terms);
    });

    // Gestion des checkbox dans la liste
    let $principal_checkbox = $('#principal-checkbox'),
        $list_checkbook = $('.list-checkbook'),
        $list_checkbook_length = $list_checkbook.length,
        $list_checkbook_number = 0,
        $btn_bulk_delete = $('#entity-list-delete-bulk-btn'),
        $btn_class_bulk_delete = $('.entity-list-delete-bulk-btn');

    $principal_checkbox.on('click', function () {
        let $this = $(this);

        if ($this.prop('checked')) {
            $('.list-checkbook').prop('checked', true);

            $list_checkbook_number = $list_checkbook_length;

            if ($list_checkbook_length > 0) {
                $btn_bulk_delete.removeClass('d-none');
                $btn_class_bulk_delete.removeClass('d-none');
            }

        } else {
            $('.list-checkbook').prop('checked', false);
            $btn_bulk_delete.addClass('d-none');
            $btn_class_bulk_delete.addClass('d-none');

            $list_checkbook_number = 0;
        }
    });

    $list_checkbook.on('click', function () {
        let $this = $(this);

        if ($this.prop('checked')) {
            $list_checkbook_number++;
            $btn_bulk_delete.removeClass('d-none');
            $btn_class_bulk_delete.removeClass('d-none');

            if ($list_checkbook_number === $list_checkbook_length)
                $principal_checkbox.prop('checked', true)
        } else {
            $list_checkbook_number--;

            if ($list_checkbook_number === 0) {
                $btn_bulk_delete.addClass('d-none');
                $btn_class_bulk_delete.addClass('d-none');
            }

            if ($list_checkbook_number < $list_checkbook_length)
                $principal_checkbox.prop('checked', false)
        }
    });

    let $container = $('#modal-container'),
        $checkbook_container = $('#list-checkbook-container');

    // Administrateur
    simpleModals($('.entity-admin-delete'), 'app_admin_admin_delete', $container);
    bulkModals($('.entity-admin-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_admin_bulk_delete', $container);

    // Booking
    simpleModals($('.entity-booking-delete'), 'app_admin_booking_delete', $container);
    bulkModals($('.entity-booking-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_booking_bulk_delete', $container);

    // Category
    simpleModals($('.entity-category-delete'), 'app_admin_category_delete', $container);
    bulkModals($('.entity-category-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_category_bulk_delete', $container);

    // City
    simpleModals($('.entity-city-delete'), 'app_admin_city_delete', $container);
    bulkModals($('.entity-city-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_city_bulk_delete', $container);

    // Commande
    simpleModals($('.entity-order-delete-bulk-btn'), 'app_admin_commande_delete', $container);
    bulkModals($('.entity-order-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_commande_bulk_delete', $container);

    // Currency
    simpleModals($('.entity-currency-delete'), 'app_admin_currency_delete', $container);
    bulkModals($('.entity-currency-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_currency_bulk_delete', $container);

    // Discount
    simpleModals($('.entity-discount-delete'), 'app_admin_discount_delete', $container);
    bulkModals($('.entity-discount-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_discount_bulk_delete', $container);

    // Emailing
    simpleModals($('.entity-emailing-delete'), 'app_admin_emailing_delete', $container);
    bulkModals($('.entity-emailing-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_emailing_bulk_delete', $container);

    // Equipment
    simpleModals($('.entity-equipment-delete'), 'app_admin_equipment_delete', $container);
    bulkModals($('.entity-equipment-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_equipment_bulk_delete', $container);

    // Equipment group
    simpleModals($('.entity-equipment-group-delete'), 'app_admin_equipment_group_delete', $container);
    bulkModals($('.entity-equipment-group-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_equipment_group_bulk_delete', $container);

    // Exchange Rate
    simpleModals($('.entity-exchange-delete'), 'app_admin_exchange_rate_delete', $container);
    bulkModals($('.entity-exchange-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_exchange_rate_bulk_delete', $container);

    // Hostel
    simpleModals($('.entity-hostel-delete'), 'app_admin_hostel_delete', $container);
    bulkModals($('.entity-hostel-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_hostel_bulk_delete', $container);

    // Locale
    simpleModals($('.entity-locale-delete'), 'app_admin_locale_delete', $container);
    bulkModals($('.entity-locale-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_locale_bulk_delete', $container);

    // Partner
    simpleModals($('.entity-partner-delete'), 'app_admin_partner_delete', $container);
    bulkModals($('.entity-partner-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_partner_bulk_delete', $container);

    // Payment
    simpleModals($('.entity-payment-delete'), 'app_admin_payment_delete', $container);
    bulkModals($('.entity-payment-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_payment_bulk_delete', $container);

    // Payout
    simpleModals($('.entity-payout-pay'), 'app_admin_payout_pay', $container);
    simpleModals($('.entity-payout-cancel'), 'app_admin_payout_cancel', $container);
    $('#app-vendor-payout').click(function (e) {
        e.preventDefault();

        showLoading();

        $.ajax({
            url: Routing.generate('app_admin_payout_bulk_pay'),
            type: 'GET',
            success: function(data) {
                hideLoading();

                if (data === 0) {
                    notification('', 'Aucun paiement disponible')
                } else {
                    $($container).html(data.html);
                    $('#confirmPayoutModal').modal();
                }
            },
        });
    });

    //bulkModals($('#app-vendor-payout'), $checkbook_container, 'app_admin_payout_bulk_pay', $container);

    // Payout
    simpleModals($('.entity-cancel-payout-pay'), 'app_admin_cancel_payout_pay', $container);
    simpleModals($('.entity-cancel-payout-cancel'), 'app_admin_cancel_payout_cancel', $container);
    $('#app-vendor-cancel-payout').click(function (e) {
        e.preventDefault();

        showLoading();

        $.ajax({
            url: Routing.generate('app_admin_cancel_payout_bulk_pay'),
            type: 'GET',
            success: function(data) {
                hideLoading();

                if (data === 0) {
                    notification('', 'Aucun remboursement disponible')
                } else {
                    $($container).html(data.html);
                    $('#confirmPayoutModal').modal();
                }
            },
        });
    });

    // Plan
    simpleModals($('.entity-plan-delete'), 'app_admin_plan_delete', $container);
    bulkModals($('.entity-plan-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_plan_bulk_delete', $container);

    // Promotion
    simpleModals($('.entity-promotion-delete'), 'app_admin_promotion_delete', $container);
    bulkModals($('.entity-promotion-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_promotion_bulk_delete', $container);

    // Review
    simpleModals($('.entity-review-delete'), 'app_admin_review_delete', $container);
    bulkModals($('.entity-review-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_review_bulk_delete', $container);

    // Room
    simpleModals($('.entity-room-delete'), 'app_admin_room_delete', $container);
    bulkModals($('.entity-room-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_room_bulk_delete', $container);

    // Room Equipment
    simpleModals($('.entity-roomEquipment-delete'), 'app_admin_room_equipment_delete', $container);
    bulkModals($('.entity-roomEquipment-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_room_equipment_bulk_delete', $container);

    // Room Equipment Group
    simpleModals($('.entity-room-equipment-group-delete'), 'app_admin_room_equipment_group_delete', $container);
    bulkModals($('.entity-room-equipment-group-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_room_equipment_group_bulk_delete', $container);

    // User
    simpleModals($('.entity-user-delete'), 'app_admin_user_delete', $container);
    bulkModals($('.entity-user-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_user_bulk_delete', $container);

    // User
    simpleModals($('.entity-banner-delete'), 'app_admin_banner_delete', $container);
    bulkModals($('.entity-banner-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_admin_banner_bulk_delete', $container);

    // Notification
    if (window.hostel.USER) {
        setInterval(function(){
            getNotification($('#notification-bulk'), 'app_notification_unread');
        }, 180000);

        $('.skin-light .dropdown.notification').on('show.bs.dropdown', function () {
            readAll('app_notification_read');
        });
    }
});

function getNotification(container, route) {
    $.ajax({
        url: Routing.generate(route),
        type: 'GET',
        success: function(data) {
            let $result = $.parseJSON(data);

            if ($result.length) {
                $('.skin-light .dropdown.notification .dropdown-menu .not-notification-bulk').addClass('d-none');
                $('.skin-light .dropdown.notification > .icon').removeClass('d-none')

                $.each($result, function(index, element) {
                    container.prepend(notificationItemView(element))
                });
            }
        },
    });
}

function readAll(route) {
    $.ajax({
        url: Routing.generate(route),
        type: 'GET',
        success: function() {
            $('.skin-light .dropdown.notification > .icon').addClass('d-none');
        },
    });
}

function notificationItemView(notification) {
    return $('<a class="dropdown-item d-flex" href="' + notification.url + '">' +
        '<div class="content">' +
        '<div class="data">' + notification.message + '</div>' +
        '<div class="time">' + jsDateFormater(new Date(notification.createdAt)) + '</div>' +
        '</div>' +
        '<div class="icon-notification ml-auto d-flex align-items-center pl-3"><i class="fas fa-circle"></i></div>' +
        '</a>');
}

function jsDateFormater(date) {
    const seconds = (new Date().getTime() - date.getTime()) / 1000;
    let term = null;

    for (term of terms) {
        if (Math.abs(seconds) < term.time) {
            break
        }
    }

    if (seconds >= 0) {
        return `Il y a ${term.text.replace('%d', Math.round(seconds / term.divide))}`;
    } else {
        return `Dans ${term.text.replace('%d', Math.round(seconds / term.divide))}`;
    }
}

function updateText(date, element, terms) {
    const seconds = (new Date().getTime() - date.getTime()) / 1000;
    let term = null;
    const prefix = element.getAttribute('prefix');

    for (term of terms) {
        if (Math.abs(seconds) < term.time) {
            break
        }
    }

    if (seconds >= 0) {
        element.innerHTML = `${prefix || 'Il y a'} ${term.text.replace('%d', Math.round(seconds / term.divide))}`
    } else {
        element.innerHTML = `${prefix || 'Dans'} ${term.text.replace('%d', Math.round(Math.abs(seconds) / term.divide))}`
    }
}





