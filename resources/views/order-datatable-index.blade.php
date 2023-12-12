@extends('layouts.datatable')

@section('app-header')
    <style>
        th {
            position: relative;
        }

        th::after {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            width: 8px;
            cursor: ew-resize;
        }

        body.resizing th::after {
            display: none;
        }

        .resizing {
            pointer-events: none;
        }
    </style>
    @livewireStyles
@endsection

@section('table')
    <livewire:order-datatable.order-datatable-index />

    <div class="modal fade" tabindex="-1" id="manual_label_selection_to_add_modal" role="dialog">
        <div class="modal-dialog" style="width: 90%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Etykiety do dodania</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="labels_to_add_after_removal_modal">@lang('labels.form.labels_to_add_after_removal')</label>
                        <select class="form-control text-uppercase" id="labels_to_add_after_removal_modal" name="labels_to_add_after_removal_modal[]" size="6">
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="button" class="btn btn-success" id="labels_to_add_after_removal_modal_ok" data-dismiss="modal">Zatwierdź</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="add-new-file" role="dialog">
        <div class="modal-dialog" id="modalDialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ __('voyager::generic.close') }}"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="titleModal">Dodaj nową fakturę sprzedaży:</h4>
                </div>
                <div class="modal-body">
                    <form id="addNewFileToOrder"
                          method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        Plik:
                        <br/>
                        <input accept=".pdf,image/*" type="file" name="file"/>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                    <button type="submit" form="addNewFileToOrder" class="btn btn-success pull-right">Wyślij
                    </button>
                </div>
            </div>
        </div>
    </div>

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/datatable/labels-filter.js') }}"></script>
    <script src="{{ asset('js/datatable/set-delete-event-listeners.js') }}"></script>
{{--    <script src="{{ asset('js/datatable/package-managment.js') }}"></script>--}}
    <script>
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

    </script>
    <script src="{{ asset('js/datatable/labels-deletion.js') }}"></script>
    <script>
        const uploadFile = (id) => {
            let url = "{{ route('orders.fileAdd', ['id' => '%%']) }}"
            $('#addNewFileToOrder').attr('action', url.replace('%%', id));
            $('#add-new-file').modal('show');
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            Livewire.hook('component.initialized', (component) => {
                $('.selectpicker').selectpicker();
                $('.selectpicker').selectpicker('render');
            })
        });

        const addLabelsForCheckedOrders = async () => {
            const label = document.getElementById('choosen-label').value;

            await Livewire.emit('addLabelsForCheckedOrders', label);
            await Swal.fire('success', 'Etykiety zostały dodane', 'success');
        }

        const resetFilters = () => {
            const filterInputs = document.querySelectorAll('#filter');

            filterInputs.forEach(input => {
                input.value = '';
            });

            Livewire.emit('resetFilters');
        }

        const selectAllOrders = () => {
            const checkBoxes = document.querySelectorAll('input[type="checkbox"]')

            checkBoxes.forEach(checkBox => {
                checkBox.checked = true;
            });

            Livewire.emit('selectAllOrders');
        }
    </script>
@endsection
