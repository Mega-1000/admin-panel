const showSelectWarehouseTemplate = (modal, orderId) => {
    const row = $('#id-' + orderId);
    const warehouseEl = row.find('.warehouse-symbol');
    const warehouse = warehouseEl.text();

    $('.warehouse-template').remove();

    let warehouseTemplate = `
                <div class="error" style="display: none">
                    <div class="alert alert-danger" role="alert">
                    </div>
                </div>
                <div class="warehouse-template">
                <p>Magazyn nie został przypisany, przypisz magazyn przed wysłaniem</p>
                <div class="form-group" style="width: 15%; padding: 5px;">
                    <label for="delivery_warehouse2">Magazyn obsługujący</label>
                    <input type="text" class="form-control" id="delivery_warehouse2" name="delivery_warehouse2" value="${warehouse}">
                </div><br>
                </div>`;

    const modalBody = modal.find('.modal-body');
    const modalOk = modal.find('#labels_to_add_after_removal_modal_ok');

    if (!warehouse) modalOk.attr('disabled', 'disabled');

    modalBody.prepend(warehouseTemplate);
    $("#delivery_warehouse2").autocomplete({
        source: available,
        classes: {
            'ui-autocomplete': 'z-index-max',
        },
        select: async (e, ui) => {
            modalOk.attr('disabled', 'disabled');
            await $.ajax({
                url: "/admin/orders/set-warehouse/" + orderId,
                method: "POST",
                data: {
                    warehouse: ui.item.value
                }
            }).done(res => {
                if (res) {
                    warehouseEl.text(ui.item.value);
                    modalOk.removeAttr('disabled');
                }
            });
        },
    });


    if (manualLabelSelectionToAdd) {
        $.ajax({
            url: "/admin/labels/" + labelId + "/associated-labels-to-add-after-removal"
        }).done(async function (data) {

            let modal = $('#manual_label_selection_to_add_modal');
            let input = modal.find("#labels_to_add_after_removal_modal");
            input.empty();
            data.forEach(function (item) {
                input.append($('<option>', {
                    value: item.id,
                    text: item.name
                }));
            });
            $('#manual_label_selection_to_add_modal').modal('show');

            if (labelId == 45) showSelectWarehouseTemplate(modal, orderId);

            modal.find("#labels_to_add_after_removal_modal_ok").off().on('click', function () {
                let ids = [];
                ids.push(input.val());
                if (modal.find("#delivery_warehouse2").val() == '') {
                    modal.find(".error").show();
                    modal.find(".error .alert").text('Wybierz magazyn');
                    return false;
                }
                if (!ids) {
                    modal.find(".error").show();
                    modal.find(".error .alert").text('Wybierz przynajmniej jeden etykietę');
                    return false;
                }
                delivery_warehouse = modal.find("#delivery_warehouse2").val();
                removeMultiLabel(orderId, labelId, ids, delivery_warehouse);
                modal.modal('hide');
            });
        });
    } else {
        let payDateLabelId = 63;
        if (labelId == payDateLabelId) {
            let modalSetTime = $('#set_time');
            modalSetTime.modal('show');
            $('#set_time').on('shown.bs.modal', function () {
                $('#invoice-month').focus()
            })
            modalSetTime.find("#remove-label-and-set-date").off().on('click', () => {
                if ($('#invoice-month').val() > 12 || $('#invoice-days').val() > 31) {
                    $('#invoice-date-error').removeAttr('hidden')
                    return
                }
                $.ajax({
                    type: "POST",
                    url: '/admin/orders/payment-deadline',
                    data: {
                        order_id: orderId,
                        date: {
                            year: $('#invoice-years').val(),
                            month: $('#invoice-month').val(),
                            day: $('#invoice-days').val(),
                        }
                    },
                }).done(function (data) {
                    removeLabelRequest();
                    refreshDtOrReload()
                    modalSetTime.modal('hide')
                    $('#invoice-month').val('')
                    $('#invoice-days').val('')
                }).fail(function (data) {
                    $('#invoice-date-error').removeAttr('hidden')
                    $('#invoice-date-error').text(data.responseText ? data.responseText : 'Nieznany błąd2')

                });
            });
            return;
        } else if (addedType == "chat") {
            var url = '{{ route("chat.index", ["all" => 1, "id" => ":id"]) }}';
            url = url.replace(':id', orderId);
            window.location.href = url
            return
        } else if (addedType == "bonus") {
            let url = '{{ route("bonus.order-chat", ['id' => ":id"]) }}';
            url = url.replace(':id', orderId);
            window.location.href = url
            return
        } else if (addedType != "C") {
            let confirmed = confirm("Na pewno usunąć etykietę?");
            if (!confirmed) {
                return;
            }
            removeLabelRequest();
            refreshDtOrReload();
            return;
        }

        let modalTypeC = $('#added_label_is_type_c_modal');
        modalTypeC.modal('show');
        modalTypeC.find("#confirmed_to_remove_chosen_label").off().on('click', removeLabelRequest);

        modalTypeC.find("#new_date_for_timed_label_type_c_ok").off().on('click', function () {
            let val = modalTypeC.find("#new_date_for_timed_label_type_c").val();
            let date = moment(val);
            if (!date.isValid()) {
                alert("Nieprawidłowa data");
                return;
            }

            $.ajax({
                url: "/api/scheduled-time-reset-type-c",
                method: "POST",
                data: {
                    order_id: orderId,
                    label_id_to_handle: labelId,
                    trigger_time: val
                }
            }).done(function () {
                removeLabelRequest();
                refreshDtOrReload();
            });
        });
    }
}
const labelActionMapping = {
    45: showSelectWarehouseTemplate,
}

const removeLabel = (labelId, orderId) => {
    const action = labelActionMapping[labelId];
    if (action) {
        action($('#manual_label_selection_to_add_modal').modal('show'), orderId);
        return;
    }

    swal.fire({
        title: 'Jesteś pewien usuwania?',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'OK',
    }).then(function (result) {
        if (result.isConfirmed) {
            Livewire.emit('removeLabel', labelId, orderId);
        }
    });
}
