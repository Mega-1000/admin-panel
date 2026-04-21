$( document ).ready(function() {
    let employeeSelect = $('#employee');
    let statusSelect = $("#status");

    employeeSelect.on('change', function() {
        $('#status').val("17").change();
    });

    checkEmployeeIfEmpty(employeeSelect);

    if (statusSelect.val() == "5") {
        checkFieldsForValidateMarking();
    }

    statusSelect.on('change', function() {
        if (this.value == "5") {
            checkFieldsForValidateMarking();
        } else {
            $(".is-empty-info").removeClass("is-empty-info");
            checkEmployeeIfEmpty(employeeSelect);
        }
    });

    $('#order_invoice_address_nip').val($('#order_invoice_address_nip').val().replace(/[^a-zA-Z0-9]+/g, ''));

    $("#order_invoice_address_nip, #order_invoice_address_phone, #order_delivery_address_phone").on('keyup', function () {
        $(this).val($(this).val().replace(/[^a-zA-Z0-9]+/g, ''));
    });

    $("#order_delivery_address_flat_number, #order_invoice_address_flat_number").on('keyup', function () {
        $(this).val($(this).val().substring(0, 10));
    });
});

function checkEmployeeIfEmpty(employeeSelect) {
    toggleIsEmptyInfoClassIfNoValue(employeeSelect, "none");
}

function checkFieldsForValidateMarking() {
    toggleIsEmptyInfoClassIfNoValue($("#shipment_price_for_client"));
    toggleIsEmptyInfoClassIfNoValue($("#delivery_warehouse"));
    toggleIsEmptyInfoClassIfNoValue($("#shipment_date"));

    toggleIsEmptyInfoClassIfNoValue($("#order_delivery_address_firstname"));
    toggleIsEmptyInfoClassIfNoValue($("#order_delivery_address_lastname"));
    toggleIsEmptyInfoClassIfNoValue($("#order_delivery_address_email"));
    toggleIsEmptyInfoClassIfNoValue($("#order_delivery_address_address"));
    toggleIsEmptyInfoClassIfNoValue($("#order_delivery_address_flat_number"));
    toggleIsEmptyInfoClassIfNoValue($("#order_delivery_address_city"));
    toggleIsEmptyInfoClassIfNoValue($("#order_delivery_address_postal_code"));
    toggleIsEmptyInfoClassIfNoValue($("#order_delivery_address_phone"));
}

function toggleIsEmptyInfoClassIfNoValue(el, val = "") {
    if (el.val() == val) {
        el.parent().addClass("is-empty-info");
    }
}
