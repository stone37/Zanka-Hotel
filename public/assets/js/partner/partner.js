$(document).ready(function() {
    let $partner_form_card = $('.partner-form .card'),
        $partner_radio = $('.partner-form .card .form-check.data');

    $partner_form_card.click(function () {
        $partner_form_card.removeClass('active');
        $(this).addClass('active');
    });
    
    $('.partner-form .card input').click(function () {
        $partner_form_card.removeClass('active');
        $(this).parents('.card').addClass('active');
    });

    $partner_radio.click(function () {
        let $this = $(this);

        $partner_radio.removeClass('active');

        $this.find('input[type="radio"]').prop('checked', true);
        $this.addClass('active');
    });




    // Gestion des checkbox dans la liste
    let $principalCheckbox = $('#principal-checkbox'),
        $listCheckbook = $('.list-checkbook'),
        $listCheckbookLength = $listCheckbook.length,
        $listCheckbookNumber = 0,
        $btnBulkDelete = $('#entity-list-delete-bulk-btn'),
        $btnClassBulkDelete = $('.entity-list-delete-bulk-btn');

    $principalCheckbox.on('click', function () {
        let $this = $(this);

        if ($this.prop('checked')) {
            $('.list-checkbook').prop('checked', true);

            $listCheckbookNumber = $listCheckbookLength;

            if ($listCheckbookLength > 0) {
                $btnBulkDelete.removeClass('d-none');
                $btnClassBulkDelete.removeClass('d-none');
            }

        } else {
            $('.list-checkbook').prop('checked', false);
            $btnBulkDelete.addClass('d-none');
            $btnClassBulkDelete.addClass('d-none');

            $listCheckbookNumber = 0;
        }
    });

    $listCheckbook.on('click', function () {
        let $this = $(this);

        if ($this.prop('checked')) {
            $listCheckbookNumber++;
            $btnBulkDelete.removeClass('d-none');
            $btnClassBulkDelete.removeClass('d-none');

            if ($listCheckbookNumber === $listCheckbookLength)
                $principalCheckbox.prop('checked', true)
        } else {
            $listCheckbookNumber--;

            if ($listCheckbookNumber === 0) {
                $btnBulkDelete.addClass('d-none');
                $btnClassBulkDelete.addClass('d-none');
            }

            if ($listCheckbookNumber < $listCheckbookLength)
                $principalCheckbox.prop('checked', false)
        }
    });

    let $container = $('#modal-container'),
        $checkbook_container = $('#list-checkbook-container');

    // Booking
    simpleModals($('.entity-booking-confirm'), 'app_partner_booking_confirmed', $container);
    bulkModals($('.entity-booking-confirm-bulk-btn a.btn-success'), $checkbook_container, 'app_partner_booking_bulk_confirmed', $container);

    simpleModals($('.entity-booking-cancel'), 'app_partner_booking_cancelled', $container);
    bulkModals($('.entity-booking-cancel-bulk-btn a.btn-danger'), $checkbook_container, 'app_partner_booking_bulk_cancelled', $container);

    // Hostel
    simpleModals($('.entity-hostel-delete'), 'app_partner_hostel_delete', $container);

    // Hostel Gallery
    simpleModals($('.entity-gallery-delete'), 'app_partner_hostel_gallery_delete', $container);
    bulkModals($('.entity-gallery-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_partner_hostel_gallery_bulk_delete', $container);

    // Promotion
    simpleModals($('.entity-promotion-delete'), 'app_partner_promotion_delete', $container);
    bulkModals($('.entity-promotion-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_partner_promotion_bulk_delete', $container);

    // Room
    simpleModals($('.entity-room-delete'), 'app_partner_room_delete', $container);
    bulkModals($('.entity-room-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_partner_room_bulk_delete', $container);

    // Room Gallery
    simpleModals($('.entity-room-gallery-delete'), 'app_partner_room_gallery_delete', $container);
    bulkModals($('.entity-room-gallery-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_partner_room_gallery_bulk_delete', $container);

    // Supplement
    simpleModals($('.entity-supplement-delete'), 'app_partner_supplement_delete', $container);
    bulkModals($('.entity-supplement-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_partner_supplement_bulk_delete', $container);

    // Taxe
    simpleModals($('.entity-taxe-delete'), 'app_partner_taxe_delete', $container);
    bulkModals($('.entity-taxe-delete-bulk-btn a.btn-danger'), $checkbook_container, 'app_partner_taxe_bulk_delete', $container);

    // Room bedding
    let $wrapper = $('#room-form-bedding-wrapper'),
        $beg_add_btn = $('#add_new_beg'),
        $beg_state = parseInt($wrapper.data('index'));

    if ($wrapper.length) {
        if ($beg_state === 0) {
            addBedFormToCollection($wrapper);
        }
    }

    $beg_add_btn.click(function(e) {
        e.preventDefault();

        addBedFormToCollection($wrapper);
    });

    $wrapper.on('click', '.delete_beg', function(e) {
       e.preventDefault();

       let $this = $(this);

        $this.closest('#room_beddings_'+ $this.attr('id')).fadeOut().remove();
        $wrapper.data('index', $wrapper.data('index') - 1);
    });

    $('a.delete_beg').click(function(e) {
        e.preventDefault();

        let $this = $(this), $parent = $this.parents('.bed-data-row');

        $parent.fadeOut().remove();
        $wrapper.data('index', $wrapper.data('index') - 1);
    });

    // Room sub info && name
    let $room_sub_info = $('.room-sub-info'),
        $room_type_select = $('select.app-room-type'),
        $room_specification_select = $('select.app-room-specification'),
        $room_feature_select = $('select.app-room-feature'),
        $room_amenities_select = $('select.app-room-amenities'),
        $room_name_value_type = $('#app-room-name .type'),
        $room_name_value_specification = $('#app-room-name .specification'),
        $room_name_value_feature = $('#app-room-name .feature'),
        $room_name_value_amenities = $('#app-room-name .amenities');

    $room_type_select.on('change', function () {
        let $this = $(this);

        $room_name_value_type.text($this.val());

        if ($this.val() && ($this.val() === 'Suite' || $this.val() === 'Appartement')) {
            $room_sub_info.show();
        } else {
            $room_sub_info.hide();
        }
    });

    $room_specification_select.on('change', function () {
        $room_name_value_specification.text($(this).val());
    });

    $room_feature_select.on('change', function () {
        $room_name_value_feature.text(' - '+$(this).val());
    });

    $room_amenities_select.on('change', function () {
        $room_name_value_amenities.text($(this).val().toLowerCase());
    });

    // Profil photo update
    $('#entity-image').change(function () {readUrl(this)});

    let  $promotion_action_type_select = $('select.app-promotion-action-type'),
         $promotion_action_amount = $('.promotion-action-amount .input-group .input-group-append .input-group-text'),
         $promotion_action_amount_title = $('.promotion-action-amount .title');

    if ($promotion_action_type_select.length > 0) {
        if ($promotion_action_type_select.val() === 'fixed_discount') {
            $promotion_action_amount_title.text('Montant');
        } else {
            $promotion_action_amount_title.text('Pourcentage');
        }
    }

    $promotion_action_type_select.on('change', function () {
        let $this = $(this);

        if ($this.val() === 'fixed_discount') {
            $promotion_action_amount.text('XOF');
            $this.attr('placeholder', 'Montant');
            $promotion_action_amount_title.text('Montant');
        } else {
            $promotion_action_amount.text('%');
            $promotion_action_amount_title.text('Pourcentage');
        }
    })
});

const addBedFormToCollection = (container) => {

    let index = container.data('index'),
        prototype = container.data('prototype');

    container.append(prototype.replace(/__name__/g, index));

    let $bedding_content =  $('#room_beddings_'+index),
        $current_first_child = $('#room_beddings_'+index+' > div:nth-child(1)'),
        $current_second_child = $('#room_beddings_'+index+' > div:nth-child(2)'),
        $select = $bedding_content.find('.mdb-select'),
        $beg_delete_btn = $('<a id="'+index+'" class="delete_beg btn-floating btn-danger btn-sm"><i class="fas fa-trash"></i></a>');

    $bedding_content.addClass('form-row');
    $current_first_child.addClass('col-12 col-md-4');
    $current_second_child.addClass('col-12 col-md-3');

    if (index > 0) {
        $bedding_content.append($beg_delete_btn);
    }

    $select.materialSelect();

    container.data('index', index + 1);
}

const readUrl = (input) => {
    let url = input.value,
        ext = url.substring(url.lastIndexOf('.')+1).toLowerCase();

    if (input.files && input.files[0] && (ext === 'gif' || ext === 'png' || ext === 'jpeg' || ext === 'jpg')) {
        let reader = new FileReader();

        reader.onload = function (e) {
            $('#image-view').attr('src', e.target.result).removeClass('d-none');
        };

        reader.readAsDataURL(input.files[0])
    }
}






