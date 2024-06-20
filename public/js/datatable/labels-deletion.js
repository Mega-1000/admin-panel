const getAvailableWarehousesString = (warehouses) => {
    fetch('/admin/get-available-warehouses-string')
        .then(res => res.json())
        .then(data => {
            window.allWarehousesString = data.replaceAll('"', '').split(',');
        });
}

getAvailableWarehousesString();

const showSelectWarehouseTemplate = (modal, orderId) => {
    const row = $('#id-' + orderId);
    const warehouseEl = row.find('.warehouse-symbol');
    const warehouse = warehouseEl.text().replace(/[^a-zA-ZżźćńółęąśŻŹĆĄŚĘŁÓŃ-]/g, '').replace(/\s+/g, '');


    $('.warehouse-template').remove();

    let warehouseTemplate = `
                <div class="error" style="display: none">
                    <div class="alert alert-danger" role="alert">
                    </div>
                </div>
                <div class="warehouse-template">
                <p>Magazyn nie został przypisany, przypisz magazyn przed wysłaniem</p>
                <div class="form-group" style="width: 15%; padding: 5px;">
                    <label for="delivery_warehouse2">Magazyn obsługujący</label>`
                    +
                    `<input type="text" class="form-control" id="delivery_warehouse2" name="delivery_warehouse2" value="${warehouse}"> `
                    +
                `</div><br>
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
}

function removeTimedLabel(orderId, labelId) {
    $.ajax({
        url: "/admin/orders/label-removal/" + orderId + "/" + labelId,
        method: "POST",
        data: {time: $('#time_label_removal').val()}
    }).done(function (res) {
        window.location.href = '#id-' + orderId;
                    window.location.reload();
    });
}


function removeLabel(orderId, labelId, manualLabelSelectionToAdd, addedType, timed = null, skipTimed = true) {
    if (timed == '1' && skipTimed) {
        $('#timed_label_removal').modal('show');
        $('#time_label_removal_ok').on('click', () => {
            removeTimedLabel(orderId, labelId)
        })
        $('#time_label_removal_cancel').on('click', () => {
            removeLabel(orderId, labelId, manualLabelSelectionToAdd, addedType, timed, false)
        })
        return;
    }

    if (labelId == '49') {
        let checkQuantity = checkOrderQuantityInStock(orderId);

        if (checkQuantity == 1) {
            $('#quantity-in-stock-list').modal('show');
            return;
        }
    }

    let removeLabelRequest = function () {
        $.ajax({
            url: "/admin/orders/label-removal/" + orderId + "/" + labelId,
            method: "POST"
        }).done(function (res) {
            refreshDtOrReload();
            $('#position__errors').empty();
            $('#quantity__errors').empty();
            $('#exists__errors').empty();
            res.forEach((error) => {
                if (error.error == '{{ ProductStockError::POSITION }}') {
                    $('#position__errors').append(`<h5>{{ __('product_stocks.form.missing_position_for_product') }} <span class="modal__product">${error.productName}</span>. {{ __('product_stocks.form.go_to_create_position') }} <a href="/admin/products/stocks/${error.product}/positions/create" target="_blank">{{ __('product_stocks.form.click_here') }}</a>`)
                }
                if (error.error == '{{ ProductStockError::QUANTITY }}') {
                    $('#quantity__errors').append(`<h5>{{ __('product_stocks.form.missing_product_quantity') }} <span class="modal__position">${error.position.position_quantity}</span>. {{ __('product_stocks.form.go_to_move_between_positions') }}<a href="/admin/products/stocks/${error.product}/edit?tab=positions" target="_blank">{{ __('product_stocks.form.click_here') }}</a>`)
                }
                if (error.error == '{{ ProductStockError::EXISTS }}') {
                    $('#exists__errors').append(`<h5>{{ __('product_stocks.form.for_product') }} <span class="modal__product">${error.productName}</span> {{ __('product_stocks.form.stock_already_performed') }} {{ __('product_stocks.form.go_to_order') }} <a href="/admin/orders/${error.order_id}/edit" target="_blank">{{ __('product_stocks.form.click_here') }}</a>`)
                }
            })
            $('#stock_modal').modal('show');
        });
    };

    function removeMultiLabel(orderId, labelId, ids, delivery_warehouse = null) {
        $.ajax({
            url: "/admin/orders/label-removal/" + orderId + "/" + labelId,
            method: "POST",
            data: {
                labelsToAddIds: ids,
                manuallyChosen: true
            }
        }).done(function () {
            if ($.inArray('47', ids) != -1) {
                $('input[name="order_id"]').val(orderId);
                $('#selectWarehouse').val(16);
                $('#warehouseSelect').attr('selected', true);
                $('#selectWarehouse').click();
                addingTaskToPlanner(orderId, delivery_warehouse);
                refreshDtOrReload();
            }
            refreshDtOrReload();
        })
            .fail((error) => {
                if (error.responseText === 'warehouse not found') {
                    $('#set-magazine').modal()
                    let form = $('#addWarehouse')
                    form.submit((event) => {
                        event.preventDefault()
                        $.ajax({
                            method: 'post',
                            url: '/admin/orders/set-warehouse-and-remove-label',
                            dataType: 'json',
                            data: {
                                order_id: orderId,
                                warehouse_id: event.target.warehouse_id.value,
                                label: labelId,
                                labelsToAddIds: ids
                            },
                        })
                        $('#set-magazine').modal('hide');
                        refreshDtOrReload()
                    })
                }
            });
    }

    let refreshDtOrReload = function () {
        $.ajax({
            url: '/api/get-labels-scheduler-await/{{ \Illuminate\Support\Facades\Auth::user()->id }}'
        }).done(function (res) {
             window.location.href = '#id-' + orderId;
                    window.location.reload();
        });
    };

    function addingTaskToPlanner(orderId, delivery_warehouse) {
        $.ajax({
            method: 'post',
            url: '/admin/planning/tasks/adding-task-to-planner',
            dataType: 'json',
            data: {
                order_id: orderId,
                delivery_warehouse: delivery_warehouse
            },
        }).done(function (data) {
            if (data.status === 'ERROR') {
                let modal = $('#add-withdraw-task');
                let input_delivery_warehouse = modal.find("#add-withdraw-task-delivery_warehouse");
                let input_order_id = modal.find("#add-withdraw-task-order_id");
                input_delivery_warehouse.val(data.delivery_warehouse);
                input_order_id.val(data.id);
                let order_ids = [data.id];
                let clickCount = 0;
                modal.modal();
                $('#withdrawTaskButton').on('click', () => {
                    if (clickCount > 0) {
                        return false;
                    } else {
                        $.ajax({
                            url: "/admin/orders/label-addition/45",
                            method: "POST",
                            data: {
                                orderIds: order_ids
                            }
                        }).done(function () {
                            modal.modal('hide');
                            table.ajax.reload(null, false);
                            return false;
                        });

                        clickCount++;
                    }
                })
            } else {
                window.open('/admin/planning/timetable', '_blank');
            }
        });
    }

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
            source: window.allWarehousesString,
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
    }

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

            if (labelId === 45) showSelectWarehouseTemplate(modal, orderId);

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
        let payDateLabelId = '63'
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
            const url = `/admin/chat/${1}/${orderId}`;
            window.location.href = url
            return
        } else if (addedType == "bonus") {
            let url = `/admin/bonus/order-chat/${orderId}`;
            url = url.replace(':id', orderId);
            window.location.href = url
            return
        } else if (addedType != "C") {
            let confirmed = confirm("Na pewno usunąć etykietę?");
            if (!confirmed) {
                return;
            }
            removeLabelRequest();
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

function checkOrderQuantityInStock(orderId) {
    let html = '';
    let status = 0;
    $.ajax({
        type: 'GET',
        url: `/admin/planning/tasks/${orderId}/checkOrderQuantityInStock`,
        async: false
    }).done(function (data) {
        if (data.status !== 200) {
            return 1;
        }
        if (Object.keys(data.data).length > 0) {
            status = 1;
        }
        $.each(data.data, function (index, value) {
            html += `
                    <h3>oferta ${index}</h3>
                    <table class="table">
                            <tr class="appendRow">
                            <td style="width: 200px;">Nazwa</td>
                            <td style="width: 100px;">Symbol</td>
                            <td style="width: 50px;">Ilość potrzebna</td>
                            <td style="width: 50px;">Na magazynie/Ilość na pozycji</td>
                            <td>#</td>
                        </tr>`;
            $.each(value, function (index, value) {
                html += `
                        <tr class="appendRow">
                            <td>${value.product_name}</td>
                            <td>${value.product_symbol}</td>
                            <td>${value.quantity}</td>
                            <td>${value.stock_quantity}/${value.first_position_quantity}</td>
                            <td><a href="/admin/products/stocks/${value.product_stock_id}/edit" target="_blank">Przenieś</a></td>
                        </tr>`;
            });
            html += '</table>';
        });
        $('#quantity-in-stock-list .error-finish-task-form').html(html);
    }).fail(function () {
        status = 1;
    });

    return status;
}

const labelActionMapping = {
    45: showSelectWarehouseTemplate,
}

// const removeLabel = (labelId, orderId, labelId) => {
//     const action = labelActionMapping[labelId];
//     if (action) {
//         action($('#manual_label_selection_to_add_modal').modal('show'), orderId, labelId);
//         return;
//     }
//
//     swal.fire({
//         title: 'Jesteś pewien usuwania?',
//         icon: 'info',
//         showCancelButton: true,
//         confirmButtonText: 'OK',
//     }).then(function (result) {
//         if (result.isConfirmed) {
//             Livewire.emit('removeLabel', labelId, orderId);
//         }
//     });
// }

const removeMultiLabel = (orderId, labelId, ids, delivery_warehouse = null) => {
    $.ajax({
        url: "/admin/orders/label-removal/" + orderId + "/" + labelId,
        method: "POST",
        data: {
            labelsToAddIds: ids,
            manuallyChosen: true
        }
    }).done(function () {
        if ($.inArray('47', ids) != -1) {
            $('input[name="order_id"]').val(orderId);
            $('#selectWarehouse').val(16);
            $('#warehouseSelect').attr('selected', true);
            $('#selectWarehouse').click();
            addingTaskToPlanner(orderId, delivery_warehouse);
        }

        window.location.href = '#id-' + orderId;
                    window.location.reload();
    })
        .fail((error) => {
            if (error.responseText === 'warehouse not found') {
                $('#set-magazine').modal()
                let form = $('#addWarehouse')
                form.submit((event) => {
                    event.preventDefault()
                    $.ajax({
                        method: 'post',
                        url: '/admin/orders/set-warehouse-and-remove-label',
                        dataType: 'json',
                        data: {
                            order_id: orderId,
                            warehouse_id: event.target.warehouse_id.value,
                            label: labelId,
                            labelsToAddIds: ids
                        },
                    })
                    $('#set-magazine').modal('hide');

                    window.location.href = '#id-' + orderId;
                    window.location.reload();
                })
            }
        });
    function addingTaskToPlanner(orderId, delivery_warehouse) {
        $.ajax({
            method: 'post',
            url: '/admin/planning/tasks/adding-task-to-planner',
            dataType: 'json',
            data: {
                order_id: orderId,
                delivery_warehouse: delivery_warehouse
            },
        }).done(function (data) {
            if (data.status === 'ERROR') {
                let modal = $('#add-withdraw-task');
                let input_delivery_warehouse = modal.find("#add-withdraw-task-delivery_warehouse");
                let input_order_id = modal.find("#add-withdraw-task-order_id");
                input_delivery_warehouse.val(data.delivery_warehouse);
                input_order_id.val(data.id);
                let order_ids = [data.id];
                let clickCount = 0;
                modal.modal();
                $('#withdrawTaskButton').on('click', () => {
                    if (clickCount > 0) {
                        return false;
                    } else {
                        $.ajax({
                            url: "/admin/orders/label-addition/45",
                            method: "POST",
                            data: {
                                orderIds: order_ids
                            }
                        }).done(function () {
                            modal.modal('hide');
                            table.ajax.reload(null, false);
                            return false;
                        });

                        clickCount++;
                    }
                })
            } else {
                window.open('/admin/planning/timetable', '_blank');
            }
        });
    }
}

