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

    $.ajax({
        url: "/admin/labels/45/associated-labels-to-add-after-removal"
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
            modal.modal('hide');
        });
    });
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
